<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tb_User;
use App\Models\Tb_Alumni;
use App\Models\Tb_Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            switch ($user->role) {
                case 1:
                    return redirect()->route('dashboard.admin');
                case 2:
                    $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();
                    if (!$alumni) {
                        Auth::logout();
                        return redirect('/login')->with('error', 'Data alumni tidak ditemukan. Silakan hubungi administrator.');
                    }
                    // Cek first login alumni
                    if ($alumni->is_First_login) {
                        return redirect()->route('alumni.email.form')->with('success', 'Silakan verifikasi email Anda.');
                    }
                    return redirect()->route('dashboard.alumni');
                case 3:
                    return redirect()->route('dashboard.company');
                default:
                    Auth::logout();
                    return redirect('/login')->with('error', 'Role tidak dikenali.');
            }
        }

        // Ambil cookies jika ada
        $username = $request->cookie('remember_username');
        $password = $request->cookie('remember_password');

        // Jika ada cookies username dan password, lakukan login otomatis
        if ($username && $password) {
            $credentials = [
                'username' => $username,
                'password' => decrypt($password),
            ];
            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::guard('web')->user();
                switch ($user->role) {
                    case 1:
                        return redirect()->route('dashboard.admin');
                    case 2:
                        $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();
                        if (!$alumni) {
                            Auth::logout();
                            return redirect('/login')->with('error', 'Data alumni tidak ditemukan. Silakan hubungi administrator.');
                        }
                        if ($alumni->is_First_login) {
                            return redirect()->route('alumni.email.form')->with('success', 'Silakan verifikasi email Anda.');
                        }
                        session(['alumni_nim' => $alumni->nim]);
                        session([
                            'alumni' => $alumni,
                            'study_program' => $alumni->studyProgram,
                        ]);
                        return redirect()->route('dashboard.alumni');
                    case 3:
                        $company = Tb_Company::where('id_user', $user->id_user)->first();
                        if (!$company) {
                            Auth::logout();
                            return redirect('/login')->with('error', 'Data company tidak ditemukan. Silakan hubungi administrator.');
                        }
                        session(['id_company' => $company->id_company]);
                        return redirect()->route('dashboard.company');
                    default:
                        Auth::logout();
                        return redirect('/login')->with('error', 'Role tidak dikenali.');
                }
            }
        }

        return view('login', compact('username', 'password'));
    }
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Lakukan attempt login pada guard default ('web')
        if (Auth::guard('web')->attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // Simpan cookies jika "ingat saya" dicentang
            if ($request->has('remember')) {
                cookie()->queue(cookie('remember_username', $request->username, 60 * 24 * 30)); // 30 hari
                cookie()->queue(cookie('remember_password', encrypt($request->password), 60 * 24 * 30));
            } else {
                cookie()->queue(cookie()->forget('remember_username'));
                cookie()->queue(cookie()->forget('remember_password'));
            }

            // Ambil user dari guard web
            $user = Auth::guard('web')->user();

            // Cek role user dan tindak lanjutnya
            switch ($user->role) {
                case 1: // Admin
                    return redirect()->route('dashboard.admin');

                case 2: // Alumni
                    // Cek apakah data alumni ada di tabel tb_alumni
                    $alumni = Tb_Alumni::where('id_user', $user->id_user)->first();
                    if (!$alumni) {
                        // Logout jika data alumni tidak ditemukan
                        Auth::logout();
                        return redirect('/login')->with('error', 'Data alumni tidak ditemukan. Silakan hubungi administrator.');
                    }

                    // Cek apakah alumni sudah memverifikasi email
                    if ($alumni && $alumni->is_First_login) {
                        return redirect()->route('alumni.email.form')->with('success', 'Silakan verifikasi email Anda.');
                    }

                    session(['alumni_nim' => $alumni->nim]);

                    session([
                        'alumni' => $alumni,
                        'study_program' => $alumni->studyProgram,
                    ]);
                    return redirect()->route('dashboard.alumni');


                case 3: // Company
                    $company = Tb_Company::where('id_user', $user->id_user)->first();
                    if (!$company) {
                        // Logout jika data company tidak ditemukan
                        Auth::logout();
                        return redirect('/login')->with('error', 'Data company tidak ditemukan. Silakan hubungi administrator.');
                    }
                    session(['id_company' => $company->id_company]);


                    return redirect()->route('dashboard.company');

                default:
                    // Logout jika role tidak dikenali
                    Auth::logout();
                    return redirect('/login')->with('error', 'Role tidak dikenali.');
            }
        }

        // Gagal login
        return back()->with('error', 'Username atau password salah.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Hapus cookies remember_username dan remember_password saat logout
        cookie()->queue(cookie()->forget('remember_username'));
        cookie()->queue(cookie()->forget('remember_password'));

        return redirect('/login');
    }
    public function ChangePasswordForm()
    {
        return view('change-password');
    }

    public function updatePasswordAll(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'string',
                'min:8',
            ],
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

        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
        }

        // Periksa apakah password baru sama dengan password lama
        if ($request->current_password === $request->password) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Arahkan pengguna ke dashboard sesuai peran tanpa private function
        if ($user->role == '1') {
            return redirect()->route('dashboard.admin');
        } elseif ($user->role == '2') {
            return redirect()->route('dashboard.alumni');
        } elseif ($user->role == '3') {
            return redirect()->route('dashboard.company');
        } else {
            return redirect()->route('/login')->with('error', 'Role tidak dikenali.');
        }
    }


    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }

    public function sendResetLinkCustom(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Cek alumni (role 2)
        $alumni = Tb_Alumni::where('email', $request->email)->first();
        $user = null;
        if ($alumni) {
            $user = Tb_User::where('id_user', $alumni->id_user)->where('role', 2)->first();
        }

        // Jika bukan alumni, cek company (role 3)
        if (!$user) {
            $company = Tb_Company::where('company_email', $request->email)->first();
            if ($company) {
                $user = Tb_User::where('id_user', $company->id_user)->where('role', 3)->first();
            }
        }

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di data alumni atau perusahaan.']);
        }

        // Generate token dan simpan di session
        $token = Str::random(64);
        Session::put('reset_token_' . $request->email, $token);

        // Kirim email reset password
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));
        Mail::send('emails.reset-password-link', [
            'resetLink' => $resetLink,
            'user' => $user
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Akun Anda');
        });

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password-confirmation', ['token' => $token, 'email' => $email]);
    }

    public function resetPasswordCustom(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // number
                'regex:/[^a-zA-Z0-9]/' // special char
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.'
        ]);

        // Cek token di session
        $sessionToken = Session::get('reset_token_' . $request->email);
        if (!$sessionToken || $sessionToken !== $request->token) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah digunakan.']);
        }

        // Cek alumni (role 2)
        $alumni = Tb_Alumni::where('email', $request->email)->first();
        $user = null;
        if ($alumni) {
            $user = Tb_User::where('id_user', $alumni->id_user)->where('role', 2)->first();
        }

        // Jika bukan alumni, cek company (role 3)
        if (!$user) {
            $company = Tb_Company::where('company_email', $request->email)->first();
            if ($company) {
                $user = Tb_User::where('id_user', $company->id_user)->where('role', 3)->first();
            }
        }

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di data alumni atau perusahaan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token dari session
        Session::forget('reset_token_' . $request->email);

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    // Halaman Lupa NIM
    public function forgotNim(Request $request)
    {
        $search = $request->input('search');
        $alumni = Tb_Alumni::query()
            ->when($search, function ($query, $search) {
                return $query->where('nim', 'like', "%{$search}%")
                             ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        return view('forgot-nim', compact('alumni'));
    }
}

