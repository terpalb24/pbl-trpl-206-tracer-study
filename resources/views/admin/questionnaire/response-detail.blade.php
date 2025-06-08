@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
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
                    <h1 class="text-2xl font-bold text-blue-800">Detail Respons Kuesioner</h1>
                    <p class="text-sm text-gray-600">Periode: {{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                <button onclick="printResponse()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
                <button onclick="exportResponse()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                        <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                        <div class="text-left">
                            <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                        <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                            <i class="fas fa-key mr-2"></i>Ganti Password
                        </a>
                        <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6" id="printable-content">
            <!-- Breadcrumb -->
            <nav class="mb-6 no-print">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li><a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" class="text-blue-600 hover:underline">Respons</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700">Detail</li>
                </ol>
            </nav>

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
                            @if(isset($userType))
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $userType == 'alumni' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                                    <i class="fas {{ $userType == 'alumni' ? 'fa-graduation-cap' : 'fa-building' }} mr-1"></i>
                                    {{ $userType == 'alumni' ? 'Alumni' : 'Perusahaan' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">ID Respons</p>
                        <p class="text-lg font-bold text-blue-900">#{{ $userAnswer->id_user_answers }}</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                @if(isset($questionsWithAnswers))
                    @php
                        $totalQuestions = 0;
                        $answeredQuestions = 0;
                        $skippedQuestions = 0;
                        
                        // Helper function to check if a question should be shown based on dependencies
                        $shouldShowQuestion = function($question, $allAnswers) {
                            if (!$question->depends_on || !$question->depends_value) {
                                return true; // No dependency, always show
                            }
                            
                            // Find the parent question's answer
                            foreach ($allAnswers as $categoryData) {
                                foreach ($categoryData['questions'] as $qData) {
                                    if ($qData['question']->id_question == $question->depends_on) {
                                        // Check if the answer matches the dependency value
                                        if ($qData['question']->type == 'multiple') {
                                            // For multiple choice, check if any selected option matches
                                            if (isset($qData['multipleAnswers'])) {
                                                foreach ($qData['multipleAnswers'] as $answer) {
                                                    $selectedOption = $qData['question']->options->where('option', $answer)->first();
                                                    if ($selectedOption && $selectedOption->id_questions_options == $question->depends_value) {
                                                        return true;
                                                    }
                                                }
                                            }
                                        } else {
                                            // For single choice, check if selected option matches
                                            if ($qData['answer']) {
                                                $selectedOption = $qData['question']->options->where('option', $qData['answer'])->first();
                                                if ($selectedOption && $selectedOption->id_questions_options == $question->depends_value) {
                                                    return true;
                                                }
                                            }
                                        }
                                        return false; // Dependency not met
                                    }
                                }
                            }
                            return false; // Parent question not found
                        };
                        
                        foreach($questionsWithAnswers as $categoryData) {
                            foreach($categoryData['questions'] as $qData) {
                                if ($shouldShowQuestion($qData['question'], $questionsWithAnswers)) {
                                    $totalQuestions++;
                                    // Check if question is answered (including location and date types)
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
                            <div class="mb-8 {{ $categoryIndex > 0 ? 'border-t border-gray-200 pt-8' : '' }}">
                                <!-- Category Header -->
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg mb-6 border border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-lg font-bold text-blue-900 flex items-center">
                                            <i class="fas fa-folder-open mr-2"></i>
                                            {{ $categoryData['category']->category_name }}
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium px-3 py-1 rounded-full
                                                {{ $categoryData['category']->for_type == 'alumni' ? 'bg-indigo-100 text-indigo-700' : 
                                                   ($categoryData['category']->for_type == 'company' ? 'bg-green-100 text-green-700' : 
                                                    'bg-blue-100 text-blue-700') }}">
                                                <i class="fas {{ $categoryData['category']->for_type == 'alumni' ? 'fa-graduation-cap' : ($categoryData['category']->for_type == 'company' ? 'fa-building' : 'fa-users') }} mr-1"></i>
                                                {{ ucfirst($categoryData['category']->for_type) }}
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ count($categoryData['questions']) }} pertanyaan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Questions -->
                                <div class="space-y-6">
                                    @php
                                        $questionNumber = 1;
                                    @endphp
                                    @foreach($categoryData['questions'] as $index => $qData)
                                        @php
                                            // Check if this question should be shown based on dependencies
                                            $shouldShow = true;
                                            $dependencyInfo = null;
                                            
                                            if ($qData['question']->depends_on && $qData['question']->depends_value) {
                                                $shouldShow = false;
                                                
                                                // Find the parent question and its answer
                                                foreach ($questionsWithAnswers as $catData) {
                                                    foreach ($catData['questions'] as $parentQData) {
                                                        if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                            $dependencyInfo = [
                                                                'parent_question' => $parentQData['question']->question,
                                                                'required_option' => $qData['question']->dependsOnOption->option ?? 'Unknown option',
                                                                'user_answered' => false
                                                            ];
                                                            
                                                            if ($parentQData['question']->type == 'multiple') {
                                                                if (isset($parentQData['multipleAnswers'])) {
                                                                    foreach ($parentQData['multipleAnswers'] as $answer) {
                                                                        $selectedOption = $parentQData['question']->options->where('option', $answer)->first();
                                                                        if ($selectedOption && $selectedOption->id_questions_options == $qData['question']->depends_value) {
                                                                            $shouldShow = true;
                                                                            $dependencyInfo['user_answered'] = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                if ($parentQData['answer']) {
                                                                    $selectedOption = $parentQData['question']->options->where('option', $parentQData['answer'])->first();
                                                                    if ($selectedOption && $selectedOption->id_questions_options == $qData['question']->depends_value) {
                                                                        $shouldShow = true;
                                                                        $dependencyInfo['user_answered'] = true;
                                                                    }
                                                                }
                                                            }
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @if($shouldShow)
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                                                <!-- Question Header -->
                                                <div class="flex justify-between items-start mb-4">
                                                    <div class="flex-1">
                                                        <div class="flex items-start">
                                                            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full mr-3 mt-1">{{ $questionNumber }}</span>
                                                            <div class="flex-1">
                                                                <p class="font-semibold text-gray-900 leading-relaxed">{{ $qData['question']->question }}</p>
                                                                @if($qData['question']->depends_on && $dependencyInfo)
                                                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-md p-2">
                                                                        <p class="text-xs text-blue-700 flex items-center">
                                                                            <i class="fas fa-link mr-1"></i> 
                                                                            <span class="font-medium">Pertanyaan bersyarat:</span>
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full ml-3">
                                                        <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'numeric' ? 'calculator' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'multiple' ? 'check-square' : ($qData['question']->type == 'location' ? 'map-marker-alt' : 'calendar-alt')))) }} mr-1"></i>
                                                        {{ $qData['question']->type == 'numeric' ? 'Numerik' : ucfirst($qData['question']->type) }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Answer Section -->
                                                <div class="border-t border-gray-300 pt-4">
                                                    @if($qData['question']->type == 'multiple' && isset($qData['multipleAnswers']))
                                                        <!-- Multiple Choice Answers -->
                                                        @if(count($qData['multipleAnswers']) > 0)
                                                            <div class="bg-white border border-green-200 rounded-md p-4">
                                                                <p class="text-sm text-green-700 font-semibold mb-3 flex items-center">
                                                                    <i class="fas fa-check-square mr-2"></i>
                                                                    Jawaban terpilih ({{ count($qData['multipleAnswers']) }} pilihan):
                                                                </p>
                                                                <div class="space-y-3">
                                                                    @foreach($qData['multipleAnswers'] as $answerIndex => $answer)
                                                                        @php
                                                                            // ✅ PERBAIKAN: Find the option that matches this answer
                                                                            $relatedOption = null;
                                                                            $otherAnswerForThisOption = null;
                                                                            
                                                                            // Find the option by answer text
                                                                            foreach($qData['question']->options as $option) {
                                                                                if ($option->option === $answer) {
                                                                                    $relatedOption = $option;
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                            // ✅ PERBAIKAN: Get other answer for this specific option
                                                                            if ($relatedOption && isset($qData['multipleOtherAnswers'][$relatedOption->id_questions_options])) {
                                                                                $otherAnswerForThisOption = $qData['multipleOtherAnswers'][$relatedOption->id_questions_options];
                                                                            }
                                                                        @endphp
                                                                        
                                                                        <div class="flex items-start bg-green-50 p-4 rounded-lg border border-green-100 hover:bg-green-100 transition-colors duration-200">
                                                                            <div class="flex-shrink-0 mr-3">
                                                                                <i class="fas fa-check-circle text-green-600 text-lg"></i>
                                                                            </div>
                                                                            <div class="flex-1">
                                                                                <div class="font-medium text-gray-900 mb-1">{{ $answer }}</div>
                                                                                
                                                                                @if($otherAnswerForThisOption)
                                                                                    <!-- ✅ PERBAIKAN: Display other answer dengan formatting yang benar -->
                                                                                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                                        <div class="flex items-start">
                                                                                            <i class="fas fa-edit text-blue-600 mr-2 mt-0.5 text-sm"></i>
                                                                                            <div class="flex-1">
                                                                                                <p class="text-blue-700 font-medium text-sm mb-1">Detail tambahan:</p>
                                                                                                <div class="bg-white border border-blue-300 rounded-md p-2">
                                                                                                    @if($relatedOption && $relatedOption->other_before_text)
                                                                                                        <span class="text-gray-700 text-sm">{{ $relatedOption->other_before_text }}</span>
                                                                                                    @endif
                                                                                                    <span class="font-semibold text-blue-900 bg-blue-100 px-2 py-1 rounded text-sm">{{ $otherAnswerForThisOption }}</span>
                                                                                                    @if($relatedOption && $relatedOption->other_after_text)
                                                                                                        <span class="text-gray-700 text-sm">{{ $relatedOption->other_after_text }}</span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                
                                                                                @if($relatedOption && $relatedOption->is_other_option)
                                                                                    <div class="mt-2">
                                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                                                            <i class="fas fa-tag mr-1"></i>
                                                                                            Pilihan Lainnya
                                                                                        </span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                
                                                                <!-- Summary untuk multiple choice -->
                                                                <div class="mt-4 pt-3 border-t border-green-200">
                                                                    <div class="flex items-center justify-between text-sm">
                                                                        <span class="text-green-700">
                                                                            <i class="fas fa-list-ul mr-1"></i>
                                                                            Total pilihan: <strong>{{ count($qData['multipleAnswers']) }}</strong>
                                                                        </span>
                                                                        @if(count($qData['multipleOtherAnswers']) > 0)
                                                                            <span class="text-blue-700">
                                                                                <i class="fas fa-comment-dots mr-1"></i>
                                                                                Detail tambahan: <strong>{{ count($qData['multipleOtherAnswers']) }}</strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                                                    <p class="text-yellow-700 font-medium">Tidak ada pilihan yang dipilih</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                    @elseif($qData['question']->type == 'option' && !empty($qData['answer']))
                                                        <!-- Single Option dengan Other Answer -->
                                                        <div class="bg-white border border-green-200 rounded-md p-4">
                                                            <div class="flex items-start bg-green-50 p-4 rounded-lg border border-green-100">
                                                                <i class="fas fa-dot-circle text-green-600 mr-3 mt-1"></i>
                                                                <div class="flex-1">
                                                                    <p class="font-medium text-gray-900 mb-1">{{ $qData['answer'] }}</p>
                                                                        
                                                                    @if(!empty($qData['otherAnswer']))
                                                                        @php
                                                                            // Find the selected option to get before/after text
                                                                            $selectedOption = $qData['question']->options->where('option', $qData['answer'])->first();
                                                                        @endphp
                                                                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                            <div class="flex items-start">
                                                                                <i class="fas fa-edit text-blue-600 mr-2 mt-0.5 text-sm"></i>
                                                                                <div class="flex-1">
                                                                                    <p class="text-blue-700 font-medium text-sm mb-1">Detail tambahan:</p>
                                                                                    <div class="bg-white border border-blue-300 rounded-md p-2">
                                                                                        <div class="flex items-center flex-wrap">
                                                                                            @if($selectedOption && $selectedOption->other_before_text)
                                                                                                <span class="mr-2 text-blue-600 font-medium">{{ $selectedOption->other_before_text }}</span>
                                                                                            @endif
                                                                                            <span class="font-semibold text-blue-900 bg-blue-100 px-2 py-1 rounded">{{ $qData['otherAnswer'] }}</span>
                                                                                            @if($selectedOption && $selectedOption->other_after_text)
                                                                                                <span class="ml-2 text-blue-600 font-medium">{{ $selectedOption->other_after_text }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($qData['question']->type == 'location' && !empty($qData['answer']))
                                                        <!-- Location Answer -->
                                                        <div class="bg-white border border-green-200 rounded-md p-4">
                                                            <div class="flex items-start">
                                                                <i class="fas fa-map-marker-alt text-red-600 mr-3 mt-1"></i>
                                                                <div class="flex-1">
                                                                    @php
                                                                        $locationData = null;
                                                                        $displayAddress = $qData['answer'];
                                                                        $provinceName = null;
                                                                        $cityName = null;
                                                                        $provinceId = null;
                                                                        $cityId = null;
                                                                        $displayText = null;
                                                                        
                                                                        // Try to parse as JSON first
                                                                        try {
                                                                            $parsedData = json_decode($qData['answer'], true);
                                                                            if (is_array($parsedData)) {
                                                                                $locationData = $parsedData;
                                                                                
                                                                                // Use the actual database structure
                                                                                $provinceName = $parsedData['province_name'] ?? null;
                                                                                $cityName = $parsedData['city_name'] ?? null;
                                                                                $provinceId = $parsedData['province_id'] ?? null;
                                                                                $cityId = $parsedData['city_id'] ?? null;
                                                                                $displayText = $parsedData['display'] ?? null;
                                                                                
                                                                                // Use display field if available, otherwise construct from parts
                                                                                if ($displayText) {
                                                                                    $displayAddress = $displayText;
                                                                                } elseif ($cityName && $provinceName) {
                                                                                    $displayAddress = $cityName . ', ' . $provinceName;
                                                                                } elseif ($cityName) {
                                                                                    $displayAddress = $cityName;
                                                                                } elseif ($provinceName) {
                                                                                    $displayAddress = $provinceName;
                                                                                }
                                                                                
                                                                                // Also check for alternative key formats (backwards compatibility)
                                                                                if (!$provinceName) {
                                                                                    $provinceName = $parsedData['province'] ?? null;
                                                                                }
                                                                                if (!$cityName) {
                                                                                    $cityName = $parsedData['city'] ?? null;
                                                                                }
                                                                                if (!$displayAddress || $displayAddress === $qData['answer']) {
                                                                                    $displayAddress = $parsedData['address'] ?? $parsedData['location'] ?? $displayAddress;
                                                                                }
                                                                            }
                                                                        } catch (Exception $e) {
                                                                            // If JSON parsing fails, treat as plain text
                                                                        }
                                                                        
                                                                        // Alternative: Check if answer contains pipe-separated data
                                                                        if (!$locationData && strpos($qData['answer'], '|') !== false) {
                                                                            $parts = explode('|', $qData['answer']);
                                                                            if (count($parts) >= 2) {
                                                                                $displayAddress = trim($parts[0]);
                                                                                $provinceName = trim($parts[1]) ?? null;
                                                                                $cityName = isset($parts[2]) ? trim($parts[2]) : null;
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    
                                                                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                                                        <div class="space-y-3">
                                                                            <!-- Main Location Display -->
                                                                            <div class="flex items-start">
                                                                                <i class="fas fa-map-pin text-green-600 mr-2 mt-1 text-sm"></i>
                                                                                <div class="flex-1">
                                                                                    <p class="font-semibold text-green-800 text-lg leading-relaxed">{{ $displayAddress }}</p>
                                                                                    
                                                                                    <!-- Detailed location info if available -->
                                                                                    @if($cityName || $provinceName)
                                                                                        <div class="mt-3 space-y-2">
                                                                                            @if($cityName)
                                                                                                <div class="flex items-center text-sm text-green-700">
                                                                                                    <i class="fas fa-building text-green-500 mr-2 text-xs"></i>
                                                                                                    <span class="font-medium mr-2">Kota/Kabupaten:</span>
                                                                                                    <span>{{ $cityName }}</span>
                                                                                                    @if($cityId)
                                                                                                        <span class="text-xs text-green-500 ml-2">(ID: {{ $cityId }})</span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endif
                                                                                            
                                                                                            @if($provinceName)
                                                                                                <div class="flex items-center text-sm text-green-700">
                                                                                                    <i class="fas fa-map text-green-500 mr-2 text-xs"></i>
                                                                                                    <span class="font-medium mr-2">Provinsi:</span>
                                                                                                    <span>{{ $provinceName }}</span>
                                                                                                    @if($provinceId)
                                                                                                        <span class="text-xs text-green-500 ml-2">(ID: {{ $provinceId }})</span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endif
                                                                                    
                                                                                    <!-- Coordinates if available -->
                                                                                    @if(isset($locationData['latitude']) && isset($locationData['longitude']))
                                                                                        <div class="flex items-center mt-2 text-xs text-green-600">
                                                                                            <i class="fas fa-crosshairs mr-2"></i>
                                                                                            <span>Koordinat: {{ $locationData['latitude'] }}, {{ $locationData['longitude'] }}</span>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <!-- Show structured data if parsed successfully -->
                                                                            @if($locationData && (config('app.debug', false) || request()->has('show_debug')))
                                                                                <details class="mt-3">
                                                                                    <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-700">
                                                                                        <i class="fas fa-code mr-1"></i>
                                                                                        Lihat data lengkap
                                                                                    </summary>
                                                                                    <div class="mt-2 p-3 bg-gray-100 rounded text-xs text-gray-700">
                                                                                        <div class="space-y-1">
                                                                                            @foreach($locationData as $key => $value)
                                                                                                <div class="flex">
                                                                                                    <span class="font-mono font-semibold text-blue-600 w-24">{{ $key }}:</span>
                                                                                                    <span class="font-mono">{{ $value }}</span>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </details>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($qData['question']->type == 'rating' && !empty($qData['answer']))
                                                        <!-- Rating Answer Display -->
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
                                                                            
                                                                            if (strpos($ratingLevel, 'kurang') !== false) {
                                                                                $ratingColor = 'red';
                                                                                $ratingIcon = 'fa-star';
                                                                            } elseif (strpos($ratingLevel, 'cukup') !== false) {
                                                                                $ratingColor = 'yellow';
                                                                                $ratingIcon = 'fa-star-half-alt';
                                                                            } elseif (strpos($ratingLevel, 'baik sekali') !== false || strpos($ratingLevel, 'sangat baik') !== false) {
                                                                                $ratingColor = 'green';
                                                                                $ratingIcon = 'fa-star';
                                                                            } elseif (strpos($ratingLevel, 'baik') !== false) {
                                                                                $ratingColor = 'blue';
                                                                                $ratingIcon = 'fa-star';
                                                                            }
                                                                        @endphp
                                                                        
                                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                                            bg-{{ $ratingColor }}-100 text-{{ $ratingColor }}-800">
                                                                            <i class="fas {{ $ratingIcon }} mr-2"></i>
                                                                            Tingkat: {{ $qData['answer'] }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @elseif($qData['question']->type == 'scale' && !empty($qData['answer']))
                                                        <!-- Scale Answer Display -->
                                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-chart-line text-blue-600 mr-3 text-lg"></i>
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
                                                                            
                                                                            <span class="text-3xl font-bold text-{{ $scaleColor }}-600 mr-3">{{ $qData['answer'] }}</span>
                                                                            <span class="text-sm text-gray-600">dari 5</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Visual scale representation -->
                                                                <div class="flex items-center space-x-1">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <div class="w-3 h-3 rounded-full {{ $i <= $scaleValue ? 'bg-'.$scaleColor.'-500' : 'bg-gray-300' }}"></div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Scale labels if available -->
                                                            @if($qData['question']->before_text || $qData['question']->after_text)
                                                                <div class="mt-3 pt-3 border-t border-blue-100">
                                                                    <div class="flex justify-between text-xs text-blue-600">
                                                                        <span class="font-medium">{{ $qData['question']->before_text ?: 'Rendah' }}</span>
                                                                        <span class="font-medium">{{ $qData['question']->after_text ?: 'Tinggi' }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @elseif($qData['question']->type == 'numeric' && !empty($qData['answer']))
                                                        <!-- Numeric Answer Display -->
                                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-calculator text-green-600 mr-3 text-xl"></i>
                                                                <div class="flex-1">
                                                                    @if($qData['question']->before_text || $qData['question']->after_text)
                                                                        <div class="flex items-center flex-wrap mb-2">
                                                                            @if($qData['question']->before_text)
                                                                                <span class="text-green-700 font-medium mr-2">{{ $qData['question']->before_text }}</span>
                                                                            @endif
                                                                            
                                                                            <span class="bg-white border border-green-300 rounded-md px-4 py-2 font-bold text-green-900 text-xl font-mono">
                                                                                {{ number_format(floatval(str_replace(',', '', $qData['answer']))) }}
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
                                                                                {{ number_format(floatval(str_replace(',', '', $qData['answer']))) }}
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
                                                    @else
                                                        <!-- Other Answer Types (text, date, option) -->
                                                        <div class="bg-white border border-green-200 rounded-md p-4">
                                                            <div class="flex items-start">
                                                                <i class="fas fa-{{ $qData['question']->type == 'date' ? 'calendar-alt' : ($qData['question']->type == 'option' ? 'check-circle' : 'quote-left') }} text-green-600 mr-3 mt-1"></i>
                                                                <div class="flex-1">
                                                                    @if($qData['question']->type == 'text')
                                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                                            @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                <div class="flex items-center flex-wrap">
                                                                                    @if($qData['question']->before_text)
                                                                                        <span class="mr-2 text-gray-600">{{ $qData['question']->before_text }}</span>
                                                                                    @endif
                                                                                    <span class="font-medium text-gray-800 bg-white px-2 py-1 rounded border">{{ $qData['answer'] }}</span>
                                                                                    @if($qData['question']->after_text)
                                                                                        <span class="ml-2 text-gray-600">{{ $qData['question']->after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            @else
                                                                                <span class="text-gray-800 font-medium">{{ $qData['answer'] }}</span>
                                                                            @endif
                                                                        </div>
                                                                    @elseif($qData['question']->type == 'date')
                                                                        @php
                                                                            try {
                                                                                $dateFormatted = \Carbon\Carbon::parse($qData['answer'])->format('d M Y');
                                                                            } catch (Exception $e) {
                                                                                $dateFormatted = $qData['answer'];
                                                                            }
                                                                        @endphp
                                                                        <div class="bg-blue-50 rounded-lg p-3">
                                                                            <div class="flex items-center text-blue-800">
                                                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                                                <span class="font-medium">{{ $dateFormatted }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @elseif($qData['question']->type == 'numeric')
                                                                        <!-- Fallback for numeric type -->
                                                                        @if($qData['question']->before_text || $qData['question']->after_text)
                                                                            <div class="bg-green-50 rounded-lg p-3">
                                                                                <div class="flex items-center flex-wrap text-green-800">
                                                                                    <i class="fas fa-calculator mr-2"></i>
                                                                                    @if($qData['question']->before_text)
                                                                                        <span class="mr-2 font-medium">{{ $qData['question']->before_text }}</span>
                                                                                    @endif
                                                                                    <span class="font-bold text-lg font-mono bg-white px-2 py-1 rounded border border-green-300">
                                                                                        {{ number_format(floatval(str_replace(',', '', $qData['answer']))) }}
                                                                                    </span>
                                                                                    @if($qData['question']->after_text)
                                                                                        <span class="ml-2 font-medium">{{ $qData['question']->after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <div class="bg-green-50 rounded-lg p-3">
                                                                                <div class="flex items-center text-green-800">
                                                                                    <i class="fas fa-calculator mr-2"></i>
                                                                                    <span class="font-bold text-lg font-mono">{{ number_format(floatval(str_replace(',', '', $qData['answer']))) }}</span>
                                                                                    <span class="text-sm text-green-600 ml-2">(numerik)</span>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <!-- Option type or other -->
                                                                        <div class="bg-green-50 rounded-lg p-3">
                                                                            <div class="flex items-start text-green-800">
                                                                                <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                                                                                <span class="font-medium">{{ $qData['answer'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if(isset($qData['otherAnswer']) && $qData['otherAnswer'])
                                                                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                                                            <div class="flex items-center text-blue-700">
                                                                                <i class="fas fa-edit mr-2 text-sm"></i>
                                                                                <div class="text-sm">
                                                                                    <span class="font-medium text-blue-600">Input pengguna:</span>
                                                                                    @if(isset($qData['otherOption']) && $qData['otherOption']->other_before_text)
                                                                                        <span class="text-blue-600">{{ $qData['otherOption']->other_before_text }}</span>
                                                                                    @endif
                                                                                    <strong class="text-blue-800 bg-white px-2 py-1 rounded border mx-1">{{ $qData['otherAnswer'] }}</strong>
                                                                                    @if(isset($qData['otherOption']) && $qData['otherOption']->other_after_text)
                                                                                        <span class="text-blue-600">{{ $qData['otherOption']->other_after_text }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php $questionNumber++; @endphp
                                        @else
                                            <!-- Skipped Question due to dependency -->
                                            <div class="bg-gray-100 border border-gray-300 rounded-lg p-5 opacity-75">
                                                <div class="flex items-start">
                                                    <span class="bg-gray-400 text-white text-xs font-bold px-2 py-1 rounded-full mr-3 mt-1">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </span>
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-600 leading-relaxed">{{ $qData['question']->question }}</p>
                                                        @if($dependencyInfo)
                                                            <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-md p-2">
                                                                <p class="text-xs text-yellow-700 flex items-center">
                                                                    <i class="fas fa-ban mr-1"></i> 
                                                                    <span class="font-medium">Pertanyaan dilewati</span>
                                                                </p>
                                                                <p class="text-xs text-yellow-600 mt-1">
                                                                    Tidak muncul karena responden tidak memilih "{{ $dependencyInfo['required_option'] }}" pada pertanyaan: "{{ $dependencyInfo['parent_question'] }}"
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs bg-gray-300 text-gray-600 px-2 py-1 rounded-full ml-3">
                                                        Dilewati
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
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
            <div class="flex justify-between items-center mt-8 no-print">
                <div class="flex space-x-3">
                    <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Responden
                    </a>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i> Lihat Kuesioner
                    </a>
                    <a href="{{ route('admin.questionnaire.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i> Dashboard Kuesioner
                    </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
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

// Print functionality
function printResponse() {
    window.print();
}

// Export functionality
function exportResponse() {
    // This would need to be implemented in the backend
    const responseId = {{ $userAnswer->id_user_answers }};
    const periodeId = {{ $periode->id_periode }};
    
    // You can implement an export route that generates PDF or Excel
    window.location.href = `/admin/questionnaire/${periodeId}/responses/${responseId}/export`;
}
</script>
@endsection
