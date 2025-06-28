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
        <x-admin.header>Tambah Pertanyaan</x-admin.header>
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
                <li class="text-gray-700">Tambah Pertanyaan</li>
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

        <!-- Create Form -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md border border-gray-200">
            <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                    Tambah Pertanyaan Baru
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Kategori: {{ $category->category_name }}
                </p>
            </div>

                <!-- Form content di dalam div card yang sudah ada -->
                <form method="POST" action="{{ route('admin.questionnaire.question.store', [$periode->id_periode, $category->id_category]) }}" class="px-4 sm:px-6 py-4 sm:py-6">
                    @csrf

                    <!-- Question text -->
                    <div class="mb-4 sm:mb-6">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-question-circle text-blue-600 mr-1"></i>
                            Pertanyaan
                        </label>
                        <textarea name="question" 
                                id="question" 
                                rows="3" 
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('question') ? 'border-red-500' : 'border-gray-300' }}" 
                                required>{{ old('question') }}</textarea>
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
                                value="{{ old('order', $category->questions->count() + 1) }}" 
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('order') ? 'border-red-500' : 'border-gray-300' }}" 
                                required>
                            @error('order')
                                <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Question Type -->
                        <div>
                            <label for="question_type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cogs text-purple-600 mr-1"></i>
                                Tipe Pertanyaan
                            </label>
                            <select name="question_type" 
                                    id="question_type" 
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 {{ $errors->has('question_type') ? 'border-red-500' : 'border-gray-300' }}" 
                                    required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="text" {{ old('question_type') == 'text' ? 'selected' : '' }}>
                                    <i class="fas fa-keyboard"></i> Teks
                                </option>
                                <option value="numeric" {{ old('question_type') == 'numeric' ? 'selected' : '' }}>
                                    <i class="fas fa-calculator"></i> Numerik (Hanya Angka)
                                </option>
                                <option value="email" {{ old('question_type') == 'email' ? 'selected' : '' }}>
                                    <i class="fas fa-envelope"></i> Email (Validasi Domain)
                                </option>
                                <option value="option" {{ old('question_type') == 'option' ? 'selected' : '' }}>
                                    <i class="fas fa-list"></i> Pilihan Ganda
                                </option>
                                <option value="multiple" {{ old('question_type') == 'multiple' ? 'selected' : '' }}>
                                    <i class="fas fa-check-square"></i> Multiple Choice
                                </option>
                                <option value="rating" {{ old('question_type') == 'rating' ? 'selected' : '' }}>
                                    <i class="fas fa-star"></i> Rating (Kurang, Cukup, Baik, Baik Sekali)
                                </option>
                                <option value="scale" {{ old('question_type') == 'scale' ? 'selected' : '' }}>
                                    <i class="fas fa-chart-line"></i> Skala Numerik (1, 2, 3, 4, 5)
                                </option>
                                <option value="date" {{ old('question_type') == 'date' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar"></i> Tanggal
                                </option>
                                <option value="location" {{ old('question_type') == 'location' ? 'selected' : '' }}>
                                    <i class="fas fa-map-marker-alt"></i> Lokasi (Negara, Provinsi & Kota)
                                </option>
                            </select>
                            @error('question_type')
                                <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Text input configuration section -->
                    <div id="text-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'text' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-keyboard text-blue-600 mr-2"></i>
                            Konfigurasi Teks Input
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan teks akan menampilkan input field untuk jawaban bebas
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                            <div>
                                <label for="text_before" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks sebelum input (opsional):
                                </label>
                                <input type="text" 
                                    id="text_before" 
                                    name="text_before_text" 
                                    value="{{ old('text_before_text') }}" 
                                    placeholder="Contoh: Saya bekerja di" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="text_after" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks setelah input (opsional):
                                </label>
                                <input type="text" 
                                    id="text_after" 
                                    name="text_after_text" 
                                    value="{{ old('text_after_text') }}" 
                                    placeholder="Contoh: sebagai posisi" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                    <span id="text-before-preview" class="text-gray-700 font-medium text-sm"></span>
                                    <input type="text" 
                                        class="flex-grow sm:min-w-48 px-3 py-2 border border-gray-300 rounded-md text-sm" 
                                        placeholder="Masukkan jawaban Anda..." 
                                        disabled>
                                    <span id="text-after-preview" class="text-gray-700 font-medium text-sm"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Saya bekerja di [input field] sebagai posisi"
                        </div>
                    </div>

                    <!-- Email input configuration section -->
                    <div id="email-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'email' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-envelope text-purple-600 mr-2"></i>
                            Konfigurasi Input Email
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan email otomatis memvalidasi format email dan memastikan ada domain (@gmail.com, @yahoo.com, dll)
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                            <div>
                                <label for="email_before" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks sebelum input (opsional):
                                </label>
                                <input type="text" 
                                    id="email_before" 
                                    name="email_before_text" 
                                    value="{{ old('email_before_text') }}" 
                                    placeholder="Contoh: Email aktif saya adalah" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="email_after" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks setelah input (opsional):
                                </label>
                                <input type="text" 
                                    id="email_after" 
                                    name="email_after_text" 
                                    value="{{ old('email_after_text') }}" 
                                    placeholder="Contoh: yang bisa dihubungi" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                    <span id="email-before-preview" class="text-gray-700 font-medium text-sm"></span>
                                    <input type="email" 
                                        class="flex-grow sm:min-w-48 px-3 py-2 border border-gray-300 rounded-md text-sm" 
                                        placeholder="contoh@domain.com" 
                                        disabled>
                                    <span id="email-after-preview" class="text-gray-700 font-medium text-sm"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700 mb-4">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Email aktif saya adalah [user@domain.com] yang bisa dihubungi"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Validasi Email:
                            </h5>
                            <ul class="text-sm text-yellow-700 space-y-1 pl-4">
                                <li>• Harus mengandung karakter @</li>
                                <li>• Harus ada domain setelah @ (contoh: gmail.com, yahoo.com)</li>
                                <li>• Format email yang valid secara umum</li>
                                <li>• Tidak boleh ada spasi atau karakter khusus yang tidak diizinkan</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Numeric input configuration section -->
                    <div id="numeric-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'numeric' ? '' : 'hidden' }}">
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                            <div>
                                <label for="numeric_before" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks sebelum input (opsional):
                                </label>
                                <input type="text" 
                                    id="numeric_before" 
                                    name="numeric_before_text" 
                                    value="{{ old('numeric_before_text') }}" 
                                    placeholder="Contoh: Gaji saya sebesar Rp" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="numeric_after" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks setelah input (opsional):
                                </label>
                                <input type="text" 
                                    id="numeric_after" 
                                    name="numeric_after_text" 
                                    value="{{ old('numeric_after_text') }}" 
                                    placeholder="Contoh: per bulan" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2">
                                    <span id="numeric-before-preview" class="text-gray-700 font-medium text-sm"></span>
                                    <input type="text" 
                                        class="flex-grow sm:min-w-48 px-3 py-2 border border-gray-300 rounded-md text-sm numeric-only" 
                                        placeholder="Masukkan angka..." 
                                        pattern="[0-9]*" 
                                        inputmode="numeric" 
                                        disabled>
                                    <span id="numeric-after-preview" class="text-gray-700 font-medium text-sm"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Gaji saya sebesar Rp [input numerik] per bulan"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mt-4">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Validasi Numerik:
                            </h5>
                            <ul class="text-sm text-yellow-700 space-y-1 pl-4">
                                <li>• Hanya dapat mengetik angka (0-9)</li>
                                <li>• Tidak boleh ada huruf atau karakter khusus</li>
                                <li>• Ideal untuk input gaji, umur, tahun, dll</li>
                                <li>• Otomatis menampilkan keyboard numerik pada mobile</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Hidden fields untuk before/after text -->
                    <input type="hidden" id="final_before_text" name="before_text" value="{{ old('before_text') }}">
                    <input type="hidden" id="final_after_text" name="after_text" value="{{ old('after_text') }}">

                    <!-- Rating options section -->
                    <div id="rating-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'rating' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-star text-yellow-600 mr-2"></i>
                            Konfigurasi Rating
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan rating akan menampilkan pilihan: Kurang, Cukup, Baik, Baik Sekali
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Rating:</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200 transition-colors duration-200">
                                    <input type="radio" name="preview_rating" value="1" class="mr-2" disabled>
                                    <span class="text-red-600 text-sm">Kurang</span>
                                </label>
                                <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200 transition-colors duration-200">
                                    <input type="radio" name="preview_rating" value="2" class="mr-2" disabled>
                                    <span class="text-yellow-600 text-sm">Cukup</span>
                                </label>
                                <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200 transition-colors duration-200">
                                    <input type="radio" name="preview_rating" value="3" class="mr-2" disabled>
                                    <span class="text-blue-600 text-sm">Baik</span>
                                </label>
                                <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md cursor-pointer hover:bg-gray-200 transition-colors duration-200">
                                    <input type="radio" name="preview_rating" value="4" class="mr-2" disabled>
                                    <span class="text-green-600 text-sm">Baik Sekali</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700 mb-4">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Bagaimana penilaian Anda terhadap kemampuan menyelesaikan pekerjaan/tugas sesuai dengan target?"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-lock mr-1"></i>
                                Opsi Rating Tetap:
                            </h5>
                            <p class="text-sm text-yellow-700">
                                Opsi rating sudah ditetapkan dan tidak dapat diubah: Kurang (1), Cukup (2), Baik (3), Baik Sekali (4)
                            </p>
                        </div>
                        
                        <!-- Hidden inputs untuk rating options - ALWAYS PRESENT -->
                        <div class="hidden">
                            <input type="hidden" name="rating_options[]" value="Kurang">
                            <input type="hidden" name="rating_options[]" value="Cukup">
                            <input type="hidden" name="rating_options[]" value="Baik">
                            <input type="hidden" name="rating_options[]" value="Baik Sekali">
                        </div>
                    </div>

                    <!-- Scale options section -->
                    <div id="scale-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'scale' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                            Konfigurasi Skala Numerik
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan skala akan menampilkan pilihan angka: 1, 2, 3, 4, 5
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                            <div>
                                <label for="scale_min_label" class="block text-sm font-medium text-gray-700 mb-2">
                                    Label untuk nilai terendah (1):
                                </label>
                                <input type="text" 
                                    id="scale_min_label" 
                                    name="scale_min_label" 
                                    value="{{ old('scale_min_label', 'Sangat Kurang') }}" 
                                    placeholder="Contoh: Sangat Kurang" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="scale_max_label" class="block text-sm font-medium text-gray-700 mb-2">
                                    Label untuk nilai tertinggi (5):
                                </label>
                                <input type="text" 
                                    id="scale_max_label" 
                                    name="scale_max_label" 
                                    value="{{ old('scale_max_label', 'Sangat Baik') }}" 
                                    placeholder="Contoh: Sangat Baik" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Skala:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                                    <span id="scale-min-preview" class="text-sm text-gray-600 font-medium order-1 sm:order-1">Sangat Kurang</span>
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
                                    <span id="scale-max-preview" class="text-sm text-gray-600 font-medium order-3">Sangat Baik</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700 mb-4">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Pada saat lulus, pada tingkat mana kompetensi di bawah ini Anda kuasai? Etika:"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-cog mr-1"></i>
                                Kustomisasi Label:
                            </h5>
                            <p class="text-sm text-yellow-700">
                                Anda dapat mengubah label untuk nilai terendah (1) dan tertinggi (5) sesuai konteks pertanyaan. 
                                Nilai 2, 3, 4 akan otomatis berada di antara kedua label tersebut.
                            </p>
                        </div>
                        
                        <!-- Hidden inputs untuk scale options - ALWAYS PRESENT -->
                        <div class="hidden">
                            <input type="hidden" name="scale_options[]" value="1">
                            <input type="hidden" name="scale_options[]" value="2">
                            <input type="hidden" name="scale_options[]" value="3">
                            <input type="hidden" name="scale_options[]" value="4">
                            <input type="hidden" name="scale_options[]" value="5">
                        </div>
                    </div>

                    <!-- Options section untuk option/multiple types -->
                    <div id="options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'option' || old('question_type') == 'multiple' ? '' : 'hidden' }}">
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
                                        <input type="hidden" name="option_indexes[]" value="{{ $index }}">
                                        
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
                                            <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200 {{ in_array($index, old('other_options', [])) ? 'bg-orange-50 border-orange-200' : '' }}">
                                                <input type="checkbox" 
                                                    id="other_checkbox_{{ $index }}" 
                                                    name="other_options[]" 
                                                    value="{{ $index }}" 
                                                    class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                                    {{ in_array($index, old('other_options', [])) ? 'checked' : '' }} 
                                                    onchange="toggleOtherConfig(this, {{ $index }})">
                                                <label for="other_checkbox_{{ $index }}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                                    <i class="fas fa-edit text-orange-600 mr-1"></i>
                                                    Lainnya
                                                </label>
                                            </div>
                                            
                                            <!-- Remove/Minimal indicator -->
                                            @if($loop->first)
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Minimal 1
                                                </span>
                                            @else
                                                <button type="button" 
                                                        class="remove-option flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-md border border-red-300 hover:border-red-500 transition-all duration-200 flex-shrink-0"
                                                        title="Hapus pilihan ini">
                                                    <i class="fas fa-times text-sm"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Konfigurasi "Lainnya" -->
                                    <div class="other-text-config {{ in_array($index, old('other_options', [])) ? '' : 'hidden' }} bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_{{ $index }}">
                                        <div class="flex items-center mb-3">
                                            <i class="fas fa-cog text-orange-600 mr-2"></i>
                                            <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                                <input type="text" 
                                                    name="other_before_text[]" 
                                                    value="{{ old('other_before_text.' . $index) }}" 
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                    placeholder="Contoh: Sebutkan"
                                                    oninput="updateOtherPreview({{ $index }})">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                                <input type="text" 
                                                    name="other_after_text[]" 
                                                    value="{{ old('other_after_text.' . $index) }}" 
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                    placeholder="Contoh: lainnya"
                                                    oninput="updateOtherPreview({{ $index }})">
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white p-3 rounded-md border border-gray-200">
                                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                                <i class="fas fa-eye text-gray-500 mr-1"></i>
                                                Preview untuk Pengguna:
                                            </label>
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2" id="other_preview_{{ $index }}">
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
                            <!-- Default first option -->
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors duration-200">
                                <!-- Main option row -->
                                <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                                    <input type="hidden" name="option_indexes[]" value="0">
                                    
                                    <!-- Option input - full width on mobile -->
                                    <input type="text" 
                                        name="options[]" 
                                        class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                        placeholder="Tuliskan pilihan..." 
                                        required>
                                    
                                    <!-- Controls row -->
                                    <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                                        <!-- Toggle "Lainnya" -->
                                        <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                                            <input type="checkbox" 
                                                id="other_checkbox_0" 
                                                name="other_options[]" 
                                                value="0" 
                                                class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                                onchange="toggleOtherConfig(this, 0)">
                                            <label for="other_checkbox_0" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                                <i class="fas fa-edit text-orange-600 mr-1"></i>
                                                Lainnya
                                            </label>
                                        </div>
                                        
                                        <!-- Minimal indicator -->
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Minimal 1
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Konfigurasi "Lainnya" -->
                                <div class="other-text-config hidden bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_0">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-cog text-orange-600 mr-2"></i>
                                        <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                                            <input type="text" 
                                                name="other_before_text[]" 
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                placeholder="Contoh: Sebutkan"
                                                oninput="updateOtherPreview(0)">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                                            <input type="text" 
                                                name="other_after_text[]" 
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                                placeholder="Contoh: lainnya"
                                                oninput="updateOtherPreview(0)">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white p-3 rounded-md border border-gray-200">
                                        <label class="block text-xs font-medium text-gray-600 mb-2">
                                            <i class="fas fa-eye text-gray-500 mr-1"></i>
                                            Preview untuk Pengguna:
                                        </label>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2" id="other_preview_0">
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
                            @endif
                        </div>
                        @error('options')
                            <p class="text-red-500 text-xs sm:text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <!-- Date options section -->
                    <div id="date-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'date' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-calendar text-indigo-600 mr-2"></i>
                            Konfigurasi Input Tanggal
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan tanggal akan menampilkan date picker untuk memilih tanggal
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input Tanggal:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <input type="date" 
                                    class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm" 
                                    disabled>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700 mb-4">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Kapan Anda lulus dari perguruan tinggi?"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-calendar-check mr-1"></i>
                                Fitur Input Tanggal:
                            </h5>
                            <ul class="text-sm text-yellow-700 space-y-1 pl-4">
                                <li>• Otomatis menampilkan calendar picker</li>
                                <li>• Format tanggal DD/MM/YYYY</li>
                                <li>• Validasi tanggal otomatis</li>
                                <li>• User-friendly pada mobile dan desktop</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Location options section -->
                    <div id="location-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'location' ? '' : 'hidden' }}">
                        <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                            Konfigurasi Input Lokasi
                        </h4>
                        
                        <div class="bg-blue-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan lokasi akan menampilkan dropdown Negara, Provinsi dan Kota/Kabupaten
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Input Lokasi:</label>
                            <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Negara:</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" disabled>
                                            <option>-- Pilih Negara --</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Provinsi:</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" disabled>
                                            <option>-- Pilih Provinsi --</option>
                                            <option>DKI Jakarta</option>
                                            <option>Jawa Barat</option>
                                            <option>Jawa Tengah</option>
                                            <option>Jawa Timur</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Kota/Kabupaten:</label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" disabled>
                                            <option>-- Pilih Kota/Kabupaten --</option>
                                            <option>Jakarta Pusat</option>
                                            <option>Jakarta Selatan</option>
                                            <option>Jakarta Utara</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-3 rounded-lg text-sm text-green-700 mb-4">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Contoh penggunaan:</strong> "Di mana lokasi tempat kerja Anda saat ini?"
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                            <h5 class="font-medium text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-map mr-1"></i>
                                Fitur Input Lokasi:
                            </h5>
                            <ul class="text-sm text-yellow-700 space-y-1 pl-4">
                                <li>• Data lengkap 34 Provinsi di Indonesia</li>
                                <li>• Kota/Kabupaten otomatis ter-filter berdasarkan provinsi</li>
                                <li>• Interface yang user-friendly</li>
                                <li>• Responsive pada semua device</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Dependency section -->
                    <div class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg">
                        <div class="flex items-start mb-3">
                            <input type="checkbox" 
                                id="has_dependency" 
                                name="has_dependency" 
                                value="1" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5 flex-shrink-0" 
                                {{ old('has_dependency') ? 'checked' : '' }}>
                            <div class="ml-3">
                                <label for="has_dependency" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    <i class="fas fa-link text-orange-600 mr-1"></i>
                                    Pertanyaan ini muncul berdasarkan jawaban pertanyaan lain
                                </label>
                                <p class="text-xs text-gray-600 mt-1">
                                    Aktifkan jika pertanyaan ini hanya muncul ketika jawaban tertentu dipilih
                                </p>
                            </div>
                        </div>
                        
                        <div id="conditional-options" 
                            class="space-y-4 {{ old('has_dependency') ? '' : 'hidden' }}">
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
                                                    {{ old('depends_on') == $q->id_question ? 'selected' : '' }}>
                                                {{ Str::limit($q->question, 50) }} ({{ ucfirst($q->type) }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('depends_on')
                                    <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Pertanyaan dengan tipe 'option', 'multiple', 'rating', atau 'scale' dapat dijadikan dependensi</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih jawaban yang memicu pertanyaan ini:
                                </label>
                                <select name="depends_value" 
                                        id="depends_value_select" 
                                        class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('depends_value') ? 'border-red-500' : 'border-gray-300' }}">
                                    <option value="">-- Pilih Jawaban --</option>
                                </select>
                                @error('depends_value')
                                    <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pertanyaan ini hanya akan muncul jika kondisi di atas terpenuhi.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Hidden fields untuk dependency -->
                        <input type="hidden" id="hidden_depends_on" name="hidden_depends_on" value="{{ old('depends_on') }}">
                        <input type="hidden" id="hidden_depends_value" name="hidden_depends_value" value="{{ old('depends_value') }}">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 sm:pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                        class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base order-2 sm:order-1">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit" 
                                class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base order-1 sm:order-2">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Pertanyaan
                        </button>
                    </div>

                </form>
            </div>
    </div>
</x-layout-admin>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// ===== ENHANCED PREVIEW FUNCTIONS =====
window.updateOtherPreview = function(index) {
    const beforeInput = document.querySelector(`input[name="other_before_text[]"]:nth-of-type(${parseInt(index) + 1})`);
    const afterInput = document.querySelector(`input[name="other_after_text[]"]:nth-of-type(${parseInt(index) + 1})`);
    const previewContainer = document.getElementById(`other_preview_${index}`);
    
    if (beforeInput && afterInput && previewContainer) {
        const beforeSpan = previewContainer.querySelector('.other-before-text');
        const afterSpan = previewContainer.querySelector('.other-after-text');
        const fullPreview = previewContainer.parentElement.querySelector('.other-preview');
        
        const beforeText = beforeInput.value || 'Sebutkan';
        const afterText = afterInput.value || 'lainnya';
        
        if (beforeSpan) beforeSpan.textContent = beforeText;
        if (afterSpan) afterSpan.textContent = afterText;
        if (fullPreview) fullPreview.textContent = `${beforeText} [Input pengguna] ${afterText}`;
    }
};

// Enhanced toggle function untuk "Lainnya" configuration
window.toggleOtherConfig = function(checkbox, index) {
    const configDiv = document.getElementById(`other_config_${index}`);
    const checkboxContainer = checkbox.closest('.bg-gray-50');
    
    if (configDiv) {
        // Toggle visibility dengan smooth animation
        if (checkbox.checked) {
            configDiv.classList.remove('hidden');
            configDiv.style.opacity = '0';
            configDiv.style.transform = 'translateY(-10px)';
            
            // Smooth show animation
            setTimeout(() => {
                configDiv.style.transition = 'all 0.3s ease-out';
                configDiv.style.opacity = '1';
                configDiv.style.transform = 'translateY(0)';
            }, 10);
            
            // Update checkbox container styling
            if (checkboxContainer) {
                checkboxContainer.classList.remove('bg-gray-50', 'border-gray-200');
                checkboxContainer.classList.add('bg-orange-50', 'border-orange-200');
            }
        } else {
            // Smooth hide animation
            configDiv.style.transition = 'all 0.3s ease-out';
            configDiv.style.opacity = '0';
            configDiv.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                configDiv.classList.add('hidden');
                configDiv.style.opacity = '';
                configDiv.style.transform = '';
                configDiv.style.transition = '';
            }, 300);
            
            // Reset checkbox container styling
            if (checkboxContainer) {
                checkboxContainer.classList.remove('bg-orange-50', 'border-orange-200');
                checkboxContainer.classList.add('bg-gray-50', 'border-gray-200');
            }
            
            // Clear input values
            const beforeInput = configDiv.querySelector('input[name="other_before_text[]"]');
            const afterInput = configDiv.querySelector('input[name="other_after_text[]"]');
            if (beforeInput) beforeInput.value = '';
            if (afterInput) afterInput.value = '';
            
            // Update preview
            updateOtherPreview(index);
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Setup basic toggle functionality
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });
    
    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });
    
    // Get form and question type elements
    const form = document.querySelector('form');
    const questionType = document.getElementById('question_type');
    
    // SIMPLIFIED Form submission handler - minimal validation
    if (form) {
        form.addEventListener('submit', function(e) {
            
            // Only validate if question type is selected
            if (!questionType.value) {
                showAlert('Pilih tipe pertanyaan terlebih dahulu!', 'error');
                return false;
            }
            
            // Validate options for option/multiple types
            if (questionType.value === 'option' || questionType.value === 'multiple') {
                const optionInputs = document.querySelectorAll('#options-container input[name="options[]"]');
                let hasValidOption = false;
                
                optionInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasValidOption = true;
                    }
                });
                
                if (!hasValidOption) {
                    showAlert('Minimal harus ada satu pilihan jawaban yang diisi!', 'error');
                    return false;
                }
            }
            // Validate dependency if enabled
            const hasDepCheckbox = document.getElementById('has_dependency');
            if (hasDepCheckbox && hasDepCheckbox.checked) {
                const dependsOnSelect = document.getElementById('depends_on_select');
                const dependsValueSelect = document.getElementById('depends_value_select');
                
                if (!dependsOnSelect.value) {
                    showAlert('Pilih pertanyaan yang menjadi dependensi!', 'error');
                    return false;
                }
                
                if (!dependsValueSelect.value) {
                    showAlert('Pilih jawaban yang memicu pertanyaan ini!', 'error');
                    return false;
                }
            }
    
            // REMOVED dependency validation - let backend handle it
            
            return true; // Allow form to submit
        });
    }
    
    // Toggle sections based on question type
    if (questionType) {
        questionType.addEventListener('change', function() {
            // Get all sections - use safe access
            const sections = {
                options: document.getElementById('options-section'),
                rating: document.getElementById('rating-options-section'),
                scale: document.getElementById('scale-options-section'),
                text: document.getElementById('text-options-section'),
                numeric: document.getElementById('numeric-options-section'),
                email: document.getElementById('email-options-section'),
                location: document.getElementById('location-options-section'),
            };
            
            // Hide all sections first
            Object.values(sections).forEach(section => {
                if (section) section.classList.add('hidden');
            });
            
            // Show appropriate section based on type
            if (this.value === 'option' || this.value === 'multiple') {
                if (sections.options) {
                    sections.options.classList.remove('hidden');
                    // Set required attribute for visible option inputs only
                    const visibleInputs = sections.options.querySelectorAll('input[name="options[]"]:not([type="hidden"])');
                    visibleInputs.forEach(input => input.setAttribute('required', 'required'));
                }
            } else if (this.value === 'rating') {
                if (sections.rating) {
                    sections.rating.classList.remove('hidden');
                }
            } else if (this.value === 'scale') {
                if (sections.scale) {
                    sections.scale.classList.remove('hidden');
                }
            } else if (this.value === 'text') {
                if (sections.text) {
                    sections.text.classList.remove('hidden');
                }
            } else if (this.value === 'numeric') {
                if (sections.numeric) {
                    sections.numeric.classList.remove('hidden');
                }
            } else if (this.value === 'email') {
                if (sections.email) {
                    sections.email.classList.remove('hidden');
                }
            } else if (this.value === 'location') {
                if (sections.location) {
                    sections.location.classList.remove('hidden');
                }
            }

            // Remove required from option inputs when not option/multiple type
            if (this.value !== 'option' && this.value !== 'multiple' && sections.options) {
                const visibleInputs = sections.options.querySelectorAll('input[name="options[]"]:not([type="hidden"])');
                visibleInputs.forEach(input => input.removeAttribute('required'));
            }
            
        });
    }
    // ===== ENHANCED ALERT FUNCTION =====
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        if (type === 'error') {
            alertDiv.className += ' bg-red-100 border border-red-400 text-red-700';
        } else if (type === 'success') {
            alertDiv.className += ' bg-green-100 border border-green-400 text-green-700';
        } else {
            alertDiv.className += ' bg-blue-100 border border-blue-400 text-blue-700';
        }
        
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                <span class="text-sm">${message}</span>
                <button type="button" class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Animate in
        setTimeout(() => {
            alertDiv.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.classList.add('translate-x-full');
                setTimeout(() => alertDiv.remove(), 300);
            }
        }, 5000);
    }
    // Live preview for scale labels
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

    // Live preview for text/numeric input before/after text (menggunakan ID yang sama)
    const textBefore = document.getElementById('text_before');
    const textAfter = document.getElementById('text_after');
    const textBeforePreview = document.getElementById('text-before-preview');
    const textAfterPreview = document.getElementById('text-after-preview');
    
    function updateTextPreview() {
        if (textBefore && textAfter && textBeforePreview && textAfterPreview) {
            textBeforePreview.textContent = textBefore.value || '';
            textAfterPreview.textContent = textAfter.value || '';
        }
    }
    
    if (textBefore && textAfter) {
        textBefore.addEventListener('input', updateTextPreview);
        textAfter.addEventListener('input', updateTextPreview);
        updateTextPreview(); // Initial update
    }

    // Live preview for numeric input before/after text
    const numericBefore = document.getElementById('numeric_before');
    const numericAfter = document.getElementById('numeric_after');
    const numericBeforePreview = document.getElementById('numeric-before-preview');
    const numericAfterPreview = document.getElementById('numeric-after-preview');
    
    function updateNumericPreview() {
        if (numericBefore && numericAfter && numericBeforePreview && numericAfterPreview) {
            numericBeforePreview.textContent = numericBefore.value || '';
            numericAfterPreview.textContent = numericAfter.value || '';
        }
    }
    
    if (numericBefore && numericAfter) {
        numericBefore.addEventListener('input', updateNumericPreview);
        numericAfter.addEventListener('input', updateNumericPreview);
        updateNumericPreview(); // Initial update
    }
    
    if (scaleMinLabel && scaleMaxLabel) {
        scaleMinLabel.addEventListener('input', updateScalePreview);
        scaleMaxLabel.addEventListener('input', updateScalePreview);
        updateScalePreview();
    }
    // Live preview for email input before/after text
    const emailBefore = document.getElementById('email_before');
    const emailAfter = document.getElementById('email_after');
    const emailBeforePreview = document.getElementById('email-before-preview');
    const emailAfterPreview = document.getElementById('email-after-preview');
    
    function updateEmailPreview() {
        if (emailBefore && emailAfter && emailBeforePreview && emailAfterPreview) {
            emailBeforePreview.textContent = emailBefore.value || '';
            emailAfterPreview.textContent = emailAfter.value || '';
        }
    }
    
    if (emailBefore && emailAfter) {
        emailBefore.addEventListener('input', updateEmailPreview);
        emailAfter.addEventListener('input', updateEmailPreview);
        updateEmailPreview(); // Initial update
    }
    
    // Add option button for option/multiple types
    const addOptionBtn = document.getElementById('add-option');
    const optionsContainer = document.getElementById('options-container');
    
    if (addOptionBtn && optionsContainer) {
        let optionIndex = document.querySelectorAll('.option-item').length;
        
        addOptionBtn.addEventListener('click', function() {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item flex items-center mb-2 flex-wrap';
            optionDiv.setAttribute('data-index', optionIndex);
            optionDiv.innerHTML = `
            <!-- Main option row -->
            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:space-x-3">
                <input type="hidden" name="option_indexes[]" value="${optionIndex}">
                
                <!-- Option input - full width on mobile -->
                <input type="text" 
                    name="options[]" 
                    class="flex-grow px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Tuliskan pilihan..." 
                    required>
                
                <!-- Controls row -->
                <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3">
                    <!-- Toggle "Lainnya" -->
                    <div class="flex items-center bg-gray-50 px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                        <input type="checkbox" 
                            id="other_checkbox_${optionIndex}" 
                            name="other_options[]" 
                            value="${optionIndex}" 
                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                            onchange="toggleOtherConfig(this, ${optionIndex})">
                        <label for="other_checkbox_${optionIndex}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
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
            <div class="other-text-config hidden bg-gradient-to-r from-orange-50 to-yellow-50 p-3 sm:p-4 rounded-lg border border-orange-200 mt-3" id="other_config_${optionIndex}">
                <div class="flex items-center mb-3">
                    <i class="fas fa-cog text-orange-600 mr-2"></i>
                    <h4 class="text-sm font-medium text-orange-800">Konfigurasi Input "Lainnya"</h4>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Teks Sebelum Input:</label>
                        <input type="text" 
                            name="other_before_text[]" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                            placeholder="Contoh: Sebutkan"
                            oninput="updateOtherPreview(${optionIndex})">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Teks Setelah Input:</label>
                        <input type="text" 
                            name="other_after_text[]" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                            placeholder="Contoh: lainnya"
                            oninput="updateOtherPreview(${optionIndex})">
                    </div>
                </div>
                
                <div class="bg-white p-3 rounded-md border border-gray-200">
                    <label class="block text-xs font-medium text-gray-600 mb-2">
                        <i class="fas fa-eye text-gray-500 mr-1"></i>
                        Preview untuk Pengguna:
                    </label>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2" id="other_preview_${optionIndex}">
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
        `;
            
            optionsContainer.appendChild(optionDiv);
            // Animate in
            setTimeout(() => {
                newOptionDiv.style.transition = 'all 0.3s ease-out';
                newOptionDiv.style.opacity = '1';
                newOptionDiv.style.transform = 'translateY(0)';
            }, 100);
            
            // Focus on new input
            const newInput = newOptionDiv.querySelector('input[name="options[]"]');
            if (newInput) {
                setTimeout(() => newInput.focus(), 300);
            }
            optionIndex++;
        });
        
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
                const optionItem = e.target.closest('.option-item');
                if (optionItem && document.querySelectorAll('.option-item').length > 0) {
                    optionItem.remove();
                    
                    // Re-index remaining options
                    document.querySelectorAll('.option-item').forEach((item, newIndex) => {
                        item.setAttribute('data-index', newIndex);
                        // Update all inputs within this option
                        const inputs = item.querySelectorAll('input, label');
                        inputs.forEach(input => {
                            if (input.name === 'option_indexes[]') {
                                input.value = newIndex;
                            } else if (input.name === 'other_options[]') {
                                input.value = newIndex;
                                input.setAttribute('onchange', `toggleOtherConfig(this, ${newIndex})`);
                                input.id = `other_checkbox_${newIndex}`;
                            } else if (input.name && input.name.startsWith('other_before_text[')) {
                                input.name = `other_before_text[${newIndex}]`;
                                input.setAttribute('oninput', `updateOtherPreview(${newIndex})`);
                            } else if (input.name && input.name.startsWith('other_after_text[')) {
                                input.name = `other_after_text[${newIndex}]`;
                                input.setAttribute('oninput', `updateOtherPreview(${newIndex})`);
                            } else if (input.tagName === 'LABEL' && input.getAttribute('for')) {
                                input.setAttribute('for', `other_checkbox_${newIndex}`);
                            }
                        });
                        
                        // Update config div id
                        const configDiv = item.querySelector('.other-text-config');
                        if (configDiv) {
                            configDiv.id = `other_config_${newIndex}`;
                        }
                        
                        // Update preview div id
                        const previewDiv = item.querySelector('[id^="other_preview_"]');
                        if (previewDiv) {
                            previewDiv.id = `other_preview_${newIndex}`;
                        }
                    });
                }
            }
        });
    }
    
    document.querySelectorAll('[id^="other_config_"]').forEach(config => {
        const index = config.id.replace('other_config_', '');
        updateOtherPreview(index);
    });
    
    // Toggle conditional options
    const hasDepCheckbox = document.getElementById('has_dependency');
    const conditionalOptions = document.getElementById('conditional-options');
    
    if (hasDepCheckbox && conditionalOptions) {
        hasDepCheckbox.addEventListener('change', function() {
            if (this.checked) {
                conditionalOptions.classList.remove('hidden');
            } else {
                conditionalOptions.classList.add('hidden');
                // Reset values when hiding
                const dependsOnSelect = document.getElementById('depends_on');
                const dependsValueSelect = document.getElementById('depends_value');
                if (dependsOnSelect) dependsOnSelect.value = '';
                if (dependsValueSelect) dependsValueSelect.value = '';
            }
        });
    }
    
    // SIMPLIFIED Dependency handling - basic functionality only
    const questionOptionsMap = {
        @if(isset($availableQuestions) && count($availableQuestions) > 0)
            @foreach($availableQuestions as $q)
                @if(in_array($q->type, ['option', 'multiple', 'rating', 'scale']) && $q->options->count() > 0)
                    "{{ $q->id_question }}": [
                        @foreach($q->options as $option)
                            {
                                id: "{{ $option->id_questions_options }}",
                                text: @json($option->option)
                            },
                        @endforeach
                    ],
                @endif
            @endforeach
        @endif
    };

    const dependsOnSelect = document.getElementById('depends_on');
    const dependsValueSelect = document.getElementById('depends_value');
    const selectedDependsOn = "{{ old('depends_on') }}";
    const selectedDependsValue = "{{ old('depends_value') }}";

    // ===== ENHANCED DEPENDENCY FUNCTIONS =====
    function populateDependsValueOptions(questionId, selectedValue = null) {
        const dependsValueSelect = document.getElementById('depends_value_select');
        if (!dependsValueSelect) return;
        
        // Clear existing options
        dependsValueSelect.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
        
        if (questionOptionsMap[questionId] && questionOptionsMap[questionId].length > 0) {
            questionOptionsMap[questionId].forEach(opt => {
                const optionEl = document.createElement('option');
                optionEl.value = opt.id;
                optionEl.textContent = opt.text;
                if (selectedValue && selectedValue == opt.id) {
                    optionEl.selected = true;
                }
                dependsValueSelect.appendChild(optionEl);
            });
            
        } else {
            const noOptionEl = document.createElement('option');
            noOptionEl.value = '';
            noOptionEl.textContent = '-- Tidak ada opsi tersedia --';
            noOptionEl.disabled = true;
            dependsValueSelect.appendChild(noOptionEl);
        }
    }
    
    // On page load, if there's old input, populate options
    if (selectedDependsOn && dependsOnSelect && dependsValueSelect) {
        populateDependsValueOptions(selectedDependsOn, selectedDependsValue);
    }

    // On dependency question change
    if (dependsOnSelect) {
        dependsOnSelect.addEventListener('change', function() {
            populateDependsValueOptions(this.value, null);
        });
    }
    
    // Add support for location type
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type');
        const locationSection = document.getElementById('location-options-section');
        
        if (questionType) {
            const originalChangeHandler = questionType.onchange;
            
            questionType.onchange = function() {
                // Call the original handler if exists
                if (typeof originalChangeHandler === 'function') {
                    originalChangeHandler.call(this);
                }
                
                // Additional handler for location type
                if (locationSection) {
                    locationSection.classList.toggle('hidden', this.value !== 'location');
                }
            };
        }
    });
    
    // Initial setup based on current question type
    if (questionType) {
        questionType.dispatchEvent(new Event('change'));
    }
});

