<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_Periode;
use App\Models\Tb_User_Answers;
use App\Models\Tb_User_Answer_Item;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Http\Request;

class EksportRespondenController extends Controller
{
    public function export($id_periode, Request $request)
    {
        $periode = Tb_Periode::with(['categories.questions.options'])->findOrFail($id_periode);

        // Get all user answers for this periode
        $userAnswers = Tb_User_Answers::where('id_periode', $id_periode)
            ->with(['user.alumni', 'user.company'])
            ->where('status', 'completed')
            ->get();

        // Prepare spreadsheet
        $spreadsheet = new Spreadsheet();

        // Prepare dynamic question headers
        $headers = [
            'No', 'Nama', 'Tipe', 'NIM/Email', 'Tanggal Isi'
        ];

        // Pisahkan pertanyaan berdasarkan kategori dan tipe user
        $categoriesAlumni = [];
        $categoriesCompany = [];
        foreach ($periode->categories as $category) {
            if (in_array($category->for_type, ['alumni', 'both'])) {
                $categoriesAlumni[] = [
                    'category' => $category,
                    'questions' => $category->questions
                ];
            }
            if (in_array($category->for_type, ['company', 'both'])) {
                $categoriesCompany[] = [
                    'category' => $category,
                    'questions' => $category->questions
                ];
            }
        }

        // Split userAnswers by type
        $alumniAnswers = $userAnswers->filter(function($ua) {
            return $ua->user && $ua->user->alumni;
        })->values();

        $companyAnswers = $userAnswers->filter(function($ua) {
            return $ua->user && $ua->user->company;
        })->values();

        // Helper function to fill worksheet for a single category
        $fillCategorySheet = function($sheet, $answers, $headers, $category, $periode, $type = 'alumni') {
            // Header: Basic info
            $sheet->setCellValue('A1', 'Periode Kuesioner');
            $sheet->setCellValue('B1', $periode->periode_name);
            $sheet->setCellValue('A2', 'Tanggal Mulai');
            $sheet->setCellValue('B2', $periode->start_date);
            $sheet->setCellValue('A3', 'Tanggal Selesai');
            $sheet->setCellValue('B3', $periode->end_date);

            $headerRow = 5;
            // Set static headers
            $staticHeaders = $headers;
            if ($type === 'company') {
                // Tambahkan kolom NIM Alumni dan Nama Alumni untuk perusahaan
                $staticHeaders = array_merge(
                    array_slice($headers, 0, 4),
                    ['NIM Alumni yang dinilai', 'Nama Alumni yang dinilai'],
                    array_slice($headers, 4)
                );
            }
            foreach ($staticHeaders as $col => $header) {
                $colLetter = Coordinate::stringFromColumnIndex($col + 1);
                $sheet->setCellValue($colLetter . $headerRow, $header);
                $sheet->getStyle($colLetter . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // Set question headers for this category
            $col = count($staticHeaders) + 1;
            foreach ($category['questions'] as $question) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $headerRow, $question->question);
                $sheet->getStyle($colLetter . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }

            // Fill data
            $row = $headerRow + 1;
            foreach ($answers as $idx => $userAnswer) {
                $user = $userAnswer->user;
                $alumni = $user ? $user->alumni : null;
                $company = $user ? $user->company : null;

                $name = $alumni ? $alumni->name : ($company ? $company->company_name : ($user->name ?? ''));
                $tipe = $alumni ? 'Alumni' : ($company ? 'Perusahaan' : '-');
                $nimOrEmail = $alumni ? $alumni->nim : ($company ? $company->company_email : '');
                $tanggalIsi = $userAnswer->created_at ? $userAnswer->created_at->format('Y-m-d H:i') : '';

                $colIdx = 1;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $idx + 1);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $name);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tipe);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $nimOrEmail);

