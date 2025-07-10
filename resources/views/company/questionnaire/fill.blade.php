@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner Employee'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Breadcrumb - Responsive -->
            <nav class="mb-4 sm:mb-6">
                <ol class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm overflow-x-auto">
                    <li>
                        <a href="{{ route('dashboard.company') }}" class="text-blue-600 hover:underline whitespace-nowrap">Dashboard</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline whitespace-nowrap">Kuesioner</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" class="text-blue-600 hover:underline whitespace-nowrap">Pilih Alumni</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700 whitespace-nowrap">Pengisian</li>
                </ol>
            </nav>

            <!-- Alumni Info Card - Responsive -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-graduate text-white text-lg sm:text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <h3 class="text-base sm:text-lg font-semibold text-blue-900">{{ $alumni->name }}</h3>
                            <p class="text-sm sm:text-base text-blue-700">{{ $alumni->nim }}</p>
                            @if($alumni->graduation_year)
                                <p class="text-xs sm:text-sm text-blue-600">Tahun Lulus: {{ $alumni->graduation_year }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-sm font-medium text-blue-900">Periode</div>
                        <div class="text-sm sm:text-base text-blue-700">{{ $periode->title }}</div>
                        <div class="text-xs text-blue-600">
                            {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar - Responsive -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 space-y-2 sm:space-y-0">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-700">Progress Pengisian</h2>
                    <span class="text-xs sm:text-sm font-medium text-gray-600">{{ $progressPercentage }}% Selesai</span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-2 sm:h-3 mb-3 sm:mb-4">
                    <div class="bg-blue-600 h-2 sm:h-3 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:justify-between text-xs sm:text-sm text-gray-600 space-y-1 sm:space-y-0">
                    <span>Kategori {{ $currentCategoryIndex + 1 }} dari {{ $totalCategories }}</span>
                    <span class="font-medium">{{ $currentCategory->category_name }}</span>
                </div>
            </div>

            <!-- Category Navigation - Responsive -->
            @if($allCategories->count() > 1)
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-3 sm:mb-4">Kategori Kuesioner</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                        @foreach($allCategories as $index => $category)
                            <div class="px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200 text-center
                                      {{ $category->id_category == $currentCategory->id_category 
                                         ? 'bg-blue-600 text-white' 
                                         : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <span class="block truncate">{{ $index + 1 }}. {{ $category->category_name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Main Form -->
            <form method="POST" action="{{ route('company.questionnaire.submit', [$periode->id_periode, $nim]) }}" 
                  id="questionnaire-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_category" value="{{ $currentCategory->id_category }}">
                
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                            {{ $currentCategory->category_name }}
                        </h2>
                        @if($currentCategory->description)
                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 mb-1">Deskripsi Kategori</p>
                                        <p class="text-sm text-blue-700 leading-relaxed">{{ $currentCategory->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($questions->isEmpty())
                        <div class="text-center py-6 sm:py-8">
                            <i class="fas fa-question-circle text-gray-400 text-3xl sm:text-4xl mb-3 sm:mb-4"></i>
                            <p class="text-sm sm:text-base text-gray-600">Tidak ada pertanyaan untuk kategori ini.</p>
                        </div>
                    @else
                        @foreach($questions as $index => $question)
                            <div class="question-container border border-gray-200 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6 
                                @if($question->depends_on) conditional-question @endif" 
                                data-question-id="{{ $question->id_question }}"
                                data-question-type="{{ $question->type }}"
                                data-required="{{ $question->is_required ? 'true' : 'false' }}"
                                @if($question->depends_on)
                                    data-depends-on="{{ $question->depends_on }}"
                                    data-depends-value="{{ $question->depends_value }}"
                                    style="display: none;"
                                @endif>
                                
                                <div class="flex flex-col sm:flex-row sm:items-start">
                                    <span class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs sm:text-sm font-medium mb-3 sm:mb-0 sm:mr-4 self-center sm:self-start">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-grow w-full">
                                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">
                                            {{ $question->question }}
                                            @if($question->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </h3>
                                        
                                        @if($question->description)
                                            <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4">{{ $question->description }}</p>
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
                                        <div class="validation-message text-red-600 text-xs sm:text-sm mt-2 hidden"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Navigation Buttons - Responsive -->
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                    <div class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:justify-between lg:items-center">
                        <!-- Left Side Buttons -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            @if($prevCategory)
                                <button type="submit" name="action" value="prev_category"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    <span class="hidden sm:inline">Kategori Sebelumnya</span>
                                    <span class="sm:hidden">Sebelumnya</span>
                                </button>
                            @endif
                            
                            <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                <i class="fas fa-arrow-left mr-2"></i>
                                <span class="hidden sm:inline">Kembali ke Pilih Alumni</span>
                                <span class="sm:hidden">Pilih Alumni</span>
                            </a>
                        </div>

                        <!-- Right Side Buttons -->
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <button type="submit" name="action" value="save_draft"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                <i class="fas fa-save mr-2"></i>
                                <span class="hidden sm:inline">Simpan Draft</span>
                                <span class="sm:hidden">Draft</span>
                            </button>

                            @if($nextCategory)
                                <button type="submit" name="action" value="next_category" id="next-category-btn"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                    <span class="hidden sm:inline">Kategori Selanjutnya</span>
                                    <span class="sm:hidden">Selanjutnya</span>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            @else
                                <button type="submit" name="action" value="submit_final" id="submit-final-btn"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                    <i class="fas fa-check mr-2"></i>
                                    <span class="hidden sm:inline">Selesaikan Kuesioner</span>
                                    <span class="sm:hidden">Selesaikan</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Enhanced Confirmation Modal - Responsive -->
            <div id="confirmation-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 hidden p-4">
                <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-green-100 mb-3 sm:mb-4">
                            <i class="fas fa-check text-green-600 text-lg sm:text-xl"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Konfirmasi Pengiriman</h3>
                        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                            Apakah Anda yakin ingin menyelesaikan kuesioner untuk alumni <strong>{{ $alumni->name }}</strong>? 
                            Setelah dikirim, Anda tidak dapat mengubah jawaban.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                            <button type="button" id="cancel-submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                Batal
                            </button>
                            <button type="button" id="confirm-submit"
                                    class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                Ya, Selesaikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Alert - Responsive -->
            <div id="validation-alert" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-lg z-50 hidden max-w-xs sm:max-w-md">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-sm sm:text-base"></i>
                    <span id="validation-message" class="text-xs sm:text-sm">Ada pertanyaan wajib yang belum diisi!</span>
                </div>
            </div>
        </div>
    </main>
</div>
<style>
/* Question Container Responsive Styles */
.question-container {
    transition: all 0.3s ease;
}

.question-container.border-red-300 {
    border-color: #fca5a5 !important;
    background-color: #fef2f2 !important;
    animation: pulse-red 2s infinite;
}

/* Validation Message Styles */
.validation-message {
    font-size: 0.875rem;
    font-weight: 500;
    transition: opacity 0.3s ease;
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

/* Shake animation for input errors */
@keyframes shake {
    0%, 20%, 40%, 60%, 80%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}

/* Enhanced Button Responsive Styles */
.btn-responsive {
    @apply inline-flex items-center justify-center;
    @apply px-4 sm:px-6 py-2 sm:py-3;
    @apply text-sm sm:text-base font-medium;
    @apply rounded-lg transition-colors duration-200;
    @apply w-full sm:w-auto;
}

/* Question Number Badge Responsive */
.question-number {
    @apply flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8;
    @apply bg-blue-100 text-blue-600 rounded-full;
    @apply flex items-center justify-center;
    @apply text-xs sm:text-sm font-medium;
    @apply mb-3 sm:mb-0 sm:mr-4;
    @apply self-center sm:self-start;
}

/* Progress Bar Responsive */
.progress-bar {
    @apply w-full bg-gray-200 rounded-full;
    @apply h-2 sm:h-3 mb-3 sm:mb-4;
}

.progress-fill {
    @apply bg-blue-600 rounded-full transition-all duration-300;
    @apply h-2 sm:h-3;
}

/* Form Input Responsive Styling */
.form-input {
    @apply w-full px-3 sm:px-4 py-2 sm:py-3;
    @apply border border-gray-300 rounded-md;
    @apply focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
    @apply text-sm sm:text-base;
    @apply transition-colors duration-200;
}

.form-input.error {
    @apply border-red-300 bg-red-50;
}

/* Checkbox and Radio Responsive */
.form-checkbox, .form-radio {
    @apply w-4 h-4 sm:w-5 sm:h-5;
    @apply text-blue-600 border-gray-300 rounded;
    @apply focus:ring-blue-500 focus:ring-2;
}

/* Location Dropdown Responsive */
.location-select {
    @apply w-full px-2 sm:px-3 py-2;
    @apply border border-gray-300 rounded-md;
    @apply focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
    @apply text-xs sm:text-sm;
    @apply transition-colors duration-200;
}

/* Card Responsive Styles */
.card-responsive {
    @apply bg-white rounded-xl shadow-md;
    @apply p-4 sm:p-6 mb-4 sm:mb-6;
}

.card-header {
    @apply border-b border-gray-200;
    @apply pb-3 sm:pb-4 mb-4 sm:mb-6;
}

/* Typography Responsive */
.heading-responsive {
    @apply text-xl sm:text-2xl font-bold text-gray-800;
}

.subheading-responsive {
    @apply text-base sm:text-lg font-semibold text-gray-700;
}

.body-text-responsive {
    @apply text-sm sm:text-base text-gray-600;
}

.small-text-responsive {
    @apply text-xs sm:text-sm text-gray-500;
}

/* Mobile Specific Optimizations */
@media (max-width: 640px) {
    .question-container {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .modal-content {
        margin: 1rem !important;
        padding: 1rem !important;
    }
    
    .navigation-buttons {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .navigation-buttons > div {
        width: 100% !important;
    }
    
    .navigation-buttons button,
    .navigation-buttons a {
        width: 100% !important;
        justify-content: center !important;
    }
}

/* Tablet Specific Optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
    .category-navigation {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .form-row {
        flex-direction: column !important;
        gap: 1rem !important;
    }
}

/* Desktop Optimizations */
@media (min-width: 1025px) {
    .question-container {
        padding: 1.5rem !important;
    }
    
    .navigation-buttons {
        flex-direction: row !important;
        justify-content: space-between !important;
    }
    
    .category-navigation {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .question-container {
        page-break-inside: avoid;
        border: 1px solid #ccc !important;
        margin-bottom: 1rem !important;
    }
    
    .card-responsive {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .question-container {
        border-width: 2px !important;
    }
    
    .form-input {
        border-width: 2px !important;
    }
    
    .btn-responsive {
        border: 2px solid currentColor !important;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .shake {
        animation: none !important;
    }
    
    .pulse-red {
        animation: none !important;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    window.questionnaireData = {
        conditionalQuestions: @json($conditionalQuestions ?? []),
        periode: @json($periode),
        alumni: @json($alumni),
        nim: "{{ $nim }}",
        currentCategory: @json($currentCategory),
        totalCategories: {{ $totalCategories }},
        currentCategoryIndex: {{ $currentCategoryIndex }},
        routes: {
            provinces: '/api/location/provinces', // ✅ Use correct API route
            cities: '/api/location/cities/:provinceId' // ✅ Use correct API route with parameter
        }
    };
    
    
    initializeQuestionnaire();
});

function initializeQuestionnaire() {
    
    // Initialize conditional questions
    initializeConditionalQuestions();
    
    // Initialize location dropdowns
    initializeLocationQuestions();
    
    initializeOptionToggles();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize confirmation modal
    initializeConfirmationModal();
    
}

function initializeOptionToggles() {
    console.log('=== Initializing option toggles ===');
    
    // ✅ STEP 1: Jangan clear value yang sudah ada - hanya hide fields yang tidak diperlukan
    document.querySelectorAll('[id^="other_field_"], [id^="multiple_other_field_"]').forEach(field => {
        // Hanya hide field, jangan clear value dulu
        field.classList.add('hidden');
        console.log('Initially hiding other field:', field.id);
    });
    
    // ✅ STEP 2: Single Option (Radio) - Other field toggle
    document.querySelectorAll('.option-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.getAttribute('data-question-id');
            const isOther = this.getAttribute('data-is-other') === '1';
            const optionId = this.value;
            
            console.log('=== Radio changed ===', {
                questionId: questionId,
                isOther: isOther,
                optionId: optionId,
                checked: this.checked
            });
            
            // Hide all other fields for this question (tapi jangan clear value)
            document.querySelectorAll(`[id^="other_field_${questionId}_"]`).forEach(field => {
                field.classList.add('hidden');
                console.log('Hiding other field:', field.id);
                
                // ✅ PERBAIKAN: Hanya clear value jika field yang berbeda
                const fieldOptionId = field.id.split('_').pop();
                if (fieldOptionId !== optionId) {
                    const otherInput = field.querySelector('input[type="text"]');
                    if (otherInput) {
                        otherInput.value = '';
                    }
                }
            });
            
            // Show other field if this is "other" option and checked
            if (isOther && this.checked) {
                const otherField = document.getElementById(`other_field_${questionId}_${optionId}`);
                console.log('Looking for other field:', `other_field_${questionId}_${optionId}`);
                
                if (otherField) {
                    otherField.classList.remove('hidden');
                    console.log('✅ Showing other field:', otherField.id);
                    
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput && !otherInput.value) {
                        // Hanya focus jika tidak ada value (untuk new input)
                        setTimeout(() => otherInput.focus(), 100);
                    }
                } else {
                    console.warn('❌ Other field not found:', `other_field_${questionId}_${optionId}`);
                }
            }
        });
    });
    
    // ✅ STEP 3: Multiple Choice (Checkbox) - Other field toggle
    document.querySelectorAll('.multiple-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const questionId = this.getAttribute('data-question-id');
            const isOther = this.getAttribute('data-is-other') === '1';
            const optionId = this.value;
            
            console.log('=== Checkbox changed ===', {
                questionId: questionId,
                isOther: isOther,
                optionId: optionId,
                checked: this.checked
            });
            
            if (isOther) {
                const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionId}`);
                console.log('Looking for multiple other field:', `multiple_other_field_${questionId}_${optionId}`);
                
                if (otherField) {
                    if (this.checked) {
                        otherField.classList.remove('hidden');
                        console.log('✅ Showing multiple other field:', otherField.id);
                        
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (otherInput && !otherInput.value) {
                            // Hanya focus jika tidak ada value (untuk new input)
                            setTimeout(() => otherInput.focus(), 100);
                        }
                    } else {
                        otherField.classList.add('hidden');
                        console.log('❌ Hiding multiple other field:', otherField.id);
                        
                        // Clear value when unchecked
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (otherInput) {
                            otherInput.value = '';
                        }
                    }
                } else {
                    console.warn('❌ Multiple other field not found:', `multiple_other_field_${questionId}_${optionId}`);
                }
            }
        });
    });
    
    // ✅ STEP 4: Enhanced - Handle existing selections dengan preserve value
    setTimeout(() => {
        console.log('=== Checking existing selections with value preservation ===');
        
        // Check existing radio selections
        document.querySelectorAll('.option-radio:checked').forEach(radio => {
            const isOther = radio.getAttribute('data-is-other') === '1';
            if (isOther) {
                const questionId = radio.getAttribute('data-question-id');
                const optionId = radio.value;
                const otherField = document.getElementById(`other_field_${questionId}_${optionId}`);
                
                console.log('Found existing radio selection:', {
                    questionId: questionId,
                    optionId: optionId,
                    isOther: isOther,
                    otherFieldExists: !!otherField
                });
                
                if (otherField) {
                    otherField.classList.remove('hidden');
                    console.log('✅ Initially showing other field for existing selection:', otherField.id);
                    
                    // ✅ Log existing value untuk debugging
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput) {
                        console.log('Other field existing value:', otherInput.value);
                    }
                }
            }
        });
        
        // Check existing checkbox selections
        document.querySelectorAll('.multiple-checkbox:checked').forEach(checkbox => {
            const isOther = checkbox.getAttribute('data-is-other') === '1';
            if (isOther) {
                const questionId = checkbox.getAttribute('data-question-id');
                const optionId = checkbox.value;
                const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionId}`);
                
                console.log('Found existing checkbox selection:', {
                    questionId: questionId,
                    optionId: optionId,
                    isOther: isOther,
                    otherFieldExists: !!otherField
                });
                
                if (otherField) {
                    otherField.classList.remove('hidden');
                    console.log('✅ Initially showing multiple other field for existing selection:', otherField.id);
                    
                    // ✅ Log existing value untuk debugging
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput) {
                        console.log('Multiple other field existing value:', otherInput.value);
                    }
                }
            }
        });
        
        console.log('=== End checking existing selections ===');
    }, 500);
    
    console.log('=== Option toggles initialization completed ===');
}
function initializeConditionalQuestions() {
    console.log('Initializing conditional questions for company questionnaire');
    
    // ✅ Updated untuk menggunakan data attributes yang benar
    document.querySelectorAll('.conditional-question, .question-container[data-depends-on]').forEach(questionContainer => {
        const parentQuestionId = questionContainer.getAttribute('data-depends-on');
        const conditionValue = questionContainer.getAttribute('data-depends-value');
        
        if (parentQuestionId && conditionValue) {
            console.log('Found conditional question', {
                questionId: questionContainer.getAttribute('data-question-id'),
                parentQuestionId: parentQuestionId,
                conditionValue: conditionValue
            });
            
            // Initially hide conditional questions
            questionContainer.style.display = 'none';
            
            // Monitor parent question changes
            const parentInputs = document.querySelectorAll(`[data-question-id="${parentQuestionId}"]`);
            console.log('Found parent inputs:', parentInputs.length);
            
            parentInputs.forEach(input => {
                console.log('Adding listener to parent input:', input);
                
                input.addEventListener('change', function() {
                    console.log('Parent input changed:', this.value);
                    handleConditionalQuestion(parentQuestionId, questionContainer, conditionValue);
                });
                
                // Check initial state
                if (input.checked || input.selected) {
                    console.log('Parent input initially checked/selected');
                    handleConditionalQuestion(parentQuestionId, questionContainer, conditionValue);
                }
            });
        }
    });
    
    // ✅ Initialize on page load for existing answers dengan delay lebih panjang
    setTimeout(() => {
        console.log('=== Checking initial conditional states... ===');
        
        // Check all currently checked/selected inputs
        document.querySelectorAll('input:checked, select option:selected').forEach(input => {
            const questionId = input.getAttribute('data-question-id') || 
                             input.closest('[data-question-id]')?.getAttribute('data-question-id');
            
            if (questionId) {
                console.log('Found checked input for question:', questionId, 'value:', input.value);
                const conditionalQuestions = document.querySelectorAll(`[data-depends-on="${questionId}"]`);
                console.log('Found conditional questions depending on', questionId, ':', conditionalQuestions.length);
                
                conditionalQuestions.forEach(conditionalQuestion => {
                    const conditionValue = conditionalQuestion.getAttribute('data-depends-value');
                    console.log('Processing conditional question with condition:', conditionValue);
                    handleConditionalQuestion(questionId, conditionalQuestion, conditionValue);
                });
            }
        });
        
        // ✅ TAMBAHAN: Manual trigger untuk memastikan
        document.querySelectorAll('.conditional-question').forEach(conditional => {
            const parentId = conditional.getAttribute('data-depends-on');
            const conditionValue = conditional.getAttribute('data-depends-value');
            
            if (parentId && conditionValue) {
                console.log('Manual check for conditional question:', {
                    conditionalQuestionId: conditional.getAttribute('data-question-id'),
                    parentId: parentId,
                    conditionValue: conditionValue
                });
                
                handleConditionalQuestion(parentId, conditional, conditionValue);
            }
        });
        
        console.log('=== End initial conditional states check ===');
    }, 1000); // ✅ Increased delay untuk memastikan semua elements loaded
}

function handleConditionalQuestion(parentQuestionId, conditionalQuestionContainer, conditionValue) {
    console.log('=== handleConditionalQuestion START ===');
    console.log('Parent Question ID:', parentQuestionId);
    console.log('Condition Value:', conditionValue);
    console.log('Conditional Question Container:', conditionalQuestionContainer.getAttribute('data-question-id'));
    
    // Get all checked/selected values from parent question
    const parentValues = [];
    
    // ✅ Enhanced parent input detection
    const parentRadios = document.querySelectorAll(`input[type="radio"][data-question-id="${parentQuestionId}"]:checked`);
    const parentCheckboxes = document.querySelectorAll(`input[type="checkbox"][data-question-id="${parentQuestionId}"]:checked`);
    const parentSelects = document.querySelectorAll(`select[data-question-id="${parentQuestionId}"]`);
    
    console.log('Found parent radios checked:', parentRadios.length);
    console.log('Found parent checkboxes checked:', parentCheckboxes.length);
    console.log('Found parent selects:', parentSelects.length);
    
    // Collect values from all parent inputs
    parentRadios.forEach(radio => {
        parentValues.push(radio.value);
        console.log('Parent radio value:', radio.value);
    });
    
    parentCheckboxes.forEach(checkbox => {
        parentValues.push(checkbox.value);
        console.log('Parent checkbox value:', checkbox.value);
    });
    
    parentSelects.forEach(select => {
        if (select.value) {
            parentValues.push(select.value);
            console.log('Parent select value:', select.value);
        }
    });
    
    console.log('All parent values collected:', parentValues);
    
    // ✅ Enhanced multiple dependency values handling
    let shouldShow = false;
    
    if (conditionValue && conditionValue.includes(',')) {
        // Multiple dependency values - split by comma and check if any parentValue is in the list
        const conditionValueArray = conditionValue.split(',').map(val => val.trim());
        shouldShow = parentValues.some(val => conditionValueArray.includes(String(val).trim()));
        
        console.log('Multiple dependency check', {
            conditionValueArray: conditionValueArray,
            parentValues: parentValues,
            shouldShow: shouldShow
        });
    } else {
        // Single dependency value - direct comparison
        shouldShow = parentValues.some(val => String(val).trim() === String(conditionValue).trim());
        
        console.log('Single dependency check', {
            conditionValue: conditionValue,
            parentValues: parentValues,
            shouldShow: shouldShow
        });
    }
    
    console.log('Final decision - Should show:', shouldShow);
    
    if (shouldShow) {
        conditionalQuestionContainer.style.display = 'block';
        conditionalQuestionContainer.style.visibility = 'visible';
        
        // Enable all inputs in the conditional question
        const inputs = conditionalQuestionContainer.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = false;
        });
        
        console.log('✅ Conditional question SHOWN:', conditionalQuestionContainer.getAttribute('data-question-id'));
    } else {
        conditionalQuestionContainer.style.display = 'none';
        conditionalQuestionContainer.style.visibility = 'hidden';
        
        // Clear and disable all inputs in the conditional question
        const inputs = conditionalQuestionContainer.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = true;
            
            if (input.type === 'radio' || input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
            
            // Clear other option fields untuk hidden questions
            if (input.type === 'radio' && input.getAttribute('data-is-other') === '1') {
                const questionId = conditionalQuestionContainer.getAttribute('data-question-id');
                const otherField = document.getElementById(`other_field_${questionId}_${input.value}`);
                if (otherField) {
                    otherField.classList.add('hidden');
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput) {
                        otherInput.value = '';
                    }
                }
            }
            
            if (input.type === 'checkbox' && input.getAttribute('data-is-other') === '1') {
                const questionId = conditionalQuestionContainer.getAttribute('data-question-id');
                const otherField = document.getElementById(`multiple_other_field_${questionId}_${input.value}`);
                if (otherField) {
                    otherField.classList.add('hidden');
                    const otherInput = otherField.querySelector('input[type="text"]');
                    if (otherInput) {
                        otherInput.value = '';
                    }
                }
            }
        });
        
        console.log('❌ Conditional question HIDDEN:', conditionalQuestionContainer.getAttribute('data-question-id'));
    }
    
    console.log('=== handleConditionalQuestion END ===');
}

// ✅ Enhanced validation function untuk multiple dependencies
function validateRequired() {
    console.log('Validating form with enhanced dependency support');
    
    const allQuestions = document.querySelectorAll('.question-container');
    let isValid = true;
    let firstInvalidQuestion = null;
    const validationErrors = [];
    
    if (allQuestions.length === 0) {
        return true;
    }
    
    allQuestions.forEach((questionContainer, index) => {
        const questionId = questionContainer.dataset.questionId;
        const questionType = questionContainer.dataset.questionType;
        const questionElement = questionContainer.querySelector('h3');
        const questionText = questionElement ? questionElement.textContent.replace('*', '').trim() : `Pertanyaan ${index + 1}`;
        
        // ✅ PERBAIKAN: Gunakan data attributes yang benar
        const parentQuestionId = questionContainer.getAttribute('data-depends-on'); // ✅ Bukan data-parent-question
        const conditionValue = questionContainer.getAttribute('data-depends-value'); // ✅ Bukan data-condition-value
        
        // Skip validation untuk pertanyaan conditional yang hidden
        if (questionContainer.style.display === 'none' || questionContainer.classList.contains('hidden')) {
            console.log('Skipping validation - question hidden:', questionId);
            return;
        }
        
        const computedStyle = window.getComputedStyle(questionContainer);
        if (computedStyle.display === 'none') {
            console.log('Skipping validation - question computed style none:', questionId);
            return;
        }
        
        // ✅ Additional check for conditional questions
        if (parentQuestionId && conditionValue) {
            // Check if parent question conditions are met
            const parentValues = [];
            
            const parentInputs = document.querySelectorAll(`[data-question-id="${parentQuestionId}"]:checked`);
            parentInputs.forEach(input => {
                parentValues.push(input.value);
            });
            
            const parentSelects = document.querySelectorAll(`select[data-question-id="${parentQuestionId}"]`);
            parentSelects.forEach(select => {
                if (select.value) {
                    parentValues.push(select.value);
                }
            });
            
            let conditionMet = false;
            
            if (conditionValue.includes(',')) {
                // Multiple dependency values
                const conditionValueArray = conditionValue.split(',').map(val => val.trim());
                conditionMet = parentValues.some(val => conditionValueArray.includes(String(val).trim()));
            } else {
                // Single dependency value
                conditionMet = parentValues.some(val => String(val).trim() === String(conditionValue).trim());
            }
            
            console.log('Conditional validation check', {
                questionId: questionId,
                parentQuestionId: parentQuestionId,
                conditionValue: conditionValue,
                parentValues: parentValues,
                conditionMet: conditionMet
            });
            
            // Skip validation if condition is not met
            if (!conditionMet) {
                console.log('Skipping validation - condition not met for question:', questionId);
                return;
            }
        }
        
        // Rest of validation logic...
        let isAnswered = false;
        let validationMessage = 'Pertanyaan ini wajib diisi!';
        
        switch (questionType) {
            case 'text':
            case 'date':
            case 'numeric':
                const textInput = questionContainer.querySelector(`input[name="question_${questionId}"], textarea[name="question_${questionId}"]`);
                isAnswered = textInput && textInput.value.trim() !== '';
                break;
                
            case 'email':
                const emailInput = questionContainer.querySelector(`input[name="question_${questionId}"]`);
                if (emailInput && emailInput.value.trim() !== '') {
                    const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
                    isAnswered = emailRegex.test(emailInput.value.trim());
                    if (!isAnswered) {
                        validationMessage = 'Format email tidak valid!';
                    }
                }
                break;
                
            case 'option':
            case 'rating':
            case 'scale':
                const radioInput = questionContainer.querySelector(`input[name="question_${questionId}"]:checked`);
                isAnswered = radioInput !== null;
                
                // Check if "other" option is selected and requires text input
                if (isAnswered && radioInput.getAttribute('data-is-other') === '1') {
                    const otherField = questionContainer.querySelector(`#other_field_${questionId}_${radioInput.value}`);
                    if (otherField && !otherField.classList.contains('hidden')) {
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (!otherInput || otherInput.value.trim() === '') {
                            isAnswered = false;
                            validationMessage = 'Jawaban "Lainnya" harus diisi';
                        }
                    }
                }
                break;
                
            case 'multiple':
                const checkboxInputs = questionContainer.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
                isAnswered = checkboxInputs.length > 0;
                
                // Check if any "other" options require additional text
                if (isAnswered) {
                    let allOtherFieldsValid = true;
                    
                    checkboxInputs.forEach(checkbox => {
                        if (checkbox.getAttribute('data-is-other') === '1') {
                            const optionId = checkbox.value;
                            const otherField = questionContainer.querySelector(`#multiple_other_field_${questionId}_${optionId}`);
                            
                            if (otherField && !otherField.classList.contains('hidden')) {
                                const otherInput = otherField.querySelector('input[type="text"]');
                                if (!otherInput || otherInput.value.trim() === '') {
                                    allOtherFieldsValid = false;
                                    validationMessage = 'Isian "Lainnya" harus diisi';
                                }
                            }
                        }
                    });
                    
                    isAnswered = allOtherFieldsValid;
                }
                break;
                
            case 'location':
                const locationCombinedInput = questionContainer.querySelector(`input[name="location_combined[${questionId}]"]`);
                if (locationCombinedInput && locationCombinedInput.value) {
                    try {
                        const locationData = JSON.parse(locationCombinedInput.value);
                        isAnswered = locationData && locationData.country && locationData.country.code && 
                                   locationData.state && locationData.state.name && 
                                   locationData.city && locationData.city.name;
                    } catch (e) {
                        isAnswered = false;
                    }
                }
                validationMessage = 'Lokasi harus dipilih lengkap (negara, provinsi/state, dan kota)!';
                break;
                
            default:
                console.warn(`Unknown question type: ${questionType}`);
                isAnswered = false;
                break;
        }
        
        // Validation result processing
        if (!isAnswered) {
            isValid = false;
            validationErrors.push(questionText);
            
            // Visual feedback
            questionContainer.classList.add('border-red-300');
            const validationMsg = questionContainer.querySelector('.validation-message');
            if (validationMsg) {
                validationMsg.textContent = validationMessage;
                validationMsg.classList.remove('hidden');
            }
            
            if (!firstInvalidQuestion) {
                firstInvalidQuestion = questionContainer;
            }
        } else {
            // Clear validation styles
            questionContainer.classList.remove('border-red-300');
            const validationMsg = questionContainer.querySelector('.validation-message');
            if (validationMsg) {
                validationMsg.classList.add('hidden');
            }
        }
    });
    
    if (!isValid) {
        console.log('Form validation failed', {
            errorCount: validationErrors.length,
            errors: validationErrors
        });
        
        showValidationAlert(`Ada ${validationErrors.length} pertanyaan yang belum diisi atau tidak valid!`);
        
        if (firstInvalidQuestion) {
            firstInvalidQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    } else {
        console.log('Form validation passed');
        hideValidationAlert();
    }
    
    return isValid;
}

// Enhanced form validation initialization
function initializeFormValidation() {
    console.log('Initializing enhanced form validation for company questionnaire');
    
    const form = document.getElementById('questionnaire-form');
    if (!form) {
        console.error('Questionnaire form not found');
        return;
    }
    
    // Add validation to form submission
    form.addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        const action = submitButton ? submitButton.value : '';
        
        console.log('Form submission attempt', { action: action });
        
        // Only validate for next_category and submit_final actions
        if (action === 'next_category' || action === 'submit_final') {
            console.log('Validating form before submission');
            
            if (!validateRequired()) {
                console.log('Form validation failed, preventing submission');
                e.preventDefault();
                return false;
            }
            
            console.log('Form validation passed');
            
            // Show confirmation modal for final submission
            if (action === 'submit_final') {
                e.preventDefault();
                showConfirmationModal();
                return false;
            }
        }
        
        // For save_draft and prev_category, don't validate
        if (action === 'save_draft' || action === 'prev_category') {
            console.log('Saving draft or going to previous category, skipping validation');
        }
    });
    
    // Enhanced input event handlers
    document.querySelectorAll('input, select, textarea').forEach(input => {
        // Clear validation styling on input change
        input.addEventListener('change', function() {
            const questionContainer = this.closest('.question-container');
            if (questionContainer) {
                questionContainer.classList.remove('border-red-300');
                const validationMsg = questionContainer.querySelector('.validation-message');
                if (validationMsg) {
                    validationMsg.classList.add('hidden');
                }
            }
        });
        
        // ✅ PERBAIKAN: Trigger conditional question handling menggunakan data-depends-on
        if (input.type === 'radio' || input.type === 'checkbox' || input.tagName === 'SELECT') {
            input.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id') || 
                                 this.closest('[data-question-id]')?.getAttribute('data-question-id');
                
                if (questionId) {
                    console.log('Input changed for question:', questionId, 'value:', this.value);
                    // ✅ PERBAIKAN: Gunakan data-depends-on bukan data-parent-question
                    const conditionalQuestions = document.querySelectorAll(`[data-depends-on="${questionId}"]`);
                    console.log('Found dependent questions:', conditionalQuestions.length);
                    
                    conditionalQuestions.forEach(conditionalQuestion => {
                        const conditionValue = conditionalQuestion.getAttribute('data-depends-value');
                        console.log('Handling conditional question with condition:', conditionValue);
                        handleConditionalQuestion(questionId, conditionalQuestion, conditionValue);
                    });
                }
            });
        }
    });
    
    // Handle numeric inputs
    document.querySelectorAll('input[type="number"], .numeric-only').forEach(input => {
        input.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        input.addEventListener('keypress', function(e) {
            // Only allow numbers
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });
    
    // Handle email validation
    document.querySelectorAll('input[type="email"]').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
                
                if (!emailRegex.test(this.value.trim())) {
                    this.classList.add('border-red-300');
                    this.style.backgroundColor = '#fef2f2';
                    
                    const questionContainer = this.closest('.question-container');
                    if (questionContainer) {
                        const validationMsg = questionContainer.querySelector('.validation-message');
                        if (validationMsg) {
                            validationMsg.textContent = 'Format email tidak valid! Email harus mengandung @ dan domain (contoh: nama@domain.com)';
                            validationMsg.classList.remove('hidden');
                        }
                    }
                } else {
                    this.classList.remove('border-red-300');
                    this.style.backgroundColor = '';
                    
                    const questionContainer = this.closest('.question-container');
                    if (questionContainer) {
                        const validationMsg = questionContainer.querySelector('.validation-message');
                        if (validationMsg) {
                            validationMsg.classList.add('hidden');
                        }
                    }
                }
            } else {
                // Clear validation styles when empty
                this.classList.remove('border-red-300');
                this.style.backgroundColor = '';
                
                const questionContainer = this.closest('.question-container');
                if (questionContainer) {
                    const validationMsg = questionContainer.querySelector('.validation-message');
                    if (validationMsg) {
                        validationMsg.classList.add('hidden');
                    }
                }
            }
        });
    });
    
}

