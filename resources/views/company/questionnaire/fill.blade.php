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
                            <p class="text-blue-700">{{ $alumni->nim }}</p>
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
// Ensure everything runs after DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM loaded - initializing enhanced validation system');
    
    // Initialize the form validation with proper event capture
    initEnhancedFormValidation();
    
    // Initialize other questionnaire features
    initializeQuestionnaire();
});

// Main validation function with proper event prevention
function initEnhancedFormValidation() {
    const form = document.getElementById('questionnaire-form');
    
    // Get navigation buttons
    const nextButton = document.getElementById('next-category-btn');
    const submitButton = document.getElementById('submit-final-btn');
    
    // Clear any existing event listeners (to prevent duplicates)
    if (nextButton) {
        // Clone to remove all event handlers
        const nextClone = nextButton.cloneNode(true);
        nextButton.parentNode.replaceChild(nextClone, nextButton);
        
        // Attack fresh event handlers to the new button
        nextClone.addEventListener('click', function(e) {
            console.log('▶️ Next button clicked - running validation');
            
            // Always prevent default submission
            e.preventDefault();
            e.stopPropagation();
            
            // Show loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validasi...';
            
            // Run enhanced validation
            const isValid = validateAllQuestions();
            
            if (isValid) {
                console.log('✅ All questions valid - submitting form');
                
                // Set action value
                let actionInput = form.querySelector('input[name="action"]');
                if (!actionInput) {
                    actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    form.appendChild(actionInput);
                }
                actionInput.value = 'next_category';
                
                // Submit form
                form.submit();
            } else {
                console.log('❌ Validation failed - form NOT submitted');
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Show error feedback with shake animation
                this.classList.add('shake');
                setTimeout(() => this.classList.remove('shake'), 500);
            }
            
            // Return false to ensure default submission is blocked
            return false;
        });
    }
    
    // Similar handling for submit button
    if (submitButton) {
        // Clone to remove all event handlers
        const submitClone = submitButton.cloneNode(true);
        submitButton.parentNode.replaceChild(submitClone, submitButton);
        
        // Attach fresh event handler
        submitClone.addEventListener('click', function(e) {
            console.log('🏁 Submit button clicked - running validation');
            
            // Always prevent default submission
            e.preventDefault();
            e.stopPropagation();
            
            // Show loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validasi...';
            
            // Run enhanced validation
            const isValid = validateAllQuestions();
            
            if (isValid) {
                console.log('✅ All questions valid - showing confirmation modal');
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Show confirmation modal
                showConfirmationModal();
            } else {
                console.log('❌ Validation failed - staying on form');
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Show error feedback with shake animation
                this.classList.add('shake');
                setTimeout(() => this.classList.remove('shake'), 500);
            }
            
            // Return false to ensure default submission is blocked
            return false;
        });
    }
    
    // Global form submission handler as a safety measure
    form.addEventListener('submit', function(e) {
        // Check if submission is being handled by our validation system
        const submitAction = this.querySelector('input[name="action"]')?.value;
        
        // Skip validation for saving draft
        if (submitAction === 'save_draft') {
            console.log('📝 Saving draft - bypassing validation');
            return true;
        }
        
        // For all other submissions, validate
        console.log('🔒 Form submission intercepted - running validation check');
        const isValid = validateAllQuestions();
        
        if (!isValid) {
            console.log('❌ Validation failed - preventing form submission');
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        
        console.log('✅ Validation passed - allowing form submission');
        return true;
    });
    
    console.log('✅ Enhanced form validation initialized');
}

// Comprehensive validation function
function validateAllQuestions() {
    console.log('🔍 Running enhanced validation on all questions');
    
    // Get all visible questions
    const allQuestions = Array.from(document.querySelectorAll('.question-container')).filter(q => {
        return window.getComputedStyle(q).display !== 'none';
    });
    
    console.log(`Found ${allQuestions.length} visible questions to validate`);
    
    let isValid = true;
    let firstInvalidQuestion = null;
    const invalidQuestions = [];
    
    // Reset previous validation styling
    allQuestions.forEach(q => {
        q.classList.remove('border-red-300');
        q.style.borderColor = '';
        q.style.backgroundColor = '';
        
        const validationMsg = q.querySelector('.validation-message');
        if (validationMsg) {
            validationMsg.classList.add('hidden');
            validationMsg.style.display = 'none';
        }
    });
    
    // Validate each question
    allQuestions.forEach((questionContainer, index) => {
        const questionId = questionContainer.dataset.questionId;
        const questionType = questionContainer.dataset.questionType;
        const isRequired = questionContainer.dataset.required === 'true';
        
        if (!isRequired) {
            console.log(`Question ${questionId} is not required - skipping validation`);
            return; // Skip non-required questions
        }
        
        console.log(`Validating question ${questionId} (${questionType})`);
        
        let isAnswered = false;
        let errorMessage = 'Pertanyaan ini wajib diisi!';
        
        // Check if question is answered based on its type
        switch (questionType) {
            case 'text':
            case 'date':
            case 'numeric':
                const textInput = questionContainer.querySelector(`input[name="question_${questionId}"], textarea[name="question_${questionId}"]`);
                isAnswered = textInput && textInput.value.trim() !== '';
                console.log(`- Text question ${questionId}: ${isAnswered ? '✓' : '✗'} (value: "${textInput?.value || ''}")`);
                break;
                
            case 'email':
                const emailInput = questionContainer.querySelector(`input[name="question_${questionId}"]`);
                if (emailInput && emailInput.value.trim() !== '') {
                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    isAnswered = emailRegex.test(emailInput.value.trim());
                    
                    if (!isAnswered) {
                        errorMessage = 'Format email tidak valid! Harus mengandung @ dan domain yang benar.';
                    }
                    
                    console.log(`- Email question ${questionId}: ${isAnswered ? '✓' : '✗'} (value: "${emailInput.value}")`);
                } else {
                    isAnswered = false;
                    console.log(`- Email question ${questionId}: ✗ (empty)`);
                }
                break;
                
            case 'option':
            case 'rating':
            case 'scale':
                const radioInput = questionContainer.querySelector(`input[name="question_${questionId}"]:checked`);
                isAnswered = !!radioInput;
                console.log(`- Option question ${questionId}: ${isAnswered ? '✓' : '✗'} (selected: ${radioInput?.value || 'none'})`);
                break;
                
            case 'multiple':
                const checkboxInputs = questionContainer.querySelectorAll(`input[name="question_${questionId}[]"]:checked`);
                isAnswered = checkboxInputs.length > 0;
                console.log(`- Multiple question ${questionId}: ${isAnswered ? '✓' : '✗'} (selected: ${checkboxInputs.length})`);
                break;
                
            case 'location':
                const provinceSelect = questionContainer.querySelector(`select[name="question_${questionId}_province"]`);
                const citySelect = questionContainer.querySelector(`select[name="question_${questionId}_city"]`);
                isAnswered = provinceSelect && provinceSelect.value && citySelect && citySelect.value;
                console.log(`- Location question ${questionId}: ${isAnswered ? '✓' : '✗'} (province: "${provinceSelect?.value || ''}", city: "${citySelect?.value || ''}")`);
                break;
                
            default:
                console.warn(`Unknown question type: ${questionType}`);
                isAnswered = true; // Skip validation for unknown types
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
            validationMsg.textContent = errorMessage;
            validationMsg.classList.remove('hidden');
            validationMsg.style.display = 'block';
            
            // Store first invalid question for scrolling
            if (!firstInvalidQuestion) {
                firstInvalidQuestion = questionContainer;
            }
            
            invalidQuestions.push(questionText);
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

// Helper function to show a more prominent validation alert
function showValidationAlert(message) {
    const validationAlert = document.getElementById('validation-alert');
    const validationMessage = document.getElementById('validation-message');
    
    if (validationAlert && validationMessage) {
        validationMessage.textContent = message;
        validationAlert.classList.remove('hidden');
        validationAlert.style.display = 'flex';
        
        // Add shake animation
        validationAlert.classList.add('shake');
        setTimeout(() => {
            validationAlert.classList.remove('shake');
        }, 500);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            validationAlert.classList.add('hidden');
        }, 5000);
    } else {
        // Fallback to browser alert if custom alert not found
        alert(message);
    }
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

// ✅ TAMBAIKAN: Real-time email validation
function initializeFormValidation() {
    const form = document.getElementById('questionnaire-form');
    const nextButton = document.getElementById('next-category-btn');
    const submitButton = document.getElementById('submit-final-btn');
    
    if (nextButton) {
        // Remove any existing event listeners first to avoid duplicates
        nextButton.replaceWith(nextButton.cloneNode(true));
        const freshNextButton = document.getElementById('next-category-btn');
        
        freshNextButton.addEventListener('click', function(e) {
            // Always prevent default action first
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🛑 Next category button clicked - starting validation...');
            
            // Add loading state
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memvalidasi...';
            
            // Force validation to run synchronously
            const validationResult = validateRequired();
            console.log(`🔍 Validation result: ${validationResult}`);
            
            if (validationResult) {
                console.log('✅ Validation passed, proceeding to next category...');
                
                // Create or update the action input
                let actionInput = form.querySelector('input[name="action"]');
                if (!actionInput) {
                    actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    form.appendChild(actionInput);
                }
                
                // Set the action value
                actionInput.value = 'next_category';
                
                // Submit the form programmatically
                console.log('📤 Submitting form to next category...');
                form.submit();
            } else {
                console.log('❌ Validation failed - blocking navigation to next category');
                
                // Restore button state
                this.disabled = false;
                this.innerHTML = originalText;
                
                // Add a visual shake effect to indicate failure
                this.classList.add('shake');
                setTimeout(() => {
                    this.classList.remove('shake');
                }, 600);
                
                // Show a prominent error message
                showValidationAlert('Ada pertanyaan wajib yang belum diisi! Mohon lengkapi semua pertanyaan sebelum melanjutkan.');
                
                // Return false to ensure the form is not submitted
                return false;
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

// Add this function to your script section to fix the error
function initializeQuestionnaire() {
    console.log('🔧 Initializing questionnaire components...');
    
    try {
        // Initialize conditional questions (questions that depend on other questions)
        initializeConditionalQuestions();
        
        // Initialize location questions (province/city selectors)
        initializeLocationQuestions();
        
        // Initialize the confirmation modal
        initializeConfirmationModal();
        
        console.log('✅ Questionnaire initialized successfully');
    } catch (error) {
        console.error('❌ Error initializing questionnaire:', error);
    }
}

// Add this function for handling conditional questions
function initializeConditionalQuestions() {
    console.log('Setting up conditional questions...');
    
    // Get all questions that depend on other questions
    const conditionalQuestions = document.querySelectorAll('.question-container[data-parent-question]');
    console.log(`Found ${conditionalQuestions.length} conditional questions`);
    
    // Set up event handlers for parent questions
    document.querySelectorAll('input[type="radio"], select').forEach(input => {
        input.addEventListener('change', function() {
            handleConditionalQuestion(this);
        });
    });
    
    // Initial check for any preselected values
    document.querySelectorAll('input[type="radio"]:checked, select').forEach(input => {
        if (input.value) {
            handleConditionalQuestion(input);
        }
    });
}

// Helper function to handle conditional logic
function handleConditionalQuestion(input) {
    const questionId = input.name.replace('question_', '');
    const value = input.value;
    
    // Find child questions that depend on this question
    document.querySelectorAll(`.question-container[data-parent-question="${questionId}"]`).forEach(childQuestion => {
        const expectedValue = childQuestion.dataset.conditionValue;
        
        // Show or hide based on the condition
        if (value === expectedValue) {
            childQuestion.style.display = '';
        } else {
            childQuestion.style.display = 'none';
            
            // Clear inputs in hidden questions
            childQuestion.querySelectorAll('input, select, textarea').forEach(childInput => {
                if (childInput.type === 'radio' || childInput.type === 'checkbox') {
                    childInput.checked = false;
                } else {
                    childInput.value = '';
                }
            });
        }
    });
}

// Add this function for location questions
function initializeLocationQuestions() {
    console.log('Setting up location questions...');
    
    // Find all location questions
    const locationQuestions = document.querySelectorAll('.question-container[data-question-type="location"]');
    console.log(`Found ${locationQuestions.length} location questions`);
    
    // Process each location question
    locationQuestions.forEach(questionContainer => {
        // Get the select elements
        const provinceSelect = questionContainer.querySelector('.province-select');
        const citySelect = questionContainer.querySelector('.city-select');
        
        // Skip if elements don't exist
        if (!provinceSelect || !citySelect) {
            console.warn('Location question is missing province or city select elements');
            return;
        }
        
        // Now safely access the question ID
        const questionId = provinceSelect.dataset.questionId || 
                          provinceSelect.name.replace('question_', '').replace('_province', '');
        
        if (!questionId) {
            console.warn('Could not determine question ID for location question');
            return;
        }
        
        console.log(`Initializing location question ${questionId}`);
        
        // Load provinces
        loadProvinces(provinceSelect);
        
        // Handle province change
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            const provinceText = this.options[this.selectedIndex].text;
            
            // Update hidden province name field if it exists
            const provinceNameInput = questionContainer.querySelector(`input[name="question_${questionId}_province_name"]`);
            if (provinceNameInput) {
                provinceNameInput.value = provinceText !== '-- Pilih Provinsi --' ? provinceText : '';
            }
            
            // Load cities based on selected province
            if (provinceId) {
                loadCities(provinceId, citySelect);
            } else {
                citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                citySelect.disabled = true;
            }
        });
    });
}

// Helper functions for location questions
function loadProvinces(provinceSelect) {
    // Set loading state
    provinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';
    
    // Simple mock data (replace with actual API call if available)
    setTimeout(() => {
        provinceSelect.innerHTML = `
            <option value="">-- Pilih Provinsi --</option>
            <option value="1">Jawa Barat</option>
            <option value="2">DKI Jakarta</option>
            <option value="3">Jawa Tengah</option>
            <option value="4">Jawa Timur</option>
            <option value="5">Banten</option>
        `;
    }, 300);
}

function loadCities(provinceId, citySelect) {
    // Set loading state
    citySelect.innerHTML = '<option value="">Memuat kota...</option>';
    citySelect.disabled = false;
    
    // Simple mock data (replace with actual API call if available)
    setTimeout(() => {
        citySelect.innerHTML = `
            <option value="">-- Pilih Kota/Kabupaten --</option>
            <option value="1">Kota A</option>
            <option value="2">Kota B</option>
            <option value="3">Kota C</option>
        `;
    }, 300);
}
</script>

@endsection
