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
                    <form id="questionnaireForm" method="POST" action="{{ route('company.questionnaire.submit', $periode->id_periode) }}">
                        @csrf
                         <!-- Select Alumni -->
            <div class="mb-6">
                <label for="alumni_nim" class="block mb-1 font-medium text-gray-700">Pilih Alumni Yang Ingin Dinilai</label>
                <select
                    id="alumni_nim"
                    name="alumni_nim"
                    class="block w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-700
                    focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                    @foreach ($alumniList as $alumni)
                        <option value="{{ $alumni->nim }}">{{ $alumni->name }} - {{ $alumni->nim }}</option>
                    @endforeach
                </select>
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
                        Setelah diselesaikan, Anda tidak dapat mengubah jawaban lagi.
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing company questionnaire functionality');
    
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
    console.log('✅ Conditional questions found:', window.questionnaireData.conditionalQuestions);
    
    // GLOBAL FORM ELEMENTS
    const form = document.getElementById('questionnaireForm');
    const formAction = document.getElementById('form-action');
    
    // ✅ PERBAIKAN: Initialization dengan urutan yang benar
    function initializeQuestionnaire() {
        console.log('=== STARTING QUESTIONNAIRE INITIALIZATION ===');
        
        // 1. SEMBUNYIKAN SEMUA CONDITIONAL QUESTIONS DULU
        hideAllConditionalQuestions();
        
        // 2. LOAD SAVED ANSWERS
        loadSavedAnswers();
        
        // 3. SETUP EVENT LISTENERS
        setupEventListeners();
        
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
    
    // ✅ PERBAIKAN: Load saved answers dengan benar
    function loadSavedAnswers() {
        console.log('=== LOADING SAVED ANSWERS ===');
        
        // Load regular answers (text, date, option, rating, scale)
        Object.entries(window.questionnaireData.prevAnswers).forEach(([questionId, answer]) => {
            console.log(`Loading answer for question ${questionId}:`, answer);
            
            // Try to find radio input first (for option, rating, scale)
            let input = document.querySelector(`input[name="answers[${questionId}]"][value="${answer}"]`);
            
            if (!input) {
                // Try other input types (text, date)
                input = document.querySelector(`input[name="answers[${questionId}]"]`) ||
                       document.querySelector(`select[name="answers[${questionId}]"]`) ||
                       document.querySelector(`textarea[name="answers[${questionId}]"]`);
            }
            
            if (input) {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = true;
                    console.log(`✅ Checked radio/checkbox for question ${questionId}`);
                } else {
                    input.value = answer;
                    console.log(`✅ Set value for question ${questionId}`);
                }
            } else {
                console.warn(`❌ Could not find input for question ${questionId}`);
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
                if (otherDiv) {
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
                        if (otherDiv) {
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
                
                console.log(`❌ Hidden dependent question: ${questionId}`);
                
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
            '11': [
                { id: '1101', name: 'Kabupaten Simeulue' },
                { id: '1102', name: 'Kabupaten Aceh Singkil' },
                { id: '1103', name: 'Kabupaten Aceh Selatan' },
                { id: '1104', name: 'Kabupaten Aceh Tenggara' },
                { id: '1105', name: 'Kabupaten Aceh Timur' },
                { id: '1171', name: 'Kota Banda Aceh' },
                { id: '1172', name: 'Kota Sabang' },
                { id: '1173', name: 'Kota Langsa' },
                { id: '1174', name: 'Kota Lhokseumawe' }
            ],
            '12': [
                { id: '1201', name: 'Kabupaten Nias' },
                { id: '1202', name: 'Kabupaten Mandailing Natal' },
                { id: '1203', name: 'Kabupaten Tapanuli Selatan' },
                { id: '1204', name: 'Kabupaten Tapanuli Tengah' },
                { id: '1205', name: 'Kabupaten Tapanuli Utara' },
                { id: '1206', name: 'Kabupaten Toba Samosir' },
                { id: '1207', name: 'Kabupaten Labuhan Batu' },
                { id: '1208', name: 'Kabupaten Asahan' },
                { id: '1209', name: 'Kabupaten Simalungun' },
                { id: '1210', name: 'Kabupaten Dairi' },
                { id: '1211', name: 'Kabupaten Karo' },
                { id: '1212', name: 'Kabupaten Deli Serdang' },
                { id: '1213', name: 'Kabupaten Langkat' },
                { id: '1271', name: 'Kota Sibolga' },
                { id: '1272', name: 'Kota Medan' },
                { id: '1273', name: 'Kota Binjai' },
                { id: '1274', name: 'Kota Tebing Tinggi' },
                { id: '1275', name: 'Kota Pematangsiantar' }
            ],
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
                { id: '3203', name: 'Kabupaten Cianjur' },
                { id: '3204', name: 'Kabupaten Bandung' },
                { id: '3205', name: 'Kabupaten Garut' },
                { id: '3206', name: 'Kabupaten Tasikmalaya' },
                { id: '3207', name: 'Kabupaten Ciamis' },
                { id: '3208', name: 'Kabupaten Kuningan' },
                { id: '3209', name: 'Kabupaten Cirebon' },
                { id: '3210', name: 'Kabupaten Majalengka' },
                { id: '3211', name: 'Kabupaten Sumedang' },
                { id: '3212', name: 'Kabupaten Indramayu' },
                { id: '3271', name: 'Kota Bogor' },
                { id: '3272', name: 'Kota Sukabumi' },
                { id: '3273', name: 'Kota Bandung' },
                { id: '3274', name: 'Kota Cirebon' },
                { id: '3275', name: 'Kota Bekasi' },
                { id: '3276', name: 'Kota Depok' },
                { id: '3277', name: 'Kota Cimahi' },
                { id: '3278', name: 'Kota Tasikmalaya' },
                { id: '3279', name: 'Kota Banjar' }
            ]
        }
    };

    // LOCATION FUNCTIONS (keep existing location functions)
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
        const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
        const locationTextSpan = document.getElementById(`location-text-${questionId}`);
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        // Check if all elements exist
        const elements = { provinceSelect, citySelect, selectedLocationDiv, locationTextSpan, combinedInput };
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
                locationTextSpan.textContent = locationString;
                selectedLocationDiv.classList.remove('hidden');
                
                // Store data
                const locationDataObj = {
                    province_id: provinceId,
                    province_name: provinceName,
                    city_id: cityId,
                    city_name: cityName,
                    display: locationString
                };
                
                combinedInput.value = JSON.stringify(locationDataObj);
                
                // Remove validation styling
                const container = combinedInput.closest('.question-container');
                if (container) {
                    container.classList.remove('border-red-300', 'bg-red-50');
                    const validationMsg = container.querySelector('.validation-message');
                    if (validationMsg) {
                        validationMsg.classList.add('hidden');
                    }
                }
                
                console.log('Location data saved:', locationDataObj);
            }
        } else {
            clearLocationDisplay(questionId);
        }
    }
    
    function clearLocationDisplay(questionId) {
        console.log('Clearing location display for question:', questionId);
        
        const selectedLocationDiv = document.getElementById(`selected-location-${questionId}`);
        const combinedInput = document.getElementById(`location-combined-${questionId}`);
        
        if (selectedLocationDiv) {
            selectedLocationDiv.classList.add('hidden');
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

    // VALIDATION FUNCTIONS
    function validateCurrentCategory() {
        let isValid = true;
        const unansweredQuestions = [];
        
        console.log('=== STARTING VALIDATION ===');
        
        // Clear previous validation messages
        document.querySelectorAll('.validation-message').forEach(msg => {
            msg.classList.add('hidden');
        });
        document.querySelectorAll('.question-container').forEach(container => {
            container.classList.remove('border-red-300', 'bg-red-50');
        });
        
        // Hide validation alert
        document.getElementById('validation-alert').classList.add('hidden');
        
        document.querySelectorAll('.question-container').forEach(container => {
            // Skip hidden questions (conditional questions that are not shown)
            if (container.style.display === 'none') {
                console.log('Skipping hidden question:', container.id);
                return;
            }
            
            const questionId = container.id.replace('question-', '');
            const questionTitle = container.querySelector('h5').textContent.trim();
            let hasAnswer = false;
            
            console.log('Validating question:', questionId, questionTitle);
            
            // Check text inputs (not disabled)
            const textInput = container.querySelector(`input[name="answers[${questionId}]"][type="text"]:not(:disabled)`);
            if (textInput && textInput.value.trim() !== '') {
                hasAnswer = true;
                console.log('✅ Text answer found');
            }
            
            // Check date inputs (not disabled)
            const dateInput = container.querySelector(`input[name="answers[${questionId}]"][type="date"]:not(:disabled)`);
            if (dateInput && dateInput.value.trim() !== '') {
                hasAnswer = true;
                console.log('✅ Date answer found');
            }
            
            // Check radio buttons (not disabled)
            const radioInputs = container.querySelectorAll(`input[name="answers[${questionId}]"]:checked:not(:disabled)`);
            if (radioInputs.length > 0) {
                hasAnswer = true;
                console.log('✅ Radio answer found');
            }
            
            // Check checkboxes (not disabled)
            const checkboxInputs = container.querySelectorAll(`input[name="multiple[${questionId}][]"]:checked:not(:disabled)`);
            if (checkboxInputs.length > 0) {
                hasAnswer = true;
                console.log('✅ Checkbox answer found');
            }
            
            // Check location inputs (not disabled)
            const locationInput = container.querySelector(`input[name="location_combined[${questionId}]"]:not(:disabled)`);
            if (locationInput && locationInput.value.trim() !== '') {
                hasAnswer = true;
                console.log('✅ Location answer found');
            }
            
            if (!hasAnswer) {
                unansweredQuestions.push(questionTitle);
                isValid = false;
                
                console.log('❌ No answer found for question:', questionId);
                
                // Show validation message
                const validationMsg = container.querySelector('.validation-message');
                if (validationMsg) {
                    validationMsg.textContent = 'Pertanyaan ini wajib dijawab';
                    validationMsg.classList.remove('hidden');
                } else {
                    // Create validation message if not exists
                    const newValidationMsg = document.createElement('div');
                    newValidationMsg.className = 'text-red-500 text-sm mt-1 validation-message';
                    newValidationMsg.textContent = 'Pertanyaan ini wajib dijawab';
                    container.appendChild(newValidationMsg);
                }
                
                // Add error styling
                container.classList.add('border-red-300', 'bg-red-50');
            }
        });
        
        if (!isValid) {
            console.log('Validation failed. Unanswered questions:', unansweredQuestions);
            
            // Show validation alert
            const validationAlert = document.getElementById('validation-alert');
            const validationMessage = document.getElementById('validation-message');
            
            if (unansweredQuestions.length === 1) {
                validationMessage.textContent = `Harap jawab pertanyaan: "${unansweredQuestions[0]}"`;
            } else {
                validationMessage.textContent = `Harap jawab ${unansweredQuestions.length} pertanyaan yang belum dijawab.`;
            }
            
            validationAlert.classList.remove('hidden');
            
            // Scroll to first unanswered question
            const firstUnanswered = document.querySelector('.question-container.border-red-300');
            if (firstUnanswered) {
                firstUnanswered.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            // Auto-hide alert after 5 seconds
            setTimeout(() => {
                validationAlert.classList.add('hidden');
            }, 5000);
            
        } else {
            console.log('✅ All visible questions answered');
        }
        
        return isValid;
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

    // VALIDATION FUNCTION
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
            
            // Check different question types
            const textInput = questionContainer.querySelector(`input[name="answers[${questionId}]"]:not([type="radio"]):not([type="checkbox"])`);
            const radioInputs = questionContainer.querySelectorAll(`input[name="answers[${questionId}]"][type="radio"]`);
            const checkboxInputs = questionContainer.querySelectorAll(`input[name="multiple[${questionId}][]"]`);
            const locationInput = questionContainer.querySelector(`input[name="location_combined[${questionId}]"]`);
            
            if (textInput) {
                // Text or date question
                isAnswered = textInput.value.trim() !== '';
                errorMessage = 'Jawaban tidak boleh kosong';
                
            } else if (radioInputs.length > 0) {
                // Radio question (option, rating, scale)
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
                // Multiple choice question
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

    // ✅ PROFILE DROPDOWN
    document.getElementById('profile-toggle')?.addEventListener('click', function() {
        const dropdown = document.getElementById('profile-dropdown');
        dropdown.classList.toggle('hidden');
    });

    // ✅ SIDEBAR TOGGLE
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('-translate-x-full');
    });

    // ✅ LOGOUT HANDLER
    document.getElementById('logout-btn')?.addEventListener('click', function(event) {
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
    
    // ✅ START INITIALIZATION
    initializeQuestionnaire();
    
    // ✅ BUTTON EVENT HANDLERS
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

    // VALIDATION FUNCTION
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
            
            // Check different question types
            const textInput = questionContainer.querySelector(`input[name="answers[${questionId}]"]:not([type="radio"]):not([type="checkbox"])`);
            const radioInputs = questionContainer.querySelectorAll(`input[name="answers[${questionId}]"][type="radio"]`);
            const checkboxInputs = questionContainer.querySelectorAll(`input[name="multiple[${questionId}][]"]`);
            const locationInput = questionContainer.querySelector(`input[name="location_combined[${questionId}]"]`);
            
            if (textInput) {
                // Text or date question
                isAnswered = textInput.value.trim() !== '';
                errorMessage = 'Jawaban tidak boleh kosong';
                
            } else if (radioInputs.length > 0) {
                // Radio question (option, rating, scale)
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
                // Multiple choice question
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

    // ✅ PROFILE DROPDOWN
    document.getElementById('profile-toggle')?.addEventListener('click', function() {
        const dropdown = document.getElementById('profile-dropdown');
        dropdown.classList.toggle('hidden');
    });

    // ✅ SIDEBAR TOGGLE
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('-translate-x-full');
    });

    // ✅ LOGOUT HANDLER
    document.getElementById('logout-btn')?.addEventListener('click', function(event) {
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
});
cument.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });

    // Profile dropdown functionality
    document.getElementById('profile-toggle')?.addEventListener('click', function() {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        
        if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Logout functionality
    document.getElementById('logout-btn')?.addEventListener('click', function(event) {
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
});
</script>

@endsection
