<?php

namespace App\Http\Controllers;

use App\Models\Tb_Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function dashboard()
    {
        return view('company.dashboard');
    }

    public function edit()
    {
        $id_company = session('id_company');
        $company = Tb_Company::where('id_company', $id_company)->firstOrFail();
        return view('company.edit', compact('company'));
    }
    

    public function update(Request $request)
    {
        // Ambil id_company dari sesi
        $id_company = session('id_company');
    
        if (!$id_company) {
            return redirect()->route('login')->with('error', 'Session tidak ditemukan. Silakan login kembali.');
        }
    
        // Validasi data yang diterima
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone_number' => 'required|string|max:20',
            'company_address' => 'required|string|max:500',
        ]);
    
        // Cari perusahaan berdasarkan id_company
        $company = Tb_Company::find($id_company);
    
        if (!$company) {
            return back()->with('error', 'Data perusahaan tidak ditemukan');
        }
    
        // Perbarui data perusahaan
        $company->company_name = $validated['company_name'];
        $company->company_email = $validated['company_email'];
        $company->company_phone_number = $validated['company_phone_number'];
        $company->company_address = $validated['company_address'];
    
        if (!$company->save()) {
            return back()->with('error', 'Gagal memperbarui profil perusahaan');
        }
    
        return redirect()->route('company.edit')->with('success', 'Profil perusahaan berhasil diperbarui');
    }
    
    //
}
