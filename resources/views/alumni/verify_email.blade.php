@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">
    <div class="flex flex-col-reverse md:flex-row items-center w-full max-w-4xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">
        
        <!-- Left Side - Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-10" id="email-form-container">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Verifikasi Email Alumni</h2>
            <p class="text-sm sm:text-base text-center text-gray-600 mb-6">
                Masukkan email Anda untuk menerima link verifikasi. Link dapat diakses dari perangkat manapun.
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
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input id="email" name="email" type="email" required autofocus class="input-field pl-10" placeholder="Masukkan Email">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle cancel button
        document.getElementById('btn-cancel').onclick = function() {
            window.location.href = "{{ route('login') }}";
        }
        
        // Handle back to login from success page
        document.getElementById('btn-back-login').onclick = function() {
            window.location.href = "{{ route('login') }}";
        }
        
        // Handle send again button
        document.getElementById('btn-send-again').onclick = function() {
            // Reset to form view
            document.getElementById('email-form-container').classList.remove('hidden');
            document.getElementById('success-container').classList.add('hidden');
            document.getElementById('email-image').classList.remove('hidden');
            document.getElementById('success-image').classList.add('hidden');
            
            // Clear email input and focus
            document.getElementById('email').value = '';
            document.getElementById('email').focus();
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
