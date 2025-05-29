@extends('layouts.app')
@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('alumni.job-history.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">
                        <i class="fas fa-plus mr-2"></i> Tambah Riwayat Kerja
                    </a>
                </div>

                <?php
                if(session('success'))
                    echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">'.session('success').'</div>';
                ?>

                @if($jobHistories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">NO</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">POSISI</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">NAMA PERUSAHAAN</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobHistories as $index => $jobHistory)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 font-medium">{{ $jobHistory->position }}</td>
                                    <td class="py-3 px-4">
                                        {{ $jobHistory->company->company_name ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('alumni.job-history.edit', $jobHistory->id_jobhistory) }}" class="p-1 text-gray-600 hover:text-yellow-600" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat kerja ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-gray-600 hover:text-red-600" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="p-1 text-gray-600 hover:text-blue-600" title="Detail"
                                                onclick="showDetail({{ $jobHistory->id_jobhistory }})">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                        <!-- Modal Detail -->
                                        <div id="modal-detail-{{ $jobHistory->id_jobhistory }}" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
                                                <button onclick="closeDetail({{ $jobHistory->id_jobhistory }})" class="absolute top-2 right-2 text-gray-500 hover:text-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <h2 class="text-xl font-semibold mb-4 text-blue-800">Detail Riwayat Kerja</h2>
                                                <div class="mb-2"><strong>Nama Perusahaan:</strong> {{ $jobHistory->company->company_name ?? '-' }}</div>
                                                <div class="mb-2"><strong>Posisi:</strong> {{ $jobHistory->position }}</div>
                                                <div class="mb-2"><strong>Gaji:</strong> Rp {{ number_format($jobHistory->salary, 0, ',', '.') }}</div>
                                                <div class="mb-2"><strong>Durasi:</strong> {{ $jobHistory->duration }}</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Riwayat Kerja</h3>
                        <p class="mt-2 text-gray-500">Anda belum menambahkan riwayat kerja. Mulai dengan menambahkan pengalaman kerja pertama Anda.</p>
                        <div class="mt-6">
                            <a href="{{ route('alumni.job-history.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Tambah Riwayat Kerja
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

<script>
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

    function showDetail(id) {
        document.getElementById('modal-detail-' + id).classList.remove('hidden');
    }
    function closeDetail(id) {
        document.getElementById('modal-detail-' + id).classList.add('hidden');
    }
</script>
@endsection