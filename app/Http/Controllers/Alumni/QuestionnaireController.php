<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use App\Models\Tb_Periode;
use App\Models\Tb_User_Answers;
use App\Models\Tb_User_Answer_Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active periods for alumni
        $periodes = Tb_Periode::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
        
        return view('alumni.questionnaire.index', compact('periodes'));
    }
    
    public function fill($id_periode, $category = null)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        
        // Check if period is active
        if ($periode->status !== 'active') {
            return redirect()->route('alumni.questionnaire.index')->with('error', 'Periode kuesioner tidak aktif.');
        }
        
        // Get all categories for this period (alumni or both types)
        $allCategories = Tb_Category::where('id_periode', $id_periode)
            ->where(function($query) {
                $query->where('for_type', 'alumni')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get();
            
        if ($allCategories->isEmpty()) {
            return redirect()->route('alumni.questionnaire.index')->with('error', 'Belum ada kategori untuk kuesioner ini.');
        }
        
        // Determine current category
        if (!$category) {
            $currentCategory = $allCategories->first();
        } else {
            $currentCategory = Tb_Category::findOrFail($category);
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
            ->with('options')
            ->orderBy('order')
            ->get();
    
        // Get previously saved answers if any
        $prevAnswers = [];
        $prevOtherAnswers = [];
        $prevMultipleAnswers = [];
        $prevMultipleOtherAnswers = [];
        $prevLocationAnswers = []; // NEW: For location questions
    
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
                
                if ($question->type == 'text' || $question->type == 'date') {
                    $prevAnswers[$item->id_question] = $item->answer;
                    
                } elseif ($question->type == 'location') {
                    // Parse location JSON data
                    $locationData = json_decode($item->answer, true);
                    if ($locationData && is_array($locationData)) {
                        $prevLocationAnswers[$item->id_question] = $locationData;
                        // Also set the display value for the form
                        $prevAnswers[$item->id_question] = $locationData['display'] ?? '';
                    } else {
                        // Fallback for old data format
                        $prevAnswers[$item->id_question] = $item->answer;
                    }
                    
                } elseif ($question->type == 'option') {
                    $prevAnswers[$item->id_question] = $item->id_questions_options;
                    
                    if ($item->other_answer) { // Using other_answer column
                        $prevOtherAnswers[$item->id_question] = $item->other_answer;
                    }
                    
                } elseif ($question->type == 'multiple') {
                    if (!isset($prevMultipleAnswers[$item->id_question])) {
                        $prevMultipleAnswers[$item->id_question] = [];
                    }
                    $prevMultipleAnswers[$item->id_question][] = $item->id_questions_options;
                    
                    if ($item->other_answer) { // Using other_answer column
                        if (!isset($prevMultipleOtherAnswers[$item->id_question])) {
                            $prevMultipleOtherAnswers[$item->id_question] = [];
                        }
                        $prevMultipleOtherAnswers[$item->id_question][$item->id_questions_options] = $item->other_answer;
                    }
                } elseif ($question->type == 'rating' || $question->type == 'scale') {
                    // For rating and scale questions - get the option ID that was selected
                    $prevAnswers[$item->id_question] = $item->id_questions_options;
                    
                } elseif ($question->type == 'option') {
                    $prevAnswers[$item->id_question] = $item->id_questions_options;
                    
                    if ($item->other_answer) { // Using other_answer column
                        $prevOtherAnswers[$item->id_question] = $item->other_answer;
                    }
                    
                } elseif ($question->type == 'multiple') {
                    if (!isset($prevMultipleAnswers[$item->id_question])) {
                        $prevMultipleAnswers[$item->id_question] = [];
                    }
                    $prevMultipleAnswers[$item->id_question][] = $item->id_questions_options;
                    
                    if ($item->other_answer) { // Using other_answer column
                        if (!isset($prevMultipleOtherAnswers[$item->id_question])) {
                            $prevMultipleOtherAnswers[$item->id_question] = [];
                        }
                        $prevMultipleOtherAnswers[$item->id_question][$item->id_questions_options] = $item->other_answer;
                    }
                }
            }
        }
    
        return view('alumni.questionnaire.fill', compact(
            'periode',
            'allCategories',
            'currentCategory',
            'prevCategory',
            'nextCategory',
            'questions',
            'prevAnswers',
            'prevOtherAnswers',
            'prevMultipleAnswers',
            'prevMultipleOtherAnswers',
            'prevLocationAnswers', // NEW
            'userAnswer',
            'currentCategoryIndex'
        ));
    }
    
    public function submit(Request $request, $id_periode)
    {
        try {
            // Debug log - capture ALL form data including other answers
            \Illuminate\Support\Facades\Log::info('Alumni questionnaire submission - FULL DEBUG', [
                'action' => $request->input('action'),
                'category_id' => $request->input('id_category'),
                'answers' => $request->input('answers'),
                'other_answers' => $request->input('other_answers'),
                'multiple' => $request->input('multiple'),
                'multiple_other_answers' => $request->input('multiple_other_answers'),
                'location_combined' => $request->input('location_combined'),
                'all_request_data' => $request->all()
            ]);
            
            // Get current category
            $category = Tb_Category::findOrFail($request->input('id_category'));
            
            // Find or create user answer
            $userId = Auth::id();
            $userAnswer = Tb_User_Answers::firstOrCreate(
                [
                    'id_user' => $userId,
                    'id_periode' => $id_periode
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
                        
                        \Log::info('Saved text answer', [
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
                        
                        \Log::info('Saved date answer', [
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
                        
                        \Log::info('Saved location answer', [
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
                            
                            \Log::info('Processing other option for radio', [
                                'question_id' => $question->id_question,
                                'option_id' => $selectedOption,
                                'option_text' => $option->option,
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
                            'other_answer' => $otherAnswer // Using other_answer column
                        ]);
                        
                        \Log::info('Saved radio answer', [
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
                            
                            \Log::info('Processing other option for multiple', [
                                'question_id' => $question->id_question,
                                'option_id' => $optionId,
                                'option_text' => $option->option,
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
                            'other_answer' => $otherAnswer // Using other_answer column
                        ]);
                        
                        \Log::info('Saved checkbox answer', [
                            'question_id' => $question->id_question,
                            'option_id' => $optionId,
                            'other_answer' => $otherAnswer,
                            'saved_id' => $savedAnswer->id_user_answer_item
                        ]);
                    }
                } elseif ($question->type == 'rating' || $question->type == 'scale') {
                    // Handle rating and scale questions (similar to option questions)
                    $selectedOption = $request->input("answers.{$question->id_question}");
                    if (!empty($selectedOption)) {
                        Tb_User_Answer_Item::create([
                            'id_user_answer' => $userAnswer->id_user_answer,
                            'id_question' => $question->id_question,
                            'id_questions_options' => $selectedOption,
                            'answer' => null,
                            'other_answer' => null
                        ]);
                        
                        \Log::info('Saved rating/scale answer', [
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
                    $query->where('for_type', 'alumni')
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
                    return redirect()->route('alumni.questionnaire.fill', [$id_periode, $nextCategory->id_category])
                        ->with('success', 'Jawaban berhasil disimpan. Lanjut ke kategori berikutnya.');
                } else {
                    // This is the last category, mark as completed
                    $userAnswer->update([
                        'status' => 'completed',
                        'submitted_at' => now()
                    ]);
                    
                    return redirect()->route('alumni.questionnaire.thank-you')
                        ->with('success', 'Kuesioner berhasil diselesaikan!');
                }
                
            } elseif ($action == 'submit_final') {
                // Mark as completed
                $userAnswer->update([
                    'status' => 'completed',
                    'submitted_at' => now()
                ]);
                
                return redirect()->route('alumni.questionnaire.thank-you')
                    ->with('success', 'Kuesioner berhasil diselesaikan!');
            }
            
            // Fallback
            return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error submitting alumni questionnaire:', [
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
        return view('alumni.questionnaire.thank-you');
    }
    
    /**
     * Display a listing of the user's questionnaire results.
     */
    public function results()
    {
        $userId = Auth::id();
        
        // Get all user answers
        $userAnswers = Tb_User_Answers::where('id_user', $userId)
            ->with('periode')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('alumni.questionnaire.results', compact('userAnswers'));
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
            ->firstOrFail();
        
        // Get categories for this period - PERBAIKAN: Hanya ambil kategori untuk alumni
        $categories = Tb_Category::where('id_periode', $id_periode)
            ->where(function($query) {
                $query->where('for_type', 'alumni')
                      ->orWhere('for_type', 'both');
            })
            ->orderBy('order')
            ->get();
        
        // Get questions and answers
        $questionsWithAnswers = [];
        
        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)
                ->with('options')
                ->orderBy('order')
                ->get();
            
            $categoryAnswers = [];
            
            foreach ($questions as $question) {
                $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->where('id_question', $question->id_question)
                    ->get();
                
                $answerData = [];
                $multipleAnswers = [];
                
                foreach ($answerItems as $item) {
                    if ($question->type == 'location') {
                        // Parse location JSON data
                        $locationData = json_decode($item->answer, true);
                        if ($locationData && is_array($locationData)) {
                            $answerData[] = [
                                'type' => 'location',
                                'value' => $locationData['display'] ?? '',
                                'province' => $locationData['province'] ?? '',
                                'city' => $locationData['city'] ?? ''
                            ];
                        } else {
                            $answerData[] = [
                                'type' => 'text',
                                'value' => $item->answer
                            ];
                        }
                        
                    } elseif ($question->type == 'option') {
                        $option = Tb_Question_Options::find($item->id_questions_options);
                        if ($option) {
                            // For option questions, we show the option name separately from the other text
                            $answerData[] = [
                                'type' => 'option',
                                'base_option_text' => $option->option,
                                'value' => $option->option,
                                'option_id' => $item->id_questions_options,
                                'is_other' => $option->is_other_option,
                                'other_text' => $item->other_answer, // Using other_answer column
                                'other_before_text' => $option->other_before_text,
                                'other_after_text' => $option->other_after_text
                            ];
                        } else {
                            $answerData[] = [
                                'type' => 'option',
                                'value' => 'Unknown option',
                                'option_id' => $item->id_questions_options
                            ];
                        }
                        
                    } elseif ($question->type == 'multiple') {
                        $option = Tb_Question_Options::find($item->id_questions_options);
                        if ($option) {
                            $displayText = $option->option;
                            
                            // Handle "other" option with user input
                            if ($option->is_other_option && !empty($item->other_answer)) { // Using other_answer
                                $otherDisplay = '';
                                if ($option->other_before_text) {
                                    $otherDisplay .= $option->other_before_text . ' ';
                                }
                                $otherDisplay .= $item->other_answer; // Using other_answer
                                if ($option->other_after_text) {
                                    $otherDisplay .= ' ' . $option->other_after_text;
                                }
                                $displayText .= ': ' . $otherDisplay;
                            }
                            
                            $multipleAnswers[] = $displayText;
                        } else {
                            $multipleAnswers[] = 'Unknown option';
                        }
                    } elseif ($question->type == 'rating' || $question->type == 'scale') {
                        $option = Tb_Question_Options::find($item->id_questions_options);
                        if ($option) {
                            $answerData[] = [
                                'type' => $question->type,
                                'value' => $option->option,
                                'option_id' => $item->id_questions_options
                            ];
                        } else {
                            $answerData[] = [
                                'type' => $question->type,
                                'value' => 'Unknown option',
                                'option_id' => $item->id_questions_options
                            ];
                        }
                    } else {
                        // Text, date, etc.
                        $answerData[] = [
                            'type' => $question->type,
                            'value' => $item->answer
                        ];
                    }
                }
                
                $categoryAnswers[] = [
                    'question' => $question,
                    'answers' => $answerData,
                    'multipleAnswers' => $multipleAnswers
                ];
            }
            
            $questionsWithAnswers[] = [
                'category' => $category,
                'questions' => $categoryAnswers
            ];
        }
        
        return view('alumni.questionnaire.response-detail', compact('userAnswer', 'questionsWithAnswers'));
    }

    /**
     * Organize questions by their dependency relationships
     * 
     * @param Collection $questions
     * @return Collection
     */
    protected function organizeQuestionsByDependency($questions)
    {
        // Create a mapping of all questions by ID for easy lookup
        $questionMap = $questions->keyBy('id_question');
        
        // Validate the dependencies of each question
        foreach ($questions as $question) {
            if ($question->depends_on) {
                $parentQuestion = $questionMap->get($question->depends_on);
                
                // Verify the parent question exists and is in the same category
                if ($parentQuestion) {
                    $question->parentQuestion = $parentQuestion;
                    
                    // Parent must be an 'option' type to support dependencies
                    if ($parentQuestion->type !== 'option') {
                        \Log::warning("Question {$question->id_question} depends on a non-option question ({$parentQuestion->id_question}). Removing dependency.");
                        $question->depends_on = null;
                        $question->depends_value = null;
                    }
                } else {
                    \Log::warning("Question {$question->id_question} depends on a non-existent question ({$question->depends_on}). Removing dependency.");
                    $question->depends_on = null;
                    $question->depends_value = null;
                }
            }
        }
        
        return $questions;
    }

    /**
     * Get provinces from external API
     */
    public function getProvinces()
    {
        try {
            // Using Indonesia Region API
            $response = file_get_contents('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            $provinces = json_decode($response, true);
            
            if ($provinces) {
                return response()->json([
                    'success' => true,
                    'data' => $provinces
                ]);
            }
            
            // Fallback data if API fails
            $fallbackProvinces = [
                ['id' => '11', 'name' => 'ACEH'],
                ['id' => '12', 'name' => 'SUMATERA UTARA'],
                ['id' => '13', 'name' => 'SUMATERA BARAT'],
                ['id' => '14', 'name' => 'RIAU'],
                ['id' => '15', 'name' => 'JAMBI'],
                ['id' => '16', 'name' => 'SUMATERA SELATAN'],
                ['id' => '17', 'name' => 'BENGKULU'],
                ['id' => '18', 'name' => 'LAMPUNG'],
                ['id' => '19', 'name' => 'KEPULAUAN BANGKA BELITUNG'],
                ['id' => '21', 'name' => 'KEPULAUAN RIAU'],
                ['id' => '31', 'name' => 'DKI JAKARTA'],
                ['id' => '32', 'name' => 'JAWA BARAT'],
                ['id' => '33', 'name' => 'JAWA TENGAH'],
                ['id' => '34', 'name' => 'DI YOGYAKARTA'],
                ['id' => '35', 'name' => 'JAWA TIMUR'],
                ['id' => '36', 'name' => 'BANTEN'],
                ['id' => '51', 'name' => 'BALI'],
                ['id' => '52', 'name' => 'NUSA TENGGARA BARAT'],
                ['id' => '53', 'name' => 'NUSA TENGGARA TIMUR'],
                ['id' => '61', 'name' => 'KALIMANTAN BARAT'],
                ['id' => '62', 'name' => 'KALIMANTAN TENGAH'],
                ['id' => '63', 'name' => 'KALIMANTAN SELATAN'],
                ['id' => '64', 'name' => 'KALIMANTAN TIMUR'],
                ['id' => '65', 'name' => 'KALIMANTAN UTARA'],
                ['id' => '71', 'name' => 'SULAWESI UTARA'],
                ['id' => '72', 'name' => 'SULAWESI TENGAH'],
                ['id' => '73', 'name' => 'SULAWESI SELATAN'],
                ['id' => '74', 'name' => 'SULAWESI TENGGARA'],
                ['id' => '75', 'name' => 'GORONTALO'],
                ['id' => '76', 'name' => 'SULAWESI BARAT'],
                ['id' => '81', 'name' => 'MALUKU'],
                ['id' => '82', 'name' => 'MALUKU UTARA'],
                ['id' => '91', 'name' => 'PAPUA BARAT'],
                ['id' => '94', 'name' => 'PAPUA']
            ];
            
            return response()->json([
                'success' => true,
                'data' => $fallbackProvinces
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching provinces: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi'
            ], 500);
        }
    }

    /**
     * Get cities/regencies by province ID from external API
     */
    public function getCities($provinceId)
    {
        try {
            // Using Indonesia Region API
            $response = file_get_contents("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
            $cities = json_decode($response, true);
            
            if ($cities) {
                return response()->json([
                    'success' => true,
                    'data' => $cities
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Data kota/kabupaten tidak ditemukan'
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching cities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kota/kabupaten'
            ], 500);
        }
    }
}
