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
        $sheet->setCellValue('C1', 'Perusahaan');
        $sheet->setCellValue('D1', 'Posisi');
        $sheet->setCellValue('E1', 'Gaji');
        $sheet->setCellValue('F1', 'Durasi');
        $sheet->setCellValue('G1', 'Tanggal Mulai');
        $sheet->setCellValue('H1', 'Tanggal Selesai');

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
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Fetch job history data with relations
        $jobHistories = Tb_jobhistory::with(['alumni', 'company'])->get();

        // Current row
        $row = 2;

        foreach ($jobHistories as $history) {
            $sheet->setCellValue('A' . $row, $history->nim);
            $sheet->setCellValue('B' . $row, $history->alumni ? $history->alumni->name : '-');
            $sheet->setCellValue('C' . $row, $history->company ? $history->company->company_name : '-');
            $sheet->setCellValue('D' . $row, $history->position);
            $sheet->setCellValue('E' . $row, $history->salary);
            $sheet->setCellValue('F' . $row, $history->duration);
            $sheet->setCellValue('G' . $row, $history->start_date);
            $sheet->setCellValue('H' . $row, $history->end_date);
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
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
}
