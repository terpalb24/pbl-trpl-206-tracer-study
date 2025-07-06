@extends('layouts.app')



@section('content')
<x-layout-admin>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                class="flex flex-col gap-3 sm:gap-4" id="importForm">
                @csrf
                
                <!-- File input section -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="border border-gray-300 rounded-md px-3 sm:px-4 py-2 w-full sm:flex-1 text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 file:mr-2 sm:file:mr-4 file:py-1 sm:file:py-2 file:px-2 sm:file:px-4 file:rounded file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        onchange="validateExcelFile(this)">
                    
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
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <!-- Bulk Delete Buttons -->
                        <button id="bulkDeleteBtn" 
                                type="button"
                                onclick="confirmBulkDelete()" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" 
                                style="display: none;">
                            <i class="fas fa-trash mr-2"></i>
                            <span id="deleteSelectedText">Hapus Terpilih</span>
                        </button>
                        
                        <button type="button"
                            onclick="deleteAllCompanies()"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-200"
                            title="Hapus semua perusahaan yang sesuai dengan filter saat ini">
                            <i class="fas fa-trash-alt mr-2"></i>
                            <span>Hapus Semua</span>
                        </button>

                        <a href="{{ route('admin.company.create') }}" 
                            class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition gap-2">
                            <i class="bi bi-plus-circle"></i>
                            <span>Tambah Perusahaan</span>
                        </a>
                    </div>
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
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
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
            </div>

            <!-- Tabel Perusahaan - Mobile: Card view, Desktop: Table view -->
            <div class="overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <form id="bulkDeleteForm" action="{{ route('admin.company.bulk-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-center w-10">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
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
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $company->id_user }}" 
                                            class="company-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
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
                                            
                                            <!-- Delete Button with SweetAlert2 -->
                                            <button type="button" 
                                                onclick="deleteCompany('{{ $company->id_user }}', '{{ $company->company_name }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                                title="Hapus Perusahaan">
                                                <i class="fas fa-trash text-xs sm:text-sm"></i>
                                            </button>
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
                                            <button type="button"
                                                onclick="deleteCompany('{{ $company->id_company }}', '{{ $company->company_name }}')"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded bg-red-500 hover:bg-red-600 text-white text-xs font-medium transition duration-200 min-w-[60px]"
                                                title="Hapus">
                                                <i class="bi bi-trash mr-1"></i>
                                                <span>Hapus</span>
                                            </button>
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
                            <!-- Add checkbox for bulk delete -->
                            <div class="flex justify-end mb-2">
                                <input type="checkbox" 
                                    value="{{ $company->id_user }}" 
                                    class="company-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    onchange="updateBulkDeleteButton()">
                            </div>
                            
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
                                <!-- Delete Button - Using id_user instead of id_company -->
                                <button type="button"
                                    onclick="deleteCompany('{{ $company->id_user }}', '{{ $company->company_name }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                    title="Hapus Perusahaan">
                                    <i class="fas fa-trash text-xs sm:text-sm"></i>
                                </button>
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
    
    <script>
        // Function to delete single company
        async function deleteCompany(id, name) {
            const result = await Swal.fire({
                title: 'Konfirmasi Hapus Perusahaan',
                html: `Apakah Anda yakin ingin menghapus perusahaan <strong>${name}</strong>?<br>
                      <div class="text-red-600 text-sm font-semibold mt-2">⚠️ Data yang dihapus tidak dapat dikembalikan!</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6B7280',
                customClass: {
                    popup: 'swal2-show',
                    title: 'text-xl font-bold mb-4',
                    htmlContainer: 'text-left',
                    confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mr-2',
                    cancelButton: 'px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2'
                }
            });

            if (result.isConfirmed) {
                try {
                    // Show loading state
                    Swal.fire({
                        title: 'Menghapus Data',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Use POST with _method=DELETE for proper routing
                    const response = await fetch(`/admin/company/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'  // This simulates DELETE method
                        })
                    });

                    let data;
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        data = {
                            success: response.ok,
                            message: response.ok ? 'Perusahaan berhasil dihapus' : 'Gagal menghapus perusahaan'
                        };
                    }

                    if (response.ok && data.success) {
                        await Swal.fire({
                            title: 'Berhasil!',
                            text: data.message || 'Perusahaan berhasil dihapus.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3085d6',
                            customClass: {
                                popup: 'swal2-show',
                                title: 'text-lg font-semibold mb-2',
                                confirmButton: 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                            }
                        });

                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                    }
                } catch (error) {
                    console.error('Error deleting company:', error);
                    await Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        customClass: {
                            popup: 'swal2-show',
                            title: 'text-lg font-semibold text-red-600 mb-2',
                            confirmButton: 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2'
                        }
                    });
                }
            }
        }

        function getSelectedCompanyIds() {
            const ids = [];
            document.querySelectorAll('.company-checkbox:checked').forEach(checkbox => {
                ids.push(checkbox.value);
            });
            return ids;
        }

        async function confirmBulkDelete() {
            const selectedIds = getSelectedCompanyIds();
            
            if (selectedIds.length === 0) {
                await Swal.fire({
                    title: 'Perhatian',
                    text: 'Pilih minimal satu perusahaan untuk dihapus!',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                await Swal.fire({
                    title: 'Error',
                    text: 'CSRF token tidak ditemukan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const result = await Swal.fire({
                title: 'Konfirmasi Hapus Perusahaan',
                html: `Apakah Anda yakin ingin menghapus <strong>${selectedIds.length} perusahaan</strong> yang dipilih?
                      <div class="text-red-600 text-sm font-semibold mt-2">
                        ⚠️ Semua data terkait akan ikut terhapus dan TIDAK DAPAT DIKEMBALIKAN!
                      </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6B7280'
            });

            if (!result.isConfirmed) return;

            const loadingSwal = Swal.fire({
                title: 'Menghapus Data',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Validate selected IDs
                if (!Array.isArray(selectedIds) || selectedIds.some(id => !id)) {
                    throw new Error('Data perusahaan yang dipilih tidak valid');
                }

                const response = await fetch('{{ route("admin.company.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        _method: 'DELETE',
                        ids: selectedIds.map(id => parseInt(id)) // Ensure IDs are numbers
                    })
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    throw new Error('Server tidak mengembalikan response JSON yang valid');
                }

                // Check both response.ok and data.success
                if (!response.ok || !data.success) {
                    let errorMessage = data.message || 'Terjadi kesalahan saat menghapus data';
                    if (data.message && data.message.includes('tidak ditemukan')) {
                        errorMessage = 'Data perusahaan yang dipilih tidak valid atau sudah tidak tersedia.';
                    }
                    throw new Error(errorMessage);
                }

                await loadingSwal.close();

                await Swal.fire({
                    title: 'Berhasil',
                    text: data.message || `${selectedIds.length} perusahaan berhasil dihapus`,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });

                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                await loadingSwal.close();
                
                await Swal.fire({
                    title: 'Gagal',
                    html: `<div class="text-left">
                            ${error.message}
                            ${error.message.includes('tidak ditemukan') || error.message.includes('tidak valid') ? 
                              '<br><br><div class="mt-2 text-sm text-gray-600">' +
                              '<p class="mb-1">Kemungkinan penyebab:</p>' +
                              '<ul class="list-disc pl-4">' +
                              '<li>Data perusahaan sudah dihapus sebelumnya</li>' +
                              '<li>Data perusahaan sudah tidak tersedia di database</li>' +
                              '<li>ID perusahaan tidak valid</li>' +
                              '</ul>' +
                              '<p class="mt-2">Silakan refresh halaman untuk memperbarui data.</p>' +
                              '</div>' : 
                              ''}
                          </div>`,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }
        }

        async function deleteAllCompanies() {
            const result = await Swal.fire({
                title: 'Konfirmasi Hapus Semua Perusahaan',
                html: `Apakah Anda yakin ingin menghapus <strong>semua perusahaan</strong>?<br>
                      <div class="text-red-600 text-sm font-semibold mt-2">
                        ⚠️ Semua data perusahaan dan data terkait akan terhapus dan TIDAK DAPAT DIKEMBALIKAN!
                      </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6B7280'
            });

            if (!result.isConfirmed) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                await Swal.fire({
                    title: 'Error',
                    text: 'CSRF token tidak ditemukan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const loadingSwal = Swal.fire({
                title: 'Menghapus Data',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const searchParams = new URLSearchParams(window.location.search);
                const response = await fetch('{{ route("admin.company.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        _method: 'DELETE',
                        delete_all: true,
                        search: searchParams.get('search')
                    })
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    throw new Error('Server tidak mengembalikan response JSON yang valid');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                }

                await loadingSwal.close();

                await Swal.fire({
                    title: 'Berhasil',
                    text: data.message || 'Semua perusahaan berhasil dihapus',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });

                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                await loadingSwal.close();

                await Swal.fire({
                    title: 'Gagal',
                    html: `<div class="text-left">
                            ${error.message}
                            ${error.message.includes('tidak ditemukan') ? 
                              '<br><br><span class="text-sm text-gray-600">Tidak ada perusahaan yang dapat dihapus dengan filter yang diterapkan.</span>' : 
                              ''}
                      </div>`,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }
        }

        function updateBulkDeleteButton() {
            const selectedIds = getSelectedCompanyIds();
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            
            if (!bulkDeleteBtn) return;
            
            if (selectedIds.length > 0) {
                bulkDeleteBtn.style.display = 'inline-flex';
                bulkDeleteBtn.innerHTML = `<i class="fas fa-trash mr-2"></i>Hapus Terpilih (${selectedIds.length})`;
            } else {
                bulkDeleteBtn.style.display = 'none';
                bulkDeleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i>Hapus Terpilih';
            }
        }

        // Event listener untuk select all checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.company-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                });
            }

            // Event listener untuk individual checkboxes
            document.querySelectorAll('.company-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkDeleteButton();
                    
                    // Update select all checkbox state
                    const selectAllCheckbox = document.getElementById('selectAll');
                    if (selectAllCheckbox) {
                        const totalCheckboxes = document.querySelectorAll('.company-checkbox').length;
                        const checkedCheckboxes = document.querySelectorAll('.company-checkbox:checked').length;
                        selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
                        selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
                    }
                });
            });

            // Initialize button state
            updateBulkDeleteButton();
        });

        // Function to handle import form submission
        async function handleImport(event) {
            event.preventDefault();
            const form = event.target;
            const fileInput = form.querySelector('input[type="file"]');
            
            if (!fileInput.files.length) {
                await Swal.fire({
                    title: 'Perhatian',
                    text: 'Pilih file Excel terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const file = fileInput.files[0];
            if (!file.name.match(/\.(xls|xlsx)$/i)) {
                await Swal.fire({
                    title: 'File Tidak Valid',
                    text: 'File harus berformat Excel (.xls atau .xlsx)',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const result = await Swal.fire({
                title: 'Konfirmasi Import',
                html: `Anda akan mengimport data dari file:<br>
                      <strong class="text-blue-600">${file.name}</strong><br><br>
                      <div class="text-left text-sm">
                        <p class="mb-2">Pastikan:</p>
                        <ul class="list-disc pl-5 space-y-1">
                          <li>Format file sesuai dengan template</li>
                          <li>Data sudah diisi dengan benar</li>
                          <li>Tidak ada duplikasi email</li>
                        </ul>
                      </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Import',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6B7280'
            });

            if (!result.isConfirmed) return;

            const formData = new FormData(form);

            // Show loading state
            const loadingSwal = Swal.fire({
                title: 'Mengimport Data',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                await loadingSwal.close();

                if (response.ok && data.success) {
                    await Swal.fire({
                        title: 'Berhasil!',
                        html: `${data.message}<br>
                              <div class="text-sm text-gray-600 mt-2">
                                ${data.imported ? `Jumlah data terimport: ${data.imported}` : ''}
                                ${data.updated ? `<br>Jumlah data terupdate: ${data.updated}` : ''}
                              </div>`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });

                    window.location.reload();
                } else {
                    let errorMessage = data.message || 'Terjadi kesalahan saat mengimport data';
                    let errorDetails = '';
                    
                    if (data.errors) {
                        errorDetails = '<div class="mt-3 text-sm text-left">' +
                            '<p class="font-semibold mb-1">Detail error:</p>' +
                            '<ul class="list-disc pl-5 space-y-1">' +
                            Object.entries(data.errors).map(([key, value]) => 
                                `<li>${value}</li>`
                            ).join('') +
                            '</ul></div>';
                    }

                    throw new Error(errorMessage + errorDetails);
                }
            } catch (error) {
                console.error('Import error:', error);
                await Swal.fire({
                    title: 'Gagal Import',
                    html: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }

            // Reset file input
            fileInput.value = '';
        }

        // Attach event listener to import form
        document.getElementById('importForm').addEventListener('submit', handleImport);

        function validateExcelFile(input) {
            const file = input.files[0];
            if (file) {
                if (!file.name.match(/\.(xls|xlsx)$/i)) {
                    Swal.fire({
                        title: 'File Tidak Valid',
                        text: 'File harus berformat Excel (.xls atau .xlsx)',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        input.value = ''; // Reset file input
                    });
                    return false;
                }

                // File size validation (optional, maximum 5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal adalah 5MB',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        input.value = ''; // Reset file input
                    });
                    return false;
                }
            }
            return true;
        }
    </script>
</x-layout-admin>
@endsection
