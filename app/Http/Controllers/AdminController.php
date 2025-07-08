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

    // Ambil filter dari request
    $studyProgram = $request->input('study_program');
    $graduationYear = $request->input('graduation_year_filter');

    // Base query alumni untuk filter dinamis
    $alumniQuery = Tb_Alumni::query();
    if ($studyProgram) {
        $alumniQuery->where('id_study', $studyProgram);
    }
    if ($graduationYear) {
        $alumniQuery->where('graduation_year', $graduationYear);
    }

    $filteredAlumni = $alumniQuery->get();

    // Statistik status alumni (bar chart)
    $statusCounts = $filteredAlumni->groupBy('status')->map->count();
    $allStatuses = ['bekerja', 'tidak bekerja', 'melanjutkan studi', 'berwiraswasta', 'sedang mencari kerja'];
    $statisticData = [];
    foreach ($allStatuses as $status) {
        $statisticData[$status] = $statusCounts[$status] ?? 0;
    }

    // Pie Chart: Distribusi Tahun Lulus Alumni (berdasarkan data terfilter)
    $graduationYearStatisticData = $filteredAlumni->groupBy('graduation_year')->map->count()->toArray();

    // Untuk dropdown filter tahun lulus
    $allGraduationYears = Tb_Alumni::select('graduation_year')->distinct()->orderBy('graduation_year', 'asc')->pluck('graduation_year')->toArray();

    // Untuk filter dan statistik kuesioner & pendapatan
    $studyPrograms = Tb_study_program::orderBy('study_program')->get();

    // Jumlah alumni mengisi kuesioner per prodi (filtered)
    $respondedQuery = Tb_Alumni::select('id_study', DB::raw('COUNT(DISTINCT tb_alumni.nim) as total'))
        ->join('tb_user_answers', function ($join) {
            $join->on('tb_alumni.id_user', '=', 'tb_user_answers.id_user')
                 ->where('tb_user_answers.status', '=', 'completed');
        });

    if ($studyProgram) {
        $respondedQuery->where('tb_alumni.id_study', $studyProgram);
    }
    if ($graduationYear) {
        $respondedQuery->where('tb_alumni.graduation_year', $graduationYear);
    }

    $respondedPerStudy = $respondedQuery
        ->groupBy('tb_alumni.id_study')
        ->pluck('total', 'id_study')
        ->toArray();

  // Distribusi Rentang Gaji Alumni
$salaryRanges = [
    '0 - 3jt' => 0,
    '3jt - 4.5jt' => 0,
    '4.5jt - 5jt' => 0,
    '5jt - 5.5jt' => 0,
    '6jt - 6.5jt' => 0,
    '6.5jt - 7jt' => 0,
    '7jt - 8jt' => 0,
    '8jt - 9jt' => 0,
    '9jt - 10jt' => 0,
    '10jt - 12jt' => 0,
    '12jt - 15jt' => 0,
    '15jt - 20jt' => 0,
    '> 20jt' => 0,
];

$salaryBaseQuery = DB::table('tb_jobhistory')
    ->join('tb_alumni', 'tb_alumni.nim', '=', 'tb_jobhistory.nim')
    ->where('tb_alumni.status', 'bekerja');

if ($studyProgram) {
    $salaryBaseQuery->where('tb_alumni.id_study', $studyProgram);
}
if ($graduationYear) {
    $salaryBaseQuery->where('tb_alumni.graduation_year', $graduationYear);
}

$salaries = $salaryBaseQuery->pluck('tb_jobhistory.salary')->map(fn($s) => (int) $s);

foreach ($salaries as $salary) {
    if ($salary < 3000000) $salaryRanges['0 - 3jt']++;
    elseif ($salary < 4500000) $salaryRanges['3jt - 4.5jt']++;
    elseif ($salary < 5000000) $salaryRanges['4.5jt - 5jt']++;
    elseif ($salary < 5500000) $salaryRanges['5jt - 5.5jt']++;
    elseif ($salary < 6500000) $salaryRanges['6jt - 6.5jt']++;
    elseif ($salary < 7000000) $salaryRanges['6.5jt - 7jt']++;
    elseif ($salary < 8000000) $salaryRanges['7jt - 8jt']++;
    elseif ($salary < 9000000) $salaryRanges['8jt - 9jt']++;
    elseif ($salary < 10000000) $salaryRanges['9jt - 10jt']++;
    elseif ($salary < 12000000) $salaryRanges['10jt - 12jt']++;
    elseif ($salary < 15000000) $salaryRanges['12jt - 15jt']++;
    elseif ($salary < 20000000) $salaryRanges['15jt - 20jt']++;
    else $salaryRanges['> 20jt']++;
}
    $salaryPerStudy = [];

    $questionnaireStats = $this->getQuestionnaireStatistics($request);

    $completedAnswersQuery = DB::table('tb_user_answers')
    ->join('tb_alumni', 'tb_user_answers.id_user', '=', 'tb_alumni.id_user')
    ->where('tb_user_answers.status', 'completed');

if ($studyProgram) {
    $completedAnswersQuery->where('tb_alumni.id_study', $studyProgram);
}


if ($graduationYear) {
    $completedAnswersQuery->where('tb_alumni.graduation_year', $graduationYear);
}

