@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner employee'])

        <!-- Content Section -->
        <div class="p-6">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('dashboard.company') }}" class="text-blue-600 hover:underline">Dashboard</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" class="text-blue-600 hover:underline">Pilih Alumni</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700">Pengisian</li>
                </ol>
            </nav>

            <!-- Alumni Info Card - TAMBAHAN BARU -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-graduate text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-blue-900">{{ $alumni->name }}</h3>
                            <p class="text-blue-700">NIM: {{ $alumni->nim }}</p>
                            @if($alumni->graduation_year)
                                <p class="text-sm text-blue-600">Tahun Lulus: {{ $alumni->graduation_year }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-blue-900">Periode</div>
                        <div class="text-blue-700">{{ $periode->title }}</div>
                        <div class="text-xs text-blue-600">
                            {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Progress Pengisian</h2>
                    <span class="text-sm font-medium text-gray-600">{{ $progressPercentage }}% Selesai</span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                </div>
                
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Kategori {{ $currentCategoryIndex + 1 }} dari {{ $totalCategories }}</span>
                    <span>{{ $currentCategory->category_name }}</span>
                </div>
            </div>

            <!-- Category Navigation -->
            @if($allCategories->count() > 1)
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Kategori Kuesioner</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($allCategories as $index => $category)
                            <p href="{{ route('company.questionnaire.fill', [$periode->id_periode, $nim, $category->id_category]) }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200
                                      {{ $category->id_category == $currentCategory->id_category 
                                         ? 'bg-blue-600 text-white' 
                                         : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $index + 1 }}. {{ $category->category_name }}
</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Main Form -->
            <form method="POST" action="{{ route('company.questionnaire.submit', [$periode->id_periode, $nim]) }}" 
                  id="questionnaire-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_category" value="{{ $currentCategory->id_category }}">
                
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $currentCategory->category_name }}</h2>
                        @if($currentCategory->description)
                            <p class="text-gray-600 mt-2">{{ $currentCategory->description }}</p>
                        @endif
                    </div>

                    @if($questions->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-question-circle text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600">Tidak ada pertanyaan untuk kategori ini.</p>
                        </div>
                    @else
                        @foreach($questions as $index => $question)
                            <div class="question-container border border-gray-200 rounded-lg p-6 mb-6" 
                                 data-question-id="{{ $question->id_question }}"
                                 data-question-type="{{ $question->type }}"
                                 data-required="{{ $question->is_required ? 'true' : 'false' }}"
                                 @if($question->depends_on)
                                     data-parent-question="{{ $question->depends_on }}"
                                     data-condition-value="{{ $question->depends_value }}"
                                     style="display: none;"
                                 @endif>
                                
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-4">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            {{ $question->question }}
                                            @if($question->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </h3>
                                        
                                        @if($question->description)
                                            <p class="text-gray-600 text-sm mb-4">{{ $question->description }}</p>
                                        @endif

                                        <!-- Question Input Based on Type -->
                                        @include('components.questionnaire.question-input', [
                                            'question' => $question,
                                            'prevAnswer' => $prevAnswers[$question->id_question] ?? null,
                                            'prevOtherAnswer' => $prevOtherAnswers[$question->id_question] ?? null,
                                            'prevMultipleAnswers' => $prevMultipleAnswers[$question->id_question] ?? [],
                                            'prevMultipleOtherAnswers' => $prevMultipleOtherAnswers[$question->id_question] ?? [],
                                            'prevLocationAnswer' => $prevLocationAnswers[$question->id_question] ?? null
                                        ])

                                        <!-- Validation Message -->
                                        <div class="validation-message text-red-600 text-sm mt-2 hidden"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Navigation Buttons -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex gap-3">
                            @if($prevCategory)
                                <button type="submit" name="action" value="prev_category"
                                        class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kategori Sebelumnya
                                </button>
                            @endif
                            
                            <button type="submit" name="action" value="save_draft"
                                    class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Draft
                            </button>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Pilih Alumni
                            </a>

                            @if($nextCategory)
                                <button type="submit" name="action" value="next_category" id="next-category-btn"
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Kategori Selanjutnya
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            @else
                                <button type="submit" name="action" value="submit_final" id="submit-final-btn"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Selesaikan Kuesioner
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Enhanced Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Pengiriman</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menyelesaikan kuesioner untuk alumni <strong>{{ $alumni->full_name }}</strong>? 
                Setelah dikirim, Anda tidak dapat mengubah jawaban.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" id="cancel-submit" 
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors duration-200">
                    Batal
                </button>
                <button type="button" id="confirm-submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                    Ya, Selesaikan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Validation Alert -->
<div id="validation-alert" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 hidden max-w-md">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <span id="validation-message">Ada pertanyaan wajib yang belum diisi!</span>
    </div>
</div>

<style>
.question-container.border-red-300 {
    border-color: #fca5a5 !important;
    background-color: #fef2f2 !important;
}

.validation-message {
    font-size: 0.875rem;
    font-weight: 500;
}

.validation-message.hidden {
    display: none;
}

/* Animation for validation alerts */
#validation-alert {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Pulsing effect for required questions */
.question-container.border-red-300 {
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% {
        border-color: #fca5a5;
        background-color: #fef2f2;
    }
    70% {
        border-color: #f87171;
        background-color: #fee2e2;
    }
    100% {
        border-color: #fca5a5;
        background-color: #fef2f2;
    }
}

/* Numeric input styling */
.numeric-only {
    appearance: textfield;
}

.numeric-only:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Shake animation for numeric input errors */
@keyframes shake {
    0%, 20%, 40%, 60%, 80%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing company questionnaire functionality');
    
    // ✅ PERBAIKAN: Pass data dari backend to JavaScript dengan benar
    window.questionnaireData = {
        conditionalQuestions: @json($conditionalQuestions ?? []),
        periode: @json($periode),
        alumni: @json($alumni),
        nim: "{{ $nim }}",
        currentCategory: @json($currentCategory),
        totalCategories: {{ $totalCategories }},
        currentCategoryIndex: {{ $currentCategoryIndex }},
        routes: {
            provinces: "{{ route('company.questionnaire.provinces') }}",
            cities: "{{ route('company.questionnaire.cities', ':provinceId') }}"
        }
    };
    
    console.log('✅ Questionnaire data loaded:', window.questionnaireData);
    
    initializeQuestionnaire();
});

function initializeQuestionnaire() {
    console.log('🔧 Initializing questionnaire functionality...');
    
    // Initialize conditional questions
    initializeConditionalQuestions();
    
    // Initialize location dropdowns
    initializeLocationQuestions();
    
    // ✅ TAMBAHAN: Initialize option toggles untuk "Lainnya"
    initializeOptionToggles();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize confirmation modal
    initializeConfirmationModal();
    
    console.log('✅ All questionnaire functionality initialized');
}

// ✅ PERBAIKAN: Function untuk handle toggle "Other" options
function initializeOptionToggles() {
    console.log('🔧 Initializing option toggles for "Other" fields...');
    
    // ✅ Handle Single Option (Radio) toggles
    document.querySelectorAll('.option-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.dataset.questionId;
            const isOther = this.dataset.isOther === '1';
            const optionId = this.value;
            
            console.log('Radio changed:', { questionId, isOther, optionId, checked: this.checked });
            
            // Hide all other fields for this question first
            document.querySelectorAll(`[id^="other_field_${questionId}_"]`).forEach(field => {
                field.classList.add('hidden');
                console.log('Hiding other field:', field.id);
            });
            
            // Show other field if this is an "other" option and it's selected
            if (isOther && this.checked) {
                const otherField = document.getElementById(`other_field_${questionId}_${optionId}`);
                if (otherField) {
                    otherField.classList.remove('hidden');
                    console.log('Showing other field:', otherField.id);
                    
                    // Focus on the text input
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput) {
                        setTimeout(() => otherInput.focus(), 100);
                    }
                } else {
                    console.warn('Other field not found:', `other_field_${questionId}_${optionId}`);
                }
            }
        });
        
        // ✅ Check initial state untuk radio yang sudah checked
        if (radio.checked && radio.dataset.isOther === '1') {
            const questionId = radio.dataset.questionId;
            const optionId = radio.value;
            const otherField = document.getElementById(`other_field_${questionId}_${optionId}`);
            if (otherField) {
                otherField.classList.remove('hidden');
                console.log('Initial state: Showing other field for checked radio:', otherField.id);
            }
        }
    });
    
    // ✅ Handle Multiple Choice (Checkbox) toggles
    document.querySelectorAll('.multiple-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const questionId = this.dataset.questionId;
            const isOther = this.dataset.isOther === '1';
            const optionId = this.value;
            
            console.log('Checkbox changed:', { questionId, isOther, optionId, checked: this.checked });
            
            if (isOther) {
                const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionId}`);
                if (otherField) {
                    if (this.checked) {
                        otherField.classList.remove('hidden');
                        console.log('Showing multiple other field:', otherField.id);
                        
                        // Focus on the text input
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (otherInput) {
                            setTimeout(() => otherInput.focus(), 100);
                        }
                    } else {
                        otherField.classList.add('hidden');
                        console.log('Hiding multiple other field:', otherField.id);
                        
                        // Clear the input value when hiding
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (otherInput) {
                            otherInput.value = '';
                        }
                    }
                } else {
                    console.warn('Multiple other field not found:', `multiple_other_field_${questionId}_${optionId}`);
                }
            }
        });
        
        // ✅ Check initial state untuk checkbox yang sudah checked
        if (checkbox.checked && checkbox.dataset.isOther === '1') {
            const questionId = checkbox.dataset.questionId;
            const optionId = checkbox.value;
            const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionId}`);
            if (otherField) {
                otherField.classList.remove('hidden');
                console.log('Initial state: Showing multiple other field for checked checkbox:', otherField.id);
            }
        }
    });
    
    console.log('✅ Option toggles initialized');
}

