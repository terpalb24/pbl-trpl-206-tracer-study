@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
       {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto relative" id="main-content">
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

                <x-alumni.job-history-table :jobHistories="$jobHistories" />
            </div>
        </div>
    </main>
</div>

   <!-- script JS  -->
           <script src="{{ asset('js/alumni.js') }}"></script>
@endsection
