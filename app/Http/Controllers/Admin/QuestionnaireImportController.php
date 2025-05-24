<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_Periode;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_Question_Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionnaireImportController extends Controller
{
    /**
     * Show the import form
     */
    public function showImportForm($id_periode)
    {
        $periode = Tb_Periode::findOrFail($id_periode);
        return view('admin.questionnaire.import', compact('periode'));
    }
    
    /**
     * Process the import
     */
    public function import(Request $request, $id_periode)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls',
        ]);
        
        $periode = Tb_Periode::findOrFail($id_periode);
        
        try {
            $file = $request->file('import_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            
            // Start from row 2 (assuming row 1 is header)
            $currentCategory = null;
            $currentQuestion = null;
            $categoryOrder = 1;
            $questionOrder = 1;
            $optionOrder = 1;
            
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowType = $worksheet->getCell('A' . $row)->getValue();
                
                if ($rowType == 'CATEGORY') {
                    $categoryName = $worksheet->getCell('B' . $row)->getValue();
                    $categoryType = $worksheet->getCell('C' . $row)->getValue() ?: 'single';
                    $forType = $worksheet->getCell('D' . $row)->getValue() ?: 'both';
                    
                    if (!in_array($forType, ['alumni', 'company', 'both'])) {
                        $forType = 'both';
                    }
                    
                    $currentCategory = Tb_Category::create([
                        'id_periode' => $periode->id_periode,
                        'category_name' => $categoryName,
                        'order' => $categoryOrder++,
                        'type' => $categoryType,
                        'for_type' => $forType,
                    ]);
                    
                    $questionOrder = 1; // Reset question order for new category
                }
                elseif ($rowType == 'QUESTION' && $currentCategory) {
                    $questionText = $worksheet->getCell('B' . $row)->getValue();
                    $questionType = $worksheet->getCell('C' . $row)->getValue() ?: 'text';
                    
                    if (!in_array($questionType, ['text', 'option'])) {
                        $questionType = 'text';
                    }
                    
                    $currentQuestion = Tb_Questions::create([
                        'id_category' => $currentCategory->id_category,
                        'question' => $questionText,
                        'order' => $questionOrder++,
                        'type' => $questionType,
                    ]);
                    
                    $optionOrder = 1; // Reset option order for new question
                }
                elseif ($rowType == 'OPTION' && $currentQuestion && $currentQuestion->type == 'option') {
                    $optionText = $worksheet->getCell('B' . $row)->getValue();
                    $isOther = strtolower($worksheet->getCell('C' . $row)->getValue()) == 'yes';
                    
                    Tb_Question_Options::create([
                        'id_question' => $currentQuestion->id_question,
                        'order' => $optionOrder++,
                        'option' => $optionText,
                        'is_other_option' => $isOther,
                    ]);
                }
            }
            
            return redirect()->route('admin.questionnaire.show', $id_periode)
                ->with('success', 'Kuisioner berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengimpor kuisioner: ' . $e->getMessage());
        }
    }
    
    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'TYPE');
        $sheet->setCellValue('B1', 'NAME/TEXT');
        $sheet->setCellValue('C1', 'TYPE/IS_OTHER');
        $sheet->setCellValue('D1', 'FOR_TYPE');
        
        // Example data
        $row = 2;
        
        // Category example
        $sheet->setCellValue('A' . $row, 'CATEGORY');
        $sheet->setCellValue('B' . $row, 'Data Pribadi');
        $sheet->setCellValue('C' . $row, 'single');
        $sheet->setCellValue('D' . $row, 'alumni');
        $row++;
        
        // Question example (text)
        $sheet->setCellValue('A' . $row, 'QUESTION');
        $sheet->setCellValue('B' . $row, 'Apa nama lengkap Anda?');
        $sheet->setCellValue('C' . $row, 'text');
        $row++;
        
        // Question example (option)
        $sheet->setCellValue('A' . $row, 'QUESTION');
        $sheet->setCellValue('B' . $row, 'Apa status pekerjaan Anda saat ini?');
        $sheet->setCellValue('C' . $row, 'option');
        $row++;
        
        // Option examples
        $sheet->setCellValue('A' . $row, 'OPTION');
        $sheet->setCellValue('B' . $row, 'Bekerja');
        $sheet->setCellValue('C' . $row, 'No');
        $row++;
        
        $sheet->setCellValue('A' . $row, 'OPTION');
        $sheet->setCellValue('B' . $row, 'Wirausaha');
        $sheet->setCellValue('C' . $row, 'No');
        $row++;
        
        $sheet->setCellValue('A' . $row, 'OPTION');
        $sheet->setCellValue('B' . $row, 'Lainnya');
        $sheet->setCellValue('C' . $row, 'Yes');
        $row++;
        
        // Another category example
        $sheet->setCellValue('A' . $row, 'CATEGORY');
        $sheet->setCellValue('B' . $row, 'Penilaian Perusahaan');
        $sheet->setCellValue('C' . $row, 'multiple');
        $sheet->setCellValue('D' . $row, 'company');
        $row++;
        
        // Add instructions
        $sheet->setCellValue('A' . ($row + 2), 'INSTRUCTIONS:');
        $sheet->setCellValue('A' . ($row + 3), '1. TYPE: CATEGORY, QUESTION, or OPTION');
        $sheet->setCellValue('A' . ($row + 4), '2. NAME/TEXT: Category name, question text, or option text');
        $sheet->setCellValue('A' . ($row + 5), '3. TYPE/IS_OTHER: For categories (single/multiple/text), for questions (text/option), for options (Yes/No for is_other)');
        $sheet->setCellValue('A' . ($row + 6), '4. FOR_TYPE: For categories only (alumni/company/both)');
        
        // Auto-size columns
        foreach(range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create the file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_kuisioner.xlsx';
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
        
        // Return the file as a download
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
