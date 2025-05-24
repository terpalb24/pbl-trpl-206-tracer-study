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
                    <h1 class="text-2xl font-bold text-blue-800">Detail Respons Kuesioner</h1>
                    <p class="text-sm text-gray-600">Periode: {{ \Carbon\Carbon::parse($userAnswer->periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($userAnswer->periode->end_date)->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                @if($userAnswer->status == 'completed')
                    <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                @endif
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->username }}&background=random&size=128" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                        <div class="text-left">
                            <p class="font-semibold leading-none">{{ session('alumni')->name ?? auth()->user()->username }}</p>
                            <p class="text-sm text-gray-300 leading-none mt-1">Alumni</p>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                        <a href="{{ route('alumni.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                            <i class="fas fa-user-edit mr-2"></i> Edit Profil
                        </a>
                        <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                            <i class="fas fa-key mr-2"></i> Ubah Password
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-100 text-red-600">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
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
                    <li><a href="{{ route('dashboard.alumni') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li><a href="{{ route('alumni.questionnaire.results') }}" class="text-blue-600 hover:underline">Hasil Kuesioner</a></li>
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
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                Alumni
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">ID Respons</p>
                        <p class="text-lg font-bold text-blue-900">#{{ $userAnswer->id_user_answer }}</p>
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
                                            if (!empty($parentQData['answers'])) {
                                                foreach($parentQData['answers'] as $answer) {
                                                    if ($answer['type'] == 'option' && 
                                                        isset($answer['option_id']) && 
                                                        $answer['option_id'] == $qData['question']->depends_value) {
                                                        $parentAnswered = true;
                                                        break 2;
                                                    }
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }
                                $shouldShow = $parentAnswered;
                            }
                            
                            if ($shouldShow) {
                                $totalQuestions++;
                                if (!empty($qData['answers']) || (isset($qData['multipleAnswers']) && count($qData['multipleAnswers']) > 0)) {
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
                        <p class="font-semibold text-gray-900">{{ session('alumni')->name ?? auth()->user()->username }}</p>
                        @if(session('alumni')->nim)
                            <p class="text-sm text-gray-600">NIM: {{ session('alumni')->nim }}</p>
                        @endif
                    </div>

                    @if(session('alumni')->program_studi)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-book text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Program Studi</p>
                            </div>
                            <p class="font-semibold text-gray-900">{{ session('alumni')->program_studi }}</p>
                        </div>
                    @endif

                    @if(session('alumni')->tahun_lulus)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                                <p class="text-sm text-gray-600 font-medium">Tahun Lulus</p>
                            </div>
                            <p class="font-semibold text-gray-900">{{ session('alumni')->tahun_lulus }}</p>
                        </div>
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
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($userAnswer->periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($userAnswer->periode->end_date)->format('d M Y') }}</p>
                        <span class="px-2 py-1 rounded-full text-xs {{ $userAnswer->periode->status == 'active' ? 'bg-green-100 text-green-800' : ($userAnswer->periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($userAnswer->periode->status) }}
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
                    @if(count($questionsWithAnswers) > 0)
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
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                                                <i class="fas fa-graduation-cap mr-1"></i>
                                                Alumni
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ count($categoryData['questions']) }} pertanyaan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Questions -->
                                <div class="space-y-6">
                                    @php $questionNumber = 1; @endphp
                                    @foreach($categoryData['questions'] as $qData)
                                        @php
                                            $shouldShow = true;
                                            $dependencyInfo = null;
                                            
                                            if ($qData['question']->depends_on) {
                                                $parentAnswered = false;
                                                foreach($questionsWithAnswers as $catData) {
                                                    foreach($catData['questions'] as $parentQData) {
                                                        if ($parentQData['question']->id_question == $qData['question']->depends_on) {
                                                            if (!empty($parentQData['answers'])) {
                                                                foreach($parentQData['answers'] as $answer) {
                                                                    if ($answer['type'] == 'option' && 
                                                                        isset($answer['option_id']) && 
                                                                        $answer['option_id'] == $qData['question']->depends_value) {
                                                                        $parentAnswered = true;
                                                                        break 2;
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                        }
                                                    }
                                                }
                                                $shouldShow = $parentAnswered;
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
                                                                @if($qData['question']->depends_on)
                                                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-md p-2">
                                                                        <p class="text-xs text-blue-700 flex items-center">
                                                                            <i class="fas fa-link mr-1"></i> 
                                                                            <span class="font-medium">Pertanyaan bersyarat</span>
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full ml-3">
                                                        <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'multiple' ? 'check-square' : ($qData['question']->type == 'location' ? 'map-marker-alt' : ($qData['question']->type == 'rating' ? 'star' : ($qData['question']->type == 'scale' ? 'chart-line' : 'calendar-alt'))))) }} mr-1"></i>
                                                        {{ ucfirst($qData['question']->type) }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Answer Section -->
                                                <div class="border-t border-gray-300 pt-4">
                                                    @if(!empty($qData['answers']))
                                                        @foreach($qData['answers'] as $answer)
                                                            @if($answer['type'] == 'location')
                                                                <!-- Location Answer -->
                                                                <div class="bg-white border border-green-200 rounded-md p-4">
                                                                    <div class="flex items-start">
                                                                        <i class="fas fa-map-marker-alt text-red-600 mr-3 mt-1"></i>
                                                                        <div class="flex-1">
                                                                            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-map-pin text-green-600 mr-2 mt-1 text-sm"></i>
                                                                                    <div class="flex-1">
                                                                                        <p class="font-semibold text-green-800 text-lg leading-relaxed">{{ $answer['value'] }}</p>
                                                                                        @if(isset($answer['province']) && isset($answer['city']))
                                                                                            <div class="mt-3 space-y-2">
                                                                                                @if($answer['city'])
                                                                                                    <div class="flex items-center text-sm text-green-700">
                                                                                                        <i class="fas fa-building text-green-500 mr-2 text-xs"></i>
                                                                                                        <span class="font-medium mr-2">Kota/Kabupaten:</span>
                                                                                                        <span>{{ $answer['city'] }}</span>
                                                                                                    </div>
                                                                                                @endif
                                                                                                @if($answer['province'])
                                                                                                    <div class="flex items-center text-sm text-green-700">
                                                                                                        <i class="fas fa-map text-green-500 mr-2 text-xs"></i>
                                                                                                        <span class="font-medium mr-2">Provinsi:</span>
                                                                                                        <span>{{ $answer['province'] }}</span>
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            
                                                            @elseif($answer['type'] == 'rating')
                                                                <!-- Rating Answer Display -->
                                                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                                                                    <div class="flex items-center">
                                                                        <i class="fas fa-star text-purple-600 mr-3 text-lg"></i>
                                                                        <div class="flex-1">
                                                                            <span class="font-semibold text-purple-800 text-lg">{{ $answer['value'] }}</span>
                                                                            <div class="mt-2">
                                                                                @php
                                                                                    $ratingLevel = strtolower($answer['value']);
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
                                                                                    Tingkat: {{ $answer['value'] }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @elseif($answer['type'] == 'scale')
                                                                <!-- Scale Answer Display -->
                                                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                                                    <div class="flex items-center justify-between">
                                                                        <div class="flex items-center">
                                                                            <i class="fas fa-chart-line text-blue-600 mr-3 text-lg"></i>
                                                                            <div>
                                                                                <span class="text-sm text-blue-600 font-medium">Skor yang dipilih:</span>
                                                                                <div class="flex items-center mt-1">
                                                                                    @php
                                                                                        $scaleValue = (int) $answer['value'];
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
                                                                                    
                                                                                    <span class="text-3xl font-bold text-{{ $scaleColor }}-600 mr-3">{{ $answer['value'] }}</span>
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

                                                            @elseif($answer['type'] == 'option')
                                                                <!-- Option Answer -->
                                                                <div class="bg-white border border-green-200 rounded-md p-4">
                                                                    <div class="flex items-start">
                                                                        <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                                                                        <div class="flex-1">
                                                                            <div class="bg-green-50 rounded-lg p-3">
                                                                                <div class="flex items-start text-green-800">
                                                                                    <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                                                                                    <span class="font-medium">{{ $answer['base_option_text'] ?? $answer['value'] }}</span>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            @if(isset($answer['is_other']) && $answer['is_other'] && !empty($answer['other_text']))
                                                                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                                                                    <div class="flex items-center text-blue-700">
                                                                                        <i class="fas fa-edit mr-2 text-sm"></i>
                                                                                        <div class="text-sm">
                                                                                            <span class="font-medium text-blue-600">Input pengguna:</span>
                                                                                            @if(!empty($answer['other_before_text']))
                                                                                                <span class="text-blue-600">{{ $answer['other_before_text'] }}</span>
                                                                                            @endif
                                                                                            <strong class="text-blue-800 bg-white px-2 py-1 rounded border mx-1">{{ $answer['other_text'] }}</strong>
                                                                                            @if(!empty($answer['other_after_text']))
                                                                                                <span class="text-blue-600">{{ $answer['other_after_text'] }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            @else
                                                                <!-- Text/Date Answer -->
                                                                <div class="bg-white border border-green-200 rounded-md p-4">
                                                                    <div class="flex items-start">
                                                                        <i class="fas fa-{{ $answer['type'] == 'date' ? 'calendar-alt' : 'quote-left' }} text-green-600 mr-3 mt-1"></i>
                                                                        <div class="flex-1">
                                                                            @if($answer['type'] == 'text')
                                                                                <div class="bg-gray-50 rounded-lg p-3">
                                                                                    @if($qData['question']->before_text || $qData['question']->after_text)
                                                                                        <div class="flex items-center flex-wrap">
                                                                                            @if($qData['question']->before_text)
                                                                                                <span class="mr-2 text-gray-600">{{ $qData['question']->before_text }}</span>
                                                                                            @endif
                                                                                            <span class="font-medium text-gray-800 bg-white px-2 py-1 rounded border">{{ $answer['value'] }}</span>
                                                                                            @if($qData['question']->after_text)
                                                                                                <span class="ml-2 text-gray-600">{{ $qData['question']->after_text }}</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="text-gray-800 font-medium">{{ $answer['value'] }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            @else
                                                                                <div class="bg-blue-50 rounded-lg p-3">
                                                                                    <div class="flex items-center text-blue-800">
                                                                                        <i class="fas fa-calendar-alt mr-2"></i>
                                                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($answer['value'])->format('d M Y') }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @elseif($qData['question']->type == 'multiple' && isset($qData['multipleAnswers']) && count($qData['multipleAnswers']) > 0)
                                                        <!-- Multiple Choice Answers -->
                                                        <div class="bg-white border border-green-200 rounded-md p-4">
                                                            <p class="text-sm text-green-700 font-semibold mb-3 flex items-center">
                                                                <i class="fas fa-check-square mr-2"></i>
                                                                Jawaban terpilih ({{ count($qData['multipleAnswers']) }} pilihan):
                                                            </p>
                                                            <div class="space-y-2">
                                                                @foreach($qData['multipleAnswers'] as $answerIndex => $answer)
                                                                    @php
                                                                        $basePart = $answer;
                                                                        $otherPart = null;
                                                                        
                                                                        if (strpos($answer, ': ') !== false) {
                                                                            $parts = explode(': ', $answer, 2);
                                                                            $basePart = $parts[0];
                                                                            $otherPart = $parts[1] ?? null;
                                                                        }
                                                                    @endphp
                                                                    
                                                                    <div class="flex items-start bg-green-50 p-3 rounded-md border border-green-100">
                                                                        <i class="fas fa-check text-green-600 mr-2 mt-1"></i>
                                                                        <div class="flex-1">
                                                                            <span class="text-gray-900 font-medium">{{ $basePart }}</span>
                                                                            @if($otherPart)
                                                                                <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-sm">
                                                                                    <span class="text-blue-700 font-medium">Detail tambahan:</span>
                                                                                    <span class="italic font-medium">{{ $otherPart }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
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
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-comment-slash text-6xl"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Jawaban</h4>
                            <p class="text-gray-500 max-w-md mx-auto">
                                Belum ada jawaban yang tersimpan untuk kuesioner ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-8 no-print">
                <div class="flex space-x-3">
                    <a href="{{ route('alumni.questionnaire.results') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>
                </div>
                <div class="flex space-x-3">
                    @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                        <a href="{{ route('alumni.questionnaire.fill', [$userAnswer->id_periode]) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-medium transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i> Lanjutkan Mengisi
                        </a>
                    @endif
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
</script>
@endsection
