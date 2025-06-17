@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Pilih Alumni untuk Dinilai'])

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
                    <li class="text-gray-500">/</li>
                    <li>
                        <a href="{{ route('company.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700">Pilih Alumni</li>
                </ol>
            </nav>

            <!-- Periode Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">{{ $periode->title }}</h3>
                        @if($periode->description)
                            <p class="text-blue-800 mb-3">{{ $periode->description }}</p>
                        @endif
                        <div class="flex items-center space-x-6 text-sm text-blue-700">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-circle text-green-500 mr-2"></i>
                                <span>Periode Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Alumni Information -->
            @if(!empty($eligibleGraduationYears))
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-blue-800 font-medium">Target Alumni untuk Periode Ini</p>
                            <p class="text-blue-700 text-sm">
                                Hanya alumni dengan tahun lulus: <strong>{{ implode(', ', $eligibleGraduationYears) }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Alumni yang Tersedia untuk Dinilai -->
            @if($availableAlumni->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">ALUMNI YANG DAPAT DINILAI</h2>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-users mr-2"></i>
                            {{ $availableAlumni->count() }} Alumni Tersedia
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($availableAlumni as $jobHistory)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col h-full">
                                    <div class="flex-grow">
                                        <h3 class="font-semibold text-gray-800 mb-2">{{ $jobHistory->alumni->name }}</h3>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-id-card text-gray-400 mr-2 w-4"></i>
                                                <span>{{ $jobHistory->nim }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-graduation-cap text-gray-400 mr-2 w-4"></i>
                                                <span>Lulus {{ $jobHistory->alumni->graduation_year }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-briefcase text-gray-400 mr-2 w-4"></i>
                                                <span>{{ $jobHistory->position ?? 'Posisi tidak disebutkan' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-2 w-4"></i>
                                                <span>{{ $jobHistory->duration }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-gray-100">
                                        @if(in_array($jobHistory->nim, $draftNims))
                                            <a href="{{ route('company.questionnaire.fill', [$periode->id_periode, $jobHistory->nim]) }}"
                                               class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors duration-200 w-full justify-center">
                                                <i class="fas fa-edit mr-2"></i>
                                                Lanjutkan Draft
                                            </a>
                                        @else
                                            <a href="{{ route('company.questionnaire.fill', [$periode->id_periode, $jobHistory->nim]) }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200 w-full justify-center">
                                                <i class="fas fa-clipboard-check mr-2"></i>
                                                Mulai Penilaian
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Alumni yang Sudah Dinilai (Informational) -->
            @if(count($completedNims) > 0)
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">ALUMNI YANG SUDAH DINILAI</h2>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ count($completedNims) }} Alumni Selesai
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-3"></i>
                            <div>
                                <p class="text-green-800 font-medium">Penilaian Selesai</p>
                                <p class="text-green-700 text-sm">
                                    Anda telah menyelesaikan penilaian untuk {{ count($completedNims) }} alumni pada periode ini. 
                                    Alumni yang sudah dinilai tidak dapat dinilai ulang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($availableAlumni->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-users-slash text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Tidak Ada Alumni yang Dapat Dinilai</h2>
                    @if(count($completedNims) > 0)
                        <p class="text-gray-600 mb-4">
                            Semua alumni yang memenuhi kriteria sudah dinilai pada periode ini.
                        </p>
                    @else
                        <p class="text-gray-600 mb-4">
                            Tidak ada alumni yang memenuhi kriteria untuk periode ini:
                        </p>
                        <ul class="text-gray-600 text-sm space-y-1 mb-4">
                            <li>• Alumni harus masih aktif bekerja di perusahaan Anda</li>
                            @if(!empty($eligibleGraduationYears))
                                <li>• Alumni harus lulus pada tahun: {{ implode(', ', $eligibleGraduationYears) }}</li>
                            @endif
                        </ul>
                    @endif
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200 mt-4">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Kuesioner
                    </a>
                </div>
            @endif

            <!-- Back Button -->
            @if($availableAlumni->isNotEmpty())
                <div class="text-center mt-6">
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Kuesioner
                    </a>
                </div>
            @endif
        </div>
    </main>
</div>

<script>
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.add('hidden');
    });

    document.getElementById('profile-toggle').addEventListener('click', () => {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        
        if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    document.getElementById('logout-btn').addEventListener('click', function(event) {
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
</script>
@endsection