@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Tambah Perusahaan</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Back button - responsive -->
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('admin.company.index') }}" 
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 text-sm sm:text-base transition-colors">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar Perusahaan</span>
            </a>
        </div>

        <!-- Form container -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <!-- Header section -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tambah Data Perusahaan</h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">
                            Lengkapi formulir di bawah untuk menambah perusahaan baru
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="bi bi-info-circle"></i>
                        <span class="hidden sm:inline">Semua field yang bertanda (*) wajib diisi</span>
                        <span class="sm:hidden">Field (*) wajib diisi</span>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 p-3 sm:p-4 bg-green-100 text-green-700 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form section -->
            <form action="{{ route('admin.company.store') }}" method="POST" 
                class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                @csrf

                <!-- Grid layout responsive -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Nama Perusahaan -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="company_name" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" 
                            value="{{ old('company_name') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Masukkan nama perusahaan">
                        @error('company_name')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Perusahaan -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="company_email" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Email Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="company_email" id="company_email" 
                            value="{{ old('company_email') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="info@perusahaan.com">
                        @error('company_email')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="company_phone_number" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_phone_number" id="company_phone_number" 
                            value="{{ old('company_phone_number') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="021-xxxxxxxx atau 08xxxxxxxxxx">
                        @error('company_phone_number')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama HRD -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="Hrd_name" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Nama HRD <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="Hrd_name" id="Hrd_name" 
                            value="{{ old('Hrd_name') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nama Hrd Perusahaan">
                        @error('Hrd_name')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror

                       

                    </div>

                    <!-- Alamat Perusahaan - Full width on larger screens -->
                    <div class="md:col-span-2 space-y-1 sm:space-y-2">
                        <label for="company_address" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Alamat Lengkap Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="company_address" id="company_address" rows="3" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                            placeholder="Masukkan alamat lengkap perusahaan">{{ old('company_address') }}</textarea>
                        @error('company_address')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action buttons - Responsive -->
                <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-end">
                        <a href="{{ route('admin.company.index') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 rounded-md text-sm sm:text-base font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors order-2 sm:order-1">
                            <i class="bi bi-arrow-left mr-2"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent rounded-md text-sm sm:text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors order-1 sm:order-2">
                            <i class="bi bi-plus-circle mr-2"></i>
                            <span>Tambah Perusahaan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- script JS  -->
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection



