@extends('layouts.app')
@php
    $company = auth()->user()->company;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar-menu w-40 lg:w-1/6 bg-blue-950 text-white flex flex-col p-4 transition-all duration-300" id="sidebar">
    <div class="flex items-center justify-between mb-6">
    <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-32 object-contain">
    <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>

      
<nav class="flex flex-col gap-3">
    <a href="#" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition">
        <i class="fas fa-home w-5 text-center"></i> <span>Beranda</span>
    </a>
    <a href="#" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition">
        <i class="fas fa-file-alt w-5 text-center"></i> <span>Kuisioner</span>
    </a>
    <a href="#" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition">
        <i class="fas fa-history w-5 text-center"></i> <span>Riwayat</span>
    </a>
    <a href="#" class="flex items-center gap-3 px-2 py-2 rounded hover:bg-blue-800 transition">
        <i class="fas fa-user w-5 text-center"></i> <span>Profil</span>
    </a>
</nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-4 md:p-6 overflow-y-auto" id="main-content">
         <!-- Header -->
         <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-gray-800"></i>
                </button>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Beranda</h1>
            </div>

            <!-- Profile Dropdown Button -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-full px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="https://i.pravatar.cc/40" class="rounded-full w-10 h-10" alt="Profile" />
                    <div class="text-left hidden sm:block">
                        <p class="font-semibold leading-none">{{ $company->company_name }}</p>
                        <p class="text-sm text-gray-300 leading-none">Alumni</p>
                    </div>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <!-- Dropdown Menu -->
                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>


        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="p-4 bg-gradient-to-r from-white via-sky-100 to-orange-300 rounded shadow">
                <div class="font-semibold">Selamat Datang!</div>
                <div class="text-sm">{{ $company->company_name }}</div>
            </div>
            <div class="flex items-center p-4 bg-blue-800 text-white rounded shadow gap-4">
                <i class="fas fa-user-graduate text-2xl"></i>
                <div>
                    <div class="text-xl font-bold">2.500</div>
                    <div class="text-sm">Alumni</div>
                </div>
            </div>
            <div class="flex items-center p-4 bg-sky-400 text-white rounded shadow gap-4">
                <i class="fas fa-building text-2xl"></i>
                <div>
                    <div class="text-xl font-bold">80</div>
                    <div class="text-sm">Perusahaan</div>
                </div>
            </div>
            <div class="flex items-center p-4 bg-orange-500 text-white rounded shadow gap-4">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <div class="text-xl font-bold">2.300</div>
                    <div class="text-sm">Mengisi Kuisioner</div>
                </div>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="bg-white shadow rounded p-4 md:p-6 flex flex-col items-start">
            <div class="flex flex-col md:flex-row gap-6 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm flex-grow">
                    <div><strong>Nama Perusahaan</strong>: {{ $company->company_name }}</div>
                    <div><strong>Alamat</strong>: {{ $company->company_address }}</div>
                    <div><strong>Nomor Telepon</strong>: {{ $company->company_phone_number }}</div>
                    <div><strong>Email</strong>: {{ $company->company_email }}</div>
                </div>
            </div>
        </div>
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