@php
    $questionId = $question->id_question;
    $questionType = $question->type;
@endphp

{{-- Text Question --}}
@if($questionType === 'text')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center flex-wrap">
            @if($question->before_text)
                <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
            @endif
            
            <input type="text" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-48"
                   placeholder="Masukkan jawaban...">
            
            @if($question->after_text)
                <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
            @endif
        </div>
    </div>

{{-- Numeric Question --}}
@elseif($questionType === 'numeric')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center flex-wrap">
            <i class="fas fa-calculator text-green-600 mr-3"></i>
            @if($question->before_text)
                <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
            @endif
            
            {{-- ✅ PERBAIKAN CRITICAL: Tambah multiple attributes untuk block huruf --}}
            <input type="text" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-48 numeric-only"
                   placeholder="Masukkan angka..."
                   pattern="[0-9]*"
                   inputmode="numeric"
                   data-question-type="numeric"
                   data-question-id="{{ $questionId }}"
                   autocomplete="off"
                   onkeypress="return /[0-9]/.test(event.key) || ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(event.key)"
                   onpaste="return false"
                   ondrop="return false">
            
            @if($question->after_text)
                <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
            @endif
        </div>
        
        <div class="mt-2 text-xs text-gray-500 flex items-center">
            <i class="fas fa-info-circle mr-1"></i>
            Hanya dapat memasukkan angka (0-9), maksimal 9 digit
        </div>
    </div>

{{-- Email Question --}}
@elseif($questionType === 'email')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center flex-wrap">
            <i class="fas fa-envelope text-blue-600 mr-3"></i>
            @if($question->before_text)
                <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
            @endif
            
            <input type="email" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-48 email-validation"
                   placeholder="contoh@domain.com"
                   pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
            
            @if($question->after_text)
                <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
            @endif
        </div>
        
        <div class="mt-2 text-xs text-gray-500 flex items-center">
            <i class="fas fa-info-circle mr-1"></i>
            Masukkan email yang valid dengan domain (contoh: nama@gmail.com)
        </div>
    </div>

{{-- Date Question --}}
@elseif($questionType === 'date')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
            <input type="date" 
                   name="question_{{ $questionId }}" 
                   value="{{ $prevAnswer ?? '' }}"
                   class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

{{-- Location Question --}}
@elseif($questionType === 'location')
    <!-- Location question -->
    <div class="location-question" data-question-id="{{ $question->id_question }}">
        <div class="grid grid-cols-1 gap-4 mb-4">
            <!-- Negara -->
            <div>
                <label for="country-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">Negara:</label>
                <select id="country-select-{{ $question->id_question }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">-- Pilih Negara --</option>
                </select>
            </div>
            
            <!-- Provinsi/State -->
            <div>
                <label for="state-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">Provinsi/State:</label>
                <select id="state-select-{{ $question->id_question }}" class="w-full px-3 py-2 border border-gray-300 rounded-md" disabled>
                    <option value="">-- Pilih Provinsi/State --</option>
                </select>
            </div>
            
            <!-- Kota -->
            <div>
                <label for="city-select-{{ $question->id_question }}" class="block text-sm font-medium text-gray-700 mb-2">Kota:</label>
                <select id="city-select-{{ $question->id_question }}" class="w-full px-3 py-2 border border-gray-300 rounded-md" disabled>
                    <option value="">-- Pilih Kota --</option>
                </select>
            </div>
        </div>
        
        <!-- Hidden input to store combined value -->
        <input type="hidden" id="location-combined-{{ $question->id_question }}" name="location_combined[{{ $question->id_question }}]" value="">
        
        <!-- Hidden input for initial value (when editing) -->
        @if(isset($prevLocationAnswers[$question->id_question]))
            <input type="hidden" id="location-initial-{{ $question->id_question }}" value="{{ json_encode($prevLocationAnswers[$question->id_question]) }}">
        @endif
    </div>
    <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

{{-- Single Option Question --}}
@elseif($questionType === 'option')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="space-y-3">
            @foreach($question->options as $option)
                <div class="flex items-start p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="radio" 
                           name="question_{{ $questionId }}" 
                           value="{{ $option->id_questions_options }}"
                           id="option_{{ $option->id_questions_options }}"
                           class="option-radio mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                           data-question-id="{{ $questionId }}"
                           data-is-other="{{ $option->is_other_option ? '1' : '0' }}"
                           {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'checked' : '' }}>
                    <label for="option_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium">
                        {{ $option->option }}
                    </label>
                </div>
                
                @if($option->is_other_option)
                    <div class="ml-6 mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? '' : 'hidden' }}"
                         id="other_field_{{ $questionId }}_{{ $option->id_questions_options }}">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-2"></i>
                            @if($option->other_before_text)
                                <span class="text-gray-600 mr-2">{{ $option->other_before_text }}</span>
                            @endif
                            <input type="text" 
                                   name="question_{{ $questionId }}_other"
                                   value="{{ $prevOtherAnswer ?? '' }}"
                                   class="flex-grow px-3 py-2 border border-blue-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Sebutkan..."
                                   id="other_{{ $option->id_questions_options }}">
                            @if($option->other_after_text)
                                <span class="text-gray-600 ml-2">{{ $option->other_after_text }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- Multiple Choice Question --}}
