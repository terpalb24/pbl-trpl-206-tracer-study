@extends('layouts.app')

@php
    $company = auth()->user()->company;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-50 overflow-hidden" id="dashboard-container">

    {{-- Sidebar --}}
    @include('components.company.sidebar')

    {{-- Tombol Toggle Sidebar (untuk mobile) - duplikat dihapus karena sudah ada di header --}}
    {{-- <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i>
    </button> --}}

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Beranda'])

        <!-- Content Container dengan responsive padding -->
        <div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6 lg:space-y-8">
            <!-- Grid Layout untuk cards -->
            <div class="grid gap-4 sm:gap-6 lg:gap-8">
                <!-- Welcome Card -->
                <div class="w-full">
                    @include('components.company.welcome-card')
                </div>
                
                <!-- Profile Card -->
                <div class="w-full">
                    @include('components.company.profile-card')
                </div>
            </div>
            
            <!-- Dashboard Stats Grid (untuk future expansion) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <!-- Tempat untuk stats cards atau dashboard widgets lainnya -->
                {{-- Contoh: --}}
                {{-- @include('components.company.stats-cards') --}}
            </div>

            <!-- Recent Activities Section (untuk future expansion) -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Recent Jobs (2/3 width on XL screens) -->
                <div class="xl:col-span-2">
                    {{-- @include('components.company.recent-jobs') --}}
                </div>
                
                <!-- Quick Actions (1/3 width on XL screens) -->
                <div class="xl:col-span-1">
                    {{-- @include('components.company.quick-actions') --}}
                </div>
            </div>
        </div>

    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>

@endsection
