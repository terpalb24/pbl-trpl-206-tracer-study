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
        <x-admin.header>Detail Kuesioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li class="text-gray-700">Detail Periode</li>
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
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 space-y-3 lg:space-y-0">
                <div class="flex-1">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">{{ $periode->periode_name }}</h2>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">Status:</span>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                   ($periode->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                <i class="fas {{ $periode->status == 'active' ? 'fa-play-circle' : ($periode->status == 'draft' ? 'fa-pause-circle' : 'fa-stop-circle') }} mr-1"></i>
                                {{ ucfirst($periode->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ date('d M Y', strtotime($periode->start_date)) }} - {{ date('d M Y', strtotime($periode->end_date)) }}
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" 
                       class="px-3 sm:px-4 py-2 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-1"></i> 
                        <span class="hidden sm:inline">Edit Periode</span>
                        <span class="sm:hidden">Edit</span>
                    </a>
                    <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                       class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-chart-bar mr-1"></i> 
                        <span class="hidden sm:inline">Lihat Respons</span>
                        <span class="sm:hidden">Respons</span>
                    </a>
                </div>
            </div>

            <!-- Periode Expired - Draft Answers Warning -->
            @if($periode->status === 'expired')
                @php
                    $draftCount = \App\Models\Tb_User_Answers::where('id_periode', $periode->id_periode)
                        ->where('status', 'draft')
                        ->count();
                @endphp
                
                @if($draftCount > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mt-4">
                        <div class="flex flex-col sm:flex-row sm:items-start space-y-3 sm:space-y-0">
                            <div class="flex items-start flex-1">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 sm:mr-3 mt-1 flex-shrink-0"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-yellow-800 mb-1">Ada Jawaban Draft yang Belum Diselesaikan</h4>
                                    <p class="text-sm text-yellow-700 mb-3">
                                        Periode sudah berakhir namun terdapat {{ $draftCount }} jawaban yang masih berstatus draft. 
                                        Klik tombol di bawah untuk menyelesaikan semua jawaban draft secara otomatis.
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.questionnaire.complete-drafts', $periode->id_periode) }}" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan semua jawaban draft? Tindakan ini tidak dapat dibatalkan.');"
                                  class="flex-shrink-0">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-3 sm:px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md transition-colors duration-200 w-full sm:w-auto justify-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span class="hidden sm:inline">Selesaikan Semua Draft ({{ $draftCount }})</span>
                                    <span class="sm:hidden">Selesaikan ({{ $draftCount }})</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2 sm:mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-green-800">Periode Selesai</p>
                                <p class="text-sm text-green-700">Semua jawaban telah diselesaikan, tidak ada draft yang tersisa.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        @php
            // Separate categories by type
            $alumniCategories = $periode->categories->where('for_type', 'alumni');
            $companyCategories = $periode->categories->where('for_type', 'company');
            $bothCategories = $periode->categories->where('for_type', 'both');
            
            $hasCategories = $periode->categories->count() > 0;
            $hasAlumniCategories = $alumniCategories->count() > 0 || $bothCategories->count() > 0;
            $hasCompanyCategories = $companyCategories->count() > 0 || $bothCategories->count() > 0;
        @endphp
        
        @if($hasCategories)
            <!-- Tab Navigation -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md mb-4 sm:mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button type="button" 
                                class="tab-button w-1/2 py-3 sm:py-4 px-1 text-center border-b-2 font-medium text-xs sm:text-sm focus:outline-none transition-colors duration-200
                                       {{ $hasAlumniCategories ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                id="alumni-tab"
                                data-tab="alumni"
                                {{ !$hasAlumniCategories ? 'disabled' : '' }}>
                            <i class="fas fa-graduation-cap mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Kategori Alumni</span>
                            <span class="sm:hidden">Alumni</span>
                            <span class="ml-1 sm:ml-2 inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $hasAlumniCategories ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $alumniCategories->count() + $bothCategories->count() }}
                            </span>
                        </button>
                        <button type="button" 
                                class="tab-button w-1/2 py-3 sm:py-4 px-1 text-center border-b-2 font-medium text-xs sm:text-sm focus:outline-none transition-colors duration-200
                                       {{ !$hasAlumniCategories && $hasCompanyCategories ? 'border-green-500 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                id="company-tab"
                                data-tab="company"
                                {{ !$hasCompanyCategories ? 'disabled' : '' }}>
                            <i class="fas fa-building mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Kategori Perusahaan</span>
                            <span class="sm:hidden">Company</span>
                            <span class="ml-1 sm:ml-2 inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-xs font-medium
                                         {{ $hasCompanyCategories ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $companyCategories->count() + $bothCategories->count() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>
            <!-- General Add Category Button -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 text-center border-2 border-dashed border-gray-300 hover:border-gray-400 transition-colors mt-4 sm:mt-6 mb-6">
                <div class="text-gray-400 mb-2">
                    <i class="fas fa-plus-circle text-xl sm:text-2xl"></i>
                </div>
                <p class="text-sm sm:text-base text-gray-600 font-medium mb-4">Tambah Kategori</p>
                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=both" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                        <i class="fas fa-users mr-2"></i>
                        Kategori Umum
                    </a>
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=alumni" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        Kategori Alumni
                    </a>
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=company" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-building mr-2"></i>
                        Kategori Perusahaan
                    </a>
                </div>
            </div>
            <!-- Alumni Tab Content -->
            <div id="alumni-content" class="tab-content {{ !$hasAlumniCategories ? 'hidden' : '' }}">
                @if($hasAlumniCategories)
                    <!-- Alumni-specific categories -->
                    @foreach($alumniCategories->concat($bothCategories) as $category)
                        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 lg:mb-6 border-l-4 border-blue-500">
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 space-y-3 lg:space-y-0">
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center mb-2 space-y-2 sm:space-y-0">
                                        <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ $category->category_name }}</h3>
                                        <span class="sm:ml-3 inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium w-fit
                                                    {{ $category->for_type == 'alumni' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            <i class="fas {{ $category->for_type == 'alumni' ? 'fa-graduation-cap' : 'fa-users' }} mr-1 text-xs"></i>
                                            {{ $category->for_type == 'alumni' ? 'Alumni' : 'Alumni & Perusahaan' }}
                                        </span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-600">
                                        <i class="fas fa-list-ul mr-1"></i>
                                        {{ $category->questions->count() }} pertanyaan
                                        @if($category->order)
                                            | Urutan: {{ $category->order }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('admin.questionnaire.category.edit', [$periode->id_periode, $category->id_category]) }}" 
                                       class="px-2 sm:px-3 py-1 sm:py-2 bg-yellow-500 text-white rounded-md text-xs sm:text-sm hover:bg-yellow-600 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-edit mr-1"></i> 
                                        <span class="hidden sm:inline">Edit Kategori</span>
                                        <span class="sm:hidden">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.questionnaire.category.destroy', [$periode->id_periode, $category->id_category]) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 bg-red-500 text-white rounded-md text-xs sm:text-sm hover:bg-red-600 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-trash mr-1"></i> 
                                            <span class="hidden sm:inline">Hapus Kategori</span>
                                            <span class="sm:hidden">Hapus</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                       class="px-2 sm:px-3 py-1 sm:py-2 bg-blue-500 text-white rounded-md text-xs sm:text-sm hover:bg-blue-600 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-plus mr-1"></i> 
                                        <span class="hidden sm:inline">Tambah Pertanyaan</span>
                                        <span class="sm:hidden">Tambah</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Questions in this category -->
                            @if($category->questions->count() > 0)
                                <div class="space-y-3">
                                    @foreach($category->questions->sortBy('order') as $question)
                                        <div class="bg-gray-50 p-3 sm:p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow 
                                                    {{ ($question->status ?? 'visible') === 'hidden' ? 'border-red-300 bg-red-50 opacity-75' : 'border-gray-200' }}">
                                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-3 lg:space-y-0">
                                                <div class="flex-1 lg:mr-4">
                                                    <div class="flex items-start mb-2">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full mr-3 mt-0.5 flex-shrink-0">
                                                            {{ $question->order ?? '?' }}
                                                        </span>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex flex-col sm:flex-row sm:items-center mb-1 space-y-1 sm:space-y-0">
                                                                <h4 class="font-semibold text-sm sm:text-base text-gray-900 {{ ($question->status ?? 'visible') === 'hidden' ? 'line-through text-gray-500' : '' }} break-words">
                                                                    {{ $question->question }}
                                                                </h4>
                                                                <!-- Status Badge -->
                                                                <span class="sm:ml-3 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium w-fit
                                                                            {{ ($question->status ?? 'visible') === 'hidden' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                    <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                                                    {{ ($question->status ?? 'visible') === 'hidden' ? 'Tersembunyi' : 'Terlihat' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-1 sm:space-y-0 text-xs sm:text-sm text-gray-600">
                                                                <span class="inline-flex items-center">
                                                                    <i class="fas {{ $question->type == 'text' ? 'fa-keyboard' : ($question->type == 'numeric' ? 'fa-calculator' : ($question->type == 'option' ? 'fa-dot-circle' : ($question->type == 'multiple' ? 'fa-check-square' : ($question->type == 'date' ? 'fa-calendar' : ($question->type == 'rating' ? 'fa-star' : ($question->type == 'scale' ? 'fa-chart-line' : 'fa-map-marker-alt')))))) }} mr-1"></i>
                                                                    {{ ucfirst($question->type) }}
                                                                </span>
                                                                @if($question->options->count() > 0)
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas fa-list mr-1"></i>
                                                                        {{ $question->options->count() }} opsi
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($question->depends_on)
                                                        <div class="ml-9 mt-2">
                                                            <p class="text-xs sm:text-sm text-blue-600 bg-blue-50 px-2 sm:px-3 py-1 rounded-full inline-block">
                                                                <i class="fas fa-link mr-1"></i>
                                                                Bergantung pada: 
                                                                {{ \App\Models\Tb_Questions::find($question->depends_on)->question ?? 'Unknown' }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display text configuration for text/scale types -->
                                                    @if($question->type == 'text' && ($question->before_text || $question->after_text))
                                                        <div class="ml-9 mt-2 text-xs sm:text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                            <strong>Preview:</strong> 
                                                            <span class="text-blue-600">{{ $question->before_text }}</span>
                                                            <em class="text-gray-500">[input field]</em>
                                                            <span class="text-blue-600">{{ $question->after_text }}</span>
                                                        </div>
                                                    @elseif($question->type == 'scale' && ($question->before_text || $question->after_text))
                                                        <div class="ml-9 mt-2 text-xs sm:text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                            <strong>Skala:</strong> 
                                                            <span class="text-blue-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span> (1) 
                                                            - (5) 
                                                            <span class="text-blue-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display options if present -->
                                                    @if(($question->type == 'option' || $question->type == 'multiple' || $question->type == 'rating') && $question->options->count() > 0)
                                                        <div class="ml-9 mt-3">
                                                            <details class="text-xs sm:text-sm">
                                                                <summary class="cursor-pointer text-gray-600 hover:text-gray-800">
                                                                    <i class="fas fa-eye mr-1"></i>
                                                                    Lihat opsi jawaban ({{ $question->options->count() }})
                                                                </summary>
                                                                <div class="mt-2 bg-white p-2 sm:p-3 rounded border">
                                                                    @if($question->type == 'rating')
                                                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                                                            @foreach($question->options->sortBy('order') as $option)
                                                                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm font-medium
                                                                                            {{ $option->option == 'Kurang' ? 'bg-red-100 text-red-800' : 
                                                                                               ($option->option == 'Cukup' ? 'bg-yellow-100 text-yellow-800' : 
                                                                                               ($option->option == 'Baik' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                                                                    <i class="fas fa-star mr-1"></i>
                                                                                    {{ $option->option }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @elseif($question->type == 'scale')
                                                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                                                                            <span class="text-xs sm:text-sm text-gray-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
                                                                            <div class="flex gap-1 sm:gap-2 justify-center">
                                                                                @for($i = 1; $i <= 5; $i++)
                                                                                    <span class="inline-flex items-center justify-center w-6 sm:w-8 h-6 sm:h-8 rounded-full border-2 border-gray-300 text-xs sm:text-sm font-bold">
                                                                                        {{ $i }}
                                                                                    </span>
                                                                                @endfor
                                                                            </div>
                                                                            <span class="text-xs sm:text-sm text-gray-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                                        </div>
                                                                    @else
                                                                        <ul class="space-y-1">
                                                                            @foreach($question->options->sortBy('order') as $option)
                                                                                <li class="flex items-center text-xs sm:text-sm">
                                                                                    <span class="inline-flex items-center justify-center w-4 sm:w-5 h-4 sm:h-5 bg-gray-100 text-gray-600 text-xs rounded-full mr-2 flex-shrink-0">
                                                                                        {{ $option->order }}
                                                                                    </span>
                                                                                    <span class="{{ $option->is_other_option ? 'text-blue-600 font-medium' : 'text-gray-700' }} break-words">
                                                                                        {{ $option->option }}
                                                                                        @if($option->is_other_option) 
                                                                                            <span class="text-xs bg-blue-100 text-blue-700 px-1 rounded ml-1">Other</span> 
                                                                                        @endif
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </div>
                                                            </details>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Action buttons -->
                                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 lg:flex-col lg:items-stretch lg:space-y-2 lg:space-x-0 lg:w-auto">
                                                    <!-- Toggle Status Button -->
                                                    <form method="POST" action="{{ route('admin.questionnaire.question.toggle-status', [$periode->id_periode, $question->id_question]) }}" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin {{ ($question->status ?? 'visible') === 'visible' ? 'menyembunyikan' : 'menampilkan' }} pertanyaan ini?');" 
                                                          class="flex-1 lg:flex-none">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 rounded-md text-xs font-medium transition-all duration-200 flex items-center justify-center lg:w-auto
                                                                       {{ ($question->status ?? 'visible') === 'hidden' 
                                                                           ? 'bg-green-100 text-green-700 hover:bg-green-200 hover:shadow-md' 
                                                                           : 'bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md' }}"
                                                                title="{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan pertanyaan' : 'Sembunyikan pertanyaan' }}">
                                                            <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                                                            <span class="hidden sm:inline">{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}</span>
                                                            <span class="sm:hidden">{{ ($question->status ?? 'visible') === 'hidden' ? 'Show' : 'Hide' }}</span>
                                                        </button>
                                                    </form>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.questionnaire.question.edit', [$periode->id_periode, $category->id_category, $question->id_question]) }}" 
                                                       class="flex-1 lg:flex-none px-2 sm:px-3 py-1 sm:py-2 bg-yellow-100 text-yellow-700 rounded-md text-xs hover:bg-yellow-200 transition-all duration-200 hover:shadow-md flex items-center justify-center"
                                                       title="Edit pertanyaan">
                                                        <i class="fas fa-edit mr-1 sm:mr-0 lg:mr-1"></i>
                                                        <span class="sm:hidden lg:inline">Edit</span>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <form method="POST" action="{{ route('admin.questionnaire.question.destroy', [$periode->id_periode, $question->id_question]) }}" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.');" 
                                                          class="flex-1 lg:flex-none">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 bg-red-100 text-red-700 rounded-md text-xs hover:bg-red-200 transition-all duration-200 hover:shadow-md flex items-center justify-center lg:w-full"
                                                                title="Hapus pertanyaan">
                                                            <i class="fas fa-trash mr-1 sm:mr-0 lg:mr-1"></i>
                                                            <span class="sm:hidden lg:inline">Hapus</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg text-center border-2 border-dashed border-gray-300">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-question-circle text-xl sm:text-2xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3">Belum ada pertanyaan di kategori ini.</p>
                                    <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        <span class="hidden sm:inline">Tambah Pertanyaan Pertama</span>
                                        <span class="sm:hidden">Tambah Pertanyaan</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach

                        <!-- Add Alumni Category Button -->
                        <div class="bg-white rounded-xl shadow-md p-6 text-center border-2 border-dashed border-blue-300 hover:border-blue-400 transition-colors">
                            <div class="text-blue-500 mb-2">
                                <i class="fas fa-plus-circle text-3xl"></i>
                            </div>
                            <p class="text-blue-600 font-medium mb-4">Tambah Kategori untuk Alumni</p>
                            <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=alumni" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Buat Kategori Alumni
                            </a>
                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-md p-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-graduation-cap text-6xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Kategori Alumni</h3>
                            <p class="text-gray-500 mb-6">Buat kategori pertama untuk kuesioner alumni.</p>
                            <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=alumni" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Buat Kategori Alumni
                            </a>
                        </div>
                    @endif
                </div>

            <!-- Company Tab Content -->
            <div id="company-content" class="tab-content {{ $hasAlumniCategories ? 'hidden' : '' }}">
                @if($hasCompanyCategories)
                    <!-- Company-specific categories -->
                    @foreach($companyCategories->concat($bothCategories) as $category)
                        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-3 sm:p-4 lg:p-6 mb-3 sm:mb-4 lg:mb-6 border-l-4 border-green-500">
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 space-y-3 lg:space-y-0">
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center mb-2 space-y-2 sm:space-y-0">
                                        <h3 class="text-base sm:text-lg font-bold text-gray-800">{{ $category->category_name }}</h3>
                                        <span class="sm:ml-3 inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium w-fit
                                                    {{ $category->for_type == 'company' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                            <i class="fas {{ $category->for_type == 'company' ? 'fa-building' : 'fa-users' }} mr-1 text-xs"></i>
                                            {{ $category->for_type == 'company' ? 'Perusahaan' : 'Alumni & Perusahaan' }}
                                        </span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-600">
                                        <i class="fas fa-list-ul mr-1"></i>
                                        {{ $category->questions->count() }} pertanyaan
                                        @if($category->order)
                                            | Urutan: {{ $category->order }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('admin.questionnaire.category.edit', [$periode->id_periode, $category->id_category]) }}" 
                                       class="px-2 sm:px-3 py-1 sm:py-2 bg-yellow-500 text-white rounded-md text-xs sm:text-sm hover:bg-yellow-600 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-edit mr-1"></i> 
                                        <span class="hidden sm:inline">Edit Kategori</span>
                                        <span class="sm:hidden">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.questionnaire.category.destroy', [$periode->id_periode, $category->id_category]) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 bg-red-500 text-white rounded-md text-xs sm:text-sm hover:bg-red-600 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-trash mr-1"></i> 
                                            <span class="hidden sm:inline">Hapus Kategori</span>
                                            <span class="sm:hidden">Hapus</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                       class="px-2 sm:px-3 py-1 sm:py-2 bg-green-500 text-white rounded-md text-xs sm:text-sm hover:bg-green-600 transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-plus mr-1"></i> 
                                        <span class="hidden sm:inline">Tambah Pertanyaan</span>
                                        <span class="sm:hidden">Tambah</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Questions in this category (same structure as alumni) -->
                            @if($category->questions->count() > 0)
                                <div class="space-y-3">
                                    @foreach($category->questions->sortBy('order') as $question)
                                        <div class="bg-gray-50 p-3 sm:p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow 
                                                    {{ ($question->status ?? 'visible') === 'hidden' ? 'border-red-300 bg-red-50 opacity-75' : 'border-gray-200' }}">
                                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-3 lg:space-y-0">
                                                <div class="flex-1 lg:mr-4">
                                                    <div class="flex items-start mb-2">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full mr-3 mt-0.5 flex-shrink-0">
                                                            {{ $question->order ?? '?' }}
                                                        </span>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex flex-col sm:flex-row sm:items-center mb-1 space-y-1 sm:space-y-0">
                                                                <h4 class="font-semibold text-sm sm:text-base text-gray-900 {{ ($question->status ?? 'visible') === 'hidden' ? 'line-through text-gray-500' : '' }} break-words">
                                                                    {{ $question->question }}
                                                                </h4>
                                                                <!-- Status Badge -->
                                                                <span class="sm:ml-3 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium w-fit
                                                                            {{ ($question->status ?? 'visible') === 'hidden' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                    <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                                                    {{ ($question->status ?? 'visible') === 'hidden' ? 'Tersembunyi' : 'Terlihat' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-1 sm:space-y-0 text-xs sm:text-sm text-gray-600">
                                                                <span class="inline-flex items-center">
                                                                    <i class="fas {{ $question->type == 'text' ? 'fa-keyboard' : ($question->type == 'numeric' ? 'fa-calculator' : ($question->type == 'option' ? 'fa-dot-circle' : ($question->type == 'multiple' ? 'fa-check-square' : ($question->type == 'date' ? 'fa-calendar' : ($question->type == 'rating' ? 'fa-star' : ($question->type == 'scale' ? 'fa-chart-line' : 'fa-map-marker-alt')))))) }} mr-1"></i>
                                                                    {{ ucfirst($question->type) }}
                                                                </span>
                                                                @if($question->options->count() > 0)
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas fa-list mr-1"></i>
                                                                        {{ $question->options->count() }} opsi
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($question->depends_on)
                                                        <div class="ml-9 mt-2">
                                                            <p class="text-xs sm:text-sm text-blue-600 bg-blue-50 px-2 sm:px-3 py-1 rounded-full inline-block">
                                                                <i class="fas fa-link mr-1"></i>
                                                                Bergantung pada: 
                                                                {{ \App\Models\Tb_Questions::find($question->depends_on)->question ?? 'Unknown' }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display text configuration for text/scale types -->
                                                    @if($question->type == 'text' && ($question->before_text || $question->after_text))
                                                        <div class="ml-9 mt-2 text-xs sm:text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                            <strong>Preview:</strong> 
                                                            <span class="text-blue-600">{{ $question->before_text }}</span>
                                                            <em class="text-gray-500">[input field]</em>
                                                            <span class="text-blue-600">{{ $question->after_text }}</span>
                                                        </div>
                                                    @elseif($question->type == 'scale' && ($question->before_text || $question->after_text))
                                                        <div class="ml-9 mt-2 text-xs sm:text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                            <strong>Skala:</strong> 
                                                            <span class="text-blue-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span> (1) 
                                                            - (5) 
                                                            <span class="text-blue-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display options if present -->
                                                    @if(($question->type == 'option' || $question->type == 'multiple' || $question->type == 'rating') && $question->options->count() > 0)
                                                        <div class="ml-9 mt-3">
                                                            <details class="text-xs sm:text-sm">
                                                                <summary class="cursor-pointer text-gray-600 hover:text-gray-800">
                                                                    <i class="fas fa-eye mr-1"></i>
                                                                    Lihat opsi jawaban ({{ $question->options->count() }})
                                                                </summary>
                                                                <div class="mt-2 bg-white p-2 sm:p-3 rounded border">
                                                                    @if($question->type == 'rating')
                                                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                                                            @foreach($question->options->sortBy('order') as $option)
                                                                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm font-medium
                                                                                            {{ $option->option == 'Kurang' ? 'bg-red-100 text-red-800' : 
                                                                                               ($option->option == 'Cukup' ? 'bg-yellow-100 text-yellow-800' : 
                                                                                               ($option->option == 'Baik' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                                                                    <i class="fas fa-star mr-1"></i>
                                                                                    {{ $option->option }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @elseif($question->type == 'scale')
                                                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                                                                            <span class="text-xs sm:text-sm text-gray-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
                                                                            <div class="flex gap-1 sm:gap-2 justify-center">
                                                                                @for($i = 1; $i <= 5; $i++)
                                                                                    <span class="inline-flex items-center justify-center w-6 sm:w-8 h-6 sm:h-8 rounded-full border-2 border-gray-300 text-xs sm:text-sm font-bold">
                                                                                        {{ $i }}
                                                                                    </span>
                                                                                @endfor
                                                                            </div>
                                                                            <span class="text-xs sm:text-sm text-gray-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                                        </div>
                                                                    @else
                                                                        <ul class="space-y-1">
                                                                            @foreach($question->options->sortBy('order') as $option)
                                                                                <li class="flex items-center text-xs sm:text-sm">
                                                                                    <span class="inline-flex items-center justify-center w-4 sm:w-5 h-4 sm:h-5 bg-gray-100 text-gray-600 text-xs rounded-full mr-2 flex-shrink-0">
                                                                                        {{ $option->order }}
                                                                                    </span>
                                                                                    <span class="{{ $option->is_other_option ? 'text-blue-600 font-medium' : 'text-gray-700' }} break-words">
                                                                                        {{ $option->option }}
                                                                                        @if($option->is_other_option) 
                                                                                            <span class="text-xs bg-blue-100 text-blue-700 px-1 rounded ml-1">Other</span> 
                                                                                        @endif
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </div>
                                                            </details>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Action buttons -->
                                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 lg:flex-col lg:items-stretch lg:space-y-2 lg:space-x-0 lg:w-auto">
                                                    <!-- Toggle Status Button -->
                                                    <form method="POST" action="{{ route('admin.questionnaire.question.toggle-status', [$periode->id_periode, $question->id_question]) }}" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin {{ ($question->status ?? 'visible') === 'visible' ? 'menyembunyikan' : 'menampilkan' }} pertanyaan ini?');" 
                                                          class="flex-1 lg:flex-none">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 rounded-md text-xs font-medium transition-all duration-200 flex items-center justify-center lg:w-auto
                                                                       {{ ($question->status ?? 'visible') === 'hidden' 
                                                                           ? 'bg-green-100 text-green-700 hover:bg-green-200 hover:shadow-md' 
                                                                           : 'bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md' }}"
                                                                title="{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan pertanyaan' : 'Sembunyikan pertanyaan' }}">
                                                            <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                                                            <span class="hidden sm:inline">{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}</span>
                                                            <span class="sm:hidden">{{ ($question->status ?? 'visible') === 'hidden' ? 'Show' : 'Hide' }}</span>
                                                        </button>
                                                    </form>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.questionnaire.question.edit', [$periode->id_periode, $category->id_category, $question->id_question]) }}" 
                                                       class="flex-1 lg:flex-none px-2 sm:px-3 py-1 sm:py-2 bg-yellow-100 text-yellow-700 rounded-md text-xs hover:bg-yellow-200 transition-all duration-200 hover:shadow-md flex items-center justify-center"
                                                       title="Edit pertanyaan">
                                                        <i class="fas fa-edit mr-1 sm:mr-0 lg:mr-1"></i>
                                                        <span class="sm:hidden lg:inline">Edit</span>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <form method="POST" action="{{ route('admin.questionnaire.question.destroy', [$periode->id_periode, $question->id_question]) }}" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.');" 
                                                          class="flex-1 lg:flex-none">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="w-full px-2 sm:px-3 py-1 sm:py-2 bg-red-100 text-red-700 rounded-md text-xs hover:bg-red-200 transition-all duration-200 hover:shadow-md flex items-center justify-center lg:w-auto"
                                                                title="Hapus pertanyaan">
                                                            <i class="fas fa-trash mr-1 sm:mr-0 lg:mr-1"></i>
                                                            <span class="sm:hidden lg:inline">Hapus</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-gray-50 p-4 sm:p-6 rounded-lg text-center border-2 border-dashed border-gray-300">
                                    <div class="text-gray-400 mb-2">
                                        <i class="fas fa-question-circle text-xl sm:text-2xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3">Belum ada pertanyaan di kategori ini.</p>
                                    <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        <span class="hidden sm:inline">Tambah Pertanyaan Pertama</span>
                                        <span class="sm:hidden">Tambah Pertanyaan</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach

                        <!-- Add Company Category Button -->
                        <div class="bg-white rounded-xl shadow-md p-6 text-center border-2 border-dashed border-green-300 hover:border-green-400 transition-colors">
                            <div class="text-green-500 mb-2">
                                <i class="fas fa-plus-circle text-3xl"></i>
                            </div>
                            <p class="text-green-600 font-medium mb-4">Tambah Kategori untuk Perusahaan</p>
                            <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=company" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-building mr-2"></i>
                                Buat Kategori Perusahaan
                            </a>
                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-md p-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-building text-6xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Kategori Perusahaan</h3>
                            <p class="text-gray-500 mb-6">Buat kategori pertama untuk kuesioner perusahaan.</p>
                            <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=company" 
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Buat Kategori Perusahaan
                            </a>
                        </div>
                    @endif
                </div>

            
        @else
            <!-- No categories at all -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-6 sm:p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-folder-open text-4xl sm:text-6xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Belum Ada Kategori</h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">Mulai dengan membuat kategori pertama untuk kuesioner ini.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=alumni" 
                       class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-md text-sm sm:text-base hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        <span class="hidden sm:inline">Kategori Alumni</span>
                        <span class="sm:hidden">Alumni</span>
                    </a>
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=company" 
                       class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-md text-sm sm:text-base hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-building mr-2"></i>
                        <span class="hidden sm:inline">Kategori Perusahaan</span>
                        <span class="sm:hidden">Company</span>
                    </a>
                    <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=both" 
                       class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-purple-600 text-white rounded-md text-sm sm:text-base hover:bg-purple-700 transition-colors duration-200">
                        <i class="fas fa-users mr-2"></i>
                        <span class="hidden sm:inline">Kategori Umum</span>
                        <span class="sm:hidden">Umum</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-start mt-4 sm:mt-6">
            <a href="{{ route('admin.questionnaire.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> 
                <span class="hidden sm:inline">Kembali ke Daftar</span>
                <span class="sm:hidden">Kembali</span>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (button.disabled) return;

                const targetTab = button.getAttribute('data-tab');
                
                // Remove active classes from all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50', 'border-green-500', 'text-green-600', 'bg-green-50');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Add active class to clicked tab
                if (targetTab === 'alumni') {
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
                } else {
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-green-500', 'text-green-600', 'bg-green-50');
                }
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show target tab content
                document.getElementById(targetTab + '-content').classList.remove('hidden');
            });
        });
    });
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
