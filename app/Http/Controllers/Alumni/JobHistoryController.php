<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_Company;
use App\Models\Tb_jobhistory as JobHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class JobHistoryController extends Controller
{
    use AuthorizesRequests;

   public function index()
{
    // Ambil user yang sedang login
    $user = auth()->user();

    // Ambil NIM dari user (diasumsikan user adalah alumni dan punya relasi ke tb_alumni)
    $nim = $user->alumni->nim ?? null;

    if (!$nim) {
        abort(403, 'Akses ditolak: hanya alumni yang bisa mengakses data ini.');
    }

    // Ambil data job histories berdasarkan nim
    $jobHistories = JobHistory::where('nim', $nim)->get();

    return view('alumni.job-history.index', compact('jobHistories'));
}

    public function create()
    {
        $companies = \App\Models\Tb_Company::all();
        return view('alumni.job-history.create', compact('companies'));
    }

public function store(Request $request)
{
    $request->validate([
        'id_company' => 'nullable|exists:tb_company,id_company',
        'new_company_name' => 'nullable|string|max:255',
        'position' => 'required|string|max:255',
        'salary' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date',
    ]);

    $id_company = $request->id_company;
    // Jika alumni mengisi nama perusahaan baru
    if (!$id_company && $request->filled('new_company_name')) {
        $company = \App\Models\Tb_Company::create([
            'company_name' => $request->new_company_name,
            'company_address' => null,
            'company_email' => null,
            'company_phone_number' => null,
            'id_user' => null,
        ]);
        $id_company = $company->id_company;
    }

    $startDate = \Carbon\Carbon::parse($request->start_date);
    $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfMonth() : null;

    $duration = null;
    if ($endDate) {
        $months = $startDate->diffInMonths($endDate);
        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;
        $duration = trim(
            ($years > 0 ? "$years tahun " : '') .
            ($remainingMonths > 0 ? "$remainingMonths bulan" : '')
        );
    } else {
        $duration = 'Masih bekerja';
    }

    JobHistory::create([
        'nim' => auth()->user()->alumni->nim,
        'id_company' => $id_company,
        'position' => $request->position,
        'salary' => $request->salary,
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
        'duration' => $duration,
    ]);

    return redirect()->route('alumni.job-history.index')
        ->with('success', 'Riwayat kerja berhasil ditambahkan.');
}
    public function edit($id)
    {
        $jobHistory = JobHistory::findOrFail($id);
        $companies = Tb_Company::all(); // Ambil semua perusahaan

        return view('alumni.job-history.edit', compact('jobHistory', 'companies'));
    }

  

public function update(Request $request, JobHistory $jobHistory)
{
    $request->validate([
        'id_company' => 'required|exists:tb_company,id_company',
        'position' => 'required|string|max:255',
        'salary' => 'required|string',
        'start_month' => 'required',
        'start_year' => 'required',
        'end_month' => 'nullable',
        'end_year' => 'nullable',
    ]);

    // Gabungkan bulan dan tahun jadi tanggal awal
    $start = Carbon::createFromDate($request->start_year, $request->start_month, 1);
    
    // Cek apakah user sedang bekerja
    $end = ($request->has('is_current') || !$request->end_month || !$request->end_year)
        ? null
        : Carbon::createFromDate($request->end_year, $request->end_month, 1)->endOfMonth();

    // Hitung durasi
    if ($end) {
        $months = $start->diffInMonths($end);
        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;
        $duration = trim(($years ? "$years tahun " : '') . ($remainingMonths ? "$remainingMonths bulan" : ''));
    } else {
        $duration = 'Masih bekerja';
    }

    // Simpan perubahan
    $jobHistory->update([
        'id_company' => $request->id_company,
        'position' => $request->position,
        'salary' => $request->salary,
        'start_date' => $start->format('Y-m-d'),
        'end_date' => $end ? $end->format('Y-m-d') : null,
        'duration' => $duration,
    ]);

    return redirect()->route('alumni.job-history.index')->with('success', 'Riwayat kerja berhasil diperbarui');
}



    public function destroy(JobHistory $jobHistory)
    {
        $jobHistory->delete();

        return redirect()->route('alumni.job-history.index')
            ->with('success', 'Riwayat kerja berhasil dihapus');
    }
}