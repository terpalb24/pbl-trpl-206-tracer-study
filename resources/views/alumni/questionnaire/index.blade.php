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
        <div class="p-3 sm:p-4 lg:p-6">
            @if(session('success'))
                <div class="mb-4 p-3 sm:p-4 bg-green-100 text-green-700 rounded-md text-sm sm:text-base">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 sm:p-4 bg-red-100 text-red-700 rounded-md text-sm sm:text-base">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Active Questionnaires Section -->
            @php
                // Filter active periods that are not completed yet
                $activePeriodes = $periodes->filter(function($periode) {
                    $userAnswer = \App\Models\Tb_User_Answers::where('id_user', auth()->id())
                        ->where('id_periode', $periode->id_periode)
                        ->first();
                    // Only show if no answer exists OR if answer exists but is still draft
                    return !$userAnswer || $userAnswer->status !== 'completed';
                });
            @endphp

            @if($activePeriodes->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-3 sm:mb-4">KUESIONER</h2>
                    
                    <!-- Mobile Cards View -->
                    <div class="block lg:hidden space-y-4">
                        @foreach($activePeriodes as $index => $periode)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 text-sm sm:text-base">
                                            Kuesioner Alumni {{ date('Y', strtotime($periode->start_date)) }}
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>
                                
                                @php
                                    $userAnswer = \App\Models\Tb_User_Answers::where('id_user', auth()->id())
                                        ->where('id_periode', $periode->id_periode)
                                        ->first();
                                @endphp
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($userAnswer && $userAnswer->status == 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Selesai
                                            </span>
                                        @elseif($userAnswer && $userAnswer->status == 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-play-circle mr-1"></i>
                                                Belum Dikerjakan
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="ml-4">
                                        @if($userAnswer && $userAnswer->status == 'completed')
                                            <a href="{{ route('alumni.questionnaire.response-detail', [
                                                'id_periode' => $periode->id_periode,
                                                'id_user_answer' => $userAnswer->id_user_answer
                                            ]) }}" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                        @elseif($userAnswer && $userAnswer->status == 'draft')
                                            <a href="{{ route('alumni.questionnaire.fill', $periode->id_periode) }}" 
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Lanjutkan
                                            </a>
                                        @else
                                            <a href="{{ route('alumni.questionnaire.fill', $periode->id_periode) }}" 
                                               class="{{ $periode->status == 'active' ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400' }} text-white px-3 py-1.5 rounded-md text-xs sm:text-sm transition-colors duration-200">
                                                <i class="fas fa-play mr-1"></i> Kerjakan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16 text-center">
                                        NO
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        JUDUL
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        STATUS
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($activePeriodes as $index => $periode)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    Kuesioner Alumni {{ date('Y', strtotime($periode->start_date)) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Periode: {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @php
                                                $userAnswer = \App\Models\Tb_User_Answers::where('id_user', auth()->id())
                                                    ->where('id_periode', $periode->id_periode)
                                                    ->first();
                                            @endphp
                                            
                                            @if($userAnswer && $userAnswer->status == 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Selesai
                                                </span>
                                            @elseif($userAnswer && $userAnswer->status == 'draft')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Draft
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-play-circle mr-1"></i>
                                                    Belum Dikerjakan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            @if($userAnswer && $userAnswer->status == 'completed')
                                                <a href="{{ route('alumni.questionnaire.response-detail', [
                                                    'id_periode' => $periode->id_periode,
                                                    'id_user_answer' => $userAnswer->id_user_answer
                                                ]) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-md inline-block transition-colors duration-200">
                                                    <i class="fas fa-eye mr-1"></i> Lihat
                                                </a>
                                            @elseif($userAnswer && $userAnswer->status == 'draft')
                                                <a href="{{ route('alumni.questionnaire.fill', $periode->id_periode) }}" 
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded-md inline-block transition-colors duration-200">
                                                    <i class="fas fa-edit mr-1"></i> Lanjutkan
                                                </a>
                                            @else
                                                <a href="{{ route('alumni.questionnaire.fill', $periode->id_periode) }}" 
                                                   class="{{ $periode->status == 'active' ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400' }} text-white px-4 py-1 rounded-md inline-block transition-colors duration-200">
                                                    <i class="fas fa-play mr-1"></i> Kerjakan
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Completed Questionnaires Section -->
            @php
                // Get completed questionnaires
                $completedAnswers = \App\Models\Tb_User_Answers::where('id_user', auth()->id())
                    ->where('status', 'completed')
                    ->with('periode')
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp

            @if($completedAnswers->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-700 mb-3 sm:mb-4">HASIL KUESIONER</h2>
                    
                    <!-- Mobile Cards View -->
                    <div class="block lg:hidden space-y-4">
                        @foreach($completedAnswers as $index => $userAnswer)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 text-sm sm:text-base">
                                            Kuesioner Alumni {{ date('Y', strtotime($userAnswer->periode->start_date)) }}
                                        </h3>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($userAnswer->periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($userAnswer->periode->end_date)->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Selesai: {{ $userAnswer->updated_at->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-end">
                                    <a href="{{ route('alumni.questionnaire.response-detail', [
                                        'id_periode' => $userAnswer->id_periode,
                                        'id_user_answer' => $userAnswer->id_user_answer
                                    ]) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs sm:text-sm transition-colors duration-200">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16 text-center">
                                        NO
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        JUDUL
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TANGGAL SELESAI
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($completedAnswers as $index => $userAnswer)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    Kuesioner Alumni {{ date('Y', strtotime($userAnswer->periode->start_date)) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Periode: {{ \Carbon\Carbon::parse($userAnswer->periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($userAnswer->periode->end_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $userAnswer->updated_at->format('d M Y, H:i') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $userAnswer->updated_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            <a href="{{ route('alumni.questionnaire.response-detail', [
                                                'id_periode' => $userAnswer->id_periode,
                                                'id_user_answer' => $userAnswer->id_user_answer
                                            ]) }}" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-md inline-block transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($activePeriodes->isEmpty() && $completedAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-4xl sm:text-5xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold mb-2">Tidak Ada Kuesioner</h2>
                    <p class="text-gray-600 mb-4 text-sm sm:text-base">
                        Saat ini tidak ada kuesioner yang tersedia atau yang telah Anda isi.
                    </p>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Silahkan periksa kembali nanti.
                    </p>
                </div>
            @endif
        </div>
    </main>
</div>
<!-- script JS  -->
<script src="{{ asset('./js/alumni.js') }}"></script>

@endsection