function initializeConfirmationModal() {
    
    const modal = document.getElementById('confirmation-modal');
    const cancelButton = document.getElementById('cancel-submit');
    const confirmButton = document.getElementById('confirm-submit');
    const form = document.getElementById('questionnaire-form');
    
    if (!modal || !cancelButton || !confirmButton || !form) {
        console.error('Confirmation modal elements not found');
        return;
    }
    
    // Cancel button
    cancelButton.addEventListener('click', function() {
        hideConfirmationModal();
    });
    
    // Confirm button
    confirmButton.addEventListener('click', function() {
        hideConfirmationModal();
        
        // Create hidden input for final submission
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'submit_final';
        form.appendChild(actionInput);
        
        // Submit the form
        form.submit();
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideConfirmationModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideConfirmationModal();
        }
    });
    
}

function showConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}
function hideConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

function showValidationAlert(message) {
    const alert = document.getElementById('validation-alert');
    const messageSpan = document.getElementById('validation-message');
    
    if (alert && messageSpan) {
        messageSpan.textContent = message;
        alert.classList.remove('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            hideValidationAlert();
        }, 5000);
    }
}

function hideValidationAlert() {
    const alert = document.getElementById('validation-alert');
    if (alert) {
        alert.classList.add('hidden');
    }
}

function initializeLocationQuestions() {
    console.log('Initializing location questions for company questionnaire');
    
    // Find all location questions
    const locationQuestions = document.querySelectorAll('.location-question');
    
    if (locationQuestions.length === 0) {
        console.log('No location questions found');
        return;
    }
    
    locationQuestions.forEach(questionContainer => {
        const questionId = questionContainer.getAttribute('data-question-id');
        
        console.log('Setting up location question:', questionId);
        
        const countrySelect = document.getElementById(`country-select-${questionId}`);
        const stateSelect = document.getElementById(`state-select-${questionId}`);
        const citySelect = document.getElementById(`city-select-${questionId}`);
        
        if (!countrySelect || !stateSelect || !citySelect) {
            console.warn('Location selects not found for question:', questionId);
            return;
        }
        
        // Load countries first
        loadCountries(countrySelect, questionId);
        
        // Setup event listeners
        setupLocationEventListeners(questionId);
        
        // Load saved data if exists
        loadSavedLocationData(questionId);
    });
}

function loadCountries(countrySelect, questionId) {
    console.log('Loading countries from JSON for question:', questionId);
    
    countrySelect.disabled = true;
    countrySelect.innerHTML = '<option value="">Loading countries...</option>';
    
    fetch('/js/location-data/countries.json')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(countries => {
            console.log('Countries loaded from JSON:', countries.length);
            
            // Clear and add default option
            countrySelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
            
            // Add country options
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.code;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });
            
            countrySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading countries from JSON:', error);
            countrySelect.innerHTML = '<option value="">Error loading countries</option>';
            countrySelect.disabled = true;
        });
}

