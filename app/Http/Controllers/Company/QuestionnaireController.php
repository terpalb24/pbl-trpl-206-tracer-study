<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use App\Models\Tb_Periode;
use App\Models\Tb_User_Answers;
use App\Models\Tb_User_Answer_Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tb_jobhistory; // Assuming this is the model for job history
use App\Models\Tb_Alumni; // Assuming this is the model for alumni

class QuestionnaireController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Get all active periods where there are categories for company
        $availableActivePeriodes = Tb_Periode::where('status', 'active')
            ->whereHas('categories', function($query) {
                $query->where(function($q) {
                    $q->where('for_type', 'company')
                      ->orWhere('for_type', 'both');
                });
            })
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        // Get all user answers (completed and draft) for filtering
        $allUserAnswers = Tb_User_Answers::where('id_user', $userId)->get();

        // Filter available periods - exclude completed ones
        $availableActivePeriodes = $availableActivePeriodes->filter(function($periode) use ($allUserAnswers) {
            $existingAnswer = $allUserAnswers->where('id_periode', $periode->id_periode)->first();
            // Only show if no answer exists OR if answer exists but is still draft
            return !$existingAnswer || $existingAnswer->status !== 'completed';
        });

        // Get draft answers (incomplete questionnaires)
        $draftUserAnswers = Tb_User_Answers::where('id_user', $userId)
            ->where('status', 'draft')
            ->get();

        // Get completed answers
        $completedUserAnswers = Tb_User_Answers::where('id_user', $userId)
            ->where('status', 'completed')
            ->with('periode')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('company.questionnaire.index', compact('availableActivePeriodes', 'draftUserAnswers', 'completedUserAnswers'));
    }
    
      public function fill($id_periode, $category = null)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $idCompany = Auth::user()->company->id_company ?? null;

        // Ambil semua alumni yang pernah bekerja di perusahaan ini
        $alumniList = Tb_jobhistory::with('alumni')
            ->where('id_company', $idCompany)
            ->get()
            ->pluck('alumni')
            ->unique('nim');

        // Ambil daftar NIM alumni yang sudah dinilai (status completed) pada periode ini oleh perusahaan ini
        $completedNims = Tb_User_Answers::where('id_user', Auth::id())
            ->where('id_periode', $id_periode)
            ->where('status', 'completed')
            ->pluck('nim')
            ->toArray();

        // Filter alumniList agar hanya alumni yang BELUM dinilai (tidak ada di $completedNims)
        $alumniList = $alumniList->filter(function($alumni) use ($completedNims) {
            return $alumni && !in_array($alumni->nim, $completedNims);
        });

        // Check if period is active
        if ($periode->status !== 'active') {
            return redirect()->route('company.questionnaire.index')->with('error', 'Periode kuesioner tidak aktif.');
        }
        
        // Get all categories for this period (company or both types)
        $allCategories = Tb_Category::where('id_periode', $id_periode)
            ->where(function($query) {
                $query->where('for_type', 'company')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get();
            
        if ($allCategories->isEmpty()) {
            return redirect()->route('company.questionnaire.index')->with('error', 'Belum ada kategori untuk kuesioner ini.');
        }
        
        // Determine current category
        if (!$category) {
            $currentCategory = $allCategories->first();
        } else {
            $currentCategory = Tb_Category::findOrFail($category);
            
            // Verify this category is for company
            if (!in_array($currentCategory->for_type, ['company', 'both'])) {
                return redirect()->route('company.questionnaire.index')->with('error', 'Kategori tidak tersedia untuk perusahaan.');
            }
        }
        
        // Find current category index
        $currentCategoryIndex = $allCategories->search(function($cat) use ($currentCategory) {
            return $cat->id_category == $currentCategory->id_category;
        });
        
        // Ensure index is never null (use 0 as fallback)
        if ($currentCategoryIndex === false) {
            $currentCategoryIndex = 0;
        }
        
        $prevCategory = $currentCategoryIndex > 0 ? $allCategories[$currentCategoryIndex - 1] : null;
        $nextCategory = $currentCategoryIndex < $allCategories->count() - 1 ? $allCategories[$currentCategoryIndex + 1] : null;
        
        // Get questions for the current category
        $questions = Tb_Questions::where('id_category', $currentCategory->id_category)
            ->visible() // Only get visible questions
            ->with('options')
            ->orderBy('order')
            ->get();
    
        // ✅ PERBAIKAN: Tambahkan conditional questions data
        $conditionalQuestions = [];
        foreach ($questions as $question) {
            if (!empty($question->depends_on) && !empty($question->depends_value)) {
                $conditionalQuestions[] = [
                    'id' => $question->id_question,
                    'depends_on' => $question->depends_on,
                    'depends_value' => $question->depends_value,
                    'type' => $question->type
                ];
            }
        }
        
        // ✅ PERBAIKAN: Hitung progress yang benar
        $totalCategories = $allCategories->count();
        $progressPercentage = $totalCategories > 0 ? round((($currentCategoryIndex + 1) / $totalCategories) * 100) : 0;
    
        // Get previously saved answers if any
        $prevAnswers = [];
        $prevOtherAnswers = [];
        $prevMultipleAnswers = [];
        $prevMultipleOtherAnswers = [];
        $prevLocationAnswers = [];
    
        $userAnswer = Tb_User_Answers::where('id_user', Auth::id())
            ->where('id_periode', $id_periode)
            ->first();
    
        if ($userAnswer) {
            // Get answer items for this category's questions
            $questionIds = $questions->pluck('id_question')->toArray();
            
            $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                ->whereIn('id_question', $questionIds)
                ->get();
            
            // Process answers by question type
            foreach ($answerItems as $item) {
                $question = $questions->firstWhere('id_question', $item->id_question);
                
                if (!$question) continue;
                
                if ($question->type == 'text' || $question->type == 'date' || $question->type == 'numeric' || $question->type == 'email') {
                    $prevAnswers[$item->id_question] = $item->answer;
                    
                } elseif ($question->type == 'location') {
                    // Parse location JSON data
                    $locationData = json_decode($item->answer, true);
                    if ($locationData && is_array($locationData)) {
                        $prevLocationAnswers[$item->id_question] = json_encode($locationData);
                        // Also set the display value for the form
                        $prevAnswers[$item->id_question] = $locationData['display'] ?? '';
                    } else {
                        // Fallback for old data format
                        $prevAnswers[$item->id_question] = $item->answer;
                    }
                    
                } elseif ($question->type == 'option' || $question->type == 'rating' || $question->type == 'scale') {
                    $prevAnswers[$item->id_question] = $item->id_questions_options;
                    
                    if ($item->other_answer && $question->type == 'option') {
                        $prevOtherAnswers[$item->id_question] = $item->other_answer;
                    }
                    
                } elseif ($question->type == 'multiple') {
                    if (!isset($prevMultipleAnswers[$item->id_question])) {
                        $prevMultipleAnswers[$item->id_question] = [];
                    }
                    $prevMultipleAnswers[$item->id_question][] = $item->id_questions_options;
                    
                    if ($item->other_answer) {
                        if (!isset($prevMultipleOtherAnswers[$item->id_question])) {
                            $prevMultipleOtherAnswers[$item->id_question] = [];
                        }
                        $prevMultipleOtherAnswers[$item->id_question][$item->id_questions_options] = $item->other_answer;
                    }

                }
            }
        }
        
        // ✅ PERBAIKAN: Pass semua data yang diperlukan ke view
        return view('company.questionnaire.fill', compact(
            'periode',
            'currentCategory',
            'allCategories',
            'currentCategoryIndex',
            'prevCategory',
            'nextCategory',
            'questions',
            'prevAnswers',
            'prevOtherAnswers',
            'prevMultipleAnswers',
            'prevMultipleOtherAnswers',
            'prevLocationAnswers',
            'conditionalQuestions',  // ✅ TAMBAHKAN INI
            'totalCategories',       // ✅ TAMBAHKAN INI
            'progressPercentage',
             'alumniList'     // ✅ TAMBAHKAN INI
        ));
    }
    
    public function submit(Request $request, $id_periode)
    {
        try {
            $nim = $request->input('alumni_nim');

            // Validasi alumni wajib dipilih
            if (empty($nim)) {
                return redirect()->back()->with('error', 'Anda harus memilih alumni yang ingin dinilai.');
            }

            // Debug log - tetap tampilkan data lengkap
            Log::info('Company questionnaire submission - FULL DEBUG', [
                'action' => $request->input('action'),
                'category_id' => $request->input('id_category'),
                'answers' => $request->input('answers'),
                'other_answers' => $request->input('other_answers'),
                'multiple' => $request->input('multiple'),
                'multiple_other_answers' => $request->input('multiple_other_answers'),
                'location_combined' => $request->input('location_combined'),
                'all_request_data' => $request->all()
            ]);

            // Ambil kategori dan validasi for_type
            $category = Tb_Category::findOrFail($request->input('id_category'));

            if (!in_array($category->for_type, ['company', 'both'])) {
                return redirect()->back()->with('error', 'Kategori tidak tersedia untuk perusahaan.');
            }

            $userId = Auth::id();

            // Cegah submit dua kali untuk alumni yang sama pada periode yang sama (status completed)
            $alreadyCompleted = \App\Models\Tb_User_Answers::where('id_user', $userId)
                ->where('id_periode', $id_periode)
                ->where('nim', $nim)
                ->where('status', 'completed')
                ->exists();
            if ($alreadyCompleted) {
                return redirect()->back()->with('error', 'Alumni ini sudah dinilai dan tidak dapat dinilai dua kali pada periode yang sama.');
            }
            
            // Cari atau buat record jawaban user berdasarkan user, periode, dan nim alumni yang dinilai
            $userAnswer = Tb_User_Answers::firstOrCreate(
                [
                    'id_user' => $userId,
                    'id_periode' => $id_periode,
                    'nim' => $nim
                ],
                [
                    'status' => 'draft',
                    'submitted_at' => null
                ]
            );
            
            // Get questions for this category
            $questions = Tb_Questions::where('id_category', $category->id_category)->get();
            
            // Process all question answers
            foreach ($questions as $question) {
                // Delete existing answers for this question first
                Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->where('id_question', $question->id_question)
                    ->delete();
                
                if ($question->type == 'text') {
                    // Handle text questions
                    $answer = $request->input("answers.{$question->id_question}");
                    if (!empty($answer)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => null,
                            'answer' => $answer,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved text answer', [
                            'question_id' => $question->id_question,
                            'answer' => $answer
                        ]);
                    }
                
                } elseif ($question->type == 'numeric') {
                    // Handle text questions
                    $answer = $request->input("answers.{$question->id_question}");
                    if (!empty($answer)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => null,
                            'answer' => $answer,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved text answer', [
                            'question_id' => $question->id_question,
                            'answer' => $answer
                        ]);
                    }
                
                } elseif ($question->type == 'email') {
                    // Handle text questions
                    $answer = $request->input("answers.{$question->id_question}");
                    if (!empty($answer)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => null,
                            'answer' => $answer,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved text answer', [
                            'question_id' => $question->id_question,
                            'answer' => $answer
                        ]);
                    }
                
                } elseif ($question->type == 'date') {
                    // Handle date questions
                    $answer = $request->input("answers.{$question->id_question}");
                    if (!empty($answer)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => null,
                            'answer' => $answer,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved date answer', [
                            'question_id' => $question->id_question,
                            'answer' => $answer
                        ]);
                    }
                    
                } elseif ($question->type == 'location') {
                    // Handle location questions
                    $locationCombined = $request->input("location_combined.{$question->id_question}");
                    if (!empty($locationCombined)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => null,
                            'answer' => $locationCombined,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved location answer', [
                            'question_id' => $question->id_question,
                            'answer' => $locationCombined
                        ]);
                    }
                    
                } elseif ($question->type == 'option') {
                    // Handle radio button questions
                    $selectedOption = $request->input("answers.{$question->id_question}");
                    if (!empty($selectedOption)) {
                        $otherAnswer = null;
                        
                        // Check if this is an "other" option and get the user input
                        $option = Tb_Question_Options::find($selectedOption);
                        if ($option && $option->is_other_option) {
                            $otherAnswer = $request->input("other_answers.{$question->id_question}");
                            
                            Log::info('Processing other option for radio', [
                                'question_id' => $question->id_question,
                                'option_id' => $selectedOption,
                                'option' => $option->option,
                                'other_answer_raw' => $otherAnswer,
                                'other_answer_trimmed' => trim($otherAnswer ?? ''),
                                'is_other_option' => $option->is_other_option,
                                'other_before_text' => $option->other_before_text,
                                'other_after_text' => $option->other_after_text
                            ]);
                        }
                        
                        $savedAnswer = Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => $selectedOption,
                            'answer' => null,
                            'other_answer' => $otherAnswer
                        ]);
                        
                        Log::info('Saved radio answer', [
                            'question_id' => $question->id_question,
                            'option_id' => $selectedOption,
                            'other_answer' => $otherAnswer,
                            'saved_id' => $savedAnswer->id_user_answer_item
                        ]);
                    }
                    
                } elseif ($question->type == 'multiple') {
                    // Handle checkbox questions
                    $selectedOptions = $request->input("multiple.{$question->id_question}", []);
                    foreach ($selectedOptions as $optionId) {
                        $otherAnswer = null;
                        
                        // Check if this is an "other" option and get the user input
                        $option = Tb_Question_Options::find($optionId);
                        if ($option && $option->is_other_option) {
                            $otherAnswer = $request->input("multiple_other_answers.{$question->id_question}.{$optionId}");
                            
                            Log::info('Processing other option for multiple', [
                                'question_id' => $question->id_question,
                                'option_id' => $optionId,
                                'option' => $option->option,
                                'other_answer_raw' => $otherAnswer,
                                'other_answer_trimmed' => trim($otherAnswer ?? ''),
                                'is_other_option' => $option->is_other_option
                            ]);
                        }
                        
                        $savedAnswer = Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => $optionId,
                            'answer' => null,
                            'other_answer' => $otherAnswer
                        ]);
                        
                        Log::info('Saved checkbox answer', [
                            'question_id' => $question->id_question,
                            'option_id' => $optionId,
                            'other_answer' => $otherAnswer,
                            'saved_id' => $savedAnswer->id_user_answer_item
                        ]);
                    }
                } elseif ($question->type == 'rating' || $question->type == 'scale') {
                    // Handle rating and scale questions
                    $selectedOption = $request->input("answers.{$question->id_question}");
                    if (!empty($selectedOption)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => $selectedOption,
                            'answer' => null,
                            'other_answer' => null
                        ]);
                        
                        Log::info('Saved rating/scale answer', [
                            'question_id' => $question->id_question,
                            'option_id' => $selectedOption,
                            'question_type' => $question->type
                        ]);
                    }
                }
            }
            
            // Get action from form
            $action = $request->input('action', 'save_draft');
            
            // Get all categories for navigation
            $allCategories = Tb_Category::where('id_periode', $id_periode)
                ->where(function($query) {
                    $query->where('for_type', 'company')
                          ->orWhere('for_type', 'both');
                })
                ->orderBy('order')
                ->get();
                
            $currentCategoryIndex = $allCategories->search(function($cat) use ($category) {
                return $cat->id_category == $category->id_category;
            });
            
            if ($currentCategoryIndex === false) {
                $currentCategoryIndex = 0;
            }
            
            $isLastCategory = ($currentCategoryIndex == $allCategories->count() - 1);
            
            // Handle different actions
            if ($action == 'save_draft') {
                return redirect()->back()->with('success', 'Draft berhasil disimpan.');
                
            } elseif ($action == 'next_category') {
                if (!$isLastCategory) {
                    $nextCategory = $allCategories[$currentCategoryIndex + 1];
                    return redirect()->route('company.questionnaire.fill', [$id_periode, $nextCategory->id_category])
                        ->with('success', 'Jawaban berhasil disimpan. Lanjut ke kategori berikutnya.');
                } else {
                    // This is the last category, mark as completed
                    $userAnswer->update([
                        'status' => 'completed',
                        'submitted_at' => now()
                    ]);
                    
                    return redirect()->route('company.questionnaire.thank-you')
                        ->with('success', 'Kuesioner berhasil diselesaikan!');
                }
                
            } elseif ($action == 'submit_final') {
                // Mark as completed
                $userAnswer->update([
                    'status' => 'completed',
                    'submitted_at' => now()
                ]);
                
                return redirect()->route('company.questionnaire.thank-you')
                    ->with('success', 'Kuesioner berhasil diselesaikan!');
            }
            
            // Fallback
            return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
            
        } catch (\Exception $e) {
            Log::error('Error submitting company questionnaire:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban: ' . $e->getMessage());
        }
    }
    
    public function thankYou()
    {
        return view('company.questionnaire.thank-you');
    }
    
    /**
     * Display a listing of the user's questionnaire results.
     */
public function results()
{
    $userId = Auth::id();
    
    // Get all user answers (both completed and draft) with proper relationships
    $userAnswers = Tb_User_Answers::where('id_user', $userId)
        ->with(['periode', 'user.company'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Add additional data for each answer
    foreach ($userAnswers as $userAnswer) {
        // ===== Tambahkan informasi alumni yang dinilai =====
        $alumni = Tb_Alumni::where('nim', $userAnswer->nim)->first();
        $userAnswer->alumni = $alumni;

        // ===== Hitung completion =====
        $totalQuestions = 0;
        $answeredQuestions = 0;
        
        $categories = Tb_Category::where('id_periode', $userAnswer->id_periode)
            ->where(function($query) {
                $query->where('for_type', 'company')
                      ->orWhere('for_type', 'both');
            })
            ->get();
        
        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)->get();
            $totalQuestions += $questions->count();

            $answeredCount = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                ->whereIn('id_question', $questions->pluck('id_question'))
                ->where(function($query) {
                    $query->whereNotNull('answer')
                          ->orWhereNotNull('id_questions_options');
                })
                ->count();

            $answeredQuestions += $answeredCount;
        }

        $userAnswer->completion_percentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
        $userAnswer->total_questions = $totalQuestions;
        $userAnswer->answered_questions = $answeredQuestions;

        // Format dates
        $userAnswer->formatted_created_at = $userAnswer->created_at->format('d M Y, H:i');
        $userAnswer->formatted_updated_at = $userAnswer->updated_at->format('d M Y, H:i');

        if ($userAnswer->status == 'completed' && $userAnswer->submitted_at) {
            $userAnswer->formatted_submitted_at = \Carbon\Carbon::parse($userAnswer->submitted_at)->format('d M Y, H:i');
        }
    }

    return view('company.questionnaire.results', compact('userAnswers'));
}
    
    /**
     * Display a specific questionnaire response.
     */
    public function responseDetail($id_periode, $id_user_answer)
    {
        $userId = Auth::id();
        
        // Security: Ensure this answer belongs to this user
        $userAnswer = Tb_User_Answers::where('id_user_answer', $id_user_answer)
            ->where('id_user', $userId)
            ->where('id_periode', $id_periode)
            ->with(['user.company', 'periode'])
            ->firstOrFail();
        
        // Get company data - use relationship as fallback
        $company = auth()->user()->company ?? session('company');
        
        // If still no company data, create a basic object to prevent errors
        if (!$company) {
            $company = (object) [
                'name' => auth()->user()->username ?? 'Unknown Company',
                'field' => null,
                'city' => null,
                'address' => null
            ];
        }
        
        // Get categories for this period - PERBAIKAN: Hanya ambil kategori untuk company
        $categories = Tb_Category::where('id_periode', $id_periode)
            ->where(function($query) {
                $query->where('for_type', 'company')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get();
        
        // Get questions and answers
        $questionsWithAnswers = [];
        
        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)
                ->visible()
                ->with('options')
                ->orderBy('order')
                ->get();
            
            $questionArray = [];
            
            foreach ($questions as $question) {
                $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->where('id_question', $question->id_question)
                    ->get();
                
                // Initialize variables to match view expectations
                $answer = null;
                $otherAnswer = null;
                $otherOption = null;
                $multipleAnswers = [];
                $multipleOtherAnswers = [];
                $multipleOtherOptions = [];
                $hasAnswer = false; // Add this flag
                
                if ($answerItems->isNotEmpty()) {
                    $hasAnswer = true; // Set to true if there are answer items
                    
                    if ($question->type == 'text' || $question->type == 'date') {
                        // For text and date questions
                        $answer = $answerItems->first()->answer;
                        // Check if answer is actually empty
                        if (empty(trim($answer))) {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'location') {
                        // For location questions - handle JSON parsing
                        $locationAnswer = $answerItems->first()->answer;
                        if (!empty($locationAnswer)) {
                            try {
                                $locationData = json_decode($locationAnswer, true);
                                if (is_array($locationData)) {
                                    $answer = $locationData['display'] ?? $locationAnswer;
                                } else {
                                    $answer = $locationAnswer;
                                }
                            } catch (\Exception $e) {
                                $answer = $locationAnswer;
                            }
                        }
                        // Check if answer is actually empty
                        if (empty(trim($answer))) {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'rating' || $question->type == 'scale') {
                        // For rating and scale questions
                        $firstItem = $answerItems->first();
                        if ($firstItem->id_questions_options) {
                            $selectedOption = Tb_Question_Options::find($firstItem->id_questions_options);
                            if ($selectedOption) {
                                $answer = $selectedOption->option;
                            } else {
                                $hasAnswer = false;
                            }
                        } else {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'option') {
                        // For single option questions
                        $firstItem = $answerItems->first();
                        if ($firstItem->id_questions_options) {
                            $selectedOption = Tb_Question_Options::find($firstItem->id_questions_options);
                            if ($selectedOption) {
                                $answer = $selectedOption->option;
                                $otherOption = null;
                                
                                if ($selectedOption->is_other_option == 1) {
                                    $otherAnswer = $firstItem->other_answer;
                                    $otherOption = $selectedOption;
                                }
                            } else {
                                $hasAnswer = false;
                            }
                        } else {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'multiple') {
                        // For multiple choice questions
                        $hasValidAnswers = false;
                        foreach ($answerItems as $item) {
                            if ($item->id_questions_options) {
                                $selectedOption = Tb_Question_Options::find($item->id_questions_options);
                                if ($selectedOption) {
                                    $hasValidAnswers = true;
                                    $displayText = $selectedOption->option;
                                    
                                    // Handle other answers for multiple choice
                                    if ($selectedOption->is_other_option == 1 && $item->other_answer) {
                                        $otherDisplay = '';
                                        if ($selectedOption->other_before_text) {
                                            $otherDisplay .= $selectedOption->other_before_text . ' ';
                                        }
                                        $otherDisplay .= $item->other_answer;
                                        if ($selectedOption->other_after_text) {
                                            $otherDisplay .= ' ' . $selectedOption->other_after_text;
                                        }
                                        $displayText .= ': ' . $otherDisplay;
                                        
                                        $multipleOtherAnswers[] = $item->other_answer;
                                        $multipleOtherOptions[] = $selectedOption;
                                    } else {
                                        $multipleOtherAnswers[] = null;
                                        $multipleOtherOptions[] = null;
                                    }
                                    
                                    $multipleAnswers[] = $displayText;
                                }
                            }
                        }
                        $hasAnswer = $hasValidAnswers;
                    }
                } else {
                    // No answer items found
                    $hasAnswer = false;
                }
                
                $questionArray[] = [
                    'question' => $question,
                    'answer' => $answer,
                    'hasAnswer' => $hasAnswer, // Add this key
                    'otherAnswer' => $otherAnswer ?? null,
                    'otherOption' => $otherOption ?? null,
                    'multipleAnswers' => $multipleAnswers,
                    'multipleOtherAnswers' => $multipleOtherAnswers,
                    'multipleOtherOptions' => $multipleOtherOptions,
                ];
            }
            
            // Only add category if it has questions
            if (!empty($questionArray)) {
                $questionsWithAnswers[] = [
                    'category' => $category,
                    'questions' => $questionArray
                ];
            }
        }
        
        
        return view('company.questionnaire.response-detail', compact('userAnswer', 'questionsWithAnswers', 'company'));
    }

    /**
     * Get provinces from external API
     */
    public function getProvinces()
    {
        try {
            $response = file_get_contents('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            $provinces = json_decode($response, true);
            
            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data provinsi'
            ]);
        }
    }

    /**
     * Get cities/regencies by province ID from external API
     */
    public function getCities($provinceId)
    {
        try {
            $response = file_get_contents("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            $cities = json_decode($response, true);
            
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kota/kabupaten'
            ]);
        }
    }
}
