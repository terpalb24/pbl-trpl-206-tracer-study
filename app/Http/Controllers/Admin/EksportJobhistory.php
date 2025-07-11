<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_jobhistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EksportJobhistory extends Controller
{
    public function exportJobHistory()
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the headers
        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'Nama Alumni');
        $sheet->setCellValue('C1', 'Program Studi');
        $sheet->setCellValue('D1', 'Perusahaan');
        $sheet->setCellValue('E1', 'Posisi');
        $sheet->setCellValue('F1', 'Gaji');
        $sheet->setCellValue('G1', 'Durasi');
        $sheet->setCellValue('H1', 'Tanggal Mulai');
        $sheet->setCellValue('I1', 'Tanggal Selesai');

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '000000', // Warna hitam untuk teks
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E0E0E0', // Warna abu-abu muda untuk background
                ],
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Fetch job history data with relations, sorted by study program
        $jobHistories = Tb_jobhistory::with(['alumni.studyProgram', 'company'])
            ->whereHas('alumni')
            ->get()
            ->sortBy(function ($history) {
                return $history->alumni->studyProgram->study_program ?? 'Z'; // Z untuk data kosong agar berada di akhir
            });

        // Current row
        $row = 2;

        foreach ($jobHistories as $history) {
            $sheet->setCellValue('A' . $row, $history->nim);
            $sheet->setCellValue('B' . $row, $history->alumni ? $history->alumni->name : '-');
            $sheet->setCellValue('C' . $row, $history->alumni && $history->alumni->studyProgram ? $history->alumni->studyProgram->study_program : '-');
            $sheet->setCellValue('D' . $row, $history->company ? $history->company->company_name : '-');
            $sheet->setCellValue('E' . $row, $history->position);
            $sheet->setCellValue('F' . $row, $this->formatSalaryRange($history->salary));
            $sheet->setCellValue('G' . $row, $history->duration);
            $sheet->setCellValue('H' . $row, $history->start_date ? date('d-m-Y', strtotime($history->start_date)) : '-');
            $sheet->setCellValue('I' . $row, $history->end_date? date('d-m-Y', strtotime($history->end_date)) : '-');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and prepare response
        $writer = new Xlsx($spreadsheet);
        $filename = 'job_history_alumni_' . date('Y-m-d_H-i-s') . '.xlsx';
        
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
}
