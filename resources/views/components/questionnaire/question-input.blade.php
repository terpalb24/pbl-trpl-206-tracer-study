@php
    $questionId = $question->id_question;
    $questionType = $question->type;
@endphp

{{-- Text Question - Responsive --}}
@if($questionType === 'text')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center flex-wrap gap-2 sm:gap-3">
            @if($question->before_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->before_text }}</span>
            @endif
            
            <input type="text" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   data-question-id="{{ $questionId }}"
                   class="flex-grow px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-0 text-sm sm:text-base"
                   placeholder="Masukkan jawaban...">
            
            @if($question->after_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->after_text }}</span>
            @endif
        </div>
    </div>

{{-- Numeric Question - Responsive --}}
@elseif($questionType === 'numeric')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center flex-wrap gap-2 sm:gap-3">
            <i class="fas fa-calculator text-green-600 text-lg sm:text-xl self-start sm:self-center"></i>
            @if($question->before_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->before_text }}</span>
            @endif
            
            <input type="text" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   data-question-id="{{ $questionId }}"
                   data-question-type="numeric"
                   class="flex-grow px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-0 numeric-only text-sm sm:text-base"
                   placeholder="Masukkan angka..."
                   pattern="[0-9]*"
                   inputmode="numeric"
                   autocomplete="off"
                   onkeypress="return /[0-9]/.test(event.key) || ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(event.key)"
                   onpaste="return false"
                   ondrop="return false">
            
            @if($question->after_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->after_text }}</span>
            @endif
        </div>
        
        <div class="mt-2 text-xs sm:text-sm text-gray-500 flex items-center">
            <i class="fas fa-info-circle mr-1"></i>
            <span>Hanya dapat memasukkan angka (0-9)</span>
        </div>
    </div>

{{-- Email Question - Responsive --}}
@elseif($questionType === 'email')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center flex-wrap gap-2 sm:gap-3">
            <i class="fas fa-envelope text-blue-600 text-lg sm:text-xl self-start sm:self-center"></i>
            @if($question->before_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->before_text }}</span>
            @endif
            
            <input type="email" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   data-question-id="{{ $questionId }}"
                   class="flex-grow px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-0 email-validation text-sm sm:text-base"
                   placeholder="contoh@domain.com"
                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
            
            @if($question->after_text)
                <span class="text-gray-700 font-medium text-sm sm:text-base whitespace-nowrap">{{ $question->after_text }}</span>
            @endif
        </div>
        
        <div class="mt-2 text-xs sm:text-sm text-gray-500 flex items-start">
            <i class="fas fa-info-circle mr-1 mt-0.5 flex-shrink-0"></i>
            <span>Masukkan email yang valid dengan domain (contoh: nama@gmail.com)</span>
        </div>
    </div>

{{-- Date Question - Responsive --}}
@elseif($questionType === 'date')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
            <i class="fas fa-calendar-alt text-blue-600 text-lg sm:text-xl self-start sm:self-center"></i>
            <input type="date" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   data-question-id="{{ $questionId }}"
                   class="w-full sm:w-auto px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
        </div>
    </div>

{{-- Location Question - Responsive --}}
@elseif($questionType === 'location')
    <div class="location-question bg-white border border-gray-300 rounded-lg p-3 sm:p-4" data-question-id="{{ $question->id_question }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4">
            <!-- Negara -->
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="country-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-globe mr-1"></i>Negara:
                </label>
                <select id="country-select-{{ $question->id_question }}" 
                        data-question-id="{{ $questionId }}"
                        class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-md text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Negara --</option>
                </select>
            </div>
            
            <!-- Provinsi/State -->
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="state-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map mr-1"></i>Provinsi/State:
                </label>
                <select id="state-select-{{ $question->id_question }}" 
                        data-question-id="{{ $questionId }}"
                        class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-md text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                    <option value="">-- Pilih Provinsi/State --</option>
                </select>
            </div>
            
            <!-- Kota -->
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="city-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-city mr-1"></i>Kota:
                </label>
                <select id="city-select-{{ $question->id_question }}" 
                        data-question-id="{{ $questionId }}"
                        class="w-full px-2 sm:px-3 py-2 border border-gray-300 rounded-md text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                    <option value="">-- Pilih Kota --</option>
                </select>
            </div>
        </div>
        
        <!-- Location Preview - Responsive -->
        <div id="location-preview-{{ $question->id_question }}" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
            <div class="flex items-start">
                <i class="fas fa-map-marker-alt text-blue-600 mr-2 mt-1 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-blue-800">Lokasi yang dipilih:</p>
                    <p id="location-preview-text-{{ $question->id_question }}" class="text-xs sm:text-sm text-blue-700 break-words"></p>
                </div>
            </div>
        </div>
        
        <!-- Hidden inputs -->
        <input type="hidden" id="location-combined-{{ $question->id_question }}" 
               name="location_combined[{{ $question->id_question }}]" 
               data-question-id="{{ $questionId }}"
               value="">
        
        @if(isset($prevLocationAnswers[$question->id_question]))
            <input type="hidden" id="location-initial-{{ $question->id_question }}" value="{{ json_encode($prevLocationAnswers[$question->id_question]) }}">
        @endif
        
        <div class="text-red-500 text-xs sm:text-sm mt-1 validation-message hidden"></div>
    </div>

