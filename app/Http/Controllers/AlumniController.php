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


class AlumniController extends Controller
{
    public function dashboard()
    {
        return view('alumni.dashboard');
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
    ]);

    // Kirim notifikasi verifikasi email (link berisi token)
    Notification::route('mail', $request->email)
        ->notify(new EmailVerificationNotification($token));

    return redirect()->route('alumni.email.form')->with('success', 'Silakan cek inbox Anda dan klik link untuk verifikasi serta ubah password.');
}

public function showChangePasswordForm(Request $request)
{
    try {
        $data = Crypt::decrypt($request->token);
    } catch (\Exception $e) {
        return redirect()->route('alumni.email.form')->with('error', 'Token tidak valid.');
    }

    return view('alumni.change_password', [
        'token' => $request->token,
        'email' => $data['email'],
        'id_user' => $data['id_user'],
    ]);
}


public function updatePassword(Request $request)
{
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
        $data = Crypt::decrypt($request->token);
    } catch (\Exception $e) {
        return redirect()->route('alumni.email.form')->with('error', 'Token tidak valid atau kadaluarsa.');
    }

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



}