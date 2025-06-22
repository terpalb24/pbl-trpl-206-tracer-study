@extends('layouts.app')

@php
    $company = auth()->user()->company;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">

    {{-- Sidebar Komponen --}}
    <x-company.sidebar/>

    {{-- Konten Utama --}}
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
         <x-company.header title="Beranda" />

        {{-- Konten Utama Beranda --}}
        <div class="p-6">
            {{-- Kartu Sambutan Komponen --}}
            <x-company.welcome-card :company="$company" />

            {{-- Kartu Profil Komponen --}}
            <x-company.profile-card :company="$company" />
        </div>
    </main>
</div>

   <!-- script JS  -->
           <script src="{{ asset('js/company.js') }}"></script>
@endsection
