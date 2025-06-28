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
        <x-admin.header>Respons Kuesioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li class="text-gray-700">Respons Periode</li>
            </ol>
        </nav>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-3 rounded mb-4" id="success-alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    <button type="button" class="ml-auto" onclick="document.getElementById('success-alert').style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-3 rounded mb-4" id="error-alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    <button type="button" class="ml-auto" onclick="document.getElementById('error-alert').style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Period Info Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-2 sm:space-y-0">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informasi Periode
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium
                        {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                          ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        <i class="fas {{ $periode->status == 'active' ? 'fa-play-circle' : ($periode->status == 'inactive' ? 'fa-pause-circle' : 'fa-stop-circle') }} mr-1"></i>
                        {{ ucfirst($periode->status) }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-3 sm:p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-play text-green-600 mr-2"></i>
                        <span class="text-xs sm:text-sm font-medium text-gray-600">Tanggal Mulai</span>
                    </div>
                    <p class="text-sm sm:text-lg font-bold text-gray-900">{{ $periode->start_date->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $periode->start_date->format('l') }}</p>
                </div>
                <div class="bg-gradient-to-r from-red-50 to-red-100 p-3 sm:p-4 rounded-lg border border-red-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-stop text-red-600 mr-2"></i>
                        <span class="text-xs sm:text-sm font-medium text-gray-600">Tanggal Selesai</span>
                    </div>
                    <p class="text-sm sm:text-lg font-bold text-gray-900">{{ $periode->end_date->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $periode->end_date->format('l') }}</p>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-3 sm:p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-users text-purple-600 mr-2"></i>
                        <span class="text-xs sm:text-sm font-medium text-gray-600">Target Alumni</span>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-900 leading-tight">{{ $periode->getTargetDescription() }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-3 sm:p-4 rounded-lg border border-green-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                        <span class="text-xs sm:text-sm font-medium text-gray-600">Total Responden</span>
                    </div>
                    <p class="text-sm sm:text-lg font-bold text-gray-900">{{ $userAnswers->total() }}</p>
                    <p class="text-xs text-gray-500">Responden</p>
                </div>
            </div>
        </div>

        <!-- Responses Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md overflow-hidden">
            <!-- Header with filters -->
            <div class="hidden lg:block p-3 sm:p-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-3 lg:space-y-0">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-users mr-2 text-blue-600"></i>
                        Daftar Responden
                    </h2>
                    
                    <!-- Filter buttons - responsive -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode]) }}" 
                            class="px-3 py-1.5 text-xs sm:text-sm border rounded-full transition-colors duration-200 {{ !request('filter') ? 'bg-blue-100 border-blue-300 text-blue-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users mr-1"></i>
                            <span class="hidden sm:inline">Semua</span>
                            <span class="sm:hidden">All</span>
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'alumni']) }}" 
                            class="px-3 py-1.5 text-xs sm:text-sm border rounded-full transition-colors duration-200 {{ request('filter') == 'alumni' ? 'bg-indigo-100 border-indigo-300 text-indigo-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            Alumni
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'company']) }}" 
                            class="px-3 py-1.5 text-xs sm:text-sm border rounded-full transition-colors duration-200 {{ request('filter') == 'company' ? 'bg-green-100 border-green-300 text-green-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-building mr-1"></i>
                            <span class="hidden sm:inline">Perusahaan</span>
                            <span class="sm:hidden">Company</span>
                        </a>
                        <form method="GET" action="" class="inline-block">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIM/username"
                                class="border rounded px-2 py-1 text-xs sm:text-sm mr-2" />
                            @foreach(request()->except(['search', 'page']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                        </form>
                        <form method="GET" action="" class="inline-block" id="filter-prodi-form">
                            <select name="study_program" id="study_program_select" class="border rounded px-2 py-1 text-xs sm:text-sm">
                                <option value="">Semua Program Studi</option>
                                @foreach($studyProgramss as $sp)
                                    <option value="{{ $sp->id_study }}" {{ (string)request('study_program') === (string)$sp->id_study ? 'selected' : '' }}>
                                        {{ $sp->study_program }}
                                    </option>
                                @endforeach
                            </select>
                            @foreach(request()->except(['study_program', 'page']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                        </form>
                        <script>
                        document.getElementById('study_program_select').addEventListener('change', function() {
                            if (this.value === '') {
                                // Submit form tanpa parameter study_program
                                const form = document.getElementById('filter-prodi-form');
                                // Buat URL baru tanpa study_program
                                const params = Array.from(form.elements)
                                    .filter(el => el.name && el.name !== 'study_program' && el.value)
                                    .map(el => encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value))
                                    .join('&');
                                window.location = window.location.pathname + (params ? '?' + params : '');
                            } else {
                                this.form.submit();
                            }
                        });
                        </script>
                    </div>
                </div>
            </div>
            
            <!-- Table - Desktop view -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Program Studi</th> 
                            <th class="px-4 py-3">Alumni Dinilai</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Pengisian</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @if(count($userAnswers) > 0)
                            @foreach($userAnswers as $index => $userAnswer)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-4 py-3">{{ ($userAnswers->currentPage() - 1) * $userAnswers->perPage() + $loop->iteration }}</td>
                                    
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium">{{ $userAnswer->display_name }}</p>
                                            @if($userAnswer->additional_info && $userAnswer->user_type_text == 'Alumni')
                                                <p class="text-xs text-gray-500">NIM: {{ $userAnswer->additional_info }}</p>
                                            @endif
                                            @if($userAnswer->nim && $userAnswer->user_type_text == 'Perusahaan')
                                                <p class="text-xs text-gray-500">NIM Alumni: {{ $userAnswer->nim }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500">{{ $userAnswer->username }}</p>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        @if($userAnswer->user_type_text == 'Alumni')
                                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                                <i class="fas fa-graduation-cap mr-1"></i>Alumni
                                            </span>
                                        @elseif($userAnswer->user_type_text == 'Perusahaan')
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-building mr-1"></i>Perusahaan
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                <i class="fas fa-user mr-1"></i>User
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $userAnswer->study_program ?? '-' }}</td>
                                    <td>
                                        @if($userAnswer->user_type_text == 'Perusahaan' && $userAnswer->nim)
                                            @php
                                                // Ambil alumni yang dinilai pada baris ini saja (berdasarkan NIM)
                                                $alumni = \App\Models\Tb_Alumni::where('nim', $userAnswer->nim)->first();
                                            @endphp
                                            @if($alumni)
                                                {{ $alumni->name ?? $alumni->full_name ?? $alumni->nim }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $userAnswer->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            <i class="fas {{ $userAnswer->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                            {{ $userAnswer->status == 'completed' ? 'Selesai' : 'Belum Selesai' }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <div>
                                            <p>{{ \Carbon\Carbon::parse($userAnswer->created_at)->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($userAnswer->created_at)->format('H:i') }}</p>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.questionnaire.response-detail', [$periode->id_periode, $userAnswer->id_user_answer]) }}" 
                                           class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs inline-flex items-center transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-lg font-medium mb-2">Belum ada responden</p>
                                        <p class="text-sm">Belum ada yang mengisi kuesioner untuk periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            {{-- Tambahkan wrapper khusus mobile --}}
            <div class="lg:hidden bg-gray-50 min-h-screen w-full py-2">
                <div class="mb-4 px-2">
                    <div class="flex flex-col gap-2">
                        <!-- Filter buttons dan filter prodi (kode Anda sebelumnya) -->
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode]) }}"
                            class="flex-1 px-2 py-1.5 text-xs border rounded-full text-center transition-colors duration-200 {{ !request('filter') ? 'bg-blue-100 border-blue-300 text-blue-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users mr-1"></i> All
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'alumni'] + request()->except(['filter', 'page'])) }}"
                            class="flex-1 px-2 py-1.5 text-xs border rounded-full text-center transition-colors duration-200 {{ request('filter') == 'alumni' ? 'bg-indigo-100 border-indigo-300 text-indigo-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-graduation-cap mr-1"></i> Alumni
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'company'] + request()->except(['filter', 'page'])) }}"
                            class="flex-1 px-2 py-1.5 text-xs border rounded-full text-center transition-colors duration-200 {{ request('filter') == 'company' ? 'bg-green-100 border-green-300 text-green-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-building mr-1"></i> Company
                        </a>
                        <form method="GET" action="" class="w-full mb-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIM/username"
                                class="w-full border rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200" />
                            @foreach(request()->except(['search', 'page']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                        </form>
                        <form method="GET" action="" id="filter-prodi-form-mobile" class="w-full">
                            <select name="study_program" id="study_program_select_mobile"
                                class="w-full border rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 mt-1">
                                <option value="">Semua Program Studi</option>
                                @foreach($studyProgramss as $sp)
                                    <option value="{{ $sp->id_study }}" {{ (string)request('study_program') === (string)$sp->id_study ? 'selected' : '' }}>
                                        {{ $sp->study_program }}
                                    </option>
                                @endforeach
                            </select>
                            @foreach(request()->except(['study_program', 'page']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                        </form>
                        <script>
                        document.getElementById('study_program_select_mobile').addEventListener('change', function() {
                            if (this.value === '') {
                                const form = document.getElementById('filter-prodi-form-mobile');
                                const params = Array.from(form.elements)
                                    .filter(el => el.name && el.name !== 'study_program' && el.value)
                                    .map(el => encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value))
                                    .join('&');
                                window.location = window.location.pathname + (params ? '?' + params : '');
                            } else {
                                this.form.submit();
                            }
                        });
                        </script>
                    </div>
                </div>
                <div class="space-y-3 p-3 sm:p-4">
                    @if(count($userAnswers) > 0)
                        @foreach($userAnswers as $index => $userAnswer)
                            <div class="bg-white rounded-lg p-3 sm:p-4 border border-gray-200 shadow">
                                <!-- Header -->
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate">{{ $userAnswer->display_name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $userAnswer->username }}</p>
                                        @if($userAnswer->additional_info && $userAnswer->user_type_text == 'Alumni')
                                            <p class="text-xs text-gray-500">NIM: {{ $userAnswer->additional_info }}</p>
                                        @endif
                                        @if($userAnswer->nim && $userAnswer->user_type_text == 'Perusahaan')
                                            <p class="text-xs text-gray-500">NIM Alumni: {{ $userAnswer->nim }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">#{{ ($userAnswers->currentPage() - 1) * $userAnswers->perPage() + $loop->iteration }}</span>
                                </div>

                                <!-- Content -->
                                <div class="grid grid-cols-1 gap-3 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tipe</p>
                                        @if($userAnswer->user_type_text == 'Alumni')
                                            <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                                <i class="fas fa-graduation-cap mr-1"></i>Alumni
                                            </span>
                                        @elseif($userAnswer->user_type_text == 'Perusahaan')
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <i class="fas fa-building mr-1"></i>Company
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                <i class="fas fa-user mr-1"></i>User
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Program Studi</p>
                                        <span class="text-sm font-medium">{{ $userAnswer->study_program ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Alumni Dinilai</p>
                                        @if($userAnswer->user_type_text == 'Perusahaan' && $userAnswer->nim)
                                            @php
                                                $alumni = \App\Models\Tb_Alumni::where('nim', $userAnswer->nim)->first();
                                            @endphp
                                            @if($alumni)
                                                <span class="text-sm font-medium">{{ $alumni->name ?? $alumni->full_name ?? $alumni->nim }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Status</p>
                                        <span class="px-2 py-1 rounded-full text-xs {{ $userAnswer->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            <i class="fas {{ $userAnswer->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                            {{ $userAnswer->status == 'completed' ? 'Selesai' : 'Belum' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Tanggal Pengisian</p>
                                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($userAnswer->created_at)->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex justify-end items-center">
                                    <a href="{{ route('admin.questionnaire.response-detail', [$periode->id_periode, $userAnswer->id_user_answer]) }}" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs inline-flex items-center transition-colors duration-200">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p class="text-lg font-medium mb-2">Belum ada responden</p>
                                <p class="text-sm text-gray-500">Belum ada yang mengisi kuesioner untuk periode ini.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if(count($userAnswers) > 0)
                <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
                        <!-- Results Info -->
                        <div class="text-xs sm:text-sm text-gray-600 order-2 sm:order-1">
                            <span class="font-medium">
                                Menampilkan {{ $userAnswers->firstItem() ?? 0 }} - {{ $userAnswers->lastItem() ?? 0 }} 
                                dari {{ $userAnswers->total() }} responden
                        </div>
                        
                        <!-- Pagination Links -->
                        <div class="order-1 sm:order-2">
                            @if($userAnswers->hasPages())
                                <nav class="flex items-center justify-center sm:justify-end space-x-1" aria-label="Pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($userAnswers->onFirstPage())
                                        <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                                            <i class="fas fa-chevron-left"></i>
                                            <span class="hidden sm:inline ml-1">Previous</span>
                                        </span>
                                    @else
                                        <a href="{{ $userAnswers->previousPageUrl() }}" 
                                            class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                            <i class="fas fa-chevron-left"></i>
                                            <span class="hidden sm:inline ml-1">Previous</span>
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($userAnswers->getUrlRange(1, $userAnswers->lastPage()) as $page => $url)
                                        @if ($page == $userAnswers->currentPage())
                                            <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 rounded">
                                                {{ $page }}
                                            </span>
                                        @elseif ($page == 1 || $page == $userAnswers->lastPage() || ($page >= $userAnswers->currentPage() - 2 && $page <= $userAnswers->currentPage() + 2))
                                            <a href="{{ $url }}" 
                                                class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                                                {{ $page }}
                                            </a>
                                        @elseif ($page == 2 && $userAnswers->currentPage() > 4)
                                            <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                        @elseif ($page == $userAnswers->lastPage() - 1 && $userAnswers->currentPage() < $userAnswers->lastPage() - 3)
                                            <span class="px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm text-gray-400">...</span>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($userAnswers->hasMorePages())
                                        <a href="{{ $userAnswers->nextPageUrl() }}" 
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
            @endif

        </div>

        <!-- Action buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-center mt-4 sm:mt-6 space-y-3 sm:space-y-0">
            <a href="{{ route('admin.questionnaire.index') }}" 
               class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            
            <!-- Export/Additional actions -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-auto">
                    <button id="btn-export-dropdown" type="button"
                        class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 focus:outline-none">
                        <i class="fas fa-download mr-2"></i> Export Data
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="export-dropdown-menu" class="hidden absolute right-0 bottom-full mb-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <a href="{{ route('admin.export-responden', ['id_periode' => $periode->id_periode, 'type' => 'alumni']) }}"
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Export Respon Alumni</a>
                        <a href="{{ route('admin.export-responden', ['id_periode' => $periode->id_periode, 'type' => 'company']) }}"
                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Export Respon Perusahaan</a>
                    </div>
                </div>
                <button class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-chart-bar mr-2"></i> Lihat Statistik
                </button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-export-dropdown');
            const menu = document.getElementById('export-dropdown-menu');
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
