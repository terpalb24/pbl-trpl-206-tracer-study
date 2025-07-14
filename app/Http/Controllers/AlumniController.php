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
use App\Models\Tb_study_program;

class AlumniController extends Controller
{
    public function dashboard()
    {
        // Gunakan relasi dari user yang sedang login, dengan fallback ke session
        if (auth()->check() && auth()->user()->alumni) {
            $alumni = auth()->user()->alumni;
        } else {
            // Fallback ke session jika relasi tidak tersedia
            $alumniId = session('id_user');
            $alumni = Tb_Alumni::find($alumniId);
            
            if (!$alumni) {
                return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
            }
        }
        
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
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
        ],
        'nim' => [
            'required',
            'string'
        ]
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.regex' => 'Format email tidak valid. Gunakan format email yang benar (contoh: nama@domain.com).',
        'nim.required' => 'NIM wajib diisi.',
    ]);

    // Cari alumni berdasarkan NIM
    $alumni = Tb_Alumni::where('nim', $request->nim)->first();

    if (!$alumni) {
        return back()->with('error', 'NIM tidak ditemukan dalam database alumni. Pastikan NIM yang Anda masukkan sudah terdaftar.');
    }

    // Buat token verifikasi dengan semua data yang diperlukan
    $token = \Crypt::encrypt([
        'email' => $request->email,
        'nim' => $alumni->nim,
        'id_user' => $alumni->id_user,
        'alumni_name' => $alumni->name,
        'expires_at' => now()->addMinutes(30),
    ]);

    // Kirim email menggunakan queue
    \Mail::to($request->email)->queue(new \App\Mail\AlumniEmailVerification($token));

    return back()->with('status', 'Link verifikasi telah dikirim ke email Anda. Silakan cek email dan klik link verifikasi untuk melanjutkan.');
}

public function showChangePasswordForm($token)
{
    try {
        // Dekripsi token untuk mendapatkan data
        $data = \Crypt::decrypt($token);

        // Cek apakah token sudah kadaluarsa
        if (now()->greaterThan($data['expires_at'])) {
            return view('alumni.verify_email')->with('error', 'Token sudah kadaluarsa. Silakan kirim ulang verifikasi email.');
        }

        // Verifikasi bahwa alumni masih ada di database
        $alumni = Tb_Alumni::where('nim', $data['nim'])->where('id_user', $data['id_user'])->first();
        if (!$alumni) {
            return view('alumni.verify_email')->with('error', 'Data alumni tidak ditemukan.');
        }

        // Tampilkan halaman ubah password dengan data dari token
        return view('alumni.change_password', [
            'token' => $token,
            'email' => $data['email'],
            'nim' => $data['nim'],
            'alumni_name' => $data['alumni_name'],
            'id_user' => $data['id_user'],
        ]);
    } catch (\Exception $e) {
        return view('alumni.verify_email')->with('error', 'Token tidak valid atau kadaluarsa. Silakan kirim ulang verifikasi email.');
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
        
        // Cek apakah token sudah kadaluarsa
        if (now()->greaterThan($data['expires_at'])) {
            return back()->with('error', 'Token sudah kadaluarsa. Silakan kirim ulang verifikasi email.');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Token tidak valid atau kadaluarsa.');
    }

    // Cari user dan alumni berdasarkan data dari token
    $user = Tb_User::where('id_user', $data['id_user'])->first();
    $alumni = Tb_Alumni::where('nim', $data['nim'])->where('id_user', $data['id_user'])->first();

    if (!$user || !$alumni) {
        return back()->with('error', 'Data user tidak ditemukan.');
    }

    // Update password dan email, serta mark email as verified
    $user->update([
        'password' => Hash::make($request->password),
    ]);

    $alumni->update([
        'email' => $data['email'],
        'email_verified_at' => now(),
        'is_First_login' => false,
    ]);
    
    // Login alumni dan set session untuk dashboard alert
    Auth::login($user);
    
    // Set session alumni_nim dan data alumni seperti pada login normal
    session(['alumni_nim' => $alumni->nim]);
    session(['id_user' => $user->id_user]);
    session([
        'alumni' => $alumni,
        'study_program' => $alumni->studyProgram,
    ]);
    
    session()->flash('password_updated', 'Email berhasil diverifikasi dan password telah diubah!');
    
    return redirect()->route('dashboard.alumni');
}

public function edit()
{
    // Gunakan relasi dari user yang sedang login, dengan fallback ke session
    if (auth()->check() && auth()->user()->alumni) {
        $alumni = auth()->user()->alumni;
    } else {
        // Fallback ke session jika relasi tidak tersedia
        $nim = session('alumni_nim');
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
        }
        $alumni = Tb_Alumni::where('nim', $nim)->firstOrFail();
    }

    return view('alumni.edit', compact('alumni'));
}

public function update(Request $request)
{
    // Validasi data yang diterima
    $validated = $request->validate([
        'phone_number' => [
            'required',
            'regex:/^[0-9\-]+$/',
            'max:15',
            'regex:/^(?!.*--)[0-9]+(-[0-9]+)*[0-9]$/'
        ],
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'address' => 'required|string|min:10|max:500',
        'batch' => 'required|integer',
        'graduation_year' => 'required',
        'status' => 'required|in:bekerja,tidak bekerja,melanjutkan studi,berwiraswasta,sedang mencari kerja',
        'id_study' => 'required|exists:tb_study_program,id_study',
    ], [
        'phone_number.required' => 'Nomor telepon wajib diisi.',
        'phone_number.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda hubung (-), tidak boleh dimulai/diakhiri dengan tanda hubung, dan tidak boleh ada dua tanda hubung berturut-turut.',
        'phone_number.max' => 'Nomor telepon maksimal 15 karakter.',
        'address.required' => 'Alamat wajib diisi.',
        'address.min' => 'Alamat minimal 10 karakter.',
        'address.max' => 'Alamat maksimal 500 karakter.',
    ]);

    // Gunakan relasi dari user yang sedang login, dengan fallback ke session
    if (auth()->check() && auth()->user()->alumni) {
        $alumni = auth()->user()->alumni;
    } else {
        // Fallback ke session jika relasi tidak tersedia
        $nim = session('alumni_nim');
        if (!$nim) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
        }
        $alumni = Tb_Alumni::where('nim', $nim)->firstOrFail();
    }

    // Update data alumni
    $updated = $alumni->update($validated);

    if (!$updated) {
        return back()->with('error', 'Gagal memperbarui profil');
    }

    return redirect()->route('alumni.edit')->with('success', 'Profil berhasil diperbarui');
}
}