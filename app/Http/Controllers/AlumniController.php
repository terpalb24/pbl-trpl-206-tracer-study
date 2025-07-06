<?php

namespace App\Http\Controllers;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tb_Alumni;
use App\Models\Tb_User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;


class AlumniController extends Controller
{
    public function dashboard()
    {
        $alumniId = session('id_user');
        $alumni = Tb_Alumni::find($alumniId);
        return view('alumni.dashboard', compact('alumni'));
    }
    
    public function showEmailForm()
{
    return view('alumni.verify_email');
}

public function sendEmailVerification(Request $request)
{
    $request->validate([
        'email' => [
            'required',
            'email:rfc,dns',
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' // Accepts all valid email domains
        ],
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.regex' => 'Format email tidak valid. Gunakan format email yang benar (contoh: nama@domain.com).'
    ]);

    $user = Auth::user();
    $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();

    if (!$alumni) {
        return back()->with('error', 'Data alumni tidak ditemukan.');
    }

    // Buat token verifikasi
    $token = \Crypt::encrypt([
        'email' => $request->email,
        'id_user' => $user->id_user,
        'expires_at' => now()->addMinutes(30),
    ]);

    // Kirim email menggunakan queue
    \Mail::to($request->email)->queue(new \App\Mail\AlumniEmailVerification($token));

    return back()->with('status', 'Link verifikasi telah dikirim ke email Anda.');
}

public function showChangePasswordForm($token)
{
    try {
        // Dekripsi token untuk mendapatkan data
        $data = \Crypt::decrypt($token);

        // Cek apakah token sudah kadaluarsa
        if (now()->greaterThan($data['expires_at'])) {
            return redirect()->route('alumni.email.form')->with('error', 'Token sudah kadaluarsa.');
        }

        // Tampilkan halaman ubah password
        return view('alumni.change_password', [
            'token' => $token,
            'email' => $data['email'],
            'id_user' => $data['id_user'],
        ]);
    } catch (\Exception $e) {
        return redirect()->route('alumni.email.form')->with('error', 'Token tidak valid atau kadaluarsa.');
    }
}



public function updatePassword(Request $request)
{
    // Validasi input
    $request->validate([
        'token' => 'required',
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',      // huruf kecil
            'regex:/[A-Z]/',      // huruf besar
            'regex:/[0-9]/',      // angka
            'regex:/[@$!%*?&]/'   // karakter spesial
        ],
    ], [
        'password.required' => 'Password wajib diisi.',
        'password.string' => 'Password harus berupa teks.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
    ]);

    try {
        // Dekripsi token untuk mendapatkan data
        $data = Crypt::decrypt($request->token);
    } catch (\Exception $e) {
        return redirect()->route('alumni.email.form')->with('error', 'Token tidak valid atau kadaluarsa.');
    }

    // Cari user dan alumni berdasarkan ID user
    $user = Tb_User::where('id_user', $data['id_user'])->first();
    $alumni = Tb_Alumni::where('id_user', $data['id_user'])->first();

    if (!$user || !$alumni) {
        return redirect()->route('alumni.email.form')->with('error', 'Data user tidak ditemukan.');
    }

    // Update password dan email
    $user->update([
        'password' => Hash::make($request->password),
    ]);

    $alumni->update([
        'email' => $data['email'],
        'is_First_login' => false,
    ]);
    // Set session alumni_nim setelah update password
    session(['alumni_nim' => $alumni->nim]);
    return redirect()->route('login')->with('success', 'Password dan email berhasil diperbarui. Silakan login.');
}

public function edit()
{
    $nim = session('alumni_nim'); // Ambil nim dari session
    $alumni = Tb_Alumni::where('nim', $nim)->firstOrFail(); // Ambil data alumni berdasarkan nim

    return view('alumni.edit', compact('alumni'));
}

public function update(Request $request)
{
    // Validasi data yang diterima
    $validated = $request->validate([
        'phone_number' => 'required',
        'email' => 'required|email',
        'batch' => 'required|integer',
        'graduation_year' => 'required',
        'status' => 'required|in:bekerja,tidak bekerja,melanjutkan studi,berwiraswasta,sedang mencari kerja',
        'id_study' => 'required|exists:tb_study_program,id_study',
    ]);

    // Ambil nim dari session
    $nim = session('alumni_nim');

    // Cari alumni berdasarkan nim
    $alumni = Tb_Alumni::where('nim', $nim)->first(); 

    if (!$alumni) {
    }

    // Update data alumni
    $updated = $alumni->update($validated);

    if (!$updated) {
        return back()->with('error', 'Gagal memperbarui profil');
    }

    return redirect()->route('alumni.edit')->with('success', 'Profil berhasil diperbarui');
}


public function verifyEmailToken($token)
{
    try {
        $data = \Crypt::decrypt($token);

        if (now()->greaterThan($data['expires_at'])) {
            return redirect()->route('alumni.email.form')->with('error', 'Token sudah kadaluarsa.');
        }

        $alumni = Tb_Alumni::where('id_user', $data['id_user'])->first();
        if ($alumni) {
            $alumni->update([
                'email' => $data['email'],
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('alumni.change_password')->with('success', 'Email berhasil diverifikasi.');
    } catch (\Exception $e) {
        return redirect()->route('alumni.email.form')->with('error', 'Token tidak valid atau kadaluarsa.');
    }
}}