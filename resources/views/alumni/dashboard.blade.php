@extends('layouts.app')
@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<!-- Add CSRF token for logout functionality -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
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

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Beranda</h1>
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

        <!-- Content -->
        <div class="p-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <div class="p-6 md:w-2/3">
                        <h2 class="text-5xl font-bold text-black mb-2">Halo!</h2>
                        <h3 class="text-4xl font-semibold text-black mb-4">{{ $alumni->name }}</h3>
                        <p class="text-2xl font-normal text-black mb-6">
                            Silahkan isi kuisioner Tracer Study untuk membantu pengembangan Polibatam!!
                        </p>
                        <a href="#" class="bg-blue-900 text-white px-6 py-2 rounded-md flex items-center space-x-2 w-fit">
                            <span>Isi Kuisioner</span>
                            <i class="fas fa-file-alt"></i>
                        </a>
                    </div>
                    <div class="md:w-1/3 flex items-center justify-center p-4">
                        <img src="{{ asset('assets/images/graduation.png') }}" alt="Graduation" class="h-64 w-64 object-cover">
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Profil Saya</h2>
                    <a href="{{ route('alumni.edit') }}" class="bg-blue-900 text-white px-4 py-2 rounded-md flex items-center space-x-2">
                        <span>Edit Profile</span>
                        <i class="fas fa-edit"></i>
                    </a>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/4 mb-6 md:mb-0 flex justify-center">
                        <div class="bg-blue-200 rounded-lg w-40 h-40 overflow-hidden">
                            <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Profile Picture" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="md:w-3/4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">NIM</h3>
                            <p class="font-semibold">{{ $alumni->nim }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Jenis Kelamin</h3>
                            <p class="font-semibold">{{ $alumni->gender }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Nama</h3>
                            <p class="font-semibold">{{ $alumni->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Email</h3>
                            <p class="font-semibold">{{ $alumni->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">No Telp</h3>
                            <p class="font-semibold">{{ $alumni->phone_number }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Prodi</h3>
                            <p class="font-semibold">{{ $alumni->studyProgram->study_program }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Angkatan</h3>
                            <p class="font-semibold">{{ $alumni->batch }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Tahun Lulus</h3>
                            <p class="font-semibold">{{ $alumni->graduation_year }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">IPK</h3>
                            <p class="font-semibold">{{ $alumni->ipk }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-500 mb-1">Status Pekerjaan</h3>
                            <p class="font-semibold">{{ $alumni->status }}</p>
                        </div>
                    </div>
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
