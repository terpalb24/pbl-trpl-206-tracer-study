<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionnaireImportExportController extends Controller
{
    public function index()
    {
        // Ambil semua periode untuk dropdown
        $periodes = \App\Models\Tb_Periode::orderBy('start_date', 'desc')->get();
        return view('admin.questionnaire.import-export', compact('periodes'));
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // Ambil daftar kategori unik dari database (atau gunakan contoh jika kosong)
        $categoryNames = Tb_Category::distinct()->pluck('category_name')->toArray();
        if (empty($categoryNames)) {
            $categoryNames = ['Pengalaman Akademik', 'Kategori Bekerja', 'Kategori Tidak Bekerja'];
        }

        // Buat satu sheet per kategori
        foreach ($categoryNames as $i => $catName) {
            $sheet = $i === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle(substr($catName, 0, 31)); // Judul sheet max 31 karakter

            // Header
            $sheet->setCellValue('A1', 'Category Name');
            $sheet->setCellValue('B1', 'Category Order');
            $sheet->setCellValue('C1', 'For Type (alumni/company/both)');
            $sheet->setCellValue('D1', 'Is Status Dependent (TRUE/FALSE)');
            $sheet->setCellValue('E1', 'Required Alumni Status (pisahkan koma)');
            $sheet->setCellValue('F1', 'Question Text');
            $sheet->setCellValue('G1', 'Question Type (text/option/multiple/rating/scale/date/location/numeric/email)');
            $sheet->setCellValue('H1', 'Question Order');
            $sheet->setCellValue('I1', 'Before Text');
            $sheet->setCellValue('J1', 'After Text');
            $sheet->setCellValue('K1', 'Scale Min Label');
            $sheet->setCellValue('L1', 'Scale Max Label');
            $sheet->setCellValue('M1', 'Options (separate with |)');
            $sheet->setCellValue('N1', 'Other Option Indexes (comma separated, 0-based)');
            $sheet->setCellValue('O1', 'Other Before Texts (| separated, match option index)');
            $sheet->setCellValue('P1', 'Other After Texts (| separated, match option index)');
            $sheet->setCellValue('Q1', 'Depends On Question (Question Text)');
            $sheet->setCellValue('R1', 'Depends On Value');

            // Contoh data baris 2
            $sheet->fromArray([
                $catName, 1, 'alumni', 'FALSE', '', 'Contoh pertanyaan untuk ' . $catName, 'option', 1, '', '', '', '', 'Sangat Baik|Baik|Cukup|Kurang|Sangat Kurang', '4', '| | | |Sebutkan:', '| | | |', '', ''
            ], null, 'A2');

            // Auto-size columns
            foreach(range('A', 'R') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Set sheet aktif ke pertama
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'questionnaire_template_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function createHeaderMap($headers)
    {
        // Filter out null/empty values and sanitize headers
        $sanitizedHeaders = array_map(function($header) {
            return trim(strtolower(str_replace(' ', '_', (string)$header)));
        }, array_filter($headers, function($value) {
            return !is_null($value) && $value !== '';
        }));
        
        return array_flip($sanitizedHeaders);
    }

    public function import(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'periode_id' => 'required|exists:tb_periode,id_periode',
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $validForTypes = ['alumni', 'company', 'both'];
            $processedCategories = [];
            $categoryMap = [];

            // First pass - process categories
            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                $category = trim($sheetName);
                
                // Read category data from row 2
                $categoryData = $worksheet->rangeToArray('A2:D2')[0];
                $categoryOrder = (int)($categoryData[0] ?? 1);
                $forType = strtolower(trim($categoryData[1] ?? 'both'));
                $isStatusDependent = strtolower(trim($categoryData[2] ?? 'false')) === 'true';
                $requiredAlumniStatus = $categoryData[3] ? json_decode($categoryData[3], true) : null;
                
                if (!in_array($forType, $validForTypes)) {
                    throw new \Exception("Invalid for_type '$forType' in sheet '$category'. Must be one of: " . implode(', ', $validForTypes));
                }

                // Find or create category
                $categoryModel = Tb_Category::where('category_name', 'LIKE', "%$category%")
                                      ->where('id_periode', $request->periode_id)
                                      ->first();

                if (!$categoryModel) {
                    $categoryModel = Tb_Category::create([
                        'id_periode' => $request->periode_id,
                        'category_name' => $category,
                        'order' => $categoryOrder,
                        'for_type' => $forType,
                        'is_status_dependent' => $isStatusDependent,
                        'required_alumni_status' => $requiredAlumniStatus
                    ]);
                } else {
                    $categoryModel->update([
                        'order' => $categoryOrder,
                        'for_type' => $forType,
                        'is_status_dependent' => $isStatusDependent,
                        'required_alumni_status' => $requiredAlumniStatus
                    ]);
                }

                $categoryMap[$category] = $categoryModel->id_category;
                $processedCategories[] = $category;
            }

            // Second pass - process questions
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $category = trim($sheet->getTitle());
                $categoryId = $categoryMap[$category];
                
                // Get headers
                $headers = $sheet->rangeToArray('A1:Q1')[0];
                $h = $this->createHeaderMap($headers);

                // Start from row 3 (after category data)
                for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
                    $rowData = $sheet->rangeToArray('A'.$row.':'.$sheet->getHighestColumn().$row)[0];
                    
                    if (empty(array_filter($rowData))) continue;

                    // Map question data according to export structure
                    $questionData = [
                        'id_category' => $categoryId,
                        'question' => trim($rowData[4] ?? ''), // question text
                        'type' => trim($rowData[5] ?? ''), // question type
                        'order' => (int)($rowData[6] ?? 0), // question order
                        'before_text' => trim($rowData[7] ?? ''), // before text
                        'after_text' => trim($rowData[8] ?? ''), // after text
                        'scale_min_label' => trim($rowData[9] ?? ''), // scale min label
                        'scale_max_label' => trim($rowData[10] ?? ''), // scale max label
                        'status' => 'visible'
                    ];

                    // Add logging for debugging
                    // Log::info("Importing question", [
                    //     'question_text' => $questionData['question'],
                    //     'type' => $questionData['type'],
                    //     'before_text' => $questionData['before_text'],
                    //     'after_text' => $questionData['after_text']
                    // ]);

                    // Create question with proper before/after text
                    $question = Tb_Questions::create($questionData);

                    // Process options if needed
                    if (in_array($question->type, ['option', 'multiple', 'rating', 'scale'])) {
                        $options = $rowData[11] ? array_filter(explode('|', $rowData[11])) : []; // options
                        $otherIndexes = $rowData[12] ? 
                            array_map(function($idx) { 
                                return (int)$idx; 
                            }, explode(',', $rowData[12])) : [];
                        $otherBeforeTexts = $rowData[13] ? array_filter(explode('|', $rowData[13])) : []; // other before texts
                        $otherAfterTexts = $rowData[14] ? array_filter(explode('|', $rowData[14])) : []; // other after texts

                        // Log::info('Processing options', [
                        //     'options' => $options,
                        //     'otherIndexes' => $otherIndexes,
                        //     'otherBeforeTexts' => $otherBeforeTexts,
                        //     'otherAfterTexts' => $otherAfterTexts
                        // ]);

                        $optionIndex = 1;
                        foreach ($options as $idx => $optionText) {
                            if (trim($optionText) === '') continue;
                            
                            $isOther = in_array($optionIndex, $otherIndexes);
                            $beforeText = null;
                            $afterText = null;

                            if ($isOther) {
                                $otherIdx = array_search($optionIndex, $otherIndexes);
                                $beforeText = $otherBeforeTexts[$otherIdx] ?? '';
                                $afterText = $otherAfterTexts[$otherIdx] ?? '';
                            }

                            Tb_Question_Options::create([
                                'id_question' => $question->id_question,
                                'option' => trim($optionText),
                                'order' => $optionIndex,
                                'is_other_option' => $isOther,
                                'other_before_text' => $beforeText,
                                'other_after_text' => $afterText
                            ]);

                            // Log::info('Created option', [
                            //     'option' => $optionText,
                            //     'order' => $optionIndex,
                            //     'is_other' => $isOther,
                            //     'before_text' => $beforeText,
                            //     'after_text' => $afterText
                            // ]);
                            
                            $optionIndex++;
                        }
                    }

                    // Handle dependencies
                    $dependsOnText = $rowData[15] ?? ''; // depends on question text
                    $dependsOnValue = $rowData[16] ?? ''; // depends on value

                    if ($dependsOnText) {
                        $parentQuestion = Tb_Questions::where('question', 'LIKE', "%$dependsOnText%")
                            ->where('id_category', $categoryId)
                            ->first();

                        if ($parentQuestion) {
                            // For option-based questions, map the text value to option ID
                            if (in_array($parentQuestion->type, ['option', 'multiple', 'rating', 'scale'])) {
                                // Get all options for parent question
                                $parentOptions = Tb_Question_Options::where('id_question', $parentQuestion->id_question)
                                    ->get();
                                
                                // Find matching option by text
                                $matchingOption = $parentOptions->first(function($option) use ($dependsOnValue) {
                                    return trim(strtolower($option->option)) === trim(strtolower($dependsOnValue));
                                });

                                if ($matchingOption) {
                                    $dependsValueId = $matchingOption->id_questions_options;
                                } else {
                                    Log::warning("Could not find matching option for depends_value", [
                                        'question_id' => $question->id_question,
                                        'parent_question_id' => $parentQuestion->id_question,
                                        'depends_value_text' => $dependsOnValue,
                                        'available_options' => $parentOptions->pluck('option')->toArray()
                                    ]);
                                    // Set to null or handle error as needed
                                    $dependsValueId = null;
                                }
                            } else {
                                // For non-option questions, use the value as-is
                                $dependsValueId = $dependsOnValue;
                            }

                            $question->update([
                                'depends_on' => $parentQuestion->id_question,
                                'depends_value' => $dependsValueId
                            ]);

                            // Log::info("Updated question dependency", [
                            //     'question_id' => $question->id_question,
                            //     'depends_on' => $parentQuestion->id_question,
                            //     'depends_value_text' => $dependsOnValue,
                            //     'depends_value_id' => $dependsValueId
                            // ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.questionnaire.index')
                            ->with('success', 'Questionnaire imported successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                            ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $periodeId = $request->periode_id;

        // Get all categories and questions
        $categories = Tb_Category::where('id_periode', $periodeId)
            ->with(['questions' => function($q) {
                $q->orderBy('order');
            }, 'questions.options'])
            ->orderBy('order')
            ->get();

        $sheetIndex = 0;
        foreach ($categories as $category) {
            // Create sheet
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle($category->category_name);

            // Set headers
            $headers = [
                'category order', 'for type', 'is_status_dependent', 'required_alumni_status',
                'question text', 'question type', 'question order', 'before text', 'after text',
                'scale min label', 'scale max label', 'options', 'other option indexes',
                'other before texts', 'other after texts', 'depends on', 'depends value'
            ];
            $sheet->fromArray([$headers], NULL, 'A1');

            // Set category info in row 2
            $categoryInfo = [
                $category->order,
                $category->for_type,
                $category->is_status_dependent ? 'true' : 'false',
                $category->required_alumni_status ? json_encode($category->required_alumni_status) : ''
            ];
            $sheet->fromArray([$categoryInfo], NULL, 'A2');

            // Process questions
            $row = 3;
            foreach ($category->questions as $question) {
                $options = $question->options->sortBy('order');
                
                // Get depends value as text if it's an option
                $dependsValue = '';
                if ($question->depends_on) {
                    $parentQuestion = Tb_Questions::with('options')->find($question->depends_on);
                    if ($parentQuestion && in_array($parentQuestion->type, ['option', 'multiple', 'rating', 'scale'])) {
                        $option = $parentQuestion->options
                            ->where('id_questions_options', $question->depends_value)
                            ->first();
                        $dependsValue = $option ? $option->option : '';
                    } else {
                        $dependsValue = $question->depends_value;
                    }
                }

                // Build question data array with all fields
                $questionData = [
                    '', '', '', '', // Skip category info columns
                    $question->question,
                    $question->type,
                    $question->order,
                    $question->before_text ?? '', // Always include before_text
                    $question->after_text ?? '',  // Always include after_text
                    $question->scale_min_label ?? '',
                    $question->scale_max_label ?? '',
                    in_array($question->type, ['option', 'multiple', 'rating', 'scale']) ? $options->pluck('option')->implode('|') : '',
                    in_array($question->type, ['option', 'multiple', 'rating', 'scale']) ? $options->where('is_other_option', true)->pluck('order')->implode(',') : '',
                    in_array($question->type, ['option', 'multiple', 'rating', 'scale']) ? $options->where('is_other_option', true)->pluck('other_before_text')->implode('|') : '',
                    in_array($question->type, ['option', 'multiple', 'rating', 'scale']) ? $options->where('is_other_option', true)->pluck('other_after_text')->implode('|') : '',
                    $question->depends_on ? Tb_Questions::find($question->depends_on)->question : '',
                    $dependsValue
                ];

                // Add debug logging
                // Log::info("Exporting question", [
                //     'question_id' => $question->id_question,
                //     'type' => $question->type,
                //     'before_text' => $question->before_text,
                //     'after_text' => $question->after_text
                // ]);

                $sheet->fromArray([$questionData], NULL, 'A' . $row);
                $row++;
            }

            $sheetIndex++;
        }

        // Save file
        $writer = new Xlsx($spreadsheet);
        $filename = 'questionnaire_export_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}