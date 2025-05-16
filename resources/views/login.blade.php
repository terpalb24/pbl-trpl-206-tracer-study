@extends('layouts.app')

@section('content')
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
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <!-- Username -->
                <div>
                    <label for="username" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Username</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        <input id="username" name="username" type="text" required autofocus class="input-field" placeholder="Masukkan Username">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input id="password" name="password" type="password" required class="input-field" placeholder="Masukkan Kata Sandi">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                            <i class="fas fa-eye" id="toggleIcon-password"></i>
                        </button>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="text-right">
                    <a href="#" class="text-sm text-theme-primary hover:underline">Lupa Kata Sandi?</a>
                </div>

                <!-- Buttons -->
                <div class="space-y-3">
                    <button type="submit" class="btn-primary">Masuk</button>
                    <a href="/" class="btn-secondary">Batal</a>
                </div>
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
