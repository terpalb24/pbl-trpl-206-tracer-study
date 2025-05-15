@extends('layouts.app')

@section('content')
<!-- Add CSRF token for logout functionality -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="editprofil-container">

    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4 ">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex flex-col p-4">
            @include('alumni.navbar')
        </div>
    </aside>

    <!-- Tombol Toggle Sidebar (Untuk Mobile) -->
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i> <!-- Ikon hamburger menu -->
    </button>

        <!-- Main Content -->
        <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Edit Profil</h1>
            </div>

            <!-- Profile Dropdown Button -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <!-- FOTO PROFIL -->
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}"
                        alt="Foto Profil"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                        
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ $alumni->name }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Alumni</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <!-- Dropdown Menu -->
                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
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

<script>
    // Toggle sidebar visibility
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.add('hidden');
    });

    // Toggle profile dropdown
    document.getElementById('profile-toggle').addEventListener('click', function () {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Logout functionality
    document.getElementById('logout-btn').addEventListener('click', function (event) {
        event.preventDefault();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';

        const csrfTokenInput = document.createElement('input');
        csrfTokenInput.type = 'hidden';
        csrfTokenInput.name = '_token';
        csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(csrfTokenInput);
        document.body.appendChild(form);
        form.submit();
    });
</script>
@endsection
