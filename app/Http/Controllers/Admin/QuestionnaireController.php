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
use Illuminate\Support\Facades\Log;
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
        if ($request->has('search') ) {
            $search = $request->get('search');
            $query->where('periode_name', 'like', '%' . $search . '%');
        }

        $periodes = $query->latest()->paginate(10);

        return view('admin.questionnaire.index', compact('periodes'));
    }

    /**
     * Show the form for creating a new questionnaire periode.
     */
    public function create()
    {
        // Mendapatkan daftar tahun kelulusan yang ada dengan jumlah alumni
        $graduationYearsWithCount = Tb_Periode::getAlumniStatisticsByYear();
        $graduationYears = $graduationYearsWithCount->keys()->toArray();
        
        // Mendapatkan opsi "tahun lalu"
        $yearsAgoOptions = Tb_Periode::getYearsAgoOptions();

        return view('admin.questionnaire.create', compact('graduationYears', 'graduationYearsWithCount', 'yearsAgoOptions'));
    }

    /**
     * Store a newly created questionnaire periode.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target_type' => 'required|in:all,specific_years,years_ago',
            'target_graduation_years' => 'nullable|array',
            'target_graduation_years.*' => 'string|regex:/^\d{4}$/',
            'years_ago_list' => 'nullable|array',
            'years_ago_list.*' => 'integer|min:1|max:20'
        ]);

        $data = $request->all();
        
        // Handle target alumni logic
        if ($request->target_type === 'all') {
            $data['all_alumni'] = true;
            $data['target_graduation_years'] = null;
            $data['years_ago_list'] = null;
            $data['target_description'] = 'Semua Alumni';
            
        } elseif ($request->target_type === 'specific_years') {
            $data['all_alumni'] = false;
            $data['years_ago_list'] = null;
            
            if (!empty($request->target_graduation_years)) {
                $sortedYears = collect($request->target_graduation_years)
                    ->sort()
                    ->reverse()
                    ->values()
                    ->toArray();
                
                $data['target_graduation_years'] = $sortedYears;
                
                $alumniCount = Tb_Alumni::whereIn('graduation_year', $sortedYears)->count();
                $yearsString = implode(', ', $sortedYears);
                $data['target_description'] = "Alumni Lulusan Tahun: {$yearsString} ({$alumniCount} alumni)";
            } else {
                return back()->withErrors(['target_graduation_years' => 'Pilih minimal satu tahun kelulusan.']);
            }
            
        } elseif ($request->target_type === 'years_ago') {
            $data['all_alumni'] = false;
            $data['target_graduation_years'] = null;
            
            if (!empty($request->years_ago_list)) {
                $currentYear = now()->year;
                $sortedYearsAgo = collect($request->years_ago_list)->sort()->values()->toArray();
                $data['years_ago_list'] = $sortedYearsAgo;
                
                // Calculate target years and count alumni
                $targetYears = collect($sortedYearsAgo)->map(function($yearsAgo) use ($currentYear) {
                    return (string)($currentYear - $yearsAgo);
                });
                
                $alumniCount = Tb_Alumni::whereIn('graduation_year', $targetYears)->count();
                
                $descriptions = collect($sortedYearsAgo)->map(function($yearsAgo) use ($currentYear) {
                    $year = $currentYear - $yearsAgo;
                    return "{$yearsAgo} tahun lalu ({$year})";
                })->toArray();
                
                $data['target_description'] = "Alumni Lulusan: " . implode(', ', $descriptions) . " ({$alumniCount} alumni)";
            } else {
                return back()->withErrors(['years_ago_list' => 'Pilih minimal satu periode tahun lalu.']);
            }
        }

        // Remove status from data as it will be calculated automatically
        unset($data['status']);

        Tb_Periode::create($data);

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Periode Questionnaire berhasil dibuat.');
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
    public function edit($id)
    {
        $periode = Tb_Periode::findOrFail($id);
        
        // Mendapatkan daftar tahun kelulusan yang ada dengan jumlah alumni
        $graduationYearsWithCount = Tb_Periode::getAlumniStatisticsByYear();
        $graduationYears = $graduationYearsWithCount->keys()->toArray();
        
        // Mendapatkan opsi "tahun lalu"
        $yearsAgoOptions = Tb_Periode::getYearsAgoOptions();

        return view('admin.questionnaire.edit', compact('periode', 'graduationYears', 'graduationYearsWithCount', 'yearsAgoOptions'));
    }

    /**
     * Update the specified questionnaire periode.
     */
    public function update(Request $request, $id)
    {
        $periode = Tb_Periode::findOrFail($id);
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target_type' => 'required|in:all,specific_years,years_ago',
            'target_graduation_years' => 'nullable|array',
            'target_graduation_years.*' => 'string|regex:/^\d{4}$/',
            'years_ago_list' => 'nullable|array',
            'years_ago_list.*' => 'integer|min:1|max:20'
        ]);

        $data = $request->all();
        
        // Handle target alumni logic (same as store method)
        if ($request->target_type === 'all') {
            $data['all_alumni'] = true;
            $data['target_graduation_years'] = null;
            $data['years_ago_list'] = null;
            $data['target_description'] = 'Semua Alumni';
            
        } elseif ($request->target_type === 'specific_years') {
            $data['all_alumni'] = false;
            $data['years_ago_list'] = null;
            
            if (!empty($request->target_graduation_years)) {
                $sortedYears = collect($request->target_graduation_years)
                    ->sort()
                    ->reverse()
                    ->values()
                    ->toArray();
                
                $data['target_graduation_years'] = $sortedYears;
                
                $alumniCount = Tb_Alumni::whereIn('graduation_year', $sortedYears)->count();
                $yearsString = implode(', ', $sortedYears);
                $data['target_description'] = "Alumni Lulusan Tahun: {$yearsString} ({$alumniCount} alumni)";
            } else {
                return back()->withErrors(['target_graduation_years' => 'Pilih minimal satu tahun kelulusan.']);
            }
            
        } elseif ($request->target_type === 'years_ago') {
            $data['all_alumni'] = false;
            $data['target_graduation_years'] = null;
            
            if (!empty($request->years_ago_list)) {
                $currentYear = now()->year;
                $sortedYearsAgo = collect($request->years_ago_list)->sort()->values()->toArray();
                $data['years_ago_list'] = $sortedYearsAgo;
                
                $targetYears = collect($sortedYearsAgo)->map(function($yearsAgo) use ($currentYear) {
                    return (string)($currentYear - $yearsAgo);
                });
                
                $alumniCount = Tb_Alumni::whereIn('graduation_year', $targetYears)->count();
                
                $descriptions = collect($sortedYearsAgo)->map(function($yearsAgo) use ($currentYear) {
                    $year = $currentYear - $yearsAgo;
                    return "{$yearsAgo} tahun lalu ({$year})";
                })->toArray();
                
                $data['target_description'] = "Alumni Lulusan: " . implode(', ', $descriptions) . " ({$alumniCount} alumni)";
            } else {
                return back()->withErrors(['years_ago_list' => 'Pilih minimal satu periode tahun lalu.']);
            }
        }

        // Remove status from data as it will be calculated automatically
        unset($data['status']);

        $periode->update($data);

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Periode Questionnaire berhasil diperbarui.');
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
                ->with('success', 'Periode kuesioner berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.questionnaire.index')
                ->with('error', 'Terjadi kesalahan saat menghapus periode kuesioner.');
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
            'order' => 'required|integer|min:1',
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
            'order' => 'required|integer|min:1',
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
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('error', 'Terjadi kesalahan saat menghapus kategori.');
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
        try {
            $questionType = $request->input('question_type');
            
            // Define validation rules
            $rules = [
                'question' => 'required|string|max:1000',
                'question_type' => 'required|in:text,option,multiple,rating,scale,date,location',
                'order' => 'required|integer|min:1',
                // ✅ PERBAIKAN: Tambahkan validation untuk before/after text question
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
                'option_indexes' => 'nullable|array',
                'option_indexes.*' => 'nullable|integer',
                'rating_options' => 'nullable|array',
                'rating_options.*' => 'nullable|string|max:255',
                'scale_options' => 'nullable|array',
                'scale_options.*' => 'nullable|string|max:255',
                'scale_min_label' => 'nullable|string|max:255',
                'scale_max_label' => 'nullable|string|max:255'
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
                'options.*.required' => 'Setiap pilihan jawaban wajib diisi.',
                'rating_options.required' => 'Pilihan rating wajib diisi.',
                'scale_options.required' => 'Pilihan skala wajib diisi.',
                'before_text.max' => 'Teks sebelum input maksimal 255 karakter.',
                'after_text.max' => 'Teks setelah input maksimal 255 karakter.',
            ];

            $validated = $request->validate($rules, $messages);
            
            \Log::info('Create Question - Validated data', [
                'question_type' => $questionType,
                'question' => $validated['question'],
                'before_text' => $validated['before_text'] ?? null,
                'after_text' => $validated['after_text'] ?? null,
                'options' => $validated['options'] ?? null,
                'other_options' => $validated['other_options'] ?? null,
                'other_before_text' => $validated['other_before_text'] ?? null,
                'other_after_text' => $validated['other_after_text'] ?? null,
                'has_dependency' => $validated['has_dependency'] ?? null,
                'depends_on' => $validated['depends_on'] ?? null,
                'depends_value' => $validated['depends_value'] ?? null,
                'hidden_depends_on' => $validated['hidden_depends_on'] ?? null,
                'hidden_depends_value' => $validated['hidden_depends_value'] ?? null
            ]);

            // Handle dependencies properly
            $dependsOn = null;
            $dependsValue = null;
            
            if ($request->has('has_dependency') && $request->input('has_dependency') == '1') {
                $dependsOn = $request->input('hidden_depends_on') ?: $request->input('depends_on');
                $dependsValue = $request->input('hidden_depends_value') ?: $request->input('depends_value');
            }

            // ✅ PERBAIKAN: Create the question dengan before/after text
            $question = Tb_Questions::create([
                'id_category' => $id_category,
                'question' => $validated['question'],
                'type' => $validated['question_type'],
                'order' => $validated['order'],
                // ✅ PERBAIKAN: Simpan before/after text untuk question input
                'before_text' => $validated['before_text'] ?? null,
                'after_text' => $validated['after_text'] ?? null,
                'depends_on' => $dependsOn,
                'depends_value' => $dependsValue,
                'scale_min_label' => $validated['scale_min_label'] ?? null,
                'scale_max_label' => $validated['scale_max_label'] ?? null
            ]);

            \Log::info('Question created successfully', [
                'question_id' => $question->id_question,
                'question_text' => $question->question,
                'before_text' => $question->before_text,
                'after_text' => $question->after_text,
                'type' => $question->type
            ]);

            // Handle options for option/multiple types
            if (($questionType === 'option' || $questionType === 'multiple') && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        $isOtherOption = in_array($index, $validated['other_options'] ?? []);
                        
                        // Get before/after text untuk other option
                        $otherBeforeText = null;
                        $otherAfterText = null;
                        
                        if ($isOtherOption) {
                            $otherBeforeText = isset($validated['other_before_text'][$index]) ? 
                                trim($validated['other_before_text'][$index]) : null;
                            $otherAfterText = isset($validated['other_after_text'][$index]) ? 
                                trim($validated['other_after_text'][$index]) : null;
                        }
                        
                        \Log::info('Creating option with before/after text', [
                            'option_index' => $index,
                            'option_text' => trim($optionText),
                            'is_other_option' => $isOtherOption,
                            'other_before_text' => $otherBeforeText,
                            'other_after_text' => $otherAfterText
                        ]);
                        
                        Tb_Question_Options::create([
                            'id_question' => $question->id_question,
                            'option' => trim($optionText),
                            'order' => $index + 1,
                            'is_other_option' => $isOtherOption ? 1 : 0,
                            'other_before_text' => $otherBeforeText,
                            'other_after_text' => $otherAfterText
                        ]);
                    }
                }
            }

            // Handle rating options
            if ($questionType === 'rating' && isset($validated['rating_options'])) {
                foreach ($validated['rating_options'] as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        Tb_Question_Options::create([
                            'id_question' => $question->id_question,
                            'option' => trim($optionText),
                            'order' => $index + 1,
                            'is_other_option' => 0
                        ]);
                    }
                }
            }

            // Handle scale options
            if ($questionType === 'scale' && isset($validated['scale_options'])) {
                foreach ($validated['scale_options'] as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        Tb_Question_Options::create([
                            'id_question' => $question->id_question,
                            'option' => trim($optionText),
                            'order' => $index + 1,
                            'is_other_option' => 0
                        ]);
                    }
                }
            }

            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating question: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        
        // Get available questions for dependency
        $availableQuestions = Tb_Questions::with('options')
            ->where('id_category', $id_category)
            ->where('id_question', '!=', $id_question)
            ->whereIn('type', ['option', 'multiple', 'rating', 'scale'])
            ->get();
        
        return view('admin.questionnaire.question.edit', compact(
            'periode', 'category', 'question', 'availableQuestions'
        ));
    }

    /**
     * Update the specified question.
     */
    public function updateQuestion(Request $request, $id_periode, $id_category, $id_question)
    {
        Log::info('=== Question Update Request Debug ===', [
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
                'options.*.required' => 'Setiap pilihan jawaban wajib diisi.',
                'rating_options.required' => 'Opsi rating diperlukan.',
                'scale_options.required' => 'Opsi skala diperlukan.',
            ];

            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for question update', [
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
            
            Log::info('Updating question with data:', $updateData);
            
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
                
                Log::info('Processing updated options:', [
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
                        
                        Log::info('Creating updated option:', $optionData);
                        
                        Tb_Question_Options::create($optionData);
                    }
                }
            } else {
                // For non-option types, delete any existing options
                Tb_Question_Options::where('id_question', $id_question)->delete();
            }
            
            DB::commit();
            
            Log::info('Question updated successfully');
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating question:', [
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
            $question = Tb_Questions::findOrFail($id_question);
            $question->delete();
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Pertanyaan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('error', 'Terjadi kesalahan saat menghapus pertanyaan.');
        }
    }

    /**
     * Toggle question status (visible/hidden)
     */
    public function toggleQuestionStatus($id_periode, $id_question)
    {
        try {
            $question = Tb_Questions::findOrFail($id_question);
            
            // Toggle status
            $newStatus = $question->status === 'visible' ? 'hidden' : 'visible';
            $question->status = $newStatus;
            $question->save();

            $statusText = $newStatus === 'visible' ? 'ditampilkan' : 'disembunyikan';
            
            return redirect()->back()->with('success', "Pertanyaan \"{$question->question}\" berhasil {$statusText}.");
            
        } catch (\Exception $e) {
            Log::error('Error toggling question status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status pertanyaan.');
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
        $sheet->setCellValue('B1', $periode->periode_name);
        $sheet->setCellValue('A2', 'Status');
        $sheet->setCellValue('B2', $periode->status);
        $sheet->setCellValue('A3', 'Tanggal Mulai');
        $sheet->setCellValue('B3', $periode->start_date);
        $sheet->setCellValue('A4', 'Tanggal Selesai');
        $sheet->setCellValue('B4', $periode->end_date);
        
        $row = 6;
        
        foreach ($periode->categories as $category) {
            $sheet->setCellValue('A' . $row, 'Kategori: ' . $category->category_name);
            $row++;
            
            foreach ($category->questions as $question) {
                $sheet->setCellValue('A' . $row, $question->order . '. ' . $question->question);
                $sheet->setCellValue('B' . $row, 'Type: ' . $question->type);
                $row++;
                
                if ($question->options->count() > 0) {
                    foreach ($question->options as $option) {
                        $sheet->setCellValue('B' . $row, '- ' . $option->option);
                        $row++;
                    }
                }
                $row++;
            }
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
                ->with('error', 'Belum ada pertanyaan untuk periode ini.');
        }
        
        // ✅ PERBAIKAN: Get user answers with proper user type detection
        $query = Tb_User_Answers::where('id_periode', $id_periode)
            ->with(['user.alumni', 'user.company']);

        // ✅ PERBAIKAN: Apply user type filter yang lebih akurat
        if ($request->has('filter') && $request->get('filter') !== '') {
            $filter = $request->get('filter');
            if ($filter === 'alumni') {
                // Filter untuk alumni - yang punya relasi alumni
                $query->whereHas('user.alumni');
            } elseif ($filter === 'company') {
                // Filter untuk company - yang punya relasi company
                $query->whereHas('user.company');
            }
        }
        
        $userAnswers = $query->orderBy('created_at', 'desc')->paginate(20);

        // ✅ PERBAIKAN: Add proper display name and user type for each user answer
        foreach ($userAnswers as $answer) {
            $user = $answer->user;
            $alumni = $user ? $user->alumni : null;
            $company = $user ? $user->company : null;
            
            if ($alumni) {
                $answer->display_name = $alumni->name ?? $alumni->full_name ?? $user->name;
                $answer->user_type_text = 'Alumni';
                $answer->user_type = 'alumni';
                $answer->additional_info = $alumni->nim ?? '';
            } elseif ($company) {
                $answer->display_name = $company->company_name ?? $user->name;
                $answer->user_type_text = 'Perusahaan';
                $answer->user_type = 'company';
                $answer->additional_info = $company->email ?? '';
            } else {
                // Fallback
                $answer->display_name = $user ? $user->name : 'Unknown User';
                $answer->user_type_text = 'Unknown';
                $answer->user_type = 'unknown';
                $answer->additional_info = '';
            }
            
            // Format submit date
            if ($answer->status == 'completed' && $answer->submitted_at) {
                $answer->formatted_submitted_at = \Carbon\Carbon::parse($answer->submitted_at)->format('d M Y, H:i');
            }
        }
        
        // ✅ PERBAIKAN: Get category statistics for this period
        $categoryStats = [
            'alumni_categories' => Tb_Category::where('id_periode', $id_periode)
                ->whereIn('for_type', ['alumni', 'both'])
                ->count(),
            'company_categories' => Tb_Category::where('id_periode', $id_periode)
                ->whereIn('for_type', ['company', 'both'])
                ->count(),
            'total_categories' => Tb_Category::where('id_periode', $id_periode)->count()
        ];
        
        return view('admin.questionnaire.responses', compact('periode', 'userAnswers', 'categoryStats'));
    }

    /**
     * View detailed response from a user.
     */
    public function responseDetail($id_periode, $id_user_answer)
    {
        // ✅ PERBAIKAN: Get periode first
        $periode = Tb_Periode::findOrFail($id_periode);
        
        // Get the user answer with relationships
        $userAnswer = Tb_User_Answers::where('id_user_answer', $id_user_answer)
            ->where('id_periode', $id_periode)
            ->with(['user', 'periode'])
            ->firstOrFail();
        
        // ✅ PERBAIKAN: Determine user type and get appropriate data
        $userData = null;
        $userType = null;
        
        // Check if user is alumni or company based on relationships
        $alumni = $userAnswer->user ? $userAnswer->user->alumni : null;
        $company = $userAnswer->user ? $userAnswer->user->company : null;
        
        if ($alumni) {
            $userType = 'alumni';
            $userData = $alumni;
        } elseif ($company) {
            $userType = 'company';
            $userData = $company;
        } else {
            // Fallback: check user role if relationships don't exist
            if ($userAnswer->user && $userAnswer->user->role === 'alumni') {
                $userType = 'alumni';
            } elseif ($userAnswer->user && $userAnswer->user->role === 'company') {
                $userType = 'company';
            }
        }
        
        \Log::info('Response Detail - User Type Detection', [
            'user_answer_id' => $id_user_answer,
            'user_id' => $userAnswer->user->id_user ?? 'unknown',
            'user_role' => $userAnswer->user->role ?? 'unknown',
            'has_alumni_relation' => $alumni ? true : false,
            'has_company_relation' => $company ? true : false,
            'determined_user_type' => $userType,
            'alumni_name' => $alumni->name ?? null,
            'company_name' => $company->company_name ?? null
        ]);
        
        // ✅ PERBAIKAN: Get categories for this period - filter based on user type
        $categories = Tb_Category::where('id_periode', $id_periode)
            ->where(function($query) use ($userType) {
                if ($userType === 'alumni') {
                    // Alumni can only see alumni and both categories
                    $query->where('for_type', 'alumni')
                          ->orWhere('for_type', 'both');
                } elseif ($userType === 'company') {
                    // Company can only see company and both categories
                    $query->where('for_type', 'company')
                          ->orWhere('for_type', 'both');
                } else {
                    // If user type is unknown, don't show any categories
                    // This prevents data leakage
                    $query->where('for_type', 'unknown_type_that_does_not_exist');
                }
            })
            ->orderBy('order')
            ->get();
        
        \Log::info('Response Detail - Categories Filter', [
            'user_type' => $userType,
            'total_categories_for_period' => Tb_Category::where('id_periode', $id_periode)->count(),
            'filtered_categories_count' => $categories->count(),
            'filtered_categories' => $categories->pluck('category_name', 'for_type')->toArray()
        ]);
        
        // ✅ PERBAIKAN: Prepare questions with answers data structure (SAMA UNTUK ALUMNI DAN COMPANY)
        $questionsWithAnswers = [];
        
        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id_category)
                ->visible()
                ->with(['options' => function($query) {
                    $query->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
            
            $questionArray = [];
            
            foreach ($questions as $question) {
                // Get answer items for this question
                $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                    ->where('id_question', $question->id_question)
                    ->get();
                
                // ✅ PERBAIKAN: Initialize variables consistently
                $answer = null;
                $otherAnswer = null;
                $otherOption = null;
                $multipleAnswers = [];
                $multipleOtherAnswers = [];
                $multipleOtherOptions = [];
                $hasAnswer = false;
                
                if ($answerItems->isNotEmpty()) {
                    $hasAnswer = true;
                    
                    if ($question->type == 'text' || $question->type == 'date') {
                        // For text and date questions
                        $answer = $answerItems->first()->answer;
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
                        if (empty(trim($answer))) {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'option') {
                        // ✅ PERBAIKAN: For single option questions - SAMA UNTUK ALUMNI DAN COMPANY
                        $firstItem = $answerItems->first();
                        if ($firstItem->id_questions_options) {
                            // ✅ Use the same option lookup method for both alumni and company
                            $selectedOption = $question->options->where('id_questions_options', $firstItem->id_questions_options)->first();
                            if ($selectedOption) {
                                $answer = $selectedOption->option;
                                $otherOption = null;
                                
                                // Handle other answer
                                if ($selectedOption->is_other_option == 1 && !empty($firstItem->other_answer)) {
                                    $otherAnswer = $firstItem->other_answer;
                                    $otherOption = $selectedOption;
                                }
                            } else {
                                // ✅ DEBUGGING: Log missing option
                                \Log::warning('Missing option for answer item', [
                                    'user_answer_id' => $userAnswer->id_user_answer,
                                    'question_id' => $question->id_question,
                                    'option_id' => $firstItem->id_questions_options,
                                    'user_type' => $userType,
                                    'available_options' => $question->options->pluck('id_questions_options')->toArray()
                                ]);
                                $hasAnswer = false;
                            }
                        } else {
                            $hasAnswer = false;
                        }
                        
                    } elseif ($question->type == 'multiple') {
                        // ✅ PERBAIKAN: For multiple choice questions - SAMA UNTUK ALUMNI DAN COMPANY
                        $hasValidAnswers = false;
                        foreach ($answerItems as $item) {
                            if ($item->id_questions_options) {
                                // ✅ Use the same option lookup method for both alumni and company
                                $selectedOption = $question->options->where('id_questions_options', $item->id_questions_options)->first();
                                if ($selectedOption) {
                                    $hasValidAnswers = true;
                                    $displayText = $selectedOption->option;
                                    
                                    // Handle other answers for multiple choice
                                    if ($selectedOption->is_other_option == 1 && !empty($item->other_answer)) {
                                        $multipleOtherAnswers[$selectedOption->id_questions_options] = $item->other_answer;
                                        $multipleOtherOptions[] = $selectedOption;
                                    } else {
                                        $multipleOtherAnswers[$selectedOption->id_questions_options] = null;
                                        $multipleOtherOptions[] = null;
                                    }
                                    
                                    $multipleAnswers[] = $displayText;
                                } else {
                                    // ✅ DEBUGGING: Log missing option
                                    \Log::warning('Missing option for multiple answer item', [
                                        'user_answer_id' => $userAnswer->id_user_answer,
                                        'question_id' => $question->id_question,
                                        'option_id' => $item->id_questions_options,
                                        'user_type' => $userType,
                                        'available_options' => $question->options->pluck('id_questions_options')->toArray()
                                    ]);
                                }
                            }
                        }
                        $hasAnswer = $hasValidAnswers;
                        
                    } elseif ($question->type == 'rating' || $question->type == 'scale') {
                        // ✅ PERBAIKAN: For rating and scale questions - SAMA UNTUK ALUMNI DAN COMPANY
                        $firstItem = $answerItems->first();
                        if ($firstItem->id_questions_options) {
                            $selectedOption = $question->options->where('id_questions_options', $firstItem->id_questions_options)->first();
                            if ($selectedOption) {
                                $answer = $selectedOption->option;
                            } else {
                                // ✅ DEBUGGING: Log missing option
                                \Log::warning('Missing option for rating/scale answer item', [
                                    'user_answer_id' => $userAnswer->id_user_answer,
                                    'question_id' => $question->id_question,
                                    'option_id' => $firstItem->id_questions_options,
                                    'user_type' => $userType,
                                    'available_options' => $question->options->pluck('id_questions_options')->toArray()
                                ]);
                                $hasAnswer = false;
                            }
                        } else {
                            $hasAnswer = false;
                        }
                    }
                } else {
                    // No answer items found
                    $hasAnswer = false;
                }
                
                // ✅ DEBUGGING: Log question processing
                if (config('app.debug')) {
                    \Log::info('Question processed', [
                        'question_id' => $question->id_question,
                        'question_type' => $question->type,
                        'user_type' => $userType,
                        'has_answer' => $hasAnswer,
                        'answer' => $answer,
                        'multiple_answers_count' => count($multipleAnswers),
                        'answer_items_count' => $answerItems->count()
                    ]);
                }
                
                $questionArray[] = [
                    'question' => $question,
                    'answer' => $answer,
                    'hasAnswer' => $hasAnswer,
                    'otherAnswer' => $otherAnswer ?? null,
                    'otherOption' => $otherOption ?? null,
                    'multipleAnswers' => $multipleAnswers,
                    'multipleOtherAnswers' => $multipleOtherAnswers,
                    'multipleOtherOptions' => $multipleOtherOptions,
                ];
            }
            
            if (!empty($questionArray)) {
                $questionsWithAnswers[] = [
                    'category' => $category,
                    'questions' => $questionArray
                ];
            }
        }
        
        // ✅ DEBUGGING: Log final data structure
        if (config('app.debug')) {
            \Log::info('Response detail data prepared', [
                'user_answer_id' => $userAnswer->id_user_answer,
                'user_type' => $userType,
                'categories_count' => count($questionsWithAnswers),
                'total_questions' => collect($questionsWithAnswers)->sum(function($cat) {
                    return count($cat['questions']);
                }),
                'categories_shown' => collect($questionsWithAnswers)->pluck('category.category_name')->toArray()
            ]);
        }
        
        // ✅ PERBAIKAN: Pass semua variabel yang diperlukan ke view
        return view('admin.questionnaire.response-detail', compact(
            'periode',              
            'userAnswer',
            'userData',
            'userType',
            'questionsWithAnswers'
        ));
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
            $options = $question->options->pluck('option', 'id_questions_options');
            return response()->json(['type' => 'option', 'options' => $options]);
        }
        
        return response()->json(['type' => 'unknown']);
    }

    /**
     * Get provinces from external API
     */
    public function getProvinces()
    {
        try {
            $response = file_get_contents('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            $provinces = json_decode($response, true);
            
            if ($provinces) {
                return response()->json([
                    'success' => true,
                    'data' => $provinces
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data provinsi'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            
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
            Log::error('Error fetching cities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kota/kabupaten'
            ], 500);
        }
    }
}
