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
use Carbon\Carbon;
use Exception;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Handle parameter clear_session untuk membersihkan session dari verifikasi email
        if ($request->has('clear_session') && $request->get('clear_session') == '1') {
            // Clear all session data
            $request->session()->flush();
            $request->session()->regenerate();
            
            // Clear cookies jika ada
            $cookie1 = cookie()->forget('remember_username');
            $cookie2 = cookie()->forget('remember_password');
            
            return redirect()->route('login')->withCookies([$cookie1, $cookie2]);
        }
        
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
        $recoveryCodes = config('recovery.admin_codes', []);
        $input = $request->input('email');
        // Jika input recovery code, skip validasi email
        if (in_array($input, $recoveryCodes)) {
            // Cek blacklist recovery code
            $usedCodesPath = storage_path('app/used_recovery_codes.json');
            $usedCodes = file_exists($usedCodesPath) ? json_decode(file_get_contents($usedCodesPath), true) : [];
            if (in_array($input, $usedCodes)) {
                return back()->withErrors(['email' => 'Recovery code sudah pernah digunakan.']);
            }
            Session::put('admin_recovery_code', $input);
            return redirect()->route('password.admin.reset');
        }
        // Jika bukan recovery code, validasi email
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

        // Generate token berdasarkan email dan secret key (tidak perlu disimpan di database)
        $timestamp = time();
        $token = base64_encode($request->email . '|' . $timestamp . '|' . hash('sha256', $request->email . $timestamp . config('app.key')));

        // Kirim email reset password
        $resetLink = url('/reset-password/' . urlencode($token) . '?email=' . urlencode($request->email));
        Mail::send('emails.reset-password-link', [
            'resetLink' => $resetLink,
            'user' => $user
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Akun Anda');
        });

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Link akan berlaku selama 24 jam.');
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

        // Verifikasi token
        try {
            $decodedToken = base64_decode($request->token);
            $tokenParts = explode('|', $decodedToken);
            
            if (count($tokenParts) !== 3) {
                return back()->withErrors(['token' => 'Token reset password tidak valid.']);
            }
            
            $tokenEmail = $tokenParts[0];
            $timestamp = $tokenParts[1];
            $hash = $tokenParts[2];
            
            // Verifikasi email
            if ($tokenEmail !== $request->email) {
                return back()->withErrors(['token' => 'Token tidak sesuai dengan email.']);
            }
            
            // Verifikasi hash
            $expectedHash = hash('sha256', $tokenEmail . $timestamp . config('app.key'));
            if ($hash !== $expectedHash) {
                return back()->withErrors(['token' => 'Token reset password tidak valid.']);
            }
            
            // Verifikasi expiry (24 jam)
            if (time() - $timestamp > 86400) { // 24 jam = 86400 detik
                return back()->withErrors(['token' => 'Token reset password sudah kadaluarsa. Silakan ajukan reset password baru.']);
            }
            
        } catch (Exception $e) {
            return back()->withErrors(['token' => 'Token reset password tidak valid.']);
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

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
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

    // Tambahan: Form reset password admin via recovery code
    public function showAdminResetPasswordForm(Request $request)
    {
        // Hanya bisa diakses jika session recovery code ada
        if (!Session::has('admin_recovery_code')) {
            return redirect()->route('password.request')->with('error', 'Akses tidak valid.');
        }
        return view('auth.admin-reset-password');
    }

    public function resetAdminPassword(Request $request)
    {
        if (!Session::has('admin_recovery_code')) {
            return redirect()->route('password.request')->with('error', 'Akses tidak valid.');
        }
        $recoveryCode = Session::get('admin_recovery_code');
        // Cek apakah recovery code sudah pernah dipakai
        $usedCodesPath = storage_path('app/used_recovery_codes.json');
        $usedCodes = file_exists($usedCodesPath) ? json_decode(file_get_contents($usedCodesPath), true) : [];
        if (in_array($recoveryCode, $usedCodes)) {
            Session::forget('admin_recovery_code');
            return redirect()->route('password.request')->with('error', 'Recovery code sudah pernah digunakan.');
        }
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // Ganti password admin (role 1)
        $admin = Tb_User::where('role', 1)->first();
        if (!$admin) {
            return redirect()->route('password.request')->with('error', 'Akun admin tidak ditemukan.');
        }
        $admin->password = Hash::make($request->password);
        $admin->save();
        // Simpan recovery code ke blacklist
        $usedCodes[] = $recoveryCode;
        file_put_contents($usedCodesPath, json_encode($usedCodes));
        Session::forget('admin_recovery_code');
        return redirect()->route('login')->with('status', 'Password admin berhasil direset. Silakan login dengan password baru.');
    }

    // Clear session untuk verifikasi email
    public function clearSession(Request $request)
    {
        try {
            // Clear all session data
            $request->session()->flush();
            $request->session()->regenerate();
            
            // Clear cookies jika ada
            cookie()->queue(cookie()->forget('remember_username'));
            cookie()->queue(cookie()->forget('remember_password'));
            
            return response()->json([
                'success' => true,
                'message' => 'Session berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus session: ' . $e->getMessage()
            ], 500);
        }
    }

    // Clear session dan redirect dengan form POST
    public function clearSessionAndRedirect(Request $request)
    {
        try {
            // Clear all session data
            $request->session()->flush();
            $request->session()->regenerate();
            
            // Clear cookies jika ada
            cookie()->queue(cookie()->forget('remember_username'));
            cookie()->queue(cookie()->forget('remember_password'));
            
            // Get redirect URL or default to login
            $redirectUrl = $request->input('redirect_url', route('login'));
            
            return redirect($redirectUrl)->with('success', 'Session berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal menghapus session: ' . $e->getMessage());
        }
    }
}

