<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_Periode;
use App\Models\Tb_User_Answers;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_User_Answer_Item;
use App\Models\Tb_Question_Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $alumni = auth()->user()->alumni;
        
        if (!$alumni) {
            return redirect()->route('alumni.dashboard')
                ->with('error', 'Data alumni tidak ditemukan. Silakan hubungi administrator.');
        }
        
        // Get only periods that are accessible by this alumni and currently active
        $availablePeriodes = Tb_Periode::where('status', 'active')
            ->get()
            ->filter(function($periode) use ($alumni) {
                return $periode->isAccessibleByAlumni($alumni);
            });

        // Get user answers for these periods
        $userAnswers = Tb_User_Answers::where('id_user', auth()->id())
            ->whereIn('id_periode', $availablePeriodes->pluck('id_periode'))
            ->get()
            ->keyBy('id_periode');

        return view('alumni.questionnaire.index', [
            'periodes' => $availablePeriodes,
            'userAnswers' => $userAnswers
        ]);
    }

    public function fill(Request $request, $periodeId)
    {
        $alumni = auth()->user()->alumni;
        
        if (!$alumni) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Data alumni tidak ditemukan.');
        }
        
        $periode = Tb_Periode::findOrFail($periodeId);
        
        // Check if alumni can access this questionnaire
        if (!$periode->isAccessibleByAlumni($alumni)) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Anda tidak memiliki akses ke kuesioner ini.');
        }

        // Check if period is active
        if ($periode->status !== 'active') {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Periode kuesioner ini sudah berakhir.');
        }

        // Get or create user answer record
        $userAnswer = Tb_User_Answers::firstOrCreate([
            'id_user' => auth()->id(),
            'id_periode' => $periodeId
        ], [
            'status' => 'draft'
        ]);

        // Check if already completed
        if ($userAnswer->status === 'completed') {
            return redirect()->route('alumni.questionnaire.response-detail', [
                'id_periode' => $periodeId,
                'id_user_answer' => $userAnswer->id_user_answer
            ])->with('info', 'Anda sudah menyelesaikan kuesioner ini.');
        }

        // ✅ PERBAIKAN: Get categories for this period - FILTER UNTUK ALUMNI SAJA DAN STATUS DEPENDENCY
        $alumni = auth()->user()->alumni;
        
        $categories = Tb_Category::where('id_periode', $periodeId)
            ->where(function($query) {
                $query->where('for_type', 'alumni')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get()
            ->filter(function($category) use ($alumni) {
                // Filter berdasarkan status dependency
                return $category->isAccessibleByAlumni($alumni);
            })
            ->values(); // Reset array keys

        if ($categories->isEmpty()) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Tidak ada kategori yang tersedia untuk alumni dengan status Anda pada kuesioner ini.');
        }

        // Calculate total categories and other variables
        $totalCategories = $categories->count();
        
        // Determine current category
        $currentCategoryIndex = (int) session('current_category_index', 0);
        $currentCategory = $categories->get($currentCategoryIndex);

        if (!$currentCategory) {
            $currentCategory = $categories->first();
            $currentCategoryIndex = 0;
            session(['current_category_index' => 0]);
        }

        // Calculate progress
        $progressPercentage = $totalCategories > 0 ? round(($currentCategoryIndex + 1) / $totalCategories * 100) : 0;
        
        // Get prev and next categories
        $prevCategory = $currentCategoryIndex > 0 ? $categories->get($currentCategoryIndex - 1) : null;
        $nextCategory = $currentCategoryIndex < $totalCategories - 1 ? $categories->get($currentCategoryIndex + 1) : null;

        // Get questions for current category
        $questions = Tb_Questions::where('id_category', $currentCategory->id_category)
            ->orderBy('order')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Kategori ini belum memiliki pertanyaan.');
        }

        // ✅ PERBAIKAN LOADING EXISTING ANSWERS - LOAD DENGAN BENAR
        $existingAnswers = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
            ->whereIn('id_question', $questions->pluck('id_question'))
            ->with(['question', 'option'])  // Load relasi untuk optimasi
            ->get();

        // ✅ PERBAIKAN FORMAT EXISTING ANSWERS - STRUKTUR YANG BENAR
        $prevAnswers = [];
        $prevMultipleAnswers = [];
        $prevLocationAnswers = [];
        $prevOtherAnswers = [];
        $prevMultipleOtherAnswers = [];
        
        foreach ($existingAnswers as $answer) {
            $question = $questions->where('id_question', $answer->id_question)->first();
            
            if ($question && $question->type === 'multiple') {
                // Untuk multiple choice, kumpulkan semua jawaban
                if (!isset($prevMultipleAnswers[$answer->id_question])) {
                    $prevMultipleAnswers[$answer->id_question] = [];
                }
                
                // Jika ada id_questions_options, gunakan itu untuk checked state
                if ($answer->id_questions_options) {
                    $prevMultipleAnswers[$answer->id_question][] = $answer->id_questions_options;
                } else {
                    $prevMultipleAnswers[$answer->id_question][] = $answer->answer;
                }
                
                // ✅ PERBAIKAN: Load multiple other answers dengan benar
                if (!empty($answer->other_answer) && $answer->id_questions_options) {
                    if (!isset($prevMultipleOtherAnswers[$answer->id_question])) {
                        $prevMultipleOtherAnswers[$answer->id_question] = [];
                    }
                    $prevMultipleOtherAnswers[$answer->id_question][$answer->id_questions_options] = $answer->other_answer;
                }
                
            } elseif ($question && $question->type === 'location') {
                try {
                    $locationData = json_decode($answer->answer, true);  // $answerItem diganti dengan $answer
                    if ($locationData) {
                        $displayValue = $locationData['display'] ?? '';
                        $prevLocationAnswers[$answer->id_question] = $locationData;  // Simpan seluruh data lokasi
                    }
                } catch (\Exception $e) {
                    $prevLocationAnswers[$answer->id_question] = $answer->answer;
                }
            } else {
                // Untuk single answer
                if ($question && in_array($question->type, ['option', 'rating', 'scale']) && $answer->id_questions_options) {
                    // Untuk option questions, gunakan ID option untuk checked state
                    $prevAnswers[$answer->id_question] = $answer->id_questions_options;
                } else {
                    // Untuk text/date/location, gunakan answer text
                    $prevAnswers[$answer->id_question] = $answer->answer;
                }
                
                // ✅ PERBAIKAN: Handle other answers untuk semua jenis pertanyaan
                if (!empty($answer->other_answer)) {
                    $prevOtherAnswers[$answer->id_question] = $answer->other_answer;
                    
                    \Log::info('Other answer loaded for question', [
                        'question_id' => $answer->id_question,
                        'question_type' => $question ? $question->type : 'unknown',
                        'other_answer' => $answer->other_answer,
                        'option_id' => $answer->id_questions_options
                    ]);
                }
            }
        }

        // ✅ PERBAIKAN: Load conditional questions data dengan benar
        $conditionalQuestions = [];
        foreach ($questions as $question) {
            if (!empty($question->depends_on) && !empty($question->depends_value)) {
                $conditionalQuestions[] = [
                    'id' => $question->id_question,
                    'depends_on' => $question->depends_on,
                    'depends_value' => $question->depends_value
                ];
            }
        }

        \Log::info('Loaded existing answers with improved structure', [
            'current_category_index' => $currentCategoryIndex,
            'total_categories' => $totalCategories,
            'progress_percentage' => $progressPercentage,
            'prev_answers_count' => count($prevAnswers),
            'prev_multiple_answers_count' => count($prevMultipleAnswers),
            'prev_location_answers_count' => count($prevLocationAnswers),
            'prev_other_answers_count' => count($prevOtherAnswers),
            'prev_multiple_other_answers_count' => count($prevMultipleOtherAnswers),
            'conditional_questions_count' => count($conditionalQuestions),
            'prev_answers' => $prevAnswers,
            'prev_multiple_answers' => $prevMultipleAnswers,
            'prev_location_answers' => $prevLocationAnswers,
            'prev_other_answers' => $prevOtherAnswers,
            'prev_multiple_other_answers' => $prevMultipleOtherAnswers
        ]);

        return view('alumni.questionnaire.fill', compact(
            'periode',
            'userAnswer',
            'currentCategory',
            'categories',
            'questions',
            'currentCategoryIndex',
            'totalCategories',
            'progressPercentage',
            'prevCategory',
            'nextCategory',
            'prevAnswers',
            'prevMultipleAnswers',
            'prevLocationAnswers',
            'prevOtherAnswers',
            'prevMultipleOtherAnswers',
            'conditionalQuestions'
        ));
    }

    // Change this method name from 'store' to 'submit'
    public function submit(Request $request, $periodeId)
    {
        \Log::info('=== QUESTIONNAIRE SUBMISSION ===', [
            'periode_id' => $periodeId,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        $alumni = auth()->user()->alumni;
        $periode = Tb_Periode::findOrFail($periodeId);
        
        // Check access
        if (!$periode->isAccessibleByAlumni($alumni)) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Anda tidak dapat mengakses kuesioner ini.');
        }

        // Get user answer record
        $userAnswer = Tb_User_Answers::where('id_user', auth()->id())
            ->where('id_periode', $periodeId)
            ->firstOrFail();

        $action = $request->input('action') ?: $request->input('form_action');
        
        \Log::info('Form submission received', [
            'action' => $action,
            'has_answers' => $request->has('answers'),
            'has_multiple' => $request->has('multiple'),
            'has_location' => $request->has('location_combined')
        ]);
        
        try {
            DB::beginTransaction();

            // ✅ VALIDASI: Pastikan kategori yang disubmit adalah untuk alumni
            if ($request->has('id_category')) {
                $category = Tb_Category::where('id_category', $request->input('id_category'))
                    ->where('id_periode', $periodeId)
                    ->where(function($query) {
                        $query->where('for_type', 'alumni')
                              ->orWhere('for_type', 'both');
                    })
                    ->first();
                    
                if (!$category) {
                    return redirect()->back()
                        ->with('error', 'Kategori tidak tersedia untuk alumni.');
                }
            }

            // Always save answers first (except for prev_category)
            if ($action !== 'prev_category') {
                $savedCount = $this->saveAnswers($request, $userAnswer);
                \Log::info('Answers saved', ['count' => $savedCount]);
            }

            if ($action === 'prev_category') {
                // ✅ HANDLE PREVIOUS CATEGORY
                $currentIndex = (int) session('current_category_index', 0);
                
                if ($currentIndex > 0) {
                    session(['current_category_index' => $currentIndex - 1]);
                    DB::commit();
                    return redirect()->route('alumni.questionnaire.fill', $periodeId)
                        ->with('success', 'Kembali ke kategori sebelumnya.');
                } else {
                    // Already at first category
                    DB::commit();
                    return redirect()->route('alumni.questionnaire.fill', $periodeId)
                        ->with('info', 'Anda sudah berada di kategori pertama.');
                }
                
            } elseif ($action === 'next_category') {
                $currentIndex = (int) session('current_category_index', 0);
                
                // ✅ PERBAIKAN: Filter kategori untuk alumni saja
                $categories = Tb_Category::where('id_periode', $periodeId)
                    ->where(function($query) {
                        $query->where('for_type', 'alumni')
                              ->orWhere('for_type', 'both');
                    })
                    ->orderBy('order')
                    ->get();

                if ($currentIndex + 1 < $categories->count()) {
                    session(['current_category_index' => $currentIndex + 1]);
                    DB::commit();
                    return redirect()->route('alumni.questionnaire.fill', $periodeId)
                        ->with('success', 'Jawaban berhasil disimpan. Lanjut ke kategori berikutnya.');
                } else {
                    // This was the last category, mark as completed
                    $userAnswer->update([
                        'status' => 'completed',
                        'created_at' => now()
                    ]);
                    session()->forget('current_category_index');
                    DB::commit();
                    return redirect()->route('alumni.questionnaire.index')
                        ->with('success', 'Kuesioner berhasil diselesaikan!');
                }
                
            } elseif ($action === 'submit_final') {
                $userAnswer->update([
                    'status' => 'completed',
                    'created_at' => now()
                ]);
                session()->forget('current_category_index');
                DB::commit();
                return redirect()->route('alumni.questionnaire.index')
                    ->with('success', 'Kuesioner berhasil diselesaikan!');
                    
            } else {
                // Save as draft (default action)
                $userAnswer->update([
                    'status' => 'draft',
                    'updated_at' => now()
                ]);
                DB::commit();
                
                $message = isset($savedCount) && $savedCount > 0 ? 
                    "Draft berhasil disimpan ($savedCount jawaban tersimpan)." : 
                    "Draft disimpan.";
                    
                return redirect()->route('alumni.questionnaire.fill', $periodeId)
                    ->with('success', $message);
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error submitting questionnaire', [
                'error' => $e->getMessage(),
                'periode_id' => $periodeId,
                'user_id' => auth()->id()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function saveAnswers(Request $request, $userAnswer)
    {
        $savedCount = 0;
        
        \Log::info('=== SAVE ANSWERS DEBUG ===', [
            'user_answer_id' => $userAnswer->id_user_answer,
            'all_request' => $request->all(),
            'has_answers' => $request->has('answers'),
            'has_other_answers' => $request->has('other_answers'),
            'has_other_answer' => $request->has('other_answer'),
            'other_answers_data' => $request->input('other_answers', []),
            'other_answer_data' => $request->input('other_answer', []),
            'has_multiple' => $request->has('multiple'),
            'multiple_data' => $request->input('multiple', []),
            'has_multiple_other_answers' => $request->has('multiple_other_answers'),
            'multiple_other_answers_data' => $request->input('multiple_other_answers', [])
        ]);

        // Handle regular answers (termasuk option radio)
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answer) {
                if (!empty($answer) && trim($answer) !== '') {
                    try {
                        $question = Tb_Questions::find($questionId);
                        
                        \Log::info('Processing question', [
                            'question_id' => $questionId,
                            'question_type' => $question ? $question->type : 'not_found',
                            'received_answer' => $answer,
                            'is_numeric' => is_numeric($answer)
                        ]);
                        
                        $finalAnswer = $answer;
                        $optionId = null;
                        
                        // Jika ini pertanyaan option dan answer berupa ID
                        if ($question && in_array($question->type, ['option', 'rating', 'scale']) && is_numeric($answer)) {
                            $option = Tb_Question_Options::find($answer);
                            if ($option) {
                                $finalAnswer = $option->option;
                                $optionId = $answer;
                                
                                \Log::info('Option found', [
                                    'option_id' => $optionId,
                                    'option' => $finalAnswer
                                ]);
                            } else {
                                \Log::warning('Option not found for ID', ['option_id' => $answer]);
                            }
                        }
                        
                        // ✅ PERBAIKAN CRITICAL: Coba SEMUA kemungkinan format other_answer
                        $otherAnswer = null;
                        
                        // Format 1: other_answers[question_id] (plural)
                        $otherAnswer1 = $request->input("other_answers.{$questionId}");
                        
                        // Format 2: other_answer[question_id] (singular) 
                        $otherAnswer2 = $request->input("other_answer.{$questionId}");
                        
                        // Format 3: other_answers dengan array
                        $otherAnswersArray = $request->input('other_answers', []);
                        $otherAnswer3 = isset($otherAnswersArray[$questionId]) ? $otherAnswersArray[$questionId] : null;
                        
                        // Format 4: other_answer dengan array
                        $otherAnswerArray = $request->input('other_answer', []);
                        $otherAnswer4 = isset($otherAnswerArray[$questionId]) ? $otherAnswerArray[$questionId] : null;
                        
                        // Pilih yang tidak kosong
                        $otherAnswer = $otherAnswer1 ?: $otherAnswer2 ?: $otherAnswer3 ?: $otherAnswer4;
                        
                        \Log::info('Other answer detection', [
                            'question_id' => $questionId,
                            'format1_other_answers' => $otherAnswer1,
                            'format2_other_answer' => $otherAnswer2, 
                            'format3_array_other_answers' => $otherAnswer3,
                            'format4_array_other_answer' => $otherAnswer4,
                            'final_other_answer' => $otherAnswer
                        ]);
                        
                        try {
                            $saved = Tb_User_Answer_Item::updateOrCreate([
                                'id_user_answer' => $userAnswer->id_user_answer,
                                'id_question' => $questionId
                            ], [
                                'answer' => $finalAnswer,
                                'id_questions_options' => $optionId,
                                'other_answer' => !empty($otherAnswer) ? trim($otherAnswer) : null
                            ]);
                            \Log::info('Regular answer saved', [
                                'question_id' => $questionId,
                                'answer' => $finalAnswer,
                                'option_id' => $optionId,
                                'other_answer' => $otherAnswer,
                                'id' => $saved->id_user_answer_item
                            ]);
                            $savedCount++;
                        } catch (\Exception $e) {
                            \Log::error('DB error on regular answer, skipped', [
                                'question_id' => $questionId,
                                'error' => $e->getMessage()
                            ]);
                            // skip error
                            continue;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save regular answer', [
                            'question_id' => $questionId,
                            'answer' => $answer,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        // skip error
                        continue;
                    }
                }
            }
        }

        // ✅ TAMBAHAN: Handle other_answer secara terpisah untuk SEMUA format
        $otherAnswerSources = [
            'other_answers' => $request->input('other_answers', []),
            'other_answer' => $request->input('other_answer', [])
        ];
        
        foreach ($otherAnswerSources as $sourceName => $otherAnswers) {
            if (!empty($otherAnswers) && is_array($otherAnswers)) {
                \Log::info("Processing {$sourceName} separately", [
                    'source' => $sourceName,
                    'data' => $otherAnswers
                ]);
                
                foreach ($otherAnswers as $questionId => $otherAnswer) {
                    if (!empty($otherAnswer) && trim($otherAnswer) !== '') {
                        try {
                            // Update existing answer item with other_answer
                            $answerItem = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                                ->where('id_question', $questionId)
                                ->first();
                                
                            if ($answerItem) {
                                try {
                                    $answerItem->update(['other_answer' => trim($otherAnswer)]);
                                    \Log::info('Other answer updated to existing', [
                                        'source' => $sourceName,
                                        'question_id' => $questionId,
                                        'other_answer' => $otherAnswer,
                                        'existing_answer_id' => $answerItem->id_user_answer_item
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::error('DB error on update other_answer, skipped', [
                                        'question_id' => $questionId,
                                        'error' => $e->getMessage()
                                    ]);
                                    continue;
                                }
                            } else {
                                // Jika belum ada answer item, buat baru khusus untuk other_answer
                                try {
                                    $newAnswerItem = Tb_User_Answer_Item::create([
                                        'id_user_answer' => $userAnswer->id_user_answer,
                                        'id_question' => $questionId,
                                        'answer' => '', // Empty answer tapi ada other_answer
                                        'other_answer' => trim($otherAnswer)
                                    ]);
                                    \Log::info('New answer item created for other_answer only', [
                                        'source' => $sourceName,
                                        'question_id' => $questionId,
                                        'other_answer' => $otherAnswer,
                                        'new_answer_id' => $newAnswerItem->id_user_answer_item
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::error('DB error on create other_answer only, skipped', [
                                        'question_id' => $questionId,
                                        'error' => $e->getMessage()
                                    ]);
                                    continue;
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to update other answer', [
                                'source' => $sourceName,
                                'question_id' => $questionId,
                                'error' => $e->getMessage()
                            ]);
                            // skip error
                            continue;
                        }
                    }
                }
            }
        }

        // ✅ PERBAIKAN: Handle multiple choice answers dengan other_answer yang benar
        if ($request->has('multiple')) {
            foreach ($request->multiple as $questionId => $selectedOptions) {
                if (is_array($selectedOptions) && !empty($selectedOptions)) {
                    try {
                        $question = Tb_Questions::find($questionId);
                        
                        \Log::info('Processing multiple choice', [
                            'question_id' => $questionId,
                            'selected_options' => $selectedOptions
                        ]);
                        
                        // Delete existing answers for this question first
                        try {
                            Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                                ->where('id_question', $questionId)
                                ->delete();
                        } catch (\Exception $e) {
                            \Log::error('DB error on delete multiple choice, skipped', [
                                'question_id' => $questionId,
                                'error' => $e->getMessage()
                            ]);
                            // skip error
                        }
                        
                        foreach ($selectedOptions as $optionId) {
                            if (!empty($optionId)) {
                                $option = Tb_Question_Options::find($optionId);
                                if ($option) {
                                    $finalAnswer = $option ? $option->option : $optionId;
                                    
                                    // ✅ PERBAIKAN: Handle multiple other answers dengan format yang benar
                                    $multipleOtherAnswer = null;
                                    
                                    // Format yang benar: multiple_other_answers[question_id][option_id]
                                    $multipleOtherAnswer = $request->input("multiple_other_answers.{$questionId}.{$optionId}");
                                    
                                    \Log::info('Processing multiple choice option', [
                                        'question_id' => $questionId,
                                        'option_id' => $optionId,
                                        'option_text' => $finalAnswer,
                                        'other_answer' => $multipleOtherAnswer,
                                        'is_other_option' => $option ? $option->is_other_option : false
                                    ]);
                                    
                                    try {
                                        $saved = Tb_User_Answer_Item::create([
                                            'id_user_answer' => $userAnswer->id_user_answer,
                                            'id_question' => $questionId,
                                            'answer' => $finalAnswer,
                                            'id_questions_options' => is_numeric($optionId) ? $optionId : null,
                                            'other_answer' => !empty($multipleOtherAnswer) ? trim($multipleOtherAnswer) : null
                                        ]);
                                        \Log::info('Multiple choice answer saved', [
                                            'question_id' => $questionId,
                                            'option_id' => $optionId,
                                            'answer' => $finalAnswer,
                                            'other_answer' => $multipleOtherAnswer,
                                            'id' => $saved->id_user_answer_item
                                        ]);
                                        $savedCount++;
                                    } catch (\Exception $e) {
                                        \Log::error('DB error on save multiple choice, skipped', [
                                            'question_id' => $questionId,
                                            'option_id' => $optionId,
                                            'error' => $e->getMessage()
                                        ]);
                                        continue;
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save multiple choice answer', [
                            'question_id' => $questionId,
                            'error' => $e->getMessage()
                        ]);
                        // skip error
                        continue;
                    }
                }
            }
        }

        // ✅ TAMBAHAN: Handle multiple_other_answers secara terpisah jika ada yang terlewat
        if ($request->has('multiple_other_answers')) {
            $multipleOtherAnswersData = $request->input('multiple_other_answers', []);
            
            \Log::info('Processing multiple_other_answers separately', [
                'data' => $multipleOtherAnswersData
            ]);
            
            foreach ($multipleOtherAnswersData as $questionId => $optionAnswers) {
                if (is_array($optionAnswers)) {
                    foreach ($optionAnswers as $optionId => $otherAnswer) {
                        if (!empty($otherAnswer) && trim($otherAnswer) !== '') {
                            try {
                                // Find the specific answer item for this question and option
                                $answerItem = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                                    ->where('id_question', $questionId)
                                    ->where('id_questions_options', $optionId)
                                    ->first();
                                    
                                if ($answerItem) {
                                    try {
                                        $answerItem->update(['other_answer' => trim($otherAnswer)]);
                                        \Log::info('Multiple other answer updated', [
                                            'question_id' => $questionId,
                                            'option_id' => $optionId,
                                            'other_answer' => $otherAnswer,
                                            'existing_answer_id' => $answerItem->id_user_answer_item
                                        ]);
                                    } catch (\Exception $e) {
                                        \Log::error('DB error on update multiple other answer, skipped', [
                                            'question_id' => $questionId,
                                            'option_id' => $optionId,
                                            'error' => $e->getMessage()
                                        ]);
                                        continue;
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error('Failed to update multiple other answer', [
                                    'question_id' => $questionId,
                                    'option_id' => $optionId,
                                    'error' => $e->getMessage()
                                ]);
                                // skip error
                                continue;
                            }
                        }
                    }
                }
            }
        }

        // Saat menyimpan data lokasi
        if ($request->has('location_combined')) {
            foreach ($request->location_combined as $questionId => $locationData) {
                if (!empty($locationData)) {
                    try {
                        // Parse location data
                        $locationObject = json_decode($locationData);
                        
                        // Simpan sebagai JSON di database
                        try {
                            $saved = Tb_User_Answer_Item::updateOrCreate([
                                'id_user_answer' => $userAnswer->id_user_answer,
                                'id_question' => $questionId
                            ], [
                                'answer' => $locationData, // Store as JSON string
                                'id_questions_options' => null,
                                'other_answer' => null
                            ]);
                            \Log::info('Location answer saved', [
                                'question_id' => $questionId,
                                'location_data' => $locationObject,
                                'id' => $saved->id_user_answer_item
                            ]);
                            $savedCount++;
                        } catch (\Exception $e) {
                            \Log::error('DB error on save location answer, skipped', [
                                'question_id' => $questionId,
                                'error' => $e->getMessage()
                            ]);
                            continue;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save location answer', [
                            'question_id' => $questionId,
                            'error' => $e->getMessage()
                        ]);
                        // skip error
                        continue;
                    }
                }
            }
        }
        
        \Log::info('Save answers complete', ['total_saved' => $savedCount]);
        return $savedCount;
    }

    /**
     * Display alumni's questionnaire history/results
     */
    public function results()
    {
        \Log::info('Alumni accessing questionnaire results', [
            'user_id' => auth()->id(),
            'alumni_id' => auth()->user()->alumni->id_alumni ?? 'no_alumni'
        ]);

        $alumni = auth()->user()->alumni;
        
        if (!$alumni) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Data alumni tidak ditemukan. Silakan hubungi administrator.');
        }

        // Get all user answers for this alumni
        $userAnswers = Tb_User_Answers::where('id_user', auth()->id())
            ->with(['periode' => function($query) {
                $query->select('id_periode', 'start_date', 'end_date', 'status');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        \Log::info('User answers loaded', [
            'count' => $userAnswers->count(),
            'user_id' => auth()->id()
        ]);

        return view('alumni.questionnaire.results', compact('userAnswers'));
    }

    /**
     * Display detailed response for specific questionnaire
     */
    public function responseDetail($periodeId, $userAnswerId)
    {
        \Log::info('Alumni accessing response detail', [
            'user_id' => auth()->id(),
            'periode_id' => $periodeId,
            'user_answer_id' => $userAnswerId
        ]);

        $alumni = auth()->user()->alumni;
        
        if (!$alumni) {
            return redirect()->route('alumni.questionnaire.index')
                ->with('error', 'Data alumni tidak ditemukan.');
        }

        // Verify user answer belongs to current user
        $userAnswer = Tb_User_Answers::where('id_user_answer', $userAnswerId)
            ->where('id_user', auth()->id())
            ->where('id_periode', $periodeId)
            ->with('periode')
            ->first();

        if (!$userAnswer) {
            return redirect()->route('alumni.questionnaire.results')
                ->with('error', 'Data jawaban tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // ✅ PERBAIKAN: Get categories for this period - FILTER UNTUK ALUMNI DENGAN STATUS DEPENDENCY
        $categories = Tb_Category::where('id_periode', $periodeId)
            ->where(function($query) {
                $query->where('for_type', 'alumni')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get()
            ->filter(function($category) use ($alumni) {
                // ✅ TAMBAHAN: Filter berdasarkan status dependency seperti di fill questionnaire
                return $category->isAccessibleByAlumni($alumni);
            })
            ->values(); // Reset array keys

        // ✅ GUNAKAN STRUKTUR YANG SAMA PERSIS DENGAN COMPANY
        $questionsWithAnswers = [];

        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)
                ->with(['category', 'options'])
                ->orderBy('order')
                ->get();

            if ($questions->isEmpty()) {
                continue;
            }

            $questionArray = [];

            foreach ($questions as $question) {
                // Get answers for this question
                $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswerId)
                    ->where('id_question', $question->id_question)
                    ->with(['question', 'option'])
                    ->get();

                $hasAnswer = $answerItems->isNotEmpty();
                $answer = null;
                $otherAnswer = null;
                $otherOption = null;
                $multipleAnswers = [];
                $multipleOtherAnswers = [];
                $multipleOtherOptions = [];

                if ($hasAnswer) {
                    if ($question->type === 'multiple') {
                        // Multiple choice answers
                        foreach ($answerItems as $answerItem) {
                            if ($answerItem->id_questions_options) {
                                $multipleAnswers[] = $answerItem->id_questions_options;
                                
                                // ✅ PERBAIKAN: Handle other answers untuk multiple choice
                                if (!empty($answerItem->other_answer)) {
                                    $multipleOtherAnswers[$answerItem->id_questions_options] = $answerItem->other_answer;
                                    
                                    $option = $question->options->where('id_questions_options', $answerItem->id_questions_options)->first();
                                    if ($option && $option->is_other_option) {
                                        $multipleOtherOptions[$answerItem->id_questions_options] = $option;
                                    }
                                }
                            }
                        }
                        
                        \Log::info('Multiple choice processed', [
                            'question_id' => $question->id_question,
                            'multiple_answers' => $multipleAnswers,
                            'multiple_other_answers' => $multipleOtherAnswers
                        ]);
                    } else {
                        // Single answer
                        $answerItem = $answerItems->first();
                        
                        if ($question->type === 'option' || $question->type === 'rating') {
                            if ($answerItem->id_questions_options) {
                                $answer = $answerItem->id_questions_options;
                                
                                $option = $question->options->where('id_questions_options', $answerItem->id_questions_options)->first();
                                if ($option && $option->is_other_option && !empty($answerItem->other_answer)) {
                                    $otherAnswer = $answerItem->other_answer;
                                    $otherOption = $option;
                                }
                            } else {
                                $answer = $answerItem->answer;
                            }
                        } else {
                            $answer = $answerItem->answer;
                            // ✅ PERBAIKAN: Set other_answer untuk semua jenis pertanyaan
                            if (!empty($answerItem->other_answer)) {
                                $otherAnswer = $answerItem->other_answer;
                            }
                        }
                    }
                }

                // ✅ DEBUG: Log other answer data
                if ($otherAnswer || !empty($multipleOtherAnswers)) {
                    \Log::info('Other Answer Found', [
                        'question_id' => $question->id_question,
                        'question_type' => $question->type,
                        'other_answer' => $otherAnswer,
                        'multiple_other_answers' => $multipleOtherAnswers,
                        'option_id' => $answer,
                        'option_is_other' => $otherOption ? $otherOption->is_other_option : false
                    ]);
                }

                // ✅ IMPORTANT: Struktur data yang sama persis dengan company
                $questionData = [
                    'question' => $question,
                    'hasAnswer' => $hasAnswer,
                    'answer' => $answer,
                    'otherAnswer' => $otherAnswer,
                    'otherOption' => $otherOption,
                    'multipleAnswers' => $multipleAnswers,
                    'multipleOtherAnswers' => $multipleOtherAnswers,
                    'multipleOtherOptions' => $multipleOtherOptions,
                ];

                $questionArray[] = $questionData;
            }

            // Only add category if it has questions
            if (!empty($questionArray)) {
                $questionsWithAnswers[] = [
                    'category' => $category,
                    'questions' => $questionArray
                ];
            }
        }

        // ✅ DEBUG: Log dependency info dengan struktur yang benar
        \Log::info('Alumni Response Detail - Dependency debug:', [
            'questionsWithAnswers' => collect($questionsWithAnswers)->map(function($cat) {
                return [
                    'category' => $cat['category']->category_name,
                    'category_status_dependent' => $cat['category']->is_status_dependent,
                    'category_required_status' => $cat['category']->required_alumni_status,
                    'alumni_status' => auth()->user()->alumni->status ?? 'unknown',
                    'category_accessible' => $cat['category']->isAccessibleByAlumni(auth()->user()->alumni),
                    'questions' => collect($cat['questions'])->map(function($q) {
                        return [
                            'id' => $q['question']->id_question,
                            'question' => substr($q['question']->question, 0, 50),
                            'depends_on' => $q['question']->depends_on,
                            'depends_value' => $q['question']->depends_value,
                            'hasAnswer' => $q['hasAnswer'],
                            'answer' => $q['answer'],
                            'otherAnswer' => $q['otherAnswer'],
                            'multipleAnswers' => $q['multipleAnswers'],
                            'multipleOtherAnswers' => $q['multipleOtherAnswers']
                        ];
                    })->toArray()
                ];
            })->toArray()
        ]);

        return view('alumni.questionnaire.response-detail', compact('userAnswer', 'questionsWithAnswers'));
    }

    private function validateAnswers(Request $request, $questions)
    {
        $errors = [];
        
        foreach ($questions as $question) {
            // ✅ SKIP VALIDASI UNTUK CONDITIONAL QUESTIONS YANG TIDAK AKTIF
            if ($question->depends_on) {
                $parentQuestionId = $question->depends_on;
                $requiredValue = $question->depends_value;
                
                // Cek apakah parent question dijawab dengan nilai yang sesuai
                $parentAnswer = $request->input("answers.{$parentQuestionId}");
                
                if ($parentAnswer != $requiredValue) {
                    \Log::info("Skipping validation for conditional question {$question->id_question} - parent answer doesn't match", [
                        'parent_question' => $parentQuestionId,
                        'parent_answer' => $parentAnswer,
                        'required_value' => $requiredValue
                    ]);
                    continue; // Skip validation untuk pertanyaan conditional yang tidak aktif
                }
            }
            
            $isRequired = $question->is_required ?? true; // Assume required if not specified
            
            if (!$isRequired) {
                continue;
            }
            
            $isAnswered = false;
            $questionText = $question->question;
            
            switch ($question->type) {
                case 'text':
                case 'date':
                    $answer = $request->input("answers.{$question->id_question}");
                    $isAnswered = !empty($answer) && trim($answer) !== '';
                    break;
                    
                case 'option':
                case 'rating':  
                case 'scale':
                    $answer = $request->input("answers.{$question->id_question}");
                    $isAnswered = !empty($answer);
                    
                    // Check if "other" option requires additional text
                    if ($isAnswered) {
                        $selectedOption = $question->options->where('id_questions_options', $answer)->first();
                        if ($selectedOption && $selectedOption->is_other_option) {
                            $otherAnswer = $request->input("other_answers.{$question->id_question}");
                            $isAnswered = !empty($otherAnswer) && trim($otherAnswer) !== '';
                        }
                    }
                    break;
                    
                case 'multiple':
                    $answers = $request->input("multiple.{$question->id_question}", []);
                    $isAnswered = !empty($answers) && is_array($answers);
                    
                    // Check if any "other" options require additional text
                    if ($isAnswered) {
                        foreach ($answers as $optionId) {
                            $selectedOption = $question->options->where('id_questions_options', $optionId)->first();
                            if ($selectedOption && $selectedOption->is_other_option) {
                                $otherAnswer = $request->input("multiple_other_answers.{$question->id_question}.{$optionId}");
                                if (empty($otherAnswer) || trim($otherAnswer) === '') {
                                    $isAnswered = false;
                                    break;
                                }
                            }
                        }
                    }
                    break;
                    
                case 'location':
                    $locationData = $request->input("location_combined.{$question->id_question}");
                    $isAnswered = !empty($locationData);
                    break;
            }
            
            if (!$isAnswered) {
                $errors[] = $questionText;
            }
        }
        
        return $errors;
    }
}
