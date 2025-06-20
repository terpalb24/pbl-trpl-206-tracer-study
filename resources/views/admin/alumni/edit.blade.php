@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Edit Alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Back button - responsive -->
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('admin.alumni.index') }}" 
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 text-sm sm:text-base transition-colors">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar Alumni</span>
            </a>
        </div>

        <!-- Form container -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <!-- Header section -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Data Alumni</h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">
                            NIM: <span class="font-semibold">{{ $alumni->nim }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="bi bi-info-circle"></i>
                        <span class="hidden sm:inline">Pastikan data yang diisi sudah benar</span>
                        <span class="sm:hidden">Periksa data dengan teliti</span>
                    </div>
                </div>
            </div>

            <!-- Form section -->
            <form action="{{ route('admin.alumni.update', $alumni->nim) }}" method="POST" 
                class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                @csrf
                @method('PUT')

                <!-- Grid layout responsive -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- NIM (readonly) -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="nim" class="block text-sm sm:text-base font-semibold text-gray-700">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nim" id="nim" 
                            value="{{ old('nim', $alumni->nim) }}" readonly
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-gray-200 focus:border-gray-300">
                        @error('nim')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIK -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="nik" class="block text-sm sm:text-base font-semibold text-gray-700">
                            NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik" id="nik" 
                            value="{{ old('nik', $alumni->nik) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Masukkan NIK">
                        @error('nik')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="name" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                            value="{{ old('name', $alumni->name) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="email" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" 
                            value="{{ old('email', $alumni->email) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="contoh@email.com">
                        @error('email')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="phone_number" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone_number" id="phone_number" 
                            value="{{ old('phone_number', $alumni->phone_number) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="08xxxxxxxxxx">
                        @error('phone_number')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="gender" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" id="gender" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="pria" {{ old('gender', $alumni->gender) == 'pria' ? 'selected' : '' }}>
                                Pria
                            </option>
                            <option value="wanita" {{ old('gender', $alumni->gender) == 'wanita' ? 'selected' : '' }}>
                                Wanita
                            </option>
                        </select>
                        @error('gender')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="date_of_birth" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" 
                            value="{{ old('date_of_birth', $alumni->date_of_birth) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        @error('date_of_birth')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program Studi -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="id_study" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Program Studi <span class="text-red-500">*</span>
                        </label>
                        <select name="id_study" id="id_study" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($prodi as $prodis)
                                <option value="{{ $prodis->id_study }}" 
                                    {{ old('id_study', $alumni->id_study) == $prodis->id_study ? 'selected' : '' }}>
                                    {{ $prodis->study_program }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_study')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="status" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Status Kelulusan <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Pilih Status --</option>
                            <option value="bekerja" {{ old('status', $alumni->status) == 'bekerja' ? 'selected' : '' }}>
                                Bekerja
                            </option>
                            <option value="tidak bekerja" {{ old('status', $alumni->status) == 'tidak bekerja' ? 'selected' : '' }}>
                                Tidak Bekerja
                            </option>
                            <option value="melanjutkan studi" {{ old('status', $alumni->status) == 'melanjutkan studi' ? 'selected' : '' }}>
                                Melanjutkan Studi
                            </option>
                            <option value="berwiraswasta" {{ old('status', $alumni->status) == 'berwiraswasta' ? 'selected' : '' }}>
                                Berwiraswasta
                            </option>
                            <option value="sedang mencari kerja" {{ old('status', $alumni->status) == 'sedang mencari kerja' ? 'selected' : '' }}>
                                Sedang Mencari Kerja
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- IPK -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="ipk" class="block text-sm sm:text-base font-semibold text-gray-700">
                            IPK <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" max="4" name="ipk" id="ipk" 
                            value="{{ old('ipk', $alumni->ipk) }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="3.50">
                        @error('ipk')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Angkatan -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="batch" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Angkatan <span class="text-red-500">*</span>
                        </label>
                        <select name="batch" id="batch" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Pilih Angkatan --</option>
                            @for($y = date('Y'); $y >= 1990; $y--)
                                <option value="{{ substr($y, -2) }}" 
                                    {{ old('batch', $alumni->batch) == substr($y, -2) ? 'selected' : '' }}>
                                    {{ substr($y, -2) }} ({{ $y }})
                                </option>
                            @endfor
                        </select>
                        @error('batch')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Lulus -->
                    <div class="space-y-1 sm:space-y-2">
                        <label for="graduation_year" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Tahun Lulus <span class="text-red-500">*</span>
                        </label>
                        <select name="graduation_year" id="graduation_year" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">-- Pilih Tahun Lulus --</option>
                            @for($y = date('Y'); $y >= 1990; $y--)
                                <option value="{{ $y }}" 
                                    {{ old('graduation_year', $alumni->graduation_year) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        @error('graduation_year')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat - Full width on larger screens -->
                    <div class="md:col-span-2 space-y-1 sm:space-y-2">
                        <label for="address" class="block text-sm sm:text-base font-semibold text-gray-700">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" id="address" rows="3" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                            placeholder="Masukkan alamat lengkap">{{ old('address', $alumni->address) }}</textarea>
                        @error('address')
                            <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action buttons - Responsive -->
                <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-end">
                        <a href="{{ route('admin.alumni.index') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 rounded-md text-sm sm:text-base font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors order-2 sm:order-1">
                            <i class="bi bi-arrow-left mr-2"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent rounded-md text-sm sm:text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors order-1 sm:order-2">
                            <i class="bi bi-check-circle mr-2"></i>
                            <span>Simpan Perubahan</span>
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
