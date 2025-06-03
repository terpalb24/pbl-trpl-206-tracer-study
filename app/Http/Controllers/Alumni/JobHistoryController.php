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

    $jobHistories = JobHistory::all(); // Ambil semua data tanpa filter user_id
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
        'id_company' => 'required|exists:tb_company,id_company',
        'position' => 'required|string|max:255',
        'salary' => 'required|numeric',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $duration = null;
    if ($request->start_date && $request->end_date) {
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // Total bulan antara start dan end (dengan pembulatan ke atas)
        $totalMonths = ceil($start->diffInMonths($end, false));

        // Hitung tahun dan bulan
        $years = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;

        $yearsText = $years > 0 ? $years . ' tahun' : '';
        $monthsText = $months > 0 ? $months . ' bulan' : '';

        $duration = trim($yearsText . ' ' . $monthsText);
    }

    JobHistory::create([
        'nim' => auth()->user()->alumni->nim,
        'id_company' => $request->id_company,
        'position' => $request->position,
        'salary' => $request->salary,
        'duration' => $duration ?? 'Durasi tidak tersedia',
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    return redirect()->route('alumni.job-history.index')
        ->with('success', 'Riwayat kerja berhasil ditambahkan');
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
        'salary' => 'required|numeric',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // Hitung durasi jika tanggal tersedia
    $duration = null;
    if ($request->start_date && $request->end_date) {
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // Total bulan antara start dan end, dibulatkan ke atas
        $totalMonths = ceil($start->diffInMonths($end, false));

        // Hitung tahun dan bulan
        $years = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;

        $yearsText = $years > 0 ? $years . ' tahun' : '';
        $monthsText = $months > 0 ? $months . ' bulan' : '';

        $duration = trim($yearsText . ' ' . $monthsText);
    }

    $jobHistory->update([
        'id_company' => $request->id_company,
        'position' => $request->position,
        'salary' => $request->salary,
        'duration' => $duration ?? 'Durasi tidak tersedia',
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
    ]);

    return redirect()->route('alumni.job-history.index')
        ->with('success', 'Riwayat kerja berhasil diperbarui');
}


    public function destroy(JobHistory $jobHistory)
    {
        $jobHistory->delete();

        return redirect()->route('alumni.job-history.index')
            ->with('success', 'Riwayat kerja berhasil dihapus');
    }
}