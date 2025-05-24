@extends('layouts.app')

@section('content')
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
                <h1 class="text-xl font-semibold">Tambah Pertanyaan</h1>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="text-blue-600 hover:underline">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pertanyaan
                    </a>
                    <h2 class="text-xl font-bold mt-2">Tambah Pertanyaan Baru</h2>
                    <p class="text-gray-600">Kategori: {{ $category->category_name }}</p>
                </div>

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="{{ route('admin.questionnaire.question.store', [$periode->id_periode, $category->id_category]) }}" class="mt-4">
                    @csrf

                    <div class="mb-4">
                        <label for="question" class="block text-gray-700 text-sm font-bold mb-2">Pertanyaan:</label>
                        <textarea name="question" id="question" rows="3" class="w-full px-3 py-2 border rounded-md @error('question') border-red-500 @enderror" required>{{ old('question') }}</textarea>
                        @error('question')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Urutan:</label>
                        <input type="number" name="order" id="order" value="{{ old('order', $category->questions->count() + 1) }}" class="w-full px-3 py-2 border rounded-md @error('order') border-red-500 @enderror" required>
                        @error('order')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="question_type" class="block text-gray-700 text-sm font-bold mb-2">Tipe Pertanyaan:</label>
                        <select name="question_type" id="question_type" class="w-full px-3 py-2 border rounded-md @error('question_type') border-red-500 @enderror" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="text" {{ old('question_type') == 'text' ? 'selected' : '' }}>Teks</option>
                            <option value="option" {{ old('question_type') == 'option' ? 'selected' : '' }}>Pilihan Ganda</option>
                            <option value="multiple" {{ old('question_type') == 'multiple' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="rating" {{ old('question_type') == 'rating' ? 'selected' : '' }}>Rating (Kurang, Cukup, Baik, Baik Sekali)</option>
                            <option value="scale" {{ old('question_type') == 'scale' ? 'selected' : '' }}>Skala Numerik (1, 2, 3, 4, 5)</option>
                            <option value="date" {{ old('question_type') == 'date' ? 'selected' : '' }}>Tanggal</option>
                            <option value="location" {{ old('question_type') == 'location' ? 'selected' : '' }}>Lokasi (Provinsi & Kota)</option>
                        </select>
                        @error('question_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Text input configuration section -->
                    <div id="text-options-section" class="mb-4 border p-4 rounded-md {{ old('question_type') == 'text' ? '' : 'hidden' }}">
                        <h4 class="text-lg font-medium mb-3">Konfigurasi Teks Input</h4>
                        
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pertanyaan teks akan menampilkan input field untuk jawaban bebas
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="text_before" class="block text-sm font-medium text-gray-700 mb-2">Teks sebelum input (opsional):</label>
                                <input type="text" id="text_before" name="text_before_input" value="{{ old('text_before_input') }}" 
                                       placeholder="Contoh: Saya bekerja di" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="text_after" class="block text-sm font-medium text-gray-700 mb-2">Teks setelah input (opsional):</label>
                                <input type="text" id="text_after" name="text_after_input" value="{{ old('text_after_input') }}" 
                                       placeholder="Contoh: sebagai posisi" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Preview Input:</label>
                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                <div class="flex items-center flex-wrap">
                                    <span id="text-before-preview" class="mr-2 text-gray-700 font-medium"></span>
                                    <input type="text" class="flex-grow px-3 py-2 border border-gray-300 rounded-md min-w-48" 
                                           placeholder="Masukkan jawaban Anda..." disabled>
                                    <span id="text-after-preview" class="ml-2 text-gray-700 font-medium"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-2 rounded text-sm text-green-700">
                            <strong>Contoh penggunaan:</strong> "Saya bekerja di [input field] sebagai posisi [input field]"
                        </div>
                    </div>

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

                    <!-- Options section (existing) -->
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
                                <div class="flex items-center mb-2 flex-wrap">
                                    <input type="text" name="options[]" value="{{ $option }}" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                                    <div class="flex items-center mr-2">
                                        <input type="checkbox" name="other_options[]" value="{{ $index }}" 
                                               {{ in_array($index, old('other_options', [])) ? 'checked' : '' }}
                                               onchange="toggleOtherConfig(this)" class="mr-1">
                                        <label class="text-sm">Lainnya</label>
                                    </div>
                                    @if($loop->first)
                                    <span class="text-gray-400 text-sm mr-2">(Minimal 1)</span>
                                    @else
                                    <button type="button" class="remove-option text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                    
                                    <div class="other-text-config {{ in_array($index, old('other_options', [])) ? '' : 'hidden' }} w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_{{ $index }}">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Placeholder untuk input "Lainnya":</label>
                                        <input type="text" name="other_placeholders[]" value="{{ old('other_placeholders.' . $index) }}" placeholder="Contoh: Sebutkan lainnya..." class="w-full px-2 py-1 border rounded text-sm">
                                    </div>
                                </div>
                                @endforeach
                            @else
                            <!-- Default first option -->
                            <div class="flex items-center mb-2 flex-wrap">
                                <input type="text" name="options[]" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                                <div class="flex items-center mr-2">
                                    <input type="checkbox" name="other_options[]" value="0" onchange="toggleOtherConfig(this)" class="mr-1">
                                    <label class="text-sm">Lainnya</label>
                                </div>
                                <span class="text-gray-400 text-sm mr-2">(Minimal 1)</span>
                                
                                <div class="other-text-config hidden w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Placeholder untuk input "Lainnya":</label>
                                    <input type="text" name="other_placeholders[]" placeholder="Contoh: Sebutkan lainnya..." class="w-full px-2 py-1 border rounded text-sm">
                                </div>
                            </div>
                            @endif
                        </div>
                        @error('options')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dependency section -->
                    <div class="mb-4">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" id="has_dependency" name="has_dependency" value="1" {{ old('has_dependency') ? 'checked' : '' }} class="mr-2">
                            <label for="has_dependency" class="text-sm font-medium text-gray-700">Pertanyaan Bersyarat</label>
                        </div>
                        
                        <div id="conditional-options" class="{{ old('has_dependency') ? '' : 'hidden' }} border p-4 rounded-md bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="depends_on" class="block text-sm font-medium text-gray-700 mb-2">Bergantung pada pertanyaan:</label>
                                    <select id="depends_on" name="depends_on" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <option value="">-- Pilih Pertanyaan --</option>
                                        @foreach($availableQuestions as $availableQuestion)
                                            <option value="{{ $availableQuestion->id_question }}" {{ old('depends_on') == $availableQuestion->id_question ? 'selected' : '' }}>
                                                {{ $availableQuestion->question }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="depends_value" class="block text-sm font-medium text-gray-700 mb-2">Jika jawabannya:</label>
                                    <select id="depends_value" name="depends_value" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <option value="">-- Pilih Jawaban --</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Pertanyaan ini hanya akan muncul jika kondisi di atas terpenuhi.</p>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Simpan Pertanyaan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
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
                scale: document.getElementById('scale-options-section')
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
                if (document.getElementById('text-options-section')) {
                    document.getElementById('text-options-section').classList.remove('hidden');
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
    
    if (scaleMinLabel && scaleMaxLabel) {
        scaleMinLabel.addEventListener('input', updateScalePreview);
        scaleMaxLabel.addEventListener('input', updateScalePreview);
        updateScalePreview();
    }
    
    // Add option button for option/multiple types
    const addOptionBtn = document.getElementById('add-option');
    const optionsContainer = document.getElementById('options-container');
    
    if (addOptionBtn && optionsContainer) {
        let optionIndex = document.querySelectorAll('input[name="options[]"]:not([type="hidden"])').length;
        
        addOptionBtn.addEventListener('click', function() {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center mb-2 flex-wrap';
            optionDiv.innerHTML = `
                <input type="text" name="options[]" class="flex-grow px-3 py-2 border rounded-md mr-2" placeholder="Tuliskan pilihan..." required>
                <div class="flex items-center mr-2">
                    <input type="checkbox" name="other_options[]" value="${optionIndex}" onchange="toggleOtherConfig(this)" class="mr-1">
                    <label class="text-sm">Lainnya</label>
                </div>
                <button type="button" class="remove-option text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
                
                <div class="other-text-config hidden w-full mt-2 bg-gray-50 p-3 rounded border border-gray-200" id="other_config_${optionIndex}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Placeholder untuk input "Lainnya":</label>
                    <input type="text" name="other_placeholders[]" placeholder="Contoh: Sebutkan lainnya..." class="w-full px-2 py-1 border rounded text-sm">
                </div>
            `;
            
            optionsContainer.appendChild(optionDiv);
            optionIndex++;
        });
        
        // Remove option functionality
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') || e.target.closest('.remove-option')) {
                const optionDiv = e.target.closest('.flex');
                const visibleOptions = optionsContainer.querySelectorAll('input[name="options[]"]:not([type="hidden"])');
                if (visibleOptions.length > 1) {
                    optionDiv.remove();
                } else {
                    alert('Minimal harus ada satu pilihan jawaban!');
                }
            }
        });
    }
    
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
                @if(($q->type === 'option' || $q->type === 'multiple') && isset($q->options) && count($q->options) > 0)
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

    const dependsOnSelect = document.getElementById('depends_on');
    const dependsValueSelect = document.getElementById('depends_value');
    const selectedDependsOn = "{{ old('depends_on') }}";
    const selectedDependsValue = "{{ old('depends_value') }}";

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
        
        // Clear placeholder when hiding
        if (!checkbox.checked) {
            const placeholderInput = configDiv.querySelector('input[name="other_placeholders[]"]');
            if (placeholderInput) {
                placeholderInput.value = '';
            }
        }
    }
};
</script>
@endsection
