@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden">

    <!-- Sidebar -->
    <aside class="sidebar-menu w-40 lg:w-1/6 bg-blue-950 text-white flex flex-col p-4 md:p-6 hidden lg:block" id="sidebar">
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-32 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @include('alumni.navbar')
    </aside>

    <!-- Tombol Toggle Sidebar (Untuk Mobile) -->
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i> <!-- Ikon hamburger menu -->
    </button>

    <!-- Main Content -->
    <main class="flex-grow p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Edit Profil</h1>
        </div>

        <!-- Pesan Flash -->
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

        <!-- Form Edit Profil -->
        <form action="{{ route('alumni.update') }}" method="POST" class="bg-white shadow rounded p-4 md:p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">

                <!-- NIM -->
                <div>
                    <label class="block font-semibold mb-1">NIM</label>
                    <input type="text" value="{{ session('alumni_nim') }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
                </div>

                <!-- Nama -->
                <div>
                    <label class="block font-semibold mb-1">Nama</label>
                    <input type="text" value="{{ $alumni->name }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
                </div>

                <!-- Kelamin -->
                <div>
                    <label class="block font-semibold mb-1">Kelamin</label>
                    <input type="text" value="{{ $alumni->gender }}" disabled class="w-full bg-gray-100 border px-3 py-2 rounded">
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
                    <label for="study_program_id" class="block font-semibold mb-1">Prodi</label>
                    <select name="study_program_id" class="w-full border px-3 py-2 rounded @error('study_program_id') border-red-500 @enderror">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach(App\Models\Tb_study_program::all() as $program)
                            <option value="{{ $program->id }}" {{ old('study_program_id', $alumni->study_program_id) == $program->id ? 'selected' : '' }}>
                                {{ $program->study_program }}
                            </option>
                        @endforeach
                    </select>
                    @error('study_program_id')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
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
                    <input type="text" name="ipk" value="{{ old('ipk', $alumni->ipk) }}" placeholder="e.g. 4.00"
                        class="w-full border px-3 py-2 rounded @error('ipk') border-red-500 @enderror">
                    @error('ipk')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Pekerjaan -->
                <div>
                    <label for="employment_status" class="block font-semibold mb-1">Status Pekerjaan</label>
                    <input type="text" name="status" value="{{ old('employment_status', $alumni->status) }}"
                        class="w-full border px-3 py-2 rounded @error('employment_status') border-red-500 @enderror"
                        placeholder="Masukkan status pekerjaan (worked atau not worked)">
                    @error('employment_status')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Tombol Aksi -->
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
@endsection
