@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Detail Respons Kuesioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Action buttons card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Detail Respons Kuesioner</h3>
                    <p class="text-sm text-gray-600">Periode: {{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }}</p>
                </div>
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                    <button onclick="printResponse()" 
                        class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-md text-xs sm:text-sm font-medium transition">
                        <i class="fas fa-print"></i> 
                        <span>Print</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Breadcrumb
        <nav class="mb-4 sm:mb-6 no-print">
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-3 sm:p-4">
                <ol class="flex items-center space-x-2 text-xs sm:text-sm">
                    <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li><a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" class="text-blue-600 hover:underline">Respons</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700 font-medium">Detail</li>
                </ol>
            </div>
        </nav> -->

        <!-- Content Section -->
        <div id="printable-content">
            <!-- Response Summary Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-md p-6 mb-6 border border-blue-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-900 mb-2">Ringkasan Respons</h2>
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $userAnswer->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $userAnswer->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                {{ $userAnswer->status == 'completed' ? 'Selesai' : 'Dalam Proses' }}
                            </span>
                            @if(isset($userAnswer->user->alumni))
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Alumni
                                </span>
                            @elseif(isset($userAnswer->user->company))
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-building mr-1"></i>
                                    Perusahaan
                                </span>
                                
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">ID Respons</p>
                        <p class="text-lg font-bold text-blue-900">#{{ $userAnswer->id_user_answer }}</p>
                    </div>
                </div>
                @if(isset($userAnswer->user->company))
                <p class="text-sm text-gray-700">
                    <i class="fas fa-user-graduate mr-1"></i>
                    Dinilai: <strong>{{ $userAnswer->nim }}</strong><br> Nama Alumni  :<strong>{{ $userAnswer->alumni->name ?? 'Tidak Diketahui' }}</strong>
                </p>
                @endif
                <!-- Progress Bar -->
                @if(isset($questionsWithAnswers))
                    @php
                        $totalQuestions = 0;
                        $answeredQuestions = 0;
                        $skippedQuestions = 0;
                        
                        foreach($questionsWithAnswers as $categoryData) {
                            foreach($categoryData['questions'] as $qData) {
                                $shouldShow = true;
                                
                                // Check if question status is visible
                                if ($qData['question']->status !== 'visible') {
                                    $shouldShow = false;
                                }
                                
                                if ($shouldShow && $qData['question']->depends_on) {
                                    $parentAnswered = false;
                                    foreach($questionsWithAnswers as $catData) {
                                        foreach($catData['questions'] as $parentQData) {
                                            if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                $hasParentAnswer = false;
                                                $parentAnswersToCheck = [];
                                                
                                                // Check for single answer
                                                if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                    $hasParentAnswer = true;
                                                    $parentAnswersToCheck = is_array($parentQData['answer']) ? $parentQData['answer'] : [$parentQData['answer']];
                                                }
                                                
                                                // Check for multiple answers
                                                if (isset($parentQData['multipleAnswers']) && is_array($parentQData['multipleAnswers']) && count($parentQData['multipleAnswers']) > 0) {
                                                    $hasParentAnswer = true;
                                                    $parentAnswersToCheck = array_merge($parentAnswersToCheck, $parentQData['multipleAnswers']);
                                                }
                                                
                                                if ($hasParentAnswer) {
                                                    // Support multi-value depends_value (OR logic)
                                                    $dependsValues = is_string($qData['question']->depends_value) ? 
                                                        explode(',', $qData['question']->depends_value) : 
                                                        [$qData['question']->depends_value];
                                                    $dependsValues = array_map('trim', $dependsValues);
                                                    
                                                    // Check if parent answer matches any of the depends_value options
                                                    foreach ($parentAnswersToCheck as $answer) {
                                                        $trimmedAnswer = trim($answer);
                                                        
                                                        // Direct match check
                                                        if (in_array($trimmedAnswer, $dependsValues)) {
                                                            $parentAnswered = true;
                                                            break 2;
                                                        }
                                                        
                                                        // Check if answer is text but depends_value contains option ID
                                                        $matchingOption = $parentQData['question']->options->where('option', $trimmedAnswer)->first();
                                                        if ($matchingOption && in_array((string)$matchingOption->id_questions_options, $dependsValues)) {
                                                            $parentAnswered = true;
                                                            break 2;
                                                        }
                                                        
                                                        // Check if answer is ID but depends_value contains option text
                                                        if (is_numeric($trimmedAnswer)) {
                                                            $optionById = $parentQData['question']->options->where('id_questions_options', $trimmedAnswer)->first();
                                                            if ($optionById && in_array($optionById->option, $dependsValues)) {
                                                                $parentAnswered = true;
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                                break 2;
                                            }
                                        }
                                    }
                                    $shouldShow = $parentAnswered;
                                }
                                
                                if ($shouldShow) {
                                    $totalQuestions++;
                                    // Check if question is answered
                                    $isAnswered = false;
                                    if ($qData['question']->type == 'multiple') {
                                        $isAnswered = isset($qData['multipleAnswers']) && count($qData['multipleAnswers']) > 0;
                                    } else {
                                        $isAnswered = !empty($qData['answer']);
                                    }
                                    
                                    if ($isAnswered) {
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
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-blue-900">Progress Pengisian</span>
                            <span class="text-sm font-medium text-blue-900">{{ $answeredQuestions }}/{{ $totalQuestions }} pertanyaan ({{ $progressPercentage }}%)</span>
                        </div>
                        <div class="w-full bg-blue-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        @if($skippedQuestions > 0)
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $skippedQuestions }} pertanyaan dilewati karena tidak memenuhi syarat dependensi
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                    Informasi Responden
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-id-card text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Nama Lengkap</p>
                        </div>
                        @if(isset($userAnswer->user->alumni))
                            <p class="font-semibold text-gray-900">{{ $userAnswer->user->alumni->name }}</p>
                            @if($userAnswer->user->alumni->nim)
                                <p class="text-sm text-gray-600">NIM: {{ $userAnswer->user->alumni->nim }}</p>
                            @endif
                        @elseif(isset($userAnswer->user->company))
                            <p class="font-semibold text-gray-900">{{ $userAnswer->user->company->company_name }}</p>
                            <p class="text-sm text-gray-600">Perusahaan</p>
                        @else
                            <p class="font-semibold text-gray-900">User #{{ $userAnswer->id_user }}</p>
                        @endif
                    </div>

                    @if(isset($userAnswer->user->alumni))
                        @if($userAnswer->user->alumni->program_studi)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-book text-blue-600 mr-2"></i>
                                    <p class="text-sm text-gray-600 font-medium">Program Studi</p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $userAnswer->user->alumni->program_studi }}</p>
                            </div>
                        @endif

                        @if($userAnswer->user->alumni->tahun_lulus)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                                    <p class="text-sm text-gray-600 font-medium">Tahun Lulus</p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $userAnswer->user->alumni->tahun_lulus }}</p>
                            </div>
                        @endif
                    @endif

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Tanggal Pengisian</p>
                        </div>
                        <p class="font-semibold text-gray-900">{{ $userAnswer->created_at->format('d M Y') }}</p>
                        <p class="text-sm text-gray-600">{{ $userAnswer->created_at->format('H:i') }} WIB</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-clock text-blue-600 mr-2"></i>
                            <p class="text-sm text-gray-600 font-medium">Periode Kuesioner</p>
                        </div>
                        <p class="font-semibold text-gray-900">{{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }}</p>
                        <span class="px-2 py-1 rounded-full text-xs {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($periode->status) }}
                        </span>
                    </div>

                    @if($userAnswer->updated_at != $userAnswer->created_at)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-edit text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Terakhir Diperbarui</p>
                            </div>
                            <p class="font-semibold text-gray-900">{{ $userAnswer->updated_at->format('d M Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $userAnswer->updated_at->format('H:i') }} WIB</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-comment-dots mr-2 text-blue-600"></i>
                        Jawaban Kuesioner
                    </h3>
                </div>

                <div class="p-6">
                    @if(isset($questionsWithAnswers) && count($questionsWithAnswers) > 0)
                        @foreach($questionsWithAnswers as $categoryIndex => $categoryData)
                            @php
                                // Filter pertanyaan yang bisa ditampilkan untuk kategori ini
                                $visibleQuestions = collect($categoryData['questions'])->filter(function($qData) use ($questionsWithAnswers) {
                                    $shouldShow = true;
                                    
                                    // Check if question status is visible
                                    if ($qData['question']->status !== 'visible') {
                                        return false;
                                    }
                                    
                                    if ($qData['question']->depends_on) {
                                        $parentAnswered = false;
                                        foreach($questionsWithAnswers as $catData) {
                                            foreach($catData['questions'] as $parentQData) {
                                                if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                    $hasParentAnswer = false;
                                                    $parentAnswersToCheck = [];
                                                    
                                                    // Check for single answer
                                                    if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                        $hasParentAnswer = true;
                                                        $parentAnswersToCheck = is_array($parentQData['answer']) ? $parentQData['answer'] : [$parentQData['answer']];
                                                    }
                                                    
                                                    // Check for multiple answers
                                                    if (isset($parentQData['multipleAnswers']) && is_array($parentQData['multipleAnswers']) && count($parentQData['multipleAnswers']) > 0) {
                                                        $hasParentAnswer = true;
                                                        $parentAnswersToCheck = array_merge($parentAnswersToCheck, $parentQData['multipleAnswers']);
                                                    }
                                                    
                                                    if ($hasParentAnswer) {
                                                        // Support multi-value depends_value (OR logic)
                                                        $dependsValues = is_string($qData['question']->depends_value) ? 
                                                            explode(',', $qData['question']->depends_value) : 
                                                            [$qData['question']->depends_value];
                                                        $dependsValues = array_map('trim', $dependsValues);
                                                        
                                                        // Check if parent answer matches any of the depends_value options
                                                        foreach ($parentAnswersToCheck as $answer) {
                                                            $trimmedAnswer = trim($answer);
                                                            
                                                            // Direct match check
                                                            if (in_array($trimmedAnswer, $dependsValues)) {
                                                                $parentAnswered = true;
                                                                break 2;
                                                            }
                                                            
                                                            // Check if answer is text but depends_value contains option ID
                                                            $matchingOption = $parentQData['question']->options->where('option', $trimmedAnswer)->first();
                                                            if ($matchingOption && in_array((string)$matchingOption->id_questions_options, $dependsValues)) {
                                                                $parentAnswered = true;
                                                                break 2;
                                                            }
                                                            
                                                            // Check if answer is ID but depends_value contains option text
                                                            if (is_numeric($trimmedAnswer)) {
                                                                $optionById = $parentQData['question']->options->where('id_questions_options', $trimmedAnswer)->first();
                                                                if ($optionById && in_array($optionById->option, $dependsValues)) {
                                                                    $parentAnswered = true;
                                                                    break 2;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    break 2;
                                                }
                                            }
                                        }
                                        $shouldShow = $parentAnswered;
                                    }
                                    return $shouldShow;
                                });
                                
                                // Hitung kategori yang sudah ditampilkan untuk index yang benar
                                $displayedCategoryIndex = 0;
                                for($i = 0; $i < $categoryIndex; $i++) {
                                    $prevCategoryVisibleQuestions = collect($questionsWithAnswers[$i]['questions'])->filter(function($qData) use ($questionsWithAnswers) {
                                        $shouldShow = true;
                                        
                                        // Check if question status is visible
                                        if ($qData['question']->status !== 'visible') {
                                            return false;
                                        }
                                        
                                        if ($qData['question']->depends_on) {
                                            $parentAnswered = false;
                                            foreach($questionsWithAnswers as $catData) {
                                                foreach($catData['questions'] as $parentQData) {
                                                    if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                        $hasParentAnswer = false;
                                                        $parentAnswersToCheck = [];
                                                        
                                                        if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                            $hasParentAnswer = true;
                                                            $parentAnswersToCheck = is_array($parentQData['answer']) ? $parentQData['answer'] : [$parentQData['answer']];
                                                        }
                                                        
                                                        if (isset($parentQData['multipleAnswers']) && is_array($parentQData['multipleAnswers']) && count($parentQData['multipleAnswers']) > 0) {
                                                            $hasParentAnswer = true;
                                                            $parentAnswersToCheck = array_merge($parentAnswersToCheck, $parentQData['multipleAnswers']);
                                                        }
                                                        
                                                        if ($hasParentAnswer) {
                                                            $dependsValues = is_string($qData['question']->depends_value) ? 
                                                                explode(',', $qData['question']->depends_value) : 
                                                                [$qData['question']->depends_value];
                                                            $dependsValues = array_map('trim', $dependsValues);
                                                            
                                                            foreach ($parentAnswersToCheck as $answer) {
                                                                $trimmedAnswer = trim($answer);
                                                                
                                                                if (in_array($trimmedAnswer, $dependsValues)) {
                                                                    $parentAnswered = true;
                                                                    break 2;
                                                                }
                                                                
                                                                $matchingOption = $parentQData['question']->options->where('option', $trimmedAnswer)->first();
                                                                if ($matchingOption && in_array((string)$matchingOption->id_questions_options, $dependsValues)) {
                                                                    $parentAnswered = true;
                                                                    break 2;
                                                                }
                                                                
                                                                if (is_numeric($trimmedAnswer)) {
                                                                    $optionById = $parentQData['question']->options->where('id_questions_options', $trimmedAnswer)->first();
                                                                    if ($optionById && in_array($optionById->option, $dependsValues)) {
                                                                        $parentAnswered = true;
                                                                        break 2;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        break 2;
                                                    }
                                                }
                                            }
                                            $shouldShow = $parentAnswered;
                                        }
                                        return $shouldShow;
                                    });
                                    
                                    if ($prevCategoryVisibleQuestions->count() > 0) {
                                        $displayedCategoryIndex++;
                                    }
                                }
                            @endphp
                            
                            {{-- Hanya tampilkan kategori jika ada pertanyaan yang bisa ditampilkan --}}
                            @if($visibleQuestions->count() > 0)
                            <div class="mb-8 {{ $displayedCategoryIndex > 0 ? 'border-t border-gray-200 pt-8' : '' }}">
                                <!-- Category Header -->
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg mb-6 border border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium px-3 py-1 rounded-full {{ $categoryData['category']->for_type == 'alumni' ? 'bg-indigo-100 text-indigo-700' : ($categoryData['category']->for_type == 'company' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                                <i class="fas {{ $categoryData['category']->for_type == 'alumni' ? 'fa-graduation-cap' : ($categoryData['category']->for_type == 'company' ? 'fa-building' : 'fa-users') }} mr-1"></i>
                                                {{ $categoryData['category']->category_name }}
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ $visibleQuestions->count() }} pertanyaan
                                            </span>
                                        </div>
                                    </div>
                                    @if($categoryData['category']->description)
                                        <div class="mt-3 pt-3 border-t border-blue-200">
                                            <p class="text-sm text-blue-700 flex items-start">
                                                <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                                {{ $categoryData['category']->description }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Questions -->
                                <div class="space-y-6">
                                    @php $questionNumber = 1; @endphp
                                    @foreach($categoryData['questions'] as $qData)
                                        @php
                                            $shouldShow = true;
                                            
                                            // Check if question status is visible
                                            if ($qData['question']->status !== 'visible') {
                                                $shouldShow = false;
                                            }
                                            
                                            if ($shouldShow && $qData['question']->depends_on) {
                                                $parentAnswered = false;
                                                foreach($questionsWithAnswers as $catData) {
                                                    foreach($catData['questions'] as $parentQData) {
                                                        if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                            $hasParentAnswer = false;
                                                            $parentAnswersToCheck = [];
                                                            
                                                            // Check for single answer
                                                            if (isset($parentQData['answer']) && $parentQData['answer']) {
                                                                $hasParentAnswer = true;
                                                                $parentAnswersToCheck = is_array($parentQData['answer']) ? $parentQData['answer'] : [$parentQData['answer']];
                                                            }
                                                            
                                                            // Check for multiple answers
                                                            if (isset($parentQData['multipleAnswers']) && is_array($parentQData['multipleAnswers']) && count($parentQData['multipleAnswers']) > 0) {
                                                                $hasParentAnswer = true;
                                                                $parentAnswersToCheck = array_merge($parentAnswersToCheck, $parentQData['multipleAnswers']);
                                                            }
                                                            
                                                            if ($hasParentAnswer) {
                                                                // Support multi-value depends_value (OR logic)
                                                                $dependsValues = is_string($qData['question']->depends_value) ? 
                                                                    explode(',', $qData['question']->depends_value) : 
                                                                    [$qData['question']->depends_value];
                                                                $dependsValues = array_map('trim', $dependsValues);
                                                                
                                                                // Check if parent answer matches any of the depends_value options
                                                                foreach ($parentAnswersToCheck as $answer) {
                                                                    $trimmedAnswer = trim($answer);
                                                                    
                                                                    // Direct match check
                                                                    if (in_array($trimmedAnswer, $dependsValues)) {
                                                                        $parentAnswered = true;
                                                                        break 2;
                                                                    }
                                                                    
                                                                    // Check if answer is text but depends_value contains option ID
                                                                    $matchingOption = $parentQData['question']->options->where('option', $trimmedAnswer)->first();
                                                                    if ($matchingOption && in_array((string)$matchingOption->id_questions_options, $dependsValues)) {
                                                                        $parentAnswered = true;
                                                                        break 2;
                                                                    }
                                                                    
                                                                    // Check if answer is ID but depends_value contains option text
                                                                    if (is_numeric($trimmedAnswer)) {
                                                                        $optionById = $parentQData['question']->options->where('id_questions_options', $trimmedAnswer)->first();
                                                                        if ($optionById && in_array($optionById->option, $dependsValues)) {
                                                                            $parentAnswered = true;
                                                                            break 2;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            break 2;
                                                        }
                                                    }
                                                }
                                                $shouldShow = $parentAnswered;
                                            }
                                            
                                            $hasAnswer = false;
                                            if ($qData['question']->type == 'multiple') {
                                                $hasAnswer = isset($qData['multipleAnswers']) && count($qData['multipleAnswers']) > 0;
                                            } else {
                                                $hasAnswer = !empty($qData['answer']);
                                            }
                                        @endphp
                                        
                                        @if($shouldShow)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                                                <!-- Question Header -->
                                                <div class="mb-4">
                                                    <!-- Mobile: Stack vertically, Desktop: Side by side -->
                                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                                                        <!-- Question content -->
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-start">
                                                                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full mr-3 mt-1 flex-shrink-0">{{ $questionNumber }}</span>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-semibold text-gray-900 leading-relaxed break-words">{{ $qData['question']->question }}</p>
                                                                    
                                                                    @if($qData['question']->depends_on)
                                                                        <div class="mt-2 bg-blue-50 border border-blue-200 rounded-md p-3">
                                                                            <p class="text-xs text-blue-700 flex items-start">
                                                                                <i class="fas fa-link mr-2 mt-0.5 flex-shrink-0"></i> 
                                                                                <span class="font-medium">Pertanyaan bersyarat</span>
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Question type badge -->
                                                        <div class="flex-shrink-0 self-start sm:ml-3">
                                                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full inline-flex items-center">
                                                                <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'numeric' ? 'calculator' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'multiple' ? 'check-square' : ($qData['question']->type == 'location' ? 'map-marker-alt' : ($qData['question']->type == 'rating' ? 'star' : ($qData['question']->type == 'scale' ? 'chart-line' : ($qData['question']->type == 'email' ? 'envelope' : 'calendar-alt'))))))) }} mr-1 flex-shrink-0"></i>
                                                                <span class="whitespace-nowrap">{{ $qData['question']->type == 'numeric' ? 'Numerik' : ucfirst($qData['question']->type) }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Answer Section -->
                                                <div class="border-t border-gray-300 pt-4">


                                                    @if($hasAnswer)
                                                        @if($qData['question']->type === 'multiple')
                                                            <!-- Multiple Choice Answers -->
                                                            <div class="bg-white border border-green-200 rounded-md p-4">
                                                                <h6 class="font-medium text-green-800 mb-2 flex items-center">
                                                                    <i class="fas fa-check-square mr-2"></i>
                                                                    Pilihan yang Dipilih:
                                                                </h6>
                                                                <ul class="space-y-3">
                                                                    @if(isset($qData['multipleAnswers']) && is_array($qData['multipleAnswers']))
                                                                        @foreach($qData['multipleAnswers'] as $answer)
                                                                            @php
                                                                                $relatedOption = $qData['question']->options->where('option', $answer)->first();
                                                                            @endphp
                                                                            <li class="text-green-700">
                                                                                <div class="flex items-center">
                                                                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                                                                    <span class="font-medium">{{ $answer }}</span>
                                                                                </div>
                                                                                
                                                                                @if($relatedOption && $relatedOption->is_other_option && isset($qData['multipleOtherAnswers'][$relatedOption->id_questions_options]))
                                                                                    <div class="ml-6 mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                                        <div class="flex items-start">
                                                                                            <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                            <div class="flex-1">
                                                                                                <p class="text-sm font-semibold text-blue-800 mb-1">Jawaban Lainnya:</p>
                                                                                                <div class="text-blue-700">
                                                                                                    @if($relatedOption->other_before_text)
                                                                                                        <span class="text-blue-600 mr-1">{{ $relatedOption->other_before_text }}:</span>
                                                                                                    @endif
                                                                                                    <strong class="text-blue-800 bg-white px-2 py-1 rounded border border-blue-300">
                                                                                                        {{ $qData['multipleOtherAnswers'][$relatedOption->id_questions_options] }}
                                                                                                    </strong>
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
                                                                        <li class="text-red-600">
                                                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                                                            Data jawaban multiple tidak valid
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <!-- Single Answer -->
                                                            <div class="bg-white border border-green-200 rounded-md p-4">
                                                                <h6 class="font-medium text-green-800 mb-3 flex items-center">
                                                                    <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'numeric' ? 'calculator' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'location' ? 'map-marker-alt' : ($qData['question']->type == 'rating' ? 'star' : ($qData['question']->type == 'scale' ? 'chart-line' : ($qData['question']->type == 'email' ? 'envelope' : 'calendar-alt')))))) }} mr-2"></i>
                                                                    Jawaban:
                                                                </h6>
                                                                
                                                                @if($qData['question']->type === 'option')
                                                                    <div class="text-green-700">
                                                                        <div class="mb-2">
                                                                            <span class="text-lg font-semibold">{{ $qData['answer'] }}</span>
                                                                        </div>
                                                                        
                                                                        @if(isset($qData['otherAnswer']) && !empty($qData['otherAnswer']))
                                                                            @php
                                                                                $selectedOption = $qData['question']->options->where('option', $qData['answer'])->first();
                                                                            @endphp
                                                                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                    <div class="flex-1">
                                                                                        <h6 class="font-semibold text-blue-800 mb-2">Jawaban Lainnya:</h6>
                                                                                        <div class="text-blue-700">
                                                                                            @if($selectedOption && $selectedOption->other_before_text)
                                                                                                <span class="text-blue-600 mr-1">{{ $selectedOption->other_before_text }}:</span>
                                                                                            @endif
                                                                                            <strong class="text-blue-900 bg-white px-3 py-2 rounded border border-blue-300 inline-block">
                                                                                                {{ $qData['otherAnswer'] }}
                                                                                            </strong>
                                                                                            @if($selectedOption && $selectedOption->other_after_text)
                                                                                                <span class="text-blue-600 ml-1">{{ $selectedOption->other_after_text }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @elseif($qData['question']->type === 'rating')
                                                                    <!-- Enhanced Rating Answer Display -->
                                                                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                                                                        <div class="flex items-center">
                                                                            <i class="fas fa-star text-purple-600 mr-3 text-lg"></i>
                                                                            <div class="flex-1">
                                                                                <span class="font-semibold text-purple-800 text-lg">{{ $qData['answer'] }}</span>
                                                                                <div class="mt-2">
                                                                                    @php
                                                                                        $ratingLevel = strtolower($qData['answer']);
                                                                                        $ratingColor = 'gray';
                                                                                        $ratingIcon = 'fa-star';
                                                                                        
                                                                                        if (strpos($ratingLevel, 'kurang') !== false || strpos($ratingLevel, 'buruk') !== false) {
                                                                                            $ratingColor = 'red';
                                                                                            $ratingIcon = 'fa-star';
                                                                                        } elseif (strpos($ratingLevel, 'cukup') !== false) {
                                                                                            $ratingColor = 'yellow';
                                                                                            $ratingIcon = 'fa-star-half-alt';
                                                                                        } elseif (strpos($ratingLevel, 'baik sekali') !== false || strpos($ratingLevel, 'sangat baik') !== false || strpos($ratingLevel, 'excellent') !== false) {
                                                                                            $ratingColor = 'green';
                                                                                            $ratingIcon = 'fa-star';
                                                                                        } elseif (strpos($ratingLevel, 'baik') !== false || strpos($ratingLevel, 'good') !== false) {
                                                                                            $ratingColor = 'blue';
                                                                                            $ratingIcon = 'fa-star';
                                                                                        }
                                                                                    @endphp
                                                                                    
                                                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                                                        bg-{{ $ratingColor }}-100 text-{{ $ratingColor }}-800">
                                                                                        <i class="fas {{ $ratingIcon }} mr-2"></i>
                                                                                        Rating: {{ $qData['answer'] }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($qData['question']->type === 'numeric')
                                                                    <!-- Enhanced Numeric Answer Display -->
                                                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                                                        <div class="flex items-center">
                                                                            <i class="fas fa-calculator text-green-600 mr-3 text-xl"></i>
                                                                            <div class="flex-1">
                                                                                @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                    <div class="flex items-center flex-wrap mb-2">
                                                                                        @if($qData['question']->before_text)
                                                                                            <span class="text-green-700 font-medium mr-2">{{ $qData['question']->before_text }}</span>
                                                                                        @endif
                                                                                        
                                                                                        <span class="bg-white border border-green-300 rounded-md px-4 py-2 font-bold text-green-900 text-lg font-mono">
                                                                                            {{ $qData['answer'] }}
                                                                                        </span>
                                                                                        
                                                                                        @if($qData['question']->after_text)
                                                                                            <span class="text-green-700 font-medium ml-2">{{ $qData['question']->after_text }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    
                                                                                    <div class="text-xs text-green-600 flex items-center">
                                                                                        <i class="fas fa-info-circle mr-1"></i>
                                                                                        Format: "{{ $qData['question']->before_text ?? '' }} [angka] {{ $qData['question']->after_text ?? '' }}"
                                                                                    </div>
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <span class="text-sm text-green-600 font-medium block mb-1">Nilai yang diinput:</span>
                                                                                        <span class="bg-white border border-green-300 rounded-md px-4 py-2 font-bold text-green-900 text-2xl font-mono">
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

                                                                @elseif($qData['question']->type === 'email')
                                                                    <!-- Enhanced Email Answer Display -->
                                                                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4">
                                                                        <div class="flex items-center">
                                                                            <i class="fas fa-envelope text-blue-600 mr-3 text-xl"></i>
                                                                            <div class="flex-1">
                                                                                @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                    <div class="flex items-center flex-wrap mb-2">
                                                                                        @if($qData['question']->before_text)
                                                                                            <span class="text-blue-700 font-medium mr-2">{{ $qData['question']->before_text }}</span>
                                                                                        @endif
                                                                                        
                                                                                        <a href="mailto:{{ $qData['answer'] }}" class="bg-white border border-blue-300 rounded-md px-4 py-2 font-bold text-blue-900 text-lg hover:bg-blue-50 transition-colors">
                                                                                            {{ $qData['answer'] }}
                                                                                        </a>
                                                                                        
                                                                                        @if($qData['question']->after_text)
                                                                                            <span class="text-blue-700 font-medium ml-2">{{ $qData['question']->after_text }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <span class="text-sm text-blue-600 font-medium block mb-1">Email yang diinput:</span>
                                                                                        <a href="mailto:{{ $qData['answer'] }}" class="bg-white border border-blue-300 rounded-md px-4 py-2 font-bold text-blue-900 text-xl hover:bg-blue-50 transition-colors inline-block">
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
                                                                @elseif($qData['question']->type === 'scale')
                                                                    <!-- Enhanced Scale Answer -->
                                                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                                                        <div class="flex items-center space-x-4">
                                                                            <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center">
                                                                                <span class="text-xl font-bold">{{ $qData['answer'] }}</span>
                                                                            </div>
                                                                            <div class="flex-1">
                                                                                <div class="w-full bg-gray-200 rounded-full h-4">
                                                                                    @php
                                                                                        $scaleValue = is_numeric($qData['answer']) ? (float)$qData['answer'] : 0;
                                                                                        $maxScale = 5; // Default max scale, adjust if needed
                                                                                        $percentage = $maxScale > 0 ? ($scaleValue / $maxScale) * 100 : 0;
                                                                                    @endphp
                                                                                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-300" 
                                                                                         style="width: {{ $percentage }}%"></div>
                                                                                </div>
                                                                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                                                    <span>1</span>
                                                                                    <span class="text-blue-600 font-medium">{{ $qData['answer'] }}/{{ $maxScale }}</span>
                                                                                    <span>{{ $maxScale }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($qData['question']->type === 'date')
                                                                    <!-- Date Answer -->
                                                                    <div class="text-green-700">
                                                                        <div class="flex items-center space-x-2">
                                                                            <i class="fas fa-calendar-alt text-green-600"></i>
                                                                            <span class="font-medium">{{ \Carbon\Carbon::parse($qData['answer'])->format('d M Y') }}</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <!-- Text Answer (includes numeric fallback) -->
                                                                    <div class="text-green-700">
                                                                        @if($qData['question']->type === 'text')
                                                                            @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                                                                    <div class="flex items-center flex-wrap">
                                                                                        @if($qData['question']->before_text)
                                                                                            <span class="mr-2 text-gray-600 font-medium">{{ $qData['question']->before_text }}</span>
                                                                                        @endif
                                                                                        <span class="font-medium text-gray-800 bg-white px-2 py-1 rounded border">{{ $qData['answer'] }}</span>
                                                                                        @if($qData['question']->after_text)
                                                                                            <span class="ml-2 text-gray-600 font-medium">{{ $qData['question']->after_text }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                                                                    <p class="whitespace-pre-wrap font-medium">{{ $qData['answer'] }}</p>
                                                                                </div>
                                                                            @endif
                                                                        @else
                                                                            <!-- Fallback for other unhandled types -->
                                                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                                                <div class="flex items-center">
                                                                                    <i class="fas fa-keyboard text-blue-600 mr-2"></i>
                                                                                    <p class="whitespace-pre-wrap font-medium">{{ $qData['answer'] }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        
                                                                        <!-- Handle other answer untuk text questions jika ada -->
                                                                        @if(isset($qData['otherAnswer']) && $qData['otherAnswer'])
                                                                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-plus-circle text-blue-600 mr-2 mt-0.5"></i>
                                                                                    <div class="flex-1">
                                                                                        <h6 class="font-semibold text-blue-800 mb-1">Informasi Tambahan:</h6>
                                                                                        <p class="text-blue-700 whitespace-pre-wrap">{{ $qData['otherAnswer'] }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        <!-- No Answer -->
                                                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                                            <p class="text-yellow-700 flex items-center">
                                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                                Tidak dijawab
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
                            @endif {{-- End if visible questions > 0 --}}
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-comment-slash text-6xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Jawaban</h4>
                            <p class="text-gray-500 max-w-md mx-auto">
                                Responden belum mengisi atau menyimpan jawaban kuesioner. 
                                Kemungkinan responden baru memulai pengisian atau terjadi masalah teknis.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:justify-between items-stretch sm:items-center gap-4 mt-8 no-print">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                        class="inline-flex items-center justify-center gap-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 sm:px-6 py-2 sm:py-3 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left"></i> 
                        <span>Kembali ke Daftar Responden</span>
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-list"></i> 
                        <span>Lihat Kuesioner</span>
                    </a>
                    <a href="{{ route('admin.questionnaire.index') }}" 
                        class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-home"></i> 
                        <span>Dashboard Kuesioner</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
    @media print {
        .no-print { display: none !important; }
        .bg-gradient-to-r { background: #f8fafc !important; }
        .shadow-md { box-shadow: none !important; }
        body { 
            font-size: 12px;
            color: black !important;
            background: white !important;
        }
        .p-6 { padding: 1rem !important; }
        .px-3, .px-4, .px-6 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        .py-4, .py-6 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
        .mb-6, .mb-4 { margin-bottom: 1rem !important; }
        .rounded-xl, .rounded-lg { border-radius: 0.5rem !important; }
        .text-blue-900, .text-blue-800, .text-gray-800, .text-gray-900 { color: black !important; }
        .text-green-800, .text-green-700 { color: #065f46 !important; }
        .text-blue-700 { color: #1d4ed8 !important; }
        .bg-green-100, .bg-blue-100, .bg-indigo-100, .bg-yellow-100 { 
            background: #f3f4f6 !important; 
            border: 1px solid #d1d5db !important; 
        }
        .border { border: 1px solid #d1d5db !important; }
        h2, h3 { page-break-after: avoid; }
        .question-item { page-break-inside: avoid; }
        
        /* Print header */
        @page {
            margin: 1cm;
            @top-center {
                content: "Detail Respons Kuesioner - Halaman " counter(page);
            }
        }
    }
    </style>

    <!-- Scripts -->
    <script>
        // Enhanced Print functionality
        function printResponse() {
            // Add print-specific title
            const originalTitle = document.title;
            document.title = 'Detail Respons Kuesioner - {{ $userAnswer->user->alumni->name ?? $userAnswer->user->company->company_name ?? "User #" . $userAnswer->id_user }}';
            
            // Create print header with response info
            const printHeader = document.createElement('div');
            printHeader.innerHTML = `
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                    <h1 style="margin: 0; font-size: 18px; font-weight: bold;">DETAIL RESPONS KUESIONER</h1>
                    <p style="margin: 5px 0; font-size: 14px;">Periode: {{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }}</p>
                    <p style="margin: 5px 0; font-size: 12px;">Dicetak pada: ${new Date().toLocaleDateString('id-ID', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
            `;
            printHeader.className = 'print-header';
            
            // Insert header at the beginning of printable content
            const printableContent = document.getElementById('printable-content');
            if (printableContent) {
                printableContent.insertBefore(printHeader, printableContent.firstChild);
            }
            
            // Add CSS classes for better print layout
            const questionItems = document.querySelectorAll('.bg-gray-50.border.border-gray-200.rounded-lg');
            questionItems.forEach(item => {
                item.classList.add('question-item');
            });
            
            // Trigger print
            window.print();
            
            // Clean up after print
            setTimeout(() => {
                document.title = originalTitle;
                if (printHeader.parentNode) {
                    printHeader.parentNode.removeChild(printHeader);
                }
                questionItems.forEach(item => {
                    item.classList.remove('question-item');
                });
            }, 1000);
        }
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection