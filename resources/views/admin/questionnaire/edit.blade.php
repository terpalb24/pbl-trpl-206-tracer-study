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
                <button id="toggle-sidebar" class="mr-4 text-gray-600 lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">Edit Periode Kuesioner</h1>
                    <p class="text-sm text-gray-600">Perbarui tanggal periode kuesioner</p>
                </div>
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
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700">Edit Periode</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" id="success-alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="ml-auto" onclick="document.getElementById('success-alert').style.display='none'">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" id="error-alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                        <button type="button" class="ml-auto" onclick="document.getElementById('error-alert').style.display='none'">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Edit Form -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-edit mr-2 text-blue-600"></i>
                        Edit Periode Kuesioner
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Perbarui tanggal mulai dan selesai periode kuesioner</p>
                </div>

                <form action="{{ route('admin.questionnaire.update', $periode->id_periode) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Period Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg mb-6 border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Periode Saat Ini
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white p-3 rounded-md border border-blue-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-play text-green-600 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-600">Tanggal Mulai</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900">{{ $periode->start_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $periode->start_date->format('l') }}</p>
                            </div>
                            <div class="bg-white p-3 rounded-md border border-blue-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-stop text-red-600 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-600">Tanggal Selesai</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900">{{ $periode->end_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $periode->end_date->format('l') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                  ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                <i class="fas {{ $periode->status == 'active' ? 'fa-play-circle' : ($periode->status == 'inactive' ? 'fa-pause-circle' : 'fa-stop-circle') }} mr-1"></i>
                                {{ ucfirst($periode->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-plus text-green-600 mr-1"></i>
                                Tanggal Mulai Baru
                            </label>
                            <input type="date" name="start_date" id="start_date" 
                                value="{{ old('start_date', $periode->start_date->format('Y-m-d')) }}" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-minus text-red-600 mr-1"></i>
                                Tanggal Selesai Baru
                            </label>
                            <input type="date" name="end_date" id="end_date" 
                                value="{{ old('end_date', $periode->end_date->format('Y-m-d')) }}" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-600 mr-3 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-800 mb-1">Informasi Status</h4>
                                <p class="text-sm text-yellow-700">
                                    Status periode akan otomatis diperbarui berdasarkan tanggal yang baru setelah disimpan:
                                </p>
                                <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                                    <li><i class="fas fa-circle text-yellow-500 mr-2" style="font-size: 6px;"></i><strong>Inactive:</strong> Jika tanggal mulai belum tiba</li>
                                    <li><i class="fas fa-circle text-green-500 mr-2" style="font-size: 6px;"></i><strong>Active:</strong> Jika berada dalam rentang tanggal</li>
                                    <li><i class="fas fa-circle text-red-500 mr-2" style="font-size: 6px;"></i><strong>Expired:</strong> Jika tanggal selesai sudah terlewat</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0 md:space-x-4 pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.questionnaire.index') }}" 
                               class="flex items-center px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar
                            </a>
                            <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                               class="flex items-center px-6 py-3 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                <i class="fas fa-eye mr-2"></i>
                                Lihat Detail
                            </a>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" onclick="resetForm()" 
                                    class="flex items-center px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </button>
                            <button type="submit" 
                                    class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });

    // Profile dropdown functionality
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

    // Logout functionality
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

    // Form validation
    const form = document.querySelector('form');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate >= endDate) {
            endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
        } else {
            endDateInput.setCustomValidity('');
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);

    form.addEventListener('submit', function(event) {
        validateDates();
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
});

// Reset form function
function resetForm() {
    const form = document.querySelector('form');
    if (confirm('Apakah Anda yakin ingin mereset form ke nilai awal?')) {
        // Reset to original values
        document.getElementById('start_date').value = '{{ $periode->start_date->format('Y-m-d') }}';
        document.getElementById('end_date').value = '{{ $periode->end_date->format('Y-m-d') }}';
    }
}
</script>
@endsection
