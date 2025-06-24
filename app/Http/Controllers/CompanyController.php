<?php

namespace App\Http\Controllers;

use App\Models\Tb_Company;
use App\Models\Tb_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    // Validasi data yang diterima (tanpa company_name)
    $validated = $request->validate([
        'company_email' => 'required|email',
        'company_phone_number' => 'required|string|max:20',
        'company_address' => 'required|string|max:500',
        'Hrd_name' => 'nullable|string|max:50',
    ]);

    // Cari perusahaan berdasarkan id_company
    $company = Tb_Company::find($id_company);

    if (!$company) {
        return back()->with('error', 'Data perusahaan tidak ditemukan');
    }

    // Perbarui data perusahaan kecuali company_name
    $oldEmail = $company->company_email;
    $company->company_email = $validated['company_email'];
    $company->company_phone_number = $validated['company_phone_number'];
    $company->company_address = $validated['company_address'];
    $company->Hrd_name = isset($validated['Hrd_name']) && $validated['Hrd_name'] !== null
        ? ucwords(strtolower($validated['Hrd_name']))
        : null;

    // Jika email berubah, update  username Tb_user
    if ($oldEmail !== $validated['company_email']) {
        $user = Tb_user::where('id_user', $company->id_user)->first();
        if ($user) {
            $user->username = $validated['company_email'];
            $user->save();
        }
    }

    if (!$company->save()) {
        return back()->with('error', 'Gagal memperbarui profil perusahaan');
    }

    return redirect()->route('company.edit')->with('success', 'Profil perusahaan berhasil diperbarui');
}

    //
}