function initializeConditionalQuestions() {
    const conditionalQuestions = window.questionnaireData.conditionalQuestions;
    
    if (!conditionalQuestions || Object.keys(conditionalQuestions).length === 0) {
        console.log('No conditional questions found');
        return;
    }
    
    console.log('Initializing conditional questions:', conditionalQuestions);
    
    // Monitor parent questions
    Object.keys(conditionalQuestions).forEach(parentQuestionId => {
        const parentInputs = document.querySelectorAll(`[name^="question_${parentQuestionId}"]`);
        
        parentInputs.forEach(input => {
            input.addEventListener('change', function() {
                handleConditionalQuestion(parentQuestionId, this.value);
            });
        });
        
        // Check initial state
        const checkedInput = document.querySelector(`[name^="question_${parentQuestionId}"]:checked`);
        if (checkedInput) {
            handleConditionalQuestion(parentQuestionId, checkedInput.value);
        }
    });
}

function handleConditionalQuestion(parentQuestionId, selectedValue) {
    const conditionalQuestions = window.questionnaireData.conditionalQuestions[parentQuestionId];
    
    if (!conditionalQuestions) return;
    
    conditionalQuestions.forEach(condition => {
        const childQuestion = document.querySelector(`[data-question-id="${condition.question_id}"]`);
        
        if (childQuestion) {
            if (selectedValue === condition.condition_value) {
                childQuestion.style.display = 'block';
                console.log(`Showing conditional question ${condition.question_id}`);
            } else {
                childQuestion.style.display = 'none';
                // Clear answers when hiding
                const inputs = childQuestion.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
                console.log(`Hiding conditional question ${condition.question_id}`);
            }
        }
    });
}

