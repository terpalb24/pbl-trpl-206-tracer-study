@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Pilih Alumni untuk Dinilai'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-4 bg-green-100 text-green-700 rounded-md flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-4 bg-red-100 text-red-700 rounded-md flex items-center shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Breadcrumb -->
            <nav class="mb-4 sm:mb-6">
                <ol class="flex items-center space-x-2 text-sm overflow-x-auto">
                    <li>
                        <a href="{{ route('dashboard.company') }}" class="text-blue-600 hover:underline whitespace-nowrap">Dashboard</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline whitespace-nowrap">Kuesioner</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700 whitespace-nowrap">Pilih Alumni</li>
                </ol>
            </nav>

            <!-- Periode Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-start">
                    <div class="flex-shrink-0 mb-3 sm:mb-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl sm:text-2xl"></i>
                    </div>
                    <div class="sm:ml-4 w-full">
                        <h3 class="text-lg sm:text-xl font-semibold text-blue-900 mb-2">{{ $periode->title }}</h3>
                        @if($periode->description)
                            <p class="text-blue-800 mb-3 text-sm sm:text-base">{{ $periode->description }}</p>
                        @endif
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-2 sm:space-y-0 text-sm text-blue-700">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-circle text-green-500 mr-2"></i>
                                <span>Periode Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Alumni Information -->
            @if(!empty($eligibleGraduationYears))
                <div class="mb-4 sm:mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-0 sm:mr-3 mb-2 sm:mb-0"></i>
                        <div>
                            <p class="text-blue-800 font-medium text-sm sm:text-base">Target Alumni untuk Periode Ini</p>
                            <p class="text-blue-700 text-xs sm:text-sm">
                                Hanya alumni dengan tahun lulus: <strong>{{ implode(', ', $eligibleGraduationYears) }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Alumni yang Tersedia untuk Dinilai -->
            @if($availableAlumni->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-700">ALUMNI YANG DAPAT DINILAI</h2>
                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                            <i class="fas fa-users mr-2"></i>
                            <span id="alumni-count">{{ $availableAlumni->count() }}</span> Alumni Tersedia
                        </div>
                    </div>

                    <!-- Filter dan Search Section -->
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-gray-50 rounded-lg border">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3 sm:gap-4">
                            <!-- Search Box -->
                            <div class="flex-1">
                                <div class="relative">
                                    <input type="text" 
                                           id="search-alumni" 
                                           placeholder="Cari nama, NIM, atau posisi alumni..." 
                                           class="w-full pl-8 sm:pl-10 pr-8 sm:pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-2 sm:pl-3">
                                        <i class="fas fa-search text-gray-400 text-xs sm:text-sm"></i>
                                    </div>
                                    <button type="button" 
                                            id="clear-search" 
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 sm:pr-3 text-gray-400 hover:text-gray-600 hidden">
                                        <i class="fas fa-times text-xs sm:text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Tahun Lulus -->
                            <div class="w-full lg:w-48">
                                <select id="filter-graduation-year" 
                                        class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm">
                                    <option value="">Semua Tahun Lulus</option>
                                    @php
                                        $graduationYears = $availableAlumni->pluck('alumni.graduation_year')->unique()->sort()->reverse();
                                    @endphp
                                    @foreach($graduationYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="w-full lg:w-48">
                                <select id="filter-draft-status" 
                                        class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs sm:text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="draft">Memiliki Draft</option>
                                    <option value="new">Belum Dinilai</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            <button type="button" 
                                    id="reset-filters" 
                                    class="w-full lg:w-auto px-3 sm:px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-md transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-refresh mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Reset</span>
                                <span class="sm:hidden">Reset</span>
                            </button>
                        </div>

                        <!-- Filter Summary -->
                        <div id="filter-summary" class="mt-3 text-xs sm:text-sm text-gray-600 hidden">
                            <span id="filter-summary-text"></span>
                        </div>
                    </div>

                    <!-- Alumni Grid -->
                    <div id="alumni-grid" class="grid gap-3 sm:gap-4 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($availableAlumni as $jobHistory)
                            <div class="alumni-card border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow duration-200"
                                 data-name="{{ strtolower($jobHistory->alumni->name) }}"
                                 data-nim="{{ $jobHistory->nim }}"
                                 data-position="{{ strtolower($jobHistory->position ?? '') }}"
                                 data-graduation-year="{{ $jobHistory->alumni->graduation_year }}"
                                 data-draft-status="{{ in_array($jobHistory->nim, $draftNims) ? 'draft' : 'new' }}">
                                <div class="flex flex-col h-full">
                                    <div class="flex-grow">
                                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                                            <h3 class="font-semibold text-gray-800 text-sm sm:text-base pr-2">{{ $jobHistory->alumni->name }}</h3>
                                            @if(in_array($jobHistory->nim, $draftNims))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 flex-shrink-0">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    <span class="hidden sm:inline">Draft</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0">
                                                    <i class="fas fa-plus mr-1"></i>
                                                    <span class="hidden sm:inline">Baru</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1 sm:space-y-2 text-xs sm:text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-id-card text-gray-400 mr-2 w-3 sm:w-4 flex-shrink-0"></i>
                                                <span class="truncate">{{ $jobHistory->nim }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-graduation-cap text-gray-400 mr-2 w-3 sm:w-4 flex-shrink-0"></i>
                                                <span class="font-medium text-blue-600">Lulus {{ $jobHistory->alumni->graduation_year }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-briefcase text-gray-400 mr-2 w-3 sm:w-4 flex-shrink-0"></i>
                                                <span class="truncate">{{ $jobHistory->position ?? 'Posisi tidak disebutkan' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-2 w-3 sm:w-4 flex-shrink-0"></i>
                                                <span class="truncate">{{ $jobHistory->duration }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 sm:mt-4 pt-3 border-t border-gray-100">
                                        @if(in_array($jobHistory->nim, $draftNims))
                                            <a href="{{ route('company.questionnaire.fill', [$periode->id_periode, $jobHistory->nim]) }}"
                                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200 w-full justify-center">
                                                <i class="fas fa-edit mr-1 sm:mr-2"></i>
                                                <span class="hidden sm:inline">Lanjutkan Draft</span>
                                                <span class="sm:hidden">Draft</span>
                                            </a>
                                        @else
                                            <a href="{{ route('company.questionnaire.fill', [$periode->id_periode, $jobHistory->nim]) }}"
                                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200 w-full justify-center">
                                                <i class="fas fa-clipboard-check mr-1 sm:mr-2"></i>
                                                <span class="hidden sm:inline">Mulai Penilaian</span>
                                                <span class="sm:hidden">Mulai</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- No Results Message -->
                    <div id="no-results" class="hidden text-center py-6 sm:py-8">
                        <div class="text-gray-500 mb-4">
                            <i class="fas fa-search text-3xl sm:text-4xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Tidak Ada Alumni yang Cocok</h3>
                        <p class="text-gray-500 text-sm sm:text-base">
                            Tidak ada alumni yang sesuai dengan kriteria pencarian atau filter yang dipilih.
                        </p>
                        <button type="button" 
                                onclick="resetAllFilters()" 
                                class="mt-4 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200">
                            <i class="fas fa-refresh mr-1 sm:mr-2"></i>
                            Reset Semua Filter
                        </button>
                    </div>
                </div>
            @endif

            <!-- Alumni yang Sudah Dinilai -->
            @if(count($completedNims) > 0)
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-700">ALUMNI YANG SUDAH DINILAI</h2>
                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ count($completedNims) }} Alumni Selesai
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <i class="fas fa-info-circle text-green-500 mr-0 sm:mr-3 mb-2 sm:mb-0"></i>
                            <div>
                                <p class="text-green-800 font-medium text-sm sm:text-base">Penilaian Selesai</p>
                                <p class="text-green-700 text-xs sm:text-sm">
                                    Anda telah menyelesaikan penilaian untuk {{ count($completedNims) }} alumni pada periode ini. 
                                    Alumni yang sudah dinilai tidak dapat dinilai ulang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($availableAlumni->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-users-slash text-4xl sm:text-5xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold mb-2">Tidak Ada Alumni yang Dapat Dinilai</h2>
                    @if(count($completedNims) > 0)
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">
                            Semua alumni yang memenuhi kriteria sudah dinilai pada periode ini.
                        </p>
                    @else
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">
                            Tidak ada alumni yang memenuhi kriteria untuk periode ini:
                        </p>
                        <ul class="text-gray-600 text-xs sm:text-sm space-y-1 mb-4">
                            <li>• Alumni harus masih aktif bekerja di perusahaan Anda</li>
                            @if(!empty($eligibleGraduationYears))
                                <li>• Alumni harus lulus pada tahun: {{ implode(', ', $eligibleGraduationYears) }}</li>
                            @endif
                        </ul>
                    @endif
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200 mt-4">
                        <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>
                        Kembali ke Daftar Kuesioner
                    </a>
                </div>
            @endif

            <!-- Back Button -->
            @if($availableAlumni->isNotEmpty())
                <div class="text-center mt-4 sm:mt-6">
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>
                        Kembali ke Daftar Kuesioner
                    </a>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>

<script>
    // Alumni Search and Filter Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-alumni');
        const clearSearchBtn = document.getElementById('clear-search');
        const graduationYearFilter = document.getElementById('filter-graduation-year');
        const draftStatusFilter = document.getElementById('filter-draft-status');
        const resetFiltersBtn = document.getElementById('reset-filters');
        const alumniCards = document.querySelectorAll('.alumni-card');
        const alumniGrid = document.getElementById('alumni-grid');
        const noResults = document.getElementById('no-results');
        const alumniCount = document.getElementById('alumni-count');
        const filterSummary = document.getElementById('filter-summary');
        const filterSummaryText = document.getElementById('filter-summary-text');

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                // Show/hide clear button
                if (searchTerm) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
                
                filterAlumni();
            });

            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.classList.add('hidden');
                filterAlumni();
            });
        }

        // Filter functionality
        if (graduationYearFilter) {
            graduationYearFilter.addEventListener('change', filterAlumni);
        }

        if (draftStatusFilter) {
            draftStatusFilter.addEventListener('change', filterAlumni);
        }

        // Reset filters
        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', resetAllFilters);
        }

        function filterAlumni() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedYear = graduationYearFilter ? graduationYearFilter.value : '';
            const selectedStatus = draftStatusFilter ? draftStatusFilter.value : '';
            
            let visibleCount = 0;
            let totalCount = alumniCards.length;

            alumniCards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const nim = card.getAttribute('data-nim') || '';
                const position = card.getAttribute('data-position') || '';
                const graduationYear = card.getAttribute('data-graduation-year') || '';
                const draftStatus = card.getAttribute('data-draft-status') || '';

                // Search criteria
                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    nim.includes(searchTerm) || 
                    position.includes(searchTerm);

                // Filter criteria
                const matchesYear = !selectedYear || graduationYear === selectedYear;
                const matchesStatus = !selectedStatus || draftStatus === selectedStatus;

                // Show/hide card
                if (matchesSearch && matchesYear && matchesStatus) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update count
            if (alumniCount) {
                alumniCount.textContent = visibleCount;
            }

            // Show/hide no results message
            if (visibleCount === 0) {
                if (alumniGrid) alumniGrid.style.display = 'none';
                if (noResults) noResults.classList.remove('hidden');
            } else {
                if (alumniGrid) alumniGrid.style.display = '';
                if (noResults) noResults.classList.add('hidden');
            }

            // Update filter summary
            updateFilterSummary(searchTerm, selectedYear, selectedStatus, visibleCount, totalCount);
        }

        function updateFilterSummary(searchTerm, selectedYear, selectedStatus, visibleCount, totalCount) {
            let summaryParts = [];

            if (searchTerm) {
                summaryParts.push(`pencarian "${searchTerm}"`);
            }

            if (selectedYear) {
                summaryParts.push(`tahun lulus ${selectedYear}`);
            }

            if (selectedStatus) {
                const statusText = selectedStatus === 'draft' ? 'memiliki draft' : 'belum dinilai';
                summaryParts.push(`status ${statusText}`);
            }

            if (summaryParts.length > 0) {
                filterSummaryText.textContent = `Menampilkan ${visibleCount} dari ${totalCount} alumni dengan filter: ${summaryParts.join(', ')}`;
                filterSummary.classList.remove('hidden');
            } else {
                filterSummary.classList.add('hidden');
            }
        }

        function resetAllFilters() {
            if (searchInput) {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
            }
            if (graduationYearFilter) graduationYearFilter.value = '';
            if (draftStatusFilter) draftStatusFilter.value = '';
            
            filterAlumni();
        }

        // Make resetAllFilters global
        window.resetAllFilters = resetAllFilters;
    });
</script>
@endsection