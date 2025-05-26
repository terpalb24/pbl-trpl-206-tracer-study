@extends('layouts.app')

@php
$admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

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
            <h1 class="text-2xl font-bold text-blue-800">Kuisioner</h1>
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
        <!-- First Card - Add Questionnaire Button -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.questionnaire.create') }}" class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-plus"></i> Tambah Kuisioner
                </a>
                <a href="{{ route('admin.questionnaire.import.form', isset($periode) ? $periode->id_periode : 1) }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-file-import"></i> Import Kuisioner
                </a>
                <a href="#" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Second Card - Search and List Questionnaires -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-4 flex justify-between items-center border-b">
                <form method="GET" action="{{ route('admin.questionnaire.index') }}" class="flex items-center space-x-2 w-full max-w-md">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kuisioner"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Target Alumni</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Mulai</th>
                            <th class="px-4 py-3">Tanggal Selesai</th>
                            <th class="px-4 py-3">Jumlah Kategori</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($periodes as $index => $periode)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">{{ ($periodes->currentPage() - 1) * $periodes->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 font-medium">{{ $periode->getTargetDescription() }}</div>
                                    @if($periode->target_type === 'years_ago' && !empty($periode->years_ago_list))
                                        <div class="text-xs text-blue-600 mt-1 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Relatif dengan tahun sekarang ({{ now()->year }})
                                        </div>
                                    @elseif($periode->target_type === 'specific_years' && !empty($periode->target_graduation_years))
                                        <div class="text-xs text-purple-600 mt-1 flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>Tahun kelulusan spesifik
                                        </div>
                                    @elseif($periode->target_type === 'all')
                                        <div class="text-xs text-green-600 mt-1 flex items-center">
                                            <i class="fas fa-users mr-1"></i>Semua alumni dapat mengakses
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                          ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($periode->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $periode->start_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $periode->end_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-blue-600 bg-blue-100 rounded-full">
                                        {{ $periode->categories->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                                           class="text-green-600 hover:text-green-900 font-medium text-sm">
                                            <i class="fas fa-chart-bar mr-1"></i>Respons
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg font-medium mb-1">Belum ada periode kuesioner</p>
                                        <p class="text-sm">Klik tombol "Tambah Periode" untuk membuat kuesioner baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t text-sm text-gray-500">
                {{ $periodes->withQueryString()->links() }}
            </div>
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
