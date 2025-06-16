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
use App\Models\Tb_jobhistory;
use App\Models\Tb_Alumni;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // PERBAIKAN: Gunakan 'for_type' sesuai database
        $availableActivePeriodes = Tb_Periode::where('status', 'active')
            ->whereHas('categories', function($query) {
                $query->whereIn('for_type', ['company', 'both']); // Sesuai dengan database
            })
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        // Get all user answers (completed and draft) for filtering
        $allUserAnswers = Tb_User_Answers::where('id_user', $userId)->get();


        // Get draft answers
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

    public function selectAlumni($id_periode)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $idCompany = Auth::user()->company->id_company ?? null;

        // Check if period is active
        if ($periode->status !== 'active') {
            return redirect()->route('company.questionnaire.index')
                ->with('error', 'Periode kuesioner tidak aktif.');
        }

        // PERBAIKAN: Gunakan 'for_type' sesuai database
        $hasCategories = Tb_Category::where('id_periode', $id_periode)
            ->whereIn('for_type', ['company', 'both'])
            ->exists();

        if (!$hasCategories) {
            return redirect()->route('company.questionnaire.index')
                ->with('error', 'Tidak ada kategori kuesioner untuk perusahaan pada periode ini.');
        }

        if (!$idCompany) {
            return redirect()->route('company.questionnaire.index')
                ->with('error', 'Data perusahaan tidak ditemukan.');
        }

        // Ambil semua alumni yang pernah bekerja di perusahaan ini
        $allAlumniFromJobHistory = Tb_jobhistory::with('alumni')
            ->where('id_company', $idCompany)
            ->get()
            ->filter(function($jobHistory) {
                return $jobHistory->alumni !== null;
            })
            ->unique('nim')
            ->values();

        // Ambil daftar NIM alumni yang sudah dinilai
        $completedNims = Tb_User_Answers::where('id_user', Auth::id())
            ->where('id_periode', $id_periode)
            ->where('status', 'completed')
            ->pluck('nim')
            ->toArray();

        // Filter alumni yang belum dinilai
        $availableAlumni = $allAlumniFromJobHistory->filter(function($jobHistory) use ($completedNims) {
            return !in_array($jobHistory->nim, $completedNims);
        });

        // Get alumni yang sedang dalam proses (draft)
        $draftNims = Tb_User_Answers::where('id_user', Auth::id())
            ->where('id_periode', $id_periode)
            ->where('status', 'draft')
            ->pluck('nim')
            ->toArray();

        return view('company.questionnaire.select-alumni', compact(
            'periode', 
            'availableAlumni', 
            'completedNims', 
            'draftNims'
        ));
    }
    
    public function fill($id_periode, $nim, $category = null)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $idCompany = Auth::user()->company->id_company ?? null;

        // Verify that this alumni worked for this company
        $jobHistoryExists = Tb_jobhistory::where('id_company', $idCompany)
            ->where('nim', $nim)
            ->exists();

        if (!$jobHistoryExists) {
            return redirect()->route('company.questionnaire.select-alumni', $id_periode)
                ->with('error', 'Alumni ini tidak pernah bekerja di perusahaan Anda.');
        }

        // Check if this alumni has already been evaluated
        $existingCompletedAnswer = Tb_User_Answers::where('id_user', Auth::id())
            ->where('id_periode', $id_periode)
            ->where('nim', $nim)
            ->where('status', 'completed')
            ->first();

        if ($existingCompletedAnswer) {
            return redirect()->route('company.questionnaire.select-alumni', $id_periode)
                ->with('error', 'Alumni ini sudah pernah dinilai pada periode ini.');
        }

        // Get alumni data
        $alumni = Tb_Alumni::where('nim', $nim)->first();
        if (!$alumni) {
            return redirect()->route('company.questionnaire.select-alumni', $id_periode)
                ->with('error', 'Data alumni tidak ditemukan.');
        }

        // Check if period is active
        if ($periode->status !== 'active') {
            return redirect()->route('company.questionnaire.index')
                ->with('error', 'Periode kuesioner tidak aktif.');
        }
        
        // PERBAIKAN: Get categories dengan field yang benar
        $allCategories = Tb_Category::where('id_periode', $id_periode)
            ->whereIn('for_type', ['company', 'both']) // Gunakan for_type
            ->orderBy('order')
            ->get();

        // DEBUGGING: Log categories
        Log::info('Categories found for company', [
            'periode_id' => $id_periode,
            'company_id' => $idCompany,
            'categories_count' => $allCategories->count(),
            'categories' => $allCategories->map(function($cat) {
                return [
                    'id' => $cat->id_category,
                    'name' => $cat->category_name,
                    'for_type' => $cat->for_type,
                    'order' => $cat->order
                ];
            })->toArray()
        ]);
            
        if ($allCategories->isEmpty()) {
            return redirect()->route('company.questionnaire.index')
                ->with('error', 'Tidak ada kategori kuesioner untuk perusahaan pada periode ini.');
        }
        
        // Determine current category
        if (!$category) {
            $currentCategory = $allCategories->first();
        } else {
            $currentCategory = $allCategories->where('id_category', $category)->first();
            if (!$currentCategory) {
                $currentCategory = $allCategories->first();
            }
        }
        
        // Find current category index
        $currentCategoryIndex = $allCategories->search(function($cat) use ($currentCategory) {
            return $cat->id_category == $currentCategory->id_category;
        });
                                    
        if ($currentCategoryIndex === false) {
            $currentCategoryIndex = 0;
        }
        
        $prevCategory = $currentCategoryIndex > 0 ? $allCategories[$currentCategoryIndex - 1] : null;
        $nextCategory = $currentCategoryIndex < $allCategories->count() - 1 ? $allCategories[$currentCategoryIndex + 1] : null;
        
        // PERBAIKAN: Get questions - GUNAKAN filter status karena ada di database
        $questions = Tb_Questions::where('id_category', $currentCategory->id_category)
            ->where('status', 'visible') // ✅ KEMBALIKAN filter status
            ->with('options')
            ->orderBy('order')
            ->get();

        // DEBUGGING: Log questions
        Log::info('Questions found for company', [
            'category_id' => $currentCategory->id_category,
            'category_name' => $currentCategory->category_name,
            'questions_count' => $questions->count(),
            'questions' => $questions->map(function($q) {
                return [
                    'id' => $q->id_question,
                    'question' => $q->question,
                    'type' => $q->type,
                    'status' => $q->status,
                    'order' => $q->order,
                    'options_count' => $q->options ? $q->options->count() : 0
                ];
            })->toArray()
        ]);

        // PERBAIKAN: Handle conditional questions dengan field yang benar
        $conditionalQuestions = [];
        foreach ($questions as $question) {
            if ($question->depends_on) { // Gunakan depends_on
                $conditionalQuestions[$question->depends_on][] = [
                    'question_id' => $question->id_question,
                    'condition_value' => $question->depends_value // Gunakan depends_value
                ];
            }
        }
        
        // Calculate progress
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
            ->where('nim', $nim)
            ->first();
    
        if ($userAnswer) {
            $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)->get();
            
            foreach ($answerItems as $item) {
                $question = Tb_Questions::find($item->id_question);
                if ($question && $question->type === 'multiple') {
                    if (!isset($prevMultipleAnswers[$item->id_question])) {
                        $prevMultipleAnswers[$item->id_question] = [];
                    }
                    $prevMultipleAnswers[$item->id_question][] = $item->answer;
                    
                    if ($item->other_answer) {
                        $prevMultipleOtherAnswers[$item->answer] = $item->other_answer;
                    }
                } elseif ($question && $question->type === 'location') {
                    // ✅ PERBAIKAN: Properly parse and structure location data
                    try {
                        if (!empty($item->answer)) {
                            $locationData = json_decode($item->answer, true);
                            
                            if ($locationData && is_array($locationData)) {
                                // Store the complete location data for JavaScript to use
                                $prevLocationAnswers[$item->id_question] = $locationData;
                                
                                \Log::info('Location answer loaded from database', [
                                    'question_id' => $item->id_question,
                                    'location_data' => $locationData
                                ]);
                            } else {
                                \Log::warning('Invalid location data format in database', [
                                    'question_id' => $item->id_question,
                                    'raw_answer' => $item->answer
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to parse location answer from database', [
                            'question_id' => $item->id_question,
                            'error' => $e->getMessage(),
                            'raw_answer' => $item->answer
                        ]);
                    }
                } else {
                    $prevAnswers[$item->id_question] = $item->answer;
                    if ($item->other_answer) {
                        $prevOtherAnswers[$item->id_question] = $item->other_answer;
                    }
                }
            }
        }
        
        return view('company.questionnaire.fill', compact(
            'periode',
            'alumni',
            'nim',
            'currentCategory',
            'allCategories',
            'currentCategoryIndex',
            'totalCategories',
            'progressPercentage',
            'prevCategory',
            'nextCategory',
            'questions',
            'conditionalQuestions',
            'prevAnswers',
            'prevOtherAnswers',
            'prevMultipleAnswers',
            'prevMultipleOtherAnswers',
            'prevLocationAnswers'
        ));
    }

    public function submit(Request $request, $id_periode, $nim)
    {
        try {
            $userId = Auth::id();
            $idCompany = Auth::user()->company->id_company ?? null;

            // Verify that this alumni worked for this company
            $jobHistoryExists = Tb_jobhistory::where('id_company', $idCompany)
                ->where('nim', $nim)
                ->exists();

            if (!$jobHistoryExists) {
                return redirect()->route('company.questionnaire.select-alumni', $id_periode)
                    ->with('error', 'Alumni ini tidak pernah bekerja di perusahaan Anda.');
            }

            $periode = Tb_Periode::findOrFail($id_periode);
            $categoryId = $request->input('id_category');
            $action = $request->input('action', 'save_draft');

            // Get or create user answer record
            $userAnswer = Tb_User_Answers::firstOrCreate(
                [
                    'id_user' => $userId,
                    'id_periode' => $id_periode,
                    'nim' => $nim
                ],
                [
                    'status' => 'draft'
                ]
            );

            // Delete existing answers for this category
            Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                ->whereHas('question', function($query) use ($categoryId) {
                    $query->where('id_category', $categoryId);
                })
                ->delete();

            // PERBAIKAN: Get questions untuk category ini dengan filter status
            $questions = Tb_Questions::where('id_category', $categoryId)
                ->where('status', 'visible') // ✅ KEMBALIKAN filter status
                ->get();

            // Process answers for each question
            foreach ($questions as $question) {
                $this->processQuestionAnswer($request, $userAnswer, $question);
            }

            // Determine next action based on button clicked
            if ($action === 'save_draft') {
                return redirect()->route('company.questionnaire.fill', [$id_periode, $nim, $categoryId])
                    ->with('success', 'Draft berhasil disimpan.');
            } elseif ($action === 'prev_category') {
                $allCategories = Tb_Category::where('id_periode', $id_periode)
                    ->whereIn('for_type', ['company', 'both'])
                    ->orderBy('order')
                    ->get();
                
                $currentIndex = $allCategories->search(function($cat) use ($categoryId) {
                    return $cat->id_category == $categoryId;
                });
                
                if ($currentIndex > 0) {
                    $prevCategory = $allCategories[$currentIndex - 1];
                    return redirect()->route('company.questionnaire.fill', [$id_periode, $nim, $prevCategory->id_category]);
                }
                
                return redirect()->route('company.questionnaire.fill', [$id_periode, $nim]);
            } elseif ($action === 'next_category') {
                $allCategories = Tb_Category::where('id_periode', $id_periode)
                    ->whereIn('for_type', ['company', 'both'])
                    ->orderBy('order')
                    ->get();
                
                $currentIndex = $allCategories->search(function($cat) use ($categoryId) {
                    return $cat->id_category == $categoryId;
                });
                
                if ($currentIndex < $allCategories->count() - 1) {
                    $nextCategory = $allCategories[$currentIndex + 1];
                    return redirect()->route('company.questionnaire.fill', [$id_periode, $nim, $nextCategory->id_category]);
                } else {
                    session(['company_current_periode_id' => $id_periode]);
                    // Last category, mark as completed
                    $userAnswer->update([
                        'status' => 'completed',
                        'created_at' => now()
                    ]);
                    
                    return redirect()->route('company.questionnaire.thank-you', ['id_periode' => $id_periode])
                     ->with('success', 'Kuesioner berhasil diselesaikan untuk alumni: ' . $nim);
                }
            } elseif ($action === 'submit_final') {
                // Store periode ID in session
                session(['company_current_periode_id' => $id_periode]);
                // Mark as completed
                $userAnswer->update([
                    'status' => 'completed', 
                    'created_at' => now()
                ]);
                
                return redirect()->route('company.questionnaire.thank-you', ['id_periode' => $id_periode])
                 ->with('success', 'Kuesioner berhasil diselesaikan untuk alumni: ' . $nim);
            }

            return redirect()->route('company.questionnaire.fill', [$id_periode, $nim, $categoryId]);

        } catch (\Exception $e) {
            Log::error('Company questionnaire submission error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.')
                ->withInput();
        }
    }

    // Method lainnya tetap sama...
    public function thankYou()
    {
        // Ambil periode ID dari session atau request sebelumnya
        $id_periode = session('company_current_periode_id') ?? request()->get('id_periode');
        
        return view('company.questionnaire.thank-you', compact('id_periode'));
    }

    public function results()
    {
        $userId = Auth::id();
        
        $userAnswers = Tb_User_Answers::where('id_user', $userId)
            ->with(['periode', 'user.company'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($userAnswers as $userAnswer) {
            $alumni = Tb_Alumni::where('nim', $userAnswer->nim)->first();
            $userAnswer->alumni = $alumni;

            $totalQuestions = 0;
            $answeredQuestions = 0;
            
            $categories = Tb_Category::where('id_periode', $userAnswer->id_periode)
                ->whereIn('for_type', ['company', 'both'])
                ->get();
            
            foreach ($categories as $category) {
                // PERBAIKAN: Tambahkan filter status
                $categoryQuestions = Tb_Questions::where('id_category', $category->id_category)
                    ->where('status', 'visible') // ✅ KEMBALIKAN filter status
                    ->count();
                $totalQuestions += $categoryQuestions;

                $answeredInCategory = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->whereHas('question', function($query) use ($category) {
                        $query->where('id_category', $category->id_category)
                              ->where('status', 'visible'); // ✅ KEMBALIKAN filter status
                    })
                    ->count();
                $answeredQuestions += $answeredInCategory;
            }

            $userAnswer->completion_percentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
            $userAnswer->total_questions = $totalQuestions;
            $userAnswer->answered_questions = $answeredQuestions;

            $userAnswer->formatted_created_at = $userAnswer->created_at->format('d M Y, H:i');
            $userAnswer->formatted_updated_at = $userAnswer->updated_at->format('d M Y, H:i');

            if ($userAnswer->status == 'completed' && $userAnswer->created_at) {
                $userAnswer->formatted_created_at = $userAnswer->created_at->format('d M Y, H:i');
            }
        }

        return view('company.questionnaire.results', compact('userAnswers'));
    }
    
    public function responseDetail($id_periode, $id_user_answer)
    {
        $userId = Auth::id();
        
        $userAnswer = Tb_User_Answers::where('id_user_answer', $id_user_answer)
            ->where('id_user', $userId)
            ->with(['periode', 'alumni'])
            ->firstOrFail();
        
        $company = auth()->user()->company ?? session('company');
        
        if (!$company) {
            $company = (object) [
                'company_name' => 'Nama Perusahaan Tidak Ditemukan',
                'address' => 'Alamat Tidak Ditemukan'
            ];
        }
        
        $categories = Tb_Category::where('id_periode', $id_periode)
            ->whereIn('for_type', ['company', 'both'])
            ->orderBy('order')
            ->get();
        
        $questionsWithAnswers = [];
        
        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)
                ->where('status', 'visible')
                ->with('options')
                ->orderBy('order')
                ->get();

            $categoryData = [
                'category' => $category,
                'questions' => []
            ];

            foreach ($questions as $question) {
                $answers = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->where('id_question', $question->id_question)
                    ->get();

                // ✅ PERBAIKAN: Process answers and set hasAnswer flag
                $hasAnswer = $answers->isNotEmpty();
                $processedAnswerData = $this->processAnswersForDisplay($question, $answers);
                
                $questionData = [
                    'question' => $question,
                    'answers' => $answers,
                    'hasAnswer' => $hasAnswer,
                    'answer' => $processedAnswerData['answer'],
                    'otherAnswer' => $processedAnswerData['otherAnswer'],
                    'otherOption' => $processedAnswerData['otherOption'],
                    'multipleAnswers' => $processedAnswerData['multipleAnswers'],
                    'multipleOtherAnswers' => $processedAnswerData['multipleOtherAnswers'],
                    'ratingOption' => $processedAnswerData['ratingOption'] ?? null // ✅ TAMBAHAN
                ];

                $categoryData['questions'][] = $questionData;
            }

            $questionsWithAnswers[] = $categoryData;
        }
        
        return view('company.questionnaire.response-detail', compact('userAnswer', 'questionsWithAnswers', 'company'));
    }

    /**
     * PERBAIKAN: Helper method sesuai struktur database yang sebenarnya
     */
    private function processQuestionAnswer($request, $userAnswer, $question)
    {
        $questionId = $question->id_question;
        $questionType = $question->type; // Gunakan 'type' sesuai database

        // Handle different question types berdasarkan struktur database yang sebenarnya
        switch ($questionType) {
            case 'text':
            case 'email':
            case 'numeric':
            case 'date':
                $answer = $request->input("question_{$questionId}");
                if ($answer !== null && $answer !== '') {
                    Tb_User_Answer_Item::create([
                        'id_user_answer' => $userAnswer->id_user_answer,
                        'id_question' => $questionId,
                        'answer' => $answer
                    ]);
                }
                break;

            case 'option':
            case 'rating':
            case 'scale':
                $answer = $request->input("question_{$questionId}");
                $otherAnswer = $request->input("question_{$questionId}_other");
                
                if ($answer) {
                    // Cari option ID berdasarkan value
                    $option = Tb_Question_Options::where('id_question', $questionId)
                        ->where('option', $answer)
                        ->first();
                    
                    Tb_User_Answer_Item::create([
                        'id_user_answer' => $userAnswer->id_user_answer,
                        'id_question' => $questionId,
                        'id_questions_options' => $option ? $option->id_questions_options : null,
                        'answer' => $answer,
                        'other_answer' => $otherAnswer
                    ]);
                }
                break;

            case 'multiple':
                $answers = $request->input("question_{$questionId}", []);
                
                foreach ($answers as $answer) {
                    $otherAnswer = $request->input("question_{$questionId}_other_{$answer}");
                    
                    // Cari option ID berdasarkan value
                    $option = Tb_Question_Options::where('id_question', $questionId)
                        ->where('option', $answer)
                        ->first();
                    
                    Tb_User_Answer_Item::create([
                        'id_user_answer' => $userAnswer->id_user_answer,
                        'id_question' => $questionId,
                        'id_questions_options' => $option ? $option->id_questions_options : null,
                        'answer' => $answer,
                        'other_answer' => $otherAnswer
                    ]);
                }
                break;

            case 'location':
                // ✅ PERBAIKAN: Handle location_combined format properly
                $combinedLocationData = $request->input("location_combined.{$questionId}");
    
                if (!empty($combinedLocationData)) {
                    try {
                        // Parse location data to verify it's valid JSON
                        $locationObject = json_decode($combinedLocationData, true);
                        
                        // Verify that we have the required location data
                        if ($locationObject && 
                            isset($locationObject['country']['code']) && 
                            isset($locationObject['state']['name']) && 
                            isset($locationObject['city']['name'])) {
                            
                            // Save the complete JSON data
                            Tb_User_Answer_Item::create([
                                'id_user_answer' => $userAnswer->id_user_answer,
                                'id_question' => $questionId,
                                'answer' => $combinedLocationData,
                                'id_questions_options' => null,
                                'other_answer' => null
                            ]);
                            
                            \Log::info('Company location answer saved successfully', [
                                'question_id' => $questionId,
                                'user_answer_id' => $userAnswer->id_user_answer,
                                'location_data' => $locationObject
                            ]);
                        } else {
                            \Log::warning('Invalid location data structure', [
                                'question_id' => $questionId,
                                'raw_data' => $combinedLocationData
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save company location answer', [
                            'question_id' => $questionId,
                            'error' => $e->getMessage(),
                            'raw_data' => $combinedLocationData
                        ]);
                    }
                } else {
                    \Log::info('No location data provided for question', [
                        'question_id' => $questionId,
                        'all_location_inputs' => $request->input('location_combined', [])
                    ]);
                }
                break;
        }
    }

    /**
     * ✅ TAMBAHAN: Helper method untuk memproses jawaban untuk ditampilkan
     */
    private function processAnswersForDisplay($question, $answers)
    {
        $result = [
            'answer' => null,
            'otherAnswer' => null,
            'otherOption' => null,
            'ratingOption' => null,
            'multipleAnswers' => [],
            'multipleOtherAnswers' => []
        ];

        if ($answers->isEmpty()) {
            return $result;
        }

        switch ($question->type) {
            case 'text':
            case 'email':
            case 'numeric':
            case 'date':
                $firstAnswer = $answers->first();
                $result['answer'] = $firstAnswer->answer;
                $result['otherAnswer'] = $firstAnswer->other_answer;
                break;

            case 'option':
                $firstAnswer = $answers->first();
                if ($firstAnswer->id_questions_options) {
                    $option = Tb_Question_Options::find($firstAnswer->id_questions_options);
                    $result['answer'] = $option ? $option->option : $firstAnswer->answer;
                    
                    if ($option && $option->is_other_option && $firstAnswer->other_answer) {
                        $result['otherAnswer'] = $firstAnswer->other_answer;
                        $result['otherOption'] = $option;
                    }
                } else {
                    $result['answer'] = $firstAnswer->answer;
                }
                break;

            case 'rating':
                $firstAnswer = $answers->first();
                
                $ratingOption = null;
                
                if ($firstAnswer->id_questions_options) {
                    $ratingOption = Tb_Question_Options::find($firstAnswer->id_questions_options);
                }
                
                if (!$ratingOption && is_numeric($firstAnswer->answer)) {
                    $ratingOption = Tb_Question_Options::find($firstAnswer->answer);
                    
                    if (!$ratingOption) {
                        $ratingOption = Tb_Question_Options::where('id_question', $question->id_question)
                            ->where('id_questions_options', $firstAnswer->answer)
                            ->first();
                    }
                }
                
                if ($ratingOption) {
                    $result['answer'] = $ratingOption->option;
                    $result['ratingOption'] = $ratingOption;
                    
                    if ($ratingOption->is_other_option && $firstAnswer->other_answer) {
                        $result['otherAnswer'] = $firstAnswer->other_answer;
                    }
                } else {
                    $result['answer'] = $firstAnswer->answer;
                    $result['ratingOption'] = null;
                }
                break;

            case 'scale':
                $firstAnswer = $answers->first();
                $result['answer'] = $firstAnswer->answer;
                break;

            case 'multiple':
                foreach ($answers as $answer) {
                    // ✅ PERBAIKAN UTAMA: Konsisten dengan admin controller
                    if ($answer->id_questions_options) {
                        $option = Tb_Question_Options::find($answer->id_questions_options);
                        if ($option) {
                            // ✅ SIMPAN TEXT OPTION, BUKAN ID (sama seperti admin)
                            $result['multipleAnswers'][] = $option->option;
                            
                            if ($option->is_other_option && $answer->other_answer) {
                                // ✅ GUNAKAN ID OPTION SEBAGAI KEY untuk other answers
                                $result['multipleOtherAnswers'][$option->id_questions_options] = $answer->other_answer;
                            }
                        } else {
                            $result['multipleAnswers'][] = $answer->answer;
                        }
                    } else {
                        // ✅ PERBAIKAN: Jika tidak ada id_questions_options, cek apakah answer berisi ID
                        if (is_numeric($answer->answer)) {
                            $option = Tb_Question_Options::find($answer->answer);
                            if ($option) {
                                $result['multipleAnswers'][] = $option->option;
                                
                                if ($option->is_other_option && $answer->other_answer) {
                                    $result['multipleOtherAnswers'][$option->id_questions_options] = $answer->other_answer;
                                }
                            } else {
                                $result['multipleAnswers'][] = $answer->answer;
                            }
                        } else {
                            // Answer sudah berupa text
                            $result['multipleAnswers'][] = $answer->answer;
                            
                            // ✅ TAMBAHAN: Cari option berdasarkan text untuk other answer
                            $option = Tb_Question_Options::where('id_question', $question->id_question)
                                ->where('option', $answer->answer)
                                ->first();
                            
                            if ($option && $option->is_other_option && $answer->other_answer) {
                                $result['multipleOtherAnswers'][$option->id_questions_options] = $answer->other_answer;
                            }
                        }
                    }
                }
                break;

            case 'location':
                $firstAnswer = $answers->first();
                try {
                    $locationData = json_decode($firstAnswer->answer, true);
                    if (is_array($locationData) && isset($locationData['display'])) {
                        $result['answer'] = $locationData['display'];
                    } else {
                        $result['answer'] = $firstAnswer->answer;
                    }
                } catch (\Exception $e) {
                    $result['answer'] = $firstAnswer->answer;
                }
                break;

            default:
                $firstAnswer = $answers->first();
                $result['answer'] = $firstAnswer->answer;
                break;
        }

        // ✅ DEBUG: Log untuk debugging multiple choice
        if ($question->type === 'multiple' && config('app.debug')) {
            \Log::debug('Company processAnswersForDisplay - Multiple choice result', [
                'question_id' => $question->id_question,
                'raw_answers_count' => $answers->count(),
                'processed_multipleAnswers' => $result['multipleAnswers'],
                'processed_multipleOtherAnswers' => $result['multipleOtherAnswers'],
                'raw_answers_data' => $answers->map(function($ans) {
                    return [
                        'id_questions_options' => $ans->id_questions_options,
                        'answer' => $ans->answer,
                        'other_answer' => $ans->other_answer
                    ];
                })->toArray()
            ]);
        }

        return $result;
    }
}