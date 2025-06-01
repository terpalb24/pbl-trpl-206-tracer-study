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
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Edit Profil</h1>
            </div>

            {{-- Profile Dropdown Komponen --}}
            <x-alumni.profile-dropdown :alumni="$alumni" />
        </div>

        {{-- Pesan Flash --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Edit Profil --}}
        <form action="{{ route('alumni.update') }}" method="POST" class="bg-white rounded-2xl shadow-md mt-8 mx-4 md:mx-10 lg:mx-16 xl:mx-24 p-6 md:p-10 lg:p-12 xl:p-16">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 text-sm ">
                <!-- NIM -->
                <div>
                    <label class="block font-semibold mb-1">NIM</label>
                    <input type="text" value="{{ session('alumni_nim') }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
                </div>

                <!-- Kelamin -->
                <div>
                    <label class="block font-semibold mb-1">Kelamin</label>
                    <input type="text" value="{{ $alumni->gender }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
                </div>
                
                <!-- Nama -->
                <div>
                    <label class="block font-semibold mb-1">Nama</label>
                    <input type="text" value="{{ $alumni->name }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
                </div>

                <!-- Nomor Telepon -->
                <div>
                    <label for="phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $alumni->phone_number) }}"
                        class="w-full border px-3 py-2 rounded @error('phone_number') border-red-500 @enderror">
                    @error('phone_number')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block font-semibold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $alumni->email) }}"
                        class="w-full border px-3 py-2 rounded @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prodi -->
                <div>
                    <label for="id_study" class="block font-semibold mb-1">Prodi</label>

                    <!-- Select asli, disembunyikan -->
                    <select name="id_study" id="id_study" class="hidden">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach(App\Models\Tb_study_program::all() as $program)
                            <option value="{{ $program->id_study }}"
                                {{ old('id_study', $alumni->id_study) == $program->id_study ? 'selected' : '' }}>
                                {{ $program->study_program }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Input combobox -->
                    <div class="relative">
                        <input type="text" id="prodi-combobox" class="w-full border px-3 py-2 rounded @error('id_study') border-red-500 @enderror"
                            placeholder="Ketik untuk mencari Prodi" autocomplete="off" />
                        <div id="prodi-list" 
                            class="absolute z-50 w-full max-h-48 overflow-auto border border-gray-300 bg-white rounded mt-1 hidden"></div>
                    </div>

                    @error('id_study')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Angkatan -->
                <div>
                    <label for="batch" class="block font-semibold mb-1">Angkatan</label>
                    <input type="number" name="batch" value="{{ old('batch', $alumni->batch) }}"
                        class="w-full border px-3 py-2 rounded @error('batch') border-red-500 @enderror">
                    @error('batch')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Lulus -->
                <div>
                    <label for="graduation_year" class="block font-semibold mb-1">Tahun Lulus</label>
                    <input type="number" name="graduation_year" value="{{ old('graduation_year', $alumni->graduation_year) }}"
                        class="w-full border px-3 py-2 rounded @error('graduation_year') border-red-500 @enderror">
                    @error('graduation_year')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IPK -->
                <div>
                    <label for="ipk" class="block font-semibold mb-1">IPK</label>
                    <input type="text" name="ipk" value="{{ old('ipk', $alumni->ipk) }}" placeholder="e.g. 4.00" disabled
                        class="w-full bg-gray-100 border px-3 py-2 rounded">
                    @error('ipk')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Pekerjaan -->
                <div>
                    <label for="status" class="block font-semibold mb-1">Status Pekerjaan</label>
                    <select name="status" id="status"
                        class="w-full border px-3 py-2 rounded @error('status') border-red-500 @enderror">
                        <option value="">-- Pilih Status --</option>
                        <option value="Bekerja" {{ old('status', $alumni->status) == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                        <option value="Tidak Bekerja" {{ old('status', $alumni->status) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                        <option value="Melanjutkan Studi" {{ old('status', $alumni->status) == 'Melanjutkan Studi' ? 'selected' : '' }}>Melanjutkan Studi</option>
                        <option value="Berwiraswasta" {{ old('status', $alumni->status) == 'Berwiraswasta' ? 'selected' : '' }}>Berwiraswasta</option>
                        <option value="Sedang Mencari Kerja" {{ old('status', $alumni->status) == 'Sedang Mencari Kerja' ? 'selected' : '' }}>Sedang Mencari Kerja</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('dashboard.alumni') }}"
                    class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </main>
</div>

   <!-- script JS  -->
           <script src="{{ asset('js/alumni.js') }}"></script>
@endsection
