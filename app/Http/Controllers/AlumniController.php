<?php

namespace App\Http\Controllers;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tb_Alumni;
use App\Models\Tb_User;
use Illuminate\Support\Facades\Hash;

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
    if (!Auth::check()) {
        return redirect()->route('login')->withErrors(['email' => 'Anda harus login terlebih dahulu.']);
    }

    $user = Auth::user();

    // Ambil data alumni terkait user
    $alumni = Tb_Alumni::where('id_user', $user->id_user)
        ->where('email', $request->email)
        ->first();
  
    if (!$alumni) {
        return back()->withErrors(['email' => 'Email tidak ditemukan atau tidak sesuai dengan akun Anda.']);
    }

       $alumni->email_verification = 'verified';
        $alumni->save();

        return redirect()->route('alumni.password.form')->with('success', 'Email berhasil diverifikasi, silakan ubah password.');
    }


public function showChangePasswordForm()
{
    return view('alumni.change_password');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = Auth::user();
    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()->route('dashboard.alumni')->with('success', 'Password berhasil diubah!');
}


}