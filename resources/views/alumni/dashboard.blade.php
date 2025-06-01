@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">

    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    {{-- Konten Utama --}}
    <main class="flex-grow overflow-y-auto" id="main-content">

        {{-- Header --}}
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Beranda</h1>
            </div>

            {{-- Dropdown Profil Komponen --}}
            <x-alumni.profile-dropdown :alumni="$alumni" />
        </div>

        {{-- Konten Utama Beranda --}}
        <div class="p-6">
            {{-- Kartu Sambutan Komponen --}}
            <x-alumni.welcome-card :alumni="$alumni" />

            {{-- Kartu Profil Komponen --}}
            <x-alumni.profile-card :alumni="$alumni" />
        </div>
    </main>
</div>

   <!-- script JS  -->
           <script src="{{ asset('js/alumni.js') }}"></script>
@endsection
