@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="editprofil-container">

    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar/>
    
    {{-- Main Content --}}
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">
        {{-- Header --}}
        <x-alumni.header title="Edit Profil" />

        <!-- Content Section -->
        <div class="p-4 lg:p-6 max-w-6xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('dashboard.alumni') }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors">Dashboard</a></li>
                    <li><span class="text-gray-400">/</span></li>
                    <li class="text-gray-700 font-medium">Edit Profil</li>
                </ol>
            </nav>

            {{-- Pesan Flash --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center text-sm shadow-sm">
                    <i class="fas fa-check-circle mr-3 text-green-600 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center text-sm shadow-sm">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Profile Header Card --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-200 card">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center overflow-hidden profile-picture relative">
                            <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil"
                                 class="absolute inset-0 w-full h-full object-cover rounded-full" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $alumni->name }}</h2>
                        <p class="text-base text-gray-600 mb-3">{{ session('alumni_nim') }}</p>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full badge">
                                <i class="fas fa-graduation-cap mr-1.5"></i>
                                Angkatan {{ $alumni->batch }}
                            </span>
                            @if($alumni->graduation_year)
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full badge">
                                    <i class="fas fa-calendar mr-1.5"></i>
                                    Lulus {{ $alumni->graduation_year }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Edit Profil --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 card">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-edit mr-3 text-blue-600"></i>
                        Edit Informasi Profil
                    </h3>
                    <p class="text-sm text-gray-600 mt-2">Perbarui informasi profil Anda di bawah ini</p>
                </div>

                <form action="{{ route('alumni.update') }}" method="POST" class="p-6" id="editProfileForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- NIM -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">NIM</label>
                            <div class="relative">
                                <input type="text" value="{{ session('alumni_nim') }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">NIM tidak dapat diubah</p>
                        </div>

                        <!-- Kelamin -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                            <div class="relative">
                                <input type="text" value="{{ $alumni->gender }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Jenis kelamin tidak dapat diubah</p>
                        </div>
                        
                        <!-- Nama -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                            <div class="relative">
                                <input type="text" value="{{ $alumni->name }}" disabled 
                                       class="w-full bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Nama tidak dapat diubah</p>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-2">
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" name="phone_number" id="phone_number" 
                                       value="{{ old('phone_number', $alumni->phone_number) }}"
                                    class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors {{ $errors->has('phone_number') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
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
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                </div>
                                <input type="email" name="email" id="email" 
                                       value="{{ old('email', $alumni->email) }}"
                                       class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors {{ $errors->has('email') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                                       placeholder="Contoh: nama@email.com">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="space-y-2 lg:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-0 pl-3 flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-sm mt-0.5"></i>
                                </div>
                                <textarea name="address" id="address" rows="3"
                                          class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none {{ $errors->has('address') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                                          placeholder="Masukkan alamat lengkap Anda">{{ old('address', $alumni->address) }}</textarea>
                            </div>
                            @error('address')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Prodi -->
                        <div class="space-y-2">
                            <label for="id_study" class="block text-sm font-semibold text-gray-700">
                                Program Studi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-graduation-cap text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" 
                                       value="{{ $alumni->studyProgram->study_program ?? 'Program Studi tidak ditemukan' }}" 
                                       disabled 
                                       class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
                                <input type="hidden" name="id_study" value="{{ $alumni->id_study }}">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Program Studi tidak dapat diubah</p>
                        </div>

                        <!-- Angkatan -->
                        <div class="space-y-2">
                            <label for="batch" class="block text-sm font-semibold text-gray-700">
                                Angkatan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                </div>
                                <select name="batch" id="batch" 
                                        class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none {{ $errors->has('batch') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
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
                        <div class="space-y-2">
                            <label for="graduation_year" class="block text-sm font-semibold text-gray-700">
                                Tahun Lulus <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-calendar-check text-gray-400 text-sm"></i>
                                </div>
                                <select name="graduation_year" id="graduation_year" 
                                        class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none {{ $errors->has('graduation_year') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
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
                        <div class="space-y-2">
                            <label for="ipk" class="block text-sm font-semibold text-gray-700">IPK</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-chart-line text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" name="ipk" id="ipk" 
                                       value="{{ old('ipk', $alumni->ipk) }}" placeholder="Contoh: 3.75" disabled
                                       class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
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
                        @php
                            // Cek apakah ada jobhistory dengan 'duration' = "Masih bekerja"
                            $hasCurrentJob = $alumni->jobHistories()->where('duration', "Masih bekerja")->exists();
                        @endphp
                        <!-- Status Pekerjaan -->
                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-semibold text-gray-700">
                                Status Pekerjaan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-briefcase text-gray-400 text-sm"></i>
                                </div>
                                @if($hasCurrentJob)
                                    <input type="text"
                                           value="{{ ucfirst($alumni->status) }}"
                                           disabled
                                           class="w-full bg-gray-50 border border-gray-300 pl-10 pr-4 py-3 rounded-lg text-sm text-gray-500 font-medium">
                                    <input type="hidden" name="status" value="{{ $alumni->status }}">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-lock text-gray-400 text-sm"></i>
                                    </div>
                                @else
                                    <select name="status" id="status"
                                            class="w-full border pl-10 pr-4 py-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none {{ $errors->has('status') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
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
                                @endif
                            </div>
                            @if($hasCurrentJob)
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-2">
                                    <p class="text-xs text-blue-700 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Status pekerjaan tidak dapat diubah karena Anda menandai "Saya saat ini sedang bekerja di perusahaan ini" pada riwayat pekerjaan.
                                    </p>
                                </div>
                            @endif
                            @error('status')
                                <p class="text-red-500 text-xs flex items-center mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Required Fields Notice --}}
                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Informasi Penting:</p>
                                <p class="mt-1">Field yang ditandai dengan tanda <span class="text-red-500 font-bold">*</span> wajib diisi. Pastikan semua informasi yang Anda masukkan sudah benar.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('dashboard.alumni') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm sm:text-base shadow-sm hover:shadow-md">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 text-sm sm:text-base shadow-sm hover:shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Enhanced CSS for better responsiveness and styling -->
<style>
    /* Custom styling for form elements */
    .form-input {
        transition: all 0.2s ease-in-out;
    }
    
    .form-input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
    }
    
    /* Custom select dropdown styling */
    select {
        background-image: none;
        cursor: pointer;
    }
    
    select:disabled {
        cursor: not-allowed;
    }
    
    /* Enhanced focus states */
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
        outline: none;
    }
    
    /* Disabled state styling */
    input:disabled, select:disabled {
        background-color: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }
    
    /* Error state enhancements */
    .border-red-500 {
        animation: shake 0.5s ease-in-out;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .bg-red-50 {
        background-color: #fef2f2;
    }
    
    /* Button hover effects */
    button, .btn {
        transition: all 0.2s ease-in-out;
    }
    
    button:hover, .btn:hover {
        transform: translateY(-1px);
    }
    
    /* Profile picture styling */
    .profile-picture {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    /* Card styling */
    .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease-in-out;
    }
    
    .card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    /* Badge styling */
    .badge {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }
    
    /* Mobile specific improvements */
    @media (max-width: 640px) {
        .grid {
            gap: 1.5rem;
        }
        
        input, select, textarea {
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        .profile-picture {
            width: 4rem;
            height: 4rem;
        }
    }
    
    /* Animation for form validation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Loading state for form submission */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Improved responsive grid */
    @media (min-width: 1024px) {
        .lg\:grid-cols-2 > div:nth-child(3) {
            grid-column: span 2;
        }
    }
    
    /* Form section dividers */
    .form-section {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
</style>

<!-- script JS  -->
<script src="{{ asset('js/alumni.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form validation and UX
    const form = document.getElementById('editProfileForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Add form-input class to all inputs for enhanced styling
    const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
    inputs.forEach(input => {
        if (!input.disabled) {
            input.classList.add('form-input');
        }
    });
    
    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        // Validate required fields
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500', 'bg-red-50');
                
                // Add shake animation
                field.addEventListener('animationend', function() {
                    field.classList.remove('border-red-500');
                }, { once: true });
            } else {
                field.classList.remove('border-red-500', 'bg-red-50');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            
            // Reset button state
            submitBtn.classList.remove('btn-loading');
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3b82f6'
                });
            }
            
            // Scroll to first error
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => firstError.focus(), 300);
            }
        }
    });
    
    // Real-time validation for required fields
    inputs.forEach(input => {
        if (input.hasAttribute('required')) {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('border-red-500', 'bg-red-50');
                } else {
                    this.classList.remove('border-red-500', 'bg-red-50');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-500', 'bg-red-50');
                }
            });
        }
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove all non-digits
            let value = e.target.value.replace(/\D/g, '');
            
            // Format Indonesian phone number
            if (value.length > 0) {
                if (value.startsWith('62')) {
                    // International format
                    value = '+' + value;
                } else if (value.startsWith('0')) {
                    // Local format - keep as is
                } else if (value.length >= 10) {
                    // Assume it's missing the 0
                    value = '0' + value;
                }
            }
            
            e.target.value = value;
        });
    }
    
    // Auto-resize textarea
    const addressTextarea = document.getElementById('address');
    if (addressTextarea) {
        addressTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Initial resize
        addressTextarea.style.height = 'auto';
        addressTextarea.style.height = (addressTextarea.scrollHeight) + 'px';
    }
    
    // Enhanced hover effects for disabled fields
    const disabledFields = form.querySelectorAll('[disabled]');
    disabledFields.forEach(field => {
        field.addEventListener('mouseenter', function() {
            const tooltip = this.parentElement.querySelector('.text-gray-500');
            if (tooltip) {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(-2px)';
            }
        });
        
        field.addEventListener('mouseleave', function() {
            const tooltip = this.parentElement.querySelector('.text-gray-500');
            if (tooltip) {
                tooltip.style.opacity = '0.7';
                tooltip.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Smooth scroll to form errors on page load
    if (document.querySelector('.text-red-500')) {
        setTimeout(() => {
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 500);
    }
    
    // Add success animation if form was submitted successfully
    @if(session('success'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10b981'
            });
        }
    @endif
});
</script>
@endsection