function loadStates(stateSelect, countryId, questionId) {
    console.log('Loading states from JSON for country:', countryId, 'question:', questionId);
    
    stateSelect.disabled = true;
    stateSelect.innerHTML = '<option value="">Loading states...</option>';
    
    fetch(`/js/location-data/${countryId}.json`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const states = data.states || [];
            console.log('States loaded from JSON:', states.length);
            
            // Clear and add default option
            stateSelect.innerHTML = '<option value="">-- Pilih Provinsi/State --</option>';
            
            // Add state options
            states.forEach(state => {
                const option = document.createElement('option');
                option.value = state.code;
                option.textContent = state.name;
                stateSelect.appendChild(option);
            });
            
            stateSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading states from JSON for country:', countryId, error);
            stateSelect.innerHTML = '<option value="">Error loading states</option>';
            stateSelect.disabled = true;
        });
}

function loadCities(citySelect, countryId, stateId, questionId) {
    console.log('Loading cities from JSON for country:', countryId, 'state:', stateId, 'question:', questionId);
    
    citySelect.disabled = true;
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    
    fetch(`/js/location-data/${countryId}.json`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const states = data.states || [];
            const selectedState = states.find(state => state.code === stateId);
            const cities = selectedState ? selectedState.cities || [] : [];
            
            console.log('Cities loaded from JSON:', cities.length);
            
            // Clear and add default option
            citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            
            // Add city options
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
            
            citySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading cities from JSON for country:', countryId, 'state:', stateId, error);
            citySelect.innerHTML = '<option value="">Error loading cities</option>';
            citySelect.disabled = true;
        });
}