function initializeLocationQuestions() {
    const locationQuestions = document.querySelectorAll('[data-question-type="location"]');
    
    locationQuestions.forEach(questionContainer => {
        const questionId = questionContainer.querySelector('.province-select').dataset.questionId;
        const provinceSelect = questionContainer.querySelector('.province-select');
        const citySelect = questionContainer.querySelector('.city-select');
        
        if (provinceSelect && citySelect) {
            // Load provinces
            loadProvinces(provinceSelect);
            
            // Handle province change
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                const provinceText = this.options[this.selectedIndex].text;
                
                // Update hidden field
                const provinceNameInput = questionContainer.querySelector(`input[name="question_${questionId}_province_name"]`);
                if (provinceNameInput) {
                    provinceNameInput.value = provinceText !== '-- Pilih Provinsi --' ? provinceText : '';
                }
                
                // Load cities
                if (provinceId) {
                    loadCities(provinceId, citySelect, questionId, questionContainer);
                } else {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    citySelect.disabled = true;
                }
            });
            
            // Handle city change
            citySelect.addEventListener('change', function() {
                const cityText = this.options[this.selectedIndex].text;
                const cityNameInput = questionContainer.querySelector(`input[name="question_${questionId}_city_name"]`);
                if (cityNameInput) {
                    cityNameInput.value = cityText !== '-- Pilih Kota/Kabupaten --' ? cityText : '';
                }
                
                // Update location display
                const selectedLocationDiv = questionContainer.querySelector(`#selected-location-${questionId}`);
                const locationText = questionContainer.querySelector(`#location-text-${questionId}`);
                
                if (this.value && provinceSelect.value) {
                    const provinceName = provinceNameInput.value;
                    const displayText = `${provinceName}, ${cityText}`;
                    
                    if (locationText) {
                        locationText.textContent = displayText;
                    }
                    if (selectedLocationDiv) {
                        selectedLocationDiv.classList.remove('hidden');
                    }
                } else {
                    if (selectedLocationDiv) {
                        selectedLocationDiv.classList.add('hidden');
                    }
                }
            });
        }
    });
}

