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
            
            <!-- Active Questionnaires (Similar to Alumni KUISIONER section) -->
            @if($availableActivePeriodes->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">KUESIONER</h2>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            {{ $availableActivePeriodes->count() }} Periode Aktif
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Mulai
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Berakhir
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($availableActivePeriodes as $periode)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($periode->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($periode->end_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            {{-- UBAH: Link ke halaman pemilihan alumni --}}
                                            <a href="{{ route('company.questionnaire.select-alumni', $periode->id_periode) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                <i class="fas fa-users mr-2"></i>
                                                Pilih Alumni
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            

            @if($availableActivePeriodes->isEmpty() && $completedUserAnswers->isEmpty() && $draftUserAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Tidak Ada Kuesioner</h2>
                    <p class="text-gray-600 mb-4">
                        Saat ini tidak ada kuesioner yang tersedia untuk periode aktif.
                    </p>
                    <p class="text-gray-600">
                        Kuesioner akan muncul di sini ketika administrator mengaktifkan periode penilaian baru.
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
