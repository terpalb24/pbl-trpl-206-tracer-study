@extends('layouts.app')

@section('content')
<div class="flex flex-col-reverse lg:flex-row items-center w-full max-w-6xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">

    <!-- Left Side - Change Password Form -->
    <div class="w-full lg:w-1/2 bg-theme-secondary p-8 sm:p-10 md:p-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Ganti Password</h2>
        <p class="text-sm sm:text-base text-center text-black mb-6">
            Silakan masukkan password baru dan konfirmasi untuk mengganti password akun Anda.
        </p>

        <form method="POST" action="{{ route('alumni.password.update') }}" class="space-y-5">
            @csrf

            <!-- Password Input -->
            <div>
                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password Baru</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" name="password" type="password" required class="input-field" placeholder="Masukkan Password Baru">
                </div>
                @error('password')
                    <div style="color:red;" class="mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Confirmation Input -->
            <div>
                <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="input-field" placeholder="Konfirmasi Password Baru">
                </div>
            </div>

            <!-- Buttons -->
            <div class="space-y-3">
                <button type="submit" class="btn-primary">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

    <!-- Right Side - Image -->
    <div class="w-full lg:w-1/2 flex justify-center items-center p-6">
        <img src="{{ asset('assets/images/login.png') }}" alt="Change Password" class="max-w-xs md:max-w-md lg:max-w-lg">
    </div>

</div