{{-- Single Option Question - Responsive --}}
@elseif($questionType === 'option')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="space-y-2 sm:space-y-3">
            @foreach($question->options as $option)
                <div class="flex items-start p-2 sm:p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="radio" 
                           name="question_{{ $questionId }}" 
                           value="{{ $option->id_questions_options }}"
                           id="option_{{ $option->id_questions_options }}"
                           class="option-radio mt-1 mr-2 sm:mr-3 text-blue-600 focus:ring-blue-500 w-4 h-4 sm:w-5 sm:h-5"
                           data-question-id="{{ $questionId }}"
                           data-is-other="{{ $option->is_other_option ? '1' : '0' }}"
                           {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'checked' : '' }}>
                    <label for="option_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium text-sm sm:text-base leading-relaxed">
                        {{ $option->option }}
                    </label>
                </div>
                
                @if($option->is_other_option)
                    <div class="ml-4 sm:ml-6 mt-2 p-2 sm:p-3 bg-blue-50 border border-blue-200 rounded-md {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? '' : 'hidden' }}"
                         id="other_field_{{ $questionId }}_{{ $option->id_questions_options }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <i class="fas fa-edit text-blue-600 self-start sm:self-center"></i>
                            @if($option->other_before_text)
                                <span class="text-gray-600 text-sm">{{ $option->other_before_text }}</span>
                            @endif
                            <input type="text" 
                                   name="question_{{ $questionId }}_other"
                                   value="{{ $prevOtherAnswer ?? '' }}"
                                   data-question-id="{{ $questionId }}"
                                   class="flex-grow px-2 sm:px-3 py-2 border border-blue-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                                   placeholder="Sebutkan..."
                                   id="other_{{ $option->id_questions_options }}">
                            @if($option->other_after_text)
                                <span class="text-gray-600 text-sm">{{ $option->other_after_text }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- Multiple Choice Question - Responsive --}}
@elseif($questionType === 'multiple')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="space-y-2 sm:space-y-3">
            @foreach($question->options as $option)
                <div class="flex items-start p-2 sm:p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="checkbox" 
                           name="question_{{ $questionId }}[]" 
                           value="{{ $option->id_questions_options }}"
                           id="multiple_{{ $option->id_questions_options }}"
                           class="multiple-checkbox mt-1 mr-2 sm:mr-3 text-blue-600 focus:ring-blue-500 rounded w-4 h-4 sm:w-5 sm:h-5"
                           data-question-id="{{ $questionId }}"
                           data-is-other="{{ $option->is_other_option ? '1' : '0' }}"
                           {{ in_array($option->id_questions_options, $prevMultipleAnswers ?? []) ? 'checked' : '' }}>
                    <label for="multiple_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium text-sm sm:text-base leading-relaxed">
                        {{ $option->option }}
                    </label>
                </div>
                
                @if($option->is_other_option)
                    <div class="ml-4 sm:ml-6 mt-2 p-2 sm:p-3 bg-blue-50 border border-blue-200 rounded-md {{ in_array($option->id_questions_options, $prevMultipleAnswers[$questionId] ?? []) ? '' : 'hidden' }}"
                         id="multiple_other_field_{{ $questionId }}_{{ $option->id_questions_options }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <i class="fas fa-edit text-blue-600 self-start sm:self-center"></i>
                            @if($option->other_before_text)
                                <span class="text-gray-600 text-sm">{{ $option->other_before_text }}</span>
                            @endif
                            <input type="text" 
                                   name="question_{{ $questionId }}_other_{{ $option->id_questions_options }}"
                                   value="{{ isset($prevMultipleOtherAnswers[$option->id_questions_options]) ? $prevMultipleOtherAnswers[$option->id_questions_options] : '' }}"
                                   data-question-id="{{ $questionId }}"
                                   class="flex-grow px-2 sm:px-3 py-2 border border-blue-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                                   placeholder="Sebutkan..."
                                   id="multiple_other_{{ $option->id_questions_options }}">
                            @if($option->other_after_text)
                                <span class="text-gray-600 text-sm">{{ $option->other_after_text }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- Rating Question - Responsive --}}
@elseif($questionType === 'rating')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex items-center mb-3 sm:mb-4">
            <i class="fas fa-star text-yellow-500 mr-2"></i>
            <span class="font-medium text-gray-700 text-sm sm:text-base">Pilih Rating</span>
        </div>
        <div class="grid gap-2 sm:gap-3">
            @foreach($question->options as $option)
                @php
                    $ratingText = strtolower($option->option);
                    $starCount = 1;
                    
                    if (str_contains($ratingText, 'kurang')) {
                        $starCount = 1;
                    } elseif (str_contains($ratingText, 'cukup')) {
                        $starCount = 2;
                    } elseif (str_contains($ratingText, 'baik sekali') || str_contains($ratingText, 'sangat baik')) {
                        $starCount = 5;
                    } elseif (str_contains($ratingText, 'baik')) {
                        $starCount = 3;
                    }
                    
                    if (is_numeric($option->option)) {
                        $starCount = min(5, max(1, (int)$option->option));
                    }
                @endphp
                
                <div class="flex items-center p-2 sm:p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="radio" 
                           name="question_{{ $questionId }}" 
                           value="{{ $option->id_questions_options }}"
                           id="rating_{{ $option->id_questions_options }}"
                           class="rating-radio mr-2 sm:mr-3 text-yellow-500 focus:ring-yellow-500 w-4 h-4 sm:w-5 sm:h-5"
                           data-question-id="{{ $questionId }}"
                           {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'checked' : '' }}>
                    <label for="rating_{{ $option->id_questions_options }}" class="cursor-pointer flex items-center flex-grow">
                        <span class="rating-option inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 rounded-md text-xs sm:text-sm font-medium transition-all duration-200 {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300' : 'bg-gray-100 text-gray-700 border-2 border-gray-300' }} hover:bg-yellow-50 hover:border-yellow-200" >
                            <span class="flex items-center mr-1 sm:mr-2"style="box-shadow: none;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $starCount)
                                        <i class="fas fa-star text-yellow-500 text-xs sm:text-sm"></i>
                                    @else
                                        <i class="far fa-star text-gray-300 text-xs sm:text-sm"></i>
                                    @endif
                                @endfor
                            </span>
                            <span class="break-words" style="box-shadow: none;">{{ $option->option }}</span>
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

{{-- Scale Question - Responsive --}}
@elseif($questionType === 'scale')
    <div class="bg-white border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="flex items-center mb-3 sm:mb-4">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            <span class="font-medium text-gray-700 text-sm sm:text-base">Skala Penilaian (1-5)</span>
        </div>
        
        <!-- Scale Labels - Responsive -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 space-y-1 sm:space-y-0">
            <span class="text-xs sm:text-sm text-gray-600 font-medium text-center sm:text-left">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
            <span class="text-xs sm:text-sm text-gray-600 font-medium text-center sm:text-right">{{ $question->after_text ?: 'Sangat Baik' }}</span>
        </div>
        
        <!-- Scale Options - Responsive Grid -->
        <div class="grid grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
            @for($i = 1; $i <= 5; $i++)
                @php
                    $scaleOption = $question->options->where('option', (string)$i)->first();
                @endphp
                @if($scaleOption)
                    <div class="flex flex-col items-center">
                        <input type="radio" 
                               name="question_{{ $questionId }}" 
                               value="{{ $scaleOption->id_questions_options }}"
                               id="scale_{{ $scaleOption->id_questions_options }}"
                               class="scale-radio hidden"
                               data-question-id="{{ $questionId }}"
                               {{ isset($prevAnswer) && $prevAnswer == $scaleOption->id_questions_options ? 'checked' : '' }}>
                        <label for="scale_{{ $scaleOption->id_questions_options }}" class="cursor-pointer">
                            <span class="inline-block w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-full border-2 {{ isset($prevAnswer) && $prevAnswer == $scaleOption->id_questions_options ? 'bg-green-500 text-white border-green-500' : 'bg-white border-gray-300' }} text-center leading-6 sm:leading-8 lg:leading-10 text-sm sm:text-base lg:text-lg font-bold hover:bg-green-50 hover:border-green-300 transition-all duration-200 scale-option">
                                {{ $i }}
                            </span>
                        </label>
                        <span class="text-xs text-gray-500 mt-1 text-center">{{ $i }}</span>
                    </div>
                @endif
            @endfor
        </div>
        
        <!-- Scale Description - Mobile -->
        <div class="mt-3 text-xs text-gray-500 text-center sm:hidden">
            <p>Ketuk angka untuk memilih skala penilaian</p>
        </div>
    </div>

{{-- Unknown Question Type - Responsive --}}
@else
    <div class="bg-gray-50 border border-gray-300 rounded-lg p-3 sm:p-4">
        <div class="text-center text-gray-500">
            <i class="fas fa-question-circle text-xl sm:text-2xl mb-2"></i>
            <p class="text-sm sm:text-base">Tipe pertanyaan tidak dikenal: {{ $questionType }}</p>
        </div>
    </div>