function setupLocationEventListeners(questionId) {
    const countrySelect = document.getElementById(`country-select-${questionId}`);
    const stateSelect = document.getElementById(`state-select-${questionId}`);
    const citySelect = document.getElementById(`city-select-${questionId}`);
    
    if (!countrySelect || !stateSelect || !citySelect) {
        console.warn('Location selects not found for question:', questionId);
        return;
    }
    
    // Country change handler
    countrySelect.addEventListener('change', function() {
        const countryId = this.value;
        const countryName = this.options[this.selectedIndex].text;
        
        console.log('Country changed for question', questionId, ':', countryId, countryName);
        
        // Reset state and city dropdowns
        stateSelect.innerHTML = '<option value="">-- Pilih Provinsi/State --</option>';
        stateSelect.disabled = true;
        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        citySelect.disabled = true;
        
        if (countryId) {
            // Use JSON files for ALL countries including Indonesia
            loadStates(stateSelect, countryId, questionId);
        }
        
        updateLocationDisplay(questionId);
    });
    
    // State/Province change handler
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        const stateName = this.options[this.selectedIndex].text;
        
        console.log('State changed for question', questionId, ':', stateId, stateName);
        
        // Reset city dropdown
        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        citySelect.disabled = true;
        
        if (stateId) {
            const countryId = countrySelect.value;
            // Use JSON files for ALL countries including Indonesia
            loadCities(citySelect, countryId, stateId, questionId);
        }
        
        updateLocationDisplay(questionId);
    });
    
    // City change handler
    citySelect.addEventListener('change', function() {
        updateLocationDisplay(questionId);
    });
}

