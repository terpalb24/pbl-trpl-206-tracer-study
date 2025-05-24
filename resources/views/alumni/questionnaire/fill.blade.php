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
            @include('alumni.sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 text-gray-600 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">Kuesioner Alumni</h1>
                    <p class="text-sm text-gray-600">Periode: {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->alumni->name ?? auth()->user()->name }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Alumni</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-100 text-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

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
                        <div class="text-2xl font-bold text-blue-900">{{ isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1 }}/{{ $allCategories->count() }}</div>
                        <div class="text-sm text-blue-700">Kategori</div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-blue-900">Progress Keseluruhan</span>
                    <span class="text-sm font-medium text-blue-900">{{ round(((isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1) / $allCategories->count()) * 100) }}%</span>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ round(((isset($currentCategoryIndex) ? ($currentCategoryIndex + 1) : 1) / $allCategories->count()) * 100) }}%"></div>
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
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full ml-3">
                                            <i class="fas fa-{{ $question->type == 'text' ? 'keyboard' : ($question->type == 'option' ? 'dot-circle' : ($question->type == 'multiple' ? 'check-square' : ($question->type == 'location' ? 'map-marker-alt' : ($question->type == 'rating' ? 'star' : ($question->type == 'scale' ? 'chart-line' : 'calendar-alt'))))) }} mr-1"></i>
                                            {{ ucfirst($question->type) }}
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
                                                     class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md hidden">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-map-pin text-green-600 mr-2"></i>
                                                        <div>
                                                            <p class="text-sm text-green-600 font-medium">Lokasi terpilih:</p>
                                                            <p id="location-text-{{ $question->id_question }}" class="font-semibold text-gray-800"></p>
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
                                        <a href="{{ route('alumni.questionnaire.fill', [$periode->id_periode, $prevCategory->id_category]) }}" 
                                           class="inline-flex items-center px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-md transition-colors duration-200">
                                            <i class="fas fa-arrow-left mr-2"></i> 
                                            Sebelumnya
                                        </a>
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

