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

            <!-- Alumni yang Tersedia untuk Dinilai -->
            @if($availableAlumni->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">ALUMNI YANG DAPAT DINILAI</h2>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-user-graduate mr-2"></i>
                            {{ $availableAlumni->count() }} Alumni Tersedia
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableAlumni as $jobHistory)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-grow">
                                        <h3 class="font-semibold text-gray-900">{{ $jobHistory->alumni->name }}</h3>
                                        <p class="text-sm text-gray-600">NIM: {{ $jobHistory->nim }}</p>
                                        @if($jobHistory->alumni->graduation_year)
                                            <p class="text-xs text-gray-500">Lulus: {{ $jobHistory->alumni->graduation_year }}</p>
                                        @endif
                                    </div>
                                    @if(in_array($jobHistory->nim, $draftNims))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Job History Info -->
                                <div class="bg-white rounded p-3 mb-3 text-sm">
                                    <p class="font-medium text-gray-700">Riwayat Kerja:</p>
                                    <p class="text-gray-600">{{ $jobHistory->position }}</p>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>{{ \Carbon\Carbon::parse($jobHistory->start_date)->format('M Y') }} - 
                                              {{ $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('M Y') : 'Sekarang' }}</span>
                                        @if($jobHistory->duration)
                                            <span>{{ $jobHistory->duration }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="text-center">
                                    @if(in_array($jobHistory->nim, $draftNims))
                                        <a href="{{ route('company.questionnaire.fill', [$periode->id_periode, $jobHistory->nim]) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors duration-200 w-full justify-center">
                                            <i class="fas fa-edit mr-2"></i>
                                            Lanjutkan Penilaian
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
                            Semua alumni yang pernah bekerja di perusahaan Anda sudah dinilai pada periode ini.
                        </p>
                    @else
                        <p class="text-gray-600 mb-4">
                            Tidak ada alumni yang tercatat pernah bekerja di perusahaan Anda.
                        </p>
                        <p class="text-gray-600">
                            Pastikan data riwayat kerja alumni sudah diperbarui.
                        </p>
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