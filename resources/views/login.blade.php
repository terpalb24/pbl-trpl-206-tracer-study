@extends('layouts.app')

@section('content')
<div class="flex flex-col-reverse lg:flex-row items-center w-full max-w-6xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">

    <!-- Left Side - Login Form -->
    <div class="w-full lg:w-1/2 bg-theme-secondary p-8 sm:p-10 md:p-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Selamat datang!</h2>
        <p class="text-sm sm:text-base text-center text-black mb-6">
            Halo! Silahkan masuk terlebih dahulu menggunakan akun yang telah terdaftar pada aplikasi ^_^
        </p>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="email" name="email" type="email" required autofocus
                        class="input-field"
                        placeholder="Masukkan Email">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Masukkan Kata Sandi</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" name="password" type="password" required
                        class="input-field"
                        placeholder="Masukkan Kata Sandi">
                </div>
            </div>

            <!-- Forgot Password -->
            <div class="text-right">
                <a href="" class="text-sm text-theme-primary hover:underline">
                    Lupa Kata Sandi?
                </a>
            </div>

            <!-- Buttons -->
            <div class="space-y-3">
                <button type="submit"
                    class="btn-primary">
                    Masuk
                </button>
                <a href=""
                    class="btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Right Side - Image -->
    <div class="w-full lg:w-1/2 flex justify-center items-center p-6">
        <img src="{{ asset('assets/images/login.png') }}" alt="Login" class="max-w-xs md:max-w-md lg:max-w-lg">
    </div>

</div>
@endsection
