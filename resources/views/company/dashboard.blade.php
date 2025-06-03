@extends('layouts.app')

@php
    $company = auth()->user()->company;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">

    {{-- Sidebar --}}
    @include('components.company.sidebar')

    {{-- Tombol Toggle Sidebar (untuk mobile) --}}
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Beranda'])

        <div class="p-6">
            @include('components.company.welcome-card')
            @include('components.company.profile-card')
        </div>

    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection
