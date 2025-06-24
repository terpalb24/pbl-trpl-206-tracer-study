@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Kuisioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Action buttons card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Kelola Kuisioner</h3>
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('admin.questionnaire.create') }}" 
                    class="inline-flex items-center justify-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition">
                    <i class="fas fa-plus"></i> 
                    <span class="hidden sm:inline">Tambah Kuisioner</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
                <a href="{{ route('admin.questionnaires.import-export') }}" 
                    class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition">
                    <i class="fas fa-file-import"></i> 
                    <span class="hidden sm:inline">Import/Export Kuisioner</span>
                    <span class="sm:hidden">Import/Export</span>
                </a>
                <a href="{{ route('admin.questionnaires.download-template') }}" 
                    class="inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition">
                    <i class="fas fa-download"></i> 
                    <span class="hidden sm:inline">Download Template</span>
                    <span class="sm:hidden">Template</span>
                </a>
                <button type="button" onclick="showRemindAllModal()" 
                    class="inline-flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition">
                    <i class="fas fa-bell"></i> 
                    <span class="hidden sm:inline">Kirim Pengingat ke Semua</span>
                    <span class="sm:hidden">Pengingat</span>
                </button>
            </div>
        </div>

        <!-- Modal Pilih Periode -->
        <div id="remindAllModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Kirim Pengingat ke Semua</h3>
                <form id="remindAllModalForm" method="POST">
                    @csrf
                    <label for="periode_id_select" class="block mb-2 text-sm font-medium text-gray-700">Pilih Periode Aktif</label>
                    <select id="periode_id_select" name="id_periode" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4 text-sm" required>
                        <option value="">-- Pilih Periode Aktif --</option>
                        @foreach($periodes as $periode)
                            @if($periode->status == 'active')
                                <option value="{{ $periode->id_periode }}">{{ $periode->periode_name }} ({{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }})</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button type="button" onclick="closeRemindAllModal()" 
                            class="w-full sm:w-auto px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm order-2 sm:order-1">Batal</button>
                        <button type="submit" 
                            class="w-full sm:w-auto px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm order-1 sm:order-2">Kirim</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 p-3 sm:p-4 bg-green-100 text-green-700 rounded-md text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 sm:p-4 bg-red-100 text-red-700 rounded-md text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Main content card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <!-- Filter section -->
            <div class="p-3 sm:p-4 border-b bg-gray-50">
                <form method="GET" action="{{ route('admin.questionnaire.index') }}" class="space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:gap-4">
                    <!-- Filter Tahun -->
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-2">
                        <label for="year" class="text-xs sm:text-sm font-medium text-gray-700 whitespace-nowrap">Filter Tahun:</label>
                        <select name="year" id="year" class="border border-gray-300 rounded-lg px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[100px] sm:min-w-[120px]">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-2">
                        <label for="status" class="text-xs sm:text-sm font-medium text-gray-700 whitespace-nowrap">Filter Status:</label>
                        <select name="status" id="status" class="border border-gray-300 rounded-lg px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[100px] sm:min-w-[120px]">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit" class="inline-flex items-center justify-center px-3 sm:px-4 py-1 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-filter mr-1 sm:mr-2"></i>
                            Filter
                        </button>
                        
                        @if(request('year') || request('status'))
                            <a href="{{ route('admin.questionnaire.index') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-1 sm:py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs sm:text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-times mr-1 sm:mr-2"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Active filter info -->
                @if(request('year') || request('status'))
                    <div class="mt-3 p-2 sm:p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center text-xs sm:text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Filter aktif: </span>
                            @if(request('year'))
                                <span class="ml-1 px-2 py-1 bg-blue-100 rounded text-xs">Tahun {{ request('year') }}</span>
                            @endif
                            @if(request('status'))
                                <span class="ml-1 px-2 py-1 bg-blue-100 rounded text-xs">Status {{ ucfirst(request('status')) }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Table section -->
            <div class="overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Target Alumni</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal Mulai</th>
                                <th class="px-4 py-3">Tanggal Selesai</th>
                                <th class="px-4 py-3">Tahun Dibuat</th>
                                <th class="px-4 py-3">Jumlah Kategori</th>
                                <th class="px-4 py-3 w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($periodes as $index => $periode)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
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
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $periode->created_at->format('Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $periode->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-blue-600 bg-blue-100 rounded-full">
                                            {{ $periode->categories->count() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="grid grid-cols-2 gap-2 w-full max-w-[160px]">
                                            <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                                               class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900 font-medium text-xs px-2 py-1.5 rounded-md bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all duration-200">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                            <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" 
                                               class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-900 font-medium text-xs px-2 py-1.5 rounded-md bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all duration-200">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                                               class="inline-flex items-center justify-center text-green-600 hover:text-green-900 font-medium text-xs px-2 py-1.5 rounded-md bg-green-50 hover:bg-green-100 border border-green-200 transition-all duration-200">
                                                <i class="fas fa-chart-bar mr-1"></i>Respons
                                            </a>
                                            
                                            @php
                                                $hasResponses = \App\Models\Tb_User_Answers::where('id_periode', $periode->id_periode)->exists();
                                                $canDelete = $periode->status !== 'active';
                                            @endphp
                                            
                                            @if($canDelete)
                                                <form action="{{ route('admin.questionnaire.destroy', $periode->id_periode) }}" 
                                                      method="POST" 
                                                      class="w-full"
                                                      onsubmit="return confirmDelete('{{ $periode->periode_name }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-full inline-flex items-center justify-center text-red-600 hover:text-red-900 font-medium text-xs px-2 py-1.5 rounded-md bg-red-50 hover:bg-red-100 border border-red-200 transition-all duration-200">
                                                        <i class="fas fa-trash mr-1"></i>Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center justify-center text-gray-400 font-medium text-xs px-2 py-1.5 rounded-md bg-gray-50 border border-gray-200 cursor-not-allowed" 
                                                      title="{{ $hasResponses ? 'Periode ini sudah memiliki respons dan tidak dapat dihapus' : 'Periode aktif tidak dapat dihapus' }}">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                                            @if(request('year') || request('status'))
                                                <p class="text-lg font-medium mb-1">Tidak ada periode kuesioner ditemukan</p>
                                                <p class="text-sm">Coba ubah filter atau reset filter untuk melihat semua data</p>
                                            @else
                                                <p class="text-lg font-medium mb-1">Belum ada periode kuesioner</p>
                                                <p class="text-sm">Klik tombol "Tambah Kuisioner" untuk membuat kuesioner baru</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-3 p-4">
                    @forelse($periodes as $index => $periode)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $periode->getTargetDescription() }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                              ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($periode->status) }}
                                        </span>
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-blue-600 bg-blue-100 rounded-full">
                                            {{ $periode->categories->count() }}
                                        </span>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full flex-shrink-0 ml-2">
                                    #{{ ($periodes->currentPage() - 1) * $periodes->perPage() + $index + 1 }}
                                </span>
                            </div>
                            
                            <div class="mb-3 space-y-1">
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Periode:</span> 
                                    {{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }}
                                </p>
                                <p class="text-gray-600 text-sm">
                                    <span class="font-medium">Tahun dibuat:</span> 
                                    {{ $periode->created_at->format('Y') }}
                                </p>
                                @if($periode->target_type === 'years_ago' && !empty($periode->years_ago_list))
                                    <p class="text-blue-600 text-xs flex items-center">
                                        <i class="fas fa-clock mr-1"></i>Relatif tahun {{ now()->year }}
                                    </p>
                                @elseif($periode->target_type === 'specific_years' && !empty($periode->target_graduation_years))
                                    <p class="text-purple-600 text-xs flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>Tahun spesifik
                                    </p>
                                @elseif($periode->target_type === 'all')
                                    <p class="text-green-600 text-xs flex items-center">
                                        <i class="fas fa-users mr-1"></i>Semua alumni
                                    </p>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                                <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                                    class="flex items-center justify-center gap-1 px-2 py-2 rounded-md bg-blue-50 hover:bg-blue-100 border border-blue-500 text-blue-600 text-xs font-medium transition duration-200">
                                    <i class="fas fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                                <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" 
                                    class="flex items-center justify-center gap-1 px-2 py-2 rounded-md bg-indigo-50 hover:bg-indigo-100 border border-indigo-500 text-indigo-600 text-xs font-medium transition duration-200">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                                    class="flex items-center justify-center gap-1 px-2 py-2 rounded-md bg-green-50 hover:bg-green-100 border border-green-500 text-green-600 text-xs font-medium transition duration-200">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Respons</span>
                                </a>
                                
                                @php
                                    $hasResponses = \App\Models\Tb_User_Answers::where('id_periode', $periode->id_periode)->exists();
                                    $canDelete = $periode->status !== 'active';
                                @endphp
                                
                                @if($canDelete)
                                    <form action="{{ route('admin.questionnaire.destroy', $periode->id_periode) }}" 
                                          method="POST" 
                                          class="w-full"
                                          onsubmit="return confirmDelete('{{ $periode->periode_name }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full flex items-center justify-center gap-1 px-2 py-2 rounded-md bg-red-50 hover:bg-red-100 border border-red-500 text-red-600 text-xs font-medium transition duration-200">
                                            <i class="fas fa-trash"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                        class="w-full flex items-center justify-center gap-1 px-2 py-2 rounded-md bg-gray-300 text-gray-500 text-xs font-medium cursor-not-allowed"
                                        title="{{ $hasResponses ? 'Periode ini sudah memiliki respons dan tidak dapat dihapus' : 'Periode aktif tidak dapat dihapus' }}">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-3xl text-gray-400 mb-3 block"></i>
                            @if(request('year') || request('status'))
                                <p class="text-gray-400 font-medium mb-1">Tidak ada periode kuesioner ditemukan</p>
                                <p class="text-gray-400 text-sm">Coba ubah filter atau reset filter</p>
                            @else
                                <p class="text-gray-400 font-medium mb-1">Belum ada periode kuesioner</p>
                                <p class="text-gray-400 text-sm">Klik tombol "Tambah Kuisioner"</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="p-3 sm:p-4 border-t bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <!-- Results Info -->
                    <div class="text-xs sm:text-sm text-gray-600 order-2 sm:order-1">
                        <span class="font-medium">
                            Menampilkan {{ $periodes->firstItem() ?? 0 }} - {{ $periodes->lastItem() ?? 0 }} 
                            dari {{ $periodes->total() }} hasil
                        </span>
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="order-1 sm:order-2">
                        @if($periodes->hasPages())
                            <nav class="flex items-center justify-center sm:justify-end space-x-1" aria-label="Pagination">
                                {{-- Previous Page Link --}}
                                @if ($periodes->onFirstPage())
                                    <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </span>
                                @else
                                    <a href="{{ $periodes->previousPageUrl() }}" 
                                        class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="hidden sm:inline ml-1">Previous</span>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($periodes->getUrlRange(1, $periodes->lastPage()) as $page => $url)
                                    @if ($page == $periodes->currentPage())
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded">
                                            {{ $page }}
                                        </span>
                                    @elseif ($page == 1 || $page == $periodes->lastPage() || ($page >= $periodes->currentPage() - 2 && $page <= $periodes->currentPage() + 2))
                                        <a href="{{ $url }}" 
                                            class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                            {{ $page }}
                                        </a>
                                    @elseif ($page == 2 && $periodes->currentPage() > 4)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @elseif ($page == $periodes->lastPage() - 1 && $periodes->currentPage() < $periodes->lastPage() - 3)
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($periodes->hasMorePages())
                                    <a href="{{ $periodes->nextPageUrl() }}" 
                                        class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                        <span class="hidden sm:inline mr-1">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                        <span class="hidden sm:inline mr-1">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function showRemindAllModal() {
            document.getElementById('remindAllModal').classList.remove('hidden');
        }
        
        function closeRemindAllModal() {
            document.getElementById('remindAllModal').classList.add('hidden');
        }
        
        document.getElementById('remindAllModalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const periodeId = document.getElementById('periode_id_select').value;
            if (!periodeId) {
                alert('Pilih periode aktif terlebih dahulu!');
                return;
            }
            this.action = '/admin/questionnaire/' + periodeId + '/remind-all';
            this.submit();
        });

        function confirmDelete(periodeName) {
            return confirm(`Apakah Anda yakin ingin menghapus periode "${periodeName}"?\n\nPeringatan: Semua data terkait (kategori, pertanyaan, dan opsi) akan dihapus secara permanen dan tidak dapat dipulihkan.`);
        }
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
