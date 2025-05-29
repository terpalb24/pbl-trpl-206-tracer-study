<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tb_Company;
use App\Models\Tb_jobhistory as JobHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        $companies = \App\Models\Tb_Company::all();
        return view('alumni.job-history.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_company' => 'required|exists:tb_company,id_company',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'duration' => 'required|string|max:255',
        ]);

        JobHistory::create([
            'nim' => auth()->user()->alumni->nim,
            'id_company' => $request->id_company,
            'position' => $request->position,
            'salary' => $request->salary,
            'duration' => $request->duration,
        ]);

        return redirect()->route('alumni.job-history.index')
            ->with('success', 'Riwayat kerja berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jobHistory = JobHistory::findOrFail($id);
        $companies = \App\Models\Tb_Company::all(); // Ambil semua perusahaan

        return view('alumni.job-history.edit', compact('jobHistory', 'companies'));
    }

    public function update(Request $request, JobHistory $jobHistory)
    {
        $request->validate([
            'id_company' => 'required|exists:tb_company,id_company',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'duration' => 'required|string|max:255',
        ]);

        $jobHistory->update([
            'id_company' => $request->id_company,
            'position' => $request->position,
            'salary' => $request->salary,
            'duration' => $request->duration,
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