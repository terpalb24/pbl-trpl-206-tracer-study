@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
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
                <h1 class="text-2xl font-bold text-blue-800">Respons Kuesioner</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
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
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <!-- Period Info Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">Informasi Periode</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                  ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($periode->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Mulai</p>
                        <p class="font-medium">{{ $periode->start_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Selesai</p>
                        <p class="font-medium">{{ $periode->end_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Responses Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Responden</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'alumni']) }}" 
                            class="px-3 py-1 text-xs border rounded-full {{ request('filter') == 'alumni' ? 'bg-blue-100 border-blue-300 text-blue-700' : 'bg-white border-gray-300 text-gray-600' }}">
                            Alumni
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode, 'filter' => 'company']) }}" 
                            class="px-3 py-1 text-xs border rounded-full {{ request('filter') == 'company' ? 'bg-blue-100 border-blue-300 text-blue-700' : 'bg-white border-gray-300 text-gray-600' }}">
                            Perusahaan
                        </a>
                        <a href="{{ route('admin.questionnaire.responses', ['id_periode' => $periode->id_periode]) }}" 
                            class="px-3 py-1 text-xs border rounded-full {{ !request('filter') ? 'bg-blue-100 border-blue-300 text-blue-700' : 'bg-white border-gray-300 text-gray-600' }}">
                            Semua
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal Pengisian</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @if(count($userAnswers) > 0)
                                @foreach($userAnswers as $index => $userAnswer)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                                        <td class="px-4 py-3">{{ ($userAnswers->currentPage() - 1) * $userAnswers->perPage() + $loop->iteration }}</td>
                                        
                                        <!-- Perbaiki: Gunakan display_name dan tambahkan info tambahan -->
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="font-medium">{{ $userAnswer->display_name }}</p>
                                                @if($userAnswer->nim)
                                                    <p class="text-xs text-gray-500">NIM: {{ $userAnswer->nim }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500">{{ $userAnswer->username }}</p>
                                            </div>
                                        </td>
                                        
                                        <!-- Perbaiki: Gunakan user_type_text yang sudah diset di controller -->
                                        <td class="px-4 py-3">
                                            @if($userAnswer->user_type_text == 'Alumni')
                                                <span class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                                    <i class="fas fa-graduation-cap mr-1"></i>Alumni
                                                </span>
                                            @elseif($userAnswer->user_type_text == 'Perusahaan')
                                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    <i class="fas fa-building mr-1"></i>Perusahaan
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                                    <i class="fas fa-user mr-1"></i>User
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $userAnswer->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                <i class="fas {{ $userAnswer->status == 'completed' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                                {{ $userAnswer->status == 'completed' ? 'Selesai' : 'Belum Selesai' }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-4 py-3">
                                            <div>
                                                <p>{{ \Carbon\Carbon::parse($userAnswer->created_at)->format('d M Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($userAnswer->created_at)->format('H:i') }}</p>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 py-3">
                                            <a href="{{ route('admin.questionnaire.response-detail', [$periode->id_periode, $userAnswer->id_user_answer]) }}" 
                                               class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm inline-flex items-center transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-lg font-medium mb-2">Belum ada responden</p>
                                            <p class="text-sm">Belum ada yang mengisi kuesioner untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t text-sm text-gray-500">
                    {{ $userAnswers->appends(request()->query())->links() }}
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.questionnaire.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md inline-flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
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
