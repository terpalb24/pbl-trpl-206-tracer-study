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

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto">
        <!-- Import & Export Excel Section -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6 mt-3 sm:mt-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Import/Export Data Alumni</h3>
            <form action="{{ route('admin.alumni.import') }}" method="POST" enctype="multipart/form-data" 
                class="flex flex-col gap-3 sm:gap-4">
                @csrf
                
                <!-- File input section -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="border border-gray-300 rounded-md px-3 sm:px-4 py-2 w-full sm:flex-1 text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 file:mr-2 sm:file:mr-4 file:py-1 sm:file:py-2 file:px-2 sm:file:px-4 file:rounded file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    
                    <!-- Action buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button type="submit"
                            class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 sm:px-5 py-2 rounded-md font-semibold text-xs sm:text-sm transition duration-200 whitespace-nowrap">
                            <i class="bi bi-upload"></i> 
                            <span class="hidden sm:inline">Import Excel</span>
                            <span class="sm:hidden">Import</span>
                        </button>
                        
                        <a href="{{ route('admin.alumni.export') }}"
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 py-2 rounded-md font-semibold text-xs sm:text-sm transition duration-200 whitespace-nowrap">
                            <i class="bi bi-download"></i> 
                            <span class="hidden sm:inline">Export Excel</span>
                            <span class="sm:hidden">Export</span>
                        </a>
                        
                        <a href="{{ route('admin.alumni.template') }}"
                            class="flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 sm:px-5 py-2 rounded-md font-semibold text-xs sm:text-sm transition duration-200 whitespace-nowrap">
                            <i class="bi bi-file-earmark-excel"></i> 
                            <span class="hidden sm:inline">Template</span>
                            <span class="sm:hidden">Template</span>
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Error messages -->
            @if(session('error'))
                <div class="text-red-500 mt-3 sm:mt-4 p-3 bg-red-50 rounded-md text-sm">{{ session('error') }}</div>
            @endif
            @error('file')
                <p class="text-red-600 text-sm mt-2 p-2 bg-red-50 rounded">{{ $message }}</p>
            @enderror
        </div>

       <!-- Main content section -->
                <!-- Section: Manajemen Program Studi -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden p-4 sm:p-6 space-y-6">

                    <!-- Header dan Tombol -->
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <h2 class="text-xl font-semibold text-gray-800">Manajemen Program Studi</h2>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <!-- Tombol Toggle Tambah Prodi -->
                            <button type="button" onclick="toggleProdiForm('prodiForm')" 
                                class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2 w-full sm:w-auto">
                                <i class="bi bi-plus-circle"></i>
                                <span>Tambah Prodi</span>
                            </button>
                            <!-- Tombol Toggle Edit Prodi -->
                            <button type="button" onclick="toggleProdiForm('editProdiForm')" 
                                class="inline-flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2 w-full sm:w-auto">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Prodi</span>
                            </button>
                            <!-- Tombol Toggle Hapus Prodi -->
                            <button type="button" onclick="toggleProdiForm('hapusProdiForm')" 
                                class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2 w-full sm:w-auto">
                                <i class="bi bi-trash"></i>
                                <span>Hapus Prodi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Pesan Sukses -->
                    @if (session('success'))
                        <div class="bg-green-50 border border-green-300 text-green-800 text-sm px-4 py-3 rounded-md flex items-start gap-2 shadow-sm animate-fade-in mt-2">
                            <i class="bi bi-check-circle-fill text-green-600 text-base mt-0.5"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Form Tambah Prodi -->
                    <div id="prodiForm" class="hidden">
                        <form action="{{ route('admin.study-program.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 mt-2">
                            @csrf
                            <input type="text" name="study_program" required 
                                placeholder="Nama Program Studi" 
                                class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-72 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm">
                            <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition w-full sm:w-auto">
                                Simpan
                            </button>
                        </form>
                    </div>

                    <!-- Form Edit Prodi -->
                    <div id="editProdiForm" class="hidden space-y-2">
                        <h3 class="text-lg font-semibold text-gray-800">Edit Program Studi</h3>
                        <form action="{{ route('admin.study-program.update') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            @method('PUT')
                            <select name="id_study" id="editSelect" required 
                                class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-72 focus:outline-none focus:ring-2 focus:ring-yellow-300 text-sm">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->id_study }}" data-name="{{ $p->study_program }}">{{ $p->study_program }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="study_program" id="editInput" required 
                                placeholder="Nama Baru Program Studi"
                                class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-72 focus:outline-none focus:ring-2 focus:ring-yellow-300 text-sm">
                            <button type="submit" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition w-full sm:w-auto">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>

                    <!-- Form Hapus Prodi -->
                    <div id="hapusProdiForm" class="hidden space-y-2">
                        <h3 class="text-lg font-semibold text-gray-800">Hapus Program Studi</h3>
                        <form action="{{ route('admin.study-program.deleteBySelect') }}" method="POST" 
                            onsubmit="return confirm('Yakin ingin menghapus program studi ini?')" 
                            class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            @method('DELETE')
                            <select name="id_study" required 
                                class="border border-gray-300 rounded-md px-4 py-2 w-full sm:w-72 focus:outline-none focus:ring-2 focus:ring-red-300 text-sm">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->id_study }}">{{ $p->study_program }}</option>
                                @endforeach
                            </select>
                            <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition w-full sm:w-auto">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>


            <!-- Section: Judul dan Tombol Tambah Alumni -->
            <div class="px-4 sm:px-6 py-4 border-b bg-white mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <!-- Judul -->
                    <h1 class="text-2xl font-bold text-gray-800">Data Alumni</h1>

                    <!-- Tombol Tambah Alumni -->
                    <a href="{{ route('admin.alumni.create') }}" 
                        class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Alumni</span>
                    </a>
                </div>
            </div>


            <!-- Filter & Search Section -->
            <div class="p-4 sm:p-6 border-b bg-gray-50">
                
                <form method="GET" action="{{ route('admin.alumni.index') }}" class="space-y-3 sm:space-y-4">
                    <!-- Mobile: Stack all filters vertically -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Filter Tahun Lulus -->
                        <div class="space-y-1">
                            <label class="text-xs sm:text-sm font-medium text-gray-700 block sm:hidden">Tahun Lulus</label>
                            <select name="graduation_year" class="border border-gray-300 rounded-md px-3 py-2 w-full text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Tahun Lulus --</option>
                                @foreach($tahunLulus as $tahun)
                                    <option value="{{ $tahun }}" {{ request('graduation_year') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filter Program Studi -->
                        <div class="space-y-1">
                            <label class="text-xs sm:text-sm font-medium text-gray-700 block sm:hidden">Program Studi</label>
                            <select name="id_study" class="border border-gray-300 rounded-md px-3 py-2 w-full text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Program Studi --</option>
                                @foreach($prodi as $p)
                                    <option value="{{ $p->id_study }}" {{ request('id_study') == $p->id_study ? 'selected' : '' }}>
                                        {{ $p->study_program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Search Input -->
                        <div class="space-y-1 sm:col-span-1 lg:col-span-1">
                            <label class="text-xs sm:text-sm font-medium text-gray-700 block sm:hidden">Pencarian</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Cari Nama, NIM..."
                                    class="w-full pl-8 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-2 sm:pl-3 text-gray-400">
                                    <i class="bi bi-search text-sm"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="space-y-1">
                            <label class="text-xs sm:text-sm font-medium text-gray-700 block sm:hidden invisible">Action</label>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition duration-200">
                                <i class="bi bi-search mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Cari</span>
                                <span class="sm:hidden">Cari</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Alumni - Mobile: Card view, Desktop: Table view -->
            <div class="overflow-hidden">
                <!-- Desktop Table View - Perbaikan Aksi -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3 text-left">NIM</th>
                                <th class="px-4 py-3 text-left">Program Studi</th>
                                <th class="px-4 py-3 text-left">Nama Alumni</th>
                                <th class="px-4 py-3 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($alumni as $index => $item)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center">{{ ($alumni->currentPage() - 1) * $alumni->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ $item->nim }}</td>
                                    <td class="px-4 py-3">{{ $item->studyProgram->study_program ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.alumni.edit', $item->nim) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700 transition-colors duration-200"
                                               title="Edit Alumni">
                                                <i class="fas fa-edit text-xs sm:text-sm"></i>
                                            </a>
                                            <!-- Delete Button pakai modal -->
                                            <button type="button"
                                                onclick="openDeleteModal({{ $item->id_user }})"
                                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                                title="Hapus Alumni">
                                                <i class="fas fa-trash text-xs sm:text-sm"></i>
                                            </button>
                                            <!-- Modal Confirm Delete -->
                                            <div id="modal-delete-{{ $item->id_user }}" class="fixed inset-0 z-50 items-center justify-center bg-black/50 backdrop-blur-sm hidden">
                                                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-xs sm:max-w-sm relative">
                                                    <button onclick="closeDeleteModal({{ $item->id_user }})"
                                                            class="absolute top-2 right-2 text-gray-400 hover:text-red-600 transition-colors duration-200 p-1">
                                                        <i class="fas fa-times text-lg"></i>
                                                    </button>
                                                    <div class="flex flex-col items-center text-center">
                                                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                                                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Konfirmasi Hapus</h3>
                                                        <p class="text-gray-600 mb-4 text-sm">Yakin ingin menghapus alumni ini?<br>
                                                            <b>{{ $item->name }}</b> (NIM: {{ $item->nim }})<br>
                                                            Semua data terkait akan ikut terhapus.</p>
                                                        <form action="{{ route('admin.alumni.destroy', $item->id_user) }}" method="POST" class="w-full flex flex-col gap-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="flex justify-center gap-2 mt-2">
                                                                <button type="button" onclick="closeDeleteModal({{ $item->id_user }})"
                                                                    class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition">Batal</button>
                                                                <button type="submit"
                                                                    class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white font-semibold transition">Ya, Hapus</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-gray-400 text-center">
                                        <i class="bi bi-inbox text-2xl mb-2 block"></i>
                                        Tidak ada data alumni
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tablet Table View (md to lg) - Perbaikan Aksi -->
                <div class="hidden md:block lg:hidden overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-3 py-2 text-center">No</th>
                                <th class="px-3 py-2 text-left">NIM</th>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($alumni as $index => $item)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2 text-center text-xs">{{ ($alumni->currentPage() - 1) * $alumni->perPage() + $index + 1 }}</td>
                                    <td class="px-3 py-2 text-xs">{{ $item->nim }}</td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs font-medium">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ $item->studyProgram->study_program ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('admin.alumni.edit', $item->nim) }}" 
                                                class="inline-flex items-center justify-center px-2 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium transition duration-200 min-w-[60px]"
                                                title="Edit">
                                                <i class="bi bi-pencil-square mr-1"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.alumni.destroy', $item->id_user) }}" method="POST" 
                                                onsubmit="return confirm('Yakin ingin menghapus alumni ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="inline-flex items-center justify-center px-2 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-xs font-medium transition duration-200 min-w-[60px]" 
                                                    title="Hapus">
                                                    <i class="bi bi-trash mr-1"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-gray-400 text-center">
                                        <i class="bi bi-inbox text-xl mb-2 block"></i>
                                        <span class="text-sm">Tidak ada data alumni</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View - Perbaikan Aksi -->
                <div class="md:hidden space-y-3 p-4">
                    @forelse($alumni as $index => $item)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $item->name }}</h3>
                                    <p class="text-gray-600 text-xs mt-1">NIM: {{ $item->nim }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full flex-shrink-0 ml-2">
                                    #{{ ($alumni->currentPage() - 1) * $alumni->perPage() + $index + 1 }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Program Studi:</span> 
                                    <span class="break-words">{{ $item->studyProgram->study_program ?? '-' }}</span>
                                </p>
                            </div>
                            
                            <div class="flex gap-2 pt-2 border-t border-gray-100">
                                <a href="{{ route('admin.alumni.edit', $item->nim) }}"
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700 transition-colors duration-200"
                                title="Edit Alumni">
                                <i class="fas fa-edit text-xs sm:text-sm"></i>
                                </a>
                                <form action="{{ route('admin.alumni.destroy', $item->id_user) }}" method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus alumni ini?\n\nData yang akan dihapus:\n- Alumni: {{ $item->name }}\n- NIM: {{ $item->nim }}\n- Semua data terkait akan ikut terhapus')" 
                                    class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <!-- Delete Button pakai modal -->
                                 <button type="button"
                                     onclick="openDeleteModal({{ $item->id_user }})"
                                     class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                     title="Hapus Alumni">
                                    <i class="fas fa-trash text-xs sm:text-sm"></i>
                                 </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="bi bi-inbox text-3xl text-gray-400 mb-3 block"></i>
                            <p class="text-gray-400">Tidak ada data alumni</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="p-4 sm:p-6 border-t bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <!-- Results Info -->
                    <div class="text-xs sm:text-sm text-gray-600 order-2 sm:order-1">
                        <span class="font-medium">
                            Menampilkan {{ $alumni->firstItem() ?? 0 }} - {{ $alumni->lastItem() ?? 0 }} 
                            dari {{ $alumni->total() }} hasil
                        </span>
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="order-1 sm:order-2">
                        @if($alumni->hasPages())
                            <nav class="flex items-center justify-center sm:justify-end space-x-1" aria-label="Pagination">
                                {{-- Previous Page Link --}}
                                @if ($alumni->onFirstPage())
                                    <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                        <i class="bi bi-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </span>
                                @else
                                    <a href="{{ $alumni->previousPageUrl() }}" 
                                        class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                        <i class="bi bi-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($alumni->getUrlRange(1, $alumni->lastPage()) as $page => $url)
                                    @if ($page == $alumni->currentPage())
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded">
                                            {{ $page }}
                                        </span>
                                    @elseif ($page == 1 || $page == $alumni->lastPage() || ($page >= $alumni->currentPage() - 2 && $page <= $alumni->currentPage() + 2))
                                        <a href="{{ $url }}" 
                                            class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                            {{ $page }}
                                        </a>
                                    @elseif ($page == 2 && $alumni->currentPage() > 4)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @elseif ($page == $alumni->lastPage() - 1 && $alumni->currentPage() < $alumni->lastPage() - 3)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($alumni->hasMorePages())
                                    <a href="{{ $alumni->nextPageUrl() }}" 
                                        class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                        <span class="hidden sm:inline mr-1">Next</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                        <span class="hidden sm:inline mr-1">Next</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

   
     <!-- Script Toggle -->
<script>
       // Autofill input edit saat pilihan diubah
    document.getElementById('editSelect').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const name = selectedOption.getAttribute('data-name');
        document.getElementById('editInput').value = name ?? '';
    });
      function toggleProdiForm(id) {
        document.querySelectorAll('#prodiForm, #editProdiForm, #hapusProdiForm').forEach(el => {
            if (el.id === id) {
                el.classList.toggle('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
    }
    // Modal open/close logic for delete confirmation
    function openDeleteModal(id) {
        const modal = document.getElementById('modal-delete-' + id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    function closeDeleteModal(id) {
        const modal = document.getElementById('modal-delete-' + id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }
</script>
 <!-- script JS  -->
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
