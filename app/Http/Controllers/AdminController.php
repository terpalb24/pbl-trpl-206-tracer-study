<?php

namespace App\Http\Controllers;

use App\Models\Tb_Alumni;
use App\Models\Tb_study_program;
use App\Models\Tb_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\Tb_Company;
use PhpOffice\PhpSpreadsheet\Style\Fill;
// ✅ TAMBAHAN: Import model yang diperlukan untuk questionnaire statistics
use App\Models\Tb_Periode;
use App\Models\Tb_Category;
use App\Models\Tb_Questions;
use App\Models\Tb_User_Answer_Item;
use App\Models\Tb_User_Answers;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $result = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM tb_alumni) AS alumni_count,
                (SELECT COUNT(*) FROM tb_company) AS company_count,
                (SELECT COUNT(*) FROM tb_user_answers WHERE `status` = 'completed') AS answer_count
        ");

        $data = $result[0];
        $alumniCount = $data->alumni_count;
        $companyCount = $data->company_count;
        $answerCount = $data->answer_count;

        // Statistik status alumni
        $statusCounts = Tb_Alumni::select('status', DB::raw('count(*) as total'))
            ->whereIn('status', [
                'bekerja', 'tidak bekerja', 'melanjutkan studi', 'berwiraswasta', 'sedang mencari kerja'
            ])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $allStatuses = [
            'bekerja', 'tidak bekerja', 'melanjutkan studi', 'berwiraswasta', 'sedang mencari kerja'
        ];
        $statisticData = [];
        foreach ($allStatuses as $status) {
            $statisticData[$status] = $statusCounts[$status] ?? 0;
        }

        // Ambil tahun lulus dari request untuk filter
        $filterGraduationYear = $request->input('graduation_year_filter');

        // Pie Chart: Distribusi Tahun Lulus Alumni (dapat difilter)
        if ($filterGraduationYear) {
            $graduationYearStatisticData = Tb_Alumni::select('graduation_year', DB::raw('count(*) as total'))
                ->where('graduation_year', $filterGraduationYear)
                ->groupBy('graduation_year')
                ->orderBy('graduation_year', 'asc')
                ->pluck('total', 'graduation_year')
                ->toArray();
        } else {
            $graduationYearStatisticData = Tb_Alumni::select('graduation_year', DB::raw('count(*) as total'))
                ->groupBy('graduation_year')
                ->orderBy('graduation_year', 'asc')
                ->pluck('total', 'graduation_year')
                ->toArray();
        }

        // Untuk dropdown filter tahun lulus
        $allGraduationYears = Tb_Alumni::select('graduation_year')->distinct()->orderBy('graduation_year', 'asc')->pluck('graduation_year')->toArray();

        // Untuk filter dan statistik kuesioner & pendapatan
        $studyPrograms = Tb_study_program::orderBy('study_program')->get();

        // Jumlah alumni mengisi kuesioner per prodi (join by id_user, lebih robust)
        $respondedPerStudy = Tb_Alumni::select('id_study', DB::raw('COUNT(DISTINCT tb_alumni.nim) as total'))
            ->join('tb_user_answers', function($join) {
                $join->on('tb_alumni.id_user', '=', 'tb_user_answers.id_user')
                     ->where('tb_user_answers.status', '=', 'completed');
            })
            ->groupBy('id_study')
            ->pluck('total', 'id_study')
            ->toArray();

        // Rata-rata pendapatan per prodi (alumni status bekerja)
        $salaryPerStudy = Tb_Alumni::select('tb_alumni.id_study', DB::raw('AVG(CAST(tb_jobhistory.salary AS UNSIGNED)) as avg_salary'))
            ->join('tb_jobhistory', 'tb_alumni.nim', '=', 'tb_jobhistory.nim')
            ->where('tb_alumni.status', 'bekerja')
            ->groupBy('tb_alumni.id_study')
            ->pluck('avg_salary', 'id_study')
            ->toArray();

        // ✅ TAMBAHAN: Questionnaire Statistics
        $questionnaireStats = $this->getQuestionnaireStatistics($request);
        
        return view('admin.dashboard', array_merge([
            'alumniCount' => $alumniCount,
            'companyCount' => $companyCount,
            'answerCount' => $answerCount,
            'statisticData' => $statisticData,
            'graduationYearStatisticData' => $graduationYearStatisticData,
            'studyPrograms' => $studyPrograms,
            'respondedPerStudy' => $respondedPerStudy,
            'salaryPerStudy' => $salaryPerStudy,
            'allGraduationYears' => $allGraduationYears,
            'filterGraduationYear' => $filterGraduationYear
        ], $questionnaireStats));
    }

    // Tampilkan semua alumni
    public function alumniIndex(Request $request)
    {
        $query = Tb_alumni::with('studyProgram');

        // Filter tahun lulus
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        // Filter program studi
        if ($request->filled('id_study')) {
            $query->where('id_study', $request->id_study);
        }

        // Search nama/nim/prodi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nim', 'LIKE', "%{$search}%")
                  ->orWhereHas('studyProgram', function ($q2) use ($search) {
                      $q2->where('study_program', 'LIKE', "%{$search}%");
                  });
            });
        }

        $alumni = $query->orderBy('name')->paginate(10)->withQueryString();

        // Data untuk filter
        $prodi = Tb_study_program::all();
        $tahunLulus = Tb_alumni::select('graduation_year')->distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

        return view('admin.alumni.index', compact('alumni', 'prodi', 'tahunLulus'));
    }

    // Form tambah alumni
    public function alumniCreate()
    {
        $prodi = Tb_study_program::all();
        return view('admin.alumni.create', compact('prodi'));
    }

    public function alumniStore(Request $request)
    {
        $request->validate([
            'nim' => 'required|unique:tb_alumni,nim',
            'nik' => 'required|unique:tb_alumni,nik|numeric',
            'name' => 'required|string|max:50',
            'gender' => 'required|in:pria,wanita',
            'email' => 'required|email|unique:tb_alumni,email',
            'phone_number' => 'required|string|max:15',
            'id_study' => 'required|exists:tb_study_program,id_study',
            'batch' => 'required|integer',
            'graduation_year' => 'required|integer',
            'date_of_birth' => 'required|date',
            'status' => 'required|string|max:50',
            'ipk' => 'required|numeric|between:0,4.00',
            'address' => 'required|string|max:255',
        ]);

        // Simpan user baru (username & password = nim)
        $user = Tb_User::create([
            'username' => $request->nim,
            'password' => bcrypt($request->nim),
            'role' => 2, // Role 2 = Alumni
        ]);

        // Simpan data alumni, sertakan id_user
        Tb_alumni::create([
            'nim' => $request->nim,
            'nik' => $request->nik,
            'name' => $request->name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'ipk' => $request->ipk,
            'address' => $request->address,
            'batch' => $request->batch,
            'graduation_year' => $request->graduation_year,
            'id_study' => $request->id_study,
            'id_user' => $user->id_user,
        ]);

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni berhasil ditambahkan.');
    }

    // Form edit alumni
    public function alumniEdit($nim)
    {
        $alumni = Tb_alumni::where('nim', $nim)->firstOrFail();
        $prodi = Tb_study_program::all();
        return view('admin.alumni.edit', compact('alumni', 'prodi'));
    }

    // Update alumni
    public function alumniUpdate(Request $request, $nim)
    {
        $request->validate([
            'nik' => 'required',
            'name' => 'required|string|max:50',
            'gender' => 'required|in:pria,wanita',
            'email' => 'required',
            'phone_number' => 'required|string|max:15',
            'id_study' => 'required|exists:tb_study_program,id_study',
            'batch' => 'required|integer',
            'graduation_year' => 'required|integer',
            'date_of_birth' => 'required|date',
            'status' => 'required|string|max:50',
            'ipk' => 'required|numeric|between:0,4.00',
            'address' => 'required|string|max:255',
        ]);

        $alumni = Tb_alumni::where('nim', $nim)->firstOrFail();

        // Update field (kecuali NIM dan id_user)
        $alumni->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'ipk' => $request->ipk,
            'address' => $request->address,
            'batch' => $request->batch,
            'graduation_year' => $request->graduation_year,
            'id_study' => $request->id_study,
        ]);

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil diperbarui.');
    }

    // Hapus alumni
    public function alumniDestroy($id_user)
    {
        Tb_User::where('id_user',$id_user)->delete();
        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        DB::beginTransaction();

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

                // Validate status
                $status = strtolower(trim($row[12] ?? ''));
                $validStatuses = ['bekerja', 'tidak bekerja', 'melanjutkan studi', 'berwiraswasta', 'sedang mencari kerja'];
                
                if (!in_array($status, $validStatuses)) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Status harus salah satu dari: " . implode(', ', $validStatuses));
                }

                // Validate gender
                $gender = strtolower(trim($row[3]));
                if (!in_array($gender, ['laki-laki', 'perempuan'])) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Jenis kelamin harus 'laki-laki' atau 'perempuan'");
                }

                // Validate study program using case-insensitive LIKE
                $studyProgramName = trim($row[11]);
                $studyProgram = Tb_study_program::whereRaw('LOWER(study_program) LIKE ?', ['%' . strtolower($studyProgramName) . '%'])->first();
                
                if (!$studyProgram) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Program Studi '" . $studyProgramName . "' tidak ditemukan");
                }

                // Create/update user
                $user = Tb_User::updateOrCreate(
                    ['username' => $row[0]],
                    [
                        'password' => bcrypt($row[0]),
                        'role' => 2
                    ]
                );

                // Create/update alumni
                Tb_Alumni::updateOrCreate(
                    ['nim' => $row[0]],
                    [
                        'id_user' => $user->id_user,
                        'nik' => $row[1],
                        'name' => $row[2],
                        'gender' => $gender,
                        'date_of_birth' => $row[4],
                        'email' => $row[5],
                        'phone_number' => $row[6],
                        'ipk' => $row[7],
                        'address' => $row[8],
                        'batch' => $row[9],
                        'graduation_year' => $row[10],
                        'id_study' => $studyProgram->id_study,
                        'status' => $status
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data alumni berhasil diimport!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'NIM',
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Email',
            'Nomor Telepon',
            'IPK',
            'Alamat',
            'Angkatan',
            'Tahun Lulus',
            'Program Studi',
            'Status'
        ];

        // Apply headers with styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style headers
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add data
        $row = 2;
        Tb_Alumni::with('studyProgram')->chunk(100, function($alumni) use ($sheet, &$row) {
            foreach ($alumni as $data) {
                $sheet->fromArray([
                    $data->nim,
                    $data->nik,
                    $data->name,
                    $data->gender,
                    $data->date_of_birth,
                    $data->email,
                    $data->phone_number,
                    $data->ipk,
                    $data->address,
                    $data->batch,
                    $data->graduation_year,
                    $data->studyProgram ? $data->studyProgram->study_program : '',
                    $data->status
                ], null, 'A' . $row);
                $row++;
            }
        });

        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_alumni_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function alumniTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'NIM',
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Email',
            'Nomor Telepon',
            'IPK',
            'Alamat',
            'Angkatan',
            'Tahun Lulus',
            'Program Studi',
            'Status'
        ];

        // Apply headers with styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style headers
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add example data
        $exampleData = [
            '12345678',              // NIM
            '1234567890123456',      // NIK
            'John Doe',              // Nama
            'laki-laki',            // Jenis Kelamin
            '2000-01-01',           // Tanggal Lahir
            'john.doe@email.com',    // Email
            '081234567890',         // No. Telepon
            '3.50',                 // IPK
            'Jl. Contoh No. 123',   // Alamat
            '2019',                 // Angkatan
            '2023',                 // Tahun Lulus
            'Teknik Informatika',   // Program Studi
            'bekerja'               // Status
        ];

        // Add example row with styling
        $sheet->fromArray([$exampleData], null, 'A2');
        $sheet->getStyle('A2:M2')->getFont()->setItalic(true);

        // Add notes
        $notes = [
            'Catatan:',
            '- NIM wajib diisi dan harus unik',
            '- Jenis Kelamin harus diisi dengan: laki-laki atau perempuan',
            '- Tanggal Lahir format: YYYY-MM-DD (contoh: 2000-01-01)',
            '- Status harus diisi dengan salah satu dari: bekerja, tidak bekerja, melanjutkan studi, berwiraswasta, atau sedang mencari kerja',
            '- Program Studi harus sesuai dengan yang ada di sistem',
            '- Email harus unik untuk setiap alumni',
            '- IPK menggunakan format desimal dengan titik (contoh: 3.50)'
        ];

        $row = 4;
        foreach ($notes as $note) {
            $sheet->setCellValue('A' . $row, $note);
            $sheet->mergeCells('A' . $row . ':M' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_alumni.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function companyIndex(Request $request)
    {
        $query = Tb_company::query();

        if ($request->has('search')){
            $search = $request->input('search');
            $query->where('company_name', 'LIKE', "%{$search}%");
        }

        // Perusahaan dengan data lengkap
        $companies = $query
            ->where(function($q) {
                $q->whereNotNull('company_address')
                  ->orWhereNotNull('company_email')
                  ->orWhereNotNull('company_phone_number');
            })
            ->orderBy('company_name')
            ->paginate(10)
            ->withQueryString();

        // Perusahaan hanya punya company_name (data lain null)
        $incompleteCompanies = Tb_Company::whereNull('company_address')
            ->whereNull('company_email')
            ->whereNull('company_phone_number')
            ->with(['jobHistories.alumni'])
            ->get();

        return view('admin.company.index', compact('companies', 'incompleteCompanies'));
    }

    public function companyCreate()
    {
        return view('admin.company.create');
    }

    public function companyStore(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100|unique:tb_company,company_name',
            'company_address' => 'required|string|max:255',
            'company_email' => 'required|email|unique:tb_company,company_email',
            'company_phone_number' => 'required|string|max:20',
        ]);

        // Buat user baru untuk perusahaan
        $user = Tb_User::create([
            'username' => $request->company_email,
            'password' => bcrypt($request->company_email),  // password default email perusahaan
            'role' => 3, // role perusahaan
        ]);

        // Simpan data perusahaan, sertakan id_user
        Tb_company::create([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_email' => $request->company_email,
            'company_phone_number' => $request->company_phone_number,
            'id_user' => $user->id_user,
        ]);

        return redirect()->route('admin.company.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function companyEdit($id_company)
    {
        $company = Tb_company::findOrFail($id_company);
        return view('admin.company.edit', compact('company'));
    }

    public function companyUpdate(Request $request, $id_company)
    {
        $request->validate([
            'company_name' => 'required|string|max:100',
            'company_email' => 'required|email|unique:tb_company,company_email,' . $id_company . ',id_company',
            'company_phone_number' => 'required|string|max:15',
            'company_address' => 'required|string|max:255',
        ]);

        $company = Tb_company::findOrFail($id_company);

        // Cek jika data perusahaan sebelumnya kosong (hanya company_name, data lain null)
        $isIncomplete = !$company->company_address && !$company->company_email && !$company->company_phone_number && !$company->id_user;

        // Update data perusahaan
        $company->update([
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'company_phone_number' => $request->company_phone_number,
            'company_address' => $request->company_address,
        ]);

        // Jika sebelumnya incomplete dan sekarang sudah ada email, buat user perusahaan
        if ($isIncomplete && $request->company_email) {
            $user = Tb_User::create([
                'username' => $request->company_email,
                'password' => bcrypt($request->company_email),
                'role' => 3,
            ]);
            $company->update([
                'id_user' => $user->id_user,
            ]);
        }

        // Update juga email di tb_user jika perlu (jika sudah ada user)
        if ($company->id_user) {
            $user = Tb_User::find($company->id_user);
            if ($user) {
                $user->update([
                    'username' => $request->company_email,
                    // password tidak diubah di sini
                ]);
            }
        }

        return redirect()->route('admin.company.index')->with('success', 'Data company berhasil diperbarui.');
    }

    public function companyDestroy($id_user)
    {
        Tb_User::where('id_user', $id_user)->delete();
        return redirect()->route('admin.company.index')->with('success', 'Data perusahaan dihapus.');
    }

    public function companyImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        DB::beginTransaction();

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            array_shift($rows); // Remove header row

            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip if name or email empty

                // Validate email
                if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Format email tidak valid");
                }

                // Create/update user
                $user = Tb_User::updateOrCreate(
                    ['username' => $row[1]], // company_email as username
                    [
                        'password' => bcrypt($row[1]), // company_email as password
                        'role' => 3
                    ]
                );

                // Create/update company
                Tb_Company::updateOrCreate(
                    ['company_email' => $row[1]], // company_email as unique identifier
                    [
                        'id_user' => $user->id_user,
                        'company_name' => $row[0],
                        'company_email' => $row[1],
                        'company_address' => $row[2],
                        'company_phone_number' => $row[3]
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data perusahaan berhasil diimport!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function companyExport()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Nama Perusahaan',
            'Email',
            'Alamat',
            'Nomor Telepon'
        ];

        // Apply headers with styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style headers
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add data
        $row = 2;
        Tb_Company::select('company_name', 'company_email', 'company_address', 'company_phone_number')
            ->chunk(100, function($companies) use ($sheet, &$row) {
                foreach ($companies as $company) {
                    $sheet->fromArray([
                        $company->company_name,
                        $company->company_email,
                        $company->company_address,
                        $company->company_phone_number
                    ], null, 'A' . $row);
                    $row++;
                }
            });

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_perusahaan_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function companyTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Nama Perusahaan',
            'Email',
            'Alamat',
            'Nomor Telepon'
        ];

        // Apply headers with styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style headers
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add example data
        $exampleData = [
            'PT Example Company',
            'company@example.com',
            'Jl. Example No. 123, Jakarta',
            '021-1234567'
        ];

        // Add example row with styling
        $sheet->fromArray([$exampleData], null, 'A2');
        $sheet->getStyle('A2:D2')->getFont()->setItalic(true);

        // Add notes
        $notes = [
            'Catatan:',
            '- Email perusahaan wajib diisi dan harus unik',
            '- Email akan digunakan sebagai username dan password untuk login',
            '- Format email harus valid (contoh: company@example.com)',
            '- Format nomor telepon: 021-1234567 atau 0812-3456-7890'
        ];

        $row = 4;
        foreach ($notes as $note) {
            $sheet->setCellValue('A' . $row, $note);
            $sheet->mergeCells("A{$row}:D{$row}");
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_perusahaan.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * ✅ PERBAIKAN: Update logic untuk "Semua Kategori" = semua pertanyaan dari semua kategori
     */
    private function getQuestionnaireStatistics(Request $request)
    {
        // Filter parameters
        $selectedPeriode = $request->input('questionnaire_periode');
        $selectedUserType = $request->input('questionnaire_user_type', 'all');
        $selectedCategory = $request->input('questionnaire_category');
        $selectedQuestion = $request->input('questionnaire_question');
        
        // Get all active periods
        $availablePeriodes = Tb_Periode::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();
        
        // Set default periode ke yang pertama jika belum dipilih
        if (!$selectedPeriode && $availablePeriodes->count() > 0) {
            $selectedPeriode = $availablePeriodes->first()->id_periode;
        }
        
        // Get categories based on selected period and user type
        $availableCategories = collect();
        if ($selectedPeriode) {
            $categoryQuery = Tb_Category::where('id_periode', $selectedPeriode);
            
            if ($selectedUserType === 'alumni') {
                $categoryQuery->whereIn('for_type', ['alumni', 'both']);
            } elseif ($selectedUserType === 'company') {
                $categoryQuery->whereIn('for_type', ['company', 'both']);
            }
            
            $availableCategories = $categoryQuery->orderBy('order')->get();
            
            // Set default kategori ke "all" jika belum dipilih
            if (!$selectedCategory && $availableCategories->count() > 0) {
                $selectedCategory = 'all';
            }
        }
        
        // Get questions based on selected category
        $availableQuestions = collect();
        if ($selectedCategory) {
            if ($selectedCategory === 'all') {
                // ✅ PERBAIKAN: Jika "Semua Kategori", ambil semua questions dari semua kategori
                $questionQuery = Tb_Questions::whereIn('id_category', $availableCategories->pluck('id_category'))
                    ->with(['category']); // Tambah relasi category untuk grouping
            } else {
                // Single category
                $questionQuery = Tb_Questions::where('id_category', $selectedCategory);
            }
            
            $availableQuestions = $questionQuery
                ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                ->where('status', 'visible')
                ->orderBy('id_category') // ✅ TAMBAHAN: Order by category dulu
                ->orderBy('order')
                ->get();
            
            // Set default question ke "all" jika belum dipilih
            if (!$selectedQuestion && $availableQuestions->count() > 0) {
                $selectedQuestion = 'all';
            }
        }
        
        // Generate statistics data
        $questionnaireChartData = [];
        $questionnaireLabels = [];
        $questionnaireValues = [];
        $multipleQuestionData = [];
        
        if ($selectedQuestion) {
            if ($selectedQuestion === 'all') {
                if ($selectedCategory === 'all') {
                    // ✅ PERBAIKAN: Handle "Semua Kategori" = semua pertanyaan dari semua kategori
                    $allQuestionsData = $this->getAllQuestionsFromAllCategories($selectedPeriode, $selectedUserType);
                    $questionnaireChartData = [
                        'type' => 'all_questions_all_categories',
                        'period_name' => $availablePeriodes->where('id_periode', $selectedPeriode)->first()->periode ?? 'Periode',
                        'questions_data' => $allQuestionsData,
                        'total_questions' => count($allQuestionsData),
                        'total_categories' => $availableCategories->count(),
                        'user_type' => $selectedUserType
                    ];
                } else {
                    // Handle "Semua Pertanyaan" dalam kategori tertentu
                    $multipleQuestionData = $this->getAllQuestionsStatistics($selectedCategory, $selectedUserType);
                    $questionnaireChartData = [
                        'type' => 'multiple',
                        'category_name' => $availableCategories->where('id_category', $selectedCategory)->first()->category_name ?? 'Kategori',
                        'questions_data' => $multipleQuestionData,
                        'total_questions' => count($multipleQuestionData)
                    ];
                }
            } else {
                // Handle single question
                // ... existing single question logic ...
            }
        }
        
        return [
            'availablePeriodes' => $availablePeriodes,
            'availableCategories' => $availableCategories,
            'availableQuestions' => $availableQuestions,
            'selectedPeriode' => $selectedPeriode,
            'selectedUserType' => $selectedUserType,
            'selectedCategory' => $selectedCategory,
            'selectedQuestion' => $selectedQuestion,
            'questionnaireChartData' => $questionnaireChartData,
            'questionnaireLabels' => $questionnaireLabels,
            'questionnaireValues' => $questionnaireValues,
            'multipleQuestionData' => $multipleQuestionData
        ];
    }

    /**
     * ✅ TAMBAHAN: Method baru untuk mendapatkan semua pertanyaan dari semua kategori
     */
    private function getAllQuestionsFromAllCategories($periodeId, $userType)
    {
        $categoryQuery = Tb_Category::where('id_periode', $periodeId);
        
        if ($userType === 'alumni') {
            $categoryQuery->whereIn('for_type', ['alumni', 'both']);
        } elseif ($userType === 'company') {
            $categoryQuery->whereIn('for_type', ['company', 'both']);
        }
        
        $categories = $categoryQuery->orderBy('order')->get();
        $allQuestionsData = [];
        
        \Log::info('Getting all questions from all categories in period: ' . $periodeId . ', user type: ' . $userType);
        
        foreach ($categories as $category) {
            try {
                $questions = Tb_Questions::where('id_category', $category->id_category)
                    ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                    ->where('status', 'visible')
                    ->with('options')
                    ->orderBy('order')
                    ->get();
                
                foreach ($questions as $question) {
                    try {
                        $answersBaseQuery = "
                            SELECT DISTINCT 
                                tai.*, 
                                tua.id_user, 
                                tua.nim,
                                tua.created_at as answer_created_at
                            FROM tb_user_answer_item tai
                            INNER JOIN tb_user_answers tua ON tai.id_user_answer = tua.id_user_answer 
                            INNER JOIN tb_user u ON tua.id_user = u.id_user
                            WHERE tai.id_question = ? 
                            AND tua.status = 'completed'
                        ";
                        
                        $queryParams = [$question->id_question];
                        
                        if ($userType === 'alumni') {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_alumni 
                                WHERE tb_alumni.id_user = u.id_user
                            ) AND tua.nim IS NULL";
                        } elseif ($userType === 'company') {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_company 
                                WHERE tb_company.id_user = u.id_user
                            ) AND tua.nim IS NOT NULL";
                        }
                        
                        $answersBaseQuery .= " ORDER BY tua.created_at DESC";
                        
                        $answers = DB::select($answersBaseQuery, $queryParams);
                        
                        // Count answers by option dengan logic yang sama
                        $answerCounts = [];
                        $labels = [];
                        $values = [];
                        $otherAnswers = [];
                        
                        if ($question->options && $question->options->count() > 0) {
                            // Initialize all options with 0 count
                            foreach ($question->options as $option) {
                                $answerCounts[$option->id_questions_options] = [
                                    'option_text' => $option->option,
                                    'count' => 0,
                                    'is_other' => $option->is_other_option ?? false
                                ];
                            }
                            
                            // ✅ GUNAKAN LOGIC YANG SAMA seperti getAllQuestionsStatistics
                            foreach ($answers as $answer) {
                                $counted = false;
                                
                                // Prioritas 1: id_questions_options
                                if (!empty($answer->id_questions_options)) {
                                    if (isset($answerCounts[$answer->id_questions_options])) {
                                        $answerCounts[$answer->id_questions_options]['count']++;
                                        $counted = true;
                                        
                                        if ($answerCounts[$answer->id_questions_options]['is_other'] && !empty($answer->other_answer)) {
                                            if (!isset($otherAnswers[$answer->id_questions_options])) {
                                                $otherAnswers[$answer->id_questions_options] = [];
                                            }
                                            $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                                        }
                                    }
                                }
                                
                                // Prioritas 2: answer text
                                if (!$counted && !empty($answer->answer)) {
                                    $option = $question->options->where('option', $answer->answer)->first();
                                    if ($option && isset($answerCounts[$option->id_questions_options])) {
                                        $answerCounts[$option->id_questions_options]['count']++;
                                        $counted = true;
                                        
                                        if ($option->is_other_option && !empty($answer->other_answer)) {
                                            if (!isset($otherAnswers[$answer->id_questions_options])) {
                                                $otherAnswers[$answer->id_questions_options] = [];
                                            }
                                            $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                                        }
                                    }
                                }
                                
                                // Prioritas 3: numeric ID dalam answer
                                if (!$counted && !empty($answer->answer) && is_numeric($answer->answer)) {
                                    $optionId = (int)$answer->answer;
                                    if (isset($answerCounts[$optionId])) {
                                        $answerCounts[$optionId]['count']++;
                                        $counted = true;
                                        
                                        if ($answerCounts[$optionId]['is_other'] && !empty($answer->other_answer)) {
                                            if (!isset($otherAnswers[$optionId])) {
                                                $otherAnswers[$optionId] = [];
                                            }
                                            $otherAnswers[$optionId][] = $answer->other_answer;
                                        }
                                    }
                                }
                                
                                // Prioritas 4: virtual option
                                if (!$counted && !empty($answer->answer)) {
                                    $answerValue = $answer->answer;
                                    if (!isset($answerCounts[$answerValue])) {
                                        $answerCounts[$answerValue] = [
                                            'option_text' => $answerValue,
                                            'count' => 0,
                                            'is_other' => false
                                        ];
                                    }
                                    $answerCounts[$answerValue]['count']++;
                                }
                            }
                            
                            // Prepare chart data for this question
                            foreach ($answerCounts as $data) {
                                $labels[] = $data['option_text'];
                                $values[] = $data['count'];
                            }
                            
                        } else {
                            // Question tanpa options - group by answer values
                            $answerGroups = [];
                            foreach ($answers as $answer) {
                                $answerValue = trim($answer->answer);
                                if (!empty($answerValue)) {
                                    if (!isset($answerGroups[$answerValue])) {
                                        $answerGroups[$answerValue] = 0;
                                    }
                                    $answerGroups[$answerValue]++;
                                }
                            }
                            
                            arsort($answerGroups);
                            
                            foreach ($answerGroups as $answerText => $count) {
                                $labels[] = $answerText;
                                $values[] = $count;
                                $answerCounts[$answerText] = [
                                    'option_text' => $answerText,
                                    'count' => $count,
                                    'is_other' => false
                                ];
                            }
                        }
                        
                        $totalResponses = array_sum($values);
                        
                        // ✅ TAMBAHAN: Include category info untuk grouping di view
                        $allQuestionsData[] = [
                            'question' => $question,
                            'category' => $category, // ✅ TAMBAHAN: Info kategori
                            'labels' => $labels,
                            'values' => $values,
                            'total_responses' => $totalResponses,
                            'answer_counts' => $answerCounts,
                            'other_answers' => $otherAnswers,
                            'question_type' => $question->type,
                            'has_options' => $question->options && $question->options->count() > 0
                        ];
                        
                    } catch (\Exception $e) {
                        \Log::error('Error getting statistics for question ' . $question->id_question . ': ' . $e->getMessage());
                        
                        $allQuestionsData[] = [
                            'question' => $question,
                            'category' => $category,
                            'labels' => [],
                            'values' => [],
                            'total_responses' => 0,
                            'error' => 'Error loading data: ' . $e->getMessage(),
                            'answer_counts' => [],
                            'other_answers' => [],
                            'question_type' => $question->type ?? 'unknown'
                        ];
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('Error getting questions for category ' . $category->id_category . ': ' . $e->getMessage());
            }
        }
        
        return $allQuestionsData;
    }
    
    /**
     * ✅ TAMBAHAN: Method untuk mendapatkan statistik semua pertanyaan dalam satu kategori
     */
    private function getAllQuestionsStatistics($categoryId, $userType)
    {
        try {
            $questions = Tb_Questions::where('id_category', $categoryId)
                ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                ->where('status', 'visible')
                ->with('options')
                ->orderBy('order')
                ->get();
            
            $allQuestionsData = [];
            
            \Log::info('Getting statistics for all questions in category: ' . $categoryId . ', user type: ' . $userType);
            
            foreach ($questions as $question) {
                try {
                    $answersBaseQuery = "
                        SELECT DISTINCT 
                            tai.*, 
                            tua.id_user, 
                            tua.nim,
                            tua.created_at as answer_created_at
                        FROM tb_user_answer_item tai
                        INNER JOIN tb_user_answers tua ON tai.id_user_answer = tua.id_user_answer 
                        INNER JOIN tb_user u ON tua.id_user = u.id_user
                        WHERE tai.id_question = ? 
                        AND tua.status = 'completed'
                    ";
                    
                    $queryParams = [$question->id_question];
                    
                    if ($userType === 'alumni') {
                        $answersBaseQuery .= " AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user
                        ) AND tua.nim IS NULL";
                    } elseif ($userType === 'company') {
                        $answersBaseQuery .= " AND EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL";
                    }
                    
                    $answersBaseQuery .= " ORDER BY tua.created_at DESC";
                    
                    $answers = DB::select($answersBaseQuery, $queryParams);
                    
                    // Count answers by option dengan logic yang sama
                    $answerCounts = [];
                    $labels = [];
                    $values = [];
                    $otherAnswers = [];
                    
                    if ($question->options && $question->options->count() > 0) {
                        // Initialize all options with 0 count
                        foreach ($question->options as $option) {
                            $answerCounts[$option->id_questions_options] = [
                                'option_text' => $option->option,
                                'count' => 0,
                                'is_other' => $option->is_other_option ?? false
                            ];
                        }
                        
                        // ✅ LOGIC SAMA seperti getAllQuestionsFromAllCategories
                        foreach ($answers as $answer) {
                            $counted = false;
                            
                            // Prioritas 1: id_questions_options
                            if (!empty($answer->id_questions_options)) {
                                if (isset($answerCounts[$answer->id_questions_options])) {
                                    $answerCounts[$answer->id_questions_options]['count']++;
                                    $counted = true;
                                    
                                    if ($answerCounts[$answer->id_questions_options]['is_other'] && !empty($answer->other_answer)) {
                                        if (!isset($otherAnswers[$answer->id_questions_options])) {
                                            $otherAnswers[$answer->id_questions_options] = [];
                                        }
                                        $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                                    }
                                }
                            }
                            
                            // Prioritas 2: answer text
                            if (!$counted && !empty($answer->answer)) {
                                $option = $question->options->where('option', $answer->answer)->first();
                                if ($option && isset($answerCounts[$option->id_questions_options])) {
                                    $answerCounts[$option->id_questions_options]['count']++;
                                    $counted = true;
                                    
                                    if ($option->is_other_option && !empty($answer->other_answer)) {
                                        if (!isset($otherAnswers[$answer->id_questions_options])) {
                                            $otherAnswers[$answer->id_questions_options] = [];
                                        }
                                        $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                                    }
                                }
                            }
                            
                            // Prioritas 3: numeric ID dalam answer
                            if (!$counted && !empty($answer->answer) && is_numeric($answer->answer)) {
                                $optionId = (int)$answer->answer;
                                if (isset($answerCounts[$optionId])) {
                                    $answerCounts[$optionId]['count']++;
                                    $counted = true;
                                    
                                    if ($answerCounts[$optionId]['is_other'] && !empty($answer->other_answer)) {
                                        if (!isset($otherAnswers[$optionId])) {
                                            $otherAnswers[$optionId] = [];
                                        }
                                        $otherAnswers[$optionId][] = $answer->other_answer;
                                    }
                                }
                            }
                            
                            // Prioritas 4: virtual option
                            if (!$counted && !empty($answer->answer)) {
                                $answerValue = $answer->answer;
                                if (!isset($answerCounts[$answerValue])) {
                                    $answerCounts[$answerValue] = [
                                        'option_text' => $answerValue,
                                        'count' => 0,
                                        'is_other' => false
                                    ];
                                }
                                $answerCounts[$answerValue]['count']++;
                            }
                        }
                        
                        // Prepare chart data for this question
                        foreach ($answerCounts as $data) {
                            $labels[] = $data['option_text'];
                            $values[] = $data['count'];
                        }
                        
                    } else {
                        // Question tanpa options - group by answer values
                        $answerGroups = [];
                        foreach ($answers as $answer) {
                            $answerValue = trim($answer->answer);
                            if (!empty($answerValue)) {
                                if (!isset($answerGroups[$answerValue])) {
                                    $answerGroups[$answerValue] = 0;
                                }
                                $answerGroups[$answerValue]++;
                            }
                        }
                        
                        arsort($answerGroups);
                        
                        foreach ($answerGroups as $answerText => $count) {
                            $labels[] = $answerText;
                            $values[] = $count;
                            $answerCounts[$answerText] = [
                                'option_text' => $answerText,
                                'count' => $count,
                                'is_other' => false
                            ];
                        }
                    }
                    
                    $totalResponses = array_sum($values);
                    
                    $allQuestionsData[] = [
                        'question' => $question,
                        'labels' => $labels,
                        'values' => $values,
                        'total_responses' => $totalResponses,
                        'answer_counts' => $answerCounts,
                        'other_answers' => $otherAnswers,
                        'question_type' => $question->type,
                        'has_options' => $question->options && $question->options->count() > 0
                    ];
                    
                } catch (\Exception $e) {
                    \Log::error('Error getting statistics for question ' . $question->id_question . ': ' . $e->getMessage());
                    
                    $allQuestionsData[] = [
                        'question' => $question,
                        'labels' => [],
                        'values' => [],
                        'total_responses' => 0,
                        'error' => 'Error loading data: ' . $e->getMessage(),
                        'answer_counts' => [],
                        'other_answers' => [],
                        'question_type' => $question->type ?? 'unknown'
                    ];
                }
            }
            
            return $allQuestionsData;
            
        } catch (\Exception $e) {
            \Log::error('Error getting all questions statistics for category ' . $categoryId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ TAMBAHAN: Method untuk handle single question statistics (yang hilang di getQuestionnaireStatistics)
     */
    private function getSingleQuestionStatistics($questionId, $selectedUserType)
    {
        try {
            $question = Tb_Questions::with('options')->find($questionId);
            
            if (!$question || !in_array($question->type, ['option', 'multiple', 'scale', 'rating'])) {
                return [
                    'type' => 'single',
                    'labels' => [],
                    'values' => [],
                    'question' => $question,
                    'total_responses' => 0,
                    'error' => 'Pertanyaan tidak valid atau tipe tidak didukung'
                ];
            }
            
            $answersBaseQuery = "
                SELECT DISTINCT 
                    tai.*, 
                    tua.id_user, 
                    tua.nim,
                    tua.created_at as answer_created_at
                FROM tb_user_answer_item tai
                INNER JOIN tb_user_answers tua ON tai.id_user_answer = tua.id_user_answer 
                INNER JOIN tb_user u ON tua.id_user = u.id_user
                WHERE tai.id_question = ? 
                AND tua.status = 'completed'
            ";
            
            $queryParams = [$questionId];
            
            // Filter by user type
            if ($selectedUserType === 'alumni') {
                $answersBaseQuery .= " AND EXISTS (
                    SELECT 1 FROM tb_alumni 
                    WHERE tb_alumni.id_user = u.id_user
                ) AND tua.nim IS NULL";
            } elseif ($selectedUserType === 'company') {
                $answersBaseQuery .= " AND EXISTS (
                    SELECT 1 FROM tb_company 
                    WHERE tb_company.id_user = u.id_user
                ) AND tua.nim IS NOT NULL";
            }
            
            $answers = DB::select($answersBaseQuery, $queryParams);
            
            // Count answers by option dengan logic yang sama
            $answerCounts = [];
            $labels = [];
            $values = [];
            $otherAnswers = [];
            
            if ($question->options && $question->options->count() > 0) {
                // Initialize all options with 0 count
                foreach ($question->options as $option) {
                    $answerCounts[$option->id_questions_options] = [
                        'option_text' => $option->option,
                        'count' => 0,
                        'is_other' => $option->is_other_option ?? false
                    ];
                }
                
                // Logic counting sama seperti method lain
                foreach ($answers as $answer) {
                    $counted = false;
                    
                    // Prioritas 1: id_questions_options
                    if (!empty($answer->id_questions_options)) {
                        if (isset($answerCounts[$answer->id_questions_options])) {
                            $answerCounts[$answer->id_questions_options]['count']++;
                            $counted = true;
                            
                            if ($answerCounts[$answer->id_questions_options]['is_other'] && !empty($answer->other_answer)) {
                                if (!isset($otherAnswers[$answer->id_questions_options])) {
                                    $otherAnswers[$answer->id_questions_options] = [];
                                }
                                $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                            }
                        }
                    }
                    
                    // Prioritas 2: answer text
                    if (!$counted && !empty($answer->answer)) {
                        $option = $question->options->where('option', $answer->answer)->first();
                        if ($option && isset($answerCounts[$option->id_questions_options])) {
                            $answerCounts[$option->id_questions_options]['count']++;
                            $counted = true;
                            
                            if ($option->is_other_option && !empty($answer->other_answer)) {
                                if (!isset($otherAnswers[$answer->id_questions_options])) {
                                    $otherAnswers[$answer->id_questions_options] = [];
                                }
                                $otherAnswers[$answer->id_questions_options][] = $answer->other_answer;
                            }
                        }
                    }
                    
                    // Prioritas 3: numeric ID dalam answer
                    if (!$counted && !empty($answer->answer) && is_numeric($answer->answer)) {
                        $optionId = (int)$answer->answer;
                        if (isset($answerCounts[$optionId])) {
                            $answerCounts[$optionId]['count']++;
                            $counted = true;
                            
                            if ($answerCounts[$optionId]['is_other'] && !empty($answer->other_answer)) {
                                if (!isset($otherAnswers[$optionId])) {
                                    $otherAnswers[$optionId] = [];
                                }
                                $otherAnswers[$optionId][] = $answer->other_answer;
                            }
                        }
                    }
                    
                    // Prioritas 4: virtual option
                    if (!$counted && !empty($answer->answer)) {
                        $answerValue = $answer->answer;
                        if (!isset($answerCounts[$answerValue])) {
                            $answerCounts[$answerValue] = [
                                'option_text' => $answerValue,
                                'count' => 0,
                                'is_other' => false
                            ];
                        }
                        $answerCounts[$answerValue]['count']++;
                    }
                }
                
                // Prepare chart data
                foreach ($answerCounts as $data) {
                    $labels[] = $data['option_text'];
                    $values[] = $data['count'];
                }
                
            } else {
                // Handle questions without options
                $answerGroups = [];
                foreach ($answers as $answer) {
                    $answerValue = trim($answer->answer);
                    if (!empty($answerValue)) {
                        if (!isset($answerGroups[$answerValue])) {
                            $answerGroups[$answerValue] = 0;
                        }
                        $answerGroups[$answerValue]++;
                    }
                }
                
                foreach ($answerGroups as $answerText => $count) {
                    $labels[] = $answerText;
                    $values[] = $count;
                    $answerCounts[$answerText] = [
                        'option_text' => $answerText,
                        'count' => $count,
                        'is_other' => false
                    ];
                }
            }
            
            return [
                'type' => 'single',
                'labels' => $labels,
                'values' => $values,
                'question' => $question,
                'total_responses' => array_sum($values),
                'answer_counts' => $answerCounts,
                'other_answers' => $otherAnswers
            ];
            
        } catch (\Exception $e) {
            \Log::error('Single question statistics query error: ' . $e->getMessage());
            
            return [
                'type' => 'single',
                'labels' => [],
                'values' => [],
                'question' => $question ?? null,
                'total_responses' => 0,
                'error' => 'Gagal memuat data statistik: ' . $e->getMessage()
            ];
        }
    }
}

