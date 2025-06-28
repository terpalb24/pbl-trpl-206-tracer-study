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
        <x-admin.header>Edit Periode Kuesioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li class="text-gray-700">Edit Periode</li>
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

        <!-- Edit Form -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md border border-gray-200">
            <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-edit mr-2 text-blue-600"></i>
                    Edit Periode Kuesioner
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Perbarui tanggal mulai, selesai, dan target alumni periode kuesioner</p>
            </div>

            <form action="{{ route('admin.questionnaire.update', $periode->id_periode) }}" method="POST" class="px-4 sm:px-6 py-4 sm:py-6">
                @csrf
                @method('PUT')
                
                <!-- Current Period Info -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6 border border-blue-200">
                    <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Periode Saat Ini
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <div class="bg-white p-3 rounded-md border border-blue-100">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-play text-green-600 mr-2"></i>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Tanggal Mulai</span>
                            </div>
                            <p class="text-sm sm:text-lg font-bold text-gray-900">{{ $periode->start_date->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $periode->start_date->format('l') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-blue-100">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-stop text-red-600 mr-2"></i>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Tanggal Selesai</span>
                            </div>
                            <p class="text-sm sm:text-lg font-bold text-gray-900">{{ $periode->end_date->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $periode->end_date->format('l') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-blue-100 sm:col-span-2 lg:col-span-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-users text-blue-600 mr-2"></i>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Target Alumni</span>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-900">{{ $periode->getTargetDescription() }}</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-medium
                            {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                              ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            <i class="fas {{ $periode->status == 'active' ? 'fa-play-circle' : ($periode->status == 'inactive' ? 'fa-pause-circle' : 'fa-stop-circle') }} mr-1"></i>
                            {{ ucfirst($periode->status) }}
                        </span>
                    </div>
                </div>

                <!-- Tanggal Periode -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-plus text-green-600 mr-1"></i>
                            Tanggal Mulai Baru
                        </label>
                        <input type="date" name="start_date" id="start_date" 
                            value="{{ old('start_date', $periode->start_date->format('Y-m-d')) }}" 
                            required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('start_date')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-minus text-red-600 mr-1"></i>
                            Tanggal Selesai Baru
                        </label>
                        <input type="date" name="end_date" id="end_date" 
                            value="{{ old('end_date', $periode->end_date->format('Y-m-d')) }}" 
                            required
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('end_date')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Target Alumni Section -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3 sm:mb-4">
                        <i class="fas fa-users mr-2"></i>Target Alumni
                    </label>
                    
                    <!-- Target Type Selection -->
                    <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                        <!-- All Alumni -->
                        <label class="flex items-start p-3 sm:p-4 bg-blue-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-blue-100" id="target-all">
                            <input type="radio" 
                                   name="target_type" 
                                   value="all"
                                   class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                   {{ old('target_type', $periode->target_type ?? 'all') === 'all' ? 'checked' : '' }}>
                            <div>
                                <span class="text-sm font-medium text-blue-800">Semua Alumni</span>
                                <p class="text-xs text-blue-600 mt-1">Questionnaire dapat diakses oleh semua alumni terdaftar</p>
                            </div>
                        </label>

                        <!-- Years Ago -->
                        <label class="flex items-start p-3 sm:p-4 bg-green-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-100" id="target-years-ago">
                            <input type="radio" 
                                   name="target_type" 
                                   value="years_ago"
                                   class="mt-1 mr-3 text-green-600 focus:ring-green-500"
                                   {{ old('target_type', $periode->target_type) === 'years_ago' ? 'checked' : '' }}>
                            <div>
                                <span class="text-sm font-medium text-green-800">Alumni N Tahun Lalu</span>
                                <p class="text-xs text-green-600 mt-1">Target alumni berdasarkan berapa tahun lalu mereka lulus (relatif dengan tahun sekarang: {{ now()->year }})</p>
                            </div>
                        </label>

                        <!-- Specific Years -->
                        <!-- <label class="flex items-start p-3 sm:p-4 bg-purple-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-purple-100" id="target-specific">
                            <input type="radio" 
                                   name="target_type" 
                                   value="specific_years"
                                   class="mt-1 mr-3 text-purple-600 focus:ring-purple-500"
                                   {{ old('target_type', $periode->target_type) === 'specific_years' ? 'checked' : '' }}>
                            <div>
                                <span class="text-sm font-medium text-purple-800">Tahun Kelulusan Spesifik</span>
                                <p class="text-xs text-purple-600 mt-1">Target alumni berdasarkan tahun kelulusan yang spesifik</p>
                            </div>
                        </label> -->
                    </div>

                    <!-- Years Ago Options -->
                    <div id="years-ago-section" class="hidden border border-green-200 rounded-lg p-3 sm:p-4 bg-green-50">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 space-y-2 sm:space-y-0">
                            <label class="text-sm font-medium text-green-800">
                                Pilih Alumni yang Lulus Berapa Tahun Lalu
                            </label>
                            <div class="flex space-x-2 text-xs">
                                <button type="button" 
                                        id="select-all-years-ago" 
                                        class="text-green-600 hover:text-green-800 font-medium">
                                    Pilih Semua
                                </button>
                                <span class="text-green-400">|</span>
                                <button type="button" 
                                        id="deselect-all-years-ago" 
                                        class="text-green-600 hover:text-green-800 font-medium">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search Box for Years Ago -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" 
                                       id="search-years-ago" 
                                       placeholder="Cari tahun atau periode..." 
                                       class="w-full pl-10 pr-10 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-green-400"></i>
                                </div>
                                <button type="button" 
                                        id="clear-search-years-ago" 
                                        class="absolute inset-y-0 right-0 items-center pr-3 text-green-400 hover:text-green-600 hidden">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if($yearsAgoOptions->isNotEmpty())
                            <div id="years-ago-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                @foreach($yearsAgoOptions as $option)
                                    <label class="years-ago-item flex items-center justify-between p-3 bg-white rounded border cursor-pointer hover:bg-green-50 transition-colors"
                                           data-search-text="{{ $option['years_ago'] }} tahun lalu {{ $option['year'] }}">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <input type="checkbox" 
                                                   name="years_ago_list[]" 
                                                   value="{{ $option['years_ago'] }}"
                                                   class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 years-ago-checkbox flex-shrink-0"
                                                   {{ in_array($option['years_ago'], old('years_ago_list', $periode->years_ago_list ?? [])) ? 'checked' : '' }}>
                                            <div class="ml-2 min-w-0">
                                                <span class="text-sm font-medium text-gray-700 block truncate">{{ $option['years_ago'] }} tahun lalu</span>
                                                <p class="text-xs text-gray-500">({{ $option['year'] }})</p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded flex-shrink-0 ml-2">
                                            {{ $option['count'] }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <!-- No Results Message for Years Ago -->
                            <div id="no-results-years-ago" class="hidden text-center py-8">
                                <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">Tidak ada hasil yang cocok dengan pencarian Anda.</p>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">
                                    Belum ada data alumni dari tahun-tahun sebelumnya.
                                </p>
                            </div>
                        @endif
                        
                        @error('years_ago_list')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Specific Years Options -->
                    <div id="specific-years-section" class="hidden border border-purple-200 rounded-lg p-3 sm:p-4 bg-purple-50">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 space-y-2 sm:space-y-0">
                            <label class="text-sm font-medium text-purple-800">
                                Pilih Tahun Kelulusan Spesifik
                            </label>
                            <div class="flex space-x-2 text-xs">
                                <button type="button" 
                                        id="select-all-specific-years" 
                                        class="text-purple-600 hover:text-purple-800 font-medium">
                                    Pilih Semua
                                </button>
                                <span class="text-purple-400">|</span>
                                <button type="button" 
                                        id="deselect-all-specific-years" 
                                        class="text-purple-600 hover:text-purple-800 font-medium">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search Box for Specific Years -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" 
                                       id="search-specific-years" 
                                       placeholder="Cari tahun kelulusan..." 
                                       class="w-full pl-10 pr-10 py-2 border border-purple-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-search text-purple-400"></i>
                                </div>
                                <button type="button" 
                                        id="clear-search-specific-years" 
                                        class="absolute inset-y-0 right-0 items-center pr-3 text-purple-400 hover:text-purple-600 hidden">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if(!empty($graduationYears))
                            <div id="specific-years-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                @foreach($graduationYears as $year)
                                    <label class="specific-year-item flex items-center justify-between p-3 bg-white rounded border cursor-pointer hover:bg-purple-50 transition-colors"
                                           data-search-text="{{ $year }}">
                                        <div class="flex items-center flex-1">
                                            <input type="checkbox" 
                                                   name="target_graduation_years[]" 
                                                   value="{{ $year }}"
                                                   class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 specific-year-checkbox flex-shrink-0"
                                                   {{ in_array($year, old('target_graduation_years', $periode->target_graduation_years ?? [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $year }}</span>
                                        </div>
                                        <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded flex-shrink-0 ml-2">
                                            {{ $graduationYearsWithCount[$year] ?? 0 }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <!-- No Results Message for Specific Years -->
                            <div id="no-results-specific-years" class="hidden text-center py-8">
                                <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">Tidak ada hasil yang cocok dengan pencarian Anda.</p>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">
                                    Belum ada data alumni dengan tahun kelulusan.
                                </p>
                            </div>
                        @endif
                        
                        @error('target_graduation_years')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Preview Target Alumni -->
                    <div class="mt-4 p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-800 mb-2">
                            <i class="fas fa-eye mr-2"></i>Preview Target Alumni:
                        </h4>
                        <p id="target-preview" class="text-sm text-gray-700 font-medium">{{ $periode->getTargetDescription() }}</p>
                        <p id="alumni-count" class="text-xs text-gray-600 mt-1"></p>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-lightbulb text-yellow-600 mr-2 sm:mr-3 mt-1 flex-shrink-0"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-800 mb-1">Informasi Perubahan</h4>
                            <div class="text-xs sm:text-sm text-yellow-700 space-y-1">
                                <p>• Status periode akan otomatis diperbarui berdasarkan tanggal yang baru setelah disimpan</p>
                                <p>• Perubahan target alumni akan mempengaruhi siapa saja yang dapat mengakses questionnaire</p>
                                <p>• Alumni yang sudah mengisi questionnaire tetap dapat mengakses meskipun target berubah</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col lg:flex-row justify-between items-center space-y-3 lg:space-y-0 lg:space-x-4 pt-4 sm:pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full lg:w-auto">
                        <a href="{{ route('admin.questionnaire.index') }}" 
                           class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar
                        </a>
                        <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                           class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full lg:w-auto">
                        <button type="button" onclick="resetForm()" 
                                class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                        <button type="submit" 
                                class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetTypeRadios = document.querySelectorAll('input[name="target_type"]');
        const yearsAgoSection = document.getElementById('years-ago-section');
        const specificYearsSection = document.getElementById('specific-years-section');
        const targetPreview = document.getElementById('target-preview');
        const alumniCount = document.getElementById('alumni-count');
        
        // Data alumni
        const alumniData = @json($graduationYearsWithCount);
        const yearsAgoData = @json($yearsAgoOptions);
        const totalAlumni = Object.values(alumniData).reduce((sum, count) => sum + count, 0);
        const currentYear = {{ now()->year }};

        // Search functionality for Years Ago
        const searchYearsAgo = document.getElementById('search-years-ago');
        const clearSearchYearsAgo = document.getElementById('clear-search-years-ago');
        const yearsAgoContainer = document.getElementById('years-ago-container');
        const noResultsYearsAgo = document.getElementById('no-results-years-ago');

        if (searchYearsAgo) {
            searchYearsAgo.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.years-ago-item');
                let visibleCount = 0;

                // Show/hide clear button
                if (searchTerm) {
                    clearSearchYearsAgo.classList.remove('hidden');
                } else {
                    clearSearchYearsAgo.classList.add('hidden');
                }

                items.forEach(item => {
                    const searchText = item.getAttribute('data-search-text').toLowerCase();
                    if (searchText.includes(searchTerm)) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0 && searchTerm) {
                    yearsAgoContainer.style.display = 'none';
                    noResultsYearsAgo.classList.remove('hidden');
                } else {
                    yearsAgoContainer.style.display = '';
                    noResultsYearsAgo.classList.add('hidden');
                }
            });

            clearSearchYearsAgo.addEventListener('click', function() {
                searchYearsAgo.value = '';
                this.classList.add('hidden');
                
                // Reset visibility
                document.querySelectorAll('.years-ago-item').forEach(item => {
                    item.style.display = '';
                });
                yearsAgoContainer.style.display = '';
                noResultsYearsAgo.classList.add('hidden');
            });
        }

        // Search functionality for Specific Years
        const searchSpecificYears = document.getElementById('search-specific-years');
        const clearSearchSpecificYears = document.getElementById('clear-search-specific-years');
        const specificYearsContainer = document.getElementById('specific-years-container');
        const noResultsSpecificYears = document.getElementById('no-results-specific-years');

        if (searchSpecificYears) {
            searchSpecificYears.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.specific-year-item');
                let visibleCount = 0;

                // Show/hide clear button
                if (searchTerm) {
                    clearSearchSpecificYears.classList.remove('hidden');
                } else {
                    clearSearchSpecificYears.classList.add('hidden');
                }

                items.forEach(item => {
                    const searchText = item.getAttribute('data-search-text').toLowerCase();
                    if (searchText.includes(searchTerm)) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0 && searchTerm) {
                    specificYearsContainer.style.display = 'none';
                    noResultsSpecificYears.classList.remove('hidden');
                } else {
                    specificYearsContainer.style.display = '';
                    noResultsSpecificYears.classList.add('hidden');
                }
            });

            clearSearchSpecificYears.addEventListener('click', function() {
                searchSpecificYears.value = '';
                this.classList.add('hidden');
                
                // Reset visibility
                document.querySelectorAll('.specific-year-item').forEach(item => {
                    item.style.display = '';
                });
                specificYearsContainer.style.display = '';
                noResultsSpecificYears.classList.add('hidden');
            });
        }

        // Handle target type changes
        targetTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Reset search boxes saat berganti target type
                if (searchYearsAgo) {
                    searchYearsAgo.value = '';
                    clearSearchYearsAgo.classList.add('hidden');
                    document.querySelectorAll('.years-ago-item').forEach(item => {
                        item.style.display = '';
                    });
                    if (yearsAgoContainer) yearsAgoContainer.style.display = '';
                    if (noResultsYearsAgo) noResultsYearsAgo.classList.add('hidden');
                }

                if (searchSpecificYears) {
                    searchSpecificYears.value = '';
                    clearSearchSpecificYears.classList.add('hidden');
                    document.querySelectorAll('.specific-year-item').forEach(item => {
                        item.style.display = '';
                    });
                    if (specificYearsContainer) specificYearsContainer.style.display = '';
                    if (noResultsSpecificYears) noResultsSpecificYears.classList.add('hidden');
                }

                // Hide all sections first
                yearsAgoSection.classList.add('hidden');
                specificYearsSection.classList.add('hidden');
                
                // Show relevant section
                if (this.value === 'years_ago') {
                    yearsAgoSection.classList.remove('hidden');
                } else if (this.value === 'specific_years') {
                    specificYearsSection.classList.remove('hidden');
                } else {
                    // Reset all selections when switching to 'all'
                    document.querySelectorAll('.years-ago-checkbox, .specific-year-checkbox').forEach(cb => {
                        cb.checked = false;
                    });
                }
                
                // Update preview
                updatePreview();
                
                // Update border colors
                updateBorderColors(this.value);
            });
        });

        // Update border colors based on selection
        function updateBorderColors(selectedType) {
            document.getElementById('target-all').classList.remove('border-blue-500');
            document.getElementById('target-years-ago').classList.remove('border-green-500');
            document.getElementById('target-specific').classList.remove('border-purple-500');
            
            if (selectedType === 'all') {
                document.getElementById('target-all').classList.add('border-blue-500');
            } else if (selectedType === 'years_ago') {
                document.getElementById('target-years-ago').classList.add('border-green-500');
            } else if (selectedType === 'specific_years') {
                document.getElementById('target-specific').classList.add('border-purple-500');
            }
        }

        // Handle checkbox changes
        document.querySelectorAll('.years-ago-checkbox, .specific-year-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updatePreview);
        });

        // Select/Deselect all buttons dengan search filter
        document.getElementById('select-all-years-ago')?.addEventListener('click', function() {
            document.querySelectorAll('.years-ago-checkbox').forEach(cb => {
                const item = cb.closest('.years-ago-item');
                if (item && item.style.display !== 'none') {
                    cb.checked = true;
                }
            });
            updatePreview();
        });

        document.getElementById('deselect-all-years-ago')?.addEventListener('click', function() {
            document.querySelectorAll('.years-ago-checkbox').forEach(cb => {
                const item = cb.closest('.years-ago-item');
                if (item && item.style.display !== 'none') {
                    cb.checked = false;
                }
            });
            updatePreview();
        });

        document.getElementById('select-all-specific-years')?.addEventListener('click', function() {
            document.querySelectorAll('.specific-year-checkbox').forEach(cb => {
                const item = cb.closest('.specific-year-item');
                if (item && item.style.display !== 'none') {
                    cb.checked = true;
                }
            });
            updatePreview();
        });

        document.getElementById('deselect-all-specific-years')?.addEventListener('click', function() {
            document.querySelectorAll('.specific-year-checkbox').forEach(cb => {
                const item = cb.closest('.specific-year-item');
                if (item && item.style.display !== 'none') {
                    cb.checked = false;
                }
            });
            updatePreview();
        });

        function updatePreview() {
            const selectedTargetType = document.querySelector('input[name="target_type"]:checked')?.value;
            
            if (selectedTargetType === 'all') {
                targetPreview.textContent = 'Semua Alumni';
                alumniCount.textContent = `Total: ${totalAlumni} alumni terdaftar`;
                
            } else if (selectedTargetType === 'years_ago') {
                const selectedYearsAgo = Array.from(document.querySelectorAll('.years-ago-checkbox:checked'))
                    .map(cb => parseInt(cb.value))
                    .sort((a, b) => a - b);

                if (selectedYearsAgo.length === 0) {
                    targetPreview.textContent = 'Belum ada periode yang dipilih';
                    alumniCount.textContent = 'Pilih minimal satu periode tahun lalu';
                } else {
                    const descriptions = selectedYearsAgo.map(yearsAgo => {
                        const year = currentYear - yearsAgo;
                        return `${yearsAgo} tahun lalu (${year})`;
                    });

                    const totalSelectedAlumni = selectedYearsAgo.reduce((sum, yearsAgo) => {
                        const year = (currentYear - yearsAgo).toString();
                        return sum + (alumniData[year] || 0);
                    }, 0);

                    targetPreview.textContent = `Alumni Lulusan: ${descriptions.join(', ')}`;
                    alumniCount.textContent = `Total: ${totalSelectedAlumni} alumni dari ${selectedYearsAgo.length} periode`;
                }
                
            } else if (selectedTargetType === 'specific_years') {
                const selectedYears = Array.from(document.querySelectorAll('.specific-year-checkbox:checked'))
                    .map(cb => cb.value)
                    .sort((a, b) => b - a);

                if (selectedYears.length === 0) {
                    targetPreview.textContent = 'Belum ada tahun yang dipilih';
                    alumniCount.textContent = 'Pilih minimal satu tahun kelulusan';
                } else {
                    const totalSelectedAlumni = selectedYears.reduce((sum, year) => {
                        return sum + (alumniData[year] || 0);
                    }, 0);

                    targetPreview.textContent = `Alumni Lulusan Tahun: ${selectedYears.join(', ')}`;
                    alumniCount.textContent = `Total: ${totalSelectedAlumni} alumni dari ${selectedYears.length} tahun kelulusan`;
                }
            }
        }

        // Initialize on page load
        const initialTargetType = document.querySelector('input[name="target_type"]:checked')?.value || 'all';
        
        if (initialTargetType === 'years_ago') {
            yearsAgoSection.classList.remove('hidden');
        } else if (initialTargetType === 'specific_years') {
            specificYearsSection.classList.remove('hidden');
        }
        
        updateBorderColors(initialTargetType);
        updatePreview();

        // Form validation
        const form = document.querySelector('form');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && startDate >= endDate) {
                endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
            } else {
                endDateInput.setCustomValidity('');
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);

        form.addEventListener('submit', function(event) {
            validateDates();
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });

    // Reset form function
    function resetForm() {
        if (confirm('Apakah Anda yakin ingin mereset form ke nilai awal?')) {
            // Reset search boxes
            const searchYearsAgo = document.getElementById('search-years-ago');
            const clearSearchYearsAgo = document.getElementById('clear-search-years-ago');
            const searchSpecificYears = document.getElementById('search-specific-years');
            const clearSearchSpecificYears = document.getElementById('clear-search-specific-years');

            if (searchYearsAgo) {
                searchYearsAgo.value = '';
                clearSearchYearsAgo.classList.add('hidden');
                document.querySelectorAll('.years-ago-item').forEach(item => {
                    item.style.display = '';
                });
                document.getElementById('years-ago-container').style.display = '';
                document.getElementById('no-results-years-ago').classList.add('hidden');
            }

            if (searchSpecificYears) {
                searchSpecificYears.value = '';
                clearSearchSpecificYears.classList.add('hidden');
                document.querySelectorAll('.specific-year-item').forEach(item => {
                    item.style.display = '';
                });
                document.getElementById('specific-years-container').style.display = '';
                document.getElementById('no-results-specific-years').classList.add('hidden');
            }

            // Reset dates to original values
            document.getElementById('start_date').value = '{{ $periode->start_date->format('Y-m-d') }}';
            document.getElementById('end_date').value = '{{ $periode->end_date->format('Y-m-d') }}';
            
            // Reset target type to original value
            const originalTargetType = '{{ old('target_type', $periode->target_type ?? 'all') }}';
            document.querySelector(`input[name="target_type"][value="${originalTargetType}"]`).checked = true;
            
            // Reset checkboxes based on original data
            document.querySelectorAll('.years-ago-checkbox').forEach(cb => {
                const originalYearsAgo = @json(old('years_ago_list', $periode->years_ago_list ?? []));
                cb.checked = originalYearsAgo.includes(parseInt(cb.value));
            });
            
            document.querySelectorAll('.specific-year-checkbox').forEach(cb => {
                const originalYears = @json(old('target_graduation_years', $periode->target_graduation_years ?? []));
                cb.checked = originalYears.includes(cb.value);
            });
            
            // Trigger change event to update UI
            document.querySelector('input[name="target_type"]:checked').dispatchEvent(new Event('change'));
        }
    }
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