$answerCountAlumni = $completedAnswersQuery->distinct('tb_user_answers.id_user')->count();


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
        'filterGraduationYear' => $graduationYear,
        'salaryRanges' => $salaryRanges,
        'answerCountAlumni' => $answerCountAlumni,
        
    ], $questionnaireStats));
}


    // Tampilkan semua alumni\
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
        'phone_number' => [
            'required',
            'max:15',
            'regex:/^[0-9-]+$/', // Only allows numbers and dashes
            function ($attribute, $value, $fail) {
                // Check if there are consecutive dashes
                if (strpos($value, '--') !== false) {
                    $fail('Format nomor telepon tidak valid. Jangan gunakan tanda strip berurutan.');
                }
                // Check if starts or ends with dash
                if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                    $fail('Format nomor telepon tidak valid. Jangan awali atau akhiri dengan tanda strip.');
                }
            },
        ],
        'id_study' => 'required|exists:tb_study_program,id_study',
        'batch' => 'required|integer',
        'graduation_year' => 'required|integer',
        'date_of_birth' => 'required|date',
        'status' => 'required|string|max:50',
        'ipk' => 'required|numeric|between:0,4.00',
        'address' => 'required|string|max:255',
    ], [
        'phone_number.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda strip (-)',
        'phone_number.required' => 'Nomor telepon wajib diisi',
        'phone_number.max' => 'Nomor telepon maksimal 15 karakter',
    ]);

    // membuat kapital pada awal inputan nama dan gender
    $name = ucwords(strtolower($request->name));
    $gender = strtolower($request->gender);

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
        'name' => $name,
        'gender' => $gender,
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
        'phone_number' => [
            'required',
            'max:15',
            'regex:/^[0-9-]+$/', // Only allows numbers and dashes
            function ($attribute, $value, $fail) {
                // Check if there are consecutive dashes
                if (strpos($value, '--') !== false) {
                    $fail('Format nomor telepon tidak valid. Jangan gunakan tanda strip berurutan.');
                }
                // Check if starts or ends with dash
                if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                    $fail('Format nomor telepon tidak valid. Jangan awali atau akhiri dengan tanda strip.');
                }
            },
        ],
        'id_study' => 'required|exists:tb_study_program,id_study',
        'batch' => 'required|integer',
        'graduation_year' => 'required|integer',
        'date_of_birth' => 'required|date',
        'status' => 'required|string|max:50',
        'ipk' => 'required|numeric|between:0,4.00',
        'address' => 'required|string|max:255',
    ], [
        'phone_number.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda strip (-)',
        'phone_number.required' => 'Nomor telepon wajib diisi',
        'phone_number.max' => 'Nomor telepon maksimal 15 karakter',
    ]);

    $alumni = Tb_alumni::where('nim', $nim)->firstOrFail();

    // Kapitalisasi nama dan gender
    $name = ucwords(strtolower($request->name));
    $gender = strtolower($request->gender);

    // Update field (kecuali NIM dan id_user)
    $alumni->update([
        'nik' => $request->nik,
        'name' => $name,
        'gender' => $gender,
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
        // SECURITY: Validate the parameter
        if (!is_numeric($id_user) || $id_user <= 0) {
            \Log::warning('Invalid ID parameter in alumniDestroy', ['id_user' => $id_user]);
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter ID tidak valid.'
                ], 400);
            }
            return redirect()->route('admin.alumni.index')
                ->with('error', 'Parameter ID tidak valid.');
        }
        
        // SECURITY: Block any string containing 'bulk'
        if (is_string($id_user) && (strpos($id_user, 'bulk') !== false || $id_user === 'bulk-delete')) {
            \Log::error('SECURITY ALERT: bulk-related string in alumniDestroy', ['id_user' => $id_user]);
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak valid. Silakan refresh halaman dan coba lagi.'
                ], 400);
            }
            return redirect()->route('admin.alumni.index')
                ->with('error', 'Parameter tidak valid. Silakan refresh halaman dan coba lagi.');
        }
        
        // Convert to integer for extra safety
        $id_user = (int) $id_user;
        
        // Check if user exists and is alumni
        $user = Tb_User::where('id_user', $id_user)->where('role', 2)->first();
        if (!$user) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alumni tidak ditemukan.'
                ], 404);
            }
            return redirect()->route('admin.alumni.index')
                ->with('error', 'Alumni tidak ditemukan.');
        }
        
        try {
            // Delete alumni data first
            Tb_Alumni::where('id_user', $id_user)->delete();
            // Delete user data
            $user->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data alumni berhasil dihapus.'
                ]);
            }
            return redirect()->route('admin.alumni.index')
                ->with('success', 'Data alumni berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting alumni', ['id_user' => $id_user, 'error' => $e->getMessage()]);
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data alumni: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.alumni.index')
                ->with('error', 'Gagal menghapus data alumni: ' . $e->getMessage());
        }
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
        'company_phone_number' => [
            'required',
            'max:20',
            'regex:/^[0-9-]+$/', // Only allows numbers and dashes
            function ($attribute, $value, $fail) {
                // Check if there are consecutive dashes
                if (strpos($value, '--') !== false) {
                    $fail('Format nomor telepon tidak valid. Jangan gunakan tanda strip berurutan.');
                }
                // Check if starts or ends with dash
                if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                    $fail('Format nomor telepon tidak valid. Jangan awali atau akhiri dengan tanda strip.');
                }
            },
        ],
        'Hrd_name' => 'nullable|string|max:50',
    ], [
        'company_phone_number.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda strip (-)',
        'company_phone_number.required' => 'Nomor telepon wajib diisi',
        'company_phone_number.max' => 'Nomor telepon maksimal 20 karakter',
    ]);

    // Kapitalisasi nama HRD
    $hrdName = $request->Hrd_name ? ucwords(strtolower($request->Hrd_name)) : null;
    $company_name = $request->company_name ? strtoupper($request->company_name) : null;

    // Buat user baru untuk perusahaan
    $user = Tb_User::create([
        'username' => $request->company_email,
        'password' => bcrypt($request->company_email),  // password default email perusahaan
        'role' => 3, // role perusahaan
    ]);

    // Simpan data perusahaan, sertakan id_user
    Tb_company::create([
        'company_name' => $company_name,
        'company_address' => $request->company_address,
        'company_email' => $request->company_email,
        'company_phone_number' => $request->company_phone_number,
        'id_user' => $user->id_user,
        'Hrd_name' => $hrdName,
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
        'company_phone_number' => [
            'required',
            'max:15',
            'regex:/^[0-9-]+$/', // Only allows numbers and dashes
            function ($attribute, $value, $fail) {
                // Check if there are consecutive dashes
                if (strpos($value, '--') !== false) {
                    $fail('Format nomor telepon tidak valid. Jangan gunakan tanda strip berurutan.');
                }
                // Check if starts or ends with dash
                if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
                    $fail('Format nomor telepon tidak valid. Jangan awali atau akhiri dengan tanda strip.');
                }
            },
        ],
        'company_address' => 'required|string|max:255',
        'Hrd_name' => 'nullable|string|max:50',
    ], [
        'company_phone_number.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda strip (-)',
        'company_phone_number.required' => 'Nomor telepon wajib diisi',
        'company_phone_number.max' => 'Nomor telepon maksimal 15 karakter',
    ]);

    $company = Tb_company::findOrFail($id_company);

    // Kapitalisasi nama HRD
    $hrdName = $request->Hrd_name ? ucwords(strtolower($request->Hrd_name)) : null;
    $company_name = $request->company_name ? strtoupper($request->company_name) : null;

    // Cek jika data perusahaan sebelumnya kosong (hanya company_name, data lain null)
    $isIncomplete = !$company->company_address && !$company->company_email && !$company->company_phone_number && !$company->id_user;

    // Update data perusahaan
    $company->update([
        'company_name' => $company_name,
        'company_email' => $request->company_email,
        'company_phone_number' => $request->company_phone_number,
        'company_address' => $request->company_address,
        'Hrd_name' => $hrdName,
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
            ]);
        }
    }

    return redirect()->route('admin.company.index')->with('success', 'Data company berhasil diperbarui.');
}

   public function companyImport(Request $request)
    {
        set_time_limit(300);
        
        // Check if request wants JSON response
        $wantsJson = $request->expectsJson() || $request->ajax();

        try {
            // Validate the upload
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:5120' // max 5MB
            ], [
                'file.required' => 'File Excel harus diupload',
                'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
                'file.max' => 'Ukuran file tidak boleh lebih dari 5MB'
            ]);

            if (!$request->file('file')->isValid()) {
                throw new \Exception('File tidak valid atau rusak');
            }

            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            array_shift($rows); // Remove header row

            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip if name or email empty

                // Validate email
                if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Format email tidak valid");
                }

                // Kapitalisasi nama HRD
                $hrdName = isset($row[4]) && $row[4] ? ucwords(strtolower($row[4])) : null;

                $company_name = isset($row[0]) && $row[0] ? strtolower($row[0]) : null;

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
                        'company_name' => $company_name, // Kapitalisasi nama perusahaan
                        'company_email' => $row[1],
                        'company_address' => $row[2],
                        'company_phone_number' => $row[3],
                        'Hrd_name' => $hrdName // Nama HRD sudah dikapitalisasi
                    ]
                );
            }

            DB::commit();
            
            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data perusahaan berhasil diimport!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Data perusahaan berhasil diimport!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errorMessage = $e->validator->errors()->first();
            
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            return redirect()->back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage();
            
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->with('error', $errorMessage);
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
            'Nomor Telepon',
            'Nama HRD'  
        ];

        // Apply headers with styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style headers
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add data
        $row = 2;
        Tb_Company::select('company_name', 'company_email', 'company_address', 'company_phone_number', 'Hrd_name')
            ->chunk(100, function($companies) use ($sheet, &$row) {
                foreach ($companies as $company) {
                    // Kapitalisasi nama HRD saat export
                    $hrdName = $company->Hrd_name ? ucwords(strtolower($company->Hrd_name)) : '';
                    $sheet->fromArray([
                        $company->company_name,
                        $company->company_email,
                        $company->company_address,
                        $company->company_phone_number,
                        $hrdName
                    ], null, 'A' . $row);
                    $row++;
                }
            });

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
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
            'Nomor Telepon',
            'Nama HRD' 
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
            '021-1234567',
            'Budi Santoso' // Nama HRD
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
            '- Format nomor telepon: 021-1234567 atau 0812-3456-7890',
            '- Nama HRD wajib diisi jika ada, bisa kosong jika tidak diketahui',
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
    
    public function companyDestroy($id_user)
    {
        // Check if request wants JSON response
        $wantsJson = request()->expectsJson() || request()->ajax();
        
        try {
            DB::beginTransaction();
            
            // Find the company first to make sure it exists
            $company = Tb_Company::where('id_user', $id_user)->first();
            if (!$company) {
                throw new \Exception('Data perusahaan tidak ditemukan.');
            }
            
            // Delete company data first
            $company->delete();
            
            // Then delete the user account
            Tb_User::where('id_user', $id_user)->where('role', 3)->delete();
            
            DB::commit();
            
            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data perusahaan berhasil dihapus'
                ]);
            }
            
            return redirect()->route('admin.company.index')
                ->with('success', 'Data perusahaan berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data perusahaan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus data perusahaan: ' . $e->getMessage());
        }
    }

    /**
     * ✅ PERBAIKAN: Update method getQuestionnaireStatistics untuk filter per tahun
     */
    private function getQuestionnaireStatistics(Request $request)
    {
        // Filter parameters
        $selectedYear = $request->input('questionnaire_year'); // Changed from periode to year
        $selectedUserType = $request->input('questionnaire_user_type', 'all');
        $selectedCategory = $request->input('questionnaire_category');
        $selectedQuestion = $request->input('questionnaire_question');
        $selectedStudyProgram = $request->input('questionnaire_study_program');
        $selectedGraduationYear = $request->input('questionnaire_graduation_year');
        
        $availableYears = Tb_Periode::select(DB::raw('YEAR(start_date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // Set default year ke yang terbaru jika belum dipilih
        if (!$selectedYear && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }
        
        $periodsInYear = collect();
        if ($selectedYear) {
            $periodsInYear = Tb_Periode::whereYear('start_date', $selectedYear)
                ->orWhereYear('end_date', $selectedYear)
                ->orderBy('start_date')
                ->get();
        }
        
        $availableStudyPrograms = Tb_study_program::orderBy('study_program')->get();
        
        $availableGraduationYears = [];
        if ($selectedYear && $periodsInYear->count() > 0) {
            // Kombinasi dari semua periode dalam tahun tersebut
            $allGraduationYears = [];
            
            foreach ($periodsInYear as $periode) {
                if ($periode->all_alumni || $periode->target_type === 'all') {
                    $yearGraduation = Tb_Alumni::select('graduation_year')
                        ->distinct()
                        ->whereNotNull('graduation_year')
                        ->where('graduation_year', '!=', '')
                        ->orderBy('graduation_year', 'desc')
                        ->pluck('graduation_year')
                        ->toArray();
                    $allGraduationYears = array_merge($allGraduationYears, $yearGraduation);
                } elseif ($periode->target_type === 'years_ago' && !empty($periode->years_ago_list)) {
                    $currentYear = now()->year;
                    $yearGraduation = collect($periode->years_ago_list)->map(function($yearsAgo) use ($currentYear) {
                        return (string)($currentYear - $yearsAgo);
                    })->toArray();
                    $allGraduationYears = array_merge($allGraduationYears, $yearGraduation);
                } elseif ($periode->target_type === 'specific_years' && !empty($periode->target_graduation_years)) {
                    $yearGraduation = collect($periode->target_graduation_years)
                        ->map(function($year) { return (string)$year; })
                        ->toArray();
                    $allGraduationYears = array_merge($allGraduationYears, $yearGraduation);
                }
            }
            
            $availableGraduationYears = array_unique($allGraduationYears);
            sort($availableGraduationYears);
            $availableGraduationYears = array_reverse($availableGraduationYears);
        }
        
        // Get categories based on selected year and user type
        $availableCategories = collect();
        if ($selectedYear && $periodsInYear->count() > 0) {
            $periodIds = $periodsInYear->pluck('id_periode')->toArray();
            
            $categoryQuery = Tb_Category::whereIn('id_periode', $periodIds);
            
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
        $questionnaireChartData = [];
        
        if ($selectedCategory) {
            if ($selectedCategory === 'all') {
                // Jika "Semua Kategori", ambil semua questions dari semua kategori dalam tahun tersebut
                $allQuestionsResult = $this->getAllQuestionsFromAllCategoriesInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                $questionnaireChartData = [
                    'type' => 'all_questions_all_categories',
                    'period_name' => 'Tahun ' . $selectedYear,
                    'questions_data' => $allQuestionsResult['questions_data'],
                    'total_questions' => count($allQuestionsResult['questions_data']),
                    'total_categories' => $availableCategories->count(),
                    'total_responders' => $allQuestionsResult['total_responders'],
                    'user_type' => $selectedUserType,
                    'study_program_filter' => $selectedStudyProgram,
                    'graduation_year_filter' => $selectedGraduationYear
                ];
            } else {
                // Normal category selected - get questions for this category
                $availableQuestions = Tb_Questions::where('id_category', $selectedCategory)
                    ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                    ->where('status', 'visible')
                    ->orderBy('order')
                    ->get();
            }
        } else {
            // Tidak ada kategori yang dipilih, ambil semua pertanyaan dari kategori yang tersedia
            $availableQuestions = Tb_Questions::whereIn('id_category', $availableCategories->pluck('id_category'))
                ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                ->where('status', 'visible')
                ->orderBy('id_category')
                ->orderBy('order')
                ->get();
        }

        // Set default question ke "all" jika belum dipilih dan ada kategori
        if (!$selectedQuestion && $selectedCategory) {
            $selectedQuestion = 'all';
        }

        // Generate statistics data
        $questionnaireLabels = [];
        $questionnaireValues = [];
        $multipleQuestionData = [];

        if ($selectedQuestion) {
            if ($selectedQuestion === 'all') {
                if ($selectedCategory === 'all') {
                    // Handle "Semua Kategori" = semua pertanyaan dari semua kategori dalam tahun
                    $allQuestionsResult = $this->getAllQuestionsFromAllCategoriesInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                    $questionnaireChartData = [
                        'type' => 'all_questions_all_categories',
                        'period_name' => 'Tahun ' . $selectedYear,
                        'questions_data' => $allQuestionsResult['questions_data'],
                        'total_questions' => count($allQuestionsResult['questions_data']),
                        'total_categories' => $availableCategories->count(),
                        'total_responders' => $allQuestionsResult['total_responders'],
                        'user_type' => $selectedUserType,
                        'study_program_filter' => $selectedStudyProgram,
                        'graduation_year_filter' => $selectedGraduationYear
                    ];
                } else {
                    // Handle "Semua Pertanyaan" dalam kategori tertentu
                    $multipleQuestionData = $this->getAllQuestionsStatisticsInYear($selectedCategory, $selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                    $questionnaireChartData = [
                        'type' => 'multiple',
                        'category_name' => $availableCategories->where('id_category', $selectedCategory)->first()->category_name ?? 'Kategori',
                        'questions_data' => $multipleQuestionData,
                        'total_questions' => count($multipleQuestionData),
                        'total_responders' => $this->getTotalRespondersInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear),
                        'study_program_filter' => $selectedStudyProgram,
                        'graduation_year_filter' => $selectedGraduationYear
                    ];
                }
            } else {
                // Handle single question
                $singleQuestionData = $this->getSingleQuestionStatisticsInYear($selectedQuestion, $selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                $questionnaireChartData = array_merge($singleQuestionData, [
                    'total_responders' => $this->getTotalRespondersInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear),
                    'study_program_filter' => $selectedStudyProgram,
                    'graduation_year_filter' => $selectedGraduationYear
                ]);
            }
        } elseif ($selectedCategory) {
            // Jika ada kategori tapi belum ada question, otomatis tampilkan semua pertanyaan
            if ($selectedCategory === 'all') {
                $allQuestionsResult = $this->getAllQuestionsFromAllCategoriesInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                $questionnaireChartData = [
                    'type' => 'all_questions_all_categories',
                    'period_name' => 'Tahun ' . $selectedYear,
                    'questions_data' => $allQuestionsResult['questions_data'],
                    'total_questions' => count($allQuestionsResult['questions_data']),
                    'total_categories' => $availableCategories->count(),
                    'total_responders' => $allQuestionsResult['total_responders'],
                    'user_type' => $selectedUserType,
                    'study_program_filter' => $selectedStudyProgram,
                    'graduation_year_filter' => $selectedGraduationYear
                ];
            } else {
                $multipleQuestionData = $this->getAllQuestionsStatisticsInYear($selectedCategory, $selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear);
                $questionnaireChartData = [
                    'type' => 'multiple',
                    'category_name' => $availableCategories->where('id_category', $selectedCategory)->first()->category_name ?? 'Kategori',
                    'questions_data' => $multipleQuestionData,
                    'total_questions' => count($multipleQuestionData),
                    'total_responders' => $this->getTotalRespondersInYear($selectedYear, $selectedUserType, $selectedStudyProgram, $selectedGraduationYear),
                    'study_program_filter' => $selectedStudyProgram,
                    'graduation_year_filter' => $selectedGraduationYear
                ];
            }
        }

        $result = [
            'availableYears' => $availableYears, // Changed from availablePeriodes
            'availableCategories' => $availableCategories,
            'availableQuestions' => $availableQuestions,
            'availableStudyPrograms' => $availableStudyPrograms,
            'availableGraduationYears' => $availableGraduationYears,
            'selectedYear' => $selectedYear,
            'selectedUserType' => $selectedUserType,
            'selectedCategory' => $selectedCategory,
            'selectedQuestion' => $selectedQuestion,
            'selectedStudyProgram' => $selectedStudyProgram,
            'selectedGraduationYear' => $selectedGraduationYear,
            'questionnaireChartData' => $questionnaireChartData,
            'questionnaireLabels' => $questionnaireLabels,
            'questionnaireValues' => $questionnaireValues,
            'multipleQuestionData' => $multipleQuestionData,
            'periodsInYear' => $periodsInYear 
        ];
        return $result;
    }

    /**
     * ✅ NEW METHOD: Get all questions from all categories in a specific year
     */
    private function getAllQuestionsFromAllCategoriesInYear($year, $userType, $studyProgramId = null, $graduationYear = null)
    {
        // Get all periods in the year
        $periodsInYear = Tb_Periode::whereYear('start_date', $year)
            ->orWhereYear('end_date', $year)
            ->get();
        
        if ($periodsInYear->count() === 0) {
            return ['questions_data' => [], 'total_responders' => 0];
        }
        
        $periodIds = $periodsInYear->pluck('id_periode')->toArray();
        
        $categoryQuery = Tb_Category::whereIn('id_periode', $periodIds);
        
        if ($userType === 'alumni') {
            $categoryQuery->whereIn('for_type', ['alumni', 'both']);
        } elseif ($userType === 'company') {
            $categoryQuery->whereIn('for_type', ['company', 'both']);
        }
        
        $categories = $categoryQuery->orderBy('order')->get();
        $allQuestionsData = [];
        
        // \Log::info('Getting all questions from all categories in year: ' . $year . ', user type: ' . $userType . ', study program: ' . $studyProgramId . ', graduation year: ' . $graduationYear);
        
        $totalResponders = $this->getTotalRespondersInYear($year, $userType, $studyProgramId, $graduationYear);
        
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
                        $questionTotalResponses = $this->getQuestionTotalRespondersInYear($question->id_question, $year, $studyProgramId, $userType, $graduationYear);
                        
                        // Build query for answers in the specific year
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
                            AND YEAR(tua.created_at) = ?
                        ";
                        
                        $queryParams = [$question->id_question, $year];
                        
                        // Apply user type filters
                        if ($userType === 'company') {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_company 
                                WHERE tb_company.id_user = u.id_user
                            )";
                            
                            if ($studyProgramId) {
                                $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                                    AND EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.nim = tua.nim 
                                        AND tb_alumni.id_study = ?
                                    )";
                                $queryParams[] = $studyProgramId;
                            }
                            
                            if ($graduationYear) {
                                $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                                    AND EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.nim = tua.nim 
                                        AND tb_alumni.graduation_year = ?
                                    )";
                                $queryParams[] = $graduationYear;
                            }
                            
                        } elseif ($userType === 'alumni') {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_alumni 
                                WHERE tb_alumni.id_user = u.id_user
                            ) AND tua.nim IS NULL";
                            
                            if ($studyProgramId) {
                                $answersBaseQuery .= " AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user 
                                    AND tb_alumni.id_study = ?
                                )";
                                $queryParams[] = $studyProgramId;
                            }
                            
                            if ($graduationYear) {
                                $answersBaseQuery .= " AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user 
                                    AND tb_alumni.graduation_year = ?
                                )";
                                $queryParams[] = $graduationYear;
                            }
                            
                        } else {
                            // $userType === 'all'
                            $answersBaseQuery .= " AND (
                                (EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user
                                ) AND tua.nim IS NULL)
                                OR 
                                (EXISTS (
                                    SELECT 1 FROM tb_company 
                                    WHERE tb_company.id_user = u.id_user
                                ) AND tua.nim IS NOT NULL)
                            )";
                            
                            if ($studyProgramId) {
                                $answersBaseQuery .= " AND (
                                    (EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.id_user = u.id_user 
                                        AND tb_alumni.id_study = ?
                                    ) AND tua.nim IS NULL)
                                    OR 
                                    (EXISTS (
                                        SELECT 1 FROM tb_company 
                                        WHERE tb_company.id_user = u.id_user
                                    ) AND tua.nim IS NOT NULL AND EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.nim = tua.nim 
                                        AND tb_alumni.id_study = ?
                                    ))
                                )";
                                $queryParams[] = $studyProgramId;
                                $queryParams[] = $studyProgramId;
                            }
                            
                            if ($graduationYear) {
                                $answersBaseQuery .= " AND (
                                    (EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.id_user = u.id_user 
                                        AND tb_alumni.graduation_year = ?
                                    ) AND tua.nim IS NULL)
                                    OR 
                                    (EXISTS (
                                        SELECT 1 FROM tb_company 
                                        WHERE tb_company.id_user = u.id_user
                                    ) AND tua.nim IS NOT NULL AND EXISTS (
                                        SELECT 1 FROM tb_alumni 
                                        WHERE tb_alumni.nim = tua.nim 
                                        AND tb_alumni.graduation_year = ?
                                    ))
                                )";
                                $queryParams[] = $graduationYear;
                                $queryParams[] = $graduationYear;
                            }
                        }
                        
                        $answersBaseQuery .= " ORDER BY tua.created_at DESC";
                        
                        $answers = DB::select($answersBaseQuery, $queryParams);
                        
                        // Process answers
                        $answerCounts = [];
                        $labels = [];
                        $values = [];
                        $otherAnswers = [];
                        
                        if ($question->options && $question->options->count() > 0) {
                            foreach ($question->options as $option) {
                                $answerCounts[$option->id_questions_options] = [
                                    'option_text' => $option->option,
                                    'count' => 0,
                                    'is_other' => $option->is_other_option ?? false
                            ];
                            }
                            
                            foreach ($answers as $answer) {
                                $counted = false;
                                
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
                            
                            foreach ($answerCounts as $data) {
                                $labels[] = $data['option_text'];
                                $values[] = $data['count'];
                            }
                            
                        } else {
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
                        
                        $allQuestionsData[] = [
                            'question' => $question,
                            'category' => $category,
                            'labels' => $labels,
                            'values' => $values,
                            'total_responses' => $questionTotalResponses,
                            'answer_counts' => $answerCounts,
                            'other_answers' => $otherAnswers,
                            'question_type' => $question->type,
                            'has_options' => $question->options && $question->options->count() > 0
                        ];
                        
                    } catch (\Exception $e) {
                        // \Log::error('Error getting statistics for question ' . $question->id_question . ': ' . $e->getMessage());
                        
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
        
        return [
            'questions_data' => $allQuestionsData,
            'total_responders' => $totalResponders
        ];
    }

    /**
     * ✅ NEW METHOD: Get question total responders in a specific year
     */
    private function getQuestionTotalRespondersInYear($questionId, $year, $studyProgramId = null, $userType = null, $graduationYear = null)
    {
        try {
            if ($userType === 'company') {
                $answersQuery = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user_answer_item tai ON tua.id_user_answer = tai.id_user_answer
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tai.id_question = ?
                    AND tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND EXISTS (
                        SELECT 1 FROM tb_company 
                        WHERE tb_company.id_user = u.id_user
                    )
                ";
                $queryParams = [$questionId, $year];
                
                if ($studyProgramId) {
                    $answersQuery .= " AND tua.nim IS NOT NULL 
                        AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.id_study = ?
                        )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersQuery .= " AND tua.nim IS NOT NULL 
                        AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.graduation_year = ?
                        )";
                    $queryParams[] = $graduationYear;
                }
                
            } elseif ($userType === 'alumni') {
                $answersQuery = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user_answer_item tai ON tua.id_user_answer = tai.id_user_answer
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tai.id_question = ?
                    AND tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user
                    )
                    AND tua.nim IS NULL
                ";
                $queryParams = [$questionId, $year];
                
                if ($studyProgramId) {
                    $answersQuery .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.id_study = ?
                    )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersQuery .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.graduation_year = ?
                    )";
                    $queryParams[] = $graduationYear;
                }
                
            } else {
                $answersQuery = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user_answer_item tai ON tua.id_user_answer = tai.id_user_answer
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tai.id_question = ?
                    AND tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL)
                    )
                ";
                $queryParams = [$questionId, $year];
                
                if ($studyProgramId) {
                    $answersQuery .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.id_study = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.id_study = ?
                        ))
                    )";
                    $queryParams[] = $studyProgramId;
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersQuery .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.graduation_year = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.graduation_year = ?
                        ))
                    )";
                    $queryParams[] = $graduationYear;
                    $queryParams[] = $graduationYear;
                }
            }
            
            $result = DB::select($answersQuery, $queryParams);
            return $result[0]->total_responders ?? 0;
            
        } catch (\Exception $e) {
            \Log::error('Error counting total question responders in year: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NEW METHOD: Get total responders in a specific year
     */
    private function getTotalRespondersInYear($year, $userType, $studyProgramId = null, $graduationYear = null)
    {
        try {
            if ($userType === 'company') {
                $query = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND EXISTS (
                        SELECT 1 FROM tb_company 
                        WHERE tb_company.id_user = u.id_user
                    )
                    AND tua.nim IS NOT NULL
                ";
                $queryParams = [$year];
                
                if ($studyProgramId) {
                    $query .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.nim = tua.nim 
                        AND tb_alumni.id_study = ?
                    )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $query .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.nim = tua.nim 
                        AND tb_alumni.graduation_year = ?
                    )";
                    $queryParams[] = $graduationYear;
                }
                
            } elseif ($userType === 'alumni') {
                $query = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user
                    )
                    AND tua.nim IS NULL
                ";
                $queryParams = [$year];
                
                if ($studyProgramId) {
                    $query .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.id_study = ?
                    )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $query .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.graduation_year = ?
                    )";
                    $queryParams[] = $graduationYear;
                }
                
            } else {
                $query = "
                    SELECT COUNT(DISTINCT tua.id_user) as total_responders
                    FROM tb_user_answers tua
                    INNER JOIN tb_user u ON tua.id_user = u.id_user
                    WHERE tua.status = 'completed'
                    AND YEAR(tua.created_at) = ?
                    AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL)
                    )
                ";
                $queryParams = [$year];
                
                if ($studyProgramId) {
                    $query .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.id_study = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.id_study = ?
                        ))
                    )";
                    $queryParams[] = $studyProgramId;
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $query .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.graduation_year = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.graduation_year = ?
                        ))
                    )";
                    $queryParams[] = $graduationYear;
                    $queryParams[] = $graduationYear;
                }
            }
            
            $result = DB::select($query, $queryParams);
            return $result[0]->total_responders ?? 0;
            
        } catch (\Exception $e) {
            \Log::error('Error counting total responders in year: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ NEW METHOD: Get all questions statistics in a specific year for a category
     */
    private function getAllQuestionsStatisticsInYear($categoryId, $year, $userType, $studyProgramId = null, $graduationYear = null)
    {
        try {
            $questions = Tb_Questions::where('id_category', $categoryId)
                ->whereIn('type', ['option', 'multiple', 'scale', 'rating'])
                ->where('status', 'visible')
                ->with('options')
                ->orderBy('order')
                ->get();
            
            $allQuestionsData = [];
            
            foreach ($questions as $question) {
                try {
                    $questionTotalResponses = $this->getQuestionTotalRespondersInYear($question->id_question, $year, $studyProgramId, $userType, $graduationYear);
                    
                    // Build query for answers in the specific year
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
                        AND YEAR(tua.created_at) = ?
                    ";
                    
                    $queryParams = [$question->id_question, $year];
                    
                    // Apply user type filters (same logic as in getAllQuestionsFromAllCategoriesInYear)
                    if ($userType === 'company') {
                        $answersBaseQuery .= " AND EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        )";
                        
                        if ($studyProgramId) {
                            $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                                AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.nim = tua.nim 
                                    AND tb_alumni.id_study = ?
                                )";
                            $queryParams[] = $studyProgramId;
                        }
                        
                        if ($graduationYear) {
                            $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                                AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.nim = tua.nim 
                                    AND tb_alumni.graduation_year = ?
                                )";
                            $queryParams[] = $graduationYear;
                        }
                        
                    } elseif ($userType === 'alumni') {
                        $answersBaseQuery .= " AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user
                        ) AND tua.nim IS NULL";
                        
                        if ($studyProgramId) {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_alumni 
                                WHERE tb_alumni.id_user = u.id_user 
                                AND tb_alumni.id_study = ?
                            )";
                            $queryParams[] = $studyProgramId;
                        }
                        
                        if ($graduationYear) {
                            $answersBaseQuery .= " AND EXISTS (
                                SELECT 1 FROM tb_alumni 
                                WHERE tb_alumni.id_user = u.id_user 
                                AND tb_alumni.graduation_year = ?
                            )";
                            $queryParams[] = $graduationYear;
                        }
                        
                    } else {
                        // $userType === 'all'
                        $answersBaseQuery .= " AND (
                            (EXISTS (
                                SELECT 1 FROM tb_alumni 
                                WHERE tb_alumni.id_user = u.id_user
                            ) AND tua.nim IS NULL)
                            OR 
                            (EXISTS (
                                SELECT 1 FROM tb_company 
                                WHERE tb_company.id_user = u.id_user
                            ) AND tua.nim IS NOT NULL)
                        )";
                        
                        if ($studyProgramId) {
                            $answersBaseQuery .= " AND (
                                (EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user 
                                    AND tb_alumni.id_study = ?
                                ) AND tua.nim IS NULL)
                                OR 
                                (EXISTS (
                                    SELECT 1 FROM tb_company 
                                    WHERE tb_company.id_user = u.id_user
                                ) AND tua.nim IS NOT NULL AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.nim = tua.nim 
                                    AND tb_alumni.id_study = ?
                                ))
                            )";
                            $queryParams[] = $studyProgramId;
                            $queryParams[] = $studyProgramId;
                        }
                        
                        if ($graduationYear) {
                            $answersBaseQuery .= " AND (
                                (EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.id_user = u.id_user 
                                    AND tb_alumni.graduation_year = ?
                                ) AND tua.nim IS NULL)
                                OR 
                                (EXISTS (
                                    SELECT 1 FROM tb_company 
                                    WHERE tb_company.id_user = u.id_user
                                ) AND tua.nim IS NOT NULL AND EXISTS (
                                    SELECT 1 FROM tb_alumni 
                                    WHERE tb_alumni.nim = tua.nim 
                                    AND tb_alumni.graduation_year = ?
                                ))
                            )";
                            $queryParams[] = $graduationYear;
                            $queryParams[] = $graduationYear;
                        }
                    }
                    
                    $answersBaseQuery .= " ORDER BY tua.created_at DESC";
                    $answers = DB::select($answersBaseQuery, $queryParams);
                    
                    // Process answers (same logic as in getAllQuestionsFromAllCategoriesInYear)
                    $answerCounts = [];
                    $labels = [];
                    $values = [];
                    $otherAnswers = [];
                    
                    if ($question->options && $question->options->count() > 0) {
                        foreach ($question->options as $option) {
                            $answerCounts[$option->id_questions_options] = [
                                'option_text' => $option->option,
                                'count' => 0,
                                'is_other' => $option->is_other_option ?? false
                            ];
                        }
                        
                        foreach ($answers as $answer) {
                            $counted = false;
                            
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
                        
                        foreach ($answerCounts as $data) {
                            $labels[] = $data['option_text'];
                            $values[] = $data['count'];
                        }
                        
                    } else {
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
                    
                    $allQuestionsData[] = [
                        'question' => $question,
                        'labels' => $labels,
                        'values' => $values,
                        'total_responses' => $questionTotalResponses,
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
            \Log::error('Error getting all questions statistics in year: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NEW METHOD: Get single question statistics in a specific year
     */
    private function getSingleQuestionStatisticsInYear($questionId, $year, $userType, $studyProgramId = null, $graduationYear = null)
    {
        try {
            $question = Tb_Questions::with('options')->find($questionId);
            if (!$question) {
                return ['error' => 'Question not found'];
            }
            
            $questionTotalResponses = $this->getQuestionTotalRespondersInYear($questionId, $year, $studyProgramId, $userType, $graduationYear);
            
            // Build query for answers in the specific year
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
                AND YEAR(tua.created_at) = ?
            ";
            
            $queryParams = [$questionId, $year];
            
            // Apply user type filters (same logic as above)
            if ($userType === 'company') {
                $answersBaseQuery .= " AND EXISTS (
                    SELECT 1 FROM tb_company 
                    WHERE tb_company.id_user = u.id_user
                )";
                
                if ($studyProgramId) {
                    $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                        AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.id_study = ?
                        )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersBaseQuery .= " AND tua.nim IS NOT NULL 
                        AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.graduation_year = ?
                        )";
                    $queryParams[] = $graduationYear;
                }
                
            } elseif ($userType === 'alumni') {
                $answersBaseQuery .= " AND EXISTS (
                    SELECT 1 FROM tb_alumni 
                    WHERE tb_alumni.id_user = u.id_user
                ) AND tua.nim IS NULL";
                
                if ($studyProgramId) {
                    $answersBaseQuery .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.id_study = ?
                    )";
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersBaseQuery .= " AND EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user 
                        AND tb_alumni.graduation_year = ?
                    )";
                    $queryParams[] = $graduationYear;
                }
                
            } else {
                // $userType === 'all'
                $answersBaseQuery .= " AND (
                    (EXISTS (
                        SELECT 1 FROM tb_alumni 
                        WHERE tb_alumni.id_user = u.id_user
                    ) AND tua.nim IS NULL)
                    OR 
                    (EXISTS (
                        SELECT 1 FROM tb_company 
                        WHERE tb_company.id_user = u.id_user
                    ) AND tua.nim IS NOT NULL)
                )";
                
                if ($studyProgramId) {
                    $answersBaseQuery .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.id_study = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.id_study = ?
                        ))
                    )";
                    $queryParams[] = $studyProgramId;
                    $queryParams[] = $studyProgramId;
                }
                
                if ($graduationYear) {
                    $answersBaseQuery .= " AND (
                        (EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.id_user = u.id_user 
                            AND tb_alumni.graduation_year = ?
                        ) AND tua.nim IS NULL)
                        OR 
                        (EXISTS (
                            SELECT 1 FROM tb_company 
                            WHERE tb_company.id_user = u.id_user
                        ) AND tua.nim IS NOT NULL AND EXISTS (
                            SELECT 1 FROM tb_alumni 
                            WHERE tb_alumni.nim = tua.nim 
                            AND tb_alumni.graduation_year = ?
                        ))
                    )";
                    $queryParams[] = $graduationYear;
                    $queryParams[] = $graduationYear;
                }
            }
            
            $answersBaseQuery .= " ORDER BY tua.created_at DESC";
            $answers = DB::select($answersBaseQuery, $queryParams);
            
            // Process answers (same logic as above)
            $answerCounts = [];
            $labels = [];
            $values = [];
            $otherAnswers = [];
            
            if ($question->options && $question->options->count() > 0) {
                foreach ($question->options as $option) {
                    $answerCounts[$option->id_questions_options] = [
                        'option_text' => $option->option,
                        'count' => 0,
                        'is_other' => $option->is_other_option ?? false
                    ];
                }
                
                foreach ($answers as $answer) {
                    $counted = false;
                    
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
                
                foreach ($answerCounts as $data) {
                    $labels[] = $data['option_text'];
                    $values[] = $data['count'];
                }
                
            } else {
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
            
            return [
                'question' => $question,
                'labels' => $labels,
                'values' => $values,
                'total_responses' => $questionTotalResponses,
                'answer_counts' => $answerCounts,
                'other_answers' => $otherAnswers,
                'question_type' => $question->type,
                'has_options' => $question->options && $question->options->count() > 0
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error getting single question statistics in year: ' . $e->getMessage());
            return ['error' => 'Error loading question data'];
        }
    }
  public function storeStudyProgram(Request $request)
{
    $request->validate([
        'study_program' => [
            'required',
            'string',
            'max:255',
            'unique:tb_study_program,study_program',
            'regex:/^[A-Z].*$/' // First letter must be uppercase
        ],
    ], [
        'study_program.regex' => 'Nama program studi harus dimulai dengan huruf kapital (contoh: Teknik Mekatronika).',
    ]);

    Tb_study_program::create([
        'study_program' => $request->study_program,
    ]);

    return redirect()->back()->with('success', 'Program Studi berhasil ditambahkan.');
}

public function updateStudyProgram(Request $request)
{
    $request->validate([
        'id_study' => 'required|exists:tb_study_program,id_study',
        'study_program' => [
            'required',
            'string',
            'max:255',
            'unique:tb_study_program,study_program,' . $request->id_study . ',id_study',
            'regex:/^[A-Z].*$/' // First letter must be uppercase
        ],
    ], [
        'study_program.regex' => 'Nama program studi harus dimulai dengan huruf kapital (contoh: Teknik Mekatronika).',
    ]);

    $prodi =Tb_study_program ::where('id_study', $request->id_study)->first();

    if (!$prodi) {
        return back()->with('error', 'Program Studi tidak ditemukan.');
    }

    $prodi->study_program = $request->study_program;
    $prodi->save();

    return redirect()->back()->with('success', 'Program Studi berhasil diperbarui.');
}


public function deleteStudyProgramBySelect(Request $request)
{
    try {
        $request->validate([
            'id_study' => 'required|exists:tb_study_program,id_study',
        ]);

        $prodi = Tb_study_program::where('id_study', $request->id_study)->first();

        if (!$prodi) {
            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program Studi tidak ditemukan.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Program Studi tidak ditemukan.');
        }

        $prodiName = $prodi->study_program;
        $prodi->delete();

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Program Studi \"{$prodiName}\" berhasil dihapus."
            ], 200);
        }

        return redirect()->back()->with('success', "Program Studi \"{$prodiName}\" berhasil dihapus.");
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->errors()['id_study'] ?? ['ID program studi tidak valid'])
            ], 422);
        }
        return redirect()->back()->with('error', 'Data tidak valid.');
    } catch (\Exception $e) {
        \Log::error('Error deleting study program: ' . $e->getMessage());
        
        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus program studi.'
            ], 500);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus program studi.');
    }
}

