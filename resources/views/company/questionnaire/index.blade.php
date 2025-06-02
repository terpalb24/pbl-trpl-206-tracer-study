@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
   {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
            {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner employee'])


        <!-- Content Section -->
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Active Questionnaires (Similar to Alumni KUISIONER section) -->
            @if($availableActivePeriodes->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">KUESIONER</h2>
                    <div class="overflow-x-auto">
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
                                @foreach($availableActivePeriodes as $index => $periode)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    Kuesioner Employer {{ date('Y', strtotime($periode->start_date)) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Periode: {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @php
                                                $draftAnswer = $draftUserAnswers->where('id_periode', $periode->id_periode)->first();
                                                $completedAnswer = $completedUserAnswers->where('id_periode', $periode->id_periode)->first();
                                            @endphp
                                            
                                            @if($completedAnswer)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Selesai
                                                </span>
                                            @elseif($draftAnswer)
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
                                            @php
                                                $draftAnswer = $draftUserAnswers->where('id_periode', $periode->id_periode)->first();
                                                $completedAnswer = $completedUserAnswers->where('id_periode', $periode->id_periode)->first();
                                            @endphp
                                            
                                            @if($completedAnswer)
                                                <!-- Already completed - show view button -->
                                                <a href="{{ route('company.questionnaire.response-detail', [
                                                    'id_periode' => $periode->id_periode,
                                                    'id_user_answer' => $completedAnswer->id_user_answer
                                                ]) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-md inline-block">
                                                    <i class="fas fa-eye mr-1"></i> Lihat
                                                </a>
                                            @elseif($draftAnswer)
                                                <!-- Has draft - show continue button -->
                                                <a href="{{ route('company.questionnaire.fill', $periode->id_periode) }}" 
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded-md inline-block">
                                                    <i class="fas fa-edit mr-1"></i> Lanjutkan
                                                </a>
                                            @else
                                                <!-- Not started - show start button -->
                                                <a href="{{ route('company.questionnaire.fill', $periode->id_periode) }}" 
                                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded-md inline-block">
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

            <!-- Completed Questionnaires (Similar to Alumni HASIL KUISIONER section) -->
            @if($completedUserAnswers->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">HASIL KUESIONER</h2>
                    <div class="overflow-x-auto">
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
                                @foreach($completedUserAnswers as $index => $userAnswer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-4 whitespace-nowrap text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    Kuesioner Employer {{ date('Y', strtotime($userAnswer->periode->start_date)) }}
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
                                            <a href="{{ route('company.questionnaire.response-detail', [
                                                'id_periode' => $userAnswer->id_periode,
                                                'id_user_answer' => $userAnswer->id_user_answer
                                            ]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-md inline-block">
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

            @if($availableActivePeriodes->isEmpty() && $completedUserAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Tidak Ada Kuesioner</h2>
                    <p class="text-gray-600 mb-4">
                        Saat ini tidak ada kuesioner yang tersedia atau yang telah Anda isi.
                    </p>
                    <p class="text-gray-600">
                        Silahkan periksa kembali nanti.
                    </p>
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
