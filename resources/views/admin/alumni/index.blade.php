@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <!-- Sidebar -->
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <!-- Header -->
    <x-slot name="header">
        <x-admin.header>Alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Import & Export Excel Section -->
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

    <div class="p-6">
        <!-- Judul & Tombol Tambah Alumni -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Daftar Alumni</h2>
            <a href="{{ route('admin.alumni.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                + Tambah Alumni
            </a>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Filter & Search Section -->
            <div class="p-4 flex flex-col md:flex-row md:items-center gap-4 border-b">
                <form method="GET" action="{{ route('admin.alumni.index') }}" class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                    <!-- Filter Tahun Lulus -->
                    <select name="graduation_year" class="border border-gray-300 rounded px-3 py-2 w-full md:w-44">
                        <option value="">-- Semua Tahun Lulus --</option>
                        @foreach($tahunLulus as $tahun)
                            <option value="{{ $tahun }}" {{ request('graduation_year') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Filter Program Studi -->
                    <select name="id_study" class="border border-gray-300 rounded px-3 py-2 w-full md:w-56">
                        <option value="">-- Semua Program Studi --</option>
                        @foreach($prodi as $p)
                            <option value="{{ $p->id_study }}" {{ request('id_study') == $p->id_study ? 'selected' : '' }}>
                                {{ $p->study_program }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Search Nama/NIM/Prodi -->
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIM, atau Prodi"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                    <!-- Tombol Cari -->
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded font-semibold">
                        Cari
                    </button>
                </form>
            </div>

            <!-- Tabel Alumni -->
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
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.alumni.edit', $item->nim) }}" 
                                        class="px-3 py-1 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm flex items-center justify-center"
                                        title="Edit">
                                        <i class="bi bi-pencil-square mr-1"></i>Edit
                                    </a>
                                    <!-- Tombol Hapus -->
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

            <!-- Pagination -->
            <div class="p-4 border-t text-sm text-gray-500">
                {{ $alumni->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- script JS  -->
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
