@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>


    {{-- Header --}}
    <x-slot name="header">
        <x-admin.header>Pengguna alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>        
    </x-slot>

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-md p-6 md:p-10 mb-6 mt-4 mx-6">
        <form action="{{ route('admin.company.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row md:items-center gap-4">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls" required
                class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/2 text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                <i class="bi bi-upload"></i> Import Excel
            </button>
            <a href="{{ route('admin.company.export') }}"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                <i class="bi bi-download"></i> Export Excel
            </a>
        </form>
        @if(session('error'))
            <div class="text-red-500">{{ session('error') }}</div>
        @endif
    </div>

    <!-- Data Table Section -->
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Daftar Pengguna Alumni</h2>
            <a href="{{ route('admin.company.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                + Tambah Pengguna Alumni
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Search Bar -->
            <div class="p-4 flex justify-between items-center border-b">
                <form method="GET" action="{{ route('admin.company.index') }}" class="flex items-center space-x-2 w-full max-w-md">
                    <button class="text-gray-500" title="Filter">
                        <i class="bi bi-filter"></i>
                    </button>
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Perusahaan"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Perusahaan</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">No. Telepon</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($companies as $index => $company)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">{{ ($companies->currentPage() - 1) * $companies->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $company->company_name }}</td>
                                <td class="px-4 py-3">{{ $company->company_address ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $company->company_email ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $company->company_phone_number ?? '-' }}</td>
                                <td class="px-4 py-3 flex justify-center space-x-2">
                                    <a href="{{ route('admin.company.edit', $company->id_company) }}"
                                       class="px-3 py-1 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-sm flex items-center justify-center"
                                       title="Edit">
                                       <i class="bi bi-pencil-square mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.company.destroy', $company->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')">
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
                                <td colspan="6" class="px-4 py-4 text-gray-400">Tidak ada data perusahaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t text-sm text-gray-500">
                {{ $companies->withQueryString()->links() }}
            </div>
        </div>
    </div>
   <!-- script JS  -->
           <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
