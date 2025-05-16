@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-10">
    <div class="flex flex-col-reverse lg:flex-row items-center w-full max-w-6xl mx-auto rounded-lg shadow-lg overflow-hidden bg-white">

        <!-- Left Side - Change Password Form -->
        <div class="w-full lg:w-1/2 bg-theme-secondary p-8 sm:p-10 md:p-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-black mb-3">Ganti Password</h2>
            <p class="text-sm sm:text-base text-center text-black mb-6">
                Silakan masukkan password lama, password baru, dan konfirmasi password baru untuk mengganti password akun Anda.
            </p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <!-- Password Lama -->
                <div>
                    <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">Password Lama</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="current_password" name="current_password" type="password" required
                               class="input-field pr-10" placeholder="Masukkan Password Lama">
                        <button type="button" onclick="togglePassword('current_password')" 
                                class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                            <i class="fas fa-eye" id="toggleIcon-current_password"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="mt-2 text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Password Baru</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required
                               class="input-field pr-10" placeholder="Masukkan Password Baru">
                        <button type="button" onclick="togglePassword('password')" 
                                class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                            <i class="fas fa-eye" id="toggleIcon-password"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="mt-2 text-red-600">{{ $message }}</div>
                    @enderror
                    <div class="mt-2 text-xs text-gray-600">
                        Password harus mengandung:
                        <ul class="list-inside list-disc">
                            <li>Setidaknya satu huruf besar</li>
                            <li>Setidaknya satu huruf kecil</li>
                            <li>Setidaknya satu angka</li>
                            <li>Setidaknya satu karakter spesial (@$!%*?&)</li>
                        </ul>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="input-field pr-10" placeholder="Konfirmasi Password Baru">
                        <button type="button" onclick="togglePassword('password_confirmation')" 
                                class="absolute right-3 top-2.5 text-gray-500 focus:outline-none">
                            <i class="fas fa-eye" id="toggleIcon-password_confirmation"></i>
                        </button>
                    </div>
                </div>

               <!-- Buttons -->
           <div class="flex justify-between space-x-3">
             <a href="{{ 
               auth()->user()->role == 1 ? route('dashboard.admin') : 
              (auth()->user()->role == 2 ? route('dashboard.alumni') : 
              (auth()->user()->role == 3 ? route('dashboard.company') : '#')) }}" 
               class="btn-secondary flex-1 text-center py-2 rounded text-gray-700 hover:bg-gray-200 transition">
                Batal
             </a>
    <button type="submit" class="btn-primary flex-1">
        Ubah Password
    </button>
</div>
            </form>
        </div>

        <!-- Right Side - Image -->
        <div class="w-full lg:w-1/2 flex justify-center items-center p-6">
            <img src="{{ asset('assets/images/login.png') }}" alt="Change Password" class="max-w-xs md:max-w-md lg:max-w-lg">
        </div>

    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('toggleIcon-' + fieldId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