public function bulkDeleteAlumni(Request $request)
{
    // Determine if this is a JSON request (for JavaScript fetch)
    $isJsonRequest = $request->expectsJson() || $request->ajax() || $request->header('Content-Type') === 'application/json';

    // Check if this is a "delete all" request
    $deleteAll = $request->input('delete_all', false);
    
    if ($deleteAll) {
        return $this->bulkDeleteAllAlumni($request, $isJsonRequest);
    }
    
    // SECURITY: Block any request containing 'bulk-delete' string or button name fields
    $allInput = $request->all();
    
    // Remove any non-essential fields that might cause issues
    unset($allInput['_token']);
    unset($allInput['_method']);
    unset($allInput['bulk_delete_action']);
    unset($allInput['delete_all']);
    
    foreach ($allInput as $key => $value) {
        if (is_string($value) && (strpos($value, 'bulk') !== false || $value === 'bulk-delete')) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request detected. Please refresh the page and try again.'
                ], 400);
            }
            return back()->with('error', 'Invalid request detected. Please refresh the page and try again.');
        }
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                if (is_string($subValue) && (strpos($subValue, 'bulk') !== false || $subValue === 'bulk-delete')) {
                    if ($isJsonRequest) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid request detected. Please refresh the page and try again.'
                        ], 400);
                    }
                    return back()->with('error', 'Invalid request detected. Please refresh the page and try again.');
                }
            }
        }
    }

    // Laravel validation untuk memastikan ids berupa array integer
    try {
        // Create a clean request with only the IDs field
        $cleanRequest = new \Illuminate\Http\Request(['ids' => $request->input('ids', [])]);
        
        $validated = $cleanRequest->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|min:1'
        ]);
        
        $validIds = $validated['ids'];
        
        // EXTRA: Filter out any non-numeric values that might have slipped through
        $validIds = array_filter($validIds, function($id) {
            return is_numeric($id) && $id > 0 && $id == (int)$id;
        });
        
        if (empty($validIds)) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid alumni IDs provided.'
                ], 400);
            }
            return back()->with('error', 'No valid alumni IDs provided.');
        }        } catch (\Illuminate\Validation\ValidationException $e) {
        if ($isJsonRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang dikirim tidak valid. Pastikan memilih alumni yang benar.',
                'errors' => $e->errors()
            ], 422);
        }
        return back()->with('error', 'Data yang dikirim tidak valid. Pastikan memilih alumni yang benar.');
    }

    // EXTRA SAFETY: Validate yang valid IDs benar-benar integer dan exist di database
    
    // Debug: Check what users exist with these IDs (any role)
    $allUsersWithIds = Tb_User::whereIn('id_user', $validIds)->get();
    
    // Check specifically for alumni (role 2)
    $existingUserIds = Tb_User::whereIn('id_user', $validIds)
        ->where('role', 2) // hanya alumni
        ->pluck('id_user')
        ->toArray();
    
    if (empty($existingUserIds)) {
        if ($isJsonRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan alumni yang valid untuk dihapus.'
            ], 404);
        }
        return back()->with('error', 'Tidak ditemukan alumni yang valid untuk dihapus.');
    }

    DB::beginTransaction();
    try {
        // Get NIMs for job history deletion
        $alumniNims = Tb_User::whereIn('tb_user.id_user', $existingUserIds)
            ->where('tb_user.role', 2)
            ->join('tb_alumni', 'tb_user.id_user', '=', 'tb_alumni.id_user')
            ->pluck('tb_alumni.nim')
            ->toArray();

        // Delete related data first to avoid foreign key constraints
        // Delete user answers and answer items
        $deletedAnswerItems = DB::table('tb_user_answer_item')
            ->whereIn('id_user_answer', function($query) use ($existingUserIds) {
                $query->select('id_user_answer')
                      ->from('tb_user_answers')
                      ->whereIn('id_user', $existingUserIds);
            })
            ->delete();

        $deletedAnswers = DB::table('tb_user_answers')
            ->whereIn('id_user', $existingUserIds)
            ->delete();

        // Delete job history using NIM
        $deletedJobHistory = 0;
        if (!empty($alumniNims)) {
            $deletedJobHistory = DB::table('tb_jobhistory')
                ->whereIn('nim', $alumniNims)
                ->delete();
        }
        
        // Hapus data dari kedua tabel dengan explicit integer binding
        $deletedAlumni = DB::table('tb_alumni')
            ->whereIn('id_user', $existingUserIds)
            ->delete();
            
        $deletedUsers = DB::table('tb_user')
            ->whereIn('id_user', $existingUserIds)
            ->where('role', 2) // extra safety untuk hanya hapus alumni
            ->delete();

        DB::commit();
        
        $successMessage = count($existingUserIds) . ' alumni berhasil dihapus.';
        
        if ($isJsonRequest) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'deleted_count' => count($existingUserIds)
            ]);
        }
        
        return redirect()->route('admin.alumni.index')
            ->with('success', $successMessage);
            
    } catch (\Exception $e) {
        DB::rollBack();

        $errorMessage = 'Terjadi kesalahan saat menghapus data alumni: ' . $e->getMessage();
        
        if ($isJsonRequest) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
        
        return back()->with('error', $errorMessage);
    }
}

