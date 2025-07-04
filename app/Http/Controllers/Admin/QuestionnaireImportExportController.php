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

        // Buat template dengan contoh kategori sederhana
        $templateCategories = ['Data Pribadi', 'Riwayat Pendidikan', 'Pengalaman Kerja'];

        // Buat satu sheet per kategori contoh
        foreach ($templateCategories as $i => $catName) {
            $sheet = $i === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle(substr($catName, 0, 31)); // Judul sheet max 31 karakter

            // Header
            $sheet->setCellValue('A1', 'Category Name');
            $sheet->setCellValue('B1', 'Category Description');
            $sheet->setCellValue('C1', 'Category Order');
            $sheet->setCellValue('D1', 'For Type (alumni/company/both)');
            $sheet->setCellValue('E1', 'Is Status Dependent (TRUE/FALSE)');
            $sheet->setCellValue('F1', 'Required Alumni Status (pisahkan koma)');
            $sheet->setCellValue('G1', 'Is Graduation Year Dependent (TRUE/FALSE)');
            $sheet->setCellValue('H1', 'Required Graduation Years (pisahkan koma)');
            $sheet->setCellValue('I1', 'Question Text');
            $sheet->setCellValue('J1', 'Question Type (text/option/multiple/rating/scale/date/location/numeric/email)');
            $sheet->setCellValue('K1', 'Question Order');
            $sheet->setCellValue('L1', 'Before Text');
            $sheet->setCellValue('M1', 'After Text');
            $sheet->setCellValue('N1', 'Scale Min Label');
            $sheet->setCellValue('O1', 'Scale Max Label');
            $sheet->setCellValue('P1', 'Options (separate with |)');
            $sheet->setCellValue('Q1', 'Other Option Indexes (comma separated, 0-based)');
            $sheet->setCellValue('R1', 'Other Before Texts (| separated, match option index)');
            $sheet->setCellValue('S1', 'Other After Texts (| separated, match option index)');
            $sheet->setCellValue('T1', 'Depends On Question (Question Text)');
            $sheet->setCellValue('U1', 'Depends On Values (| separated for multiple values)');

            // Contoh data kategori di baris 2
            $sheet->fromArray([
                $catName, 'Deskripsi untuk ' . $catName, $i + 1, 'alumni', 'FALSE', '', 'FALSE', '', 'Contoh pertanyaan untuk ' . $catName, 'option', 1, '', '', '', '', 'Sangat Baik|Baik|Cukup|Kurang|Sangat Kurang', '4', '| | | |Sebutkan:', '| | | |', '', 'Sangat Baik|Baik'
            ], null, 'A2');

            // Auto-size columns
            foreach(range('A', 'U') as $column) {
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
        set_time_limit(300);
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
                $categoryData = $worksheet->rangeToArray('A2:H2')[0];
                $categoryName = trim($categoryData[0] ?? $category);
                $categoryDescription = trim($categoryData[1] ?? '');
                $categoryOrder = (int)($categoryData[2] ?? 1);
                $forType = strtolower(trim($categoryData[3] ?? 'both'));
                $isStatusDependent = strtolower(trim($categoryData[4] ?? 'false')) === 'true';
                $requiredAlumniStatus = $categoryData[5] ? array_filter(explode(',', $categoryData[5])) : null;
                $isGraduationYearDependent = strtolower(trim($categoryData[6] ?? 'false')) === 'true';
                $requiredGraduationYears = $categoryData[7] ? array_filter(explode(',', $categoryData[7])) : null;
                
                if (!in_array($forType, $validForTypes)) {
                    throw new \Exception("Invalid for_type '$forType' in sheet '$category'. Must be one of: " . implode(', ', $validForTypes));
                }

                // Find or create category
                $categoryModel = Tb_Category::where('category_name', 'LIKE', "%$categoryName%")
                                      ->where('id_periode', $request->periode_id)
                                      ->first();

                if (!$categoryModel) {
                    $categoryModel = Tb_Category::create([
                        'id_periode' => $request->periode_id,
                        'category_name' => $categoryName,
                        'description' => $categoryDescription,
                        'order' => $categoryOrder,
                        'for_type' => $forType,
                        'is_status_dependent' => $isStatusDependent,
                        'required_alumni_status' => $requiredAlumniStatus,
                        'is_graduation_year_dependent' => $isGraduationYearDependent,
                        'required_graduation_years' => $requiredGraduationYears
                    ]);
                } else {
                    $categoryModel->update([
                        'category_name' => $categoryName,
                        'description' => $categoryDescription,
                        'order' => $categoryOrder,
                        'for_type' => $forType,
                        'is_status_dependent' => $isStatusDependent,
                        'required_alumni_status' => $requiredAlumniStatus,
                        'is_graduation_year_dependent' => $isGraduationYearDependent,
                        'required_graduation_years' => $requiredGraduationYears
                    ]);
                }

                $categoryMap[$categoryName] = $categoryModel->id_category;
                $processedCategories[] = $categoryName;
            }

            // Second pass - process questions
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $category = trim($sheet->getTitle());
                
                // Get category name from row 2, column A
                $categoryData = $sheet->rangeToArray('A2:A2')[0];
                $categoryName = trim($categoryData[0] ?? $category);
                
                $categoryId = $categoryMap[$categoryName];
                
                // Get headers
                $headers = $sheet->rangeToArray('A1:U1')[0];
                $h = $this->createHeaderMap($headers);

                // Start from row 3 (after category data)
                for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
                    $rowData = $sheet->rangeToArray('A'.$row.':'.$sheet->getHighestColumn().$row)[0];
                    
                    if (empty(array_filter($rowData))) continue;

                    // Map question data according to export structure (shifted by 2 due to graduation year columns)
                    $questionData = [
                        'id_category' => $categoryId,
                        'question' => trim($rowData[8] ?? ''), // question text (column I)
                        'type' => trim($rowData[9] ?? ''), // question type (column J)
                        'order' => (int)($rowData[10] ?? 0), // question order (column K)
                        'before_text' => trim($rowData[11] ?? ''), // before text (column L)
                        'after_text' => trim($rowData[12] ?? ''), // after text (column M)
                        'scale_min_label' => trim($rowData[13] ?? ''), // scale min label (column N)
                        'scale_max_label' => trim($rowData[14] ?? ''), // scale max label (column O)
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
                        $options = $rowData[15] ? array_filter(explode('|', $rowData[15])) : []; // options (column P)
                        $otherIndexes = $rowData[16] ? 
                            array_map(function($idx) { 
                                return (int)$idx; 
                            }, explode(',', $rowData[16])) : []; // other indexes (column Q)
                        $otherBeforeTexts = $rowData[17] ? array_filter(explode('|', $rowData[17])) : []; // other before texts (column R)
                        $otherAfterTexts = $rowData[18] ? array_filter(explode('|', $rowData[18])) : []; // other after texts (column S)

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
                    $dependsOnText = $rowData[19] ?? ''; // depends on question text (column T)
                    $dependsOnValues = $rowData[20] ?? ''; // depends on values (column U)

                    if ($dependsOnText) {
                        $parentQuestion = Tb_Questions::where('question', 'LIKE', "%$dependsOnText%")
                            ->where('id_category', $categoryId)
                            ->first();

                        if ($parentQuestion) {
                            $dependsValueIds = [];
                            
                            // ✅ Handle multiple dependency values
                            if ($dependsOnValues && trim($dependsOnValues) !== '') {
                                $dependsValueArray = array_filter(explode('|', $dependsOnValues));
                                
                                // Log::info("Processing dependency values", [
                                //     'question_id' => $question->id_question,
                                //     'parent_question_id' => $parentQuestion->id_question,
                                //     'raw_depends_values' => $dependsOnValues,
                                //     'parsed_values' => $dependsValueArray
                                // ]);
                                
                                // For option-based questions, map the text values to option IDs
                                if (in_array($parentQuestion->type, ['option', 'multiple', 'rating', 'scale'])) {
                                    $parentOptions = Tb_Question_Options::where('id_question', $parentQuestion->id_question)
                                        ->get();
                                    
                                    foreach ($dependsValueArray as $valueText) {
                                        $valueText = trim($valueText);
                                        if (empty($valueText)) continue;
                                        
                                        // Find matching option by text
                                        $matchingOption = $parentOptions->first(function($option) use ($valueText) {
                                            return trim(strtolower($option->option)) === trim(strtolower($valueText));
                                        });

                                        if ($matchingOption) {
                                            $dependsValueIds[] = $matchingOption->id_questions_options;
                                        } else {
                                            Log::warning("Could not find matching option for depends_value", [
                                                'question_id' => $question->id_question,
                                                'parent_question_id' => $parentQuestion->id_question,
                                                'depends_value_text' => $valueText,
                                                'available_options' => $parentOptions->pluck('option')->toArray()
                                            ]);
                                        }
                                    }
                                } else {
                                    // For non-option questions, use the values as-is
                                    $dependsValueIds = $dependsValueArray;
                                }
                            }

                            // ✅ Store as comma-separated string (matching the controller logic)
                            $dependsValueString = !empty($dependsValueIds) ? implode(',', $dependsValueIds) : null;
                            
                            if ($dependsValueString) {
                                $question->update([
                                    'depends_on' => $parentQuestion->id_question,
                                    'depends_value' => $dependsValueString
                                ]);

                                // Log::info("Updated question dependency with multiple values", [
                                //     'question_id' => $question->id_question,
                                //     'depends_on' => $parentQuestion->id_question,
                                //     'depends_value_texts' => $dependsValueArray ?? [],
                                //     'depends_value_ids' => $dependsValueIds,
                                //     'depends_value_string' => $dependsValueString
                                // ]);
                            }
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

        // Get all categories and questions with custom ordering
        $categories = Tb_Category::where('id_periode', $periodeId)
            ->with(['questions' => function($q) {
                $q->orderBy('order');
            }, 'questions.options'])
            ->orderBy('order')
            ->get()
            ->sortBy(function($category) {
                // Custom sort: alumni first, company second, both third
                switch($category->for_type) {
                    case 'alumni':
                        return 1;
                    case 'company':
                        return 2;
                    case 'both':
                        return 3;
                    default:
                        return 4;
                }
            });

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
                'category name', 'category description', 'category order', 'for type', 'is_status_dependent', 'required_alumni_status',
                'is_graduation_year_dependent', 'required_graduation_years',
                'question text', 'question type', 'question order', 'before text', 'after text',
                'scale min label', 'scale max label', 'options', 'other option indexes',
                'other before texts', 'other after texts', 'depends on', 'depends values'
            ];
            $sheet->fromArray([$headers], NULL, 'A1');

            // Set category info in row 2
            $categoryInfo = [
                $category->category_name,
                $category->description ?? '',
                $category->order,
                $category->for_type,
                $category->is_status_dependent ? 'true' : 'false',
                $category->required_alumni_status ? implode(',', $category->required_alumni_status) : '',
                $category->is_graduation_year_dependent ? 'true' : 'false',
                $category->required_graduation_years ? implode(',', $category->required_graduation_years) : ''
            ];
            $sheet->fromArray([$categoryInfo], NULL, 'A2');

            // Process questions
            $row = 3;
            foreach ($category->questions as $question) {
                $options = $question->options->sortBy('order');
                
                // ✅ Get depends values as text if it's an option (supporting multiple values)
                $dependsValues = '';
                if ($question->depends_on) {
                    $parentQuestion = Tb_Questions::with('options')->find($question->depends_on);
                    if ($parentQuestion && in_array($parentQuestion->type, ['option', 'multiple', 'rating', 'scale'])) {
                        // ✅ Handle multiple dependency values (comma-separated)
                        if ($question->depends_value && trim($question->depends_value) !== '') {
                            $dependsValueIds = array_filter(explode(',', $question->depends_value));
                            $dependsValueTexts = [];
                            
                            // Log::info("Exporting dependency values", [
                            //     'question_id' => $question->id_question,
                            //     'parent_question_id' => $parentQuestion->id_question,
                            //     'raw_depends_value' => $question->depends_value,
                            //     'parsed_ids' => $dependsValueIds
                            // ]);
                            
                            foreach ($dependsValueIds as $valueId) {
                                $valueId = trim($valueId);
                                if (empty($valueId)) continue;
                                
                                $option = $parentQuestion->options
                                    ->where('id_questions_options', $valueId)
                                    ->first();
                                if ($option) {
                                    $dependsValueTexts[] = $option->option;
                                }
                            }
                            
                            // ✅ Join multiple values with pipe separator
                            $dependsValues = implode('|', $dependsValueTexts);
                            
                            // Log::info("Exported dependency values", [
                            //     'question_id' => $question->id_question,
                            //     'depends_value_texts' => $dependsValueTexts,
                            //     'final_depends_values' => $dependsValues
                            // ]);
                        }
                    } else {
                        // ✅ For non-option questions, the depends_value might still be comma-separated
                        $dependsValues = $question->depends_value;
                    }
                }

                // Build question data array with all fields
                $questionData = [
                    '', '', '', '', '', '', '', '', // Skip category info columns (A-H)
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
                    $dependsValues
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

        // Auto-size all columns and add simple styling
        for ($i = 0; $i < $sheetIndex; $i++) {
            $sheet = $spreadsheet->setActiveSheetIndex($i);
            
            // Auto-size columns
            foreach(range('A', 'U') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add simple styling
            // Header row styling
            $headerRange = 'A1:U1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            // Category info row styling
            $categoryRange = 'A2:U2';
            $sheet->getStyle($categoryRange)->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7F3FF']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            // Data rows styling
            $lastRow = $sheet->getHighestRow();
            if ($lastRow > 2) {
                $dataRange = 'A3:U' . $lastRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
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