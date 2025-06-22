@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
       {{-- Sidebar Komponen --}}
    <x-alumni.sidebar/>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">
        <!-- Header dengan judul -->
        <x-alumni.header title="Riwayat Kerja" />

   

        <!-- Content -->
        <div class="p-6 mt-12">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-end mb-4"></div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- ✅ TAMBAHAN: Alert khusus untuk perubahan status -->
                @if(session('status_updated'))
                    <div class="mb-4 p-4 bg-blue-100 border border-blue-400 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-blue-600 mr-3"></i>
                            <div>
                                <h4 class="text-blue-800 font-semibold">Status Profil Diperbarui</h4>
                                <p class="text-blue-700">
                                    Status profil Anda telah otomatis diperbarui menjadi "Bekerja" 
                                    karena Anda sedang bekerja di posisi ini.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <x-alumni.job-history-table :jobHistories="$jobHistories" />
            </div>
        </div>
    </main>
</div>

   <!-- script JS  -->
           <script src="{{ asset('js/alumni.js') }}"></script>
@endsection