/**
 * Delete all alumni data (with filters if applied)
 */
private function bulkDeleteAllAlumni(Request $request, $isJsonRequest)
{
    try {
        // Build query based on current filters (same logic as in alumniIndex)
        $query = Tb_alumni::with('studyProgram');

        // Apply same filters as in index page
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->filled('id_study')) {
            $query->where('id_study', $request->id_study);
        }

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

        // Get all alumni IDs that match the filters
        $alumniToDelete = $query->pluck('id_user')->toArray();
        
        if (empty($alumniToDelete)) {
            if ($isJsonRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada alumni yang ditemukan dengan filter yang diterapkan.'
                ], 404);
            }
            return back()->with('error', 'Tidak ada alumni yang ditemukan dengan filter yang diterapkan.');
        }

        // Get NIMs for job history deletion
        $alumniNims = Tb_alumni::whereIn('id_user', $alumniToDelete)->pluck('nim')->toArray();

        DB::beginTransaction();

        // Delete related data first to avoid foreign key constraints
        // Delete user answers and answer items
        $deletedAnswerItems = DB::table('tb_user_answer_item')
            ->whereIn('id_user_answer', function($query) use ($alumniToDelete) {
                $query->select('id_user_answer')
                      ->from('tb_user_answers')
                      ->whereIn('id_user', $alumniToDelete);
            })
            ->delete();

        $deletedAnswers = DB::table('tb_user_answers')
            ->whereIn('id_user', $alumniToDelete)
            ->delete();

        // Delete job history using NIM
        $deletedJobHistory = DB::table('tb_jobhistory')
            ->whereIn('nim', $alumniNims)
            ->delete();

        // Delete alumni data
        $deletedAlumni = DB::table('tb_alumni')
            ->whereIn('id_user', $alumniToDelete)
            ->delete();

        // Delete user accounts (only alumni users)
        $deletedUsers = DB::table('tb_user')
            ->whereIn('id_user', $alumniToDelete)
            ->where('role', 2) // Only delete alumni users
            ->delete();

        DB::commit();

        $successMessage = count($alumniToDelete) . ' alumni berhasil dihapus.';
        
        if ($isJsonRequest) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'deleted_count' => count($alumniToDelete)
            ]);
        }
        
        return redirect()->route('admin.alumni.index')
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();

        $errorMessage = 'Terjadi kesalahan saat menghapus semua data alumni: ' . $e->getMessage();
        
        if ($isJsonRequest) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
        
        return back()->with('error', $errorMessage);
    }
}
public function bulkDeleteCompany(Request $request)
{
    $isJsonRequest = $request->expectsJson() || $request->ajax() || $request->header('Content-Type') === 'application/json';

    // Jika delete_all dikirim, jalankan hapus semua
    if ($request->input('delete_all')) {
        return $this->bulkDeleteAllCompany($request, $isJsonRequest);
    }

    // Validasi input
    $validated = $request->validate([
        'ids' => 'required|array|min:1',
        'ids.*' => 'required|integer|min:1'
    ]);
    $ids = $validated['ids'];

    // Ambil id_user perusahaan yang valid
    $companyUserIds = Tb_Company::whereIn('id_user', $ids)->pluck('id_user')->toArray();

    if (empty($companyUserIds)) {
        $msg = 'Tidak ditemukan perusahaan yang valid untuk dihapus.';
        if ($isJsonRequest) {
            return response()->json(['success' => false, 'message' => $msg], 404);
        }
        return back()->with('error', $msg);
    }

    DB::beginTransaction();
    try {
        // Hapus data perusahaan
        Tb_Company::whereIn('id_user', $companyUserIds)->delete();
        // Hapus user perusahaan
        Tb_User::whereIn('id_user', $companyUserIds)->where('role', 3)->delete();

        DB::commit();
        $msg = count($companyUserIds) . ' perusahaan berhasil dihapus.';
        if ($isJsonRequest) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('admin.company.index')->with('success', $msg);
    } catch (\Exception $e) {
        DB::rollBack();
        $msg = 'Terjadi kesalahan saat menghapus data perusahaan: ' . $e->getMessage();
        if ($isJsonRequest) {
            return response()->json(['success' => false, 'message' => $msg], 500);
        }
        return back()->with('error', $msg);
    }
}

