<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_jobhistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EksportJobhistory extends Controller
{
    public function exportJobHistory()
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Fetch job history data with relations, sorted by study program
        $jobHistories = Tb_jobhistory::with(['alumni.studyProgram', 'company'])
            ->whereHas('alumni')
            ->get()
            ->sortBy(function ($history) {
                return $history->alumni->studyProgram->study_program ?? 'Z'; // Z untuk data kosong agar berada di akhir
            });

        // Separate data based on duration
        $stillWorking = $jobHistories->filter(function ($history) {
            return $history->duration === 'Masih bekerja';
        });
        
        $historyWorking = $jobHistories->filter(function ($history) {
            return $history->duration !== 'Masih bekerja';
        });

        // Create first sheet for "Masih Bekerja"
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Masih Bekerja');
        $this->createSheet($sheet1, $stillWorking, 'Masih Bekerja');

        // Create second sheet for "History/Sudah Selesai"
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('History Pekerjaan');
        $this->createSheet($sheet2, $historyWorking, 'History Pekerjaan');

        // Create writer and prepare response
        $writer = new Xlsx($spreadsheet);
        $filename = 'job_history_alumni_separated_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file to PHP output
        $writer->save('php://output');
        exit;
    }

    private function formatSalaryRange($salary)
    {
        if (!$salary) return '-';
        
        $salaryRanges = [
            '3000000' => '3.000.000 - 4.500.000',
            '4500000' => '4.500.000 - 5.000.000',
            '5000000' => '5.000.000 - 5.500.000',
            '6000000' => '6.000.000 - 6.500.000',
            '6500000' => '6.500.000 - 7.000.000',
            '7000000' => '7.000.000 - 8.000.000',
            '8000000' => '8.000.000 - 9.000.000',
            '9000000' => '9.000.000 - 10.000.000',
            '10000000' => '10.000.000 - 12.000.000',
            '12000000' => '12.000.000 - 15.000.000',
            '15000000' => '15.000.000 - 20.000.000',
            '20000000' => '> 20.000.000'
        ];
        
        return $salaryRanges[$salary] ?? $salary;
    }

    private function createSheet($sheet, $data, $sheetType)
    {
        // === Tambahkan Logo ===
        $drawing = new Drawing();
        $drawing->setName('Logo Kampus');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('assets/images/polteklogo.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // === Judul Utama ===
        $sheet->mergeCells('B1:I1');
        $sheet->setCellValue('B1', 'Laporan Riwayat Pekerjaan Alumni');
        $sheet->getStyle('B1')->getFont()->setSize(16)->setBold(true)->setColor(new Color('000000'));
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFFFF');
        $sheet->getRowDimension('1')->setRowHeight(40);

        // === Sub Judul ===
        $sheet->mergeCells('B2:I2');
        $statusText = $sheetType === 'Masih Bekerja' ? 'Status: Masih Bekerja' : 'Status: Riwayat Pekerjaan';
        $sheet->setCellValue('B2', $statusText);
        $sheet->getStyle('B2')->getFont()->setSize(12)->setBold(true)->setColor(new Color('666666'));
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('2')->setRowHeight(25);

        // === Informasi Summary (Baris 4-6) ===
        $sheet->setCellValue('A4', 'Kategori');
        $sheet->setCellValue('B4', $sheetType);
        $sheet->setCellValue('A5', 'Total Data');
        $sheet->setCellValue('B5', count($data) . ' alumni');
        $sheet->setCellValue('A6', 'Tanggal Export');
        $sheet->setCellValue('B6', date('d-m-Y H:i:s'));

        // === Styling untuk informasi header ===
        $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setColor(new Color('1F2937'));
        $sheet->getStyle('A4:A6')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle('B4:B6')->getFont()->setColor(new Color('374151'));
        
        // Border untuk informasi header
        $sheet->getStyle('A4:B6')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('D1D5DB'));
        
        // Set tinggi baris untuk informasi
        for ($i = 4; $i <= 6; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // Header tabel dimulai dari baris ke-8
        $headerRow = 8;

        // Set the headers
        $headers = ['NIM', 'Nama Alumni', 'Program Studi', 'Perusahaan', 'Posisi', 'Gaji', 'Durasi', 'Tanggal Mulai', 'Tanggal Selesai'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        
        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . $headerRow, $header);
        }

        // Style the header row with different colors for each sheet
        $headerColor = $sheetType === 'Masih Bekerja' ? '4CAF50' : '2196F3'; // Green for still working, Blue for history
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF', // White text
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => $headerColor,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ];
        $sheet->getStyle('A' . $headerRow . ':I' . $headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // Current row for data
        $row = $headerRow + 1;

        foreach ($data as $history) {
            $sheet->setCellValue('A' . $row, $history->nim);
            $sheet->setCellValue('B' . $row, $history->alumni ? $history->alumni->name : '-');
            $sheet->setCellValue('C' . $row, $history->alumni && $history->alumni->studyProgram ? $history->alumni->studyProgram->study_program : '-');
            $sheet->setCellValue('D' . $row, $history->company ? $history->company->company_name : '-');
            $sheet->setCellValue('E' . $row, $history->position);
            $sheet->setCellValue('F' . $row, $this->formatSalaryRange($history->salary));
            $sheet->setCellValue('G' . $row, $history->duration);
            $sheet->setCellValue('H' . $row, $history->start_date ? date('d-m-Y', strtotime($history->start_date)) : '-');
            $sheet->setCellValue('I' . $row, $history->end_date ? date('d-m-Y', strtotime($history->end_date)) : '-');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data area
        if (count($data) > 0) {
            $sheet->getStyle('A' . $headerRow . ':I' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            // Add alternating row colors for better readability
            for ($i = $headerRow + 1; $i < $row; $i++) {
                if (($i - $headerRow) % 2 === 0) { // Even rows
                    $sheet->getStyle('A' . $i . ':I' . $i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F9FAFB');
                }
            }
        }

        return $sheet;
    }
}
