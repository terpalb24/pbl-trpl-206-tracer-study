@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center max-w-6xl w-full">
        <!-- Form Section -->
        <div class="space-y-6">
            <p class="text-sm text-gray-700">
                Silahkan masukkan akun email yang terhubung, agar kami dapat mengirim email untuk mereset kata sandi anda <span class="text-blue-500">^_^</span>
            </p>

            <form method="POST" action="{{ route('alumni.email.verify') }}" class="space-y-4">
                @csrf
                <div class="relative">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Masukkan Email"
                        required
                        class="w-full pl-10 pr-4 py-2 border rounded-md bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 01-8 0 4 4 0 018 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v2m0 0h3m-3 0H9m3 0v4" />
                        </svg>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col space-y-2">
                    <button
                        type="submit"
                        class="bg-blue-900 text-white py-2 rounded-md hover:bg-blue-800 transition">
                        Kirim
                    </button>
                    <a href="{{ route('login') }}" class="text-center text-sm text-black font-semibold hover:underline">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Illustration Section -->
        <div class="hidden md:block">
            <img src="{{ asset('assets/images/email.png') }}" alt="Reset Password Illustration" class="w-full max-w-md mx-auto" />
        </div>
    </div>
</div>
@endsection
