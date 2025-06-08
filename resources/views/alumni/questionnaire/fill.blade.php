@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
    <x-alumni.header title="Kuesioner" />
           
        <!-- Content Section -->
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <div class="flex-1">
                            <p class="font-medium">{{ session('error') }}</p>
                            
                            @if(session('validation_errors'))
                                <div class="mt-3">
                                    <p class="text-sm font-medium mb-2">Pertanyaan yang belum dijawab:</p>
                                    <ul class="text-sm space-y-1 max-h-32 overflow-y-auto">
                                        @foreach(session('validation_errors') as $error)
                                            <li class="flex items-start">
                                                <i class="fas fa-circle text-xs mr-2 mt-1.5"></i>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('dashboard.alumni') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li><a href="{{ route('alumni.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700">Pengisian</li>
                </ol>
            </nav>

            <!-- Progress Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-md p-6 mb-6 border border-blue-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-blue-900">Progress Kuesioner</h2>
                        <p class="text-sm text-blue-700">{{ $currentCategory->category_name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-900">{{ isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1 }}/{{ isset($totalCategories) ? $totalCategories : 1 }}</div>
                        <div class="text-sm text-blue-700">Kategori</div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-blue-900">Progress Keseluruhan</span>
                    <span class="text-sm font-medium text-blue-900">{{ isset($currentCategoryIndex, $totalCategories) ? round((($currentCategoryIndex + 1) / $totalCategories) * 100) : 0 }}%</span>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ isset($currentCategoryIndex, $totalCategories) ? round((($currentCategoryIndex + 1) / $totalCategories) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Category Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                            {{ $currentCategory->category_name }}
                        </h3>
                        <p class="text-gray-600 mt-1">{{ $currentCategory->description ?? 'Silakan jawab pertanyaan berikut dengan lengkap dan jujur.' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-700">
                            <i class="fas fa-user-graduate mr-1"></i>
                            Alumni
                        </span>
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                            {{ count($questions) }} pertanyaan
                        </span>
                    </div>
                </div>
            </div>

            <!-- Questions Form Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-question-circle mr-2 text-blue-600"></i>
                        Daftar Pertanyaan
                    </h4>
                </div>

                <div class="p-6">
                    <form id="questionnaireForm" method="POST" action="{{ route('alumni.questionnaire.submit', $periode->id_periode) }}">
                        @csrf
                        <input type="hidden" name="id_category" value="{{ $currentCategory->id_category }}">
                        <input type="hidden" name="action" id="form-action" value="save_draft">
                        
                        <div class="space-y-8">
                            @foreach($questions as $question)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 question-container {{ $question->depends_on ? 'conditional-question' : '' }}"
                                     id="question-{{ $question->id_question }}"
                                     data-depends-on="{{ $question->depends_on ?? '' }}"
                                     data-depends-value="{{ $question->depends_value ?? '' }}"
                                     style="{{ $question->depends_on ? 'display:none;' : '' }}">
                                    
                                    <!-- Question Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-start">
                                                <span class="bg-blue-600 text-white text-sm font-bold px-3 py-1 rounded-full mr-3 mt-1">{{ $loop->iteration }}</span>
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-lg text-gray-900 leading-relaxed">{{ $question->question }}</h5>
                                                    @if($question->depends_on)
                                                        <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-md p-2">
                                                            <p class="text-xs text-yellow-700 flex items-center">
                                                                <i class="fas fa-link mr-1"></i> 
                                                                <span class="font-medium">Pertanyaan bersyarat</span>
                                                                <span class="ml-2 text-yellow-600">
                                                                    (Muncul jika pertanyaan sebelumnya dijawab dengan nilai tertentu)
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full ml-3">
                                            <i class="fas fa-{{ $question->type == 'text' ? 'keyboard' : ($question->type == 'numeric' ? 'calculator' : ($question->type == 'option' ? 'dot-circle' : ($question->type == 'multiple' ? 'check-square' : ($question->type == 'location' ? 'map-marker-alt' : ($question->type == 'rating' ? 'star' : ($question->type == 'scale' ? 'chart-line' : 'calendar-alt')))))) }} mr-1"></i>
                                            {{ $question->type == 'numeric' ? 'Numeric' : ucfirst($question->type) }}
                                            @if($question->depends_on)
                                                <i class="fas fa-link ml-1 text-yellow-600" title="Pertanyaan Bersyarat"></i>
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Question Content -->
                                    <div class="border-t border-gray-300 pt-4">
                                        @if($question->type == 'text')
                                            <!-- Text question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center flex-wrap">
                                                    @if($question->before_text)
                                                        <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
                                                    @endif
                                                    
                                                    <input type="text" 
                                                           name="answers[{{ $question->id_question }}]" 
                                                           value="{{ $prevAnswers[$question->id_question] ?? '' }}"
                                                           class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-48"
                                                           placeholder="Masukkan jawaban Anda...">
                                                    
                                                    @if($question->after_text)
                                                        <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>
                                        @elseif($question->type == 'numeric')
                                            <!-- Numeric question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center flex-wrap">
                                                    <i class="fas fa-calculator text-green-600 mr-3"></i>
                                                    @if($question->before_text)
                                                        <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
                                                    @endif
                                                    
                                                    <input type="text" 
                                                           name="answers[{{ $question->id_question }}]" 
                                                           value="{{ $prevAnswers[$question->id_question] ?? '' }}"
                                                           class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 min-w-48 numeric-only"
                                                           placeholder="Masukkan angka..."
                                                           pattern="[0-9]*"
                                                           inputmode="numeric">
                                                    
                                                    @if($question->after_text)
                                                        <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="mt-2 text-xs text-gray-500 flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Hanya dapat memasukkan angka (0-9)
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>
                                        @elseif($question->type == 'date')
                                            <!-- Date question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                                                    <input type="date" 
                                                           name="answers[{{ $question->id_question }}]" 
                                                           value="{{ $prevAnswers[$question->id_question] ?? '' }}"
                                                           class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'location')
                                            <!-- Location question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center mb-4">
                                                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                                    <span class="font-medium text-gray-700">Pilih Lokasi</span>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Provinsi:</label>
                                                        <select id="province-{{ $question->id_question }}" 
                                                                class="province-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                data-question-id="{{ $question->id_question }}">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Kota/Kabupaten:</label>
                                                        <select id="city-{{ $question->id_question }}" 
                                                                class="city-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                data-question-id="{{ $question->id_question }}" disabled>
                                                            <option value="">-- Pilih Kota/Kabupaten --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <input type="hidden" 
                                                       name="location_combined[{{ $question->id_question }}]"
                                                       id="location-combined-{{ $question->id_question }}"
                                                       value="{{ isset($prevLocationAnswers[$question->id_question]) ? json_encode($prevLocationAnswers[$question->id_question]) : '' }}">
                                                
                                                <div id="selected-location-{{ $question->id_question }}" 
                                                     class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md {{ isset($prevLocationAnswers[$question->id_question]) && !empty($prevLocationAnswers[$question->id_question]['display']) ? '' : 'hidden' }}">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-map-pin text-green-600 mr-2"></i>
                                                        <div>
                                                            <p class="text-sm text-green-600 font-medium">Lokasi terpilih:</p>
                                                            <p id="location-text-{{ $question->id_question }}" class="font-semibold text-gray-800">
                                                                {{ $prevLocationAnswers[$question->id_question]['display'] ?? '' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>
                                        
                                        @elseif($question->type == 'option')
                                            <!-- Single choice question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="space-y-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-start p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                                            <input type="radio" 
                                                                   name="answers[{{ $question->id_question }}]" 
                                                                   value="{{ $option->id_questions_options }}"
                                                                   id="option_{{ $option->id_questions_options }}"
                                                                   class="option-radio mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                                                   data-question-id="{{ $question->id_question }}"
                                                                   data-is-other="{{ $option->is_other_option }}"
                                                                   {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'checked' : '' }}>
                                                            <label for="option_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium">
                                                                {{ $option->option }}
                                                            </label>
                                                        </div>
                                                        
                                                        @if($option->is_other_option)
                                                            <div class="ml-6 mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? '' : 'hidden' }}"
                                                                 id="other_field_{{ $question->id_question }}_{{ $option->id_questions_options }}">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                                                                    @if($option->other_before_text)
                                                                        <span class="text-gray-600 mr-2">{{ $option->other_before_text }}</span>
                                                                    @endif
                                                                    <input type="text" 
                                                                           name="other_answers[{{ $question->id_question }}]"
                                                                           value="{{ $prevOtherAnswers[$question->id_question] ?? '' }}"
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
                                            <!-- TAMBAHKAN VALIDATION MESSAGE -->
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'multiple')
                                            <!-- Multiple choice question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="space-y-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-start p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                                            <input type="checkbox" 
                                                                   name="multiple[{{ $question->id_question }}][]" 
                                                                   value="{{ $option->id_questions_options }}"
                                                                   id="multiple_{{ $option->id_questions_options }}"
                                                                   class="multiple-checkbox mt-1 mr-3 text-blue-600 focus:ring-blue-500 rounded"
                                                                   data-question-id="{{ $question->id_question }}"
                                                                   data-is-other="{{ $option->is_other_option }}"
                                                                   {{ in_array($option->id_questions_options, $prevMultipleAnswers[$question->id_question] ?? []) ? 'checked' : '' }}>
                                                            <label for="multiple_{{ $option->id_questions_options }}" class="text-gray-700 cursor-pointer flex-grow font-medium">
                                                                {{ $option->option }}
                                                            </label>
                                                        </div>
                                                        
                                                        @if($option->is_other_option)
                                                            <div class="ml-6 mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md {{ in_array($option->id_questions_options, $prevMultipleAnswers[$question->id_question] ?? []) ? '' : 'hidden' }}"
                                                                 id="multiple_other_field_{{ $question->id_question }}_{{ $option->id_questions_options }}">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                                                                    @if($option->other_before_text)
                                                                        <span class="text-gray-600 mr-2">{{ $option->other_before_text }}</span>
                                                                    @endif
                                                                    <input type="text" 
                                                                           name="multiple_other_answers[{{ $question->id_question }}][{{ $option->id_questions_options }}]"
                                                                           value="{{ $prevMultipleOtherAnswers[$question->id_question][$option->id_questions_options] ?? '' }}"
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
                                            <!-- TAMBAHKAN VALIDATION MESSAGE -->
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'rating')
                                            <!-- Rating question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center mb-4">
                                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                                    <span class="font-medium text-gray-700">Pilih Rating</span>
                                                </div>
                                                <div class="grid gap-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                                            <input type="radio" 
                                                                   name="answers[{{ $question->id_question }}]" 
                                                                   value="{{ $option->id_questions_options }}"
                                                                   id="rating_{{ $option->id_questions_options }}"
                                                                   class="rating-radio mr-3 text-yellow-500 focus:ring-yellow-500"
                                                                   data-question-id="{{ $question->id_question }}"
                                                                   {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'checked' : '' }}>
                                                            <label for="rating_{{ $option->id_questions_options }}" class="cursor-pointer flex items-center flex-grow">
                                                                <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300' : 'bg-gray-100 text-gray-700 border-2 border-gray-300' }} hover:bg-yellow-50 transition-colors duration-200">
                                                                    <i class="fas fa-star mr-1"></i>
                                                                    {{ $option->option }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!-- TAMBAHKAN VALIDATION MESSAGE -->
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'scale')
                                            <!-- Scale question -->
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
                                                                       name="answers[{{ $question->id_question }}]" 
                                                                       value="{{ $scaleOption->id_questions_options }}"
                                                                       id="scale_{{ $scaleOption->id_questions_options }}"
                                                                       class="scale-radio hidden"
                                                                       data-question-id="{{ $question->id_question }}"
                                                                       {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $scaleOption->id_questions_options ? 'checked' : '' }}>
                                                                <label for="scale_{{ $scaleOption->id_questions_options }}" class="cursor-pointer">
                                                                    <span class="inline-block w-12 h-12 rounded-full border-2 {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $scaleOption->id_questions_options ? 'bg-blue-500 text-white border-blue-500' : 'bg-white border-gray-300' }} text-center leading-10 text-lg font-bold hover:bg-blue-50 hover:border-blue-300 transition-all duration-200">
                                                                        {{ $i }}
                                                                    </span>
                                                                </label>
                                                                <span class="text-xs text-gray-500 mt-1">{{ $i }}</span>
                                                            </div>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <!-- TAMBAHKAN VALIDATION MESSAGE -->
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation Footer -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    @if($prevCategory)
                                        <!-- ✅ PERBAIKI LINK SEBELUMNYA -->
                                        <button type="button" id="prev-category-btn"
                                               class="inline-flex items-center px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-md transition-colors duration-200">
                                            <i class="fas fa-arrow-left mr-2"></i> 
                                            Sebelumnya
                                        </button>
                                    @endif
                                </div>

                                <div class="flex space-x-3">
                                    <!-- Save Draft Button -->
                                    <button type="button" id="save-draft-btn" 
                                            class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-md transition-colors duration-200">
                                        <i class="fas fa-save mr-2"></i> 
                                        Simpan Draft
                                    </button>

                                    @if($nextCategory)
                                        <!-- Next Category Button -->
                                        <button type="button" id="next-category-btn" 
                                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                                            Selanjutnya 
                                            <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    @else
                                        <!-- Final Submit Button -->
                                        <button type="button" id="submit-final-btn" 
                                                class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                                            <i class="fas fa-check mr-2"></i> 
                                            Selesai
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
            <h3 class="text-xl font-bold mb-2">Konfirmasi Penyelesaian</h3>
            <p class="text-gray-600 mb-4">
                Semua pertanyaan telah dijawab. Apakah Anda yakin ingin menyelesaikan kuesioner ini?
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    <p class="text-sm text-yellow-800">
                        <strong>Perhatian:</strong> Setelah diselesaikan, Anda tidak dapat mengubah jawaban lagi.
                    </p>
                </div>
            </div>
            <div class="flex justify-center space-x-3">
                <button id="modal-cancel" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-md transition-colors duration-200">
                    Batal
                </button>
                <button id="modal-confirm" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-check mr-2"></i>Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Validation Alert Placeholder -->
<div id="validation-alert-container"></div>

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
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Pulsing effect for required questions */
    .question-container.border-red-300 {
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    /* Shake animation for numeric input errors */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Numeric input styling */
    .numeric-only {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        letter-spacing: 1px;
    }

    .numeric-only:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    </style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing alumni questionnaire functionality');
    
    // ✅ PERBAIKAN: Pass data dari backend ke JavaScript dengan benar
    window.questionnaireData = {
        prevAnswers: @json($prevAnswers ?? []),
        prevMultipleAnswers: @json($prevMultipleAnswers ?? []),
        prevLocationAnswers: @json($prevLocationAnswers ?? []),
        prevOtherAnswers: @json($prevOtherAnswers ?? []),
        prevMultipleOtherAnswers: @json($prevMultipleOtherAnswers ?? []),
        conditionalQuestions: @json($conditionalQuestions ?? []),
        currentCategoryIndex: {{ $currentCategoryIndex ?? 0 }},
        totalCategories: {{ $totalCategories ?? 0 }},
        progressPercentage: {{ $progressPercentage ?? 0 }}
    };
    
    console.log('✅ Questionnaire data loaded:', window.questionnaireData);
    
    // ✅ PERBAIKAN: Load saved answers dengan benar setelah DOM ready
    function loadSavedAnswers() {
        console.log('=== LOADING SAVED ANSWERS ===');
        
        // Load regular answers
        Object.entries(window.questionnaireData.prevAnswers).forEach(([questionId, answer]) => {
            const input = document.querySelector(`input[name="answers[${questionId}]"][value="${answer}"]`) ||
                         document.querySelector(`input[name="answers[${questionId}]"]`) ||
                         document.querySelector(`select[name="answers[${questionId}]"]`) ||
                         document.querySelector(`textarea[name="answers[${questionId}]"]`);
            
            if (input) {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = true;
                } else {
                    input.value = answer;
                }
                console.log(`✅ Loaded answer for question ${questionId}:`, answer);
            }
        });
        
        // Load multiple choice answers
        Object.entries(window.questionnaireData.prevMultipleAnswers).forEach(([questionId, answers]) => {
            answers.forEach(answer => {
                const checkbox = document.querySelector(`input[name="multiple[${questionId}][]"][value="${answer}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log(`✅ Loaded multiple choice for question ${questionId}:`, answer);
                }
            });
        });
        
        // Load other answers
        Object.entries(window.questionnaireData.prevOtherAnswers).forEach(([questionId, otherAnswer]) => {
            const otherInput = document.querySelector(`input[name="other_answers[${questionId}]"]`) ||
                              document.querySelector(`input[name="other_answer[${questionId}]"]`) ||
                              document.querySelector(`textarea[name="other_answers[${questionId}]"]`);
            
            if (otherInput) {
                otherInput.value = otherAnswer;
                // Show other input container if hidden
                const otherContainer = otherInput.closest('.other-input-container');
                if (otherContainer) {
                    otherContainer.style.display = 'block';
                }
                console.log(`✅ Loaded other answer for question ${questionId}:`, otherAnswer);
            }
        });
        
        // Load multiple other answers
        Object.entries(window.questionnaireData.prevMultipleOtherAnswers).forEach(([questionId, optionAnswers]) => {
            Object.entries(optionAnswers).forEach(([optionId, otherAnswer]) => {
                const otherInput = document.querySelector(`input[name="multiple_other_answers[${questionId}][${optionId}]"]`) ||
                                  document.querySelector(`textarea[name="multiple_other_answers[${questionId}][${optionId}]"]`);
                
                if (otherInput) {
                    otherInput.value = otherAnswer;
                    // Show other input container if hidden
                    const otherContainer = otherInput.closest('.other-input-container');
                    if (otherContainer) {
                        otherContainer.style.display = 'block';
                    }
                    console.log(`✅ Loaded multiple other answer for question ${questionId}, option ${optionId}:`, otherAnswer);
                }
            });
        });
        
        // Load location answers
        Object.entries(window.questionnaireData.prevLocationAnswers).forEach(([questionId, locationData]) => {
            console.log(`Loading location data for question ${questionId}:`, locationData);
            
            // Set province and city if available
            if (locationData.province_id) {
                const provinceSelect = document.getElementById(`province-${questionId}`);
                if (provinceSelect) {
                    provinceSelect.value = locationData.province_id;
                    // Trigger change to load cities
                    provinceSelect.dispatchEvent(new Event('change'));
                    
                    // Set city after a delay to allow cities to load
                    setTimeout(() => {
                        if (locationData.city_id) {
                            const citySelect = document.getElementById(`city-${questionId}`);
                            if (citySelect) {
                                citySelect.value = locationData.city_id;
                                citySelect.dispatchEvent(new Event('change'));
                            }
                        }
                    }, 1000);
                }
            }
            
            // Update display if available
            if (locationData.display) {
                const locationDisplay = document.getElementById(`location-text-${questionId}`);
                if (locationDisplay) {
                    locationDisplay.textContent = locationData.display;
                }
                
                const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
                if (selectedLocationDiv) {
                    selectedLocationDiv.style.display = 'block';
                }
            }
        });
    }
    
    // GLOBAL FORM ELEMENTS
    const form = document.getElementById('questionnaireForm');
    const formAction = document.getElementById('form-action');

    // LOCATION FUNCTIONS - SIMPLIFIED VERSION
    function initializeLocationQuestions() {
        console.log('=== INITIALIZING LOCATION QUESTIONS ===');
        
        // Find all province selects
        const provinceSelects = document.querySelectorAll('.province-select');
        console.log('Found province selects:', provinceSelects.length);
        
        if (provinceSelects.length === 0) {
            console.log('No location questions found');
            return;
        }
        
        provinceSelects.forEach((provinceSelect, index) => {
            const questionId = provinceSelect.id.replace('province-', '');
            console.log(`Setting up location question ${index + 1}: ${questionId}`);
            
            loadProvincesFromAPI(questionId);
            setupLocationEventListeners(questionId);
        });
    }
    
    function loadProvincesFromAPI(questionId) {
        console.log('Loading provinces from API for question:', questionId);
        
        const provinceSelect = document.getElementById(`province-${questionId}`);
        if (!provinceSelect) {
            console.error('Province select not found for question:', questionId);
            return;
        }
        
        // Show loading
        provinceSelect.innerHTML = '<option value="">-- Memuat provinsi... --</option>';
        
        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(response => {
                console.log('Province API Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(provinces => {
                console.log('Provinces received:', provinces.length);
                
                if (!provinces || !Array.isArray(provinces)) {
                    throw new Error('Invalid provinces data');
                }
                
                // Clear and populate provinces
                provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                
                provinces.forEach(province => {
                    if (province && province.id && province.name) {
                        const option = document.createElement('option');
                        option.value = province.id;
                        option.textContent = province.name;
                        option.setAttribute('data-name', province.name);
                        provinceSelect.appendChild(option);
                    }
                });
                
                console.log(`Successfully loaded ${provinces.length} provinces for question ${questionId}`);
                
                // Load saved data after provinces are populated
                setTimeout(() => {
                    loadSavedLocationData(questionId);
                }, 200);
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
                
                // Fallback to manual data
                console.log('Using fallback province data');
                loadFallbackProvinces(provinceSelect, questionId);
            });
    }
    
    function loadFallbackProvinces(provinceSelect, questionId) {
        const fallbackProvinces = [
            { id: '11', name: 'Aceh' },
            { id: '12', name: 'Sumatera Utara' },
            { id: '13', name: 'Sumatera Barat' },
            { id: '14', name: 'Riau' },
            { id: '15', name: 'Jambi' },
            { id: '16', name: 'Sumatera Selatan' },
            { id: '17', name: 'Bengkulu' },
            { id: '18', name: 'Lampung' },
            { id: '19', name: 'Kepulauan Bangka Belitung' },
            { id: '21', name: 'Kepulauan Riau' },
            { id: '31', name: 'DKI Jakarta' },
            { id: '32', name: 'Jawa Barat' },
            { id: '33', name: 'Jawa Tengah' },
            { id: '34', name: 'DI Yogyakarta' },
            { id: '35', name: 'Jawa Timur' },
            { id: '36', name: 'Banten' },
            { id: '51', name: 'Bali' },
            { id: '52', name: 'Nusa Tenggara Barat' },
            { id: '53', name: 'Nusa Tenggara Timur' },
            { id: '61', name: 'Kalimantan Barat' },
            { id: '62', name: 'Kalimantan Tengah' },
            { id: '63', name: 'Kalimantan Selatan' },
            { id: '64', name: 'Kalimantan Timur' },
            { id: '65', name: 'Kalimantan Utara' },
            { id: '71', name: 'Sulawesi Utara' },
            { id: '72', name: 'Sulawesi Tengah' },
            { id: '73', name: 'Sulawesi Selatan' },
            { id: '74', name: 'Sulawesi Tenggara' },
            { id: '75', name: 'Gorontalo' },
            { id: '76', name: 'Sulawesi Barat' },
            { id: '81', name: 'Maluku' },
            { id: '82', name: 'Maluku Utara' },
            { id: '91', name: 'Papua Barat' },
            { id: '94', name: 'Papua' }
        ];
        
        provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
        
        fallbackProvinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = province.name;
            option.setAttribute('data-name', province.name);
            provinceSelect.appendChild(option);
        });
        
        console.log(`Loaded ${fallbackProvinces.length} fallback provinces for question ${questionId}`);
        
        // Load saved data
        setTimeout(() => {
            loadSavedLocationData(questionId);
        }, 200);
    }
    
    function setupLocationEventListeners(questionId) {
        console.log('Setting up event listeners for question:', questionId);
        
        const provinceSelect = document.getElementById(`province-${questionId}`);
        const citySelect = document.getElementById(`city-${questionId}`);
        
        if (!provinceSelect || !citySelect) {
            console.error('Province or city select not found for question:', questionId);
            return;
        }
        
        // Province change event
        provinceSelect.addEventListener('change', function() {
            console.log('Province changed:', this.value, 'for question:', questionId);
            handleProvinceChange(questionId, this.value);
        });
        
        // City change event
        citySelect.addEventListener('change', function() {
            console.log('City changed:', this.value, 'for question:', questionId);
            updateLocationDisplay(questionId);
        });
        
        console.log('Event listeners set up successfully for question:', questionId);
    }
    
    function handleProvinceChange(questionId, provinceId) {
        console.log(`Province changed for question ${questionId}: ${provinceId}`);
        
        const citySelect = document.getElementById(`city-${questionId}`);
        if (!citySelect) {
            console.error('City select not found for question:', questionId);
            return;
        }
        
        // Reset city select
        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
        citySelect.disabled = true;
        
        // Clear location display and hidden input
        const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
        const locationCombinedInput = document.getElementById(`location-combined-${questionId}`);
        
        if (selectedLocationDiv) {
            selectedLocationDiv.classList.add('hidden');
        }
        if (locationCombinedInput) {
            locationCombinedInput.value = '';
        }
        
        if (!provinceId) {
            console.log('No province selected, city select disabled');
            return;
        }
        
        // Show loading
        citySelect.innerHTML = '<option value="">-- Memuat kota/kabupaten... --</option>';
        
        const apiUrl = `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`;
        console.log('Fetching cities from:', apiUrl);
        
        fetch(apiUrl)
            .then(response => {
                console.log('City API Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(cities => {
                console.log('Cities received:', cities.length);
                
                if (!cities || !Array.isArray(cities)) {
                    throw new Error('Invalid cities data received');
                }
                
                if (cities.length === 0) {
                    console.warn('No cities found for province:', provinceId);
                    citySelect.innerHTML = '<option value="">-- Tidak ada kota/kabupaten --</option>';
                    return;
                }
                
                // Populate city options
                citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                
                cities.forEach(city => {
                    if (city && city.id && city.name) {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        option.setAttribute('data-name', city.name);
                        citySelect.appendChild(option);
                    }
                });
                
                citySelect.disabled = false;
                console.log(`Successfully loaded ${cities.length} cities for province ${provinceId}`);
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                
                // Use fallback cities
                const fallbackCities = getFallbackCities(provinceId);
                if (fallbackCities && fallbackCities.length > 0) {
                    console.log('Using fallback cities for province:', provinceId);
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    
                    fallbackCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        option.setAttribute('data-name', city.name);
                        citySelect.appendChild(option);
                    });
                    
                    citySelect.disabled = false;
                } else {
                    citySelect.innerHTML = '<option value="">-- Error memuat kota/kabupaten --</option>';
                    citySelect.disabled = true;
                }
            });
    }
    
    function getFallbackCities(provinceId) {
        const fallbackData = {
            '11': [
                { id: '1101', name: 'Kabupaten Simeulue' },
                { id: '1102', name: 'Kabupaten Aceh Singkil' },
                { id: '1171', name: 'Kota Banda Aceh' },
                { id: '1172', name: 'Kota Sabang' }
            ],
            '31': [
                { id: '3101', name: 'Kabupaten Kepulauan Seribu' },
                { id: '3171', name: 'Kota Jakarta Selatan' },
                { id: '3172', name: 'Kota Jakarta Timur' },
                { id: '3173', name: 'Kota Jakarta Pusat' },
                { id: '3174', name: 'Kota Jakarta Barat' },
                { id: '3175', name: 'Kota Jakarta Utara' }
            ],
            '34': [
                { id: '3401', name: 'Kabupaten Kulon Progo' },
                { id: '3402', name: 'Kabupaten Bantul' },
                { id: '3403', name: 'Kabupaten Gunung Kidul' },
                { id: '3404', name: 'Kabupaten Sleman' },
                { id: '3471', name: 'Kota Yogyakarta' }
            ]
        };
        
        return fallbackData[provinceId] || null;
    }
    
    function updateLocationDisplay(questionId) {
        console.log('Updating location display for question:', questionId);
        
        const provinceSelect = document.getElementById(`province-${questionId}`);
        const citySelect = document.getElementById(`city-${questionId}`);
        const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
        const locationTextSpan = document.getElementById(`location-text-${questionId}`);
        const locationCombinedInput = document.getElementById(`location-combined-${questionId}`);
        
        if (!provinceSelect || !citySelect || !selectedLocationDiv || !locationTextSpan || !locationCombinedInput) {
            console.error('Location elements not found for question:', questionId);
            return;
        }
        
        const provinceId = provinceSelect.value;
        const cityId = citySelect.value;
        
        if (!provinceId || !cityId) {
            selectedLocationDiv.classList.add('hidden');
            locationCombinedInput.value = '';
            return;
        }
        
        // Get selected text values
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex].text;
        const cityName = citySelect.options[citySelect.selectedIndex].text;
        
        const displayText = `${cityName}, ${provinceName}`;
        
        // Create location data object
        const locationData = {
            province_id: provinceId,
            province_name: provinceName,
            city_id: cityId,
            city_name: cityName,
            display: displayText
        };
        
        // Update display
        locationTextSpan.textContent = displayText;
        selectedLocationDiv.classList.remove('hidden');
        
        // Update hidden input
        locationCombinedInput.value = JSON.stringify(locationData);
        
        console.log('Location updated:', locationData);
    }
    
    function loadSavedLocationData(questionId) {
        console.log('Loading saved location data for question:', questionId);
        
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        if (!combinedInput || !combinedInput.value) {
            console.log('No saved location data found for question:', questionId);
            return;
        }
        
        try {
            const savedLocation = JSON.parse(combinedInput.value);
            console.log('Found saved location data:', savedLocation);
            
            const provinceSelect = document.getElementById(`province-${questionId}`);
            const citySelect = document.getElementById(`city-${questionId}`);
            
            if (!provinceSelect || !citySelect) {
                console.error('Province or city select not found for loading saved data');
                return;
            }
            
            // Set province
            if (savedLocation.province_id) {
                provinceSelect.value = savedLocation.province_id;
                
                // Load cities for this province
                handleProvinceChange(questionId, savedLocation.province_id);
                
                // Set city after a delay to ensure cities are loaded
                setTimeout(() => {
                    if (savedLocation.city_id) {
                        citySelect.value = savedLocation.city_id;
                        updateLocationDisplay(questionId);
                    }
                }, 500);
            }
            
            // Show saved display immediately if available
            if (savedLocation.display) {
                const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
                const locationTextSpan = document.getElementById(`location-text-${questionId}`);
                
                if (selectedLocationDiv && locationTextSpan) {
                    locationTextSpan.textContent = savedLocation.display;
                    selectedLocationDiv.classList.remove('hidden');
                }
            }
            
        } catch (error) {
            console.error('Error parsing saved location data:', error);
        }
    }

    // CONDITIONAL QUESTIONS FUNCTION
    function initializeConditionalQuestions() {
        console.log('Initializing conditional questions...');
        
        // Handle radio button changes for single choice questions
        document.querySelectorAll('.option-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
                
                console.log('Radio button changed:', {
                    questionId: questionId,
                    optionValue: this.value,
                    isOther: isOther,
                    radioElement: this
                });
                
                // Hide all other fields for this question first
                document.querySelectorAll(`[id^="other_field_${questionId}_"]`).forEach(field => {
                    field.classList.add('hidden');
                    const input = field.querySelector('input[type="text"]');
                    if (input) {
                        input.value = '';
                        // Remove required attribute when hiding
                        input.removeAttribute('required');
                        // ✅ PERBAIKAN: JANGAN hapus name attribute, cukup disable
                        input.disabled = true;
                        
                        console.log('Hidden other field:', {
                            fieldId: field.id,
                            inputName: input.name,
                            disabled: input.disabled
                        });
                    }
                });
                
                // Show this other field if applicable
                if (isOther) {
                    const otherField = document.getElementById(`other_field_${questionId}_${this.value}`);
                    console.log('Looking for other field:', `other_field_${questionId}_${this.value}`, otherField);
                    
                    if (otherField) {
                        otherField.classList.remove('hidden');
                        const otherInput = otherField.querySelector('input[type="text"]');
                        if (otherInput) {
                            // ✅ PERBAIKAN CRITICAL: Pastikan name attribute SELALU konsisten
                            const correctName = `other_answers[${questionId}]`; // Gunakan plural untuk konsistensi
                            otherInput.setAttribute('name', correctName);
                            otherInput.setAttribute('required', 'required');
                            otherInput.disabled = false; // Enable input
                            
                            console.log('Other input configured:', {
                                questionId: questionId,
                                optionValue: this.value,
                                inputName: otherInput.getAttribute('name'),
                                correctName: correctName,
                                inputId: otherInput.id,
                                hasName: otherInput.hasAttribute('name'),
                                nameValue: otherInput.name,
                                disabled: otherInput.disabled,
                                required: otherInput.required
                            });
                            
                            // Focus on the input
                            setTimeout(() => otherInput.focus(), 100);
                        }
                    } else {
                        console.error('Other field not found for:', `other_field_${questionId}_${this.value}`);
                    }
                }
                
                // Handle conditional questions
                handleDependentQuestions(questionId, this.value);
            });
        });

        // Handle checkbox changes for multiple choice questions
        document.querySelectorAll('.multiple-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const optionId = this.value;
                const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
                
                console.log('Multiple checkbox changed:', {
                    questionId: questionId,
                    optionId: optionId,
                    isOther: isOther,
                    checked: this.checked
                });
                
                if (isOther) {
                    const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionId}`);
                    console.log('Looking for multiple other field:', `multiple_other_field_${questionId}_${optionId}`, otherField);
                    
                    if (otherField) {
                        const otherInput = otherField.querySelector('input[type="text"]');
                        
                        if (this.checked) {
                            // Show and enable the other field
                            otherField.classList.remove('hidden');
                            if (otherInput) {
                                // ✅ PERBAIKAN CRITICAL: Pastikan name attribute konsisten untuk multiple choice
                                const correctName = `multiple_other_answers[${questionId}][${optionId}]`;
                                otherInput.setAttribute('name', correctName);
                                otherInput.setAttribute('required', 'required');
                                otherInput.disabled = false;
                                
                                console.log('Multiple other input enabled:', {
                                    questionId: questionId,
                                    optionId: optionId,
                                    inputName: otherInput.getAttribute('name'),
                                    correctName: correctName,
                                    disabled: otherInput.disabled,
                                    required: otherInput.required
                                });
                                
                                // Focus on the input
                                setTimeout(() => otherInput.focus(), 100);
                            }
                        } else {
                            // Hide and disable the other field
                            otherField.classList.add('hidden');
                            if (otherInput) {
                                otherInput.value = '';
                                otherInput.removeAttribute('required');
                                otherInput.disabled = true;
                                
                                console.log('Multiple other input disabled:', {
                                    questionId: questionId,
                                    optionId: optionId,
                                    inputName: otherInput.name,
                                    disabled: otherInput.disabled
                                });
                            }
                        }
                    } else {
                        console.error('Multiple other field not found for:', `multiple_other_field_${questionId}_${optionId}`);
                    }
                }
                
                // Handle dependencies for multiple choice
                const checkedValues = [];
                document.querySelectorAll(`input[name="multiple[${questionId}][]"]:checked`).forEach(cb => {
                    checkedValues.push(cb.value);
                });
                
                checkedValues.forEach(value => {
                    handleDependentQuestions(questionId, value);
                });
            });
        });
        
        // Tambahkan event listeners untuk rating dan scale questions

        // Handle rating changes
        document.querySelectorAll('.rating-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const selectedValue = this.value;
                
                console.log('Rating changed:', {
                    questionId: questionId,
                    selectedValue: selectedValue
                });
                
                // Handle conditional questions
                handleDependentQuestions(questionId, selectedValue);
            });
        });

        // Handle scale changes
        document.querySelectorAll('.scale-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const selectedValue = this.value;
                
                console.log('Scale changed:', {
                    questionId: questionId,
                    selectedValue: selectedValue
                });
                
                // Handle conditional questions
                handleDependentQuestions(questionId, selectedValue);
            });
        });
        
        // Initialize dependencies on page load
        initializeDependenciesOnLoad();
    }

    function initializeDependenciesOnLoad() {
        console.log('=== INITIALIZING DEPENDENCIES ON LOAD ===');
        
        // ✅ TAMBAHKAN DELAY UNTUK MEMASTIKAN DOM READY
        setTimeout(() => {
            // First, check all existing answers and trigger conditional questions
            document.querySelectorAll('.option-radio:checked, .rating-radio:checked, .scale-radio:checked').forEach(radio => {
                const questionId = radio.getAttribute('data-question-id');
                const selectedValue = radio.value;
                
                console.log('Found checked radio on load:', {
                    questionId: questionId,
                    selectedValue: selectedValue
                });
                
                handleDependentQuestions(questionId, selectedValue);
            });
            
            // Also check for multiple choice questions (if any affect dependencies)
            document.querySelectorAll('.multiple-checkbox:checked').forEach(checkbox => {
                const questionId = checkbox.getAttribute('data-question-id');
                const selectedValue = checkbox.value;
                
                console.log('Found checked checkbox on load:', {
                    questionId: questionId,
                    selectedValue: selectedValue
                });
                
                handleDependentQuestions(questionId, selectedValue);
            });
            
            // ✅ SHOW CONDITIONAL QUESTIONS THAT SHOULD BE VISIBLE BASED ON EXISTING ANSWERS
            const conditionalQuestions = document.querySelectorAll('.conditional-question');
            console.log('Found conditional questions:', conditionalQuestions.length);
            
            conditionalQuestions.forEach(question => {
                const dependsOn = question.getAttribute('data-depends-on');
                const dependsValue = question.getAttribute('data-depends-value');
                const questionId = question.id.replace('question-', '');
                
                console.log('Checking conditional question:', {
                    questionId: questionId,
                    dependsOn: dependsOn,
                    dependsValue: dependsValue
                });
                
                // ✅ PERBAIKI CARA MENGECEK PARENT QUESTION
                // Check if parent question has the required answer
                const parentRadios = document.querySelectorAll(`input[data-question-id="${dependsOn}"]`);
                let parentValue = null;
                
                parentRadios.forEach(radio => {
                    if (radio.checked) {
                        parentValue = radio.value;
                    }
                });
                
                console.log('Parent question state:', {
                    parentQuestionId: dependsOn,
                    parentValue: parentValue,
                    requiredValue: dependsValue,
                    shouldShow: parentValue == dependsValue
                });
                
                if (parentValue == dependsValue) {
                    question.style.display = 'block';
                    
                    // Enable all inputs
                    const inputs = question.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.disabled = false;
                    });
                    
                    console.log(`✅ Showed conditional question ${questionId} on load`);
                } else {
                    question.style.display = 'none';
                    
                    // Disable all inputs
                    const inputs = question.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.disabled = true;
                    });
                    
                    console.log(`❌ Hidden conditional question ${questionId} on load`);
                }
            });
        }, 500); // Delay 500ms untuk memastikan semua sudah ter-load
    }

    // ✅ PERBAIKI FUNCTION handleDependentQuestions
    function handleDependentQuestions(parentQuestionId, selectedValue) {
        console.log(`=== HANDLING DEPENDENT QUESTIONS ===`);
        console.log(`Parent Question ID: ${parentQuestionId}, Selected Value: ${selectedValue}`);
        
        const dependentQuestions = document.querySelectorAll(`.conditional-question[data-depends-on="${parentQuestionId}"]`);
        
        console.log(`Found ${dependentQuestions.length} dependent questions for parent ${parentQuestionId}`);
        
        dependentQuestions.forEach((dependentQuestion, index) => {
            const dependsValue = dependentQuestion.getAttribute('data-depends-value');
            const questionId = dependentQuestion.id.replace('question-', '');
            
            console.log(`Processing dependent question ${index + 1}:`, {
                questionId: questionId,
                dependsValue: dependsValue,
                selectedValue: selectedValue,
                shouldShow: dependsValue == selectedValue
            });
            
            if (dependsValue == selectedValue) {
                // ✅ SHOW THE QUESTION
                dependentQuestion.style.display = 'block';
                
                // Enable all inputs
                const inputs = dependentQuestion.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                });
                
                console.log(`✅ Showed dependent question ${questionId}`);
            } else {
                // ✅ HIDE THE QUESTION
                dependentQuestion.style.display = 'none';
                
                // Disable all inputs and clear their values
                const inputs = dependentQuestion.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    
                    // Clear values when hiding
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                    
                    // Hide any other fields related to this input
                    if (input.type === 'radio' && input.getAttribute('data-is-other') === '1') {
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
                
                console.log(`❌ Hidden dependent question ${questionId}`);
            }
        });
    }

    // ✅ TAMBAHKAN FUNCTION DEBUG UNTUK TROUBLESHOOTING
    function debugConditionalQuestions() {
        console.log('=== DEBUG CONDITIONAL QUESTIONS ===');
        
        const allQuestions = document.querySelectorAll('.question-container');
        console.log(`Total questions: ${allQuestions.length}`);
        
        const conditionalQuestions = document.querySelectorAll('.conditional-question');
        console.log(`Conditional questions: ${conditionalQuestions.length}`);
        
        conditionalQuestions.forEach((question, index) => {
            const questionId = question.id.replace('question-', '');
            const dependsOn = question.getAttribute('data-depends-on');
            const dependsValue = question.getAttribute('data-depends-value');
            const isVisible = question.style.display !== 'none';
            
            // Check parent question status
            const parentRadios = document.querySelectorAll(`input[data-question-id="${dependsOn}"]`);
            let parentValue = null;
            
            parentRadios.forEach(radio => {
                if (radio.checked) {
                    parentValue = radio.value;
                }
            });
            
            console.log(`Conditional Question ${index + 1}:`, {
                questionId: questionId,
                dependsOn: dependsOn,
                dependsValue: dependsValue,
                parentValue: parentValue,
                isVisible: isVisible,
                shouldBeVisible: parentValue == dependsValue
            });
        });
        
        // Check all checked radios
        const checkedRadios = document.querySelectorAll('input[type="radio"]:checked');
        console.log('Currently checked radios:');
        checkedRadios.forEach(radio => {
            console.log({
                questionId: radio.getAttribute('data-question-id'),
                value: radio.value,
                name: radio.name
            });
        });
    }

    // INITIALIZE ALL FUNCTIONALITY
    console.log('Initializing all questionnaire functionality...');
    initializeLocationQuestions();
    initializeConditionalQuestions();
    
    // ✅ LOAD SAVED ANSWERS AFTER INITIALIZATION
    setTimeout(() => {
        console.log('Loading saved answers...');
        loadSavedAnswers();
        debugConditionalQuestions();
    }, 1500); // Increased delay to ensure all data is loaded
    
    console.log('Alumni questionnaire initialization complete!');

    // FORM SUBMISSION HANDLERS
    document.getElementById('save-draft-btn')?.addEventListener('click', function() {
        console.log('Saving draft...');
        const actionInput = document.querySelector('input[name="action"]');
        if (actionInput) {
            actionInput.value = 'save_draft';
        }
        form.submit();
    });

    document.getElementById('prev-category-btn')?.addEventListener('click', function() {
        console.log('Going to previous category...');
        const actionInput = document.querySelector('input[name="action"]');
        if (actionInput) {
            actionInput.value = 'prev_category';
        }
        form.submit();
    });

    document.getElementById('next-category-btn')?.addEventListener('click', function() {
        console.log('Attempting to go to next category...');
        if (validateCurrentCategory()) {
            const actionInput = document.querySelector('input[name="action"]');
            if (actionInput) {
                actionInput.value = 'next_category';
            }
            form.submit();
        }
    });

    document.getElementById('submit-final-btn')?.addEventListener('click', function() {
        console.log('Attempting final submission...');
        if (validateCurrentCategory()) {
            // Show confirmation modal
            document.getElementById('confirmation-modal').classList.remove('hidden');
        }
    });

    // ✅ TAMBAHKAN FUNCTION VALIDASI LENGKAP
    function validateCurrentCategory() {
        console.log('=== VALIDATING CURRENT CATEGORY ===');
        
        const validationErrors = [];
        let hasErrors = false;
        
        // Clear previous validation styles
        document.querySelectorAll('.question-container').forEach(container => {
            container.classList.remove('border-red-300');
            const validationMsg = container.querySelector('.validation-message');
            if (validationMsg) {
                validationMsg.classList.add('hidden');
                validationMsg.textContent = '';
            }
        });
        
        // ✅ HANYA VALIDASI PERTANYAAN YANG VISIBLE DAN TIDAK DISABLED
        const visibleQuestions = document.querySelectorAll('.question-container:not([style*="display: none"]):not([style*="display:none"])');
        
        visibleQuestions.forEach((questionContainer, index) => {
            const questionId = questionContainer.id.replace('question-', '');
            const questionTitle = questionContainer.querySelector('h5').textContent.trim();
            
            // ✅ CEK APAKAH PERTANYAAN CONDITIONAL DAN TIDAK VISIBLE
            const isConditional = questionContainer.classList.contains('conditional-question');
            const isCurrentlyVisible = questionContainer.style.display !== 'none';
            
            console.log(`Validating question ${index + 1}: ${questionId}`, {
                isConditional,
                isCurrentlyVisible,
                displayStyle: questionContainer.style.display
            });
            
            // ✅ SKIP VALIDASI JIKA PERTANYAAN CONDITIONAL DAN TIDAK VISIBLE
            if (isConditional && !isCurrentlyVisible) {
                console.log(`Skipping validation for conditional question ${questionId} - not visible`);
                return;
            }
            
            // ✅ CEK APAKAH ADA INPUT YANG DISABLED (CONDITIONAL YANG TIDAK AKTIF)
            const allInputs = questionContainer.querySelectorAll('input, select, textarea');
            const hasDisabledInputs = Array.from(allInputs).some(input => input.disabled);
            
            if (hasDisabledInputs) {
                console.log(`Skipping validation for question ${questionId} - has disabled inputs`);
                return;
            }
            
            // Check if question is required (assume all visible questions are required)
            const isRequired = true;
            
            if (!isRequired) {
                console.log(`Question ${questionId} is not required, skipping validation`);
                return;
            }
            
            let isAnswered = false;
            let errorMessage = '';
            
            // Check different question types
            const textInput = questionContainer.querySelector('input[type="text"]:not([name*="other"]):not([name*="multiple_other"]):not(:disabled)');
            const dateInput = questionContainer.querySelector('input[type="date"]:not(:disabled)');
            const radioInputs = questionContainer.querySelectorAll('input[type="radio"]:not(:disabled)');
            const checkboxInputs = questionContainer.querySelectorAll('input[type="checkbox"]:not(:disabled)');
            const locationInput = questionContainer.querySelector('input[name^="location_combined"]:not(:disabled)');
            
            if (textInput && !dateInput && !locationInput) {
                // Text or Numeric question
                const isNumericQuestion = textInput.classList.contains('numeric-only');
                
                if (isNumericQuestion) {
                    // Numeric validation
                    const numericValue = textInput.value.trim();
                    isAnswered = numericValue !== '' && /^\d+$/.test(numericValue);
                    errorMessage = 'Pertanyaan ini harus dijawab dengan angka';
                } else {
                    // Text validation
                    isAnswered = textInput.value.trim() !== '';
                    errorMessage = 'Pertanyaan ini harus dijawab';
                }
                
            } else if (dateInput) {
                // Date question
                isAnswered = dateInput.value !== '';
                errorMessage = 'Tanggal harus dipilih';
                
            } else if (radioInputs.length > 0) {
                // Radio/option/rating/scale question
                isAnswered = Array.from(radioInputs).some(radio => radio.checked);
                errorMessage = 'Pilihan harus dipilih';
                
                // Check if "other" option is selected and requires text input
                const checkedRadio = Array.from(radioInputs).find(radio => radio.checked);
                if (checkedRadio && checkedRadio.getAttribute('data-is-other') === '1') {
                    const otherField = questionContainer.querySelector(`#other_field_${questionId}_${checkedRadio.value}`);
                    if (otherField && !otherField.classList.contains('hidden')) {
                        const otherInput = otherField.querySelector('input[type="text"]:not(:disabled)');
                        if (!otherInput || otherInput.value.trim() === '') {
                            isAnswered = false;
                            errorMessage = 'Jawaban "Lainnya" harus diisi';
                        }
                    }
                }
                
            } else if (checkboxInputs.length > 0) {
                // Multiple choice question
                const checkedBoxes = Array.from(checkboxInputs).filter(cb => cb.checked);
                isAnswered = checkedBoxes.length > 0;
                errorMessage = 'Minimal satu pilihan harus dipilih';
                
                // ✅ TAMBAHAN: Validasi "other" fields untuk multiple choice
                if (isAnswered) {
                    let allOtherFieldsValid = true;
                    
                    checkedBoxes.forEach(checkbox => {
                        const isOtherOption = parseInt(checkbox.getAttribute('data-is-other')) === 1;
                        if (isOtherOption) {
                            const optionId = checkbox.value;
                            const otherField = questionContainer.querySelector(`#multiple_other_field_${questionId}_${optionId}`);
                            
                            if (otherField && !otherField.classList.contains('hidden')) {
                                const otherInput = otherField.querySelector('input[type="text"]');
                                if (otherInput && otherInput.value.trim() === '') {
                                    allOtherFieldsValid = false;
                                    errorMessage = 'Isian "Lainnya" harus diisi';
                                }
                            }
                        }
                    });
                    
                    isAnswered = allOtherFieldsValid;
                }
                
            } else if (locationInput) {
                // Location question
                isAnswered = locationInput.value.trim() !== '';
                errorMessage = 'Lokasi harus dipilih';
            }
            
            console.log(`Question ${questionId} validation result:`, {
                isRequired,
                isAnswered,
                errorMessage,
                isConditional,
                isCurrentlyVisible
            });
            
            if (isRequired && !isAnswered) {
                hasErrors = true;
                validationErrors.push(`${index + 1}. ${questionTitle}`);
                
                // Add visual indication
                questionContainer.classList.add('border-red-300');
                
                // Show error message
                const validationMsg = questionContainer.querySelector('.validation-message');
                if (validationMsg) {
                    validationMsg.textContent = errorMessage;
                    validationMsg.classList.remove('hidden');
                }
            }
        });
        
        if (hasErrors) {
            console.log('Validation failed:', validationErrors);
            
            // Scroll to first error
            const firstErrorQuestion = document.querySelector('.question-container.border-red-300');
            if (firstErrorQuestion) {
                firstErrorQuestion.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            // Show validation alert
            showValidationAlert(validationErrors);
            
            return false;
        }
        
        console.log('Validation passed!');
        return true;
    }

    // ✅ TAMBAHKAN FUNCTION ALERT VALIDASI
    function showValidationAlert(errors) {
        // Remove existing alert
        const existingAlert = document.getElementById('validation-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertHtml = `
            <div id="validation-alert" 
                 role="alert"
                 aria-live="polite"
                 class="fixed top-6 right-6 w-96 bg-white border-l-4 border-red-500 rounded-lg shadow-xl transform transition-all duration-300 ease-out translate-x-full">
                <div class="p-5">
                    <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-500"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="mb-3">
                                <h4 class="text-lg font-semibold text-gray-900 leading-tight">
                                    Pertanyaan Belum Dijawab
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    Silakan jawab pertanyaan berikut:
                                </p>
                            </div>

                            <!-- Scrollable Error List -->
                            <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                <ul class="space-y-2">
                                    ${errors.map(error => `
                                        <li class="flex items-start text-sm text-gray-700">
                                            <i class="fas fa-circle text-[0.35rem] text-red-400 mt-1.5 mr-2"></i>
                                            <span class="flex-1">${error}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button onclick="dismissValidationAlert(this)" 
                                class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
                                aria-label="Tutup pesan">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Insert alert
        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Trigger animation
        requestAnimationFrame(() => {
            const alert = document.getElementById('validation-alert');
            alert.classList.remove('translate-x-full');
            alert.classList.add('translate-x-0');
        });

        // Auto dismiss
        setTimeout(() => {
            dismissValidationAlert(document.querySelector('#validation-alert button'));
        }, 8000);
    }

    function dismissValidationAlert(button) {
        const alert = button.closest('#validation-alert');
        
        // Animate out
        alert.classList.add('translate-x-full', 'opacity-0');
        
        // Remove after animation
        setTimeout(() => alert.remove(), 300);
    }

    // Add required styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 2px;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    // Add numeric-only input restriction for questionnaire filling
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('numeric-only')) {
            // Remove any non-numeric characters
            let value = e.target.value.replace(/[^0-9]/g, '');
            
            // Update the input value
            if (e.target.value !== value) {
                e.target.value = value;
                
                // Show brief feedback for invalid characters
                showNumericInputFeedback(e.target);
            }
        }
    });
    
    // Prevent non-numeric key presses on numeric-only inputs
    document.addEventListener('keypress', function(e) {
        if (e.target.classList.contains('numeric-only')) {
            // Allow: backspace, delete, tab, escape, enter, arrow keys
            if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+Z
                (e.ctrlKey && [65, 67, 86, 88, 90].indexOf(e.keyCode) !== -1)) {
                return;
            }
            
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                
                // Show feedback for blocked characters
                showNumericInputFeedback(e.target, true);
            }
        }
    });
    
    // Function to show numeric input feedback
    function showNumericInputFeedback(input, isBlocked = false) {
        // Remove existing feedback
        const existingFeedback = input.parentNode.querySelector('.numeric-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Create feedback element
        const feedback = document.createElement('div');
        feedback.className = 'numeric-feedback absolute top-full left-0 mt-1 px-2 py-1 bg-red-100 text-red-600 text-xs rounded shadow-sm border border-red-200 z-10';
        feedback.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Hanya angka yang diperbolehkan';
        
        // Make parent relative if not already
        if (getComputedStyle(input.parentNode).position === 'static') {
            input.parentNode.style.position = 'relative';
        }
        
        // Add feedback
        input.parentNode.appendChild(feedback);
        
        // Auto remove after 2 seconds
        setTimeout(() => {
            if (feedback.parentNode) {
                feedback.remove();
            }
        }, 2000);
        
        // Add a slight shake animation to the input
        input.style.animation = 'shake 0.3s ease-in-out';
        setTimeout(() => {
            input.style.animation = '';
        }, 300);
    }
    document.head.appendChild(styleSheet);

    
});
</script>
<!-- script JS  -->
<script src="{{ asset('./js/alumni.js') }}"></script>
@endsection