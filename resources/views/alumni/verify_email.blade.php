@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">
    <div class="flex flex-col-reverse md:flex-row items-center w-full max-w-4xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">
        
        <!-- Left Side - Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-10" id="email-form-container">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Verifikasi Email Alumni</h2>
            <p class="text-sm sm:text-base text-center text-gray-600 mb-6">
                Masukkan NIM dan email baru Anda untuk menerima link verifikasi. Link dapat diakses dari perangkat manapun.
            </p>
            @if(session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-300 rounded p-3" id="success-message">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('alumni.verify.email') }}" class="space-y-5" id="email-verification-form">
                @csrf
                <div>
                    <label for="nim" class="block mb-1 text-sm font-medium text-gray-700">NIM</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-id-card"></i></span>
                        <input id="nim" name="nim" type="text" required autofocus class="input-field pl-10" placeholder="Masukkan NIM" value="{{ old('nim') }}">
                    </div>
                    @error('nim')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input id="email" name="email" type="email" required class="input-field pl-10" placeholder="Masukkan Email Baru" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <button
                        type="submit"
                        class="bg-blue-900 text-white py-2 rounded-md hover:bg-blue-800 transition">
                        Kirim Link Verifikasi
                    </button>
                    <button type="button" id="btn-cancel" class="text-center text-sm text-black font-semibold hover:underline">
                        Kembali ke Login
                    </button>
                    
                    <!-- Fallback HTML link jika JavaScript tidak bekerja -->
                    <noscript>
                        <a href="{{ route('login') }}" class="text-center text-sm text-blue-600 font-semibold hover:underline">
                            ← Kembali ke Login (HTML)
                        </a>
                    </noscript>
                    
                    <!-- Link alternatif dengan GET parameter untuk clear session -->
                    <a href="{{ route('login') }}?clear_session=1" class="text-center text-xs text-gray-500 hover:text-gray-700" style="display: none;" id="backup-login-link">
                        Kembali ke Login (Backup)
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Success Page (Hidden by default) -->
        <div class="w-full md:w-1/2 p-8 sm:p-10 hidden" id="success-container">
            <div class="text-center">
                <div class="mb-6">
                    <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Email Berhasil Dikirim!</h2>
                <p class="text-sm sm:text-base text-center text-gray-600 mb-6">
                    Link verifikasi telah dikirim ke email Anda. Silakan cek email dan klik link verifikasi untuk melanjutkan.
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Link verifikasi akan kadaluarsa dalam 30 menit. Jika tidak menerima email, periksa folder spam atau kirim ulang.
                    </p>
                </div>
                <div class="flex flex-col space-y-2">
                    <button type="button" id="btn-send-again" class="bg-blue-900 text-white py-2 rounded-md hover:bg-blue-800 transition">
                        Kirim Ulang Email
                    </button>
                    <button type="button" id="btn-back-login" class="text-center text-sm text-black font-semibold hover:underline">
                        Kembali ke Login
                    </button>
                    
                    <!-- Fallback HTML link jika JavaScript tidak bekerja -->
                    <noscript>
                        <a href="{{ route('login') }}" class="text-center text-sm text-blue-600 font-semibold hover:underline">
                            ← Kembali ke Login (HTML)
                        </a>
                    </noscript>
                    
                    <!-- Link alternatif dengan GET parameter untuk clear session -->
                    <a href="{{ route('login') }}?clear_session=1" class="text-center text-xs text-gray-500 hover:text-gray-700" style="display: none;" id="backup-login-link-success">
                        Kembali ke Login (Backup)
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Illustration -->
        <div class="w-full md:w-1/2 flex justify-center items-center p-6 bg-gray-50">
            <img src="{{ asset('assets/images/email.png') }}" alt="Verifikasi Email" class="max-w-xs md:max-w-md lg:max-w-lg w-full h-auto" id="email-image">
            <img src="{{ asset('assets/images/email.png') }}" alt="Email Berhasil Dikirim" class="max-w-xs md:max-w-md lg:max-w-lg w-full h-auto hidden" id="success-image">
        </div>
    </div>
</div>
{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk membersihkan session di server dan redirect
    function clearSessionAndRedirect(url) {
        console.log('🧹 Membersihkan session server dan melakukan redirect...');
        
        // Clear client-side storage
        localStorage.clear();
        sessionStorage.clear();
        
        // Clear cookies yang mungkin mempengaruhi session
        document.cookie.split(";").forEach(function(c) { 
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
        });
        
        // Kirim AJAX request untuk clear session di server
        fetch('/clear-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify({})
        })
        .then(response => {
            console.log('✅ Session cleared on server');
            // Force redirect dengan menghapus referrer
            window.location.replace(url);
        })
        .catch(error => {
            console.log('❌ Error clearing session:', error);
            // Tetap redirect meskipun error
            window.location.replace(url);
        });
    }
    
    // Fungsi alternatif dengan form POST untuk menghindari session
    function redirectViaForm(url) {
        console.log('📝 Redirect menggunakan form dengan clear session...');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/clear-session-redirect';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value;
        
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
        }
        
        // Add redirect URL
        const urlInput = document.createElement('input');
        urlInput.type = 'hidden';
        urlInput.name = 'redirect_url';
        urlInput.value = url;
        form.appendChild(urlInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Fungsi untuk membuka window baru jika redirect tidak berhasil
    function fallbackRedirect(url) {
        console.log('🪟 Fallback: Membuka window baru...');
        
        // Try to open in current window first
        try {
            window.open(url, '_self');
        } catch (e) {
            // If fails, open in new window
            window.open(url, '_blank');
        }
    }
    
    // Fungsi utama untuk handle redirect ke login
    function handleLoginRedirect() {
        const loginUrl = "{{ route('login') }}";
        console.log('🔄 Memulai proses redirect ke login...');
        
        // Show loading indicator
        const buttons = document.querySelectorAll('button');
        buttons.forEach(btn => {
            if (btn.innerText.includes('Kembali ke Login')) {
                btn.innerHTML = '⏳ Menghapus Session...';
                btn.disabled = true;
            }
        });
        
        // Method 1: Clear session via AJAX dan redirect
        try {
            clearSessionAndRedirect(loginUrl);
        } catch (e) {
            console.log('❌ Method 1 gagal:', e);
            
            // Method 2: Redirect via form
            try {
                setTimeout(() => {
                    redirectViaForm(loginUrl);
                }, 1000);
            } catch (e2) {
                console.log('❌ Method 2 gagal:', e2);
                
                // Method 3: Fallback redirect
                setTimeout(() => {
                    fallbackRedirect(loginUrl);
                }, 2000);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle cancel button dengan multiple methods
        document.getElementById('btn-cancel').onclick = function(e) {
            e.preventDefault();
            
            // Show confirmation with SweetAlert
            Swal.fire({
                title: 'Kembali ke Login?',
                text: 'Session verifikasi akan dihapus dan Anda akan kembali ke halaman login.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kembali',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleLoginRedirect();
                }
            });
        }
        
        // Handle back to login from success page dengan multiple methods
        document.getElementById('btn-back-login').onclick = function(e) {
            e.preventDefault();
            
            // Show confirmation with SweetAlert
            Swal.fire({
                title: 'Kembali ke Login?',
                text: 'Session verifikasi akan dihapus dan Anda akan kembali ke halaman login.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kembali',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleLoginRedirect();
                }
            });
        }
        
        // Tampilkan link backup setelah 3 detik jika masih di halaman
        setTimeout(function() {
            const backupLink = document.getElementById('backup-login-link');
            const backupLinkSuccess = document.getElementById('backup-login-link-success');
            if (backupLink) {
                backupLink.style.display = 'block';
                backupLink.innerHTML = '🔗 Link Backup - Klik jika tombol tidak bekerja';
                console.log('🔗 Backup link displayed after 3 seconds');
            }
            if (backupLinkSuccess) {
                backupLinkSuccess.style.display = 'block';
                backupLinkSuccess.innerHTML = '🔗 Link Backup - Klik jika tombol tidak bekerja';
                console.log('🔗 Success backup link displayed after 3 seconds');
            }
        }, 3000);
        
        // Log session information untuk debugging
        console.log('📊 Session Debug Info:');
        console.log('- Current URL:', window.location.href);
        console.log('- Referrer:', document.referrer);
        console.log('- User Agent:', navigator.userAgent);
        console.log('- Cookies:', document.cookie);
        console.log('- Local Storage Keys:', Object.keys(localStorage));
        console.log('- Session Storage Keys:', Object.keys(sessionStorage));
        
        // Handle send again button
        document.getElementById('btn-send-again').onclick = function() {
            // Reset to form view
            document.getElementById('email-form-container').classList.remove('hidden');
            document.getElementById('success-container').classList.add('hidden');
            document.getElementById('email-image').classList.remove('hidden');
            document.getElementById('success-image').classList.add('hidden');
            
            // Clear input fields and focus on first field
            document.getElementById('nim').value = '';
            document.getElementById('email').value = '';
            document.getElementById('nim').focus();
        }
        
        // Check if there's a success message and show success page
        @if(session('status'))
            // Hide form and show success page
            document.getElementById('email-form-container').classList.add('hidden');
            document.getElementById('success-container').classList.remove('hidden');
            document.getElementById('email-image').classList.add('hidden');
            document.getElementById('success-image').classList.remove('hidden');
            
            // Show SweetAlert success
            Swal.fire({
                icon: 'success',
                title: 'Email Berhasil Dikirim!',
                text: 'Link verifikasi telah dikirim ke email Anda. Silakan cek email dan klik link verifikasi.',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#1e40af'
            });
        @endif
    });
</script>
@endsection
