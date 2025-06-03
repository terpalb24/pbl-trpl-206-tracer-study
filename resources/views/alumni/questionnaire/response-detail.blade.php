@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
      < {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    
        
    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
    <!-- Header -->
    <x-alumni.header title="Kuesioner" />
                
        



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
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                                                <i class="fas fa-graduation-cap mr-1"></i>
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
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ $visibleQuestions->count() }} pertanyaan
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
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow duration-200">
                                                <!-- Question Header -->
                                                <div class="flex justify-between items-start mb-4">
                                                    <div class="flex-1">
                                                        <div class="flex items-start">
                                                            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full mr-3 mt-1">{{ $questionNumber }}</span>
                                                            <div class="flex-1">
                                                                <p class="font-semibold text-gray-900 leading-relaxed">{{ $qData['question']->question }}</p>
                                                                
                                                                @if($qData['question']->depends_on)
                                                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-md p-3">
                                                                        <p class="text-xs text-blue-700 flex items-start">
                                                                            <i class="fas fa-link mr-2 mt-0.5"></i> 
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
                                                    @if($qData['hasAnswer'])
                                                        @if($qData['question']->type === 'multiple')
                                                            <!-- Multiple Choice Answers -->
                                                            <div class="bg-white border border-green-200 rounded-md p-4">
                                                                <h6 class="font-medium text-green-800 mb-2 flex items-center">
                                                                    <i class="fas fa-check-square mr-2"></i>
                                                                    Pilihan yang Dipilih:
                                                                </h6>
                                                                <ul class="space-y-3">
                                                                    @foreach($qData['multipleAnswers'] as $optionId)
                                                                        @php
                                                                            $option = $qData['question']->options->where('id_questions_options', $optionId)->first();
                                                                        @endphp
                                                                        <li class="text-green-700">
                                                                            <div class="flex items-center">
                                                                                <i class="fas fa-check text-green-600 mr-2"></i>
                                                                                <span class="font-medium">{{ $option ? $option->option : $optionId }}</span>
                                                                            </div>
                                                                            
                                                                            <!-- ✅ PERBAIKAN: Handle other answer untuk multiple choice -->
                                                                            @if($option && $option->is_other_option && isset($qData['multipleOtherAnswers'][$optionId]))
                                                                                <div class="ml-6 mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                                    <div class="flex items-start">
                                                                                        <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                        <div class="flex-1">
                                                                                            <p class="text-sm font-semibold text-blue-800 mb-1">Jawaban Lainnya:</p>
                                                                                            <div class="text-blue-700">
                                                                                                @if($option->other_before_text)
                                                                                                    <span class="text-blue-600 mr-1">{{ $option->other_before_text }}:</span>
                                                                                                @endif
                                                                                                <strong class="text-blue-800 bg-white px-2 py-1 rounded border border-blue-300">
                                                                                                    {{ $qData['multipleOtherAnswers'][$optionId] }}
                                                                                                </strong>
                                                                                                @if($option->other_after_text)
                                                                                                    <span class="text-blue-600 ml-1">{{ $option->other_after_text }}</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <!-- Single Answer -->
                                                            <div class="bg-white border border-green-200 rounded-md p-4">
                                                                <h6 class="font-medium text-green-800 mb-3 flex items-center">
                                                                    <i class="fas fa-{{ $qData['question']->type == 'text' ? 'keyboard' : ($qData['question']->type == 'option' ? 'dot-circle' : ($qData['question']->type == 'location' ? 'map-marker-alt' : ($qData['question']->type == 'rating' ? 'star' : ($qData['question']->type == 'scale' ? 'chart-line' : 'calendar-alt')))) }} mr-2"></i>
                                                                    Jawaban:
                                                                </h6>
                                                                
                                                                @if($qData['question']->type === 'option' || $qData['question']->type === 'rating')
                                                                    @php
                                                                        $option = $qData['question']->options->where('id_questions_options', $qData['answer'])->first();
                                                                    @endphp
                                                                    
                                                                    <div class="text-green-700">
                                                                        <div class="mb-2">
                                                                            <span class="text-lg font-semibold">{{ $option ? $option->option : $qData['answer'] }}</span>
                                                                        </div>
                                                                        
                                                                        <!-- ✅ PERBAIKAN: Handle other answer untuk single choice -->
                                                                        @if($option && $option->is_other_option && $qData['otherAnswer'])
                                                                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                                                <div class="flex items-start">
                                                                                    <i class="fas fa-edit text-blue-600 mr-2 mt-0.5"></i>
                                                                                    <div class="flex-1">
                                                                                        <h6 class="font-semibold text-blue-800 mb-2">Jawaban Lainnya:</h6>
                                                                                        <div class="text-blue-700">
                                                                                            @if($option->other_before_text)
                                                                                                <span class="text-blue-600 mr-1">{{ $option->other_before_text }}:</span>
                                                                                            @endif
                                                                                            <strong class="text-blue-900 bg-white px-3 py-2 rounded border border-blue-300 inline-block">
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
                                                                    </div>
                                                                
                                                                @elseif($qData['question']->type === 'location')
                                                                    <!-- ✅ PERBAIKAN: Enhanced Location Answer Display -->
                                                                    <div class="text-green-700">
                                                                        @php
                                                                            // Debug location data
                                                                            $originalAnswer = $qData['answer'];
                                                                            $locationDisplay = null;
                                                                            $cityName = null;
                                                                            $provinceName = null;
                                                                            
                                                                            // Try multiple parsing methods
                                                                            if (!empty($originalAnswer)) {
                                                                                // Method 1: Try JSON decode
                                                                                try {
                                                                                    $locationData = json_decode($originalAnswer, true);
                                                                                    
                                                                                    if ($locationData && is_array($locationData)) {
                                                                                        // JSON format detected
                                                                                        if (isset($locationData['display'])) {
                                                                                            $locationDisplay = $locationData['display'];
                                                                                        } elseif (isset($locationData['city_name']) && isset($locationData['province_name'])) {
                                                                                            $cityName = $locationData['city_name'];
                                                                                            $provinceName = $locationData['province_name'];
                                                                                            $locationDisplay = $cityName . ', ' . $provinceName;
                                                                                        } elseif (isset($locationData['city']) && isset($locationData['province'])) {
                                                                                            $cityName = $locationData['city'];
                                                                                            $provinceName = $locationData['province'];
                                                                                            $locationDisplay = $cityName . ', ' . $provinceName;
                                                                                        } else {
                                                                                            // Unknown JSON structure, show as formatted
                                                                                            $locationDisplay = 'Data lokasi: ' . json_encode($locationData);
                                                                                        }
                                                                                    } else {
                                                                                        // Not JSON or invalid JSON, treat as string
                                                                                        $locationDisplay = $originalAnswer;
                                                                                        
                                                                                        // Try to parse as "City, Province" format
                                                                                        if (strpos($originalAnswer, ',') !== false) {
                                                                                            $parts = array_map('trim', explode(',', $originalAnswer));
                                                                                            if (count($parts) >= 2) {
                                                                                                $cityName = $parts[0];
                                                                                                $provinceName = $parts[1];
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                } catch (\Exception $e) {
                                                                                    // JSON parsing failed, treat as string
                                                                                    $locationDisplay = $originalAnswer;
                                                                                    
                                                                                    // Try to parse as "City, Province" format
                                                                                    if (strpos($originalAnswer, ',') !== false) {
                                                                                        $parts = array_map('trim', explode(',', $originalAnswer));
                                                                                        if (count($parts) >= 2) {
                                                                                            $cityName = $parts[0];
                                                                                            $provinceName = $parts[1];
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            
                                                                            // Final fallback
                                                                            if (empty($locationDisplay) || $locationDisplay === 'null') {
                                                                                $locationDisplay = 'Lokasi tidak tersedia';
                                                                            }
                                                                        @endphp
                                                                        
                                                                        <!-- Display location with enhanced styling -->
                                                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                                                            <div class="flex items-start">
                                                                                <i class="fas fa-map-marker-alt text-red-600 mr-3 mt-1 text-lg"></i>
                                                                                <div class="flex-1">
                                                                                    <!-- Main location display -->
                                                                                    <div class="mb-3">
                                                                                        <h6 class="text-sm font-semibold text-green-800 mb-2">Lokasi:</h6>
                                                                                        <div class="bg-white rounded-md p-3 border border-green-300">
                                                                                            <span class="text-lg font-semibold text-green-900">{{ $locationDisplay }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Detailed breakdown if available -->
                                                                                    @if($cityName && $provinceName)
                                                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                                                                            <div class="bg-blue-50 rounded-md p-3 border border-blue-200">
                                                                                                <div class="flex items-center">
                                                                                                    <i class="fas fa-city text-blue-600 mr-2"></i>
                                                                                                    <div>
                                                                                                        <p class="text-xs text-blue-600 font-medium">Kota/Kabupaten</p>
                                                                                                        <p class="font-semibold text-blue-800">{{ $cityName }}</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="bg-purple-50 rounded-md p-3 border border-purple-200">
                                                                                                <div class="flex items-center">
                                                                                                    <i class="fas fa-map text-purple-600 mr-2"></i>
                                                                                                    <div>
                                                                                                        <p class="text-xs text-purple-600 font-medium">Provinsi</p>
                                                                                                        <p class="font-semibold text-purple-800">{{ $provinceName }}</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    
                                                                                    <!-- Debug info (only show if needed) -->
                                                                                    @if(config('app.debug') && auth()->user()->role === 'admin')
                                                                                        <div class="mt-3 bg-gray-100 rounded-md p-2 text-xs">
                                                                                            <strong>Debug Info:</strong><br>
                                                                                            Original: {{ $originalAnswer }}<br>
                                                                                            Processed: {{ $locationDisplay }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                @elseif($qData['question']->type === 'scale')
                                                                    <!-- Scale Answer -->
                                                                    <div class="text-green-700">
                                                                        <div class="flex items-center space-x-4">
                                                                            <span class="text-3xl font-bold text-blue-600">{{ $qData['answer'] }}</span>
                                                                            <div class="flex-1">
                                                                                <div class="w-full bg-gray-200 rounded-full h-3">
                                                                                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full" 
                                                                                         style="width: {{ ($qData['answer'] / 5) * 100 }}%"></div>
                                                                                </div>
                                                                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                                                    <span>1</span>
                                                                                    <span>5</span>
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
                                                                    <!-- Text Answer -->
                                                                    <div class="text-green-700">
                                                                        <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                                                            <p class="whitespace-pre-wrap font-medium">{{ $qData['answer'] }}</p>
                                                                        </div>
                                                                        
                                                                        <!-- Handle other answer untuk text questions jika ada -->
                                                                        @if($qData['otherAnswer'])
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

<!-- script JS  -->
<script src="{{ asset('./js/alumni.js') }}"></script>

@endsection
