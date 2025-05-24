@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Tambah Periode Kuisioner</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <form action="{{ route('admin.questionnaire.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="bg-blue-50 p-4 rounded-md">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                Status akan otomatis ditentukan berdasarkan tanggal:
                            </p>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                <li>Inactive: Jika tanggal saat ini sebelum tanggal mulai</li>
                                <li>Active: Jika tanggal saat ini di antara tanggal mulai dan selesai</li>
                                <li>Expired: Jika tanggal saat ini setelah tanggal selesai</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.questionnaire.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.add('hidden');
    });

    document.getElementById('profile-toggle').addEventListener('click', () => {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

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
