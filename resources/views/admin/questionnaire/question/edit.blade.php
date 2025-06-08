@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Logo" class="w-36 mt-2 object-contain">
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
                <button id="toggle-sidebar" class="mr-4 text-gray-600 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-semibold">Edit Pertanyaan</h1>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="text-blue-600 hover:underline">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pertanyaan
                    </a>
                    <h2 class="text-xl font-bold mt-2">Edit Pertanyaan</h2>
                    <p class="text-gray-600">Kategori: {{ $category->category_name }}</p>
                </div>

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif

                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Helper section to indicate current question type -->
                <div class="mb-4 bg-yellow-50 p-3 rounded-md border border-yellow-200">
                    <p class="font-medium">Tipe pertanyaan saat ini: <span class="text-blue-700">{{ ucfirst($question->type) }}</span></p>
                    <p class="text-sm mt-1">Untuk mengubah tipe pertanyaan, hapus dan buat ulang pertanyaan ini.</p>
                </div>

                @if(config('app.debug'))
                <div class="bg-yellow-50 p-3 rounded-md mb-4 text-sm border border-yellow-200">
                    <strong>Debug Info:</strong> Type: {{ $question->type }} | 
                    Before Text: {{ $question->before_text ?? 'null' }} |
                    After Text: {{ $question->after_text ?? 'null' }}
                </div>
                @endif

                <!-- Form -->
                <form id="questionForm" method="POST" action="{{ route('admin.questionnaire.question.update', [$periode->id_periode, $category->id_category, $question->id_question]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Question text -->
                    <div class="mb-4">
                        <label for="question" class="block text-gray-700 text-sm font-bold mb-2">Pertanyaan:</label>
                        <textarea name="question" id="question" rows="3" class="w-full px-3 py-2 border rounded-md @error('question') border-red-500 @enderror" required>{{ old('question', $question->question) }}</textarea>
                        @error('question')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order -->
                    <div class="mb-4">
                        <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Urutan:</label>
                        <input type="number" name="order" id="order" value="{{ old('order', $question->order) }}" class="w-full px-3 py-2 border rounded-md @error('order') border-red-500 @enderror" required>
                        @error('order')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type selection - LOCKED with hidden field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipe Pertanyaan (Terkunci):</label>
                        <select class="w-full px-3 py-2 border rounded-md bg-gray-100" disabled>
                            <option value="{{ $question->type }}" selected>
                                @if($question->type == 'text') Teks
                                @elseif($question->type == 'numeric') Numerik (Hanya Angka)
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
                        <p class="text-sm text-gray-500 mt-1">Tipe pertanyaan tidak dapat diubah setelah dibuat.</p>
                    </div>

                    <!-- Text options section -->
                    @if($question->type == 'text')
                    <div id="text-options-section" class="mb-4 border p-4 rounded-md">
                        <h4 class="text-lg font-medium mb-3">Konfigurasi Teks Input</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="before_text" class="block text-gray-700 text-sm font-bold mb-2">Teks Sebelum Input:</label>
                                <input type="text" name="before_text" id="before_text" value="{{ old('before_text', $question->before_text) }}" class="w-full px-3 py-2 border rounded-md" placeholder="Contoh: Masukkan">
                           
                            </div>
                            <div>
                                <label for="after_text" class="block text-gray-700 text-sm font-bold mb-2">Teks Setelah Input:</label>
                                <input type="text" name="after_text" id="after_text" value="{{ old('after_text', $question->after_text) }}" class="w-full px-3 py-2 border rounded-md" placeholder="Contoh: tahun">
                            
                            </div>
                        </div>
                        
                        <div class="mt-3 bg-blue-50 p-2 rounded text-sm text-blue-700">
                            <strong>Preview:</strong> <span id="text-preview">{{ $question->before_text ?? 'Masukkan' }} [Input Pengguna] {{ $question->after_text ?? 'tahun' }}</span>
                        </div>
                    </div>
                    @endif
                    <!-- Numeric options section -->
                    @if($question->type == 'numeric')
                    <div id="numeric-options-section" class="mb-4 border p-4 rounded-md">
                        <h4 class="text-lg font-medium mb-3">Konfigurasi Input Numerik</h4>
                        
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan numerik hanya menerima input berupa angka (0-9) dan tidak dapat mengetik huruf
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="numeric_before_text" class="block text-gray-700 text-sm font-bold mb-2">Teks Sebelum Input:</label>
                                <input type="text" name="before_text" id="numeric_before_text" value="{{ old('before_text', $question->before_text) }}" class="w-full px-3 py-2 border rounded-md" placeholder="Contoh: Gaji saya sebesar Rp">
                            </div>
                            <div>
                                <label for="numeric_after_text" class="block text-gray-700 text-sm font-bold mb-2">Teks Setelah Input:</label>
                                <input type="text" name="after_text" id="numeric_after_text" value="{{ old('after_text', $question->after_text) }}" class="w-full px-3 py-2 border rounded-md" placeholder="Contoh: per bulan">
                            </div>
                        </div>
                        
                        <div class="mt-3 bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Preview Input:</label>
                            <div class="flex items-center flex-wrap">
                                <span id="numeric-before-preview" class="mr-2 text-gray-700 font-medium">{{ $question->before_text ?? '' }}</span>
                                <input type="text" class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-48" 
                                       placeholder="Masukkan angka..." pattern="[0-9]*" inputmode="numeric" disabled>
                                <span id="numeric-after-preview" class="ml-2 text-gray-700 font-medium">{{ $question->after_text ?? '' }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-2 rounded text-sm text-green-700 mt-3">
                            <strong>Contoh penggunaan:</strong> "Gaji saya sebesar Rp [input numerik] per bulan"
                        </div>
                    </div>
                    @endif
                    <!-- Rating options section -->
                    @if($question->type == 'rating')
                    <div id="rating-options-section" class="mb-4 border p-4 rounded-md">
                        <h4 class="text-lg font-medium mb-3">Konfigurasi Rating</h4>
                        
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan rating menampilkan pilihan: Kurang, Cukup, Baik, Baik Sekali
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
                        
                        <div class="bg-yellow-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-lock mr-1"></i>
                                Opsi rating sudah tetap dan tidak dapat diubah. Untuk mengubah opsi, hapus dan buat ulang pertanyaan ini.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                            <strong>Contoh penggunaan:</strong> "Kemampuan menyelesaikan pekerjaan/tugas sesuai dengan target"
                        </div>
                        
                        <!-- Hidden inputs for rating options - always include current options -->
                        <div class="hidden">
                            @foreach($question->options as $option)
                                <input type="hidden" name="rating_options[]" value="{{ $option->option }}">
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Scale options section -->
                    @if($question->type == 'scale')
                    <div id="scale-options-section" class="mb-4 border p-4 rounded-md">
                        <h4 class="text-lg font-medium mb-3">Konfigurasi Skala Numerik</h4>
                        
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan skala menampilkan pilihan angka: 1, 2, 3, 4, 5
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="scale_min_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai terendah (1):</label>
                                <input type="text" id="scale_min_label" name="before_text" value="{{ old('before_text', $question->before_text ?: 'Sangat Kurang') }}" 
                                       placeholder="Contoh: Sangat Kurang" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                              
                            </div>
                            <div>
                                <label for="scale_max_label" class="block text-sm font-medium text-gray-700 mb-2">Label untuk nilai tertinggi (5):</label>
                                <input type="text" id="scale_max_label" name="after_text" value="{{ old('after_text', $question->after_text ?: 'Sangat Baik') }}" 
                                       placeholder="Contoh: Sangat Baik" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                @error('after_text')
                            
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Preview Skala:</label>
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-md">
                                <span id="scale-min-preview" class="text-sm text-gray-600">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
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
                                <span id="scale-max-preview" class="text-sm text-gray-600">{{ $question->after_text ?: 'Sangat Baik' }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-lock mr-1"></i>
                                Opsi skala (1-5) sudah tetap dan tidak dapat diubah. Anda hanya dapat mengubah label minimum dan maksimum.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                            <strong>Contoh penggunaan:</strong> "Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda kuasai? Etika:"
                        </div>
                        
                        <!-- Hidden inputs for scale options - always include current options -->
                        <div class="hidden">
                            @foreach($question->options as $option)
                                <input type="hidden" name="scale_options[]" value="{{ $option->option }}">
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Options section for option/multiple type questions -->
                    @if($question->type == 'option' || $question->type == 'multiple')
                    <div id="options-section" class="mb-4 border p-4 rounded-md">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-lg font-medium">Pilihan Jawaban</h4>
                            <button type="button" id="add-option" class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i> Tambah Pilihan
                            </button>
                        </div>
                        
                        <div id="options-container">
                            @if(old('options'))
                                @foreach(old('options') as $index => $option)
                                <div class="flex items-center mb-2 flex-wrap">
                                    <input type="hidden" name="option_ids[]" value="{{ old('option_ids')[$index] ?? '' }}">
                                    <input type="text" name="options[]" value="{{ $option }}" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                                    <div class="flex items-center mr-2">
                                        <input type="checkbox" id="is_other_option_{{ $index }}" name="is_other_option[]" value="{{ $index }}" class="other-option-checkbox" 
                                               {{ in_array($index, old('is_other_option', [])) ? 'checked' : '' }} onchange="toggleOtherConfig(this)">
                                        <label for="is_other_option_{{ $index }}" class="ml-1 text-sm">Lainnya</label>
                                    </div>
                                    <button type="button" class="remove-option text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <div class="other-text-config {{ in_array($index, old('is_other_option', [])) ? '' : 'hidden' }} w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_{{ $index }}">
                                        <h4 class="text-sm font-medium mb-2">Konfigurasi Teks "Lainnya"</h4>
                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <div>
                                                <label class="text-xs text-gray-600">Teks Sebelum:</label>
                                                <input type="text" name="other_before_text[]" value="{{ old('other_before_text')[$index] ?? '' }}" class="w-full px-2 py-1 border rounded text-sm" placeholder="Sebutkan">
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">Teks Setelah:</label>
                                                <input type="text" name="other_after_text[]" value="{{ old('other_after_text')[$index] ?? '' }}" class="w-full px-2 py-1 border rounded text-sm" placeholder="lainnya">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500">Preview: <span class="other-preview">Sebutkan [Input] lainnya</span></p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                @foreach($question->options as $index => $option)
                                <div class="flex items-center mb-2 flex-wrap">
                                    <input type="hidden" name="option_ids[]" value="{{ $option->id_questions_options }}">
                                    <input type="text" name="options[]" value="{{ $option->option }}" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                                    <div class="flex items-center mr-2">
                                        <input type="checkbox" id="is_other_option_{{ $index }}" name="is_other_option[]" value="{{ $index }}" class="other-option-checkbox" 
                                               {{ $option->is_other_option ? 'checked' : '' }} onchange="toggleOtherConfig(this)">
                                        <label for="is_other_option_{{ $index }}" class="ml-1 text-sm">Lainnya</label>
                                    </div>
                                    <button type="button" class="remove-option text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <div class="other-text-config {{ $option->is_other_option ? '' : 'hidden' }} w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_{{ $index }}">
                                        <h4 class="text-sm font-medium mb-2">Konfigurasi Teks "Lainnya"</h4>
                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <div>
                                                <label class="text-xs text-gray-600">Teks Sebelum:</label>
                                                <input type="text" name="other_before_text[]" value="{{ $option->other_before_text ?? '' }}" class="w-full px-2 py-1 border rounded text-sm" placeholder="Sebutkan">
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">Teks Setelah:</label>
                                                <input type="text" name="other_after_text[]" value="{{ $option->other_after_text ?? '' }}" class="w-full px-2 py-1 border rounded text-sm" placeholder="lainnya">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500">Preview: <span class="other-preview">Sebutkan [Input] lainnya</span></p>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        
                      
                    </div>
                    @endif

                    <!-- Dependency section -->
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="toggle-conditional" name="has_dependency" value="1" class="mr-2" 
                                   {{ $question->depends_on ? 'checked' : '' }}>
                            <label for="toggle-conditional" class="text-sm font-medium">
                                Pertanyaan ini muncul berdasarkan jawaban pertanyaan lain
                            </label>
                        </div>
                        
                        <div id="conditional-options" class="border p-4 rounded-md mt-2" 
                             style="display: {{ $question->depends_on ? 'block' : 'none' }}">
                            <div class="mb-3">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Bergantung pada pertanyaan:
                                </label> 
                                <select name="depends_on" id="depends_on_select" class="w-full px-3 py-2 border rounded-md @error('depends_on') border-red-500 @enderror">
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
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Pertanyaan dengan tipe 'option', 'multiple', 'rating', atau 'scale' dapat dijadikan dependensi</p>
                            </div>

                            <div class="mb-3">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Pilih jawaban yang memicu pertanyaan ini:
                                </label>
                                <select name="depends_value" id="depends_value_select" class="w-full px-3 py-2 border rounded-md @error('depends_value') border-red-500 @enderror">
                                    <option value="">-- Pilih Jawaban --</option>
                                </select>
                                @error('depends_value')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Debug info for dependency -->
                            @if(config('app.debug') && $question->depends_on)
                                <div class="bg-yellow-50 p-2 rounded text-xs border border-yellow-200">
                                    <strong>Current Dependency:</strong> Question ID {{ $question->depends_on }} → Option ID {{ $question->depends_value }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Hidden fields to ensure dependency values are submitted -->
                    <input type="hidden" id="hidden_depends_on" name="hidden_depends_on" value="{{ old('depends_on', $question->depends_on) }}">
                    <input type="hidden" id="hidden_depends_value" name="hidden_depends_value" value="{{ old('depends_value', $question->depends_value) }}">

                    <!-- Submit buttons -->
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md mr-2">Batal</a>
                        <button id="submit-button" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('questionForm');
    const submitButton = document.getElementById('submit-button');
    const questionType = '{{ $question->type }}';
    
    console.log('Page loaded, question type:', questionType);
    
    // Form submission handler - SIMPLIFIED
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            
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
            
            // Handle dependency fields before submission
            const toggleConditional = document.getElementById('toggle-conditional');
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
                dependsOnSelect.value = '';
                dependsValueSelect.value = '';
                
                console.log('Dependency disabled, clearing values');
            }
            
            console.log('Form validation passed, submitting...');
            return true;
        });
    }
    
    // Text preview functionality for text type questions
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
            
            // Initial preview update
            updateTextPreview();
        }
    }
    // Numeric preview functionality for numeric type questions
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
            
            // Initial preview update
            updateNumericPreview();
        }
    }
    // Scale preview functionality for scale type questions
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
            
            console.log('Dependency checkbox changed:', this.checked);
            
            // Reset dependency selects when unchecked
            if (!this.checked) {
                const dependsOnSelect = document.getElementById('depends_on_select');
                const dependsValueSelect = document.getElementById('depends_value_select');
                if (dependsOnSelect) dependsOnSelect.value = '';
                if (dependsValueSelect) {
                    dependsValueSelect.innerHTML = '<option value="">-- Pilih Jawaban --</option>';
                    dependsValueSelect.value = '';
                }
            }
        });
    }
    
    // Add option functionality for option/multiple types
    if (questionType === 'option' || questionType === 'multiple') {
        const addOptionBtn = document.getElementById('add-option');
        const optionsContainer = document.getElementById('options-container');
        
        if (addOptionBtn && optionsContainer) {
            let optionIndex = document.querySelectorAll('input[name="options[]"]').length;
            
            addOptionBtn.addEventListener('click', function() {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'flex items-center mb-2 flex-wrap';
                optionDiv.innerHTML = `
                    <input type="hidden" name="option_ids[]" value="">
                    <input type="text" name="options[]" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                    <div class="flex items-center mr-2">
                        <input type="checkbox" id="is_other_option_${optionIndex}" name="is_other_option[]" value="${optionIndex}" class="other-option-checkbox" onchange="toggleOtherConfig(this)">
                        <label for="is_other_option_${optionIndex}" class="ml-1 text-sm">Lainnya</label>
                    </div>
                    <button type="button" class="remove-option text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div class="other-text-config hidden w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_${optionIndex}">
                        <h4 class="text-sm font-medium mb-2">Konfigurasi Teks "Lainnya"</h4>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-xs text-gray-600">Teks Sebelum:</label>
                                <input type="text" name="other_before_text[]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Sebutkan">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Teks Setelah:</label>
                                <input type="text" name="other_after_text[]" class="w-full px-2 py-1 border rounded text-sm" placeholder="lainnya">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Preview: <span class="other-preview">Sebutkan [Input] lainnya</span></p>
                    </div>
                `;
                
                optionsContainer.appendChild(optionDiv);
                optionIndex++;
            });
            
            // Remove option functionality
            optionsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-option') || e.target.parentElement.classList.contains('remove-option')) {
                    const optionDiv = e.target.closest('.flex.items-center.mb-2');
                    if (optionDiv && document.querySelectorAll('input[name="options[]"]').length > 1) {
                        optionDiv.remove();
                    }
                }
            });
        }
    }
    
    // Dependency options handling - UPDATED to include rating and scale
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

    console.log('Question options map:', questionOptionsMap);

    const dependsOnSelect = document.getElementById('depends_on_select');
    const dependsValueSelect = document.getElementById('depends_value_select');
    const selectedDependsOn = "{{ old('depends_on', $question->depends_on ?? '') }}";
    const selectedDependsValue = "{{ old('depends_value', $question->depends_value ?? '') }}";

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
        
        // Update hidden field
        const hiddenDependsValue = document.getElementById('hidden_depends_value');
        if (hiddenDependsValue && selectedValue) {
            hiddenDependsValue.value = selectedValue;
        }
    }

    // On page load, if editing and has dependency, populate options
    if (selectedDependsOn && dependsOnSelect && dependsValueSelect) {
        console.log('Loading existing dependency:', selectedDependsOn, selectedDependsValue);
        populateDependsValueOptions(selectedDependsOn, selectedDependsValue);
    }

    // On depends_on change
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
    
    // On depends_value change
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
    
    // Other option preview functionality
    const updateOtherPreviews = function() {
        document.querySelectorAll('.other-text-config').forEach(config => {
            const beforeText = config.querySelector('input[name="other_before_text[]"]');
            const afterText = config.querySelector('input[name="other_after_text[]"]');
            const preview = config.querySelector('.other-preview');
            
            if (beforeText && afterText && preview) {
                const before = beforeText.value || 'Sebutkan';
                const after = afterText.value || 'lainnya';
                preview.textContent = `${before} [Input] ${after}`;
            }
        });
    };
    
    // Add event listeners for other option text inputs
    document.addEventListener('input', function(e) {
        if (e.target.name === 'other_before_text[]' || e.target.name === 'other_after_text[]') {
            updateOtherPreviews();
        }
    });
    
    // Initial update for other previews
    updateOtherPreviews();
    
    // Sidebar functionality
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });
});

// Global function for other option configuration
function toggleOtherConfig(checkbox) {
    const index = checkbox.value;
    const configDiv = document.getElementById(`other_config_${index}`);
    if (configDiv) {
        configDiv.classList.toggle('hidden', !checkbox.checked);
        
        // Clear other text fields when hiding
        if (!checkbox.checked) {
            const beforeText = configDiv.querySelector('input[name="other_before_text[]"]');
            const afterText = configDiv.querySelector('input[name="other_after_text[]"]');
            if (beforeText) beforeText.value = '';
            if (afterText) afterText.value = '';
        }
    }
}
</script>
@endsection