// Global function for other option configuration
window.toggleOtherConfig = function(checkbox) {
    const index = checkbox.value;
    const configDiv = document.getElementById(`other_config_${index}`);
    if (configDiv) {
        configDiv.classList.toggle('hidden', !checkbox.checked);
        
        if (!checkbox.checked) {
            const beforeInput = configDiv.querySelector(`input[name="other_before_text[${index}]"]`);
            const afterInput = configDiv.querySelector(`input[name="other_after_text[${index}]"]`);
            if (beforeInput) beforeInput.value = '';
            if (afterInput) afterInput.value = '';
            updateOtherPreview(index);
        }
    }
};

const toggleConditional = document.getElementById('has_dependency');
const conditionalOptions = document.getElementById('conditional-options');
const dependsOnSelect = document.getElementById('depends_on_select');
const dependsValueSelect = document.getElementById('depends_value_select');

if (toggleConditional && conditionalOptions) {
    toggleConditional.addEventListener('change', function() {
        conditionalOptions.classList.toggle('hidden', !this.checked);
        
        
        // Reset dependency selects when unchecked
        if (!this.checked) {
            if (dependsOnSelect) dependsOnSelect.value = '';
            if (dependsValueSelect) {
                dependsValueSelect.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
            }
            
            // Clear hidden fields
            const hiddenDependsOn = document.getElementById('hidden_depends_on');
            const hiddenDependsValue = document.getElementById('hidden_depends_value');
            if (hiddenDependsOn) hiddenDependsOn.value = '';
            if (hiddenDependsValue) hiddenDependsValue.value = '';
        }
    });
}

const questionOptionsMap = {};
@foreach($availableQuestions as $q)
    @if(in_array($q->type, ['option', 'multiple', 'rating', 'scale']) && $q->options->count() > 0)
        questionOptionsMap[{{ $q->id_question }}] = [
            @foreach($q->options as $option)
                {
                    id: {{ $option->id_questions_options }},
                    text: "{{ addslashes($option->option) }}"
                },
            @endforeach
        ];
    @endif
@endforeach

function populateDependsValueOptions(questionId, selectedValue = null) {
    if (!dependsValueSelect) return;
    
    dependsValueSelect.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
    
    if (questionOptionsMap[questionId] && questionOptionsMap[questionId].length > 0) {
        questionOptionsMap[questionId].forEach(opt => {
            const optionEl = document.createElement('option');
            optionEl.value = opt.id;
            optionEl.textContent = opt.text;
            if (selectedValue && selectedValue == opt.id) {
                optionEl.selected = true;
            }
            dependsValueSelect.appendChild(optionEl);
        });
        
    } else {
        // If no options available, show message
        const noOptionEl = document.createElement('option');
        noOptionEl.value = '';
        noOptionEl.textContent = '-- Tidak ada opsi tersedia --';
        noOptionEl.disabled = true;
        dependsValueSelect.appendChild(noOptionEl);
        
    }
}

