@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner Employee'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Active Questionnaires -->
            @if($availableActivePeriodes->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-700">KUESIONER</h2>
                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            <span>{{ $availableActivePeriodes->count() }} Periode Aktif</span>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Mulai
                                        </th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Berakhir
                                        </th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($availableActivePeriodes as $periode)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            </td>
                                            <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" 
                                                   class="inline-flex items-center px-3 py-2 lg:px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                    <i class="fas fa-users mr-2"></i>
                                                    <span class="hidden lg:inline">Pilih Alumni</span>
                                                    <span class="lg:hidden">Pilih</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        @foreach($availableActivePeriodes as $periode)
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-gray-500 uppercase">Mulai:</span>
                                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-gray-500 uppercase">Berakhir:</span>
                                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <i class="fas fa-users mr-2"></i>
                                        Pilih Alumni
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($availableActivePeriodes->isEmpty() && $completedUserAnswers->isEmpty() && $draftUserAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-4xl sm:text-5xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold mb-2">Tidak Ada Kuesioner</h2>
                    <p class="text-sm sm:text-base text-gray-600 mb-4">
                        Saat ini tidak ada kuesioner yang tersedia untuk periode aktif.
                    </p>
                    <p class="text-sm sm:text-base text-gray-600">
                        Kuesioner akan muncul di sini ketika administrator mengaktifkan periode penilaian baru.
                    </p>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection
