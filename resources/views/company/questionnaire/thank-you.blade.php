@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('company.sidebar')
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
                <h1 class="text-2xl font-bold text-blue-800">Kuesioner Selesai</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/company-icon.png') }}" alt="Company Logo" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Company' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Perusahaan</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-100 text-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-10 text-center">
                <div class="mb-6 text-green-600">
                    <i class="fas fa-check-circle text-7xl"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">Terima Kasih</h2>
                <p class="text-xl text-gray-600 mb-8">
                    Jawaban Anda telah berhasil disimpan. Terima kasih telah berpartisipasi dalam Tracer Study Polibatam.
                </p>
                <div class="flex justify-center space-x-4">
                    <!-- FIX: Changed from company.questionnaires.index to company.questionnaire.index -->
                    <a href="{{ route('company.questionnaire.index') }}" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kuesioner
                    </a>
                    <a href="{{ route('dashboard.company') }}" class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });
    
    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });
    
    document.getElementById('profile-toggle')?.addEventListener('click', function() {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });
    
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        
        if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    document.getElementById('logout-btn')?.addEventListener('click', function(event) {
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
