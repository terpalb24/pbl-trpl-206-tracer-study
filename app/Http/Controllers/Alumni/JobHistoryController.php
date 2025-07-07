<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_Company;
use App\Models\Tb_jobhistory ;
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
    $jobHistories = Tb_jobhistory::where('nim', $nim)->get();

    return view('alumni.job-history.index', compact('jobHistories'));
}

    public function create()
    {
        $companies = Tb_Company::all();
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

    $alumni = auth()->user()->alumni;
    
    if ($request->has('is_current') && $alumni->status !== 'bekerja') {
        // Auto-update status alumni ke "bekerja" jika mencentang sedang bekerja
        $alumni->update(['status' => 'bekerja']);
        
        session()->flash('status_updated', true);
        session()->flash('old_status', $alumni->status);
    }

    $id_company = $request->id_company;
    // Jika alumni mengisi nama perusahaan baru
    if (!$id_company && $request->filled('new_company_name')) {
        $company = Tb_Company::create([
            'company_name' => strtoupper($request->new_company_name),
            'company_address' => null,
            'company_email' => null,
            'company_phone_number' => null,
            'id_user' => null,
        ]);
        $id_company = $company->id_company;
    }

    $startDate = Carbon::parse($request->start_date);
    $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfMonth() : null;

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

    Tb_jobhistory::create([
        'nim' => auth()->user()->alumni->nim,
        'id_company' => $id_company,
        'position' => $request->position,
        'salary' => $request->salary,
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
        'duration' => $duration,
    ]);

    $message = 'Riwayat kerja berhasil ditambahkan.';
    
    if (session('status_updated')) {
        $message .= ' Status profil Anda telah diperbarui otomatis menjadi "Bekerja" karena Anda sedang bekerja.';
    }

    return redirect()->route('alumni.job-history.index')
        ->with('success', $message);
}

    public function edit($id)
    {
        $jobHistory = Tb_jobhistory::findOrFail($id);
        $companies = Tb_Company::all(); // Ambil semua perusahaan

        return view('alumni.job-history.edit', compact('jobHistory', 'companies'));
    }

  

public function update(Request $request, Tb_jobhistory $jobHistory)
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

    $alumni = auth()->user()->alumni;
    
    if ($request->has('is_current') && $alumni->status !== 'bekerja') {
        // Auto-update status alumni ke "bekerja" jika mencentang sedang bekerja
        $alumni->update(['status' => 'bekerja']);
        
        session()->flash('status_updated', true);
        session()->flash('old_status', $alumni->status);
    }

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

    $message = 'Riwayat kerja berhasil diperbarui.';
    
    if (session('status_updated')) {
        $message .= ' Status profil Anda telah diperbarui otomatis menjadi "Bekerja" karena Anda sedang bekerja.';
    }

    return redirect()->route('alumni.job-history.index')
        ->with('success', $message);
}



    public function destroy(Tb_jobhistory $jobHistory)
    {
        $jobHistory->delete();

        return redirect()->route('alumni.job-history.index')
            ->with('success', 'Riwayat kerja berhasil dihapus');
    }
}