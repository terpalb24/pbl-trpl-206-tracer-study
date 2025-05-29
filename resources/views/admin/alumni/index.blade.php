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
                <h1 class="text-2xl font-bold text-blue-800">Alumni</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">Administrator</p>
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

        <!-- Import & Export Excel Form -->
        <div class="bg-white rounded-xl shadow-md p-6 md:p-10 mb-6 mt-4 mx-6">
            <form action="{{ route('admin.alumni.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row md:items-center gap-4">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required
                    class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/2 text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit"
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                    <i class="bi bi-upload"></i> Import Excel
                </button>
                <a href="{{ route('admin.alumni.export') }}"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                    <i class="bi bi-download"></i> Export Excel
                </a>
            </form>
            @if(session('error'))
                <div class="text-red-500 mt-4">{{ session('error') }}</div>
            @endif

            @error('file')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-700">Daftar Alumni</h2>
                <a href="{{ route('admin.alumni.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    + Tambah Alumni
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 flex flex-col md:flex-row md:items-center gap-4 border-b">
                    <form method="GET" action="{{ route('admin.alumni.index') }}" class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                        <select name="graduation_year" class="border border-gray-300 rounded px-3 py-2 w-full md:w-44">
                            <option value="">-- Semua Tahun Lulus --</option>
                            @foreach($tahunLulus as $tahun)
                                <option value="{{ $tahun }}" {{ request('graduation_year') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                        <select name="id_study" class="border border-gray-300 rounded px-3 py-2 w-full md:w-56">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($prodi as $p)
                                <option value="{{ $p->id_study }}" {{ request('id_study') == $p->id_study ? 'selected' : '' }}>
                                    {{ $p->study_program }}
                                </option>
                            @endforeach
                        </select>
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Alumni"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="bi bi-search"></i>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded font-semibold">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-center">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">NIM</th>
                                <th class="px-4 py-3">Program Studi</th>
                                <th class="px-4 py-3">Nama Alumni</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($alumni as $index => $item)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ ($alumni->currentPage() - 1) * $alumni->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ $item->nim }}</td>
                                    <td class="px-4 py-3">{{ $item->studyProgram->study_program ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->name }}</td>
                                    <td class="px-4 py-3 flex justify-center space-x-2">
                                        <a href="{{ route('admin.alumni.edit', $item->nim) }}" 
                                           class="px-3 py-1 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm flex items-center justify-center"
                                           title="Edit">
                                           <i class="bi bi-pencil-square mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.alumni.destroy', $item->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alumni ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="px-3 py-1 rounded-md bg-red-500 hover:bg-red-600 text-white text-sm flex items-center justify-center" 
                                                title="Hapus">
                                                <i class="bi bi-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-gray-400 text-center">tidak ada alumni</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t text-sm text-gray-500">
                    {{ $alumni->withQueryString()->links() }}
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
