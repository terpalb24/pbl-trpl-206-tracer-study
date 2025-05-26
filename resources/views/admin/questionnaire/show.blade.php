@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Detail Kuisioner</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Period Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $periode->periode_name }}</h2>
                        <p class="text-gray-600">Status: 
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                            {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                               ($periode->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($periode->status) }}
                            </span>
                        </p>
                        <p class="text-gray-600">Tanggal: {{ date('d M Y', strtotime($periode->start_date)) }} - {{ date('d M Y', strtotime($periode->end_date)) }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md mr-2 hover:bg-yellow-600 transition">
                            <i class="fas fa-edit mr-1"></i> Edit Periode
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-chart-bar mr-1"></i> Lihat Respons
                        </a>
                    </div>
                </div>
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
                <div class="bg-white rounded-xl shadow-md mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex" aria-label="Tabs">
                            <button type="button" 
                                    class="tab-button w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200
                                           {{ $hasAlumniCategories ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    id="alumni-tab"
                                    data-tab="alumni"
                                    {{ !$hasAlumniCategories ? 'disabled' : '' }}>
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Kategori Alumni
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                             {{ $hasAlumniCategories ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $alumniCategories->count() + $bothCategories->count() }}
                                </span>
                            </button>
                            <button type="button" 
                                    class="tab-button w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200
                                           {{ !$hasAlumniCategories && $hasCompanyCategories ? 'border-green-500 text-green-600 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    id="company-tab"
                                    data-tab="company"
                                    {{ !$hasCompanyCategories ? 'disabled' : '' }}>
                                <i class="fas fa-building mr-2"></i>
                                Kategori Perusahaan
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                             {{ $hasCompanyCategories ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $companyCategories->count() + $bothCategories->count() }}
                                </span>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Alumni Tab Content -->
                <div id="alumni-content" class="tab-content {{ !$hasAlumniCategories ? 'hidden' : '' }}">
                    @if($hasAlumniCategories)
                        <!-- Alumni-specific categories -->
                        @foreach($alumniCategories->concat($bothCategories) as $category)
                            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 border-blue-500">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <h3 class="text-lg font-bold text-gray-800">{{ $category->category_name }}</h3>
                                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $category->for_type == 'alumni' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                <i class="fas {{ $category->for_type == 'alumni' ? 'fa-graduation-cap' : 'fa-users' }} mr-1 text-xs"></i>
                                                {{ $category->for_type == 'alumni' ? 'Alumni' : 'Alumni & Perusahaan' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-list-ul mr-1"></i>
                                            {{ $category->questions->count() }} pertanyaan
                                            @if($category->order)
                                                | Urutan: {{ $category->order }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.questionnaire.category.edit', [$periode->id_periode, $category->id_category]) }}" 
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600 transition">
                                            <i class="fas fa-edit mr-1"></i> Edit Kategori
                                        </a>
                                        <form method="POST" action="{{ route('admin.questionnaire.category.destroy', [$periode->id_periode, $category->id_category]) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 transition">
                                                <i class="fas fa-trash mr-1"></i> Hapus Kategori
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                           class="px-3 py-1 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition">
                                            <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                        </a>
                                    </div>
                                </div>

                                <!-- Questions in this category -->
                                @if($category->questions->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($category->questions->sortBy('order') as $question)
                                            <div class="bg-gray-50 p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow 
                                                        {{ ($question->status ?? 'visible') === 'hidden' ? 'border-red-300 bg-red-50 opacity-75' : 'border-gray-200' }}">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="flex items-start mb-2">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full mr-3 mt-0.5">
                                                                {{ $question->order ?? '?' }}
                                                            </span>
                                                            <div class="flex-1">
                                                                <div class="flex items-center mb-1">
                                                                    <h4 class="font-semibold text-gray-900 {{ ($question->status ?? 'visible') === 'hidden' ? 'line-through text-gray-500' : '' }}">
                                                                        {{ $question->question }}
                                                                    </h4>
                                                                    <!-- Status Badge -->
                                                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                                {{ ($question->status ?? 'visible') === 'hidden' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                        <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                                                        {{ ($question->status ?? 'visible') === 'hidden' ? 'Tersembunyi' : 'Terlihat' }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas {{ $question->type == 'text' ? 'fa-keyboard' : ($question->type == 'option' ? 'fa-dot-circle' : ($question->type == 'multiple' ? 'fa-check-square' : ($question->type == 'date' ? 'fa-calendar' : ($question->type == 'rating' ? 'fa-star' : ($question->type == 'scale' ? 'fa-chart-line' : 'fa-map-marker-alt'))))) }} mr-1"></i>
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
                                                                <p class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full inline-block">
                                                                    <i class="fas fa-link mr-1"></i>
                                                                    Bergantung pada: 
                                                                    {{ \App\Models\Tb_Questions::find($question->depends_on)->question ?? 'Unknown' }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Display text configuration for text/scale types -->
                                                        @if($question->type == 'text' && ($question->before_text || $question->after_text))
                                                            <div class="ml-9 mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                                <strong>Preview:</strong> 
                                                                <span class="text-blue-600">{{ $question->before_text }}</span>
                                                                <em class="text-gray-500">[input field]</em>
                                                                <span class="text-blue-600">{{ $question->after_text }}</span>
                                                            </div>
                                                        @elseif($question->type == 'scale' && ($question->before_text || $question->after_text))
                                                            <div class="ml-9 mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                                <strong>Skala:</strong> 
                                                                <span class="text-blue-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span> (1) 
                                                                - (5) 
                                                                <span class="text-blue-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Display options if present -->
                                                        @if(($question->type == 'option' || $question->type == 'multiple' || $question->type == 'rating') && $question->options->count() > 0)
                                                            <div class="ml-9 mt-3">
                                                                <details class="text-sm">
                                                                    <summary class="cursor-pointer text-gray-600 hover:text-gray-800">
                                                                        <i class="fas fa-eye mr-1"></i>
                                                                        Lihat opsi jawaban ({{ $question->options->count() }})
                                                                    </summary>
                                                                    <div class="mt-2 bg-white p-3 rounded border">
                                                                        @if($question->type == 'rating')
                                                                            <div class="flex flex-wrap gap-2">
                                                                                @foreach($question->options->sortBy('order') as $option)
                                                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium
                                                                                                {{ $option->option == 'Kurang' ? 'bg-red-100 text-red-800' : 
                                                                                                   ($option->option == 'Cukup' ? 'bg-yellow-100 text-yellow-800' : 
                                                                                                   ($option->option == 'Baik' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                                                                        <i class="fas fa-star mr-1"></i>
                                                                                        {{ $option->option }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @elseif($question->type == 'scale')
                                                                            <div class="flex items-center justify-between">
                                                                                <span class="text-sm text-gray-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
                                                                                <div class="flex gap-2">
                                                                                    @for($i = 1; $i <= 5; $i++)
                                                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 border-gray-300 text-sm font-bold">
                                                                                            {{ $i }}
                                                                                        </span>
                                                                                    @endfor
                                                                                </div>
                                                                                <span class="text-sm text-gray-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                                            </div>
                                                                        @else
                                                                            <ul class="space-y-1">
                                                                                @foreach($question->options->sortBy('order') as $option)
                                                                                    <li class="flex items-center text-sm">
                                                                                        <span class="inline-flex items-center justify-center w-5 h-5 bg-gray-100 text-gray-600 text-xs rounded-full mr-2">
                                                                                            {{ $option->order }}
                                                                                        </span>
                                                                                        <span class="{{ $option->is_other_option ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
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
                                                    <div class="flex items-center space-x-2 ml-4">
                                                        <!-- Toggle Status Button -->
                                                        <form method="POST" action="{{ route('admin.questionnaire.question.toggle-status', [$periode->id_periode, $question->id_question]) }}" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin {{ ($question->status ?? 'visible') === 'visible' ? 'menyembunyikan' : 'menampilkan' }} pertanyaan ini?');" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center
                                                                           {{ ($question->status ?? 'visible') === 'hidden' 
                                                                               ? 'bg-green-100 text-green-700 hover:bg-green-200 hover:shadow-md' 
                                                                               : 'bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md' }}"
                                                                    title="{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan pertanyaan' : 'Sembunyikan pertanyaan' }}">
                                                                <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                                                                {{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}
                                                            </button>
                                                        </form>

                                                        <!-- Edit Button -->
                                                        <a href="{{ route('admin.questionnaire.question.edit', [$periode->id_periode, $category->id_category, $question->id_question]) }}" 
                                                           class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-md text-sm hover:bg-yellow-200 transition-all duration-200 hover:shadow-md"
                                                           title="Edit pertanyaan">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete Button -->
                                                        <form method="POST" action="{{ route('admin.questionnaire.question.destroy', [$periode->id_periode, $question->id_question]) }}" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.');" 
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="px-3 py-2 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200 transition-all duration-200 hover:shadow-md"
                                                                    title="Hapus pertanyaan">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-6 rounded-lg text-center border-2 border-dashed border-gray-300">
                                        <div class="text-gray-400 mb-2">
                                            <i class="fas fa-question-circle text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 mb-3">Belum ada pertanyaan di kategori ini.</p>
                                        <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition">
                                            <i class="fas fa-plus mr-2"></i>
                                            Tambah Pertanyaan Pertama
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
                            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4 border-green-500">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <h3 class="text-lg font-bold text-gray-800">{{ $category->category_name }}</h3>
                                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $category->for_type == 'company' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                                <i class="fas {{ $category->for_type == 'company' ? 'fa-building' : 'fa-users' }} mr-1 text-xs"></i>
                                                {{ $category->for_type == 'company' ? 'Perusahaan' : 'Alumni & Perusahaan' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-list-ul mr-1"></i>
                                            {{ $category->questions->count() }} pertanyaan
                                            @if($category->order)
                                                | Urutan: {{ $category->order }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.questionnaire.category.edit', [$periode->id_periode, $category->id_category]) }}" 
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600 transition">
                                            <i class="fas fa-edit mr-1"></i> Edit Kategori
                                        </a>
                                        <form method="POST" action="{{ route('admin.questionnaire.category.destroy', [$periode->id_periode, $category->id_category]) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 transition">
                                                <i class="fas fa-trash mr-1"></i> Hapus Kategori
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                           class="px-3 py-1 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition">
                                            <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                        </a>
                                    </div>
                                </div>

                                <!-- Questions in this category (same structure as alumni) -->
                                @if($category->questions->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($category->questions->sortBy('order') as $question)
                                            <div class="bg-gray-50 p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow 
                                                        {{ ($question->status ?? 'visible') === 'hidden' ? 'border-red-300 bg-red-50 opacity-75' : 'border-gray-200' }}">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="flex items-start mb-2">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full mr-3 mt-0.5">
                                                                {{ $question->order ?? '?' }}
                                                            </span>
                                                            <div class="flex-1">
                                                                <div class="flex items-center mb-1">
                                                                    <h4 class="font-semibold text-gray-900 {{ ($question->status ?? 'visible') === 'hidden' ? 'line-through text-gray-500' : '' }}">
                                                                        {{ $question->question }}
                                                                    </h4>
                                                                    <!-- Status Badge -->
                                                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                                {{ ($question->status ?? 'visible') === 'hidden' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                                        <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                                                        {{ ($question->status ?? 'visible') === 'hidden' ? 'Tersembunyi' : 'Terlihat' }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas {{ $question->type == 'text' ? 'fa-keyboard' : ($question->type == 'option' ? 'fa-dot-circle' : ($question->type == 'multiple' ? 'fa-check-square' : ($question->type == 'date' ? 'fa-calendar' : ($question->type == 'rating' ? 'fa-star' : ($question->type == 'scale' ? 'fa-chart-line' : 'fa-map-marker-alt'))))) }} mr-1"></i>
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
                                                                <p class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-full inline-block">
                                                                    <i class="fas fa-link mr-1"></i>
                                                                    Bergantung pada: 
                                                                    {{ \App\Models\Tb_Questions::find($question->depends_on)->question ?? 'Unknown' }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Display text configuration for text/scale types -->
                                                        @if($question->type == 'text' && ($question->before_text || $question->after_text))
                                                            <div class="ml-9 mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                                <strong>Preview:</strong> 
                                                                <span class="text-blue-600">{{ $question->before_text }}</span>
                                                                <em class="text-gray-500">[input field]</em>
                                                                <span class="text-blue-600">{{ $question->after_text }}</span>
                                                            </div>
                                                        @elseif($question->type == 'scale' && ($question->before_text || $question->after_text))
                                                            <div class="ml-9 mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded">
                                                                <strong>Skala:</strong> 
                                                                <span class="text-blue-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span> (1) 
                                                                - (5) 
                                                                <span class="text-blue-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Display options if present -->
                                                        @if(($question->type == 'option' || $question->type == 'multiple' || $question->type == 'rating') && $question->options->count() > 0)
                                                            <div class="ml-9 mt-3">
                                                                <details class="text-sm">
                                                                    <summary class="cursor-pointer text-gray-600 hover:text-gray-800">
                                                                        <i class="fas fa-eye mr-1"></i>
                                                                        Lihat opsi jawaban ({{ $question->options->count() }})
                                                                    </summary>
                                                                    <div class="mt-2 bg-white p-3 rounded border">
                                                                        @if($question->type == 'rating')
                                                                            <div class="flex flex-wrap gap-2">
                                                                                @foreach($question->options->sortBy('order') as $option)
                                                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium
                                                                                                {{ $option->option == 'Kurang' ? 'bg-red-100 text-red-800' : 
                                                                                                   ($option->option == 'Cukup' ? 'bg-yellow-100 text-yellow-800' : 
                                                                                                   ($option->option == 'Baik' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                                                                        <i class="fas fa-star mr-1"></i>
                                                                                        {{ $option->option }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @elseif($question->type == 'scale')
                                                                            <div class="flex items-center justify-between">
                                                                                <span class="text-sm text-gray-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
                                                                                <div class="flex gap-2">
                                                                                    @for($i = 1; $i <= 5; $i++)
                                                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 border-gray-300 text-sm font-bold">
                                                                                            {{ $i }}
                                                                                        </span>
                                                                                    @endfor
                                                                                </div>
                                                                                <span class="text-sm text-gray-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                                                                            </div>
                                                                        @else
                                                                            <ul class="space-y-1">
                                                                                @foreach($question->options->sortBy('order') as $option)
                                                                                    <li class="flex items-center text-sm">
                                                                                        <span class="inline-flex items-center justify-center w-5 h-5 bg-gray-100 text-gray-600 text-xs rounded-full mr-2">
                                                                                            {{ $option->order }}
                                                                                        </span>
                                                                                        <span class="{{ $option->is_other_option ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
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
                                                    <div class="flex items-center space-x-2 ml-4">
                                                        <!-- Toggle Status Button -->
                                                        <form method="POST" action="{{ route('admin.questionnaire.question.toggle-status', [$periode->id_periode, $question->id_question]) }}" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin {{ ($question->status ?? 'visible') === 'visible' ? 'menyembunyikan' : 'menampilkan' }} pertanyaan ini?');" 
                                                              class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center
                                                                           {{ ($question->status ?? 'visible') === 'hidden' 
                                                                               ? 'bg-green-100 text-green-700 hover:bg-green-200 hover:shadow-md' 
                                                                               : 'bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md' }}"
                                                                    title="{{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan pertanyaan' : 'Sembunyikan pertanyaan' }}">
                                                                <i class="fas {{ ($question->status ?? 'visible') === 'hidden' ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                                                                {{ ($question->status ?? 'visible') === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}
                                                            </button>
                                                        </form>

                                                        <!-- Edit Button -->
                                                        <a href="{{ route('admin.questionnaire.question.edit', [$periode->id_periode, $category->id_category, $question->id_question]) }}" 
                                                           class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-md text-sm hover:bg-yellow-200 transition-all duration-200 hover:shadow-md"
                                                           title="Edit pertanyaan">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete Button -->
                                                        <form method="POST" action="{{ route('admin.questionnaire.question.destroy', [$periode->id_periode, $question->id_question]) }}" 
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.');" 
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="px-3 py-2 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200 transition-all duration-200 hover:shadow-md"
                                                                    title="Hapus pertanyaan">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-6 rounded-lg text-center border-2 border-dashed border-gray-300">
                                        <div class="text-gray-400 mb-2">
                                            <i class="fas fa-question-circle text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 mb-3">Belum ada pertanyaan di kategori ini.</p>
                                        <a href="{{ route('admin.questionnaire.question.create', [$periode->id_periode, $category->id_category]) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition">
                                            <i class="fas fa-plus mr-2"></i>
                                            Tambah Pertanyaan Pertama
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

                <!-- General Add Category Button -->
                <div class="bg-white rounded-xl shadow-md p-6 text-center border-2 border-dashed border-gray-300 hover:border-gray-400 transition-colors mt-6">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-plus-circle text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium mb-4">Tambah Kategori untuk Semua Pengguna</p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=both" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            <i class="fas fa-users mr-2"></i>
                            Kategori Umum
                        </a>
                        <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <i class="fas fa-plus mr-2"></i>
                            Kategori Baru
                        </a>
                    </div>
                </div>
            @else
                <!-- No categories at all -->
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-folder-open text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan membuat kategori pertama untuk kuesioner ini.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=alumni" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Kategori Alumni
                        </a>
                        <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=company" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            <i class="fas fa-building mr-2"></i>
                            Kategori Perusahaan
                        </a>
                        <a href="{{ route('admin.questionnaire.category.create', $periode->id_periode) }}?for_type=both" 
                           class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            <i class="fas fa-users mr-2"></i>
                            Kategori Umum
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar functionality
        document.getElementById('toggle-sidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('hidden');
        });

        document.getElementById('close-sidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.add('hidden');
        });

        // Profile dropdown functionality
        document.getElementById('profile-toggle').addEventListener('click', () => {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profile-dropdown');
            const toggle = document.getElementById('profile-toggle');
            
            if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Logout functionality
        document.getElementById('logout-btn').addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';

            const csrfTokenInput = document.createElement('input');
            csrfTokenInput.type = 'hidden';
            csrfTokenInput.name = '_token';
            csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.appendChild(csrfTokenInput);
            document.body.appendChild(form);
            form.submit();
        });

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
@endsection
