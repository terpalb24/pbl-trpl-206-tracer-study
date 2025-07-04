<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tb_Periode;
use App\Models\Tb_User_Answers;
use App\Models\Tb_User_Answer_Item;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class EksportRespondenController extends Controller
{
    public function export($id_periode, Request $request)
    {
        $periode = Tb_Periode::with(['categories.questions.options'])->findOrFail($id_periode);

        // Get all user answers for this periode
        $userAnswers = Tb_User_Answers::where('id_periode', $id_periode)
            ->with(['user.alumni.studyProgram', 'user.company'])
            ->where('status', 'completed')
            ->get();

        // Prepare spreadsheet
        $spreadsheet = new Spreadsheet();

        // Prepare dynamic question headers
        $headers = [
            'No', 'Nama', 'Program Studi', 'Tipe', 'NIM/Email', 'Tanggal Isi'
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
        })->sortBy(function($ua){
            // Urutkan berdasarkan tahun lulus (yang paling dekat dengan tahun sekarang)
            $graduationYear = $ua->user->alumni->graduation_year ?? 0;
            return abs(date('Y') - $graduationYear); // Jarak dari tahun sekarang
        })->values();
    
        $companyAnswers = $userAnswers->filter(function($ua) {
            return $ua->user && $ua->user->company;
        })->values();

                $fillCategorySheet = function($sheet, $answers, $headers, $category, $periode, $type = 'alumni') {

                // Filter answers untuk hanya menampilkan yang benar-benar mengisi pertanyaan di kategori ini
                $categoryQuestionIds = $category['questions']->pluck('id_question')->toArray();
                $filteredAnswers = $answers->filter(function($userAnswer) use ($categoryQuestionIds) {
                    // Cek apakah user ini memiliki jawaban untuk pertanyaan dalam kategori ini
                    $hasAnswerInCategory = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)
                        ->whereIn('id_question', $categoryQuestionIds)
                        ->exists();
                    return $hasAnswerInCategory;
                });

                // === Tambahkan Logo ===
                $drawing = new Drawing();
                $drawing->setName('Logo Kampus');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('assets/images/polteklogo.png')); // Ganti backslash ke slash
                $drawing->setHeight(80); // Sesuaikan ukuran jika perlu
                $drawing->setCoordinates('A1'); // Letakkan di kiri atas
                $drawing->setOffsetX(10); // Jarak dari kiri cell
                $drawing->setOffsetY(5);  // Jarak dari atas cell
                $drawing->setWorksheet($sheet);

                // === Judul Utama ===
                $sheet->mergeCells('B1:H1'); // Span beberapa kolom agar judul besar
                $sheet->setCellValue('B1', 'Laporan Hasil Kuesioner Tracer Study');
                $sheet->getStyle('B1')->getFont()->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFFFFF'); // Blue background
                $sheet->getRowDimension('1')->setRowHeight(40); // Tinggikan baris judul

                // === Informasi Periode (Baris 4–9) ===
                $sheet->setCellValue('A4', 'Periode Kuesioner');
                $sheet->setCellValue('B4', $periode->periode_name);
                $sheet->setCellValue('A5', 'Tanggal Mulai');
                $sheet->setCellValue('B5', $periode->start_date);
                $sheet->setCellValue('A6', 'Tanggal Selesai');
                $sheet->setCellValue('B6', $periode->end_date);

                // === Deskripsi Periode ===
                $sheet->setCellValue('A7', 'Deskripsi');
                $sheet->setCellValue('B7', $category['category']->description ?? 'Tidak ada deskripsi');
                $sheet->getStyle('B7')->getAlignment()->setWrapText(true);
                
                // === Informasi Kategori ===
                $sheet->setCellValue('A8', 'Kategori');
                $sheet->setCellValue('B8', $category['category']->category_name);
                $sheet->getStyle('B8')->getFont()->setBold(true);
                
                // === Informasi Jumlah Responden ===
                $sheet->setCellValue('A9', 'Jumlah Responden');
                $sheet->setCellValue('B9', count($filteredAnswers));
                $sheet->getStyle('B9')->getFont()->setBold(true);

                // === Styling untuk informasi header ===
                $sheet->getStyle('A4:A9')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1F2937'));
                $sheet->getStyle('A4:A9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6'); // Light gray background
                $sheet->getStyle('B4:B9')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('374151'));
                
                // Border untuk informasi header
                $sheet->getStyle('A4:B9')->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D1D5DB'));
                
                // Set tinggi baris untuk informasi
                for ($i = 4; $i <= 9; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }

                // Header tabel dimulai dari baris ke-11 agar cukup ruang
                $headerRow = 11;

                    if ($type === 'alumni') {
                $staticHeaders = ['No', 'Nama', 'Program Studi', 'Tahun Lulus', 'Tipe', 'NIM', 'Tanggal Isi'];
            } elseif ($type === 'company') {
                $staticHeaders = [
                    'No', 'Prodi Alumni yang dinilai', 'Tahun Lulus Alumni', 'Nama Perusahaan', 'Tipe',
                    'NIM Alumni yang dinilai', 'Nama Alumni yang dinilai', 'Tanggal Isi'
                ];
            }

            foreach ($staticHeaders as $col => $header) {
                $colLetter = Coordinate::stringFromColumnIndex($col + 1);
                $sheet->setCellValue($colLetter . $headerRow, $header);
                $sheet->getStyle($colLetter . $headerRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle($colLetter . $headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('1F2937'); // Dark gray background
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($colLetter . $headerRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('374151'));
            }

            // Set question headers for this category
            $col = count($staticHeaders) + 1;
            foreach ($category['questions'] as $question) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $headerRow, $question->question);
                $sheet->getStyle($colLetter . $headerRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle($colLetter . $headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('1F2937'); // Dark gray background
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle($colLetter . $headerRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($colLetter . $headerRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('374151'));
                $col++;
            }
            
            // Set tinggi baris header
            $sheet->getRowDimension($headerRow)->setRowHeight(25);
            
         if ($type === 'company') {
            $filteredAnswers = $filteredAnswers->sortBy(function($ua) {
                // Urutkan berdasarkan tahun lulus alumni yang dinilai
                $nimAlumni = $ua->nim ?? null;
                if (!$nimAlumni) return 9999; // Jika tidak ada nim, taruh di akhir
                $alumni = \App\Models\Tb_Alumni::with('studyProgram')->where('nim', $nimAlumni)->first();
                if (!$alumni || !$alumni->graduation_year) return 9999;
                return abs(date('Y') - $alumni->graduation_year); // Jarak dari tahun sekarang
            })->values();
          } else {
            // Urutkan alumni berdasarkan tahun lulus yang paling dekat dengan tahun sekarang
            $filteredAnswers = $filteredAnswers->sortBy(function($ua) {
                $graduationYear = $ua->user->alumni->graduation_year ?? 0;
                return abs(date('Y') - $graduationYear); // Jarak dari tahun sekarang
            })->values();
          }

            // Fill data - menggunakan filteredAnswers
            $row = $headerRow + 1;
            foreach ($filteredAnswers as $idx => $userAnswer) {
                $user = $userAnswer->user;
                $alumni = $user ? $user->alumni : null;
                $company = $user ? $user->company : null;
                $prodiName = $alumni ? ($alumni->studyProgram ? $alumni->studyProgram->study_program : '-') : '-';
                $graduationYear = $alumni ? $alumni->graduation_year : '-';

                $name = $alumni ? $alumni->name : ($company ? $company->company_name : ($user->name ?? ''));
                $tipe = $alumni ? 'Alumni' : ($company ? 'Perusahaan' : '-');
                $nimOrEmail = $alumni ? $alumni->nim : ($company ? $company->company_email : '');
                $tanggalIsi = $userAnswer->created_at ? $userAnswer->created_at->format('Y-m-d H:i') : '';

                $colIdx = 1;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $idx + 1);
             if ($type === 'alumni') {
                // Alumni: No - Nama - Prodi - Tahun Lulus - Tipe - NIM - Tanggal Isi
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $name);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $prodiName);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $graduationYear);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tipe);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $nimOrEmail);
            } elseif ($type === 'company') {
                // Company: No - Prodi alumni yang dinilai - Tahun Lulus Alumni - Nama Perusahaan - Tipe - NIM Alumni - Nama Alumni - Tanggal Isi
                $nimAlumni = $userAnswer->nim ?? '';
                $namaAlumni = '';
                $prodiAlumni = '-';
                $tahunLulusAlumni = '-';
                if ($nimAlumni) {
                    $alumniObj = \App\Models\Tb_Alumni::with('studyProgram')->where('nim', $nimAlumni)->first();
                    $namaAlumni = $alumniObj ? $alumniObj->name : '';
                    $prodiAlumni = $alumniObj && $alumniObj->studyProgram ? $alumniObj->studyProgram->study_program : '-';
                    $tahunLulusAlumni = $alumniObj ? $alumniObj->graduation_year : '-';
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $prodiAlumni); // kolom 2
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tahunLulusAlumni); // kolom 3
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $name); // nama perusahaan
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tipe);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $nimAlumni);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $namaAlumni);
            }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx++) . $row, $tanggalIsi);

                // Get all answer items for this user answer
                $answerItems = Tb_User_Answer_Item::where('id_user_answer', $userAnswer->id_user_answer)->get();

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
                                   
                                    // Build value with consistent bracket logic
                                    if (!empty($item->answer) && $item->answer !== $main) {
                                        $val .= $item->answer;
                                    }
                                    $val .= '[';
                                    if ($optionBefore !== '') {
                                        $val .= $optionBefore . ' ';
                                    }
                                    $val .= $main;
                                    if ($optionAfter !== '') {
                                        $val .= ' ' . $optionAfter;
                                    }
                                    $val .= ']';
                                    
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
                                if ($optionBefore !== '' || $optionAfter !== '') {
                                    $answerText .= '[';
                                    if ($optionBefore !== '') {
                                        $answerText .= $optionBefore . ' ';
                                    }
                                    $answerText .= $main;
                                    if ($optionAfter !== '') {
                                        $answerText .= ' ' . $optionAfter;
                                    }
                                    $answerText .= ']';
                                } else {
                                    $answerText .= '[' . $main . ']';
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
            
            // Add alternating row colors for data rows
            $dataStartRow = $headerRow + 1;
            $dataEndRow = $dataStartRow + count($filteredAnswers) - 1;
            
            if ($dataEndRow >= $dataStartRow) {
                // Apply borders to all data cells
                $sheet->getStyle('A' . $dataStartRow . ':' . Coordinate::stringFromColumnIndex($maxCol) . $dataEndRow)
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('E5E7EB'));
                
                // Apply alternating row colors
                for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                    if (($row - $dataStartRow) % 2 === 1) { // Even rows (0-indexed)
                        $sheet->getStyle('A' . $row . ':' . Coordinate::stringFromColumnIndex($maxCol) . $row)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F9FAFB'); // Very light gray
                    }
                }
            }
        };

        // Tambahkan fungsi helper untuk sanitasi nama sheet
        $sanitizeSheetTitle = function($title, $index = null) {
            // Hapus karakter yang tidak diizinkan di Excel sheet names
            $title = preg_replace('/[\\\\\/\?\*\[\]:]+/', '_', $title);
            
            // Pastikan tidak kosong
            if (empty(trim($title))) {
                $title = 'Sheet_' . ($index ?? 1);
            }
            
            // Batasi hingga 31 karakter
            $title = substr($title, 0, 31);
            
            return $title;
        };

        // Array untuk melacak nama sheet yang sudah digunakan
        $usedSheetNames = [];

        // Export type: alumni or company (from query param ?type=alumni/company)
        $type = $request->get('type', 'alumni');
        if ($type === 'company') {
            foreach ($categoriesCompany as $idx => $cat) {
                $sheet = $idx === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
                
                // Sanitasi nama sheet
                $sheetTitle = $sanitizeSheetTitle($cat['category']->category_name, $idx + 1);
                
                // Pastikan nama sheet unik
                $originalTitle = $sheetTitle;
                $counter = 1;
                while (in_array($sheetTitle, $usedSheetNames)) {
                    $suffix = '_' . $counter;
                    $maxLength = 31 - strlen($suffix);
                    $sheetTitle = substr($originalTitle, 0, $maxLength) . $suffix;
                    $counter++;
                }
                $usedSheetNames[] = $sheetTitle;
                
                $sheet->setTitle($sheetTitle);
                $fillCategorySheet($sheet, $companyAnswers, $headers, $cat, $periode, 'company');
            }
            $fileName = 'responden_perusahaan_periode_' . $periode->id_periode . '.xlsx';
        } else {
            foreach ($categoriesAlumni as $idx => $cat) {
                $sheet = $idx === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
                
                // Sanitasi nama sheet
                $sheetTitle = $sanitizeSheetTitle($cat['category']->category_name, $idx + 1);
                
                // Pastikan nama sheet unik
                $originalTitle = $sheetTitle;
                $counter = 1;
                while (in_array($sheetTitle, $usedSheetNames)) {
                    $suffix = '_' . $counter;
                    $maxLength = 31 - strlen($suffix);
                    $sheetTitle = substr($originalTitle, 0, $maxLength) . $suffix;
                    $counter++;
                }
                $usedSheetNames[] = $sheetTitle;
                
                $sheet->setTitle($sheetTitle);
                $fillCategorySheet($sheet, $alumniAnswers, $headers, $cat, $periode, 'alumni');
            }
            $fileName = 'responden_alumni_periode_' . $periode->id_periode . '.xlsx';
        }

        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save(filename: $tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}


