<?php

namespace App\Http\Controllers;

use App\Models\Tb_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $user = Tb_User::where('username', $request->username)->first();
        
        if ($user && Auth::attempt($credentials)) {
            $request->session()->regenerate();

            switch ($user->role) {
                case 1:
                    return redirect()->route('dashboard.admin');
                case 2:
                    return redirect()->route('dashboard.alumni');
                case 3:
                    return redirect()->route('dashboard.company');
                default:
                    return redirect('/login')->with('error', 'Role tidak dikenali.');
            }
            
        }
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');}
}