@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Detail Kuesioner Employee'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8" id="printable-content">
            <!-- Breadcrumb -->
            <nav class="mb-4 sm:mb-6 no-print">
                <ol class="flex items-center space-x-2 text-sm overflow-x-auto">
                    <li><a href="{{ route('dashboard.company') }}" class="text-blue-600 hover:underline whitespace-nowrap">Dashboard</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li><a href="{{ route('company.questionnaire.results') }}" class="text-blue-600 hover:underline whitespace-nowrap">Hasil Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700 whitespace-nowrap">Detail</li>
                </ol>
            </nav>

            <!-- Response Summary Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6 border border-blue-200">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 space-y-3 sm:space-y-0">
                    <div class="flex-1">
                        <h2 class="text-xl sm:text-2xl font-bold text-blue-900 mb-2">Ringkasan Respons</h2>
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $userAnswer->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $userAnswer->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                {{ $userAnswer->status == 'completed' ? 'Selesai' : 'Dalam Proses' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-building mr-1"></i>
                                Perusahaan
                            </span>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm text-gray-600">ID Respons</p>
                        <p class="text-lg font-bold text-blue-900">#{{ $userAnswer->id_user_answer }}</p>
                    </div>
                </div>

                <div class="text-sm text-gray-700 mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-4">
                        <div class="flex items-center">
                            <i class="fas fa-user-graduate mr-2"></i>
                            <span>Dinilai: <strong>{{ $userAnswer->nim }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            <span>Nama Alumni: <strong>{{ $userAnswer->alumni->name ?? 'Tidak Diketahui' }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $totalQuestions = 0;
                    $answeredQuestions = 0;
                    $skippedQuestions = 0;
                    
                    foreach($questionsWithAnswers as $categoryData) {
                        foreach($categoryData['questions'] as $qData) {
                            $shouldShow = true;
                            
                            if ($qData['question']->depends_on) {
                                $parentAnswered = false;
                                foreach($questionsWithAnswers as $catData) {
                                    foreach($catData['questions'] as $parentQData) {
                                        if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                            if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                if ($parentQData['answer'] == $qData['question']->depends_value) {
                                                    $parentAnswered = true;
                                                }
                                                break 2;
                                            }
                                        }
                                    }
                                }
                                $shouldShow = $parentAnswered;
                            }
                            
                            if ($shouldShow) {
                                $totalQuestions++;
                                if ($qData['hasAnswer']) {
                                    $answeredQuestions++;
                                }
                            } else {
                                $skippedQuestions++;
                            }
                        }
                    }
                    
                    $progressPercentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                @endphp
                
                <div class="mb-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 space-y-1 sm:space-y-0">
                        <span class="text-sm font-medium text-blue-900">Progress Pengisian</span>
                        <span class="text-sm font-medium text-blue-900">{{ $answeredQuestions }}/{{ $totalQuestions }} pertanyaan ({{ $progressPercentage }}%)</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2 sm:h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 sm:h-3 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    @if($skippedQuestions > 0)
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ $skippedQuestions }} pertanyaan dilewati karena tidak memenuhi syarat dependensi
                        </p>
                    @endif
                </div>
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6 border border-gray-200">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-building mr-2 text-blue-600"></i>
                    Informasi Responden
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-building text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Nama Perusahaan</p>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $company->name ?? auth()->user()->username }}</p>
                    </div>

                    @if($company && $company->field)
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-industry text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Bidang Perusahaan</p>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $company->field }}</p>
                        </div>
                    @endif

                    @if($company && $company->city)
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Lokasi</p>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $company->city }}</p>
                        </div>
                    @endif

                    <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Tanggal Pengisian</p>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $userAnswer->created_at->format('d M Y') }}</p>
                        <p class="text-xs sm:text-sm text-gray-600">{{ $userAnswer->created_at->format('H:i') }} WIB</p>
                    </div>

                    <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-clock text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Periode Kuesioner</p>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ \Carbon\Carbon::parse($userAnswer->periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($userAnswer->periode->end_date)->format('d M Y') }}</p>
                        <span class="inline-block mt-1 px-2 py-1 rounded-full text-xs {{ $userAnswer->periode->status == 'active' ? 'bg-green-100 text-green-800' : ($userAnswer->periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($userAnswer->periode->status) }}
                        </span>
                    </div>

                    @if($userAnswer->updated_at != $userAnswer->created_at)
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-edit text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Terakhir Diperbarui</p>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $userAnswer->updated_at->format('d M Y') }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">{{ $userAnswer->updated_at->format('H:i') }} WIB</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-comment-dots mr-2 text-blue-600"></i>
                        Jawaban Kuesioner
                    </h3>
                </div>

                <div class="p-4 sm:p-6">
                    @if(count($questionsWithAnswers) > 0)
                        @foreach($questionsWithAnswers as $categoryIndex => $categoryData)
                            <div class="mb-6 sm:mb-8 {{ $categoryIndex > 0 ? 'border-t border-gray-200 pt-6 sm:pt-8' : '' }}">
                                <!-- Category Header -->
                                <div class="bg-gradient-to-r from-green-50 to-blue-50 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6 border border-green-200">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-700 w-fit">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $categoryData['category']->category_name }}
                                            </span>
                                            @php
                                                $visibleQuestions = collect($categoryData['questions'])->filter(function($qData) use ($questionsWithAnswers) {
                                                    $shouldShow = true;
                                                    if ($qData['question']->depends_on) {
                                                        $parentAnswered = false;
                                                        foreach($questionsWithAnswers as $catData) {
                                                            foreach($catData['questions'] as $parentQData) {
                                                                if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                                    if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                                        if ($parentQData['answer'] == $qData['question']->depends_value) {
                                                                            $parentAnswered = true;
                                                                        }
                                                                        break 2;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $shouldShow = $parentAnswered;
                                                    }
                                                    return $shouldShow;
                                                });
                                            @endphp
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full w-fit">
                                                {{ $visibleQuestions->count() }} pertanyaan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Questions -->
                                <div class="space-y-4 sm:space-y-6">
                                    @php $questionNumber = 1; @endphp
                                    @foreach($categoryData['questions'] as $qData)
                                        @php
                                            $shouldShow = true;
                                            if ($qData['question']->depends_on) {
                                                $parentAnswered = false;
                                                foreach($questionsWithAnswers as $catData) {
                                                    foreach($catData['questions'] as $parentQData) {
                                                        if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                            if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                                if ($parentQData['answer'] == $qData['question']->depends_value) {
                                                                    $parentAnswered = true;
                                                                }
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                                $shouldShow = $parentAnswered;
                                            }
                                        @endphp
                                        
                                        @if($shouldShow)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sm:p-5 hover:shadow-md transition-shadow duration-200">
                                                <!-- Question Header -->
                                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 space-y-3 sm:space-y-0">
                                                    <div class="flex-1">
                                                        <div class="flex flex-col sm:flex-row sm:items-start">
                                                            <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-full mr-0 sm:mr-3 mb-2 sm:mb-0 w-fit">{{ $questionNumber }}</span>
                                                            <div class="flex-1">
                                                                <p class="font-semibold text-gray-900 leading-relaxed text-sm sm:text-base">{{ $qData['question']->question }}</p>
                                                                
                                                                @if($qData['question']->depends_on)
                                                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-md p-2 sm:p-3">
                                                                        <p class="text-xs text-blue-700 flex items-start">
                                                                            <i class="fas fa-link mr-2 mt-0.5"></i> 
                                                                            <span class="font-medium">Pertanyaan bersyarat</span>
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full ml-0 sm:ml-3 w-fit">
                                                        <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'multiple' ? 'check-square' : ($qData['question']->type == 'location' ? 'map-marker-alt' : ($qData['question']->type == 'rating' ? 'star' : ($qData['question']->type == 'scale' ? 'chart-line' : 'calendar-alt'))))) }} mr-1"></i>
                                                        {{ ucfirst($qData['question']->type) }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Answer Section -->
                                                <div class="border-t border-gray-300 pt-4">
                                                    @if($qData['hasAnswer'])
                                                        @if($qData['question']->type === 'multiple')
                                                            <!-- Multiple Choice Answers -->
                                                            <div class="bg-white border border-green-200 rounded-md p-3 sm:p-4">
                                                                <h6 class="font-medium text-green-800 mb-2 flex items-center text-sm sm:text-base">
                                                                    <i class="fas fa-check-square mr-2"></i>
                                                                    Pilihan yang Dipilih:
                                                                </h6>
                                                                <ul class="space-y-2 sm:space-y-3">
                                                                    @if(isset($qData['multipleAnswers']) && is_array($qData['multipleAnswers']))
                                                                        @foreach($qData['multipleAnswers'] as $answer)
                                                                            @php
                                                                                $relatedOption = null;
                                                                                $relatedOption = $qData['question']->options->where('option', $answer)->first();
                                                                                
                                                                                if (!$relatedOption && is_numeric($answer)) {
                                                                                    $relatedOption = $qData['question']->options->where('id_questions_options', $answer)->first();
                                                                                }
                                                                                
                                                                                $optionId = $relatedOption ? $relatedOption->id_questions_options : $answer;
                                                                            @endphp
                                                                            <li class="text-green-700">
                                                                                <div class="flex items-center">
                                                                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                                                                    <span class="font-medium text-sm sm:text-base">{{ $relatedOption ? $relatedOption->option : $answer }}</span>
                                                                                </div>
                                                                                
                                                                                @if($relatedOption && $relatedOption->is_other_option)
                                                                                    @php
                                                                                        $otherAnswerValue = null;
                                                                                        
                                                                                        if (isset($qData['multipleOtherAnswers'][$optionId])) {
                                                                                            $otherAnswerValue = $qData['multipleOtherAnswers'][$optionId];
                                                                                        }
                                                                                        
                                                                                        if (!$otherAnswerValue && isset($qData['multipleOtherAnswers'][$answer])) {
                                                                                            $otherAnswerValue = $qData['multipleOtherAnswers'][$answer];
                                                                                        }
                                                                                        
                                                                                        if (!$otherAnswerValue && isset($qData['otherAnswer'])) {
                                                                                            $otherAnswerValue = $qData['otherAnswer'];
                                                                                        }
                                                                                        
                                                                                        if (!$otherAnswerValue && isset($qData['multipleOtherAnswers']) && is_array($qData['multipleOtherAnswers'])) {
                                                                                            foreach($qData['multipleOtherAnswers'] as $key => $value) {
                                                                                                if (!empty($value) && trim($value)) {
                                                                                                    $otherAnswerValue = $value;
                                                                                                    break;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    
                                                                                    <div class="ml-4 sm:ml-6 mt-2 bg-blue-50 border border-blue-200 rounded-lg p-2 sm:p-3">
                                                                                        <div class="flex items-start">
                                                                                            <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                            <div class="flex-1">
                                                                                                <p class="text-sm font-semibold text-blue-800 mb-1">Jawaban Lainnya:</p>
                                                                                                <div class="text-blue-700 text-xs sm:text-sm">
                                                                                                    @if($relatedOption->other_before_text)
                                                                                                        <span class="text-blue-600 mr-1">{{ $relatedOption->other_before_text }}:</span>
                                                                                                    @endif
                                                                                                    
                                                                                                    @if($otherAnswerValue && !empty(trim($otherAnswerValue)))
                                                                                                        <strong class="text-blue-800 bg-white px-2 py-1 rounded border border-blue-300 inline-block">
                                                                                                            {{ $otherAnswerValue }}
                                                                                                        </strong>
                                                                                                    @else
                                                                                                        <span class="text-gray-500 italic bg-gray-100 px-2 py-1 rounded border border-gray-300 inline-block">
                                                                                                            (Tidak ada jawaban tambahan yang diisi)
                                                                                                        </span>
                                                                                                    @endif
                                                                                                    
                                                                                                    @if($relatedOption->other_after_text)
                                                                                                        <span class="text-blue-600 ml-1">{{ $relatedOption->other_after_text }}</span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    @else
                                                                        <li class="text-red-600 text-sm sm:text-base">
                                                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                                                            Data jawaban multiple tidak valid
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        
                                                        @elseif($qData['question']->type === 'numeric')
                                                            <div class="text-green-700">
                                                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                                                                    <div class="flex flex-col sm:flex-row sm:items-center">
                                                                        <i class="fas fa-calculator text-green-600 mr-0 sm:mr-3 mb-2 sm:mb-0 text-xl"></i>
                                                                        <div class="flex-1">
                                                                            @if(!empty($qData['question']->before_text) || !empty($qData['question']->after_text))
                                                                                <div class="flex flex-col sm:flex-row sm:items-center flex-wrap mb-2 space-y-1 sm:space-y-0">
                                                                                    @if(!empty($qData['question']->before_text))
                                                                                        <span class="text-green-700 font-medium mr-0 sm:mr-2 text-sm sm:text-base">{{ $qData['question']->before_text }}</span>
                                                                                    @endif
                                                                                    
                                                                                    <span class="bg-white border border-green-300 rounded-md px-3 sm:px-4 py-2 font-bold text-green-900 text-lg font-mono">
                                                                                        {{ $qData['answer'] }}
                                                                                    </span>
                                                                                    
                                                                                    @if(!empty($qData['question']->after_text))
                                                                                        <span class="text-green-700 font-medium ml-0 sm:ml-2 text-sm sm:text-base">{{ $qData['question']->after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                                
                                                                                <div class="text-xs text-green-600 flex items-center">
                                                                                    <i class="fas fa-info-circle mr-1"></i>
                                                                                    Format: "{{ $qData['question']->before_text ?? '' }} [angka] {{ $qData['question']->after_text ?? '' }}"
                                                                                </div>
                                                                            @else
                                                                                <div class="mb-2">
                                                                                    <span class="text-sm text-green-600 font-medium block mb-1">Nilai yang diinput:</span>
                                                                                    <span class="bg-white border border-green-300 rounded-md px-3 sm:px-4 py-2 font-bold text-green-900 text-xl sm:text-2xl font-mono">
                                                                                        {{ $qData['answer'] }}
                                                                                    </span>
                                                                                </div>
                                                                                
                                                                                <div class="text-xs text-green-600 flex items-center">
                                                                                    <i class="fas fa-info-circle mr-1"></i>
                                                                                    Input numerik ({{ strlen(str_replace([',', '.'], '', $qData['answer'])) }} digit)
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @elseif($qData['question']->type === 'email')
                                                            <!-- Email Answer -->
                                                            <div class="text-green-700">
                                                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                                                    <div class="flex flex-col sm:flex-row sm:items-center">
                                                                        <i class="fas fa-envelope text-blue-600 mr-0 sm:mr-3 mb-2 sm:mb-0 text-xl"></i>
                                                                        <div class="flex-1">
                                                                            @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                <div class="flex flex-col sm:flex-row sm:items-center flex-wrap mb-2 space-y-1 sm:space-y-0">
                                                                                    @if($qData['question']->before_text)
                                                                                        <span class="text-blue-700 font-medium mr-0 sm:mr-2 text-sm sm:text-base">{{ $qData['question']->before_text }}</span>
                                                                                    @endif
                                                                                    
                                                                                    <a href="mailto:{{ $qData['answer'] }}" class="bg-white border border-blue-300 rounded-md px-3 sm:px-4 py-2 font-bold text-blue-900 text-sm sm:text-lg hover:bg-blue-50 transition-colors break-all">
                                                                                        {{ $qData['answer'] }}
                                                                                    </a>
                                                                                    
                                                                                    @if($qData['question']->after_text)
                                                                                        <span class="text-blue-700 font-medium ml-0 sm:ml-2 text-sm sm:text-base">{{ $qData['question']->after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            @else
                                                                                <div class="mb-2">
                                                                                    <span class="text-sm text-blue-600 font-medium block mb-1">Email yang diinput:</span>
                                                                                    <a href="mailto:{{ $qData['answer'] }}" class="bg-white border border-blue-300 rounded-md px-3 sm:px-4 py-2 font-bold text-blue-900 text-sm sm:text-xl hover:bg-blue-50 transition-colors inline-block break-all">
                                                                                        {{ $qData['answer'] }}
                                                                                    </a>
                                                                                </div>
                                                                            @endif
                                                                            
                                                                            <div class="text-xs text-blue-600 flex items-center mt-2">
                                                                                <i class="fas fa-info-circle mr-1"></i>
                                                                                Email terverifikasi • Klik untuk mengirim email
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @elseif($qData['question']->type == 'location')
                                                            <!-- Location Answer - Responsive -->
                                                            <div class="bg-white border border-green-200 rounded-md p-3 sm:p-4">
                                                                <div class="flex items-start">
                                                                    <i class="fas fa-map-marker-alt text-red-600 mr-2 sm:mr-3 mt-1"></i>
                                                                    <div class="flex-1">
                                                                        @if(isset($qData['locationData']))
                                                                            <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-100">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-map-pin text-green-600 mr-2 mt-1 text-lg"></i>
                                                                                    <div class="flex-1">
                                                                                        <p class="font-semibold text-green-800 text-sm sm:text-lg leading-relaxed">{{ $qData['answer'] }}</p>
                                                                                        
                                                                                        <!-- Detail lokasi lengkap - Responsive Grid -->
                                                                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3 text-sm">
                                                                                            @if(isset($qData['locationData']['country_name']))
                                                                                                <div class="bg-white rounded-lg p-2 border border-green-200">
                                                                                                    <p class="text-xs text-green-600 font-medium">Negara:</p>
                                                                                                    <p class="font-medium text-gray-700 text-xs sm:text-sm">{{ $qData['locationData']['country_name'] }}</p>
                                                                                                </div>
                                                                                            @endif
                                                                                            
                                                                                            @if(isset($qData['locationData']['state_name']))
                                                                                                <div class="bg-white rounded-lg p-2 border border-green-200">
                                                                                                    <p class="text-xs text-green-600 font-medium">Provinsi/State:</p>
                                                                                                    <p class="font-medium text-gray-700 text-xs sm:text-sm">{{ $qData['locationData']['state_name'] }}</p>
                                                                                                </div>
                                                                                            @endif
                                                                                            
                                                                                            @if(isset($qData['locationData']['city_name']))
                                                                                                <div class="bg-white rounded-lg p-2 border border-green-200">
                                                                                                    <p class="text-xs text-green-600 font-medium">Kota:</p>
                                                                                                    <p class="font-medium text-gray-700 text-xs sm:text-sm">{{ $qData['locationData']['city_name'] }}</p>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        
                                                                                        <!-- Kode lokasi - Responsive Flow -->
                                                                                        <div class="mt-3 text-xs text-gray-500">
                                                                                            @if(isset($qData['locationData']['country_code']))
                                                                                                <span class="inline-block bg-gray-100 rounded-full px-2 sm:px-3 py-1 mr-2 mb-1 sm:mb-2">
                                                                                                    <i class="fas fa-globe-americas mr-1"></i>
                                                                                                    <span class="hidden sm:inline">Kode Negara: </span>{{ $qData['locationData']['country_code'] }}
                                                                                                </span>
                                                                                            @endif
                                                                                            
                                                                                            @if(isset($qData['locationData']['state_code']))
                                                                                                <span class="inline-block bg-gray-100 rounded-full px-2 sm:px-3 py-1 mr-2 mb-1 sm:mb-2">
                                                                                                    <i class="fas fa-map mr-1"></i>
                                                                                                    <span class="hidden sm:inline">Kode Provinsi: </span>{{ $qData['locationData']['state_code'] }}
                                                                                                </span>
                                                                                            @endif
                                                                                            
                                                                                            @if(isset($qData['locationData']['city_code']))
                                                                                                <span class="inline-block bg-gray-100 rounded-full px-2 sm:px-3 py-1 mr-2 mb-1 sm:mb-2">
                                                                                                    <i class="fas fa-city mr-1"></i>
                                                                                                    <span class="hidden sm:inline">Kode Kota: </span>{{ $qData['locationData']['city_code'] }}
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <!-- Fallback jika tidak ada data lokasi terstruktur -->
                                                                            <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-100">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-map-pin text-green-600 mr-2 mt-1 text-sm"></i>
                                                                                    <div class="flex-1">
                                                                                        <p class="font-semibold text-green-800 text-sm sm:text-lg leading-relaxed">{{ $qData['answer'] }}</p>
                                                                                        <p class="text-xs text-gray-500 mt-2">
                                                                                            <i class="fas fa-info-circle mr-1"></i>
                                                                                            Data lokasi terperinci tidak tersedia
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @elseif($qData['question']->type === 'option')
                                                            <!-- Single Option Answer - Responsive -->
                                                            <div class="bg-white border border-green-200 rounded-md p-3 sm:p-4">
                                                                @php
                                                                    $option = null;
                                                                    $option = $qData['question']->options->where('option', $qData['answer'])->first();
                                                                    
                                                                    if (!$option && is_numeric($qData['answer'])) {
                                                                        $option = $qData['question']->options->where('id_questions_options', $qData['answer'])->first();
                                                                    }
                                                                @endphp
                                                                
                                                                <div class="flex items-start">
                                                                    <div class="flex-1">
                                                                        <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-100">
                                                                            <div class="flex items-start">
                                                                                <i class="fas fa-check-circle text-green-600 mr-2 mt-1 text-sm"></i>
                                                                                <div class="flex-1">
                                                                                    <h6 class="font-medium text-green-800 mb-2 flex items-center text-sm sm:text-base">
                                                                                        <span class="text-sm text-green-600 mr-2">Pilihan yang dipilih:</span>
                                                                                    </h6>
                                                                                    <p class="font-semibold text-green-800 text-sm sm:text-lg leading-relaxed">
                                                                                        {{ $option ? $option->option : $qData['answer'] }}
                                                                                    </p>
                                                                                    
                                                                                    @if($option && $option->is_other_option && isset($qData['otherAnswer']) && !empty(trim($qData['otherAnswer'])))
                                                                                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                                                                            <div class="flex items-start">
                                                                                                <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                                <div class="flex-1">
                                                                                                    <p class="text-sm font-semibold text-blue-800 mb-1">Jawaban Lainnya:</p>
                                                                                                    <div class="text-blue-700 text-xs sm:text-sm">
                                                                                                        @if($option->other_before_text)
                                                                                                            <span class="text-blue-600 mr-1">{{ $option->other_before_text }}:</span>
                                                                                                        @endif
                                                                                                        <strong class="text-blue-900 bg-white px-2 sm:px-3 py-1 sm:py-2 rounded border border-blue-300 inline-block">
                                                                                                            {{ $qData['otherAnswer'] }}
                                                                                                        </strong>
                                                                                                        @if($option->other_after_text)
                                                                                                            <span class="text-blue-600 ml-1">{{ $option->other_after_text }}</span>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    
                                                                                    <div class="text-xs text-green-600 flex items-center mt-2">
                                                                                        <i class="fas fa-info-circle mr-1"></i>
                                                                                        Pilihan tunggal (radio button)
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @elseif($qData['question']->type == 'rating')
                                                            <!-- Rating Answer Display - Responsive -->
                                                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-3 sm:p-4">
                                                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                                                    <div class="flex flex-col sm:flex-row sm:items-center">
                                                                        <i class="fas fa-star text-purple-600 mr-0 sm:mr-3 mb-2 sm:mb-0 text-lg"></i>
                                                                        <div class="flex-1">
                                                                            <div class="mb-2">
                                                                                <span class="text-sm text-purple-600 font-medium block mb-1">Rating yang dipilih:</span>
                                                                                <span class="font-bold text-purple-800 text-lg sm:text-xl">
                                                                                    @if($qData['ratingOption'])
                                                                                        {{ $qData['ratingOption']->option }}
                                                                                    @else
                                                                                        {{ $qData['answer'] }}
                                                                                    @endif
                                                                                </span>
                                                                            </div>
                                                                            @if($qData['otherAnswer'])
                                                                                <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-2 sm:p-3">
                                                                                    <div class="flex items-start">
                                                                                        <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                        <div class="flex-1">
                                                                                            <span class="font-semibold text-blue-800 text-sm">Keterangan Tambahan:</span>
                                                                                            <p class="text-blue-700 mt-1 text-xs sm:text-sm">{{ $qData['otherAnswer'] }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Rating badge -->
                                                                    <div class="ml-0 sm:ml-4 self-start sm:self-center">
                                                                        <div class="bg-white rounded-lg px-3 sm:px-4 py-2 border border-purple-300 shadow-sm">
                                                                            <div class="text-center">
                                                                                <div class="text-xl sm:text-2xl font-bold text-purple-600">★</div>
                                                                                <div class="text-xs text-purple-600 font-medium">Rating</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @elseif($qData['question']->type == 'scale')
                                                            <!-- Scale Answer Display - Responsive -->
                                                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                                                    <div class="flex flex-col sm:flex-row sm:items-center">
                                                                        <i class="fas fa-chart-line text-blue-600 mr-0 sm:mr-3 mb-2 sm:mb-0 text-lg"></i>
                                                                        <div>
                                                                            <span class="text-sm text-blue-600 font-medium">Skor yang dipilih:</span>
                                                                            <div class="flex items-center mt-1">
                                                                                @php
                                                                                    $scaleValue = (int) $qData['answer'];
                                                                                    $scaleColor = 'gray';
                                                                                    
                                                                                    if ($scaleValue == 1) {
                                                                                        $scaleColor = 'red';
                                                                                    } elseif ($scaleValue == 2) {
                                                                                        $scaleColor = 'orange';
                                                                                    } elseif ($scaleValue == 3) {
                                                                                        $scaleColor = 'yellow';
                                                                                    } elseif ($scaleValue == 4) {
                                                                                        $scaleColor = 'blue';
                                                                                    } elseif ($scaleValue == 5) {
                                                                                        $scaleColor = 'green';
                                                                                    }
                                                                                @endphp
                                                                                
                                                                                <span class="text-2xl sm:text-3xl font-bold text-{{ $scaleColor }}-600 mr-3">{{ $qData['answer'] }}</span>
                                                                                <span class="text-sm text-gray-600">dari 5</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Visual scale representation -->
                                                                    <div class="flex items-center space-x-1">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-full {{ $i <= $scaleValue ? 'bg-'.$scaleColor.'-500' : 'bg-gray-300' }}"></div>
                                                                        @endfor
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @else
                                                            <!-- Default Answer (text, date, option) - Responsive -->
                                                            <div class="bg-white border border-green-200 rounded-md p-3 sm:p-4">
                                                                <div class="flex items-start">
                                                                    <i class="fas fa-{{ $qData['question']->type == 'date' ? 'calendar-alt' : ($qData['question']->type == 'option' ? 'check-circle' : 'quote-left') }} text-green-600 mr-2 sm:mr-3 mt-1"></i>
                                                                    <div class="flex-1">
                                                                        @if($qData['question']->type == 'text')
                                                                            <div class="bg-green-50 rounded-lg p-3 sm:p-4 border border-green-100">
                                                                                <p class="font-semibold text-green-800 text-sm sm:text-lg leading-relaxed whitespace-pre-wrap">{{ $qData['answer'] }}</p>
                                                                            </div>
                                                                        @else
                                                                            <span class="font-semibold text-green-800 text-sm sm:text-lg">{{ $qData['answer'] }}</span>
                                                                        @endif
                                                                        
                                                                        @if($qData['otherAnswer'])
                                                                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                    <div class="flex-1">
                                                                                        <h6 class="font-semibold text-blue-800 mb-2 text-sm sm:text-base">Jawaban Lainnya:</h6>
                                                                                        <div class="text-blue-700 text-xs sm:text-sm">
                                                                                            @if($qData['otherOption'] && $qData['otherOption']->other_before_text)
                                                                                                <span class="text-blue-600 mr-1">{{ $qData['otherOption']->other_before_text }}:</span>
                                                                                            @endif
                                                                                            <strong class="text-blue-900 bg-white px-2 sm:px-3 py-1 sm:py-2 rounded border border-blue-300 inline-block">
                                                                                                {{ $qData['otherAnswer'] }}
                                                                                            </strong>
                                                                                            @if($qData['otherOption'] && $qData['otherOption']->other_after_text)
                                                                                                <span class="text-blue-600 ml-1">{{ $qData['otherOption']->other_after_text }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <!-- No Answer -->
                                                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 sm:p-4">
                                                            <p class="text-yellow-700 flex items-center text-sm sm:text-base">
                                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                                Pertanyaan ini belum dijawab
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php $questionNumber++; @endphp
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-comment-slash text-4xl sm:text-6xl"></i>
                            </div>
                            <h4 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Jawaban</h4>
                            <p class="text-gray-500 max-w-md mx-auto text-sm sm:text-base">
                                Belum ada jawaban yang tersimpan untuk kuesioner ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons - Responsive -->
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mt-6 sm:mt-8 space-y-4 lg:space-y-0 no-print">
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('company.questionnaire.results') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium transition-colors duration-200 text-center text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>
                    @if($userAnswer->periode->status == 'active')
                        <a href="{{ route('company.questionnaire.select-alumni', $userAnswer->id_periode) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium transition-colors duration-200 text-center text-sm sm:text-base">
                            <i class="fas fa-plus mr-2"></i> 
                            <span class="hidden sm:inline">Isi Kuesioner Alumni Lain</span>
                            <span class="sm:hidden">Alumni Lain</span>
                        </a>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                        <a href="{{ route('company.questionnaire.fill', [$userAnswer->id_periode, $userAnswer->nim]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium transition-colors duration-200 text-center text-sm sm:text-base">
                            <i class="fas fa-edit mr-2"></i> Lanjutkan Mengisi
                        </a>
                    @endif
                    
                    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-print mr-2"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    #sidebar { display: none !important; }
    #main-content { margin-left: 0 !important; width: 100% !important; }
    .bg-gradient-to-r { background: #f8fafc !important; }
    .shadow-md { box-shadow: none !important; }
    body { font-size: 12px; }
    .p-6 { padding: 1rem !important; }
}
</style>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection