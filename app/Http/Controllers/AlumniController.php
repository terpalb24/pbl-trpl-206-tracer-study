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


public function verifyEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email', 
    ]);

    $user = Auth::user();
    $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();
    

    if (!$alumni) {
        return redirect()->back()->with('error', 'Data alumni tidak ditemukan.');
    }

    // Enkripsi data email dan id_user sebagai token
    $token = Crypt::encrypt([
        'email' => $request->email,
        'id_user' => $user->id_user,
        'expires_at' => Carbon::now()->addMinutes(30), // Token kadaluarsa 30 menit
    ]);
    // Kirim notifikasi verifikasi email (link berisi token)
    Notification::route('mail', $request->email)
    ->notify((new EmailVerificationNotification($token))->delay(now()->addSeconds(2)));

        

    return redirect()->route('alumni.email.form')->with('success', 'Silakan cek inbox Anda dan klik link untuk verifikasi serta ubah password.');
}



public function showChangePasswordForm($token)
{
    try {
        // Debug token yang diterima
        \Log::info('Received token for password change: ', ['token' => $token]);

        // Dekripsi token untuk mendapatkan data
        $data = \Crypt::decrypt($token);

        // Debug data setelah dekripsi
        \Log::info('Decrypted data: ', ['data' => $data]);

        // Cek apakah token sudah kadaluarsa (misalnya kadaluarsa 30 menit setelah dibuat)
        if (Carbon::now()->greaterThan($data['expires_at'])) {
            \Log::warning('Token expired: ', ['token' => $token]);
            return redirect()->route('alumni.email.form')->with('error', 'Token sudah kadaluarsa.');
        }

        // Tampilkan halaman ubah password
        return view('alumni.change_password', [
            'token' => $token,
            'email' => $data['email'],
            'id_user' => $data['id_user'],
        ]);
    } catch (\Exception $e) {
        // Log error untuk debugging jika dekripsi token gagal
        \Log::error('Token decryption failed: ', [
            'error' => $e->getMessage(),
            'token' => $token
        ]);

        // Jika token tidak valid, redirect ke halaman verifikasi email
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
        'ipk' => 'required',
        'status' => 'required|in:worked,not worked',
        'id_study' => 'required|exists:tb_study_program,id_study',
    ]);

    // Ambil nim dari session
    $nim = session('alumni_nim');

    // Cari alumni berdasarkan nim
    $alumni = Tb_Alumni::where('nim', $nim)->first(); 

    if (!$alumni) {
        return back()->with('error', 'Data alumni tidak ditemukan');
    }

    // Update data alumni
    $updated = $alumni->update($validated);

    if (!$updated) {
        return back()->with('error', 'Gagal memperbarui profil');
    }

    return redirect()->route('alumni.edit')->with('success', 'Profil berhasil diperbarui');
}


};