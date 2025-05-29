@extends('layouts.app')
@php
    $alumni = auth()->user()->alumni;
@endphp


@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4 ">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('alumni.sidebar')
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
                <h1 class="text-2xl font-bold text-blue-800">Riwayat Kerja</h1>
            </div>
            <!-- Profile Dropdown Button -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}"
                        alt="Foto Profil"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ $alumni->name }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Alumni</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <!-- Dropdown Menu -->
                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Edit Riwayat Kerja</h2>
                <form action="{{ route('alumni.job-history.update', $jobHistory->id_jobhistory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="id_company" class="block text-gray-700 font-medium mb-2">Nama Perusahaan</label>
                            <select name="id_company" id="id_company" class="select2 w-full" required>
                                <option value="">Pilih atau ketik nama perusahaan...</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}"
                                        {{ old('id_company', $jobHistory->id_company) == $company->id_company ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-gray-700 font-medium mb-2">Posisi</label>
                            <input type="text" name="position" id="position"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. Software Engineer"
                                value="{{ old('position', $jobHistory->position) }}">
                        </div>
                        <div>
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Gaji</label>
                            <input type="text" name="salary" id="salary"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. 5.000.000"
                                value="{{ old('salary', number_format($jobHistory->salary, 0, ',', '.')) }}">
                        </div>
                        <div>
                            <label for="duration" class="block text-gray-700 font-medium mb-2">Durasi</label>
                            <input type="text" name="duration" id="duration"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. 2 tahun"
                                value="{{ old('duration', $jobHistory->duration) }}">
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <a href="{{ route('alumni.job-history.index') }}" class="mr-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#id_company').select2({
        placeholder: 'Cari atau tambah perusahaan...',
        allowClear: true
    });
});

// Format gaji ribuan
document.addEventListener('DOMContentLoaded', function () {
    const salaryInput = document.getElementById('salary');
    salaryInput.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            this.value = parseInt(value, 10).toLocaleString('id-ID');
        } else {
            this.value = '';
        }
    });

    // Saat submit, hilangkan format ribuan agar value yang dikirim hanya angka
    salaryInput.form.addEventListener('submit', function () {
        salaryInput.value = salaryInput.value.replace(/\D/g, '');
    });
});

// Toggle sidebar visibility
document.getElementById('toggle-sidebar').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('hidden');
});

document.getElementById('close-sidebar').addEventListener('click', function () {
    document.getElementById('sidebar').classList.add('hidden');
});

// Toggle profile dropdown
document.getElementById('profile-toggle').addEventListener('click', function () {
    document.getElementById('profile-dropdown').classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('profile-dropdown');
    const toggle = document.getElementById('profile-toggle');
    if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Logout functionality
document.getElementById('logout-btn').addEventListener('click', function (event) {
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