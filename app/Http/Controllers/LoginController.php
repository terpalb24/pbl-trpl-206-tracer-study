<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tb_User;
use App\Models\Tb_Alumni;
    
    class LoginController extends Controller
    {
        public function showLoginForm()
        {
            return view('login');
        }public function login(Request $request)
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
                        if ($alumni && $alumni->is_First_login)  {
                            return redirect()->route('alumni.email.form')->with('success', 'Silakan verifikasi email Anda.');
                        }
        
                        // Jika sudah terverifikasi, redirect ke dashboard alumni
                        return redirect()->route('dashboard.alumni');
                    
                    case 3: // Company
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
    
            return redirect('/login');
        }
    }
    
