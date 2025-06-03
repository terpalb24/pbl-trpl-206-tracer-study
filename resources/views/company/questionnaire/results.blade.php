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
            @include('company.sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <main class="flex-grow overflow-y-auto" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Riwayat Kuesioner'])
             

        <!-- Content Section -->
        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @if($userAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Belum Ada Riwayat</h2>
                    <p class="text-gray-600">
                        Perusahaan Anda belum mengisi kuesioner apapun saat ini.
                    </p>
                    <a href="{{ route('company.questionnaire.index') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Lihat Kuesioner
                    </a>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Kuesioner Yang Telah Diisi</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Periode</th>
                                    <th class="py-3 px-4 text-left">Tanggal Pengisian</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($userAnswers as $userAnswer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $userAnswer->periode->periode_name }}</td>
                                        <td class="py-3 px-4">{{ $userAnswer->updated_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3 px-4">
                                            @if($userAnswer->status == 'completed')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <!-- FIX: Correctly pass both required parameters to the route -->
                                            <a href="{{ route('company.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                               class="text-blue-600 hover:underline">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                            @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                                <a href="{{ route('company.questionnaire.fill', [$userAnswer->id_periode]) }}" 
                                                   class="text-green-600 hover:underline ml-3">
                                                    <i class="fas fa-edit mr-1"></i> Lanjutkan
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
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hidden');
        });
    
        document.getElementById('close-sidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('hidden');
        });
    
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