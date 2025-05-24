<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_User_Answers;
use App\Models\Tb_Periode;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use App\Models\Tb_User_Answer_Item;
use App\Models\Tb_User;
use App\Models\Tb_Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the questionnaires.
     */
    public function index(Request $request)
    {
        // Update all statuses before displaying
        Tb_Periode::updateAllStatuses();
        
        $query = Tb_Periode::with('categories');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $periodeIds = Tb_Periode::where('status', 'LIKE', "%{$search}%")->pluck('id_periode');
            
            $categoryIds = Tb_Category::where('category_name', 'LIKE', "%{$search}%")->pluck('id_category');
            
            $query->whereIn('id_periode', $periodeIds)
                  ->orWhereHas('categories', function($q) use ($categoryIds) {
                      $q->whereIn('id_category', $categoryIds);
                  });
        }

        $periodes = $query->latest()->paginate(10);

        return view('admin.questionnaire.index', compact('periodes'));
    }

    /**
     * Show the form for creating a new questionnaire periode.
     */
    public function create()
    {
        return view('admin.questionnaire.create');
    }

    /**
     * Store a newly created questionnaire periode.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $periode = new Tb_Periode([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        // Status will be automatically set in the model's saving event
        $periode->save();

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Periode kuisioner berhasil ditambahkan.');
    }

    /**
     * Display the specified questionnaire periode.
     */
    public function show($id_periode)
    {
        $periode = Tb_Periode::with(['categories.questions.options'])->findOrFail($id_periode);
        
        // Update status before displaying
        if ($periode->status !== $periode->calculateStatus()) {
            $periode->status = $periode->calculateStatus();
            $periode->save();
        }
        
        return view('admin.questionnaire.show', compact('periode'));
    }

    /**
     * Show the form for editing the specified questionnaire periode.
     */
    public function edit($id_periode)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        return view('admin.questionnaire.edit', compact('periode'));
    }

    /**
     * Update the specified questionnaire periode.
     */
    public function update(Request $request, $id_periode)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $periode = Tb_Periode::findOrFail($id_periode);
        $periode->start_date = $request->start_date;
        $periode->end_date = $request->end_date;
        
        // Status will be automatically updated in the model's saving event
        $periode->save();

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Periode kuisioner berhasil diperbarui.');
    }

    /**
     * Remove the specified questionnaire periode.
     */
    public function destroy($id_periode)
    {
        try {
            $periode = Tb_Periode::findOrFail($id_periode);
            $periode->delete();

            return redirect()->route('admin.questionnaire.index')
                ->with('success', 'Periode kuisioner berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting periode: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus periode.');
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function createCategory($id_periode)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        return view('admin.questionnaire.category.create', compact('periode'));
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request, $id_periode)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'order' => 'required|integer',
            'for_type' => 'required|in:alumni,company,both',
        ]);

        $periode = Tb_Periode::findOrFail($id_periode);
        
        $category = Tb_Category::create([
            'id_periode' => $id_periode,
            'category_name' => $request->category_name,
            'order' => $request->order,
            'for_type' => $request->for_type,
        ]);

        return redirect()->route('admin.questionnaire.show', $id_periode)
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a category.
     */
    public function editCategory($id_periode, $id_category)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $category = Tb_Category::findOrFail($id_category);
        
        return view('admin.questionnaire.category.edit', compact('periode', 'category'));
    }

    /**
     * Update the specified category.
     */
    public function updateCategory(Request $request, $id_periode, $id_category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'order' => 'required|integer',
            'for_type' => 'required|in:alumni,company,both',
        ]);

        $category = Tb_Category::findOrFail($id_category);
        $category->update([
            'category_name' => $request->category_name,
            'order' => $request->order,
            'for_type' => $request->for_type,
        ]);

        return redirect()->route('admin.questionnaire.show', $id_periode)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroyCategory($id_periode, $id_category)
    {
        try {
            $category = Tb_Category::findOrFail($id_category);
            $category->delete();

            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting category: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus kategori.');
        }
    }

    /**
     * Show the form for creating a new question.
     */
    public function createQuestion($id_periode, $id_category)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $category = Tb_Category::findOrFail($id_category);
        
        // Get questions that can be used as conditions (already existing questions)
        $availableQuestions = Tb_Questions::where('id_category', $id_category)->get();
        
        return view('admin.questionnaire.question.create', compact(
            'periode', 'category', 'availableQuestions'
        ));
    }

    /**
     * Store a newly created question.
     */
    public function storeQuestion(Request $request, $id_periode, $id_category)
    {
        // Debug: Log all incoming request data
        \Log::info('=== Question Create Request Debug ===', [
            'all_data' => $request->all(),
            'question_type' => $request->input('question_type'),
            'options' => $request->input('options', []),
            'rating_options' => $request->input('rating_options', []),
            'scale_options' => $request->input('scale_options', []),
            'has_dependency' => $request->input('has_dependency'),
            'before_text' => $request->input('before_text'),
            'after_text' => $request->input('after_text'),
        ]);

        try {
            $questionType = $request->input('question_type');
            
            // Define validation rules
            $rules = [
                'question' => 'required|string|max:1000',
                'question_type' => 'required|in:text,option,multiple,rating,scale,date,location',
                'order' => 'required|integer|min:1',
                'before_text' => 'nullable|string|max:255',
                'after_text' => 'nullable|string|max:255',
                'has_dependency' => 'nullable|boolean',
                'depends_on' => 'nullable|integer|exists:tb_questions,id_question',
                'depends_value' => 'nullable|string|max:255',
                'other_options' => 'nullable|array',
                'other_options.*' => 'nullable|integer',
                'other_before_text' => 'nullable|array',
                'other_before_text.*' => 'nullable|string|max:255',
                'other_after_text' => 'nullable|array',
                'other_after_text.*' => 'nullable|string|max:255',
                'rating_options' => 'nullable|array',
                'rating_options.*' => 'nullable|string|max:255',
                'scale_options' => 'nullable|array',
                'scale_options.*' => 'nullable|string|max:255',
            ];

            // Add conditional validation for options based on question type
            if ($questionType === 'option' || $questionType === 'multiple') {
                $rules['options'] = 'required|array|min:1';
                $rules['options.*'] = 'required|string|max:255';
            } elseif ($questionType === 'rating') {
                $rules['rating_options'] = 'required|array|min:1';
                $rules['rating_options.*'] = 'required|string|max:255';
            } elseif ($questionType === 'scale') {
                $rules['scale_options'] = 'required|array|min:1';
                $rules['scale_options.*'] = 'required|string|max:255';
            }

            $messages = [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question_type.required' => 'Tipe pertanyaan wajib dipilih.',
                'question_type.in' => 'Tipe pertanyaan tidak valid.',
                'order.required' => 'Urutan pertanyaan wajib diisi.',
                'order.min' => 'Urutan pertanyaan minimal 1.',
                'options.required' => 'Pilihan jawaban wajib diisi untuk tipe pertanyaan pilihan.',
                'options.min' => 'Minimal harus ada satu pilihan jawaban.',
                'options.*.required' => 'Setiap pilihan jawaban harus diisi.',
                'rating_options.required' => 'Opsi rating diperlukan.',
                'scale_options.required' => 'Opsi skala diperlukan.',
            ];

            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for question creation', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();
            
            // Prepare question data
            $questionData = [
                'id_category' => $id_category,
                'question' => $validated['question'],
                'type' => $validated['question_type'],
                'before_text' => $request->input('before_text', ''),
                'after_text' => $request->input('after_text', ''),
                'order' => $validated['order'],
            ];

            // Handle dependency
            if ($request->input('has_dependency') && $request->input('depends_on')) {
                $questionData['depends_on'] = $request->input('depends_on');
                $questionData['depends_value'] = $request->input('depends_value');
            } else {
                $questionData['depends_on'] = null;
                $questionData['depends_value'] = null;
            }

            \Log::info('Creating question with data:', $questionData);
            
            $question = Tb_Questions::create($questionData);
            
            // Handle options for types that support them
            if (in_array($validated['question_type'], ['option', 'multiple', 'rating', 'scale'])) {
                $options = [];
                $otherOptions = $request->input('other_options', []);
                $otherBeforeTexts = $request->input('other_before_text', []);
                $otherAfterTexts = $request->input('other_after_text', []);
                
                // Get the right options array based on question type
                if ($validated['question_type'] === 'rating') {
                    $options = $request->input('rating_options', []);
                } elseif ($validated['question_type'] === 'scale') {
                    $options = $request->input('scale_options', []);
                } else {
                    $options = $request->input('options', []);
                }
                
                \Log::info('Processing options:', [
                    'question_type' => $validated['question_type'],
                    'options' => $options,
                    'other_options' => $otherOptions,
                ]);
                
                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        $isOther = in_array($index, $otherOptions);
                        $otherBeforeText = isset($otherBeforeTexts[$index]) ? $otherBeforeTexts[$index] : '';
                        $otherAfterText = isset($otherAfterTexts[$index]) ? $otherAfterTexts[$index] : '';
                        
                        $optionData = [
                            'id_question' => $question->id_question,
                            'option' => trim($optionText),
                            'order' => $index + 1,
                            'is_other_option' => $isOther,
                            'other_before_text' => $otherBeforeText,
                            'other_after_text' => $otherAfterText,
                        ];
                        
                        \Log::info('Creating option:', $optionData);
                        
                        Tb_Question_Options::create($optionData);
                    }
                }
            }
            
            DB::commit();
            
            \Log::info('Question and options created successfully');
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating question:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a question.
     */
    public function editQuestion($id_periode, $id_category, $id_question)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        $category = Tb_Category::findOrFail($id_category);
        $question = Tb_Questions::with('options')->findOrFail($id_question);
        
        // Get available questions for dependency - UPDATED to include rating and scale
        $availableQuestions = Tb_Questions::with('options')
            ->where('id_category', $id_category)
            ->where('id_question', '!=', $id_question)  // Exclude current question
            ->whereIn('type', ['option', 'multiple', 'rating', 'scale'])  // Include rating and scale
            ->orderBy('order')
            ->get();
        
        return view('admin.questionnaire.question.edit', compact(
            'periode', 
            'category', 
            'question', 
            'availableQuestions'
        ));
    }

    /**
     * Update the specified question.
     */
    public function updateQuestion(Request $request, $id_periode, $id_category, $id_question)
    {
        \Log::info('=== Question Update Request Debug ===', [
            'all_data' => $request->all(),
            'question_type' => $request->input('question_type'),
            'options' => $request->input('options', []),
            'rating_options' => $request->input('rating_options', []),
            'scale_options' => $request->input('scale_options', []),
            'has_dependency' => $request->input('has_dependency'),
        ]);

        try {
            $questionType = $request->input('question_type');
            
            // Define validation rules
            $rules = [
                'question' => 'required|string|max:1000',
                'question_type' => 'required|in:text,option,multiple,rating,scale,date,location',
                'order' => 'required|integer|min:1',
                'before_text' => 'nullable|string|max:255',
                'after_text' => 'nullable|string|max:255',
                'has_dependency' => 'nullable|boolean',
                'depends_on' => 'nullable|integer|exists:tb_questions,id_question',
                'depends_value' => 'nullable|string|max:255',
                'hidden_depends_on' => 'nullable|integer|exists:tb_questions,id_question',
                'hidden_depends_value' => 'nullable|string|max:255',
                'other_options' => 'nullable|array',
                'other_options.*' => 'nullable|integer',
                'other_before_text' => 'nullable|array',
                'other_before_text.*' => 'nullable|string|max:255',
                'other_after_text' => 'nullable|array',
                'other_after_text.*' => 'nullable|string|max:255',
                'rating_options' => 'nullable|array',
                'rating_options.*' => 'nullable|string|max:255',
                'scale_options' => 'nullable|array',
                'scale_options.*' => 'nullable|string|max:255',
                'option_ids' => 'nullable|array',
                'option_ids.*' => 'nullable|integer',
                'is_other_option' => 'nullable|array',
                'is_other_option.*' => 'nullable|integer',
            ];

            // Add conditional validation for options based on question type
            if ($questionType === 'option' || $questionType === 'multiple') {
                $rules['options'] = 'required|array|min:1';
                $rules['options.*'] = 'required|string|max:255';
            } elseif ($questionType === 'rating') {
                $rules['rating_options'] = 'required|array|min:1';
                $rules['rating_options.*'] = 'required|string|max:255';
            } elseif ($questionType === 'scale') {
                $rules['scale_options'] = 'required|array|min:1';
                $rules['scale_options.*'] = 'required|string|max:255';
            }

            $messages = [
                'question.required' => 'Pertanyaan wajib diisi.',
                'question_type.required' => 'Tipe pertanyaan wajib dipilih.',
                'question_type.in' => 'Tipe pertanyaan tidak valid.',
                'order.required' => 'Urutan pertanyaan wajib diisi.',
                'order.min' => 'Urutan pertanyaan minimal 1.',
                'options.required' => 'Pilihan jawaban wajib diisi untuk tipe pertanyaan pilihan.',
                'options.min' => 'Minimal harus ada satu pilihan jawaban.',
                'options.*.required' => 'Setiap pilihan jawaban harus diisi.',
                'options.*.string' => 'Pilihan jawaban harus berupa teks.',
                'rating_options.required' => 'Opsi rating diperlukan.',
                'scale_options.required' => 'Opsi skala diperlukan.',
            ];

            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for question update', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();
            
            $question = Tb_Questions::findOrFail($id_question);
            
            // Prepare update data
            $updateData = [
                'question' => $validated['question'],
                'type' => $validated['question_type'],
                'before_text' => $request->input('before_text', ''),
                'after_text' => $request->input('after_text', ''),
                'order' => $validated['order'],
            ];

            // Handle dependencies using hidden fields to ensure they're always submitted
            if ($request->has('has_dependency') && $request->input('has_dependency') == '1') {
                $updateData['depends_on'] = $request->input('hidden_depends_on') ?: $request->input('depends_on');
                $updateData['depends_value'] = $request->input('hidden_depends_value') ?: $request->input('depends_value');
            } else {
                $updateData['depends_on'] = null;
                $updateData['depends_value'] = null;
            }
            
            \Log::info('Updating question with data:', $updateData);
            
            $question->update($updateData);
            
            // Handle options for types that support them
            if (in_array($validated['question_type'], ['option', 'multiple', 'rating', 'scale'])) {
                // Delete existing options
                Tb_Question_Options::where('id_question', $id_question)->delete();
                
                $options = [];
                $otherOptions = $request->input('is_other_option', []);
                $otherBeforeTexts = $request->input('other_before_text', []);
                $otherAfterTexts = $request->input('other_after_text', []);
                
                // Get the right options array based on question type
                if ($validated['question_type'] === 'rating') {
                    $options = $request->input('rating_options', []);
                } elseif ($validated['question_type'] === 'scale') {
                    $options = $request->input('scale_options', []);
                } else {
                    $options = $request->input('options', []);
                }
                
                \Log::info('Processing updated options:', [
                    'question_type' => $validated['question_type'],
                    'options' => $options,
                    'other_options' => $otherOptions
                ]);
                
                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        $isOther = in_array($index, $otherOptions);
                        $otherBeforeText = isset($otherBeforeTexts[$index]) ? $otherBeforeTexts[$index] : '';
                        $otherAfterText = isset($otherAfterTexts[$index]) ? $otherAfterTexts[$index] : '';
                        
                        $optionData = [
                            'id_question' => $question->id_question,
                            'option' => trim($optionText),
                            'order' => $index + 1,
                            'is_other_option' => $isOther,
                            'other_before_text' => $otherBeforeText,
                            'other_after_text' => $otherAfterText,
                        ];
                        
                        \Log::info('Creating updated option:', $optionData);
                        
                        Tb_Question_Options::create($optionData);
                    }
                }
            } else {
                // For non-option types, delete any existing options
                Tb_Question_Options::where('id_question', $id_question)->delete();
            }
            
            DB::commit();
            
            \Log::info('Question updated successfully');
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating question:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question.
     */
    public function destroyQuestion($id_periode, $id_question)
    {
        try {
            DB::beginTransaction();
            
            $question = Tb_Questions::findOrFail($id_question);
            
            // Delete associated options first
            Tb_Question_Options::where('id_question', $id_question)->delete();
            
            // Delete the question
            $question->delete();
            
            DB::commit();
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting question: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus pertanyaan.');
        }
    }

    /**
     * Export questionnaire data to Excel.
     */
    public function export($id_periode)
    {
        $periode = Tb_Periode::with(['categories.questions.options'])->findOrFail($id_periode);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $sheet->setCellValue('A1', 'Periode Kuisioner');
        $sheet->setCellValue('B1', $periode->status);
        $sheet->setCellValue('A2', 'Tanggal Mulai');
        $sheet->setCellValue('B2', $periode->start_date);
        $sheet->setCellValue('A3', 'Tanggal Selesai');
        $sheet->setCellValue('B3', $periode->end_date);
        
        $row = 5;
        
        foreach ($periode->categories as $category) {
            $sheet->setCellValue('A' . $row, 'Kategori: ' . $category->category_name);
            $sheet->setCellValue('B' . $row, 'Tipe: ' . $category->type);
            $row += 2;
            
            foreach ($category->questions as $question) {
                $sheet->setCellValue('A' . $row, 'Pertanyaan: ' . $question->question);
                $sheet->setCellValue('B' . $row, 'Tipe: ' . $question->type);
                $row++;
                
                if ($question->type === 'option' && $question->options->isNotEmpty()) {
                    $sheet->setCellValue('A' . $row, 'Opsi Jawaban:');
                    $row++;
                    
                    foreach ($question->options as $option) {
                        $sheet->setCellValue('A' . $row, $option->order . '. ' . $option->option);
                        $row++;
                    }
                }
                
                $row++;
            }
            
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'kuisioner_' . $periode->id_periode . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * View questionnaire responses.
     */
    public function responses(Request $request, $id_periode)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        
        // Update status before displaying
        if ($periode->status !== $periode->calculateStatus()) {
            $periode->status = $periode->calculateStatus();
            $periode->save();
        }
        
        // Check if there are any categories and questions for this period
        $hasQuestions = Tb_Category::where('id_periode', $id_periode)
            ->whereHas('questions')
            ->exists();
        
        if (!$hasQuestions) {
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('error', 'Belum ada kategori atau pertanyaan untuk periode ini. Silakan tambahkan kategori dan pertanyaan terlebih dahulu.');
        }
        
        // Get user answers with pagination and filtering
        $query = DB::table('tb_user_answers')
            ->leftJoin('tb_user', 'tb_user_answers.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_alumni', 'tb_user.id_user', '=', 'tb_alumni.id_user')
            ->leftJoin('tb_company', 'tb_user.id_user', '=', 'tb_company.id_user')
            ->where('tb_user_answers.id_periode', $id_periode);
    
        // Apply user type filter if requested
        if ($request->has('filter')) {
            if ($request->filter == 'alumni') {
                $query->whereNotNull('tb_alumni.id_alumni');
            } elseif ($request->filter == 'company') {
                $query->whereNotNull('tb_company.id_company');
            }
        }
        
        $userAnswers = $query->select(
                'tb_user_answers.*',
                'tb_user.username',
                'tb_alumni.name as alumni_name',
                'tb_company.company_name'
            )
            ->orderBy('tb_user_answers.updated_at', 'desc')
            ->paginate(10);
    
        // Add a display name for each user answer
        foreach ($userAnswers as $answer) {
            if (!empty($answer->alumni_name)) {
                $answer->name = $answer->alumni_name;
                $answer->user_type = 'alumni';
            } elseif (!empty($answer->company_name)) {
                $answer->name = $answer->company_name;
                $answer->user_type = 'company';
            } else {
                $answer->name = $answer->username ?? ('User #' . $answer->id_user);
                $answer->user_type = 'unknown';
            }
        }
        
        return view('admin.questionnaire.responses', compact('periode', 'userAnswers'));
    }

    /**
     * View detailed response from a user.
     */
    public function responseDetail($id_periode, $id_user_answer)
    {
        try {
            // Find the user answer record
            $userAnswer = Tb_User_Answers::with(['user.alumni', 'user.company'])->findOrFail($id_user_answer);
            $periode = Tb_Periode::findOrFail($id_periode);

            // Determine user type
            $userType = null;
            if ($userAnswer->user->alumni) {
                $userType = 'alumni';
            } elseif ($userAnswer->user->company) {
                $userType = 'company';
            }

            // Get categories for this period - PERBAIKAN: Filter berdasarkan user type
            $categoriesQuery = Tb_Category::where('id_periode', $id_periode);
            
            // Filter categories based on user type
            if ($userType) {
                $categoriesQuery->where(function($query) use ($userType) {
                    $query->where('for_type', $userType)
                          ->orWhere('for_type', 'both');
                });
            }
            
            $categories = $categoriesQuery->orderBy('order')->get();

            $questionsWithAnswers = [];

            foreach ($categories as $category) {
                $questions = Tb_Questions::where('id_category', $category->id_category)
                    ->with('options')
                    ->orderBy('order')
                    ->get();

                $questionArray = [];

                foreach ($questions as $question) {
                    $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                        ->where('id_question', $question->id_question)
                        ->get();

                    // Initialize variables
                    $answer = null;
                    $otherAnswer = null;
                    $otherOption = null;
                    $multipleAnswers = [];
                    $multipleOtherAnswers = [];
                    $multipleOtherOptions = [];

                    if ($answerItems->isNotEmpty()) {
                        if ($question->type == 'text' || $question->type == 'date') {
                            // For text and date questions
                            $answer = $answerItems->first()->answer;
                            
                        } elseif ($question->type == 'location') {
                            // For location questions - handle JSON parsing
                            $locationAnswer = $answerItems->first()->answer;
                            if (!empty($locationAnswer)) {
                                $answer = $locationAnswer; // Store raw answer for compatibility
                                
                                // Try to parse JSON for display
                                try {
                                    $locationData = json_decode($locationAnswer, true);
                                    if (is_array($locationData)) {
                                        // Store structured location data for better display
                                        $answer = $locationData['display'] ?? $locationAnswer;
                                    }
                                } catch (\Exception $e) {
                                    // Keep original answer if JSON parsing fails
                                    $answer = $locationAnswer;
                                }
                            }
                            
                        } elseif ($question->type == 'rating' || $question->type == 'scale') {
                            // For rating and scale questions
                            $firstItem = $answerItems->first();
                            if ($firstItem->id_questions_options) {
                                $selectedOption = Tb_Question_Options::find($firstItem->id_questions_options);
                                if ($selectedOption) {
                                    $answer = $selectedOption->option;
                                }
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
                                        $otherOption = $selectedOption; // Include the full option object for text formatting
                                    }
                                }
                            }
                            
                        } elseif ($question->type == 'multiple') {
                            // For multiple choice questions
                            foreach ($answerItems as $item) {
                                if ($item->id_questions_options) {
                                    $selectedOption = Tb_Question_Options::find($item->id_questions_options);
                                    if ($selectedOption) {
                                        $multipleAnswers[] = $selectedOption->option;
                                        
                                        // Handle other answers for multiple choice
                                        if ($selectedOption->is_other_option == 1 && $item->other_answer) {
                                            $multipleOtherAnswers[] = $item->other_answer;
                                            $multipleOtherOptions[] = $selectedOption;
                                        } else {
                                            $multipleOtherAnswers[] = null;
                                            $multipleOtherOptions[] = null;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $questionArray[] = [
                        'question' => $question,
                        'answer' => $answer,
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

            return view('admin.questionnaire.response-detail', compact('userAnswer', 'periode', 'questionsWithAnswers', 'userType'));
        } catch (\Exception $e) {
            \Log::error('Error displaying response detail: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('admin.questionnaire.responses', $id_periode)
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get options for a specific question.
     */
    public function getQuestionOptions($id)
    {
        $question = Tb_Questions::findOrFail($id);
        
        if ($question->type === 'text') {
            return response()->json(['type' => 'text']);
        } else if ($question->type === 'option') {
            // Fixed: Use the correct model name
            $options = Tb_Question_Options::where('id_question', $question->id_question)->get();
            
            return response()->json([
                'type' => 'option',
                'options' => $options
            ]);
        }
        
        return response()->json(['error' => 'Unsupported question type'], 400);
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
