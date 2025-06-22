@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Riwayat Kuesioner'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 sm:mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 sm:mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Breadcrumb --}}
            <nav class="mb-4 sm:mb-6">
                <ol class="flex items-center space-x-2 text-sm overflow-x-auto">
                    <li>
                        <a href="{{ route('dashboard.company') }}" class="text-blue-600 hover:underline whitespace-nowrap">Dashboard</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline whitespace-nowrap">Kuesioner</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700 whitespace-nowrap">Riwayat</li>
                </ol>
            </nav>

            {{-- Tombol Isi Kuesioner Lagi jika masih ada periode aktif --}}
            @php
                $availableActivePeriodes = $availableActivePeriodes ?? (session('availableActivePeriodes') ?? collect());
            @endphp
            @if(isset($availableActivePeriodes) && $availableActivePeriodes->isNotEmpty())
                <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 rounded-xl p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <div class="flex-1">
                            <h3 class="text-lg sm:text-xl font-semibold text-green-800 mb-1 sm:mb-2">Periode Aktif Tersedia</h3>
                            <p class="text-sm sm:text-base text-green-700">
                                Masih ada alumni yang dapat Anda nilai pada periode aktif.
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('company.questionnaire.index') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 sm:px-5 sm:py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition-colors duration-200 text-sm sm:text-base">
                                <i class="fas fa-plus mr-2"></i>
                                <span class="hidden sm:inline">Isi Kuesioner Lagi</span>
                                <span class="sm:hidden">Isi Lagi</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($userAnswers->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-4xl sm:text-5xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold mb-2">Belum Ada Riwayat</h2>
                    <p class="text-sm sm:text-base text-gray-600 mb-4">
                        Perusahaan Anda belum mengisi kuesioner apapun saat ini.
                    </p>
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="inline-flex items-center px-4 py-2 sm:px-5 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Lihat Kuesioner
                    </a>
                </div>
            @else
                <!-- Results Table/Cards -->
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-700">Kuesioner Yang Telah Diisi</h2>
                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                            <i class="fas fa-list mr-2"></i>
                            <span>{{ $userAnswers->count() }} Riwayat</span>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alumni
                                        </th>
                                        <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Pengisian
                                        </th>
                                        <th class="px-4 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 xl:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($userAnswers as $userAnswer)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $userAnswer->alumni->name ?? 'Unknown Alumni' }}
                                                </div>
                                                @if(isset($userAnswer->alumni->nim))
                                                    <div class="text-xs text-gray-500">
                                                        {{ $userAnswer->alumni->nim }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $userAnswer->updated_at->format('d M Y, H:i') }}
                                            </td>
                                            <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                                                @if($userAnswer->status == 'completed')
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-edit mr-1"></i>
                                                        Draft
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('company.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        Lihat
                                                    </a>
                                                    @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                                        <a href="{{ route('company.questionnaire.fill', [$userAnswer->id_periode, $userAnswer->nim ?? $userAnswer->user->nim ?? $userAnswer->alumni->nim]) }}" 
                                                           class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
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
                    </div>

                    <!-- Mobile/Tablet Card View -->
                    <div class="lg:hidden space-y-4">
                        @foreach($userAnswers as $userAnswer)
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">
                                            {{ $userAnswer->alumni->name ?? 'Unknown Alumni' }}
                                        </h3>
                                        @if(isset($userAnswer->alumni->nim))
                                            <p class="text-xs text-gray-500 mt-1">{{ $userAnswer->alumni->nim }}</p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        @if($userAnswer->status == 'completed')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                <span class="hidden sm:inline">Selesai</span>
                                                <span class="sm:hidden">✓</span>
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-edit mr-1"></i>
                                                <span class="hidden sm:inline">Draft</span>
                                                <span class="sm:hidden">Draft</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-xs sm:text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $userAnswer->updated_at->format('d M Y, H:i') }}
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2 pt-2">
                                    <a href="{{ route('company.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200">
                                        <i class="fas fa-eye mr-1 sm:mr-2"></i>
                                        Lihat Detail
                                    </a>
                                    @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                        <a href="{{ route('company.questionnaire.fill', [$userAnswer->id_periode, $userAnswer->nim ?? $userAnswer->user->nim ?? $userAnswer->alumni->nim]) }}" 
                                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition-colors duration-200">
                                            <i class="fas fa-edit mr-1 sm:mr-2"></i>
                                            Lanjutkan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination (jika ada) -->
                @if(method_exists($userAnswers, 'links'))
                    <div class="mt-4 sm:mt-6">
                        {{ $userAnswers->links() }}
                    </div>
                @endif
            @endif

            <!-- Back Button -->
            <div class="text-center mt-4 sm:mt-6">
                <a href="{{ route('company.questionnaire.index') }}" 
                   class="inline-flex items-center px-4 py-2 sm:px-5 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Kuesioner
                </a>
            </div>
        </div>
    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection