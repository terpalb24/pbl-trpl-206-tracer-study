<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use App\Models\Tb_Questionnaire;
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
        return view('admin.questionnaire.import-export');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->setCellValue('B1', 'Category Description');
        $sheet->setCellValue('C1', 'Category Type (alumni/company)');
        $sheet->setCellValue('D1', 'Question Text');
        $sheet->setCellValue('E1', 'Question Type (text/radio/checkbox/select)');
        $sheet->setCellValue('F1', 'Is Required (yes/no)');
        $sheet->setCellValue('G1', 'Options (separate with |)');
        $sheet->setCellValue('H1', 'Has Other Option (yes/no)');

        // Example data
        $sheet->setCellValue('A2', 'Academic Experience');
        $sheet->setCellValue('B2', 'Questions about your academic experience');
        $sheet->setCellValue('C2', 'alumni');
        $sheet->setCellValue('D2', 'How would you rate your overall academic experience?');
        $sheet->setCellValue('E2', 'radio');
        $sheet->setCellValue('F2', 'yes');
        $sheet->setCellValue('G2', 'Excellent|Good|Average|Poor|Very Poor');
        $sheet->setCellValue('H2', 'no');

        $sheet->setCellValue('A3', 'Academic Experience');
        $sheet->setCellValue('B3', 'Questions about your academic experience');
        $sheet->setCellValue('C3', 'alumni');
        $sheet->setCellValue('D3', 'What aspects of the curriculum could be improved?');
        $sheet->setCellValue('E3', 'checkbox');
        $sheet->setCellValue('F3', 'yes');
        $sheet->setCellValue('G3', 'Course content|Teaching methods|Assessment methods|Practical experience|Research opportunities');
        $sheet->setCellValue('H3', 'yes');

        $sheet->setCellValue('A4', 'Employment Information');
        $sheet->setCellValue('B4', 'Questions about your current employment');
        $sheet->setCellValue('C4', 'alumni');
        $sheet->setCellValue('D4', 'What is your current employment status?');
        $sheet->setCellValue('E4', 'select');
        $sheet->setCellValue('F4', 'yes');
        $sheet->setCellValue('G4', 'Employed full-time|Employed part-time|Self-employed|Unemployed|Pursuing further education');
        $sheet->setCellValue('H4', 'no');

        $sheet->setCellValue('A5', 'Company Feedback');
        $sheet->setCellValue('B5', 'Feedback about our graduates');
        $sheet->setCellValue('C5', 'company');
        $sheet->setCellValue('D5', 'How would you rate the overall performance of our graduates?');
        $sheet->setCellValue('E5', 'radio');
        $sheet->setCellValue('F5', 'yes');
        $sheet->setCellValue('G5', 'Excellent|Good|Average|Poor|Very Poor');
        $sheet->setCellValue('H5', 'no');

        // Auto-size columns
        foreach(range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'questionnaire_template_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);
            
            $categories = [];
            $questionsData = [];

            foreach ($dataRows as $row) {
                if (empty($row[0]) || empty($row[3])) {
                    continue; // Skip empty rows
                }

                $categoryName = $row[0];
                $categoryDesc = $row[1];
                $categoryType = strtolower($row[2]) === 'alumni' ? 'alumni' : 'company';
                $questionText = $row[3];
                $questionType = strtolower($row[4]);
                $isRequired = strtolower($row[5]) === 'yes' ? 1 : 0;
                $options = !empty($row[6]) ? explode('|', $row[6]) : [];
                $hasOtherOption = strtolower($row[7] ?? 'no') === 'yes' ? true : false;

                // Create or get category
                if (!isset($categories[$categoryName])) {
                    $category = Tb_Category::firstOrCreate(
                        ['name' => $categoryName],
                        [
                            'description' => $categoryDesc,
                            'for_type' => $categoryType
                        ]
                    );
                    $categories[$categoryName] = $category->id;
                }

                $categoryId = $categories[$categoryName];

                // Create question
                $question = Tb_Questions::create([
                    'id_category' => $categoryId,
                    'question_text' => $questionText,
                    'question_type' => $questionType,
                    'is_required' => $isRequired,
                ]);

                // Create options if applicable
                if (in_array($questionType, ['radio', 'checkbox', 'select']) && !empty($options)) {
                    foreach ($options as $index => $optionText) {
                        Tb_Question_Options::create([
                            'id_question' => $question->id,
                            'option' => trim($optionText),
                            'is_other_option' => ($hasOtherOption && $index == count($options) - 1) ? 1 : 0
                        ]);
                    }

                    // Add "Other" option if requested
                    if ($hasOtherOption && !in_array('Other', $options)) {
                        Tb_Question_Options::create([
                            'id_question' => $question->id,
                            'option' => 'Other',
                            'is_other_option' => 1
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.questionnaires.import-export')->with('success', 'Questionnaire imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing questionnaire: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->setCellValue('B1', 'Category Description');
        $sheet->setCellValue('C1', 'Category Type');
        $sheet->setCellValue('D1', 'Question Text');
        $sheet->setCellValue('E1', 'Question Type');
        $sheet->setCellValue('F1', 'Is Required');
        $sheet->setCellValue('G1', 'Options');
        $sheet->setCellValue('H1', 'Has Other Option');

        // Get data
        $categories = Tb_Category::all();
        $row = 2;

        foreach ($categories as $category) {
            $questions = Tb_Questions::where('id_category', $category->id)->get();
            
            foreach ($questions as $question) {
                $sheet->setCellValue('A' . $row, $category->name);
                $sheet->setCellValue('B' . $row, $category->description);
                $sheet->setCellValue('C' . $row, $category->for_type);
                $sheet->setCellValue('D' . $row, $question->question_text);
                $sheet->setCellValue('E' . $row, $question->question_type);
                $sheet->setCellValue('F' . $row, $question->is_required ? 'yes' : 'no');
                
                // Get options
                if (in_array($question->question_type, ['radio', 'checkbox', 'select'])) {
                    $options = Tb_Question_Options::where('id_question', $question->id)->get();
                    $optionTexts = [];
                    $hasOtherOption = false;
                    
                    foreach ($options as $option) {
                        if ($option->is_other_option) {
                            $hasOtherOption = true;
                            if (strtolower($option->option) !== 'other') {
                                $optionTexts[] = $option->option;
                            }
                        } else {
                            $optionTexts[] = $option->option;
                        }
                    }
                    
                    $sheet->setCellValue('G' . $row, implode('|', $optionTexts));
                    $sheet->setCellValue('H' . $row, $hasOtherOption ? 'yes' : 'no');
                } else {
                    $sheet->setCellValue('G' . $row, '');
                    $sheet->setCellValue('H' . $row, 'no');
                }
                
                $row++;
            }
        }

        // Auto-size columns
        foreach(range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'questionnaire_export_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