private function bulkDeleteAllCompany(Request $request, $isJsonRequest)
{
    try {
        $query = Tb_Company::query();

        // Filter pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('company_name', 'LIKE', "%{$search}%");
        }

        $companyUserIds = $query->pluck('id_user')->toArray();

        if (empty($companyUserIds)) {
            $msg = 'Tidak ada perusahaan yang ditemukan dengan filter yang diterapkan.';
            if ($isJsonRequest) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return back()->with('error', $msg);
        }

        DB::beginTransaction();
        // Hapus data perusahaan
        Tb_Company::whereIn('id_user', $companyUserIds)->delete();
        // Hapus user perusahaan
        Tb_User::whereIn('id_user', $companyUserIds)->where('role', 3)->delete();
        DB::commit();

        $msg = count($companyUserIds) . ' perusahaan berhasil dihapus.';
        if ($isJsonRequest) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('admin.company.index')->with('success', $msg);
    } catch (\Exception $e) {
        DB::rollBack();
        $msg = 'Terjadi kesalahan saat menghapus semua data perusahaan: ' . $e->getMessage();
        if ($isJsonRequest) {
            return response()->json(['success' => false, 'message' => $msg], 500);
        }
        return back()->with('error', $msg);
    }
  }
  public function import(Request $request)
    {
        set_time_limit(300);
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

                // Cek NIM sudah ada
                $existingNim = Tb_Alumni::where('nim', $row[0])->exists();
                if ($existingNim) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": NIM '" . $row[0] . "' sudah terdaftar di sistem.");
                }

                // Cek NIK sudah ada
                $existingNik = Tb_Alumni::where('nik', $row[1])->exists();
                if ($existingNik) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": NIK '" . $row[1] . "' sudah terdaftar di sistem.");
                }

                // Validate status
                $status = strtolower(trim($row[12] ?? ''));
                $validStatuses = ['bekerja', 'tidak bekerja', 'melanjutkan studi', 'berwiraswasta', 'sedang mencari kerja'];
                if (!in_array($status, $validStatuses)) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Status harus salah satu dari: " . implode(', ', $validStatuses));
                }

                // Validate gender
                $gender = strtolower(trim($row[3])); // Paksa lowercase gender
                if (!in_array($gender, ['pria', 'wanita'])) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Jenis kelamin harus 'Pria' atau 'Wanita'");
                }

                // Validate study program using case-insensitive LIKE 
                $studyProgramName = trim($row[11]);
                $studyProgram = Tb_study_program::whereRaw('LOWER(study_program) LIKE ?', ['%' . strtolower($studyProgramName) . '%'])->first();
                if (!$studyProgram) {
                    throw new \Exception("Baris ke-" . ($index + 2) . ": Program Studi '" . $studyProgramName . "' tidak ditemukan");
                }

                // Kapitalisasi nama alumni
                $name = ucwords(strtolower(trim($row[2])));

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
                        'name' => $name,
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
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data alumni berhasil diimport!'
                ]);
            }
            return redirect()->back()->with('success', 'Data alumni berhasil diimport!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
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
            'Pria',            // Jenis Kelamin
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
            '- Jenis Kelamin harus diisi dengan: Pria atau Wanita',
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


}