function updateLocationDisplay(questionId) {
    const countrySelect = document.getElementById(`country-select-${questionId}`);
    const stateSelect = document.getElementById(`state-select-${questionId}`);
    const citySelect = document.getElementById(`city-select-${questionId}`);
    const previewElement = document.getElementById(`location-preview-${questionId}`);
    const previewText = document.getElementById(`location-preview-text-${questionId}`);
    const combinedInput = document.getElementById(`location-combined-${questionId}`);
    
    if (!countrySelect || !stateSelect || !citySelect) {
        console.warn('Location selects not found for question:', questionId);
        return;
    }
    
    const countryValue = countrySelect.value;
    const stateValue = stateSelect.value;
    const cityValue = citySelect.value;
    
    const countryText = countryValue ? countrySelect.options[countrySelect.selectedIndex].text : '';
    const stateText = stateValue ? stateSelect.options[stateSelect.selectedIndex].text : '';
    const cityText = cityValue ? citySelect.options[citySelect.selectedIndex].text : '';
    
    console.log('Updating location display for question', questionId, ':', {
        country: { value: countryValue, text: countryText },
        state: { value: stateValue, text: stateText },
        city: { value: cityValue, text: cityText }
    });
    
    // Build location display text
    const locationParts = [];
    if (cityText && cityText !== '-- Pilih Kota --') locationParts.push(cityText);
    if (stateText && stateText !== '-- Pilih Provinsi/State --' && stateText !== '-- Pilih Provinsi --') locationParts.push(stateText);
    if (countryText && countryText !== '-- Pilih Negara --') locationParts.push(countryText);
    
    const locationDisplay = locationParts.join(', ');
    
    // Update preview if elements exist
    if (previewElement && previewText) {
        if (locationDisplay) {
            previewText.textContent = locationDisplay;
            previewElement.classList.remove('hidden');
        } else {
            previewElement.classList.add('hidden');
        }
    }
    
    // Update combined input for form submission
    if (combinedInput) {
        const locationData = {
            country: {
                code: countryValue,
                name: countryText
            },
            state: {
                code: stateValue,
                name: stateText
            },
            city: {
                code: cityValue,
                name: cityText
            },
            display: locationDisplay
        };
        
        combinedInput.value = JSON.stringify(locationData);
        console.log('Location data saved to combined input:', locationData);
    } else {
        console.warn('Combined input not found for question:', questionId);
    }
}