if (dependsOnSelect && dependsValueSelect) {
    dependsOnSelect.addEventListener('change', function() {
        populateDependsValueOptions(this.value, null);
        
        // Update hidden field
        const hiddenDependsOn = document.getElementById('hidden_depends_on');
        if (hiddenDependsOn) {
            hiddenDependsOn.value = this.value;
        }
    });
}

if (dependsValueSelect) {
    dependsValueSelect.addEventListener('change', function() {
        
        // Update hidden field
        const hiddenDependsValue = document.getElementById('hidden_depends_value');
        if (hiddenDependsValue) {
            hiddenDependsValue.value = this.value;
        }
    });
}

const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function(e) {
        // Handle dependency fields before submission
        const toggleConditional = document.getElementById('has_dependency');
        const dependsOnSelect = document.getElementById('depends_on_select');
        const dependsValueSelect = document.getElementById('depends_value_select');
        const hiddenDependsOn = document.getElementById('hidden_depends_on');
        const hiddenDependsValue = document.getElementById('hidden_depends_value');
        
        if (toggleConditional && toggleConditional.checked) {
            // Dependency is enabled, validate the selections
            if (!dependsOnSelect.value) {
                e.preventDefault();
                alert('Pilih pertanyaan yang menjadi dependensi!');
                return false;
            }
            
            if (!dependsValueSelect.value) {
                e.preventDefault();
                alert('Pilih jawaban yang memicu pertanyaan ini!');
                return false;
            }
            
            // Copy values to hidden fields for submission
            hiddenDependsOn.value = dependsOnSelect.value;
            hiddenDependsValue.value = dependsValueSelect.value;
            
            
        } else {
            // Dependency is disabled, clear the hidden fields
            hiddenDependsOn.value = '';
            hiddenDependsValue.value = '';
            
        }
        
        return true;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const questionTypeSelect = document.getElementById('question_type');
    
    // Function to update hidden fields with correct before/after text
    function updateHiddenFields(questionType) {
        const finalBeforeText = document.getElementById('final_before_text');
        const finalAfterText = document.getElementById('final_after_text');
        
        let beforeValue = '';
        let afterValue = '';
        
        switch(questionType) {
            case 'text':
                beforeValue = document.getElementById('text_before').value || '';
                afterValue = document.getElementById('text_after').value || '';
                break;
            case 'email':
                beforeValue = document.getElementById('email_before').value || '';
                afterValue = document.getElementById('email_after').value || '';
                break;
            case 'numeric':
                beforeValue = document.getElementById('numeric_before').value || '';
                afterValue = document.getElementById('numeric_after').value || '';
                break;
        }
        
        finalBeforeText.value = beforeValue;
        finalAfterText.value = afterValue;
        
        
    }

    // Handle question type change
    if (questionTypeSelect) {
        questionTypeSelect.addEventListener('change', function() {
            // Toggle sections based on question type
            // Get all sections - use safe access
            const sections = {
                options: document.getElementById('options-section'),
                rating: document.getElementById('rating-options-section'),
                scale: document.getElementById('scale-options-section'),
                text: document.getElementById('text-options-section'),
                numeric: document.getElementById('numeric-options-section'),
                email: document.getElementById('email-options-section'),
                location: document.getElementById('location-options-section'),
            };
            
            // Hide all sections first
            Object.values(sections).forEach(section => {
                if (section) section.classList.add('hidden');
            });
            
            // Show appropriate section based on type
            if (this.value === 'option' || this.value === 'multiple') {
                if (sections.options) {
                    sections.options.classList.remove('hidden');
                    // Set required attribute for visible option inputs only
                    const visibleInputs = sections.options.querySelectorAll('input[name="options[]"]:not([type="hidden"])');
                    visibleInputs.forEach(input => input.setAttribute('required', 'required'));
                }
            } else if (this.value === 'rating') {
                if (sections.rating) {
                    sections.rating.classList.remove('hidden');
                }
            } else if (this.value === 'scale') {
                if (sections.scale) {
                    sections.scale.classList.remove('hidden');
                }
            } else if (this.value === 'text') {
                if (sections.text) {
                    sections.text.classList.remove('hidden');
                }
            } else if (this.value === 'numeric') {
                if (sections.numeric) {
                    sections.numeric.classList.remove('hidden');
                }
            } else if (this.value === 'email') {
                if (sections.email) {
                    sections.email.classList.remove('hidden');
                }
            } else if (this.value === 'location') {
                if (sections.location) {
                    sections.location.classList.remove('hidden');
                }
            }

            // Remove required from option inputs when not option/multiple type
            if (this.value !== 'option' && this.value !== 'multiple' && sections.options) {
                const visibleInputs = sections.options.querySelectorAll('input[name="options[]"]:not([type="hidden"])');
                visibleInputs.forEach(input => input.removeAttribute('required'));
            }
            
            // Update hidden fields when question type changes
            updateHiddenFields(this.value);
            
        });
    }
    
    // Handle input changes for before/after text fields
    ['text_before', 'text_after', 'email_before', 'email_after', 'numeric_before', 'numeric_after'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function() {
                updateHiddenFields(questionTypeSelect.value);
                updatePreview();
            });
        }
    });

    // Update preview text
    function updatePreview() {
        // Update text preview
        const textBefore = document.getElementById('text_before');
        const textAfter = document.getElementById('text_after');
        const textBeforePreview = document.getElementById('text-before-preview');
        const textAfterPreview = document.getElementById('text-after-preview');
        
        if (textBefore && textAfter && textBeforePreview && textAfterPreview) {
            textBeforePreview.textContent = textBefore.value || '';
            textAfterPreview.textContent = textAfter.value || '';
        }
        
        // Update email preview
        const emailBefore = document.getElementById('email_before');
        const emailAfter = document.getElementById('email_after');
        const emailBeforePreview = document.getElementById('email-before-preview');
        const emailAfterPreview = document.getElementById('email-after-preview');
        
        if (emailBefore && emailAfter && emailBeforePreview && emailAfterPreview) {
            emailBeforePreview.textContent = emailBefore.value || '';
            emailAfterPreview.textContent = emailAfter.value || '';
        }
        
        // Update numeric preview
        const numericBefore = document.getElementById('numeric_before');
        const numericAfter = document.getElementById('numeric_after');
        const numericBeforePreview = document.getElementById('numeric-before-preview');
        const numericAfterPreview = document.getElementById('numeric-after-preview');
        
        if (numericBefore && numericAfter && numericBeforePreview && numericAfterPreview) {
            numericBeforePreview.textContent = numericBefore.value || '';
            numericAfterPreview.textContent = numericAfter.value || '';
        }
        
        // Update scale preview
        const scaleMinLabel = document.getElementById('scale_min_label');
        const scaleMaxLabel = document.getElementById('scale_max_label');
        const scaleMinPreview = document.getElementById('scale-min-preview');
        const scaleMaxPreview = document.getElementById('scale-max-preview');
        
        if (scaleMinLabel && scaleMaxLabel && scaleMinPreview && scaleMaxPreview) {
            scaleMinPreview.textContent = scaleMinLabel.value || 'Sangat Kurang';
            scaleMaxPreview.textContent = scaleMaxLabel.value || 'Sangat Baik';
        }
    }

    // Initialize on page load
    if (questionTypeSelect.value) {
        updateHiddenFields(questionTypeSelect.value);
        updatePreview();
    }

    // Handle form submission to ensure hidden fields are updated
    document.querySelector('form').addEventListener('submit', function() {
        updateHiddenFields(questionTypeSelect.value);
       
    });
});
</script>
<script src="{{ asset('js/script.js') }}"></script>
@endsection
