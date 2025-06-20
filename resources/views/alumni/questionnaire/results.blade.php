@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
   {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <x-alumni.header title="Hasil Kuesioner" />
        
        <!-- Content Section -->
        <div class="p-3 sm:p-4 lg:p-6">
            @if($userAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 text-center">
                    <div class="text-gray-500 mb-4 sm:mb-6">
                        <i class="fas fa-clipboard-list text-4xl sm:text-5xl lg:text-6xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2 sm:mb-4">Belum Ada Riwayat</h2>
                    <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg max-w-md mx-auto">
                        Anda belum mengisi kuesioner apapun saat ini. Silakan isi kuesioner yang tersedia.
                    </p>
                    <a href="{{ route('alumni.questionnaire.index') }}" 
                       class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Lihat Kuesioner
                    </a>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md border border-gray-200">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800">Kuesioner Yang Telah Anda Isi</h2>
                                <p class="text-sm sm:text-base text-gray-600 mt-1">{{ $userAnswers->count() }} kuesioner tersedia</p>
                            </div>
                            <a href="{{ route('alumni.questionnaire.index') }}" 
                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-plus mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Isi Kuesioner Baru</span>
                                <span class="sm:hidden">Baru</span>
                            </a>
                        </div>
                    </div>

                    <!-- Mobile Cards View -->
                    <div class="block lg:hidden">
                        <div class="divide-y divide-gray-200">
                            @foreach($userAnswers as $index => $userAnswer)
                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium text-gray-900 text-sm sm:text-base truncate">
                                                Kuesioner Alumni {{ date('Y', strtotime($userAnswer->periode->start_date)) }}
                                            </h3>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                                {{ $userAnswer->updated_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <span class="ml-2 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                            #{{ $index + 1 }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if($userAnswer->status == 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2 ml-4">
                                            <a href="{{ route('alumni.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                Lihat
                                            </a>
                                            @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                                <a href="{{ route('alumni.questionnaire.fill', [$userAnswer->id_periode]) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs transition-colors duration-200">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Lanjut
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NO
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PERIODE
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TANGGAL PENGISIAN
                                    </th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        STATUS
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($userAnswers as $index => $userAnswer)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        Kuesioner Alumni {{ date('Y', strtotime($userAnswer->periode->start_date)) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $userAnswer->updated_at->format('d M Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $userAnswer->updated_at->format('H:i') }} WIB
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($userAnswer->status == 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('alumni.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                                   class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm transition-colors duration-200"
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Lihat
                                                </a>
                                                @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                                    <a href="{{ route('alumni.questionnaire.fill', [$userAnswer->id_periode]) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm transition-colors duration-200"
                                                       title="Lanjutkan Mengisi">
                                                        <i class="fas fa-edit mr-1"></i>
                                                        Lanjutkan
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination if needed -->
                    @if($userAnswers->hasPages())
                        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ $userAnswers->firstItem() }} - {{ $userAnswers->lastItem() }} dari {{ $userAnswers->total() }} hasil
                                </div>
                                <div class="flex-1 flex justify-center sm:justify-end">
                                    {{ $userAnswers->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
                    @php
                        $completedCount = $userAnswers->where('status', 'completed')->count();
                        $draftCount = $userAnswers->where('status', 'draft')->count();
                        $totalCount = $userAnswers->count();
                        $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
                    @endphp

                    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-blue-100">
                                <i class="fas fa-clipboard-list text-blue-600 text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Total</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-green-100">
                                <i class="fas fa-check-circle text-green-600 text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Selesai</p>
                                <p class="text-lg sm:text-2xl font-bold text-green-600">{{ $completedCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-clock text-yellow-600 text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Draft</p>
                                <p class="text-lg sm:text-2xl font-bold text-yellow-600">{{ $draftCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-purple-100">
                                <i class="fas fa-chart-pie text-purple-600 text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Tingkat Selesai</p>
                                <p class="text-lg sm:text-2xl font-bold text-purple-600">{{ $completionRate }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- script JS  -->
<script src="{{ asset('./js/alumni.js') }}"></script>
@endsection