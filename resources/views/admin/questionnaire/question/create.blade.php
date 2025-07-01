@extends('layouts.app')

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

            <form id="questionForm" method="POST" action="{{ route('admin.questionnaire.question.store', [$periode->id_periode, $category->id_category]) }}" class="px-4 sm:px-6 py-4 sm:py-6">
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
                              class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('question') border-red-500 @enderror"
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
                               class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('order') border-red-500 @enderror" 
                               required>
                        @error('order')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Type selection -->
                    <div>
                        <label for="question_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list text-gray-500 mr-1"></i>
                            Tipe Pertanyaan
                        </label>
                        <select name="question_type" id="question_type" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('question_type') border-red-500 @enderror" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="text" {{ old('question_type') == 'text' ? 'selected' : '' }}>Teks</option>
                            <option value="option" {{ old('question_type') == 'option' ? 'selected' : '' }}>Pilihan Ganda</option>
                            <option value="numeric" {{ old('question_type') == 'numeric' ? 'selected' : '' }}>Numerik (Hanya Angka)</option>
                            <option value="email" {{ old('question_type') == 'email' ? 'selected' : '' }}>Email (Validasi Domain)</option>
                            <option value="multiple" {{ old('question_type') == 'multiple' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="rating" {{ old('question_type') == 'rating' ? 'selected' : '' }}>Rating (Kurang, Cukup, Baik, Baik Sekali)</option>
                            <option value="scale" {{ old('question_type') == 'scale' ? 'selected' : '' }}>Skala Numerik (1, 2, 3, 4, 5)</option>
                            <option value="date" {{ old('question_type') == 'date' ? 'selected' : '' }}>Tanggal</option>
                            <option value="location" {{ old('question_type') == 'location' ? 'selected' : '' }}>Lokasi (Provinsi & Kota)</option>
                        </select>
                        @error('question_type')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Text options section -->
                <div id="text-options-section" class="mb-4 sm:mb-6 border border-gray-200 p-3 sm:p-4 rounded-lg {{ old('question_type') == 'text' ? '' : 'hidden' }}">
                    <h4 class="text-base sm:text-lg font-medium mb-3 flex items-center">
                        <i class="fas fa-keyboard text-blue-600 mr-2"></i>
                        Konfigurasi Teks Input
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="text_before" class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input (opsional):</label>
                            <input type="text" id="text_before" name="text_before_text" value="{{ old('text_before_text') }}" 
                                   placeholder="Contoh: Saya bekerja di" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="text_after" class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input (opsional):</label>
                            <input type="text" id="text_after" name="text_after_text" value="{{ old('text_after_text') }}" 
                                   placeholder="Contoh: sebagai posisi" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="mt-3 bg-blue-50 p-3 rounded-lg text-sm text-blue-700">
                        <strong>Preview:</strong> <span id="text-preview">{{ old('text_before_text', 'Masukkan') }} [Input Pengguna] {{ old('text_after_text', 'tahun') }}</span>
                    </div>
                </div>

                <!-- Email input configuration section -->
                <div id="email-options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'email' ? '' : 'hidden' }}">
                    <h4 class="text-lg font-medium mb-3">Konfigurasi Input Email</h4>
                    
                    <div class="bg-blue-50 p-3 rounded-md mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan email otomatis memvalidasi format email dan memastikan ada domain (@gmail.com, @yahoo.com, dll)
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="email_before" class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input (opsional):</label>
                            <input type="text" id="email_before" name="email_before_text" value="{{ old('email_before_text') }}" 
                                   placeholder="Contoh: Email aktif saya adalah" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="email_after" class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input (opsional):</label>
                            <input type="text" id="email_after" name="email_after_text" value="{{ old('email_after_text') }}" 
                                   placeholder="Contoh: yang bisa dihubungi" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Preview Input:</label>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <div class="flex items-center flex-wrap">
                                <span id="email-before-preview" class="mr-2 text-gray-700 font-medium"></span>
                                <input type="email" class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-48 email-only" 
                                       placeholder="contoh@domain.com" disabled>
                                <span id="email-after-preview" class="ml-2 text-gray-700 font-medium"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                        <strong>Contoh penggunaan:</strong> "Email aktif saya adalah [user@domain.com] yang bisa dihubungi"
                    </div>
                    
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 p-3 rounded-md">
                        <h5 class="font-medium text-yellow-800 mb-2">Validasi Email:</h5>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Harus mengandung karakter @</li>
                            <li>• Harus ada domain setelah @ (contoh: gmail.com, yahoo.com)</li>
                            <li>• Format email yang valid secara umum</li>
                            <li>• Tidak boleh ada spasi atau karakter khusus yang tidak diizinkan</li>
                        </ul>
                    </div>
                </div>

                <!-- Numeric input configuration section -->
                <div id="numeric-options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'numeric' ? '' : 'hidden' }}">
                    <h4 class="text-lg font-medium mb-3">Konfigurasi Input Numerik</h4>
                    
                    <div class="bg-blue-50 p-3 rounded-md mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan numerik hanya menerima input berupa angka (0-9) dan tidak dapat mengetik huruf
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="numeric_before" class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input (opsional):</label>
                            <input type="text" id="numeric_before" name="numeric_before_text" value="{{ old('numeric_before_text') }}" 
                                   placeholder="Contoh: Gaji saya sebesar Rp" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="numeric_after" class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input (opsional):</label>
                            <input type="text" id="numeric_after" name="numeric_after_text" value="{{ old('numeric_after_text') }}" 
                                   placeholder="Contoh: per bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Preview Input:</label>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <div class="flex items-center flex-wrap">
                                <span id="numeric-before-preview" class="mr-2 text-gray-700 font-medium"></span>
                                <input type="text" class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-48 numeric-only" 
                                       placeholder="Masukkan angka..." pattern="[0-9]*" inputmode="numeric" disabled>
                                <span id="numeric-after-preview" class="ml-2 text-gray-700 font-medium"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                        <strong>Contoh penggunaan:</strong> "Gaji saya sebesar Rp [input numerik] per bulan"
                    </div>
                </div>

                <!-- Hidden fields to handle before/after text based on question type -->
                <input type="hidden" id="final_before_text" name="before_text" value="{{ old('before_text') }}">
                <input type="hidden" id="final_after_text" name="after_text" value="{{ old('after_text') }}">

                <!-- Rating options section -->
                <div id="rating-options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'rating' ? '' : 'hidden' }}">
                    <h4 class="text-lg font-medium mb-3">Konfigurasi Rating</h4>
                    
                    <div class="bg-blue-50 p-3 rounded-md mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan rating akan menampilkan pilihan: Kurang, Cukup, Baik, Baik Sekali
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Preview Rating:</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                <input type="radio" name="preview_rating" value="1" class="mr-2" disabled>
                                <span class="text-red-600">Kurang</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                <input type="radio" name="preview_rating" value="2" class="mr-2" disabled>
                                <span class="text-yellow-600">Cukup</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                <input type="radio" name="preview_rating" value="3" class="mr-2" disabled>
                                <span class="text-blue-600">Baik</span>
                            </label>
                            <label class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                <input type="radio" name="preview_rating" value="4" class="mr-2" disabled>
                                <span class="text-green-600">Baik Sekali</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                        <strong>Contoh penggunaan:</strong> "Kemampuan menyelesaikan pekerjaan/tugas sesuai dengan target"
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
                <div id="scale-options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'scale' ? '' : 'hidden' }}">
                    <h4 class="text-lg font-medium mb-3">Konfigurasi Skala Numerik</h4>
                    
                    <div class="bg-blue-50 p-3 rounded-md mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pertanyaan skala akan menampilkan pilihan angka: 1, 2, 3, 4, 5
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="scale_min_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai terendah (1):</label>
                            <input type="text" id="scale_min_label" name="scale_min_label" value="{{ old('scale_min_label', 'Sangat Kurang') }}" 
                                   placeholder="Contoh: Sangat Kurang" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="scale_max_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai tertinggi (5):</label>
                            <input type="text" id="scale_max_label" name="scale_max_label" value="{{ old('scale_max_label', 'Sangat Baik') }}" 
                                   placeholder="Contoh: Sangat Baik" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Preview Skala:</label>
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-md">
                            <span id="scale-min-preview" class="text-sm text-gray-600">Sangat Kurang</span>
                            <div class="flex gap-3">
                                <label class="flex flex-col items-center">
                                    <input type="radio" name="preview_scale" value="1" class="mb-1" disabled>
                                    <span class="text-lg font-bold text-red-600">1</span>
                                </label>
                                <label class="flex flex-col items-center">
                                    <input type="radio" name="preview_scale" value="2" class="mb-1" disabled>
                                    <span class="text-lg font-bold text-orange-600">2</span>
                                </label>
                                <label class="flex flex-col items-center">
                                    <input type="radio" name="preview_scale" value="3" class="mb-1" disabled>
                                    <span class="text-lg font-bold text-yellow-600">3</span>
                                </label>
                                <label class="flex flex-col items-center">
                                    <input type="radio" name="preview_scale" value="4" class="mb-1" disabled>
                                    <span class="text-lg font-bold text-blue-600">4</span>
                                </label>
                                <label class="flex flex-col items-center">
                                    <input type="radio" name="preview_scale" value="5" class="mb-1" disabled>
                                    <span class="text-lg font-bold text-green-600">5</span>
                                </label>
                            </div>
                            <span id="scale-max-preview" class="text-sm text-gray-600">Sangat Baik</span>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                        <strong>Contoh penggunaan:</strong> "Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda kuasai? Etika:"
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
                <div id="options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'option' || old('question_type') == 'multiple' ? '' : 'hidden' }}">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-lg font-medium">Pilihan Jawaban</h4>
                        <button type="button" id="add-option" class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Pilihan
                        </button>
                    </div>
                    
                    <div id="options-container">
                        @if(old('options'))
                            @foreach(old('options') as $index => $option)
                            <div class="option-item flex items-center mb-2 flex-wrap" data-index="{{ $index }}">
                                <input type="text" name="options[]" value="{{ $option }}" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                                
                                <!-- ✅ PERBAIKAN: Hidden fields untuk option mapping -->
                                <input type="hidden" name="option_indexes[]" value="{{ $index }}">
                                
                                <div class="flex items-center mr-2">
                                    <input type="checkbox" name="other_options[]" value="{{ $index }}" 
                                           {{ in_array($index, old('other_options', [])) ? 'checked' : '' }}
                                           onchange="toggleOtherConfig(this, {{ $index }})" class="mr-1" id="other_checkbox_{{ $index }}">
                                    <label for="other_checkbox_{{ $index }}" class="text-sm">Lainnya</label>
                                </div>
                                
                                @if($loop->first)
                                <span class="text-gray-400 text-sm mr-2">(Minimal 1)</span>
                                @else
                                <button type="button" class="remove-option text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                                
                                <!-- ✅ PERBAIKAN: Before/After Text Configuration sesuai dengan edit -->
                                <div class="other-text-config {{ in_array($index, old('other_options', [])) ? '' : 'hidden' }} w-full mt-2 bg-gray-50 p-4 rounded border border-gray-200" id="other_config_{{ $index }}">
                                    <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-edit mr-2 text-blue-600"></i>
                                        Konfigurasi Input "Lainnya"
                                    </h5>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input:</label>
                                            <!-- ✅ PERBAIKAN: Menggunakan array biasa seperti di edit -->
                                            <input type="text" name="other_before_text[]" value="{{ old('other_before_text.' . $index) }}" 
                                                   placeholder="Contoh: yaitu" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                   oninput="updateOtherPreview({{ $index }})">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input:</label>
                                            <!-- ✅ PERBAIKAN: Menggunakan array biasa seperti di edit -->
                                            <input type="text" name="other_after_text[]" value="{{ old('other_after_text.' . $index) }}" 
                                                   placeholder="Contoh: per bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                   oninput="updateOtherPreview({{ $index }})">
                                        </div>
                                    </div>
                                    
                                    <!-- Preview -->
                                    <div class="bg-white border border-gray-300 rounded-md p-3">
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Preview:</label>
                                        <div class="flex items-center flex-wrap" id="other_preview_{{ $index }}">
                                            <span class="other-before-text mr-2 text-gray-700 font-medium"></span>
                                            <input type="text" class="px-2 py-1 border border-gray-300 rounded text-sm min-w-32" 
                                                   placeholder="Input pengguna..." disabled>
                                            <span class="other-after-text ml-2 text-gray-700 font-medium"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 bg-blue-50 p-2 rounded text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Contoh:</strong> "Lainnya, yaitu [input] per bulan"
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <!-- Default first option -->
                        <div class="option-item flex items-center mb-2 flex-wrap" data-index="0">
                            <input type="text" name="options[]" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                            
                            <!-- ✅ PERBAIKAN: Hidden field untuk option mapping -->
                            <input type="hidden" name="option_indexes[]" value="0">
                            
                            <div class="flex items-center mr-2">
                                <input type="checkbox" name="other_options[]" value="0" onchange="toggleOtherConfig(this, 0)" class="mr-1" id="other_checkbox_0">
                                <label for="other_checkbox_0" class="text-sm">Lainnya</label>
                            </div>
                            <span class="text-gray-400 text-sm mr-2">(Minimal 1)</span>
                            
                            <!-- ✅ PERBAIKAN: Before/After Text untuk Default Option -->
                            <div class="other-text-config hidden w-full mt-2 bg-gray-50 p-4 rounded border border-gray-200" id="other_config_0">
                                <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                                    Konfigurasi Input "Lainnya"
                                </h5>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input:</label>
                                        <!-- ✅ PERBAIKAN: Menggunakan associative array -->
                                        <input type="text" name="other_before_text[0]" placeholder="Contoh: yaitu" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               oninput="updateOtherPreview(0)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input:</label>
                                        <!-- ✅ PERBAIKAN: Menggunakan associative array -->
                                        <input type="text" name="other_after_text[0]" placeholder="Contoh: per bulan" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                               oninput="updateOtherPreview(0)">
                                    </div>
                                </div>
                                
                                <!-- Preview -->
                                <div class="bg-white border border-gray-300 rounded-md p-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-2">Preview:</label>
                                    <div class="flex items-center flex-wrap" id="other_preview_0">
                                        <span class="other-before-text mr-2 text-gray-700 font-medium"></span>
                                        <input type="text" class="px-2 py-1 border border-gray-300 rounded text-sm min-w-32" 
                                               placeholder="Input pengguna..." disabled>
                                        <span class="other-after-text ml-2 text-gray-700 font-medium"></span>
                                    </div>
                                </div>
                                
                                <div class="mt-3 bg-blue-50 p-2 rounded text-xs text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Contoh:</strong> "Lainnya, yaitu [input] per bulan"
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @error('options')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                         class="space-y-4 {{ old('has_dependency') ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bergantung pada pertanyaan:
                            </label> 
                            <select name="depends_on" 
                                    id="depends_on_select" 
                                    class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('depends_on') border-red-500 @enderror">
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
                                <p class="text-red-500 text-xs mt-1 flex items-center">
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
                                    class="w-full px-3 py-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('depends_value') border-red-500 @enderror">
                                <option value="">-- Pilih Jawaban --</option>
                            </select>
                            @error('depends_value')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for dependency -->
                <input type="hidden" id="hidden_depends_on" name="hidden_depends_on" value="{{ old('depends_on') }}">
                <input type="hidden" id="hidden_depends_value" name="hidden_depends_value" value="{{ old('depends_value') }}">

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
                        Simpan Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
// ✅ PERBAIKAN: Enhanced function untuk update preview other text
window.updateOtherPreview = function(index) {
    const beforeInput = document.querySelector(`input[name="other_before_text[${index}]"]`);
    const afterInput = document.querySelector(`input[name="other_after_text[${index}]"]`);
    const previewContainer = document.getElementById(`other_preview_${index}`);
    
    if (beforeInput && afterInput && previewContainer) {
        const beforeSpan = previewContainer.querySelector('.other-before-text');
        const afterSpan = previewContainer.querySelector('.other-after-text');
        
        if (beforeSpan && afterSpan) {
            beforeSpan.textContent = beforeInput.value || '';
            afterSpan.textContent = afterInput.value || '';
        }
    }
};

// ✅ PERBAIKAN: Enhanced toggle function
window.toggleOtherConfig = function(checkbox, index) {
    const configDiv = document.getElementById(`other_config_${index}`);
    if (configDiv) {
        configDiv.classList.toggle('hidden', !checkbox.checked);
        
        // ✅ PERBAIKAN: Clear before/after text when hiding
        if (!checkbox.checked) {
            const beforeInput = configDiv.querySelector(`input[name="other_before_text[${index}]"]`);
            const afterInput = configDiv.querySelector(`input[name="other_after_text[${index}]"]`);
            if (beforeInput) beforeInput.value = '';
            if (afterInput) afterInput.value = '';
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
            console.log('Form submitting with question type:', questionType.value);
            
            // Only validate if question type is selected
            if (!questionType.value) {
                e.preventDefault();
                alert('Pilih tipe pertanyaan terlebih dahulu!');
                return false;
            }
            
            // For option/multiple types only, validate visible option inputs
            if (questionType.value === 'option' || questionType.value === 'multiple') {
                const visibleOptionInputs = document.querySelectorAll('#options-section input[name="options[]"]:not([type="hidden"])');
                let hasValidOption = false;
                
                visibleOptionInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasValidOption = true;
                    }
                });
                
                if (!hasValidOption) {
                    e.preventDefault();
                    alert('Minimal harus ada satu pilihan jawaban!');
                    return false;
                }
            }
            
            // REMOVED dependency validation - let backend handle it
            
            console.log('Form validation passed, submitting...');
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
                    console.log('Rating section shown');
                }
            } else if (this.value === 'scale') {
                if (sections.scale) {
                    sections.scale.classList.remove('hidden');
                    console.log('Scale section shown');
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
            
            console.log('Question type changed to:', this.value);
        });
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
                <input type="text" name="options[]" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                
                <!-- ✅ PERBAIKAN: Hidden field untuk option mapping -->
                <input type="hidden" name="option_indexes[]" value="${optionIndex}">
                
                <div class="flex items-center mr-2">
                    <input type="checkbox" name="other_options[]" value="${optionIndex}" onchange="toggleOtherConfig(this, ${optionIndex})" class="mr-1" id="other_checkbox_${optionIndex}">
                    <label for="other_checkbox_${optionIndex}" class="text-sm">Lainnya</label>
                </div>
                <button type="button" class="remove-option text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
                
                <div class="other-text-config hidden w-full mt-2 bg-gray-50 p-4 rounded border border-gray-200" id="other_config_${optionIndex}">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-edit mr-2 text-blue-600"></i>
                        Konfigurasi Input "Lainnya"
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input:</label>
                            <input type="text" name="other_before_text[${optionIndex}]" placeholder="Contoh: yaitu" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                   oninput="updateOtherPreview(${optionIndex})">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input:</label>
                            <input type="text" name="other_after_text[${optionIndex}]" placeholder="Contoh: per bulan" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                   oninput="updateOtherPreview(${optionIndex})">
                        </div>
                    </div>
                    
                    <!-- Preview -->
                    <div class="bg-white border border-gray-300 rounded-md p-3">
                        <label class="block text-xs font-medium text-gray-600 mb-2">Preview:</label>
                        <div class="flex items-center flex-wrap" id="other_preview_${optionIndex}">
                            <span class="other-before-text mr-2 text-gray-700 font-medium"></span>
                            <input type="text" class="px-2 py-1 border border-gray-300 rounded text-sm min-w-32" 
                                   placeholder="Input pengguna..." disabled>
                            <span class="other-after-text ml-2 text-gray-700 font-medium"></span>
                        </div>
                    </div>
                    
                    <div class="mt-3 bg-blue-50 p-2 rounded text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Contoh:</strong> "Lainnya, yaitu [input] per bulan"
                    </div>
                </div>
            `;
            
            optionsContainer.appendChild(optionDiv);
            optionIndex++;
        });
        
        // ✅ PERBAIKAN: Enhanced Remove option functionality
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
                const optionItem = e.target.closest('.option-item');
                if (optionItem && document.querySelectorAll('.option-item').length > 1) {
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
    
    // ✅ PERBAIKAN: Initialize previews for existing options
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
    
    // // SIMPLIFIED Dependency handling - basic functionality only
    // const questionOptionsMap = {
    //     @if(isset($availableQuestions) && count($availableQuestions) > 0)
    //         @foreach($availableQuestions as $q)
    //             @if(in_array($q->type, ['option', 'multiple', 'rating', 'scale']) && $q->options->count() > 0)
    //                 "{{ $q->id_question }}": [
    //                     @foreach($q->options as $option)
    //                         {
    //                             id: "{{ $option->id_questions_options }}",
    //                             text: @json($option->option)
    //                         },
    //                     @endforeach
    //                 ],
    //             @endif
    //         @endforeach
    //     @endif
    // };

    // const dependsOnSelect = document.getElementById('depends_on');
    // const dependsValueSelect = document.getElementById('depends_value');
    // const selectedDependsOn = "{{ old('depends_on') }}";
    // const selectedDependsValue = "{{ old('depends_value') }}";

    // // ===== ENHANCED DEPENDENCY FUNCTIONS =====
    // function populateDependsValueOptions(questionId, selectedValue = null) {
    //     const dependsValueSelect = document.getElementById('depends_value_select');
    //     if (!dependsValueSelect) return;
        
    //     // Clear existing options
    //     dependsValueSelect.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
        
    //     if (questionOptionsMap[questionId] && questionOptionsMap[questionId].length > 0) {
    //         questionOptionsMap[questionId].forEach(opt => {
    //             const optionEl = document.createElement('option');
    //             optionEl.value = opt.id;
    //             optionEl.textContent = opt.text;
    //             if (selectedValue && selectedValue == opt.id) {
    //                 optionEl.selected = true;
    //             }
    //             dependsValueSelect.appendChild(optionEl);
    //         });
            
    //     } else {
    //         const noOptionEl = document.createElement('option');
    //         noOptionEl.value = '';
    //         noOptionEl.textContent = '-- Tidak ada opsi tersedia --';
    //         noOptionEl.disabled = true;
    //         dependsValueSelect.appendChild(noOptionEl);
    //     }
    // }
    
    // // On page load, if there's old input, populate options
    // if (selectedDependsOn && dependsOnSelect && dependsValueSelect) {
    //     populateDependsValueOptions(selectedDependsOn, selectedDependsValue);
    // }

    // // On dependency question change
    // if (dependsOnSelect) {
    //     dependsOnSelect.addEventListener('change', function() {
    //         populateDependsValueOptions(this.value, null);
    //     });
    // }
    
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
        
        // ✅ PERBAIKAN: Clear before/after text when hiding
        if (!checkbox.checked) {
            const beforeInput = configDiv.querySelector(`input[name="other_before_text[${index}]"]`);
            const afterInput = configDiv.querySelector(`input[name="other_after_text[${index}]"]`);
            if (beforeInput) beforeInput.value = '';
            if (afterInput) afterInput.value = '';
            updateOtherPreview(index);
        }
    }
};

// ✅ PERBAIKAN: Enhanced dependency handling
const toggleConditional = document.getElementById('has_dependency');
const conditionalOptions = document.getElementById('conditional-options');
const dependsOnSelect = document.getElementById('depends_on_select');
const dependsValueSelect = document.getElementById('depends_value_select');

if (toggleConditional && conditionalOptions) {
    toggleConditional.addEventListener('change', function() {
        conditionalOptions.classList.toggle('hidden', !this.checked);
        
        console.log('Dependency checkbox changed:', this.checked);
        
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

// ✅ PERBAIKAN: Question options mapping for dependencies
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

// ✅ PERBAIKAN: Populate depends value options
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
        
        console.log(`Populated ${questionOptionsMap[questionId].length} options for question ${questionId}`);
    } else {
        // If no options available, show message
        const noOptionEl = document.createElement('option');
        noOptionEl.value = '';
        noOptionEl.textContent = '-- Tidak ada opsi tersedia --';
        noOptionEl.disabled = true;
        dependsValueSelect.appendChild(noOptionEl);
        
        console.log(`No options available for question ${questionId}`);
    }
}

// ✅ PERBAIKAN: On depends_on change
if (dependsOnSelect && dependsValueSelect) {
    dependsOnSelect.addEventListener('change', function() {
        console.log('Depends on changed to:', this.value);
        populateDependsValueOptions(this.value, null);
        
        // Update hidden field
        const hiddenDependsOn = document.getElementById('hidden_depends_on');
        if (hiddenDependsOn) {
            hiddenDependsOn.value = this.value;
        }
    });
}

// ✅ PERBAIKAN: On depends_value change
if (dependsValueSelect) {
    dependsValueSelect.addEventListener('change', function() {
        console.log('Depends value changed to:', this.value);
        
        // Update hidden field
        const hiddenDependsValue = document.getElementById('hidden_depends_value');
        if (hiddenDependsValue) {
            hiddenDependsValue.value = this.value;
        }
    });
}

// ✅ PERBAIKAN: Form submission validation
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
            
            console.log('Dependency enabled:', {
                depends_on: dependsOnSelect.value,
                depends_value: dependsValueSelect.value
            });
        } else {
            // Dependency is disabled, clear the hidden fields
            hiddenDependsOn.value = '';
            hiddenDependsValue.value = '';
            
            console.log('Dependency disabled, clearing values');
        }
        
        console.log('Form validation passed, submitting...');
        return true;
    });
}

// ✅ PERBAIKAN: Enhanced form handling with before/after text
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
        
        console.log('Updated hidden fields for', questionType, ':', {
            before: beforeValue,
            after: afterValue
        });
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
                    console.log('Rating section shown');
                }
            } else if (this.value === 'scale') {
                if (sections.scale) {
                    sections.scale.classList.remove('hidden');
                    console.log('Scale section shown');
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
            
            console.log('Question type changed to:', this.value);
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
        // Text preview
        const textBefore = document.getElementById('text_before');
        const textAfter = document.getElementById('text_after');
        const textBeforePreview = document.getElementById('text-before-preview');
        const textAfterPreview = document.getElementById('text-after-preview');
        
        if (textBefore && textAfter && textBeforePreview && textAfterPreview) {
            textBeforePreview.textContent = textBefore.value || '';
            textAfterPreview.textContent = textAfter.value || '';
        }
        
        // Email preview  
        const emailBefore = document.getElementById('email_before');
        const emailAfter = document.getElementById('email_after');
        const emailBeforePreview = document.getElementById('email-before-preview');
        const emailAfterPreview = document.getElementById('email-after-preview');
        
        if (emailBefore && emailAfter && emailBeforePreview && emailAfterPreview) {
            emailBeforePreview.textContent = emailBefore.value || '';
            emailAfterPreview.textContent = emailAfter.value || '';
        }
        
        // Numeric preview
        const numericBefore = document.getElementById('numeric_before');
        const numericAfter = document.getElementById('numeric_after');
        const numericBeforePreview = document.getElementById('numeric-before-preview');
        const numericAfterPreview = document.getElementById('numeric-after-preview');
        
        if (numericBefore && numericAfter && numericBeforePreview && numericAfterPreview) {
            numericBeforePreview.textContent = numericBefore.value || '';
            numericAfterPreview.textContent = numericAfter.value || '';
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
        console.log('Form submitting with before/after text:', {
            question_type: questionTypeSelect.value,
            before_text: document.getElementById('final_before_text').value,
            after_text: document.getElementById('final_after_text').value
        });
    });
});
</script>
    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
