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

        return view('admin.dashboard', compact(
            'alumniCount', 'companyCount', 'answerCount', 'statisticData',
            'graduationYearStatisticData', 'studyPrograms', 'respondedPerStudy', 'salaryPerStudy',
            'allGraduationYears', 'filterGraduationYear'
        ));
    }
    // Tampilkan semua alumni
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
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ]);

    $file = $request->file('file');
    $path = $file->getRealPath();

    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    DB::beginTransaction();

    try {
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (empty($row[0])) continue; // skip jika nim kosong

            $nim = $row[0];
            $nik = $row[1];
            $name = $row[2];
            $gender = $row[3];
            $date_of_birth = $row[4];
            $email = $row[5];
            $phone_number = $row[6];
            $ipk = $row[7];
            $address = $row[8];
            $batch = $row[9];
            $graduation_year = $row[10];
            $programStudyName = $row[11]; // nama program studi dari Excel

            // Cari id_study berdasarkan nama program studi yang mirip
            $studyProgram = Tb_study_program::whereRaw(
                'LOWER(study_program) LIKE ?', 
                ['%' . strtolower($programStudyName) . '%']
            )->first();

            if (!$studyProgram) {
                throw new \Exception("Program Studi '$programStudyName' tidak ditemukan pada baris ke-" . ($i + 1));
            }

            $id_study = $studyProgram->id_study;

            // Cek apakah user sudah ada berdasarkan username (nim)
            $user = Tb_User::where('username', $nim)->first();

            if (!$user) {
                $user = Tb_User::create([
                    'username' => $nim,
                    'password' => bcrypt($nim),
                    'role' => 2, // Alumni role
                ]);
            }

            Tb_Alumni::updateOrCreate(
                ['nim' => $nim],
                [
                    'nik' => $nik,
                    'name' => $name,
                    'gender' => $gender,
                    'date_of_birth' => $date_of_birth,
                    'email' => $email,
                    'phone_number' => $phone_number,
                    'status' => 'tidak bekerja', // status default
                    'ipk' => $ipk,
                    'address' => $address,
                    'batch' => $batch,
                    'graduation_year' => $graduation_year,
                    'id_study' => $id_study,
                    'id_user' => $user->id_user,
                ]
            );
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Data alumni berhasil diimport!');
}
public function export()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->fromArray([
        'NIM', 'NIK', 'Nama', 'Gender', 'Tanggal Lahir', 'Email', 'Nomor HP',
        'IPK', 'Alamat', 'Angkatan', 'Tahun Lulus', 'Program Studi'
    ], null, 'A1');

    // Ambil data alumni
    $alumniData = Tb_Alumni::with('studyProgram')->get();

    $rowNum = 2; // Mulai dari baris ke-2 (setelah header)
    foreach ($alumniData as $alumni) {
        $sheet->fromArray([
            $alumni->nim,
            $alumni->nik,
            $alumni->name,
            $alumni->gender,
            $alumni->date_of_birth,
            $alumni->email,
            $alumni->phone_number,
            $alumni->ipk,
            $alumni->address,
            $alumni->batch,
            $alumni->graduation_year,
            $alumni->studyProgram->study_program ?? '', // Nama program studi
        ], null, 'A' . $rowNum);

        $rowNum++;
    }

    $writer = new Xlsx($spreadsheet);

    // Simpan sementara ke file
    $fileName = 'data_alumni.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($tempFile);

    // Kembalikan response download
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}


//company index
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
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ]);

    $file = $request->file('file');
    $path = $file->getRealPath();

    DB::beginTransaction();

    try {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (empty($row[0])) continue; // skip jika nama perusahaan kosong

            $companyName = $row[0];
            $address = $row[1] ?? null;
            $email = $row[2] ?? null;
            $phoneNumber = $row[3] ?? null;

            $userId = null;

            if (!empty($email)) {
                $existingUser = Tb_User::where('username', $email)->first();

                if (!$existingUser) {
                    $newUser = Tb_User::create([
                        'username' => $email,
                        'password' => bcrypt($email),
                        'role' => 3, // role perusahaan
                    ]);
                    $userId = $newUser->id_user;
                } else {
                    $userId = $existingUser->id_user;
                }
            }

            Tb_Company::updateOrCreate(
                ['company_name' => $companyName],
                [
                    'company_address' => $address,
                    'company_email' => $email,
                    'company_phone_number' => $phoneNumber,
                    'id_user' => $userId, // Assign id_user dari Tb_User
                ]
            );
        }

        DB::commit();
        return redirect()->back()->with('success', 'Data perusahaan berhasil diimport!');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
    }
}


    public function companyExport()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->fromArray([
            'Nama Perusahaan', 'Alamat', 'Email', 'Nomor Telepon'
        ], null, 'A1');

        // Ambil data perusahaan
        $companies = Tb_Company::all();

        $rowNum = 2;
        foreach ($companies as $company) {
            $sheet->fromArray([
                $company->company_name,
                $company->company_address,
                $company->company_email,
                $company->company_phone_number,
            ], null, 'A' . $rowNum);

            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_perusahaan.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    // Download template alumni (Excel)
public function alumniTemplate()
{
    $spreadsheet = new spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Header
    $sheet->fromArray([
        'NIM', 'NIK', 'Nama', 'Gender', 'Tanggal Lahir', 'Email', 'Nomor HP',
        'IPK', 'Alamat', 'Angkatan', 'Tahun Lulus', 'Program Studi'
    ], null, 'A1');
    // Contoh data
    $sheet->fromArray([
        '12345678', '3201010101010001', 'Budi Santoso', 'pria', '1999-01-01', 'budi@email.com', '08123456789',
        '3.50', 'Jl. Merdeka No.1', '2017', '2021', 'Teknik Informatika'
    ], null, 'A2');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = 'template_alumni.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

// Download template company (Excel)
public function companyTemplate()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Header
    $sheet->fromArray([
        'Nama Perusahaan', 'Alamat', 'Email', 'Nomor Telepon'
    ], null, 'A1');
    // Contoh data
    $sheet->fromArray([
        'PT Maju Jaya', 'Jl. Sudirman No.10', 'majujaya@email.com', '0211234567'
    ], null, 'A2');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = 'template_company.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

    //
}
