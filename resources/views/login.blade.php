@extends('layouts.app')

@section('content')
@if(Auth::check())
    <script>
        // Redirect ke dashboard sesuai role jika sudah login
        @php
            $role = Auth::user()->role;
            $redirect = $role == 1 ? route('dashboard.admin') : ($role == 2 ? route('dashboard.alumni') : ($role == 3 ? route('dashboard.company') : route('login')));
        @endphp
        window.location.href = "{{ $redirect }}";
    </script>
@endif
<!-- Include Google Translate Widget Component -->
<x-translate-widget 
    position="bottom-left" 
    :languages="['en', 'id']" 
    theme="light" 
/>
<!-- Google Translate Widget CSS -->
<link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">

    <div class="flex flex-col-reverse lg:flex-row items-center w-full max-w-6xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 bg-theme-secondary p-8 sm:p-10 md:p-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Selamat datang!</h2>
            <p class="text-sm sm:text-base text-center text-black mb-6">
                Halo! Silahkan masuk terlebih dahulu menggunakan akun yang telah terdaftar pada aplikasi ^_^
            </p>
            @if(session('error'))
            <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-3">
                {{ session('error') }}
            </div>
            @endif
            @if(session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-300 rounded p-3">
                {{ session('status') }}
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <!-- Username -->
                <div>
                    <label for="username" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Username</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        <input id="username" name="username" type="text" required autofocus class="input-field" placeholder="Masukkan Username"
                            value="{{ old('username', $username ?? '') }}">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input id="password" name="password" type="password" required class="input-field" placeholder="Masukkan Kata Sandi"
                            value="{{ isset($password) && $password ? decrypt($password) : '' }}">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                            <i class="fas fa-eye" id="toggleIcon-password"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="mr-2" {{ (old('remember') || (isset($username) && isset($password))) ? 'checked' : '' }}>
                    <label for="remember" class="text-sm text-gray-700">Ingat saya</label>
                </div>

                <!-- Forgot Password -->
                <div class="text-right">
                    <a href="/forgot-password" class="text-sm text-theme-primary hover:underline">Lupa Kata Sandi?</a>
                    <a href="/forgot-nim" class="text-sm text-theme-primary hover:underline ml-4">Lupa NIM?</a>
                </div>

                <!-- Buttons -->
                <div class="space-y-3">
                    <button type="submit" class="btn-primary">Masuk</button>
                    <a href="/" class="btn-secondary">Batal</a>
                </div>
                <!-- Alert untuk berhasil kirim email atau ngga -->
                @if ($errors->any())
                <div class="alert alert-danger col-md-6 mt-3" style="max-width: 400px">
                  <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
                @endif
            </form>
        </div>
        <!-- Right Side - Image -->
        <div class="w-full lg:w-1/2 flex justify-center items-center p-6">
            <img src="{{ asset('assets/images/login.png') }}" alt="Login" class="max-w-xs md:max-w-md lg:max-w-lg">
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('toggleIcon-' + fieldId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

@endsection

