@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    {{-- Header --}}
    <x-slot name="header">
        <x-admin.header>Perusahaan</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>        
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto">
        <!-- Import & Export Excel Section -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6 mt-3 sm:mt-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Import/Export Data Perusahaan</h3>
            <form action="{{ route('admin.company.import') }}" method="POST" enctype="multipart/form-data" 
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
                        
                        <a href="{{ route('admin.company.export') }}"
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-5 py-2 rounded-md font-semibold text-xs sm:text-sm transition duration-200 whitespace-nowrap">
                            <i class="bi bi-download"></i> 
                            <span class="hidden sm:inline">Export Excel</span>
                            <span class="sm:hidden">Export</span>
                        </a>
                        
                        <a href="{{ route('admin.company.template') }}"
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
        </div>

        <!-- Warning perusahaan tidak lengkap -->
        @if(isset($incompleteCompanies) && $incompleteCompanies->count())
            <div class="mb-4 sm:mb-6">
                <div class="p-3 sm:p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded text-sm sm:text-base">
                    <strong>Perhatian:</strong> Berikut adalah perusahaan yang ditambahkan oleh alumni namun belum memiliki data lengkap.<br>
                    <span class="hidden sm:inline">Mohon untuk melengkapi data perusahaan berikut.</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden mb-4 sm:mb-6">
                <div class="p-3 sm:p-4 border-b font-semibold text-yellow-800 bg-yellow-50 text-sm sm:text-base">
                    Perusahaan Tidak Lengkap
                </div>
                
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3 text-left">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left">Ditambahkan Oleh</th>
                                <th class="px-4 py-3 text-center w-32">Aksi</th> {{-- Tambah width --}}
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @foreach($incompleteCompanies as $index => $company)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ $company->company_name }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $alumniList = [];
                                            foreach($company->jobHistories as $jh) {
                                                if ($jh->alumni) {
                                                    $alumniList[] = $jh->alumni->name . ' (NIM: ' . $jh->alumni->nim . ')';
                                                }
                                            }
                                        @endphp
                                        @if(count($alumniList))
                                            {!! implode('<br>', $alumniList) !!}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center">
                                            <a href="{{ route('admin.company.edit', $company->id_company) }}"
                                               class="inline-flex items-center justify-center px-3 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium transition duration-200 min-w-[100px]"
                                               title="Lengkapi Data Perusahaan">
                                               <i class="bi bi-pencil-square mr-1"></i>
                                               <span>Lengkapi</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-3 p-4">
                    @foreach($incompleteCompanies as $index => $company)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $company->company_name }}</h3>
                                    <p class="text-xs text-gray-600 mt-1">Perusahaan #{{ $index + 1 }}</p>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full flex-shrink-0 ml-2">
                                    Tidak Lengkap
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Ditambahkan oleh:</span>
                                    @php
                                        $alumniList = [];
                                        foreach($company->jobHistories as $jh) {
                                            if ($jh->alumni) {
                                                $alumniList[] = $jh->alumni->name;
                                            }
                                        }
                                    @endphp
                                    @if(count($alumniList))
                                        <span class="break-words">{{ implode(', ', $alumniList) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-100">
                                <a href="{{ route('admin.company.edit', $company->id_company) }}" 
                                    class="w-full flex items-center justify-center gap-1 px-3 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium transition duration-200">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Lengkapi Data</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Main content section -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <!-- Header section dengan judul dan tombol tambah -->
            <div class="p-4 sm:p-6 border-b">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Daftar Perusahaan</h2>
                    <a href="{{ route('admin.company.create') }}" 
                        class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2 sm:w-auto w-full">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Perusahaan</span>
                    </a>
                </div>
            </div>

            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="mx-4 sm:mx-6 mt-4 p-3 sm:p-4 bg-green-100 text-green-700 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search Section -->
            <div class="p-4 sm:p-6 border-b bg-gray-50">
                <form method="GET" action="{{ route('admin.company.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                    <div class="relative w-full sm:max-w-md">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari Nama Perusahaan..."
                            class="w-full pl-8 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-2 sm:pl-3 text-gray-400">
                            <i class="bi bi-search text-sm"></i>
                        </div>
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition duration-200">
                        <i class="bi bi-search mr-1 sm:mr-2"></i>
                        <span>Cari</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Perusahaan - Mobile: Card view, Desktop: Table view -->
            <div class="overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3 text-left">Nama Perusahaan</th>
                                <th class="px-4 py-3 text-left">Alamat</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Telepon</th>
                                <th class="px-4 py-3 text-center w-40">Aksi</th> {{-- Ubah dari w-24 ke w-40 --}}
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($companies as $index => $company)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center">{{ ($companies->currentPage() - 1) * $companies->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $company->company_name }}</td>
                                    <td class="px-4 py-3">{{ $company->company_address ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $company->company_email ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $company->company_phone_number ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center space-x-2">
                                            
                                              <!-- Edit Button -->
                                             <a href="{{ route('admin.company.edit', $company->id_company) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700 transition-colors duration-200"
                                               title="Edit Perusahaan">
                                                <i class="fas fa-edit text-xs sm:text-sm"></i>
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.company.destroy', $company->id_user) }}" method="POST" 
                                                onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?\n\nData yang akan dihapus:\n- Perusahaan: {{ addslashes($company->company_name) }}\n- Semua data terkait akan ikut terhapus')" 
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                                title="Hapus Perusahaan">
                                                 <i class="fas fa-trash text-xs sm:text-sm"></i>
                                               </button>
                                                
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-gray-400 text-center">
                                        <i class="bi bi-inbox text-2xl mb-2 block"></i>
                                        Tidak ada data perusahaan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tablet Table View (md to lg) -->
                <div class="hidden md:block lg:hidden overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-3 py-2 text-center">No</th>
                                <th class="px-3 py-2 text-left">Perusahaan</th>
                                <th class="px-3 py-2 text-left">Kontak</th>
                                <th class="px-3 py-2 text-center w-32">Aksi</th> {{-- Ubah dari w-20 ke w-32 --}}
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($companies as $index => $company)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2 text-center text-xs">{{ ($companies->currentPage() - 1) * $companies->perPage() + $index + 1 }}</td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs font-medium">{{ $company->company_name }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ $company->company_address ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs">{{ $company->company_email ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $company->company_phone_number ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('admin.company.edit', $company->id_company) }}" 
                                                class="inline-flex items-center justify-center px-2 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium transition duration-200 min-w-[60px]"
                                                title="Edit">
                                                <i class="bi bi-pencil-square mr-1"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.company.destroy', $company->id_user) }}" method="POST" 
                                                onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')" class="inline">
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
                                        <span class="text-sm">Tidak ada data perusahaan</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3 p-4">
                    @forelse($companies as $index => $company)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $company->company_name }}</h3>
                                    <p class="text-gray-600 text-xs mt-1">{{ $company->company_address ?? 'Alamat tidak tersedia' }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full flex-shrink-0 ml-2">
                                    #{{ ($companies->currentPage() - 1) * $companies->perPage() + $index + 1 }}
                                </span>
                            </div>
                            
                            <div class="mb-3 space-y-1">
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Email:</span> 
                                    <span class="break-words">{{ $company->company_email ?? '-' }}</span>
                                </p>
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Telepon:</span> 
                                    <span>{{ $company->company_phone_number ?? '-' }}</span>
                                </p>
                            </div>
                            
                            <div class="flex gap-2 pt-2 border-t border-gray-100">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.company.edit', $company->id_company) }}"
                                  class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700 transition-colors duration-200"
                                  title="Edit Perusahaan">
                                <i class="fas fa-edit text-xs sm:text-sm"></i>
                                </a>
                                <form action="{{ route('admin.company.destroy', $company->id_user) }}" method="POST" 
                                    onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?\n\nData yang akan dihapus:\n- Perusahaan: {{ addslashes($company->company_name) }}\n- Semua data terkait akan ikut terhapus')" 
                                    class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                     <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                                title="Hapus Perusahaan">
                                             <i class="fas fa-trash text-xs sm:text-sm"></i>
                                     </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="bi bi-inbox text-3xl text-gray-400 mb-3 block"></i>
                            <p class="text-gray-400">Tidak ada data perusahaan</p>
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
                            Menampilkan {{ $companies->firstItem() ?? 0 }} - {{ $companies->lastItem() ?? 0 }} 
                            dari {{ $companies->total() }} hasil
                        </span>
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="order-1 sm:order-2">
                        @if($companies->hasPages())
                            <nav class="flex items-center justify-center sm:justify-end space-x-1" aria-label="Pagination">
                                {{-- Previous Page Link --}}
                                @if ($companies->onFirstPage())
                                    <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                        <i class="bi bi-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </span>
                                @else
                                    <a href="{{ $companies->previousPageUrl() }}" 
                                        class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                        <i class="bi bi-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($companies->getUrlRange(1, $companies->lastPage()) as $page => $url)
                                    @if ($page == $companies->currentPage())
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded">
                                            {{ $page }}
                                        </span>
                                    @elseif ($page == 1 || $page == $companies->lastPage() || ($page >= $companies->currentPage() - 2 && $page <= $companies->currentPage() + 2))
                                        <a href="{{ $url }}" 
                                            class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                            {{ $page }}
                                        </a>
                                    @elseif ($page == 2 && $companies->currentPage() > 4)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @elseif ($page == $companies->lastPage() - 1 && $companies->currentPage() < $companies->lastPage() - 3)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($companies->hasMorePages())
                                    <a href="{{ $companies->nextPageUrl() }}" 
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

    <!-- script JS  -->
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
