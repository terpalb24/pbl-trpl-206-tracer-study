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
        <x-admin.header>Edit Pertanyaan</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li><a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="text-blue-600 hover:underline">Detail Periode</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li class="text-gray-700">Edit Pertanyaan</li>
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
                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                    Edit Pertanyaan
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Kategori: {{ $category->category_name }}
                </p>
            </div>

            <!-- Helper section -->
            <div class="px-4 sm:px-6 py-3 bg-yellow-50 border-b border-yellow-200">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">
                            Tipe pertanyaan saat ini: <span class="text-blue-700">{{ ucfirst($question->type) }}</span>
                        </p>
                        <p class="text-xs text-yellow-700 mt-1">
                            Untuk mengubah tipe pertanyaan, hapus dan buat ulang pertanyaan ini.
                        </p>
                    </div>
                </div>
            </div>

            <form id="questionForm" method="POST" action="{{ route('admin.questionnaire.question.update', [$periode->id_periode, $category->id_category, $question->id_question]) }}" class="px-4 sm:px-6 py-4 sm:py-6">
                @csrf
                @method('PUT')

                <!-- Question text -->
                <div class="mb-4 sm:mb-6">
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-question-circle text-blue-600 mr-1"></i>
                        Pertanyaan
                    </label>
                    <textarea name="question" 
                              id="question" 
                              rows="3" 
                              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('question') ? 'border-red-500' : 'border-gray-300' }}"
                              required>{{ old('question', $question->question) }}</textarea>
                    @error('question')
                        <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Grid untuk Order dan Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up text-green-600 mr-1"></i>
                            Urutan
                        </label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               value="{{ old('order', $question->order) }}" 
                               class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('order') ? 'border-red-500' : 'border-gray-300' }}" 
                               required>
                        @error('order')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Type selection - LOCKED -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-500 mr-1"></i>
                            Tipe Pertanyaan (Terkunci)
                        </label>
                        <select class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" disabled>
                            <option value="{{ $question->type }}" selected>
                                @if($question->type == 'text') Teks
                                @elseif($question->type == 'numeric') Numerik (Hanya Angka)
                                @elseif($question->type == 'email') Email (Validasi Domain)
                                @elseif($question->type == 'option') Pilihan Ganda
                                @elseif($question->type == 'multiple') Multiple Choice
                                @elseif($question->type == 'rating') Rating (Kurang, Cukup, Baik, Baik Sekali)
                                @elseif($question->type == 'scale') Skala Numerik (1, 2, 3, 4, 5)
                                @elseif($question->type == 'date') Tanggal
                                @elseif($question->type == 'location') Lokasi
                                @endif
                            </option>
                        </select>
                        <input type="hidden" name="question_type" value="{{ $question->type }}">
                        <p class="text-xs text-gray-500 mt-1">Tipe pertanyaan tidak dapat diubah setelah dibuat.</p>
                    </div>
                </div>

                <!-- Text options section -->
                @if($question->type == 'text')
                <div id="text-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-keyboard text-blue-600 mr-2"></i>
                        Konfigurasi Teks Input
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="before_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Sebelum Input:</label>
                            <input type="text" 
                                   name="before_text" 
                                   id="before_text" 
                                   value="{{ old('before_text', $question->before_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: Masukkan">
                        </div>
                        <div>
                            <label for="after_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Setelah Input:</label>
                            <input type="text" 
                                   name="after_text" 
                                   id="after_text" 
                                   value="{{ old('after_text', $question->after_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: tahun">
                        </div>
                    </div>
                    
                    <div class="mt-3 bg-blue-50 p-3 rounded-lg text-sm text-blue-700">
                        <strong>Preview:</strong> <span id="text-preview">{{ $question->before_text ?? 'Masukkan' }} [Input Pengguna] {{ $question->after_text ?? 'tahun' }}</span>
                    </div>
                </div>
                @endif

                <!-- Numeric options section -->
                @if($question->type == 'numeric')
                <div id="numeric-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-calculator text-green-600 mr-2"></i>
                        Konfigurasi Input Numerik
                    </h4>
                    
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan numerik hanya menerima input berupa angka (0-9) dan tidak dapat mengetik huruf
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="numeric_before_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Sebelum Input:</label>
                            <input type="text" 
                                   name="before_text" 
                                   id="numeric_before_text" 
                                   value="{{ old('before_text', $question->before_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: Gaji saya sebesar Rp">
                        </div>
                        <div>
                            <label for="numeric_after_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Setelah Input:</label>
                            <input type="text" 
                                   name="after_text" 
                                   id="numeric_after_text" 
                                   value="{{ old('after_text', $question->after_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: per bulan">
                        </div>
                    </div>
                    
                    <div class="mt-3 bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input:</label>
                        <div class="flex items-center flex-wrap gap-2">
                            <span id="numeric-before-preview" class="text-gray-700 font-medium text-sm">{{ $question->before_text ?? '' }}</span>
                            <input type="text" 
                                   class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-32 sm:min-w-48 text-sm" 
                                   placeholder="Masukkan angka..." 
                                   pattern="[0-9]*" 
                                   inputmode="numeric" 
                                   disabled>
                            <span id="numeric-after-preview" class="text-gray-700 font-medium text-sm">{{ $question->after_text ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Email options section -->
                @if($question->type == 'email')
                <div id="email-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-envelope text-purple-600 mr-2"></i>
                        Konfigurasi Input Email
                    </h4>
                    
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan email otomatis memvalidasi format email dan memastikan ada domain
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="email_before_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Sebelum Input:</label>
                            <input type="text" 
                                   name="before_text" 
                                   id="email_before_text" 
                                   value="{{ old('before_text', $question->before_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: Email aktif saya adalah">
                        </div>
                        <div>
                            <label for="email_after_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Setelah Input:</label>
                            <input type="text" 
                                   name="after_text" 
                                   id="email_after_text" 
                                   value="{{ old('after_text', $question->after_text) }}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: yang bisa dihubungi">
                        </div>
                    </div>
                    
                    <div class="mt-3 bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input:</label>
                        <div class="flex items-center flex-wrap gap-2">
                            <span id="email-before-preview" class="text-gray-700 font-medium text-sm">{{ $question->before_text ?? '' }}</span>
                            <input type="email" 
                                   class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-32 sm:min-w-48 text-sm" 
                                   placeholder="contoh@domain.com" 
                                   disabled>
                            <span id="email-after-preview" class="text-gray-700 font-medium text-sm">{{ $question->after_text ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rating options section -->
                @if($question->type == 'rating')
                <div id="rating-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-star text-yellow-600 mr-2"></i>
                        Konfigurasi Rating
                    </h4>
                    
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan rating menampilkan pilihan: Kurang, Cukup, Baik, Baik Sekali
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Rating:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200">
                                <input type="radio" name="preview_rating" value="1" class="mr-2" disabled>
                                <span class="text-red-600 text-sm">Kurang</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200">
                                <input type="radio" name="preview_rating" value="2" class="mr-2" disabled>
                                <span class="text-yellow-600 text-sm">Cukup</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200">
                                <input type="radio" name="preview_rating" value="3" class="mr-2" disabled>
                                <span class="text-blue-600 text-sm">Baik</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200">
                                <input type="radio" name="preview_rating" value="4" class="mr-2" disabled>
                                <span class="text-green-600 text-sm">Baik Sekali</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-lock mr-1"></i>
                            Opsi rating sudah tetap dan tidak dapat diubah. Untuk mengubah opsi, hapus dan buat ulang pertanyaan ini.
                        </p>
                    </div>
                    
                    <!-- Hidden inputs for rating options -->
                    <div class="hidden">
                        @foreach($question->options as $option)
                            <input type="hidden" name="rating_options[]" value="{{ $option->option }}">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Scale options section -->
                @if($question->type == 'scale')
                <div id="scale-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                        Konfigurasi Skala Numerik
                    </h4>
                    
                    <div class="bg-blue-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan skala menampilkan pilihan angka: 1, 2, 3, 4, 5
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                        <div>
                            <label for="scale_min_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai terendah (1):</label>
                            <input type="text" 
                                   id="scale_min_label" 
                                   name="before_text" 
                                   value="{{ old('before_text', $question->before_text ?: 'Sangat Kurang') }}" 
                                   placeholder="Contoh: Sangat Kurang" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="scale_max_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai tertinggi (5):</label>
                            <input type="text" 
                                   id="scale_max_label" 
                                   name="after_text" 
                                   value="{{ old('after_text', $question->after_text ?: 'Sangat Baik') }}" 
                                   placeholder="Contoh: Sangat Baik" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Skala:</label>
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                                <span id="scale-min-preview" class="text-sm text-gray-600 font-medium order-1 sm:order-1">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
                                <div class="flex gap-2 sm:gap-3 order-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center cursor-pointer">
                                            <input type="radio" name="preview_scale" value="{{ $i }}" class="mb-1" disabled>
                                            <span class="text-lg font-bold 
                                                {{ $i == 1 ? 'text-red-600' : 
                                                   ($i == 2 ? 'text-orange-600' : 
                                                   ($i == 3 ? 'text-yellow-600' : 
                                                   ($i == 4 ? 'text-blue-600' : 'text-green-600'))) }}">{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                                <span id="scale-max-preview" class="text-sm text-gray-600 font-medium order-3">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-3 rounded-lg mb-4">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-lock mr-1"></i>
                            Opsi skala (1-5) sudah tetap dan tidak dapat diubah. Anda hanya dapat mengubah label minimum dan maksimum.
                        </p>
                    </div>
                    
                    <!-- Hidden inputs for scale options -->
                    <div class="hidden">
                        @foreach($question->options as $option)
                            <input type="hidden" name="scale_options[]" value="{{ $option->option }}">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Options section for option/multiple type questions -->
                @if($question->type == 'option' || $question->type == 'multiple')
                <div id="options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 space-y-2 sm:space-y-0">
                        <h4 class="text-base sm:text-lg font-medium flex items-center">
                            <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                            Pilihan Jawaban
                        </h4>
                        <button type="button" 
                                id="add-option" 
                                class="px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-plus mr-1"></i> 
                            <span class="hidden sm:inline">Tambah Pilihan</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                    
                    <div id="options-container" class="space-y-3">
                        @if(old('options'))
                            @foreach(old('options') as $index => $option)
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors duration-200">
                                <!-- Main option row -->
                                <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                                    <input type="hidden" name="option_ids[]" value="{{ old('option_ids')[$index] ?? '' }}">
                                    
                                    <!-- Option input - full width on mobile -->
                                    <input type="text" 
                                           name="options[]" 
                                           value="{{ $option }}" 
                                           class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Tuliskan pilihan..." 
                                           required>
                                    
                                    <!-- Controls row -->
                                    <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                                        <!-- Toggle "Lainnya" -->
                                        <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200 {{ in_array($index, old('is_other_option', [])) ? 'bg-orange-50 border-orange-200' : '' }}">
                                            <input type="checkbox" 
                                                   id="is_other_option_{{ $index }}" 
                                                   name="is_other_option[]" 
                                                   value="{{ $index }}" 
                                                   class="other-option-checkbox h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                                   {{ in_array($index, old('is_other_option', [])) ? 'checked' : '' }} 
                                                   onchange="toggleOtherConfig(this)">
                                            <label for="is_other_option_{{ $index }}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                                <i class="fas fa-edit text-orange-600 mr-1"></i>
                                                Lainnya
                                            </label>
                                        </div>
                                        
                                        <!-- Remove button -->
                                        <button type="button" 
                                                class="remove-option flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-md border border-red-300 hover:border-red-500 transition-all duration-200 flex-shrink-0"
                                                title="Hapus pilihan ini">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Konfigurasi "Lainnya" -->
                                <div class="other-text-config {{ in_array($index, old('is_other_option', [])) ? '' : 'hidden' }} bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_{{ $index }}">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-cog text-orange-600 mr-2"></i>
                                        <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                            <input type="text" 
                                                   name="other_before_text[]" 
                                                   value="{{ old('other_before_text')[$index] ?? '' }}" 
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                   placeholder="Contoh: Sebutkan">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                            <input type="text" 
                                                   name="other_after_text[]" 
                                                   value="{{ old('other_after_text')[$index] ?? '' }}" 
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                   placeholder="Contoh: lainnya">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-3 rounded-md border border-gray-200">
                                        <label class="block text-xs font-medium text-gray-600 mb-2">
                                            <i class="fas fa-eye text-gray-500 mr-1"></i>
                                            Preview untuk Pengguna:
                                        </label>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                            <span class="other-before-text text-gray-700 font-medium text-sm">Sebutkan</span>
                                            <input type="text" 
                                                   class="flex-grow sm:min-w-32 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50" 
                                                   placeholder="[Input pengguna]" 
                                                   disabled>
                                            <span class="other-after-text text-gray-700 font-medium text-sm">lainnya</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <span class="other-preview">Sebutkan [Input pengguna] lainnya</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            @foreach($question->options as $index => $option)
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors duration-200">
                                <!-- Main option row -->
                                <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                                    <input type="hidden" name="option_ids[]" value="{{ $option->id_questions_options }}">
                                    
                                    <!-- Option input - full width on mobile -->
                                    <input type="text" 
                                           name="options[]" 
                                           value="{{ $option->option }}" 
                                           class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="Tuliskan pilihan..." 
                                           required>
                                    
                                    <!-- Controls row -->
                                    <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                                        <!-- Toggle "Lainnya" -->
                                        <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200 {{ $option->is_other_option ? 'bg-orange-50 border-orange-200' : '' }}">
                                            <input type="checkbox" 
                                                   id="is_other_option_{{ $index }}" 
                                                   name="is_other_option[]" 
                                                   value="{{ $index }}" 
                                                   class="other-option-checkbox h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                                   {{ $option->is_other_option ? 'checked' : '' }} 
                                                   onchange="toggleOtherConfig(this)">
                                            <label for="is_other_option_{{ $index }}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                                <i class="fas fa-edit text-orange-600 mr-1"></i>
                                                Lainnya
                                            </label>
                                        </div>
                                        
                                        <!-- Remove button -->
                                        <button type="button" 
                                                class="remove-option flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-md border border-red-300 hover:border-red-500 transition-all duration-200 flex-shrink-0"
                                                title="Hapus pilihan ini">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Konfigurasi "Lainnya" -->
                                <div class="other-text-config {{ $option->is_other_option ? '' : 'hidden' }} bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_{{ $index }}">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-cog text-orange-600 mr-2"></i>
                                        <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                            <input type="text" 
                                                   name="other_before_text[]" 
                                                   value="{{ $option->other_before_text ?? '' }}" 
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                   placeholder="Contoh: Sebutkan">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                            <input type="text" 
                                                   name="other_after_text[]" 
                                                   value="{{ $option->other_after_text ?? '' }}" 
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                   placeholder="Contoh: lainnya">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-3 rounded-md border border-gray-200">
                                        <label class="block text-xs font-medium text-gray-600 mb-2">
                                            <i class="fas fa-eye text-gray-500 mr-1"></i>
                                            Preview untuk Pengguna:
                                        </label>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                            <span class="other-before-text text-gray-700 font-medium text-sm">{{ $option->other_before_text ?? 'Sebutkan' }}</span>
                                            <input type="text" class="flex-grow sm:min-w-32 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50" placeholder="[Input pengguna]" disabled>
                                            <span class="other-after-text text-gray-700 font-medium text-sm">{{ $option->other_after_text ?? 'lainnya' }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <span class="other-preview">{{ $option->other_before_text ?? 'Sebutkan' }} [Input pengguna] {{ $option->other_after_text ?? 'lainnya' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif

                <!-- Dependency section -->
                <div class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                    <div class="flex items-start mb-3">
                        <input type="checkbox" 
                               id="toggle-conditional" 
                               name="has_dependency" 
                               value="1" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5 flex-shrink-0" 
                               {{ $question->depends_on ? 'checked' : '' }}>
                        <div class="ml-3">
                            <label for="toggle-conditional" class="text-sm font-medium text-gray-700 cursor-pointer">
                                <i class="fas fa-link text-orange-600 mr-1"></i>
                                Pertanyaan ini muncul berdasarkan jawaban pertanyaan lain
                            </label>
                            <p class="text-xs text-gray-600 mt-1">
                                Aktifkan jika pertanyaan ini hanya muncul ketika jawaban tertentu dipilih
                            </p>
                        </div>
                    </div>
                    
                    <div id="conditional-options" 
                         class="space-y-4" 
                         style="display: {{ $question->depends_on ? 'block' : 'none' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bergantung pada pertanyaan:
                            </label> 
                            <select name="depends_on" 
                                    id="depends_on_select" 
                                    class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('depends_on') ? 'border-red-500' : 'border-gray-300' }}">
                                <option value="">-- Pilih Pertanyaan --</option>
                                @foreach($availableQuestions as $q)
                                    @if(in_array($q->type, ['option', 'multiple', 'rating', 'scale']))
                                        <option value="{{ $q->id_question }}" 
                                                {{ old('depends_on', $question->depends_on) == $q->id_question ? 'selected' : '' }}>
                                            {{ Str::limit($q->question, 50) }} ({{ ucfirst($q->type) }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('depends_on')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Pertanyaan dengan tipe 'option', 'multiple', 'rating', atau 'scale' dapat dijadikan dependensi</p>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Pilih jawaban yang memicu pertanyaan ini:
                                </label>
                                <button type="button" id="add-dependency-answer" 
                                        class="px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700 transition-colors" 
                                        style="display: {{ $question->depends_on ? 'inline-block' : 'none' }};">
                                    <i class="fas fa-plus mr-1"></i> Tambah Jawaban
                                </button>
                            </div>
                            
                            <div id="dependency-answers-container">
                                @php
                                    $existingDependsValues = old('depends_value') ?: 
                                        ($question->depends_value ? explode(',', $question->depends_value) : ['']);
                                @endphp
                                
                                @foreach($existingDependsValues as $index => $dependsValue)
                                    <div class="dependency-answer-item flex items-center mb-2" data-index="{{ $index }}">
                                        <select name="depends_value[]" 
                                                class="depends-value-select flex-grow px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mr-2 {{ $errors->has('depends_value.'.$index) ? 'border-red-500' : 'border-gray-300' }}">
                                            <option value="">-- Pilih Jawaban --</option>
                                        </select>
                                        @if($index === 0)
                                            <span class="text-gray-400 text-sm">(Minimal 1)</span>
                                        @else
                                            <button type="button" class="remove-dependency-answer text-red-500 hover:text-red-700 px-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('depends_value')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            @error('depends_value.*')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            
                            <div class="mt-2 bg-blue-50 p-2 rounded text-xs text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Logika:</strong> Pertanyaan ini akan muncul jika <strong>SALAH SATU</strong> dari jawaban yang dipilih di atas terpilih (OR logic)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for dependency -->
                <input type="hidden" id="hidden_depends_on" name="hidden_depends_on" value="{{ old('depends_on', $question->depends_on) }}">

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 sm:pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                       class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base order-2 sm:order-1">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button id="submit-button"
                            type="submit" 
                            class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base order-1 sm:order-2">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('questionForm');
        const submitButton = document.getElementById('submit-button');
        const questionType = '{{ $question->type }}';
        
        // Enhanced dependency handling untuk multiple answers
        const addDependencyAnswerBtn = document.getElementById('add-dependency-answer');
        const dependencyAnswersContainer = document.getElementById('dependency-answers-container');
        const dependsOnSelect = document.getElementById('depends_on_select');
        
        let dependencyAnswerIndex = document.querySelectorAll('.dependency-answer-item').length;
        
        // Add new dependency answer
        if (addDependencyAnswerBtn && dependencyAnswersContainer) {
            addDependencyAnswerBtn.addEventListener('click', function() {
                const newAnswerDiv = document.createElement('div');
                newAnswerDiv.className = 'dependency-answer-item flex items-center mb-2';
                newAnswerDiv.setAttribute('data-index', dependencyAnswerIndex);
                
                newAnswerDiv.innerHTML = `
                    <select name="depends_value[]" 
                            class="depends-value-select flex-grow px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mr-2">
                        <option value="">-- Pilih Jawaban --</option>
                    </select>
                    <button type="button" class="remove-dependency-answer text-red-500 hover:text-red-700 px-2">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                
                dependencyAnswersContainer.appendChild(newAnswerDiv);
                
                // Populate the new select with options
                const questionId = dependsOnSelect.value;
                if (questionId) {
                    populateDependencyAnswerSelect(newAnswerDiv.querySelector('.depends-value-select'), questionId);
                }
                
                dependencyAnswerIndex++;
            });
            
            // Remove dependency answer
            dependencyAnswersContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-dependency-answer') || e.target.closest('.remove-dependency-answer')) {
                    const answerItem = e.target.closest('.dependency-answer-item');
                    if (answerItem && document.querySelectorAll('.dependency-answer-item').length > 1) {
                        answerItem.remove();
                    }
                }
            });
        }
        
        // Form submission handler
        if (form) {
            form.addEventListener('submit', function(e) {
                // Basic validation
                const questionField = document.getElementById('question');
                const orderField = document.getElementById('order');
                
                if (!questionField.value.trim()) {
                    e.preventDefault();
                    alert('Pertanyaan tidak boleh kosong!');
                    return false;
                }
                
                if (!orderField.value) {
                    e.preventDefault();
                    alert('Urutan pertanyaan harus diisi!');
                    return false;
                }
                
                // For option/multiple types, validate at least one option
                if (questionType === 'option' || questionType === 'multiple') {
                    const optionInputs = document.querySelectorAll('input[name="options[]"]');
                    let hasValidOption = false;
                    
                    optionInputs.forEach(input => {
                        if (input.value.trim()) {
                            hasValidOption = true;
                        }
                    });
                    
                    if (!hasValidOption) {
                        e.preventDefault();
                        alert('Minimal harus ada satu pilihan jawaban!');
                        return false;
                    }
                }
                
                // ✅ Enhanced dependency validation for multiple answers
                const toggleConditional = document.getElementById('toggle-conditional');
                const dependsOnSelect = document.getElementById('depends_on_select');
                const dependsValueSelects = document.querySelectorAll('.depends-value-select'); // ✅ Fixed: Define here
                const hiddenDependsOn = document.getElementById('hidden_depends_on');
                
                if (toggleConditional && toggleConditional.checked) {
                    // Dependency is enabled, validate the selections
                    if (!dependsOnSelect.value) {
                        e.preventDefault();
                        alert('Pilih pertanyaan yang menjadi dependensi!');
                        return false;
                    }

                    // Validate at least one dependency answer is selected
                    let hasSelectedAnswer = false;
                    dependsValueSelects.forEach(select => {
                        if (select.value) {
                            hasSelectedAnswer = true;
                        }
                    });

                    if (!hasSelectedAnswer) {
                        e.preventDefault();
                        alert('Pilih minimal satu jawaban yang memicu pertanyaan ini!');
                        return false;
                    }
                    
                    // Validate no duplicate answers selected
                    const selectedValues = [];
                    let hasDuplicate = false;
                    
                    dependsValueSelects.forEach(select => {
                        if (select.value) {
                            if (selectedValues.includes(select.value)) {
                                hasDuplicate = true;
                            } else {
                                selectedValues.push(select.value);
                            }
                        }
                    });
                    
                    if (hasDuplicate) {
                        e.preventDefault();
                        alert('Tidak boleh memilih jawaban yang sama lebih dari sekali!');
                        return false;
                    }
                    
                    // Copy values to hidden fields for submission
                    hiddenDependsOn.value = dependsOnSelect.value;
                    
                    console.log('Multiple dependency validation passed:', {
                        depends_on: dependsOnSelect.value,
                        depends_values: selectedValues
                    });
                } else {
                    // Dependency is disabled, clear the hidden fields
                    hiddenDependsOn.value = '';
                    dependsOnSelect.value = '';
                    dependsValueSelects.forEach(select => {
                        select.value = '';
                    });
                }
                
                return true;
            });
        }
        
        // Text preview functionality
        if (questionType === 'text') {
            const beforeTextInput = document.getElementById('before_text');
            const afterTextInput = document.getElementById('after_text');
            const textPreview = document.getElementById('text-preview');
            
            if (beforeTextInput && afterTextInput && textPreview) {
                const updateTextPreview = function() {
                    const before = beforeTextInput.value || 'Masukkan';
                    const after = afterTextInput.value || 'tahun';
                    textPreview.textContent = `${before} [Input Pengguna] ${after}`;
                };
                
                beforeTextInput.addEventListener('input', updateTextPreview);
                afterTextInput.addEventListener('input', updateTextPreview);
                updateTextPreview();
            }
        }
        
        // Numeric preview functionality
        if (questionType === 'numeric') {
            const numericBeforeInput = document.getElementById('numeric_before_text');
            const numericAfterInput = document.getElementById('numeric_after_text');
            const numericBeforePreview = document.getElementById('numeric-before-preview');
            const numericAfterPreview = document.getElementById('numeric-after-preview');
            
            if (numericBeforeInput && numericAfterInput && numericBeforePreview && numericAfterPreview) {
                const updateNumericPreview = function() {
                    numericBeforePreview.textContent = numericBeforeInput.value || '';
                    numericAfterPreview.textContent = numericAfterInput.value || '';
                };
                
                numericBeforeInput.addEventListener('input', updateNumericPreview);
                numericAfterInput.addEventListener('input', updateNumericPreview);
                updateNumericPreview();
            }
        }
        
        // Scale preview functionality
        if (questionType === 'scale') {
            const scaleMinLabel = document.getElementById('scale_min_label');
            const scaleMaxLabel = document.getElementById('scale_max_label');
            const scaleMinPreview = document.getElementById('scale-min-preview');
            const scaleMaxPreview = document.getElementById('scale-max-preview');
            
            function updateScalePreview() {
                if (scaleMinLabel && scaleMaxLabel && scaleMinPreview && scaleMaxPreview) {
                    scaleMinPreview.textContent = scaleMinLabel.value || 'Sangat Kurang';
                    scaleMaxPreview.textContent = scaleMaxLabel.value || 'Sangat Baik';
                }
            }
            
            if (scaleMinLabel && scaleMaxLabel) {
                scaleMinLabel.addEventListener('input', updateScalePreview);
                scaleMaxLabel.addEventListener('input', updateScalePreview);
                updateScalePreview();
            }
        }
        
        // Conditional questions handling
        const toggleConditional = document.getElementById('toggle-conditional');
        const conditionalOptions = document.getElementById('conditional-options');
        
        if (toggleConditional && conditionalOptions) {
            toggleConditional.addEventListener('change', function() {
                conditionalOptions.style.display = this.checked ? 'block' : 'none';
                const addBtn = document.getElementById('add-dependency-answer');
            
                if (this.checked) {
                    if (addBtn) addBtn.style.display = 'inline-block';
                } else {
                    if (addBtn) addBtn.style.display = 'none';
                    
                    // Reset dependency selects when unchecked
                    const dependsOnSelect = document.getElementById('depends_on_select');
                    const dependsValueSelects = document.querySelectorAll('.depends-value-select');
                    
                    if (dependsOnSelect) dependsOnSelect.value = '';
                    dependsValueSelects.forEach(select => {
                        select.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
                        select.value = '';
                    });
                }
            });
        }
        
        // Add option functionality
        if (questionType === 'option' || questionType === 'multiple') {
            const addOptionBtn = document.getElementById('add-option');
            const optionsContainer = document.getElementById('options-container');
            
            if (addOptionBtn && optionsContainer) {
                let optionIndex = document.querySelectorAll('input[name="options[]"]').length;
                
                addOptionBtn.addEventListener('click', function() {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors duration-200';
                    optionDiv.innerHTML = `
                        <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                            <input type="hidden" name="option_ids[]" value="">
                            
                            <input type="text" name="options[]" class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan pilihan..." required>
                            
                            <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                                <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                                    <input type="checkbox" id="is_other_option_${optionIndex}" name="is_other_option[]" value="${optionIndex}" class="other-option-checkbox h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" onchange="toggleOtherConfig(this)">
                                    <label for="is_other_option_${optionIndex}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                        <i class="fas fa-edit text-orange-600 mr-1"></i>
                                        Lainnya
                                    </label>
                                </div>
                                
                                <button type="button" class="remove-option flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-md border border-red-300 hover:border-red-500 transition-all duration-200 flex-shrink-0" title="Hapus pilihan ini">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="other-text-config hidden bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_${optionIndex}">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-cog text-orange-600 mr-2"></i>
                                <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                    <input type="text" name="other_before_text[]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: Sebutkan">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                    <input type="text" name="other_after_text[]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: lainnya">
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-md border border-gray-200">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <i class="fas fa-eye text-gray-500 mr-1"></i>
                                    Preview untuk Pengguna:
                                </label>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                    <span class="other-before-text text-gray-700 font-medium text-sm">Sebutkan</span>
                                    <input type="text" class="flex-grow sm:min-w-32 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50" placeholder="[Input pengguna]" disabled>
                                    <span class="other-after-text text-gray-700 font-medium text-sm">lainnya</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <span class="other-preview">Sebutkan [Input pengguna] lainnya</span>
                                </p>
                            </div>
                        </div>
                    `;
                    
                    optionsContainer.appendChild(optionDiv);
                    optionIndex++;
                });
                
                // Enhanced remove option functionality
                optionsContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
                        const optionDiv = e.target.closest('.border.border-gray-200.rounded-lg');
                        if (optionDiv && document.querySelectorAll('input[name="options[]"]').length > 1) {
                            // Add fade out animation before removing
                            optionDiv.style.opacity = '0.5';
                            optionDiv.style.transform = 'scale(0.95)';
                            optionDiv.style.transition = 'all 0.2s ease-out';
                            
                            setTimeout(() => {
                                optionDiv.remove();
                            }, 200);
                        } else if (document.querySelectorAll('input[name="options[]"]').length === 1) {
                            alert('Minimal harus ada satu pilihan jawaban!');
                        }
                    }
                });
            }
        }
        
        // Enhanced dependency options handling
        const questionOptionsMap = {
            @if(isset($availableQuestions) && count($availableQuestions) > 0)
                @foreach($availableQuestions as $q)
                    @if(in_array($q->type, ['option', 'multiple', 'rating', 'scale']) && $q->options && count($q->options) > 0)
                        "{{ $q->id_question }}": [
                            @foreach($q->options as $opt)
                                { 
                                    id: "{{ $opt->id_questions_options }}", 
                                    text: @json($opt->option)
                                },
                            @endforeach
                        ],
                    @endif
                @endforeach
            @endif
        };

        // ✅ Debug: Check if data is loaded correctly
        console.log('Question Options Map:', questionOptionsMap);

        const selectedDependsOn = "{{ old('depends_on', $question->depends_on ?? '') }}";
        // ✅ Enhanced parsing untuk multiple dependency values
        let selectedDependsValues = @json(old('depends_value') ?: ($question->depends_value ? explode(',', $question->depends_value) : []));

        // ✅ Handle case dimana explode PHP belum sempurna atau ada formatting issue
        if (selectedDependsValues.length === 1 && typeof selectedDependsValues[0] === 'string' && selectedDependsValues[0].includes(',')) {
            selectedDependsValues = selectedDependsValues[0].split(',').map(val => val.trim()).filter(val => val !== '');
        }

        // ✅ Ensure values are strings
        selectedDependsValues = selectedDependsValues.map(val => String(val).trim()).filter(val => val !== '');

        console.log('Selected Depends On:', selectedDependsOn);
        console.log('Processed Selected Depends Values:', selectedDependsValues);

        // Function to populate single dependency answer select
        function populateDependencyAnswerSelect(selectElement, questionId, selectedValue = null) {
            if (!selectElement) return;
            
            console.log('Populating select for question:', questionId, 'with selected value:', selectedValue);
            
            selectElement.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
            
            if (questionOptionsMap[questionId] && questionOptionsMap[questionId].length > 0) {
                questionOptionsMap[questionId].forEach(opt => {
                    const optionEl = document.createElement('option');
                    optionEl.value = opt.id;
                    optionEl.textContent = opt.text;
                    // ✅ Enhanced comparison - ensure both are strings
                    if (selectedValue && String(selectedValue).trim() === String(opt.id).trim()) {
                        optionEl.selected = true;
                        console.log('✅ Matched and selected option:', opt.text, 'with ID:', opt.id);
                    }
                    selectElement.appendChild(optionEl);
                });
            } else {
                const noOptionEl = document.createElement('option');
                noOptionEl.value = '';
                noOptionEl.textContent = '-- Tidak ada opsi tersedia --';
                noOptionEl.disabled = true;
                selectElement.appendChild(noOptionEl);
                console.log('❌ No options available for question:', questionId);
            }
        }

        // Function to populate all dependency answer selects
        function populateAllDependencyAnswers(questionId) {
            const allSelects = document.querySelectorAll('.depends-value-select');
            console.log('Populating all selects, found:', allSelects.length, 'selects');
            console.log('Available values to populate:', selectedDependsValues);
            
            allSelects.forEach((select, index) => {
                const selectedValue = selectedDependsValues[index] || null;
                console.log(`Populating select ${index} with value:`, selectedValue);
                populateDependencyAnswerSelect(select, questionId, selectedValue);
            });
        }

        // On page load, if editing and has dependency, populate options
        if (selectedDependsOn && dependsOnSelect) {
            console.log('Initializing dependency selects on page load'); // ✅ Debug
            populateAllDependencyAnswers(selectedDependsOn);
            
            // Show add button if dependency is enabled
            const addBtn = document.getElementById('add-dependency-answer');
            if (addBtn) addBtn.style.display = 'inline-block';
        }

        // On depends_on change
        if (dependsOnSelect) {
            dependsOnSelect.addEventListener('change', function() {
                const addBtn = document.getElementById('add-dependency-answer');
                
                if (this.value) {
                    if (addBtn) addBtn.style.display = 'inline-block';
                    populateAllDependencyAnswers(this.value);
                } else {
                    if (addBtn) addBtn.style.display = 'none';
                    // Clear all dependency answer selects
                    const allSelects = document.querySelectorAll('.depends-value-select');
                    allSelects.forEach(select => {
                        select.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
                    });
                }
                
                const hiddenDependsOn = document.getElementById('hidden_depends_on');
                if (hiddenDependsOn) {
                    hiddenDependsOn.value = this.value;
                }
            });
        }

        // Make functions global for other scripts
        window.populateDependencyAnswerSelect = populateDependencyAnswerSelect;
        window.populateAllDependencyAnswers = populateAllDependencyAnswers;

        // Enhanced toggle function for other option configuration
        function toggleOtherConfig(checkbox) {
            const index = checkbox.value;
            const configDiv = document.getElementById(`other_config_${index}`);
            const parentDiv = checkbox.closest('.border.border-gray-200.rounded-lg');
            const checkboxContainer = checkbox.closest('.bg-gray-50');
            
            if (configDiv) {
                configDiv.classList.toggle('hidden', !checkbox.checked);
                
                // Update visual styling
                if (checkbox.checked) {
                    if (checkboxContainer) {
                        checkboxContainer.classList.remove('bg-gray-50', 'border-gray-200');
                        checkboxContainer.classList.add('bg-orange-50', 'border-orange-200');
                    }
                } else {
                    if (checkboxContainer) {
                        checkboxContainer.classList.remove('bg-orange-50', 'border-orange-200');
                        checkboxContainer.classList.add('bg-gray-50', 'border-gray-200');
                    }
                    
                    // Clear other text fields when hiding
                    const beforeText = configDiv.querySelector('input[name="other_before_text[]"]');
                    const afterText = configDiv.querySelector('input[name="other_after_text[]"]');
                    if (beforeText) beforeText.value = '';
                    if (afterText) afterText.value = '';
                }
                
                // Update preview
                updateOtherPreviews();
            }
        }

        // Enhanced Other option preview functionality
        const updateOtherPreviews = function() {
            document.querySelectorAll('.other-text-config').forEach((config, index) => {
                const beforeText = config.querySelector('input[name="other_before_text[]"]');
                const afterText = config.querySelector('input[name="other_after_text[]"]');
                const preview = config.querySelector('.other-preview');
                const beforeSpan = config.querySelector('.other-before-text');
                const afterSpan = config.querySelector('.other-after-text');
                
                if (beforeText && afterText && preview) {
                    const before = beforeText.value || 'Sebutkan';
                    const after = afterText.value || 'lainnya';
                    
                    // Update preview text
                    preview.textContent = `${before} [Input pengguna] ${after}`;
                    
                    // Update live preview spans
                    if (beforeSpan) beforeSpan.textContent = before;
                    if (afterSpan) afterSpan.textContent = after;
                }
            });
        };

        // Add option functionality with enhanced "Lainnya" support
        if (questionType === 'option' || questionType === 'multiple') {
            const addOptionBtn = document.getElementById('add-option');
            const optionsContainer = document.getElementById('options-container');
            
            if (addOptionBtn && optionsContainer) {
                let optionIndex = document.querySelectorAll('input[name="options[]"]').length;
                
                addOptionBtn.addEventListener('click', function() {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors duration-200';
                    optionDiv.innerHTML = `
                        <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                            <input type="hidden" name="option_ids[]" value="">
                            
                            <input type="text" name="options[]" class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan pilihan..." required>
                            
                            <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                                <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                                    <input type="checkbox" id="is_other_option_${optionIndex}" name="is_other_option[]" value="${optionIndex}" class="other-option-checkbox h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" onchange="toggleOtherConfig(this)">
                                    <label for="is_other_option_${optionIndex}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                        <i class="fas fa-edit text-orange-600 mr-1"></i>
                                        Lainnya
                                    </label>
                                </div>
                                
                                <button type="button" class="remove-option flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-md border border-red-300 hover:border-red-500 transition-all duration-200 flex-shrink-0" title="Hapus pilihan ini">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="other-text-config hidden bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_${optionIndex}">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-cog text-orange-600 mr-2"></i>
                                <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                    <input type="text" name="other_before_text[]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: Sebutkan">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                    <input type="text" name="other_after_text[]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Contoh: lainnya">
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 rounded-md border border-gray-200">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <i class="fas fa-eye text-gray-500 mr-1"></i>
                                    Preview untuk Pengguna:
                                </label>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                    <span class="other-before-text text-gray-700 font-medium text-sm">Sebutkan</span>
                                    <input type="text" class="flex-grow sm:min-w-32 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50" placeholder="[Input pengguna]" disabled>
                                    <span class="other-after-text text-gray-700 font-medium text-sm">lainnya</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <span class="other-preview">Sebutkan [Input pengguna] lainnya</span>
                                </p>
                            </div>
                        </div>
                    `;
                    
                    optionsContainer.appendChild(optionDiv);
                    optionIndex++;
                });
                
                // Remove option functionality
                optionsContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
                        const optionDiv = e.target.closest('.border.border-gray-200.rounded-lg');
                        if (optionDiv && document.querySelectorAll('input[name="options[]"]').length > 1) {
                            // Add fade out animation before removing
                            optionDiv.style.opacity = '0.5';
                            optionDiv.style.transform = 'scale(0.95)';
                            optionDiv.style.transition = 'all 0.2s ease-out';
                            
                            setTimeout(() => {
                                optionDiv.remove();
                            }, 200);
                        } else if (document.querySelectorAll('input[name="options[]"]').length === 1) {
                            alert('Minimal harus ada satu pilihan jawaban!');
                        }
                    }
                });
            }
        }
        
        // Add event listeners for other option text inputs with enhanced functionality
        document.addEventListener('input', function(e) {
            if (e.target.name === 'other_before_text[]' || e.target.name === 'other_after_text[]') {
                updateOtherPreviews();
            }
        });
    });

    // Global function for other option configuration
    function toggleOtherConfig(checkbox) {
        const index = checkbox.value;
        const configDiv = document.getElementById(`other_config_${index}`);
        const parentDiv = checkbox.closest('.border.border-gray-200.rounded-lg');
        const checkboxContainer = checkbox.closest('.bg-gray-50');
        
        if (configDiv) {
            configDiv.classList.toggle('hidden', !checkbox.checked);
            
            // Update visual styling
            if (checkbox.checked) {
                if (checkboxContainer) {
                    checkboxContainer.classList.remove('bg-gray-50', 'border-gray-200');
                    checkboxContainer.classList.add('bg-orange-50', 'border-orange-200');
                }
            } else {
                if (checkboxContainer) {
                    checkboxContainer.classList.remove('bg-orange-50', 'border-orange-200');
                    checkboxContainer.classList.add('bg-gray-50', 'border-gray-200');
                }
                
                // Clear other text fields when hiding
                const beforeText = configDiv.querySelector('input[name="other_before_text[]"]');
                const afterText = configDiv.querySelector('input[name="other_after_text[]"]');
                if (beforeText) beforeText.value = '';
                if (afterText) afterText.value = '';
            }
            
            // Update preview
            updateOtherPreviews();
        }
    }
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