                if ($type === 'company') {
                    // Tampilkan NIM Alumni dan Nama Alumni yang dinilai perusahaan
                    $nimAlumni = $userAnswer->nim ?? '';
                    $namaAlumni = '';
                    if ($nimAlumni) {
                        $alumniObj = \App\Models\Tb_Alumni::where('nim', $nimAlumni)->first();
                        $namaAlumni = $alumniObj ? $alumniObj->name : '';
                    }
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $nimAlumni);
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $namaAlumni);
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tanggalIsi);

                // Get all answer items for this user answer
                $answerItems = \App\Models\Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)->get();

                // Map answers by question id
                $answersByQuestion = [];
                foreach ($answerItems as $item) {
                    if (!isset($answersByQuestion[$item->id_question])) {
                        $answersByQuestion[$item->id_question] = [];
                    }
                    $answersByQuestion[$item->id_question][] = $item;
                }

                // Fill answers for each question in this category
                $col = count($staticHeaders) + 1;
                foreach ($category['questions'] as $question) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $answerText = '';
                    $beforeText = $question->before_text ?? '';
                    $afterText = $question->after_text ?? '';
                    if (isset($answersByQuestion[$question->id_question])) {
                        $items = $answersByQuestion[$question->id_question];
                        if ($question->type === 'multiple') {
                            $answerText = collect($items)->map(function($item) use ($question, $beforeText, $afterText) {
                                $val = '';
                                $opt = null;
                                $optionBefore = '';
                                $optionAfter = '';
                                $optionValue = '';
                                if ($item->id_questions_options) {
                                    $opt = $question->options->where('id_questions_options', $item->id_questions_options)->first();
                                    if ($opt) {
                                        $optionBefore = $opt->other_before_text ?? '';
                                        $optionAfter = $opt->other_after_text ?? '';
                                        $optionValue = $opt->option ?? '';
                                    }
                                }
                                $before = $item->answer_before ?? $beforeText;
                                $after = $item->answer_after ?? $afterText;
                                if (
                                    (!empty($item->other_answer) || !empty($item->other_answer_before) || !empty($item->other_answer_after))
                                    || ($opt && $opt->is_other_option)
                                ) {
                                    $main = $item->other_answer ?? '';
                                    $beforeOther = $item->other_answer_before ?? '';
                                    $afterOther = $item->other_answer_after ?? '';
                                    // Gabungkan seperti tipe option: before (optionBefore), value (main), after (optionAfter)
                                  
                                    $val = '';
                                     // tampilkan juga answer aslinya jika ada dan berbeda dari $main
                                   
                                     if (!empty($item->answer) && $item->answer !== $main) {
                                        $val .=  $item->answer ;
                                    }
                                    if ($optionBefore !== '') {
                                        $val .= '['. $optionBefore . ' ';
                                    }
                                    $val .= $main;
                                    if ($optionAfter !== '') {
                                        $val .= ' ' . $optionAfter . ']';
                                    }
                                    
                                } else {
                                    if ($opt) {
                                        $main = $opt->option;
                                        $val = '';
                                        if ($optionBefore !== '') {
                                            $val .= $optionBefore . ' ';
                                        }
                                        $val .= $main;
                                        if ($optionAfter !== '') {
                                            $val .= ' ' . $optionAfter;
                                        }
                                        if (!empty($item->answer) && $item->answer !== $main) {
                                            $val .= ' [' . $item->answer . ']';
                                        }
                                    } else {
                                        $main = $item->answer ?? '';
                                        $val = trim($before)
                                            . (trim($before) !== '' ? ' ' : '')
                                            . $main
                                            . (trim($after) !== '' ? ' ' : '') . trim($after);
                                    }
                                }
                                return trim($val);
                            })->implode(', ');
                        } else if ($question->type === 'option') {
                            // Pilihan ganda (single choice)
                            $item = $items[0];
                            $opt = null;
                            $optionBefore = '';
                            $optionAfter = '';
                            $optionValue = '';
                            if ($item->id_questions_options) {
                                $opt = $question->options->where('id_questions_options', $item->id_questions_options)->first();
                                if ($opt) {
                                    $optionBefore = $opt->other_before_text ?? '';
                                    $optionAfter = $opt->other_after_text ?? '';
                                    $optionValue = $opt->option ?? '';
                                }
                            }
                            $before = $item->answer_before ?? $beforeText;
                            $after = $item->answer_after ?? $afterText;
                            if (
                                (!empty($item->other_answer) || !empty($item->other_answer_before) || !empty($item->other_answer_after))
                                || ($opt && $opt->is_other_option)
                            ) {
                                $main = $item->other_answer ?? '';
                                $beforeOther = $item->other_answer_before ?? '';
                                $afterOther = $item->other_answer_after ?? '';
                                // Gabungkan before_text, value, after_text untuk jawaban "lainnya"
                                $answerText = '';
                                 // tampilkan juga answer aslinya (tb_user_answer_item.answer) jika ada dan berbeda dari $main
                                if (!empty($item->answer) && $item->answer !== $main) {
                                    $answerText .=  $item->answer ;
                                }
                                // before_text (dari kolom option)
                                if ($optionBefore !== '') {
                                    $answerText .= ' ['. $optionBefore . ' ';
                                }
                                // value (jawaban lainnya)
                                $answerText .= $main;
                                // after_text (dari kolom option)
                                if ($optionAfter !== '') {
                                    $answerText .= ' ' . $optionAfter . ']';
                                }
                               
                            } else {
                                if ($opt) {
                                    $main = $opt->option;
                                    // Gabungkan before_text, value, after_text untuk jawaban utama
                                    $answerText = '';
                                    if ($optionBefore !== '') {
                                        $answerText .= $optionBefore . ' ';
                                    }
                                    $answerText .= $main;
                                    if ($optionAfter !== '') {
                                        $answerText .= ' ' . $optionAfter;
                                    }
                                    // tampilkan juga answer aslinya jika berbeda dari $main
                                    if (!empty($item->answer) && $item->answer !== $main) {
                                        $answerText .= ' [' . $item->answer . ']';
                                    }
                                } else {
                                    $main = $item->answer ?? '';
                                    $answerText = trim($before)
                                        . (trim($before) !== '' ? ' ' : '')
                                        . $main
                                        . (trim($after) !== '' ? ' ' : '') . trim($after);
                                }
                            }
                        } else {
                            // ...existing code for other types...
                            $item = $items[0];
                            $before = $item->answer_before ?? $beforeText;
                            $after = $item->answer_after ?? $afterText;
                            if (!empty($item->other_answer) || !empty($item->other_answer_before) || !empty($item->other_answer_after)) {
                                $main = $item->other_answer ?? '';
                                $before = $item->other_answer_before ?? $before;
                                $after = $item->other_answer_after ?? $after;
                                $answerText = trim($before) . (trim($before) !== '' ? ' ' : '') .
                                              $main .
                                              (trim($after) !== '' ? ' ' : '') . trim($after);
                            } else {
                                if ($question->type === 'rating') {
                                    $opt = null;
                                    if ($item->id_questions_options) {
                                        $opt = $question->options->where('id_questions_options', $item->id_questions_options)->first();
                                    }
                                    if ($opt) {
                                        $main = $opt->option;
                                    } elseif (!empty($item->answer) && !is_numeric($item->answer)) {
                                        $main = $item->answer;
                                    } elseif (is_numeric($item->answer)) {
                                        $opt = $question->options->where('option', $item->answer)->first();
                                        if (!$opt) {
                                            $opt = $question->options->where('id_questions_options', $item->answer)->first();
                                        }
                                        $main = $opt ? $opt->option : $item->answer;
                                    } else {
                                        $main = '';
                                    }
                                } elseif ($question->type === 'location') {
                                    $loc = json_decode($item->answer, true);
                                    $main = (is_array($loc) && isset($loc['display'])) ? $loc['display'] : $item->answer;
                                } elseif ($question->type==='scale'){
                                    $main = $item->answer .'/5';
                                }
                                else {
                                    $main = $item->answer;
                                }
                                $answerText = trim($before) . (trim($before) !== '' ? ' ' : '') .
                                              $main .
                                              (trim($after) !== '' ? ' ' : '') . trim($after);
                            }
                        }
                    }
                    $sheet->setCellValue($colLetter . $row, trim($answerText));
                    $col++;
                }
                $row++;
            }

            // Auto-size columns
            $maxCol = count($staticHeaders) + count($category['questions']);
            for ($c = 1; $c <= $maxCol; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        };

        // Export type: alumni or company (from query param ?type=alumni/company)
        $type = $request->get('type', 'alumni');
        if ($type === 'company') {
            foreach ($categoriesCompany as $idx => $cat) {
                $sheet = $idx === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
                $sheet->setTitle(substr($cat['category']->category_name, 0, 31));
                $fillCategorySheet($sheet, $companyAnswers, $headers, $cat, $periode, 'company');
            }
            $fileName = 'responden_perusahaan_periode_' . $periode->id_periode . '.xlsx';
        } else {
            foreach ($categoriesAlumni as $idx => $cat) {
                $sheet = $idx === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
                $sheet->setTitle(substr($cat['category']->category_name, 0, 31));
                $fillCategorySheet($sheet, $alumniAnswers, $headers, $cat, $periode, 'alumni');
            }
            $fileName = 'responden_alumni_periode_' . $periode->id_periode . '.xlsx';
        }

        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}


