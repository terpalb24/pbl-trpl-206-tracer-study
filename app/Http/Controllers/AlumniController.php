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

    // Ambil user yang sedang login
    $user = Auth::user();
    $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();

    // Update email alumni
    $alumni->email = $request->email;
    $alumni->is_First_login = false; // menanandai bahwa alumni sudah login pertama kali
    $alumni->save();

    // Kirimkan notifikasi verifikasi email
    Notification::send($alumni, new EmailVerificationNotification($alumni));
    return redirect()->route('alumni.email.form')->with('success', 'Email Anda telah berhasil diperbarui. Silakan cek inbox Anda untuk merubah password.');
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