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
                    <li><span class="text-gray-500">/</span></li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a>
                    </li>
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
                        <div class="text-2xl font-bold text-blue-900">
                            {{ isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1 }}/{{ $allCategories->count() }}
                        </div>
                        <div class="text-sm text-blue-700">Kategori</div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-blue-900">Progress Keseluruhan</span>
                    <span class="text-sm font-medium text-blue-900">
                        {{ round(((isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1) / $allCategories->count()) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-3">
                    <div
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300"
                        style="width: {{ round(((isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1) / $allCategories->count()) * 100) }}%"
                    ></div>
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
                        <p class="text-gray-600 mt-1">
                            {{ $currentCategory->description ?? 'Silakan jawab pertanyaan berikut dengan lengkap dan jujur.' }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                            <i class="fas fa-building mr-1"></i>
                            Perusahaan
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
                    <form id="questionnaireForm" method="POST" action="{{ route('company.questionnaire.submit', [$periode->id_periode]) }}">
                        @csrf
                        <div class="mb-6">
                            <label for="alumni_nim" class="block text-sm font-medium text-gray-700">Pilih Alumni yang Dinilai</label>
                            <select name="alumni_nim" id="alumni_nim" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                <option value="">-- Pilih Alumni --</option>
                                @foreach($alumniList as $alumni)
                                    <option value="{{ $alumni->nim }}" {{ (old('alumni_nim') ?? request('alumni_nim')) == $alumni->nim ? 'selected' : '' }}>
                                        {{ $alumni->name ?? $alumni->nama }} ({{ $alumni->nim }})
                                    </option>
                                @endforeach
                            </select>
                            @if(session('error'))
                                <div class="text-red-500 text-sm mt-1">{{ session('error') }}</div>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Nama alumni hanya muncul jika alumni tersebut belum pernah dinilai pada periode ini. Setelah submit, Anda dapat memilih alumni lain untuk dinilai.</p>
                        </div>
                        <input type="hidden" name="id_category" value="{{ $currentCategory->id_category }}">
                        <input type="hidden" name="action" id="form-action" value="save_draft">
                        <div class="space-y-8">
                            @foreach($questions as $question)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 question-container {{ $question->depends_on ? 'conditional-question' : '' }}"
                                     id="question-{{ $question->id_question }}"
                                     @if($question->depends_on)
                                         data-depends-on="{{ $question->depends_on }}"
                                         data-depends-value="{{ $question->depends_value }}"
                                     @endif>
                                       
                                     
                                    <!-- Question Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
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
                                            <i class="fas fa-{{ $question->type == 'text' ? 'keyboard' : ($question->type == 'option' ? 'dot-circle' : ($question->type == 'multiple' ? 'check-square' : ($question->type == 'location' ? 'map-marker-alt' : ($question->type == 'rating' ? 'star' : ($question->type == 'scale' ? 'chart-line' : 'calendar-alt'))))) }} mr-1"></i>
                                            {{ ucfirst($question->type) }}
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

                                        @elseif($question->type == 'date')
                                            <!-- Date question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                                                    <input type="date" 
                                                           name="answers[{{ $question->id_question }}]" 
                                                           value="{{ $prevAnswers[$question->id_question] ?? '' }}"
                                                           class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'option')
                                            <!-- Single choice question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="space-y-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-start">
                                                            <label class="flex items-start cursor-pointer w-full">
                                                                <input type="radio" 
                                                                       name="answers[{{ $question->id_question }}]" 
                                                                       value="{{ $option->id_questions_options }}"
                                                                       data-question-id="{{ $question->id_question }}"
                                                                       data-is-other="{{ $option->is_other_option }}"
                                                                       class="option-radio mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                                                       {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'checked' : '' }}>
                                                                <span class="flex-1 text-gray-800">{{ $option->option }}</span>
                                                            </label>
                                                        </div>
                                                        
                                                        @if($option->is_other_option)
                                                            <div id="other_field_{{ $question->id_question }}_{{ $option->id_questions_options }}" 
                                                                 class="ml-6 mt-2 {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? '' : 'hidden' }}">
                                                                <div class="flex items-center bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                                                                    @if($option->other_before_text)
                                                                        <span class="text-gray-600 mr-2">{{ $option->other_before_text }}:</span>
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
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'multiple')
                                            <!-- Multiple choice question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="space-y-3">
                                                    @foreach($question->options as $option)
                                                        <div class="flex items-start">
                                                            <label class="flex items-start cursor-pointer w-full">
                                                                <input type="checkbox" 
                                                                       name="multiple[{{ $question->id_question }}][]" 
                                                                       value="{{ $option->id_questions_options }}"
                                                                       data-question-id="{{ $question->id_question }}"
                                                                       data-is-other="{{ $option->is_other_option }}"
                                                                       class="multiple-checkbox mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                                                       {{ isset($prevMultipleAnswers[$question->id_question]) && in_array($option->id_questions_options, $prevMultipleAnswers[$question->id_question]) ? 'checked' : '' }}>
                                                                <span class="flex-1 text-gray-800">{{ $option->option }}</span>
                                                            </label>
                                                        </div>
                                                        
                                                        @if($option->is_other_option)
                                                            <div id="multiple_other_field_{{ $question->id_question }}_{{ $option->id_questions_options }}" 
                                                                 class="ml-6 mt-2 {{ isset($prevMultipleAnswers[$question->id_question]) && in_array($option->id_questions_options, $prevMultipleAnswers[$question->id_question]) ? '' : 'hidden' }}">
                                                                <div class="flex items-center bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                                                                    @if($option->other_before_text)
                                                                        <span class="text-gray-600 mr-2">{{ $option->other_before_text }}:</span>
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
                                                        <div class="flex items-center">
                                                            <label class="flex items-center cursor-pointer w-full p-3 rounded-lg border hover:bg-yellow-50 transition-colors duration-200">
                                                                <input type="radio" 
                                                                       name="answers[{{ $question->id_question }}]" 
                                                                       value="{{ $option->id_questions_options }}"
                                                                       data-question-id="{{ $question->id_question }}"
                                                                       class="rating-radio mr-3 text-yellow-600 focus:ring-yellow-500"
                                                                       {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'checked' : '' }}>
                                                                <span class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300' : 'bg-gray-100 text-gray-700 border-2 border-gray-300' }} hover:bg-yellow-50 transition-colors duration-200">
                                                                    <i class="fas fa-star mr-1"></i>
                                                                    {{ $option->option }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'scale')
                                            <!-- Scale question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center mb-4">
                                                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                                                    <span class="font-medium text-gray-700">Skala 1-5</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    @foreach($question->options as $option)
                                                        <div class="flex flex-col items-center">
                                                            <label class="flex flex-col items-center cursor-pointer p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                                                                <input type="radio" 
                                                                       name="answers[{{ $question->id_question }}]" 
                                                                       value="{{ $option->id_questions_options }}"
                                                                       data-question-id="{{ $question->id_question }}"
                                                                       class="scale-radio mb-2 text-blue-600 focus:ring-blue-500"
                                                                       {{ isset($prevAnswers[$question->id_question]) && $prevAnswers[$question->id_question] == $option->id_questions_options ? 'checked' : '' }}>
                                                                <span class="text-2xl font-bold text-blue-600 mb-1">{{ $option->option }}</span>
                                                                <span class="text-xs text-gray-600 text-center">{{ $option->option_label ?? '' }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="text-red-500 text-sm mt-1 validation-message hidden"></div>

                                        @elseif($question->type == 'location')
                                            <!-- Location question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi:</label>
                                                        <select id="province-{{ $question->id_question }}" 
                                                                data-question-id="{{ $question->id_question }}"
                                                                class="province-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten:</label>
                                                        <select id="city-{{ $question->id_question }}" 
                                                                data-question-id="{{ $question->id_question }}"
                                                                class="city-select w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                                disabled>
                                                            <option value="">-- Pilih Kota/Kabupaten --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                                            <span class="text-sm font-medium text-gray-700">Lokasi Terpilih: </span>
                                                            <span id="location-display-{{ $question->id_question }}" class="text-sm text-blue-600 font-medium ml-1">Belum dipilih</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" 
                                                           name="location_combined[{{ $question->id_question }}]" 
                                                           id="location-combined-{{ $question->id_question }}"
                                                           value="{{ $prevLocationAnswers[$question->id_question] ?? '' }}">
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
                                                           inputmode="numeric"
                                                           data-question-id="{{ $question->id_question }}">
                                                    
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

                                        @elseif($question->type == 'email')
                                            <!-- Email question -->
                                            <div class="bg-white border border-gray-300 rounded-lg p-4">
                                                <div class="flex items-center flex-wrap">
                                                    <i class="fas fa-envelope text-blue-600 mr-3"></i>
                                                    @if($question->before_text)
                                                        <span class="mr-2 text-gray-700 font-medium">{{ $question->before_text }}</span>
                                                    @endif
                                                    
                                                    <input type="email" 
                                                           name="answers[{{ $question->id_question }}]" 
                                                           value="{{ $prevAnswers[$question->id_question] ?? '' }}"
                                                           class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-48 email-validation"
                                                           placeholder="contoh@domain.com"
                                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                           data-question-id="{{ $question->id_question }}">
                                                    
                                                    @if($question->after_text)
                                                        <span class="ml-2 text-gray-700 font-medium">{{ $question->after_text }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="mt-2 text-xs text-gray-500 flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Masukkan email yang valid dengan domain (contoh: nama@gmail.com)
                                                </div>
                                            </div>
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

<!-- Validation Alert -->
<div id="validation-alert" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 hidden max-w-md">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <div class="flex-1">
            <strong class="font-bold">Perhatian!</strong>
            <p class="block sm:inline" id="validation-message">Harap jawab semua pertanyaan yang wajib diisi.</p>
        </div>
        <button class="ml-4 text-red-500 hover:text-red-700" onclick="document.getElementById('validation-alert').classList.add('hidden')">
            <i class="fas fa-times"></i>
        </button>
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

/* Numeric input styling */
    .numeric-only {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        letter-spacing: 1px;
    }

    .numeric-only:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }

    /* Shake animation for numeric input errors */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing company questionnaire functionality');
    
    // ✅ PERBAIKAN: Pass data dari backend to JavaScript dengan benar
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
    
    // GLOBAL FORM ELEMENTS
    const form = document.getElementById('questionnaireForm');
    const formAction = document.getElementById('form-action');
    
    // ✅ PERBAIKAN: Load saved answers dengan benar untuk semua tipe input
    function loadSavedAnswers() {
        console.log('=== LOADING SAVED ANSWERS ===');
        
        // Load regular answers (text, date, option, rating, scale, numeric, email)
        Object.entries(window.questionnaireData.prevAnswers).forEach(([questionId, answer]) => {
            console.log(`Loading answer for question ${questionId}:`, answer);
            
            // ✅ CARI SEMUA JENIS INPUT UNTUK QUESTION INI
            // Try radio inputs first (for option, rating, scale)
            let input = document.querySelector(`input[name="answers[${questionId}]"][value="${answer}"]`);
            
            if (!input) {
                // Try other input types (text, date, numeric, email)
                input = document.querySelector(`input[name="answers[${questionId}]"]`) ||
                       document.querySelector(`select[name="answers[${questionId}]"]`) ||
                       document.querySelector(`textarea[name="answers[${questionId}]"]`);
            }
            
            if (input) {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = true;
                    console.log(`✅ Checked radio/checkbox for question ${questionId}`);
                } else {
                    // ✅ UNTUK TEXT, DATE, NUMERIC, EMAIL
                    input.value = answer;
                    console.log(`✅ Set value "${answer}" for question ${questionId} (type: ${input.type})`);
                    
                    // ✅ TRIGGER CHANGE EVENT UNTUK VALIDASI
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            } else {
                console.warn(`❌ Could not find input for question ${questionId} with answer:`, answer);
            }
        });
        
        // Load multiple choice answers
        Object.entries(window.questionnaireData.prevMultipleAnswers).forEach(([questionId, answers]) => {
            if (Array.isArray(answers)) {
                answers.forEach(answer => {
                    const checkbox = document.querySelector(`input[name="multiple[${questionId}][]"][value="${answer}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        console.log(`✅ Checked multiple choice for question ${questionId}:`, answer);
                    }
                });
            }
        });
        
        // Load other answers
        Object.entries(window.questionnaireData.prevOtherAnswers).forEach(([questionId, otherAnswer]) => {
            const otherInput = document.querySelector(`input[name="other_answers[${questionId}]"]`);
            if (otherInput) {
                otherInput.value = otherAnswer;
                const otherDiv = otherInput.closest('div');
                if (otherDiv && otherDiv.id && otherDiv.id.includes('other_field_')) {
                    otherDiv.classList.remove('hidden');
                }
                console.log(`✅ Loaded other answer for question ${questionId}`);
            }
        });
        
        // Load multiple other answers
        Object.entries(window.questionnaireData.prevMultipleOtherAnswers).forEach(([questionId, otherAnswers]) => {
            if (typeof otherAnswers === 'object') {
                Object.entries(otherAnswers).forEach(([optionId, otherAnswer]) => {
                    const otherInput = document.querySelector(`input[name="multiple_other_answers[${questionId}][${optionId}]"]`);
                    if (otherInput) {
                        otherInput.value = otherAnswer;
                        const otherDiv = otherInput.closest('div');
                        if (otherDiv && otherDiv.id && otherDiv.id.includes('multiple_other_field_')) {
                            otherDiv.classList.remove('hidden');
                        }
                        console.log(`✅ Loaded multiple other answer for question ${questionId}, option ${optionId}`);
                    }
                });
            }
        });
        
        console.log('✅ Saved answers loaded');
    }
    
    // ✅ PERBAIKAN: Setup event listeners
    function setupEventListeners() {
        console.log('=== SETTING UP EVENT LISTENERS ===');
        
        // Handle radio button changes for single choice questions
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
                
                console.log('Radio changed:', {
                    questionId: questionId,
                    selectedValue: this.value,
                    isOther: isOther
                });
                
                // Handle other field visibility
                if (isOther) {
                    handleOtherFieldVisibility(questionId, this.value, true);
                } else {
                    hideAllOtherFieldsForQuestion(questionId);
                }
                
                // Handle conditional questions
                handleDependentQuestions(questionId, this.value);
            });
        });
        
        // Handle checkbox changes for multiple choice questions
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
                
                console.log('Checkbox changed:', {
                    questionId: questionId,
                    selectedValue: this.value,
                    isOther: isOther,
                    checked: this.checked
                });
                
                // Handle other field visibility for multiple choice
                if (isOther) {
                    handleMultipleOtherFieldVisibility(questionId, this.value, this.checked);
                }
                
                // Handle conditional questions for multiple choice
                const checkedValues = [];
                document.querySelectorAll(`input[name="multiple[${questionId}][]"]:checked`).forEach(cb => {
                    checkedValues.push(cb.value);
                });
                
                if (checkedValues.length > 0) {
                    checkedValues.forEach(value => {
                        handleDependentQuestions(questionId, value);
                    });
                } else {
                    // If no options selected, hide all dependent questions
                    hideAllDependentQuestions(questionId);
                }
            });
        });
        
        console.log('✅ Event listeners setup complete');
    }
    
    // ✅ PERBAIKAN: Initialize dengan urutan yang benar
    function initializeQuestionnaire() {
        console.log('=== STARTING QUESTIONNAIRE INITIALIZATION ===');
        
        // 1. SEMBUNYIKAN SEMUA CONDITIONAL QUESTIONS DULU
        hideAllConditionalQuestions();
        
        // 2. SETUP EVENT LISTENERS
        setupEventListeners();
        
        // 3. LOAD SAVED ANSWERS
        loadSavedAnswers();
        
        // 4. INITIALIZE LOCATION QUESTIONS
        initializeLocationQuestions();
        
        // 5. INITIALIZE CONDITIONAL QUESTIONS BERDASARKAN JAWABAN YANG ADA
        initializeConditionalQuestionsBasedOnAnswers();
        
        console.log('=== QUESTIONNAIRE INITIALIZATION COMPLETE ===');
    }
    
    // ✅ PERBAIKAN: Hide all conditional questions by default
    function hideAllConditionalQuestions() {
        console.log('=== HIDING ALL CONDITIONAL QUESTIONS ===');
        
        const conditionalQuestions = document.querySelectorAll('.conditional-question');
        console.log('Found conditional questions to hide:', conditionalQuestions.length);
        
        conditionalQuestions.forEach((question, index) => {
            console.log(`Hiding conditional question ${index + 1}:`, question.id);
            
            // Hide the question
            question.style.display = 'none';
            question.classList.add('hidden');
            
            // Disable all form elements in conditional questions
            const formElements = question.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                element.disabled = true;
                // Clear values
                if (element.type === 'radio' || element.type === 'checkbox') {
                    element.checked = false;
                } else {
                    element.value = '';
                }
            });
            
            // Clear validation messages
            const validationMessages = question.querySelectorAll('.validation-message');
            validationMessages.forEach(msg => {
                msg.classList.add('hidden');
                msg.textContent = '';
            });
            
            // Remove error styling
            question.classList.remove('border-red-300', 'bg-red-50');
        });
        
        console.log('✅ All conditional questions hidden and disabled');
    }
    
    // ✅ PERBAIKAN: Handle dependent questions properly
    function handleDependentQuestions(parentQuestionId, selectedValue) {
        console.log(`=== HANDLING DEPENDENT QUESTIONS ===`);
        console.log(`Parent Question: ${parentQuestionId}, Selected Value: "${selectedValue}"`);
        
        // Find all questions that depend on this parent question
        const dependentQuestions = document.querySelectorAll(`.conditional-question[data-depends-on="${parentQuestionId}"]`);
        console.log(`Found ${dependentQuestions.length} dependent questions for parent ${parentQuestionId}`);
        
        dependentQuestions.forEach(dependentQuestion => {
            const dependsValue = dependentQuestion.getAttribute('data-depends-value');
            const questionId = dependentQuestion.id.replace('question-', '');
            
            console.log(`Processing dependent question ${questionId}:`, {
                dependsOn: parentQuestionId,
                dependsValue: dependsValue,
                selectedValue: selectedValue,
                shouldShow: dependsValue === selectedValue
            });
            
            if (dependsValue === selectedValue) {
                // Show the dependent question
                dependentQuestion.style.display = 'block';
                dependentQuestion.classList.remove('hidden');
                
                // Enable all form elements
                const formElements = dependentQuestion.querySelectorAll('input, select, textarea');
                formElements.forEach(element => {
                    element.disabled = false;
                });
                
                console.log(`✅ Showed dependent question: ${questionId}`);
                
            } else {
                // Hide the dependent question
                dependentQuestion.style.display = 'none';
                dependentQuestion.classList.add('hidden');
                
                // Clear and disable all form elements
                const formElements = dependentQuestion.querySelectorAll('input, select, textarea');
                formElements.forEach(element => {
                    if (element.type === 'radio' || element.type === 'checkbox') {
                        element.checked = false;
                    } else {
                        element.value = '';
                    }
                    element.disabled = true;
                });
                
                // Clear validation errors
                const validationMessages = dependentQuestion.querySelectorAll('.validation-message');
                validationMessages.forEach(msg => {
                    msg.classList.add('hidden');
                    msg.textContent = '';
                });
                
                // Remove error styling
                dependentQuestion.classList.remove('border-red-300', 'bg-red-50');
                
                // Recursively hide nested dependent questions
                hideNestedDependentQuestions(questionId);
            }
        });
    }
    
    function hideNestedDependentQuestions(parentQuestionId) {
        const nestedDependents = document.querySelectorAll(`.conditional-question[data-depends-on="${parentQuestionId}"]`);
        nestedDependents.forEach(nestedQuestion => {
            nestedQuestion.style.display = 'none';
            nestedQuestion.classList.add('hidden');
            
            // Clear and disable form elements
            const formElements = nestedQuestion.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                if (element.type === 'radio' || element.type === 'checkbox') {
                    element.checked = false;
                } else {
                    element.value = '';
                }
                element.disabled = true;
            });
            
            // Clear validation errors
            const validationMessages = nestedQuestion.querySelectorAll('.validation-message');
            validationMessages.forEach(msg => {
                msg.classList.add('hidden');
                msg.textContent = '';
            });
            
            nestedQuestion.classList.remove('border-red-300', 'bg-red-50');
            
            // Recursively hide further nested questions
            const questionIdMatch = nestedQuestion.id.match(/question-(\d+)/);
            if (questionIdMatch) {
                hideNestedDependentQuestions(questionIdMatch[1]);
            }
        });
    }
    
    function hideAllDependentQuestions(parentQuestionId) {
        const dependentQuestions = document.querySelectorAll(`.conditional-question[data-depends-on="${parentQuestionId}"]`);
        dependentQuestions.forEach(dependentQuestion => {
            dependentQuestion.style.display = 'none';
            dependentQuestion.classList.add('hidden');
            
            const formElements = dependentQuestion.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                if (element.type === 'radio' || element.type === 'checkbox') {
                    element.checked = false;
                } else {
                    element.value = '';
                }
                element.disabled = true;
            });
        });
    }
    
    // ✅ PERBAIKAN: Initialize conditional questions based on existing answers
    function initializeConditionalQuestionsBasedOnAnswers() {
        console.log('=== INITIALIZING CONDITIONAL QUESTIONS BASED ON ANSWERS ===');
        
        // Add small delay to ensure all answers are loaded
        setTimeout(() => {
            // Check all checked radio buttons and trigger conditional logic
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                const questionId = radio.getAttribute('data-question-id');
                const selectedValue = radio.value;
                
                console.log('Found checked radio on load:', {
                    questionId: questionId,
                    selectedValue: selectedValue
                });
                
                handleDependentQuestions(questionId, selectedValue);
            });
            
            // Check all checked checkboxes and trigger conditional logic
            const checkedMultiples = new Map();
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
                const questionId = checkbox.getAttribute('data-question-id');
                if (!checkedMultiples.has(questionId)) {
                    checkedMultiples.set(questionId, []);
                }
                checkedMultiples.get(questionId).push(checkbox.value);
            });
            
            checkedMultiples.forEach((values, questionId) => {
                console.log('Found checked checkboxes on load:', {
                    questionId: questionId,
                    selectedValues: values
                });
                
                values.forEach(value => {
                    handleDependentQuestions(questionId, value);
                });
            });
            
            console.log('✅ Conditional questions initialized based on existing answers');
        }, 300);
    }
    
    // Helper functions for other field visibility
    function handleOtherFieldVisibility(questionId, optionValue, show) {
        // Hide all other fields for this question first
        hideAllOtherFieldsForQuestion(questionId);
        
        if (show) {
            const otherField = document.getElementById(`other_field_${questionId}_${optionValue}`);
            if (otherField) {
                otherField.classList.remove('hidden');
                const input = otherField.querySelector('input[type="text"]');
                if (input) {
                    input.required = true;
                    input.focus();
                }
            }
        }
    }
    
    function hideAllOtherFieldsForQuestion(questionId) {
        document.querySelectorAll(`[id^="other_field_${questionId}_"]`).forEach(field => {
            field.classList.add('hidden');
            const input = field.querySelector('input[type="text"]');
            if (input) {
                input.value = '';
                input.required = false;
            }
        });
    }
    
    function handleMultipleOtherFieldVisibility(questionId, optionValue, show) {
        const otherField = document.getElementById(`multiple_other_field_${questionId}_${optionValue}`);
        if (otherField) {
            if (show) {
                otherField.classList.remove('hidden');
                const input = otherField.querySelector('input[type="text"]');
                if (input) {
                    input.required = true;
                    input.focus();
                }
            } else {
                otherField.classList.add('hidden');
                const input = otherField.querySelector('input[type="text"]');
                if (input) {
                    input.value = '';
                    input.required = false;
                }
            }
        }
    }

    // LOCATION DATA - Complete dataset (keep existing location code)
    const locationData = {
        provinces: [
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
        ],
        cities: {
            '31': [
                { id: '3101', name: 'Kabupaten Kepulauan Seribu' },
                { id: '3171', name: 'Kota Jakarta Selatan' },
                { id: '3172', name: 'Kota Jakarta Timur' },
                { id: '3173', name: 'Kota Jakarta Pusat' },
                { id: '3174', name: 'Kota Jakarta Barat' },
                { id: '3175', name: 'Kota Jakarta Utara' }
            ],
            '32': [
                { id: '3201', name: 'Kabupaten Bogor' },
                { id: '3202', name: 'Kabupaten Sukabumi' },
                { id: '3271', name: 'Kota Bogor' },
                { id: '3272', name: 'Kota Sukabumi' },
                { id: '3273', name: 'Kota Bandung' },
                { id: '3274', name: 'Kota Cirebon' },
                { id: '3275', name: 'Kota Bekasi' },
                { id: '3276', name: 'Kota Depok' }
            ]
        }
    };

    // LOCATION FUNCTIONS
    function initializeLocationQuestions() {
        console.log('=== INITIALIZING LOCATION QUESTIONS ===');
        
        // Find all province selects
        const provinceSelects = document.querySelectorAll('.province-select');
        console.log('Found province selects:', provinceSelects.length);
        
        if (provinceSelects.length === 0) {
            console.warn('No province selects found! Location functionality will not work.');
            return;
        }
        
        provinceSelects.forEach((provinceSelect, index) => {
            const questionId = provinceSelect.getAttribute('data-question-id');
            console.log(`Setting up location question ${index + 1}:`, {
                questionId: questionId,
                elementId: provinceSelect.id
            });
            
            // Populate provinces
            setupProvinceOptions(provinceSelect);
            
            // Setup event listeners
            setupLocationEventListeners(questionId);
            
            // Load any saved data
            loadSavedLocationData(questionId);
        });
    }
    
    function setupProvinceOptions(provinceSelect) {
        console.log('Setting up province options for:', provinceSelect.id);
        
        // Clear existing options except the first one
        provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
        
        // Add all provinces
        locationData.provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = province.name;
            option.setAttribute('data-name', province.name);
            provinceSelect.appendChild(option);
        });
        
        console.log('Added', locationData.provinces.length, 'provinces to select');
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
    
    function handleProvinceChange(questionId, selectedProvinceId) {
        console.log('=== HANDLING PROVINCE CHANGE ===');
        console.log('Question ID:', questionId);
        console.log('Selected Province ID:', selectedProvinceId);
        
        const citySelect = document.getElementById(`city-${questionId}`);
        if (!citySelect) {
            console.error('City select not found for question:', questionId);
            return;
        }
        
        // Reset city select
        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
        citySelect.disabled = true;
        
        // Clear location display
        clearLocationDisplay(questionId);
        
        if (selectedProvinceId && locationData.cities[selectedProvinceId]) {
            console.log('Loading cities for province:', selectedProvinceId);
            const cities = locationData.cities[selectedProvinceId];
            console.log('Found cities:', cities.length);
            
            // Enable city select
            citySelect.disabled = false;
            
            // Add city options
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                option.setAttribute('data-name', city.name);
                citySelect.appendChild(option);
            });
            
            console.log('Successfully loaded', cities.length, 'cities');
        } else {
            console.log('No cities found for province:', selectedProvinceId);
        }
    }
    
    function updateLocationDisplay(questionId) {
        console.log('=== UPDATING LOCATION DISPLAY ===');
        console.log('Question ID:', questionId);
        
        const provinceSelect = document.getElementById(`province-${questionId}`);
        const citySelect = document.getElementById(`city-${questionId}`);
        const locationDisplaySpan = document.getElementById(`location-display-${questionId}`);
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        const elements = { provinceSelect, citySelect, locationDisplaySpan, combinedInput };
        const missingElements = Object.entries(elements).filter(([key, value]) => !value).map(([key]) => key);
        
        if (missingElements.length > 0) {
            console.error('Missing location elements for question', questionId, ':', missingElements);
            return;
        }
        
        const provinceId = provinceSelect.value;
        const cityId = citySelect.value;
        
        console.log('Current values:', { provinceId, cityId });
        
        if (provinceId && cityId) {
            const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.getAttribute('data-name');
            const cityName = citySelect.options[citySelect.selectedIndex]?.getAttribute('data-name');
            
            console.log('Location names:', { provinceName, cityName });
            
            if (provinceName && cityName) {
                const locationString = `${cityName}, ${provinceName}`;
                
                // Update display
                locationDisplaySpan.textContent = locationString;
                
                // Store data
                const locationDataObj = {
                    province_id: provinceId,
                    province_name: provinceName,
                    city_id: cityId,
                    city_name: cityName,
                    display: locationString
                };
                
                combinedInput.value = JSON.stringify(locationDataObj);
                
                console.log('Location data saved:', locationDataObj);
            }
        } else {
            clearLocationDisplay(questionId);
        }
    }
    
    function clearLocationDisplay(questionId) {
        console.log('Clearing location display for question:', questionId);
        
        const locationDisplaySpan = document.getElementById(`location-display-${questionId}`);
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        if (locationDisplaySpan) {
            locationDisplaySpan.textContent = 'Belum dipilih';
        }
        
        if (combinedInput) {
            combinedInput.value = '';
        }
    }
    
    function loadSavedLocationData(questionId) {
        console.log('Loading saved location data for question:', questionId);
        
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        if (!combinedInput || !combinedInput.value) {
            console.log('No saved location data found');
            return;
        }
        
        try {
            const savedLocation = JSON.parse(combinedInput.value);
            console.log('Found saved location data:', savedLocation);
            
            const provinceSelect = document.getElementById(`province-${questionId}`);
            if (provinceSelect && savedLocation.province_id) {
                provinceSelect.value = savedLocation.province_id;
                
                // Trigger province change to load cities
                handleProvinceChange(questionId, savedLocation.province_id);
                
                // Set city after a delay to ensure cities are loaded
                setTimeout(() => {
                    const citySelect = document.getElementById(`city-${questionId}`);
                    if (citySelect && savedLocation.city_id) {
                        citySelect.value = savedLocation.city_id;
                        updateLocationDisplay(questionId);
                    }
                }, 200);
            }
        } catch (error) {
            console.error('Error parsing saved location data:', error);
        }
    }

    // ✅ PERBAIKAN: VALIDATION FUNCTION - SATU SAJA, YANG BENAR
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
        
        // Only validate visible questions that are not disabled
        const visibleQuestions = document.querySelectorAll('.question-container:not([style*="display: none"]):not([style*="display:none"])');
        
        visibleQuestions.forEach((questionContainer, index) => {
            const questionId = questionContainer.id.replace('question-', '');
            const questionTitle = questionContainer.querySelector('h5').textContent.trim();
            
            // Check if question is conditional and currently visible
            const isConditional = questionContainer.classList.contains('conditional-question');
            const isCurrentlyVisible = questionContainer.style.display !== 'none';
            
            // Skip validation for conditional questions that are not visible
            if (isConditional && !isCurrentlyVisible) {
                return;
            }
            
            let isRequired = true; // Assume all questions are required
            let isAnswered = false;
            let errorMessage = 'Pertanyaan ini wajib dijawab';
            
            // ✅ PERBAIKAN: Check different question types dengan lebih tepat
            const textInput = questionContainer.querySelector(`input[name="answers[${questionId}]"][type="text"]:not(:disabled)`);
            const dateInput = questionContainer.querySelector(`input[name="answers[${questionId}]"][type="date"]:not(:disabled)`);
            const emailInput = questionContainer.querySelector(`input[name="answers[${questionId}]"][type="email"]:not(:disabled)`);
            const radioInputs = questionContainer.querySelectorAll(`input[name="answers[${questionId}]"][type="radio"]`);
            const checkboxInputs = questionContainer.querySelectorAll(`input[name="multiple[${questionId}][]"]`);
            const locationInput = questionContainer.querySelector(`input[name="location_combined[${questionId}]"]`);
            
            if (textInput) {
                // ✅ TEXT QUESTION (termasuk numeric)
                const isNumeric = textInput.classList.contains('numeric-only');
                const textValue = textInput.value.trim();
                
                if (textValue === '') {
                    isAnswered = false;
                    errorMessage = isNumeric ? 'Angka harus diisi' : 'Jawaban tidak boleh kosong';
                } else if (isNumeric && !/^\d+$/.test(textValue)) {
                    isAnswered = false;
                    errorMessage = 'Hanya angka yang diperbolehkan';
                } else {
                    isAnswered = true;
                }
                
            } else if (dateInput) {
                // ✅ DATE QUESTION
                isAnswered = dateInput.value.trim() !== '';
                errorMessage = 'Tanggal harus dipilih';
                
            } else if (emailInput) {
                // ✅ EMAIL QUESTION
                const emailValue = emailInput.value.trim();
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                
                if (emailValue === '') {
                    isAnswered = false;
                    errorMessage = 'Email harus diisi';
                } else if (!emailRegex.test(emailValue)) {
                    isAnswered = false;
                    errorMessage = 'Format email tidak valid (harus mengandung @domain.com)';
                } else {
                    isAnswered = true;
                }
                
            } else if (radioInputs.length > 0) {
                // ✅ RADIO QUESTION (option, rating, scale)
                const checkedRadio = questionContainer.querySelector(`input[name="answers[${questionId}]"]:checked`);
                isAnswered = checkedRadio !== null;
                errorMessage = 'Pilih salah satu pilihan';
                
                // Check "other" field if needed
                if (isAnswered && checkedRadio) {
                    const isOtherOption = parseInt(checkedRadio.getAttribute('data-is-other')) === 1;
                    if (isOtherOption) {
                        const otherInput = questionContainer.querySelector(`input[name="other_answers[${questionId}]"]`);
                        if (otherInput && otherInput.value.trim() === '') {
                            isAnswered = false;
                            errorMessage = 'Isian "Lainnya" harus diisi';
                        }
                    }
                }
                
            } else if (checkboxInputs.length > 0) {
                // ✅ MULTIPLE CHOICE QUESTION
                const checkedBoxes = questionContainer.querySelectorAll(`input[name="multiple[${questionId}][]"]:checked`);
                isAnswered = checkedBoxes.length > 0;
                errorMessage = 'Minimal satu pilihan harus dipilih';
                
                // Check "other" fields for multiple choice
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
                // ✅ LOCATION QUESTION
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
        
        console.log('✅ Validation passed');
        return true;
    }
    
    function showValidationAlert(errors) {
        const alertHtml = `
            <div id="validation-alert" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 max-w-md">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                    <div class="flex-1">
                        <strong class="font-bold">Perhatian!</strong>
                        <p class="text-sm mb-2">Silakan jawab pertanyaan berikut sebelum melanjutkan:</p>
                        <ul class="text-sm space-y-1 max-h-32 overflow-y-auto">
                            ${errors.map(error => `<li class="flex items-start"><i class="fas fa-circle text-xs mr-2 mt-1.5"></i><span>${error}</span></li>`).join('')}
                        </ul>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto remove after 10 seconds
        setTimeout(() => {
            const alert = document.getElementById('validation-alert');
            if (alert) {
                alert.remove();
            }
        }, 10000);
    }

    // ✅ NUMERIC DAN EMAIL VALIDATION
    // Add numeric-only input restriction
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

    // Email validation for email type questions
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('email-validation')) {
            const email = e.target.value;
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Remove existing feedback
            const existingFeedback = e.target.parentNode.querySelector('.email-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Validate email format
            if (email && !emailRegex.test(email)) {
                showEmailValidationFeedback(e.target);
            } else {
                // Remove error styling if email is valid
                e.target.classList.remove('border-red-500');
            }
        }
    });
    
    // Function to show email validation feedback
    function showEmailValidationFeedback(input) {
        // Add error styling
        input.classList.add('border-red-500');
        
        // Create feedback element
        const feedback = document.createElement('div');
        feedback.className = 'email-feedback absolute top-full left-0 mt-1 px-2 py-1 bg-red-100 text-red-600 text-xs rounded shadow-sm border border-red-200 z-10';
        feedback.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Format email tidak valid (harus ada @domain.com)';
        
        // Make parent relative if not already
        if (getComputedStyle(input.parentNode).position === 'static') {
            input.parentNode.style.position = 'relative';
        }
        
        // Add feedback
        input.parentNode.appendChild(feedback);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (feedback.parentNode) {
                feedback.remove();
            }
        }, 3000);
    }

    // BUTTON HANDLERS
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

    // MODAL HANDLERS
    document.getElementById('modal-cancel')?.addEventListener('click', function() {
        document.getElementById('confirmation-modal').classList.add('hidden');
    });

    document.getElementById('modal-confirm')?.addEventListener('click', function() {
        const actionInput = document.querySelector('input[name="action"]');
        if (actionInput) {
            actionInput.value = 'submit_final';
        }
        form.submit();
    });
    
    // ✅ START INITIALIZATION
    initializeQuestionnaire();
});
</script>

@endsection
