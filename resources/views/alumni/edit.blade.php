@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="editprofil-container">

    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" id="sidebar" />

    {{-- Tombol Toggle Sidebar (Untuk Mobile) --}}
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i> <!-- Ikon hamburger menu -->
    </button>

    {{-- Main Content --}}
    <main class="flex-grow overflow-y-auto" id="main-content">
        {{-- Header --}}
        <x-alumni.header title="Edit Profil" />

        <!-- Content Section -->
        <div class="p-3 sm:p-4 lg:p-6">
            {{-- Breadcrumb --}}
            <nav class="mb-4 sm:mb-6">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm">
                    <li><a href="{{ route('dashboard.alumni') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700">Edit Profil</li>
                </ol>
            </nav>

            {{-- Pesan Flash --}}
            @if (session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 text-green-700 rounded-lg flex items-center text-sm sm:text-base">
                    <i class="fas fa-check-circle mr-2 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-100 text-red-700 rounded-lg flex items-center text-sm sm:text-base">
                    <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Profile Header Card --}}
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6 border border-gray-200">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-graduate text-white text-2xl sm:text-3xl"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ $alumni->name }}</h2>
                        <p class="text-sm sm:text-base text-gray-600">{{ session('alumni_nim') }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                Angkatan {{ $alumni->batch }}
                            </span>
                            @if($alumni->graduation_year)
                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Lulus {{ $alumni->graduation_year }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Edit Profil --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-edit mr-2 text-blue-600"></i>
                        Edit Informasi Profil
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui informasi profil Anda di bawah ini</p>
                </div>

                <form action="{{ route('alumni.update') }}" method="POST" class="p-4 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- NIM -->
                        <div class="space-y-1 sm:space-y-2">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700">NIM</label>
                            <div class="relative">
                                <input type="text" value="{{ session('alumni_nim') }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base text-gray-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">NIM tidak dapat diubah</p>
                        </div>

                        <!-- Kelamin -->
                        <div class="space-y-1 sm:space-y-2">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700">Jenis Kelamin</label>
                            <div class="relative">
                                <input type="text" value="{{ $alumni->gender }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base text-gray-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Jenis kelamin tidak dapat diubah</p>
                        </div>
                        
                        <!-- Nama -->
                        <div class="space-y-1 sm:space-y-2">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700">Nama Lengkap</label>
                            <div class="relative">
                                <input type="text" value="{{ $alumni->name }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base text-gray-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Nama tidak dapat diubah</p>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="phone_number" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" name="phone_number" id="phone_number" 
                                       value="{{ old('phone_number', $alumni->phone_number) }}"
                                       class="w-full border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone_number') border-red-500 @enderror"
                                       placeholder="Contoh: 08123456789">
                            </div>
                            @error('phone_number')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="email" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                </div>
                                <input type="email" name="email" id="email" 
                                       value="{{ old('email', $alumni->email) }}"
                                       class="w-full border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                       placeholder="Contoh: nama@email.com">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Prodi -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="id_study" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Program Studi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-graduation-cap text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" 
                                       value="{{ $alumni->studyProgram->study_program ?? 'Program Studi tidak ditemukan' }}" 
                                       disabled 
                                       class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base text-gray-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Program Studi tidak dapat diubah</p>
                        </div>

                        <!-- Angkatan -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="batch" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Angkatan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                </div>
                                <select name="batch" id="batch" 
                                        class="w-full border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('batch') border-red-500 @enderror appearance-none">
                                    <option value="">-- Pilih Angkatan --</option>
                                    @for($y = date('Y'); $y >= 1990; $y--)
                                        <option value="{{ substr($y, -2) }}" {{ old('batch', $alumni->batch) == substr($y, -2) ? 'selected' : '' }}>
                                            {{ substr($y, -2) }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            @error('batch')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Tahun Lulus -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="graduation_year" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Tahun Lulus <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-calendar-check text-gray-400 text-sm"></i>
                                </div>
                                <select name="graduation_year" id="graduation_year" 
                                        class="w-full border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('graduation_year') border-red-500 @enderror appearance-none">
                                    <option value="">-- Pilih Tahun Lulus --</option>
                                    @for($y = date('Y'); $y >= 1990; $y--)
                                        <option value="{{ $y }}" {{ old('graduation_year', $alumni->graduation_year) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            @error('graduation_year')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- IPK -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="ipk" class="block text-sm sm:text-base font-semibold text-gray-700">IPK</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-chart-line text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" name="ipk" id="ipk" 
                                       value="{{ old('ipk', $alumni->ipk) }}" placeholder="Contoh: 3.75" disabled
                                       class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base text-gray-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">IPK tidak dapat diubah</p>
                            @error('ipk')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status Pekerjaan -->
                        <div class="space-y-1 sm:space-y-2">
                            <label for="status" class="block text-sm sm:text-base font-semibold text-gray-700">
                                Status Pekerjaan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-briefcase text-gray-400 text-sm"></i>
                                </div>
                                <select name="status" id="status"
                                        class="w-full border border-gray-300 pl-10 pr-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror appearance-none">
                                    <option value="">-- Pilih Status Pekerjaan --</option>
                                    <option value="bekerja" {{ old('status', $alumni->status) == 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                                    <option value="tidak bekerja" {{ old('status', $alumni->status) == 'tidak bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                    <option value="melanjutkan studi" {{ old('status', $alumni->status) == 'melanjutkan studi' ? 'selected' : '' }}>Melanjutkan Studi</option>
                                    <option value="berwiraswasta" {{ old('status', $alumni->status) == 'berwiraswasta' ? 'selected' : '' }}>Berwiraswasta</option>
                                    <option value="sedang mencari kerja" {{ old('status', $alumni->status) == 'sedang mencari kerja' ? 'selected' : '' }}>Sedang Mencari Kerja</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            @error('status')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Required Fields Notice --}}
                    <div class="mt-6 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5 flex-shrink-0"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Informasi Penting:</p>
                                <p class="mt-1">Field yang ditandai dengan tanda <span class="text-red-500 font-bold">*</span> wajib diisi. Pastikan semua informasi yang Anda masukkan sudah benar.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                        <a href="{{ route('dashboard.alumni') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-save mr-2"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Enhanced CSS for better responsiveness -->
<style>
    /* Custom select dropdown styling */
    select {
        background-image: none;
    }
    
    /* Focus states enhancement */
    input:focus, select:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Mobile specific improvements */
    @media (max-width: 640px) {
        .grid {
            gap: 1rem;
        }
        
        input, select {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
    
    /* Combobox dropdown styling */
    #prodi-list {
        max-height: 12rem;
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }
    
    #prodi-list::-webkit-scrollbar {
        width: 4px;
    }
    
    #prodi-list::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #prodi-list::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 2px;
    }
    
    /* Animation for form validation */
    .border-red-500 {
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>

<!-- script JS  -->
<script src="{{ asset('js/alumni.js') }}"></script>
@endsection