@endif

<style>
/* Enhanced responsive styles untuk question inputs */
@media (max-width: 640px) {
    /* Mobile-specific improvements */
    .flex-wrap {
        flex-wrap: wrap !important;
    }
    
    .min-w-0 {
        min-width: 0 !important;
    }
    
    /* Better touch targets for mobile */
    input[type="radio"], 
    input[type="checkbox"] {
        min-width: 1rem !important;
        min-height: 1rem !important;
    }
    
    /* Scale option mobile optimization */
    .scale-option {
        min-width: 2rem !important;
        min-height: 2rem !important;
        line-height: 1.5rem !important;
        font-size: 0.875rem !important;
    }
    
    /* Location select mobile optimization */
    .location-question select {
        padding: 0.5rem !important;
        font-size: 0.875rem !important;
    }
}

/* Tablet optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
    .location-question {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .location-question > div:last-child {
        grid-column: span 2 !important;
    }
}

/* Desktop optimizations */
@media (min-width: 1025px) {
    .rating-radio + label span {
        padding: 0.75rem 1rem !important;
    }
    
    .scale-option {
        width: 3rem !important;
        height: 3rem !important;
        line-height: 2.5rem !important;
    }
}

/* Enhanced focus states */
input:focus,
select:focus,
textarea:focus {
    outline: none !important;
    ring-width: 2px !important;
    ring-color: rgb(59 130 246) !important;
    border-color: rgb(59 130 246) !important;
}

/* Better visual feedback */
.hover\:bg-gray-50:hover {
    background-color: rgb(249 250 251) !important;
    transition: background-color 0.2s ease !important;
}

/* Improved checkbox and radio styling */
input[type="radio"]:checked,
input[type="checkbox"]:checked {
    background-color: rgb(59 130 246) !important;
    border-color: rgb(59 130 246) !important;
}

/* Better text wrapping */
.break-words {
    word-wrap: break-word !important;
    word-break: break-word !important;
}

/* Loading states */
.location-question select:disabled {
    background-color: rgb(243 244 246) !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
}

/* Animation for scale selection */
.scale-option {
    transition: all 0.2s ease-in-out !important;
}

.scale-option:hover {
    transform: scale(1.05) !important;
}

/* Enhanced validation styles */
.validation-message {
    transition: opacity 0.3s ease !important;
}

.validation-message:not(.hidden) {
    animation: fadeIn 0.3s ease !important;
}

/* Rating option styling dengan dynamic color change menggunakan JavaScript */
.rating-radio:checked + label .rating-option {
    background-color: rgb(254 249 195) !important; /* bg-yellow-100 */
    color: rgb(146 64 14) !important; /* text-yellow-800 */
    border-color: rgb(252 211 77) !important; /* border-yellow-300 */
    box-shadow: 0 0 0 2px rgba(252, 211, 77, 0.2) !important;
}

.rating-radio:not(:checked) + label .rating-option {
    background-color: rgb(243 244 246) !important; /* bg-gray-100 */
    color: rgb(55 65 81) !important; /* text-gray-700 */
    border-color: rgb(209 213 219) !important; /* border-gray-300 */
}

/* Hover states untuk rating options */
.rating-option:hover {
    background-color: rgb(254 252 232) !important; /* bg-yellow-50 */
    border-color: rgb(253 224 71) !important; /* border-yellow-200 */
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

/* Transition untuk smooth color change */
.rating-option {
    transition: all 0.3s ease !important;
}

/* Stars hover effect untuk rating */
.rating-radio + label:hover .fa-star {
    transform: scale(1.1) !important;
    transition: transform 0.2s ease !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-0.25rem); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Enhanced script dengan support untuk conditional questions
document.addEventListener('DOMContentLoaded', function() {
    // Handle rating radio buttons
    const ratingRadios = document.querySelectorAll('.rating-radio');
    
    ratingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.getAttribute('data-question-id');
            const allRatingOptions = document.querySelectorAll(`input[name="question_${questionId}"] + label .rating-option`);
            
            // Reset semua rating options ke state default
            allRatingOptions.forEach(option => {
                option.classList.remove('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
                option.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
            });
            
            // Apply yellow style ke option yang dipilih
            if (this.checked) {
                const selectedOption = this.nextElementSibling.querySelector('.rating-option');
                selectedOption.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300');
                selectedOption.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
            }
        });
    });
    
    // ✅ HAPUS: Event listeners untuk conditional questions (akan dihandle di fill.blade.php)
    // Hanya handle rating visual feedback di sini
    
    console.log('question-input.blade.php loaded, found inputs with data-question-id:', 
                document.querySelectorAll('[data-question-id]').length);
});
</script>