@elseif($questionType === 'multiple')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="space-y-3">
            @foreach($question->options as $option)
                <div class="flex items-start p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="checkbox" 
                           name="question_{{ $questionId }}[]" 
                           value="{{ $option->id_questions_options }}"
                           id="multiple_{{ $option->id_questions_options }}"
                           class="multiple-checkbox mt-1 mr-3 text-blue-600 focus:ring-blue-500 rounded"
                           data-question-id="{{ $questionId }}"
                           data-is-other="{{ $option->is_other_option ? '1' : '0' }}"
                           {{ in_array($option->id_questions_options, $prevMultipleAnswers ?? []) ? 'checked' : '' }}>
                    <label for="multiple_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium">
                        {{ $option->option }}
                    </label>
                </div>
                
                @if($option->is_other_option)
                    <div class="ml-6 mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md {{ in_array($option->id_questions_options, $prevMultipleAnswers ?? []) ? '' : 'hidden' }}"
                         id="multiple_other_field_{{ $questionId }}_{{ $option->id_questions_options }}">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-2"></i>
                            @if($option->other_before_text)
                                <span class="text-gray-600 mr-2">{{ $option->other_before_text }}</span>
                            @endif
                            <input type="text" 
                                   name="question_{{ $questionId }}_other_{{ $option->id_questions_options }}"
                                   value="{{ $prevMultipleOtherAnswers[$option->id_questions_options] ?? '' }}"
                                   class="flex-grow px-3 py-2 border border-blue-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Sebutkan..."
                                   id="multiple_other_{{ $option->id_questions_options }}">
                            @if($option->other_after_text)
                                <span class="text-gray-600 ml-2">{{ $option->other_after_text }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- Rating Question --}}
@elseif($questionType === 'rating')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center mb-4">
            <i class="fas fa-star text-yellow-500 mr-2"></i>
            <span class="font-medium text-gray-700">Pilih Rating</span>
        </div>
        <div class="grid gap-3">
            @foreach($question->options as $option)
                @php
                    // Tentukan jumlah bintang berdasarkan text rating
                    $ratingText = strtolower($option->option);
                    $starCount = 1; // default
                    
                    if (str_contains($ratingText, 'kurang')) {
                        $starCount = 1;
                    } elseif (str_contains($ratingText, 'cukup')) {
                        $starCount = 2;
                    } elseif (str_contains($ratingText, 'baik sekali') || str_contains($ratingText, 'sangat baik')) {
                        $starCount = 5;
                    } elseif (str_contains($ratingText, 'baik')) {
                        $starCount = 3;
                    }
                    
                    // Jika menggunakan angka sebagai rating
                    if (is_numeric($option->option)) {
                        $starCount = min(5, max(1, (int)$option->option));
                    }
                @endphp
                
                <div class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <input type="radio" 
                           name="question_{{ $questionId }}" 
                           value="{{ $option->id_questions_options }}"
                           id="rating_{{ $option->id_questions_options }}"
                           class="rating-radio mr-3 text-yellow-500 focus:ring-yellow-500"
                           data-question-id="{{ $questionId }}"
                           {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'checked' : '' }}>
                    <label for="rating_{{ $option->id_questions_options }}" class="cursor-pointer flex items-center flex-grow">
                        <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ isset($prevAnswer) && $prevAnswer == $option->id_questions_options ? 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300' : 'bg-gray-100 text-gray-700 border-2 border-gray-300' }} hover:bg-yellow-50 transition-colors duration-200">
                            {{-- Tampilkan bintang sesuai rating --}}
                            <span class="flex items-center mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $starCount)
                                        <i class="fas fa-star text-yellow-500"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </span>
                            {{ $option->option }}
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

{{-- Scale Question --}}
@elseif($questionType === 'scale')
    <div class="bg-white border border-gray-300 rounded-lg p-4">
        <div class="flex items-center mb-4">
            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
            <span class="font-medium text-gray-700">Skala Penilaian (1-5)</span>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-gray-600 font-medium">{{ $question->before_text ?: 'Sangat Kurang' }}</span>
            <span class="text-sm text-gray-600 font-medium">{{ $question->after_text ?: 'Sangat Baik' }}</span>
        </div>
        <div class="flex items-center justify-between space-x-2">
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
                            <span class="inline-block w-12 h-12 rounded-full border-2 {{ isset($prevAnswer) && $prevAnswer == $scaleOption->id_questions_options ? 'bg-green-500 text-white border-green-500' : 'bg-white border-gray-300' }} text-center leading-10 text-lg font-bold hover:bg-green-50 hover:border-green-300 transition-all duration-200 scale-option">
                                {{ $i }}
                            </span>
                        </label>
                        <span class="text-xs text-gray-500 mt-1">{{ $i }}</span>
                    </div>
                @endif
            @endfor
        </div>
    </div>

{{-- Unknown Question Type --}}
@else
    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
        <div class="text-center text-gray-500">
            <i class="fas fa-question-circle text-2xl mb-2"></i>
            <p>Tipe pertanyaan tidak dikenal: {{ $questionType }}</p>
        </div>
    </div>
@endif