function loadSavedLocationData(questionId) {
    const initialDataElement = document.getElementById(`location-initial-${questionId}`);
    
    if (!initialDataElement || !initialDataElement.value) {
        console.log('No saved location data for question:', questionId);
        return;
    }
    
    try {
        const locationData = JSON.parse(initialDataElement.value);
        console.log('Loading saved location data for question', questionId, ':', locationData);
        
        const countrySelect = document.getElementById(`country-select-${questionId}`);
        const stateSelect = document.getElementById(`state-select-${questionId}`);
        const citySelect = document.getElementById(`city-select-${questionId}`);
        
        if (locationData.country && locationData.country.code) {
            // Wait for countries to load first
            setTimeout(() => {
                countrySelect.value = locationData.country.code;
                countrySelect.dispatchEvent(new Event('change'));
                
                if (locationData.state && locationData.state.code) {
                    // Wait for states to load
                    setTimeout(() => {
                        stateSelect.value = locationData.state.code;
                        stateSelect.dispatchEvent(new Event('change'));
                        
                        if (locationData.city && locationData.city.code) {
                            // Wait for cities to load
                            setTimeout(() => {
                                citySelect.value = locationData.city.code;
                                citySelect.dispatchEvent(new Event('change'));
                            }, 1000);
                        }
                    }, 1000);
                }
            }, 1000);
        }
    } catch (error) {
        console.error('Error parsing saved location data for question', questionId, ':', error);
    }
}
</script>
<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection