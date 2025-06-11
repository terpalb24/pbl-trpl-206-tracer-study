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
        $sheet = $spreadsheet->getActiveSheet();

        // Header dinamis + kolom dependency
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->setCellValue('B1', 'Category Order');
        $sheet->setCellValue('C1', 'For Type (alumni/company/both)');
        $sheet->setCellValue('D1', 'Question Text');
        $sheet->setCellValue('E1', 'Question Type (text/option/multiple/rating/scale/date/location/numeric/email)');
        $sheet->setCellValue('F1', 'Question Order');
        $sheet->setCellValue('G1', 'Before Text');
        $sheet->setCellValue('H1', 'After Text');
        $sheet->setCellValue('I1', 'Scale Min Label');
        $sheet->setCellValue('J1', 'Scale Max Label');
        $sheet->setCellValue('K1', 'Options (separate with |)');
        $sheet->setCellValue('L1', 'Other Option Indexes (comma separated, 0-based)');
        $sheet->setCellValue('M1', 'Other Before Texts (| separated, match option index)');
        $sheet->setCellValue('N1', 'Other After Texts (| separated, match option index)');
        $sheet->setCellValue('O1', 'Depends On Question (Question Text)');
        $sheet->setCellValue('P1', 'Depends On Value');

        // Contoh data
        $sheet->setCellValue('A2', 'Pengalaman Akademik');
        $sheet->setCellValue('B2', '1');
        $sheet->setCellValue('C2', 'alumni');
        $sheet->setCellValue('D2', 'Bagaimana penilaian Anda terhadap pengalaman akademik?');
        $sheet->setCellValue('E2', 'option');
        $sheet->setCellValue('F2', '1');
        $sheet->setCellValue('G2', '');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', '');
        $sheet->setCellValue('J2', '');
        $sheet->setCellValue('K2', 'Sangat Baik|Baik|Cukup|Kurang|Sangat Kurang');
        $sheet->setCellValue('L2', '4');
        $sheet->setCellValue('M2', '| | | |Sebutkan:');
        $sheet->setCellValue('N2', '| | | |');
        $sheet->setCellValue('O2', '');
        $sheet->setCellValue('P2', '');

        $sheet->setCellValue('A3', 'Kepuasan Fasilitas');
        $sheet->setCellValue('B3', '2');
        $sheet->setCellValue('C3', 'both');
        $sheet->setCellValue('D3', 'Nilai fasilitas kampus secara keseluruhan');
        $sheet->setCellValue('E3', 'rating');
        $sheet->setCellValue('F3', '1');
        $sheet->setCellValue('G3', '');
        $sheet->setCellValue('H3', '');
        $sheet->setCellValue('I3', '');
        $sheet->setCellValue('J3', '');
        $sheet->setCellValue('K3', '1|2|3|4|5');
        $sheet->setCellValue('L3', '');
        $sheet->setCellValue('M3', '');
        $sheet->setCellValue('N3', '');
        $sheet->setCellValue('O3', '');
        $sheet->setCellValue('P3', '');

        $sheet->setCellValue('A4', 'Lokasi Kerja');
        $sheet->setCellValue('B4', '3');
        $sheet->setCellValue('C4', 'alumni');
        $sheet->setCellValue('D4', 'Dimana lokasi kerja Anda saat ini?');
        $sheet->setCellValue('E4', 'location');
        $sheet->setCellValue('F4', '1');
        $sheet->setCellValue('G4', '');
        $sheet->setCellValue('H4', '');
        $sheet->setCellValue('I4', '');
        $sheet->setCellValue('J4', '');
        $sheet->setCellValue('K4', '');
        $sheet->setCellValue('L4', '');
        $sheet->setCellValue('M4', '');
        $sheet->setCellValue('N4', '');
        $sheet->setCellValue('O4', 'Bagaimana penilaian Anda terhadap pengalaman akademik?');
        $sheet->setCellValue('P4', 'Sangat Baik');

        // Auto-size columns
        foreach(range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

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
            'periode_id' => 'required|exists:tb_periode,id_periode',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            $file = $request->file('file');
            $periodeId = $request->input('periode_id');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Header mapping
            $header = array_map('strtolower', $rows[0]);
            $dataRows = array_slice($rows, 1);

            $categoryMap = [];
            $questionMap = []; // key: question text + category + for_type => id_question
            foreach ($dataRows as $row) {
                if (empty($row[0]) || empty($row[3]) || empty($row[4])) continue;

                $catName = trim($row[0]);
                $catOrder = intval($row[1] ?? 1);
                $forType = strtolower(trim($row[2] ?? 'alumni'));
                $forType = in_array($forType, ['alumni', 'company', 'both']) ? $forType : 'alumni';

                // Buat kategori pada periode yang dipilih, dan for_type sesuai
                $categoryKey = $catName . '|' . $forType;
                if (!isset($categoryMap[$categoryKey])) {
                    // Jika both, buat dua kategori: alumni dan company
                    if ($forType === 'both') {
                        foreach (['alumni', 'company'] as $ft) {
                            $cat = Tb_Category::firstOrCreate(
                                [
                                    'category_name' => $catName,
                                    'id_periode' => $periodeId,
                                    'for_type' => $ft
                                ],
                                [
                                    'order' => $catOrder,
                                ]
                            );
                            $categoryMap[$catName . '|' . $ft] = $cat->id_category;
                        }
                        // Gunakan alumni untuk pertanyaan ini, nanti pertanyaan bisa diduplikasi jika perlu
                        $id_category = $categoryMap[$catName . '|alumni'];
                    } else {
                        $cat = Tb_Category::firstOrCreate(
                            [
                                'category_name' => $catName,
                                'id_periode' => $periodeId,
                                'for_type' => $forType
                            ],
                            [
                                'order' => $catOrder,
                            ]
                        );
                        $categoryMap[$categoryKey] = $cat->id_category;
                        $id_category = $cat->id_category;
                    }
                } else {
                    $id_category = $categoryMap[$categoryKey];
                }

                // Pertanyaan
                $questionText = trim($row[3]);
                $questionType = strtolower(trim($row[4]));
                $questionOrder = intval($row[5] ?? 1);
                $beforeText = trim($row[6] ?? '');
                $afterText = trim($row[7] ?? '');
                $scaleMinLabel = trim($row[8] ?? '');
                $scaleMaxLabel = trim($row[9] ?? '');

                // Dependency columns
                $dependsOnText = trim($row[14] ?? '');
                $dependsOnValue = trim($row[15] ?? '');

                // Helper to get depends_on id_question
                $getDependsOnId = function($catName, $forType, $dependsOnText) use ($questionMap) {
                    // Try both for_type if both
                    if ($forType === 'both') {
                        foreach (['alumni', 'company'] as $ft) {
                            $key = $catName . '|' . $ft . '|' . $dependsOnText;
                            if (isset($questionMap[$key])) return $questionMap[$key];
                        }
                    } else {
                        $key = $catName . '|' . $forType . '|' . $dependsOnText;
                        if (isset($questionMap[$key])) return $questionMap[$key];
                    }
                    return null;
                };

                // Jika both, buat pertanyaan di dua kategori
                if ($forType === 'both') {
                    foreach (['alumni', 'company'] as $ft) {
                        $id_cat = $categoryMap[$catName . '|' . $ft];
                        $dependsOnId = $dependsOnText ? $getDependsOnId($catName, $ft, $dependsOnText) : null;
                        $question = Tb_Questions::create([
                            'id_category' => $id_cat,
                            'question' => $questionText,
                            'type' => $questionType,
                            'order' => $questionOrder,
                            'before_text' => $beforeText,
                            'after_text' => $afterText,
                            'scale_min_label' => $scaleMinLabel,
                            'scale_max_label' => $scaleMaxLabel,
                            'depends_on' => $dependsOnId,
                            'depends_value' => $dependsOnValue ?: null,
                        ]);
                        // Map for dependency
                        $questionMap[$catName . '|' . $ft . '|' . $questionText] = $question->id_question;
                        // Opsi (jika applicable)
                        $optionsRaw = trim($row[10] ?? '');
                        $otherIndexesRaw = trim($row[11] ?? '');
                        $otherBeforeRaw = trim($row[12] ?? '');
                        $otherAfterRaw = trim($row[13] ?? '');

                        $options = $optionsRaw !== '' ? explode('|', $optionsRaw) : [];
                        $otherIndexes = $otherIndexesRaw !== '' ? array_map('intval', explode(',', $otherIndexesRaw)) : [];
                        $otherBeforeArr = $otherBeforeRaw !== '' ? explode('|', $otherBeforeRaw) : [];
                        $otherAfterArr = $otherAfterRaw !== '' ? explode('|', $otherAfterRaw) : [];

                        if (in_array($questionType, ['option', 'multiple', 'rating', 'scale']) && count($options)) {
                            foreach ($options as $idx => $optText) {
                                $isOther = in_array($idx, $otherIndexes);
                                $otherBefore = isset($otherBeforeArr[$idx]) ? trim($otherBeforeArr[$idx]) : null;
                                $otherAfter = isset($otherAfterArr[$idx]) ? trim($otherAfterArr[$idx]) : null;
                                Tb_Question_Options::create([
                                    'id_question' => $question->id_question,
                                    'option' => trim($optText),
                                    'order' => $idx + 1,
                                    'is_other_option' => $isOther ? 1 : 0,
                                    'other_before_text' => $isOther ? $otherBefore : null,
                                    'other_after_text' => $isOther ? $otherAfter : null,
                                ]);
                            }
                        }
                    }
                } else {
                    $dependsOnId = $dependsOnText ? $getDependsOnId($catName, $forType, $dependsOnText) : null;
                    $question = Tb_Questions::create([
                        'id_category' => $id_category,
                        'question' => $questionText,
                        'type' => $questionType,
                        'order' => $questionOrder,
                        'before_text' => $beforeText,
                        'after_text' => $afterText,
                        'scale_min_label' => $scaleMinLabel,
                        'scale_max_label' => $scaleMaxLabel,
                        'depends_on' => $dependsOnId,
                        'depends_value' => $dependsOnValue ?: null,
                    ]);
                    $questionMap[$catName . '|' . $forType . '|' . $questionText] = $question->id_question;
                    // Opsi (jika applicable)
                    $optionsRaw = trim($row[10] ?? '');
                    $otherIndexesRaw = trim($row[11] ?? '');
                    $otherBeforeRaw = trim($row[12] ?? '');
                    $otherAfterRaw = trim($row[13] ?? '');

                    $options = $optionsRaw !== '' ? explode('|', $optionsRaw) : [];
                    $otherIndexes = $otherIndexesRaw !== '' ? array_map('intval', explode(',', $otherIndexesRaw)) : [];
                    $otherBeforeArr = $otherBeforeRaw !== '' ? explode('|', $otherBeforeRaw) : [];
                    $otherAfterArr = $otherAfterRaw !== '' ? explode('|', $otherAfterRaw) : [];

                    if (in_array($questionType, ['option', 'multiple', 'rating', 'scale']) && count($options)) {
                        foreach ($options as $idx => $optText) {
                            $isOther = in_array($idx, $otherIndexes);
                            $otherBefore = isset($otherBeforeArr[$idx]) ? trim($otherBeforeArr[$idx]) : null;
                            $otherAfter = isset($otherAfterArr[$idx]) ? trim($otherAfterArr[$idx]) : null;
                            Tb_Question_Options::create([
                                'id_question' => $question->id_question,
                                'option' => trim($optText),
                                'order' => $idx + 1,
                                'is_other_option' => $isOther ? 1 : 0,
                                'other_before_text' => $isOther ? $otherBefore : null,
                                'other_after_text' => $isOther ? $otherAfter : null,
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.questionnaire.index')->with('success', 'Import questionnaire berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing questionnaire: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodes = \App\Models\Tb_Periode::orderBy('start_date', 'desc')->get();

        if (!$periodeId) {
            return view('admin.questionnaire.import-export', compact('periodes'))->with('export_mode', true);
        }

        $periode = \App\Models\Tb_Periode::find($periodeId);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header dinamis + kolom dependency
        $sheet->setCellValue('A1', 'Category Name');
        $sheet->setCellValue('B1', 'Category Order');
        $sheet->setCellValue('C1', 'For Type');
        $sheet->setCellValue('D1', 'Question Text');
        $sheet->setCellValue('E1', 'Question Type');
        $sheet->setCellValue('F1', 'Question Order');
        $sheet->setCellValue('G1', 'Before Text');
        $sheet->setCellValue('H1', 'After Text');
        $sheet->setCellValue('I1', 'Scale Min Label');
        $sheet->setCellValue('J1', 'Scale Max Label');
        $sheet->setCellValue('K1', 'Options');
        $sheet->setCellValue('L1', 'Other Option Indexes');
        $sheet->setCellValue('M1', 'Other Before Texts');
        $sheet->setCellValue('N1', 'Other After Texts');
        $sheet->setCellValue('O1', 'Depends On Question (Question Text)');
        $sheet->setCellValue('P1', 'Depends On Value');

        $categories = \App\Models\Tb_Category::where('id_periode', $periodeId)->orderBy('order')->get();
        $row = 2;
        foreach ($categories as $cat) {
            $questions = Tb_Questions::where('id_category', $cat->id_category)->orderBy('order')->get();
            foreach ($questions as $q) {
                $sheet->setCellValue('A' . $row, $cat->category_name);
                $sheet->setCellValue('B' . $row, $cat->order);
                $sheet->setCellValue('C' . $row, $cat->for_type);
                $sheet->setCellValue('D' . $row, $q->question);
                $sheet->setCellValue('E' . $row, $q->type);
                $sheet->setCellValue('F' . $row, $q->order);
                $sheet->setCellValue('G' . $row, $q->before_text);
                $sheet->setCellValue('H' . $row, $q->after_text);
                $sheet->setCellValue('I' . $row, $q->scale_min_label);
                $sheet->setCellValue('J' . $row, $q->scale_max_label);

                $options = Tb_Question_Options::where('id_question', $q->id_question)->orderBy('order')->get();
                $optionTexts = [];
                $otherIndexes = [];
                $otherBeforeArr = [];
                $otherAfterArr = [];
                foreach ($options as $idx => $opt) {
                    $optionTexts[] = $opt->option;
                    if ($opt->is_other_option) {
                        $otherIndexes[] = $idx;
                        $otherBeforeArr[$idx] = $opt->other_before_text ?? '';
                        $otherAfterArr[$idx] = $opt->other_after_text ?? '';
                    } else {
                        $otherBeforeArr[$idx] = '';
                        $otherAfterArr[$idx] = '';
                    }
                }
                $sheet->setCellValue('K' . $row, implode('|', $optionTexts));
                $sheet->setCellValue('L' . $row, implode(',', $otherIndexes));
                $sheet->setCellValue('M' . $row, implode('|', $otherBeforeArr));
                $sheet->setCellValue('N' . $row, implode('|', $otherAfterArr));

                // Dependency columns
                $dependsOnText = '';
                $dependsOnValue = '';
                if ($q->depends_on) {
                    $dependsQ = Tb_Questions::find($q->depends_on);
                    if ($dependsQ) {
                        $dependsOnText = $dependsQ->question;
                        $dependsOnValue = $q->depends_value;
                    }
                }
                $sheet->setCellValue('O' . $row, $dependsOnText);
                $sheet->setCellValue('P' . $row, $dependsOnValue);

                $row++;
            }
        }

        foreach(range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'questionnaire_export_periode_' . $periodeId . '_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
