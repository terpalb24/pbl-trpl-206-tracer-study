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
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Riwayat Kuesioner Employer</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/company-icon.png') }}" alt="Company Logo" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Company' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Perusahaan</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

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