async function loadProvinces(provinceSelect) {
    try {
        const response = await fetch(window.questionnaireData.routes.provinces);
        const result = await response.json();
        
        if (result.success) {
            provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            result.data.forEach(province => {
                provinceSelect.innerHTML += `<option value="${province.id}">${province.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
    }
}

async function loadCities(provinceId, citySelect, questionId, questionContainer) {
    try {
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;
        
        const url = window.questionnaireData.routes.cities.replace(':provinceId', provinceId);
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
            result.data.forEach(city => {
                citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
            });
            citySelect.disabled = false;
        }
    } catch (error) {
        console.error('Error loading cities:', error);
        citySelect.innerHTML = '<option value="">Error loading cities</option>';
    }
}

// ✅ TAMBAHAN: Function untuk mengecek jawaban yang hilang
function isQuestionAnswered(questionId, questionType, questionContainer) {
    console.log(`Checking if question ${questionId} (${questionType}) is answered...`);
    
    switch (questionType) {
        case 'text':
        case 'date':
        case 'numeric':
            const textInput = questionContainer.querySelector(`input[name="question_${questionId}"], textarea[name="question_${questionId}"]`);
            const result = textInput && textInput.value.trim() !== '';
            console.log(`Text question ${questionId}: input found=${!!textInput}, value="${textInput?.value}", result=${result}`);
            return result;
            
        case 'email':
            const emailInput = questionContainer.querySelector(`input[name="question_${questionId}"]`);
            if (emailInput && emailInput.value.trim() !== '') {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                const isValidEmail = emailRegex.test(emailInput.value.trim());
                console.log(`Email question ${questionId}: input found=${!!emailInput}, value="${emailInput?.value}", isValid=${isValidEmail}`);
                return isValidEmail;
            }
            console.log(`Email question ${questionId}: input not found or empty`);
            return false;
            
        case 'option':
        case 'rating':
        case 'scale':
            const radioInput = questionContainer.querySelector(`input[name="question_${questionId}"]:checked`);
            const radioResult = radioInput !== null;
            console.log(`Radio question ${questionId}: checked input found=${!!radioInput}, result=${radioResult}`);
            return radioResult;
            
        case 'multiple':
            const checkboxInputs = questionContainer.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
            const checkboxResult = checkboxInputs.length > 0;
            console.log(`Checkbox question ${questionId}: checked count=${checkboxInputs.length}, result=${checkboxResult}`);
            return checkboxResult;
            
        case 'location':
            const provinceSelect = questionContainer.querySelector(`select[name="question_${questionId}_province"]`);
            const citySelect = questionContainer.querySelector(`select[name="question_${questionId}_city"]`);
            const locationResult = provinceSelect && citySelect && provinceSelect.value && citySelect.value;
            console.log(`Location question ${questionId}: province="${provinceSelect?.value}", city="${citySelect?.value}", result=${locationResult}`);
            return locationResult;
            
        default:
            console.warn(`Unknown question type for question ${questionId}: ${questionType}`);
            return false;
    }
}

// ✅ PERBAIKAN: Update validateRequired untuk semua pertanyaan jadi wajib + validasi email
function validateRequired() {
    console.log('🔍 Starting validation check...');
    
    // ✅ UBAH: Ambil semua pertanyaan yang visible, bukan hanya yang data-required="true"
    const allQuestions = document.querySelectorAll('.question-container');
    let isValid = true;
    let firstInvalidQuestion = null;
    const validationErrors = [];
    
    console.log(`Found ${allQuestions.length} questions to validate`);
    
    // Jika tidak ada pertanyaan, langsung return true
    if (allQuestions.length === 0) {
        console.log('No questions found, validation passed');
        return true;
    }
    
    allQuestions.forEach((questionContainer, index) => {
        const questionId = questionContainer.dataset.questionId;
        const questionType = questionContainer.dataset.questionType;
        const questionElement = questionContainer.querySelector('h3');
        const questionText = questionElement ? questionElement.textContent.replace('*', '').trim() : `Pertanyaan ${index + 1}`;
        
        // ✅ PENTING: Skip validation untuk pertanyaan conditional yang hidden
        if (questionContainer.style.display === 'none' || questionContainer.classList.contains('hidden')) {
            console.log(`Skipping validation for hidden conditional question ${questionId}`);
            return;
        }
        
        // ✅ Skip juga jika questionContainer secara programmatically hidden
        const computedStyle = window.getComputedStyle(questionContainer);
        if (computedStyle.display === 'none') {
            console.log(`Skipping validation for computed hidden question ${questionId}`);
            return;
        }
        
        console.log(`Validating question ${questionId} (${questionType}): ${questionText}`);
        
        // Detailed validation based on question type
        let isAnswered = false;
        let validationMessage = 'Pertanyaan ini wajib diisi!';
        
        switch (questionType) {
            case 'text':
            case 'date':
            case 'numeric':
                const textInput = questionContainer.querySelector(`input[name="question_${questionId}"], textarea[name="question_${questionId}"]`);
                isAnswered = textInput && textInput.value.trim() !== '';
                console.log(`Text/Date/Numeric validation: input found=${!!textInput}, value="${textInput?.value}", isAnswered=${isAnswered}`);
                break;
                
            case 'email':
                const emailInput = questionContainer.querySelector(`input[name="question_${questionId}"]`);
                if (emailInput && emailInput.value.trim() !== '') {
                    // Email format validation
                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    const isValidEmail = emailRegex.test(emailInput.value.trim());
                    
                    if (!isValidEmail) {
                        isAnswered = false;
                        validationMessage = 'Format email tidak valid! Contoh: user@example.com';
                    } else {
                        isAnswered = true;
                    }
                    
                    console.log(`Email validation: value="${emailInput.value}", isValidFormat=${isValidEmail}, isAnswered=${isAnswered}`);
                } else {
                    isAnswered = false;
                    console.log(`Email validation: input found=${!!emailInput}, value="${emailInput?.value}", isAnswered=${isAnswered}`);
                }
                break;
                
            case 'option':
            case 'rating':
            case 'scale':
                const radioInput = questionContainer.querySelector(`input[name="question_${questionId}"]:checked`);
                isAnswered = radioInput !== null;
                console.log(`Radio validation: checked input found=${!!radioInput}, isAnswered=${isAnswered}`);
                break;
                
            case 'multiple':
                const checkboxInputs = questionContainer.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
                isAnswered = checkboxInputs.length > 0;
                console.log(`Checkbox validation: checked inputs count=${checkboxInputs.length}, isAnswered=${isAnswered}`);
                break;
                
            case 'location':
                const provinceSelect = questionContainer.querySelector(`select[name="question_${questionId}_province"]`);
                const citySelect = questionContainer.querySelector(`select[name="question_${questionId}_city"]`);
                isAnswered = provinceSelect && citySelect && provinceSelect.value && citySelect.value;
                console.log(`Location validation: province="${provinceSelect?.value}", city="${citySelect?.value}", isAnswered=${isAnswered}`);
                break;
                
            default:
                console.warn(`Unknown question type: ${questionType}`);
                isAnswered = false;
        }
        
        if (!isAnswered) {
            console.log(`❌ Question ${questionId} is not answered or invalid`);
            isValid = false;
            
            // Add visual error indicators
            questionContainer.classList.add('border-red-300');
            questionContainer.style.borderColor = '#fca5a5';
            questionContainer.style.backgroundColor = '#fef2f2';
            
            // Show validation message
            let validationMsg = questionContainer.querySelector('.validation-message');
            if (!validationMsg) {
                // Create validation message if it doesn't exist
                validationMsg = document.createElement('div');
                validationMsg.className = 'validation-message text-red-600 text-sm mt-2';
                questionContainer.appendChild(validationMsg);
            }
            validationMsg.textContent = validationMessage;
            validationMsg.classList.remove('hidden');
            validationMsg.style.display = 'block';
            
            // Store first invalid question for scrolling
            if (!firstInvalidQuestion) {
                firstInvalidQuestion = questionContainer;
            }
            
            validationErrors.push(questionText);
        } else {
            console.log(`✅ Question ${questionId} is answered and valid`);
            
            // Remove error indicators if present
            questionContainer.classList.remove('border-red-300');
            questionContainer.style.borderColor = '';
            questionContainer.style.backgroundColor = '';
            const validationMsg = questionContainer.querySelector('.validation-message');
            if (validationMsg) {
                validationMsg.classList.add('hidden');
                validationMsg.style.display = 'none';
            }
        }
    });
    
    if (!isValid) {
        console.log(`❌ Validation failed. ${validationErrors.length} questions not answered or invalid:`, validationErrors);
        
        // Show validation alert
        showValidationAlert(`Ada ${validationErrors.length} pertanyaan yang belum diisi atau tidak valid!`);
        
        // Scroll to first invalid question
        if (firstInvalidQuestion) {
            firstInvalidQuestion.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Focus on first input in the question
            const firstInput = firstInvalidQuestion.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => {
                    firstInput.focus();
                    // Add pulse effect to highlight the field
                    firstInput.style.animation = 'pulse 1s infinite';
                    setTimeout(() => {
                        firstInput.style.animation = '';
                    }, 3000);
                }, 500);
            }
        }
        
        // Add shake effect to all invalid questions
        allQuestions.forEach(questionContainer => {
            if (questionContainer.classList.contains('border-red-300')) {
                questionContainer.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    questionContainer.style.animation = '';
                }, 500);
            }
        });
        
    } else {
        console.log('✅ All visible questions are answered and valid');
        hideValidationAlert();
    }
    
    return isValid;
}

// ✅ TAMBAHAN: Update isQuestionAnswered untuk validasi email
function isQuestionAnswered(questionId, questionType, questionContainer) {
    console.log(`Checking if question ${questionId} (${questionType}) is answered...`);
    
    switch (questionType) {
        case 'text':
        case 'date':
        case 'numeric':
            const textInput = questionContainer.querySelector(`input[name="question_${questionId}"], textarea[name="question_${questionId}"]`);
            const result = textInput && textInput.value.trim() !== '';
            console.log(`Text question ${questionId}: input found=${!!textInput}, value="${textInput?.value}", result=${result}`);
            return result;
            
        case 'email':
            const emailInput = questionContainer.querySelector(`input[name="question_${questionId}"]`);
            if (emailInput && emailInput.value.trim() !== '') {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                const isValidEmail = emailRegex.test(emailInput.value.trim());
                console.log(`Email question ${questionId}: input found=${!!emailInput}, value="${emailInput?.value}", isValid=${isValidEmail}`);
                return isValidEmail;
            }
            console.log(`Email question ${questionId}: input not found or empty`);
            return false;
            
        case 'option':
        case 'rating':
        case 'scale':
            const radioInput = questionContainer.querySelector(`input[name="question_${questionId}"]:checked`);
            const radioResult = radioInput !== null;
            console.log(`Radio question ${questionId}: checked input found=${!!radioInput}, result=${radioResult}`);
            return radioResult;
            
        case 'multiple':
            const checkboxInputs = questionContainer.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
            const checkboxResult = checkboxInputs.length > 0;
            console.log(`Checkbox question ${questionId}: checked count=${checkboxInputs.length}, result=${checkboxResult}`);
            return checkboxResult;
            
        case 'location':
            const provinceSelect = questionContainer.querySelector(`select[name="question_${questionId}_province"]`);
            const citySelect = questionContainer.querySelector(`select[name="question_${questionId}_city"]`);
            const locationResult = provinceSelect && citySelect && provinceSelect.value && citySelect.value;
            console.log(`Location question ${questionId}: province="${provinceSelect?.value}", city="${citySelect?.value}", result=${locationResult}`);
            return locationResult;
            
        default:
            console.warn(`Unknown question type for question ${questionId}: ${questionType}`);
            return false;
    }
}

// ✅ TAMBAHAN: Real-time email validation
function initializeFormValidation() {
    const form = document.getElementById('questionnaire-form');
    const nextButton = document.getElementById('next-category-btn');
    const submitButton = document.getElementById('submit-final-btn');
    
    if (nextButton) {
        nextButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            e.stopPropagation(); // Stop event bubbling
            
            console.log('🔄 Next category button clicked - starting validation...');
            
            // Add loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memvalidasi...';
            
            // Run validation immediately
            const validationResult = validateRequired();
            console.log(`Validation result: ${validationResult}`);
            
            if (validationResult) {
                console.log('✅ Validation passed, submitting form...');
                
                // Create and append action input
                let actionInput = form.querySelector('input[name="action"]');
                if (actionInput) {
                    actionInput.remove();
                }
                
                actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'next_category';
                form.appendChild(actionInput);
                
                // Submit the form
                form.submit();
            } else {
                console.log('❌ Validation failed, staying on current page');
                
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Show error feedback
                this.classList.add('shake');
                setTimeout(() => {
                    this.classList.remove('shake');
                }, 600);
            }
        });
    }
    
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            e.stopPropagation(); // Stop event bubbling
            
            console.log('🔄 Final submit button clicked - starting validation...');
            
            // Add loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memvalidasi...';
            
            // Run validation immediately
            const validationResult = validateRequired();
            console.log(`Validation result: ${validationResult}`);
            
            if (validationResult) {
                console.log('✅ Validation passed, showing confirmation modal...');
                
                // Restore button state before showing modal
                this.disabled = false;
                this.innerHTML = originalText;
                
                showConfirmationModal();
            } else {
                console.log('❌ Validation failed, staying on current page');
                
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Show error feedback
                this.classList.add('shake');
                setTimeout(() => {
                    this.classList.remove('shake');
                }, 600);
            }
        });
    }
    
    // Prevent form submission via Enter key on required fields
    form.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const target = e.target;
            const isButton = target.type === 'button' || target.type === 'submit';
            const isTextarea = target.tagName === 'TEXTAREA';
            
            // Only allow Enter in textareas and buttons
            if (!isTextarea && !isButton) {
                e.preventDefault();
                
                // Move focus to next input field
                const formElements = Array.from(form.querySelectorAll('input, select, textarea, button'));
                const currentIndex = formElements.indexOf(target);
                const nextElement = formElements[currentIndex + 1];
                
                if (nextElement) {
                    nextElement.focus();
                }
            }
        }
    });
    
    // ✅ UBAH: Add real-time validation feedback untuk SEMUA pertanyaan
    const allQuestions = document.querySelectorAll('.question-container');
    
    allQuestions.forEach(questionContainer => {
        const inputs = questionContainer.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Real-time email validation
            if (input.type === 'email' || questionContainer.dataset.questionType === 'email') {
                input.addEventListener('input', function() {
                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    const validationMsg = questionContainer.querySelector('.validation-message');
                    
                    if (this.value.trim() !== '' && !emailRegex.test(this.value.trim())) {
                        // Show email format error
                        questionContainer.classList.add('border-red-300');
                        questionContainer.style.borderColor = '#fca5a5';
                        questionContainer.style.backgroundColor = '#fef2f2';
                        
                        if (!validationMsg) {
                            const newValidationMsg = document.createElement('div');
                            newValidationMsg.className = 'validation-message text-red-600 text-sm mt-2';
                            questionContainer.appendChild(newValidationMsg);
                        }
                        const currentValidationMsg = questionContainer.querySelector('.validation-message');
                        currentValidationMsg.textContent = 'Format email tidak valid! Contoh: user@example.com';
                        currentValidationMsg.classList.remove('hidden');
                        currentValidationMsg.style.display = 'block';
                    } else if (this.value.trim() !== '' && emailRegex.test(this.value.trim())) {
                        // Valid email format
                        questionContainer.classList.remove('border-red-300');
                        questionContainer.style.borderColor = '';
                        questionContainer.style.backgroundColor = '';
                        if (validationMsg) {
                            validationMsg.classList.add('hidden');
                            validationMsg.style.display = 'none';
                        }
                        
                        // Show success indicator temporarily
                        questionContainer.classList.add('border-green-300', 'bg-green-50');
                        setTimeout(() => {
                            questionContainer.classList.remove('border-green-300', 'bg-green-50');
                        }, 1000);
                    }
                });
            }
            
            input.addEventListener('change', function() {
                // Clear validation error when user starts answering (for non-email fields)
                if (input.type !== 'email' && questionContainer.dataset.questionType !== 'email') {
                    questionContainer.classList.remove('border-red-300');
                    questionContainer.style.borderColor = '';
                    questionContainer.style.backgroundColor = '';
                    const validationMsg = questionContainer.querySelector('.validation-message');
                    if (validationMsg) {
                        validationMsg.classList.add('hidden');
                        validationMsg.style.display = 'none';
                    }
                    
                    // Add success indicator if question is now answered
                    const questionId = questionContainer.dataset.questionId;
                    const questionType = questionContainer.dataset.questionType;
                    
                    if (isQuestionAnswered(questionId, questionType, questionContainer)) {
                        questionContainer.classList.add('border-green-300', 'bg-green-50');
                        setTimeout(() => {
                            questionContainer.classList.remove('border-green-300', 'bg-green-50');
                        }, 1000);
                    }
                }
            });
        });
    });
    
    console.log('✅ Form validation initialized');
}

// ✅ TAMBAHAN: Function untuk menampilkan alert validasi
function showValidationAlert(message) {
    const alertElement = document.getElementById('validation-alert');
    const messageElement = document.getElementById('validation-message');
    
    if (alertElement && messageElement) {
        messageElement.textContent = message;
        alertElement.classList.remove('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            hideValidationAlert();
        }, 5000);
    }
}

// ✅ TAMBAHAN: Function untuk menyembunyikan alert validasi
function hideValidationAlert() {
    const alertElement = document.getElementById('validation-alert');
    if (alertElement) {
        alertElement.classList.add('hidden');
    }
}

// ✅ TAMBAHAN: Function untuk inisialisasi modal konfirmasi
function initializeConfirmationModal() {
    console.log('🔧 Initializing confirmation modal...');
    
    const modal = document.getElementById('confirmation-modal');
    const cancelButton = document.getElementById('cancel-submit');
    const confirmButton = document.getElementById('confirm-submit');
    
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            hideConfirmationModal();
        });
    }
    
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            console.log('User confirmed final submission');
            
            const form = document.getElementById('questionnaire-form');
            
            // Create and append action input
            let actionInput = form.querySelector('input[name="action"]');
            if (actionInput) {
                actionInput.remove();
            }
            
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'submit_final';
            form.appendChild(actionInput);
            
            // Hide modal and submit form
            hideConfirmationModal();
            form.submit();
        });
    }
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideConfirmationModal();
            }
        });
    }
    
    console.log('✅ Confirmation modal initialized');
}

// ✅ TAMBAHAN: Function untuk menampilkan modal konfirmasi
function showConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // Focus on cancel button for accessibility
        const cancelButton = document.getElementById('cancel-submit');
        if (cancelButton) {
            setTimeout(() => cancelButton.focus(), 100);
        }
    }
}

// ✅ TAMBAHAN: Function untuk menyembunyikan modal konfirmasi
function hideConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>

@endsection