<!-- Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Konfirmasi Penyelesaian</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menyelesaikan kuesioner ini? Setelah diselesaikan, Anda tidak dapat mengubah jawaban lagi.</p>
            <div class="flex justify-center space-x-3">
                <button id="modal-cancel" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-md transition-colors duration-200">
                    Batal
                </button>
                <button id="modal-confirm" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200">
                    Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing alumni questionnaire functionality');
    
    // GLOBAL FORM ELEMENTS
    const form = document.getElementById('questionnaireForm');
    const formAction = document.getElementById('form-action');
    
    // BUTTON HANDLERS
    document.getElementById('save-draft-btn')?.addEventListener('click', function() {
        formAction.value = 'save_draft';
        form.submit();
    });
    
    document.getElementById('next-category-btn')?.addEventListener('click', function() {
        formAction.value = 'next_category';
        form.submit();
    });
    
    document.getElementById('submit-final-btn')?.addEventListener('click', function() {
        formAction.value = 'submit_final';
        document.getElementById('confirmation-modal').classList.remove('hidden');
    });
    
    // CONFIRMATION MODAL
    document.getElementById('modal-cancel')?.addEventListener('click', function() {
        document.getElementById('confirmation-modal').classList.add('hidden');
    });
    
    document.getElementById('modal-confirm')?.addEventListener('click', function() {
        formAction.value = 'submit_final';
        form.submit();
    });
    
    // LOCATION QUESTION HANDLERS
    initializeLocationQuestions();
    
    // HANDLE "OTHER" OPTION FIELDS FOR RADIO BUTTONS
    document.querySelectorAll('.option-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.getAttribute('data-question-id');
            const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
            
            console.log('Radio changed:', {
                questionId: questionId,
                selectedValue: this.value,
                isOther: isOther
            });
            
            // Hide all other fields for this question
            document.querySelectorAll(`input[name="other_answers[${questionId}]"]`).forEach(input => {
                const parentDiv = input.closest('.bg-blue-50');
                if (parentDiv) {
                    parentDiv.classList.add('hidden');
                }
            });
            
            // Show this other field if applicable
            if (isOther) {
                const otherField = document.getElementById(`other_field_${questionId}_${this.value}`);
                if (otherField) {
                    otherField.classList.remove('hidden');
                    // Focus on the input field
                    const input = otherField.querySelector('input[type="text"]');
                    if (input) {
                        setTimeout(() => input.focus(), 100);
                    }
                }
            }
            
            // Handle conditional questions
            handleDependentQuestions(questionId, this.value);
        });
    });
    
    // HANDLE "OTHER" OPTION FIELDS FOR CHECKBOXES
    document.querySelectorAll('.multiple-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const questionId = this.getAttribute('data-question-id');
            const isOther = parseInt(this.getAttribute('data-is-other')) === 1;
            
            console.log('Checkbox changed:', {
                questionId: questionId,
                selectedValue: this.value,
                isOther: isOther,
                checked: this.checked
            });
            
            // Toggle this other field based on checkbox state
            if (isOther) {
                const otherField = document.getElementById(`multiple_other_field_${questionId}_${this.value}`);
                if (otherField) {
                    if (this.checked) {
                        otherField.classList.remove('hidden');
                        // Focus on the input field
                        const input = otherField.querySelector('input[type="text"]');
                        if (input) {
                            setTimeout(() => input.focus(), 100);
                        }
                    } else {
                        otherField.classList.add('hidden');
                        // Clear the input value when hidden
                        const input = otherField.querySelector('input[type="text"]');
                        if (input) {
                            input.value = '';
                        }
                    }
                }
            }
            
            // Handle conditional questions - determine the combined value of all checked options
            const allChecked = Array.from(document.querySelectorAll(`input[name="multiple[${questionId}][]"]:checked`))
                .map(input => input.value);
            
            // If any checkbox is checked/unchecked, check if it affects conditional questions
            allChecked.forEach(value => {
                handleDependentQuestions(questionId, value);
            });
        });
    });
    
    // HANDLE RATING QUESTIONS
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
            
            // Add visual feedback
            const container = this.closest('.grid');
            if (container) {
                container.querySelectorAll('label span').forEach(span => {
                    span.classList.remove('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
                    span.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
                });
                
                const selectedSpan = this.nextElementSibling.querySelector('span');
                if (selectedSpan) {
                    selectedSpan.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300');
                    selectedSpan.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
                }
            }
        });
    });
    
    // HANDLE SCALE QUESTIONS
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
            
            // Add visual feedback
            const container = this.closest('.flex');
            if (container) {
                container.querySelectorAll('label span').forEach(span => {
                    span.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                    span.classList.add('bg-white', 'border-gray-300');
                });
                
                const selectedSpan = this.nextElementSibling.querySelector('span');
                if (selectedSpan) {
                    selectedSpan.classList.remove('bg-white', 'border-gray-300');
                    selectedSpan.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                }
            }
        });
    });
    
    // Initialize pre-selected rating and scale options
    document.querySelectorAll('.rating-radio:checked, .scale-radio:checked').forEach(radio => {
        const questionId = radio.getAttribute('data-question-id');
        const selectedValue = radio.value;
        
        // Process conditional questions for pre-selected rating/scale
        handleDependentQuestions(questionId, selectedValue);
        
        // Add visual feedback for pre-selected options
        if (radio.classList.contains('rating-radio')) {
            const selectedSpan = radio.nextElementSibling.querySelector('span');
            if (selectedSpan) {
                selectedSpan.classList.remove('bg-gray-100', 'text-gray-700', 'border-gray-300');
                selectedSpan.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
            }
        } else if (radio.classList.contains('scale-radio')) {
            const selectedSpan = radio.nextElementSibling.querySelector('span');
            if (selectedSpan) {
                selectedSpan.classList.remove('bg-white', 'border-gray-300');
                selectedSpan.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
            }
        }
    });
    
    // Function to handle conditional questions
    function handleDependentQuestions(parentId, selectedValue) {
        console.log(`Checking dependencies for question ${parentId} with value "${selectedValue}"`);
        
        // Find all questions that depend on this one
        document.querySelectorAll('.conditional-question').forEach(question => {
            const dependsOn = question.getAttribute('data-depends-on');
            const dependsValue = question.getAttribute('data-depends-value');
            
            if (dependsOn === parentId) {
                if (selectedValue == dependsValue) {
                    question.style.display = 'block';
                    console.log(`Showing question ${question.id} because ${selectedValue} == ${dependsValue}`);
                } else {
                    question.style.display = 'none';
                    console.log(`Hiding question ${question.id} because ${selectedValue} != ${dependsValue}`);
                }
            }
        });
    }
    
    // Initialize conditional questions for pre-selected options
    document.querySelectorAll('.option-radio:checked').forEach(radio => {
        const questionId = radio.getAttribute('data-question-id');
        const selectedValue = radio.value;
        
        // Process conditional questions for this pre-selected option
        handleDependentQuestions(questionId, selectedValue);
        
        // Also handle "other" field if this is an "other" option
        if (parseInt(radio.getAttribute('data-is-other')) === 1) {
            const otherField = document.getElementById(`other_field_${questionId}_${selectedValue}`);
            if (otherField) {
                otherField.classList.remove('hidden');
            }
        }
    });
    
    // Initialize pre-selected checkboxes
    document.querySelectorAll('.multiple-checkbox:checked').forEach(checkbox => {
        const isOther = parseInt(checkbox.getAttribute('data-is-other'));
        
        // Handle "other" fields for pre-selected checkboxes
        if (isOther === 1) {
            const otherField = document.getElementById(`multiple_other_field_${checkbox.getAttribute('data-question-id')}_${checkbox.value}`);
            if (otherField) {
                otherField.classList.remove('hidden');
            }
        }
        
        // Also handle conditional questions for pre-selected checkboxes
        const questionId = checkbox.getAttribute('data-question-id');
        handleDependentQuestions(questionId, checkbox.value);
    });
    
    // Initialize location questions functionality
    function initializeLocationQuestions() {
        console.log('Initializing location questions...');
        
        // Load provinces for all location questions
        document.querySelectorAll('.province-select').forEach(select => {
            console.log('Loading provinces for select:', select.id);
            loadProvinces(select);
        });
        
        // Handle province changes
        document.querySelectorAll('.province-select').forEach(select => {
            select.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                const citySelect = document.getElementById(`city-${questionId}`);
                
                console.log('Province changed:', this.value, 'for question:', questionId);
                
                if (this.value) {
                    loadCities(questionId, this.value);
                    citySelect.disabled = false;
                } else {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    citySelect.disabled = true;
                    updateLocationDisplay(questionId);
                }
            });
        });
        
        // Handle city changes
        document.querySelectorAll('.city-select').forEach(select => {
            select.addEventListener('change', function() {
                const questionId = this.getAttribute('data-question-id');
                console.log('City changed:', this.value, 'for question:', questionId);
                updateLocationDisplay(questionId);
            });
        });
        
        // Initialize with pre-selected values after a delay
        setTimeout(() => {
            initializePreSelectedLocations();
        }, 1000);
    }
    
    function loadProvinces(selectElement) {
        console.log('Fetching provinces...');
        fetch('/alumni/questionnaire/provinces')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Provinces data:', data);
                if (data.success) {
                    selectElement.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.id;
                        option.textContent = province.name;
                        option.setAttribute('data-name', province.name);
                        selectElement.appendChild(option);
                    });
                    console.log('Provinces loaded successfully');
                } else {
                    console.error('Failed to load provinces:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
            });
    }
    
    function loadCities(questionId, provinceId) {
        const citySelect = document.getElementById(`city-${questionId}`);
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;
        
        console.log('Fetching cities for province:', provinceId);
        fetch(`/alumni/questionnaire/cities/${provinceId}`)
            .then(response => {
                console.log('Cities response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Cities data:', data);
                if (data.success) {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    data.data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        option.setAttribute('data-name', city.name);
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                    console.log('Cities loaded successfully');
                } else {
                    console.error('Failed to load cities:', data.message);
                    citySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading data</option>';
            });
    }
    
    function updateLocationDisplay(questionId) {
        const provinceSelect = document.getElementById(`province-${questionId}`);
        const citySelect = document.getElementById(`city-${questionId}`);
        const selectedLocation = document.getElementById(`selected-location-${questionId}`);
        const locationText = document.getElementById(`location-text-${questionId}`);
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.getAttribute('data-name');
        const cityName = citySelect.options[citySelect.selectedIndex]?.getAttribute('data-name');
        
        console.log('Updating location display:', { provinceName, cityName });
        
        if (provinceName && cityName && provinceName !== '-- Pilih Provinsi --' && cityName !== '-- Pilih Kota/Kabupaten --') {
            const locationString = `${cityName}, ${provinceName}`;
            locationText.textContent = locationString;
            selectedLocation.classList.remove('hidden');
            
            // Store combined location data
            const locationData = {
                province_id: provinceSelect.value,
                province_name: provinceName,
                city_id: citySelect.value,
                city_name: cityName,
                display: locationString
            };
            combinedInput.value = JSON.stringify(locationData);
            
            console.log('Location data stored:', locationData);
        } else {
            selectedLocation.classList.add('hidden');
            combinedInput.value = '';
        }
    }
    
    function initializePreSelectedLocations() {
        console.log('Initializing pre-selected locations...');
        
        // Process pre-selected location data from PHP
        @if(isset($prevLocationAnswers) && is_array($prevLocationAnswers))
            @foreach($prevLocationAnswers as $questionId => $locationData)
                console.log('Processing pre-selected location for question {{ $questionId }}:', @json($locationData));
                
                const provinceSelect{{ $questionId }} = document.getElementById('province-{{ $questionId }}');
                const citySelect{{ $questionId }} = document.getElementById('city-{{ $questionId }}');
                
                if (provinceSelect{{ $questionId }} && citySelect{{ $questionId }}) {
                    const locationData = @json($locationData);
                    
                    // Set province if it exists
                    if (locationData.province_id) {
                        console.log('Setting province to:', locationData.province_id);
                        provinceSelect{{ $questionId }}.value = locationData.province_id;
                        
                        // Load cities for this province
                        loadCities({{ $questionId }}, locationData.province_id);
                        
                        // Set city after a delay to allow cities to load
                        setTimeout(() => {
                            if (locationData.city_id) {
                                console.log('Setting city to:', locationData.city_id);
                                citySelect{{ $questionId }}.value = locationData.city_id;
                                citySelect{{ $questionId }}.disabled = false;
                                updateLocationDisplay({{ $questionId }});
                            }
                        }, 2000);
                    }
                }
            @endforeach
        @endif
    }
});

// Profile dropdown handlers
document.getElementById('toggle-sidebar').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('hidden');
});

document.getElementById('close-sidebar').addEventListener('click', function() {
    document.getElementById('sidebar').classList.add('hidden');
});

document.getElementById('profile-toggle').addEventListener('click', function() {
    document.getElementById('profile-dropdown').classList.toggle('hidden');
});

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profile-dropdown');
    const toggle = document.getElementById('profile-toggle');
    
    if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

document.getElementById('logout-btn').addEventListener('click', function(event) {
    event.preventDefault();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("logout") }}';

    const csrfTokenInput = document.createElement('input');
    csrfTokenInput.type = 'hidden';
    csrfTokenInput.name = '_token';
    csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.appendChild(csrfTokenInput);
    document.body.appendChild(form);
    form.submit();
});
</script>
@endsection
