@extends('layouts.app')
@php
    $admin = auth()->user()->admin;
@endphp
@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<x-layout-admin>
    <!-- Sidebar Slot -->
    <x-slot name="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
        </div>
    </x-slot>

    <!-- Header Slot -->
    <x-slot name="header">
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Beranda</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">Administrator</p>
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
                    <a href="#" id="logout-btn" data-logout-url="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Main Dashboard Content -->
    <div class="p-4 mt-6">
        <div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <div class="p-4 md:w-2/3 pl-12 mt-6">
                    <h2 class="text-4xl font-bold text-black leading-tight mb-2">Halo!</h2>
                    <p class="text-3xl font-semibold text-black leading-tight">Administrator</p>
                </div>
                <div class="md:w-1/3 flex items-center justify-center p-4">
                    <img src="{{ asset('assets/images/adminprofile.png') }}" alt="Admin Profile" class="h-40 w-40 object-cover">
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="flex items-center p-4 bg-blue-950 text-white rounded-2xl shadow gap-4">
                <div class="bg-white p-3 rounded-2xl shadow">
                    <i class="fas fa-user-graduate text-blue-950 text-2xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ number_format($alumniCount) }}</div>
                    <div class="text-2xl">Alumni</div>
                </div>
            </div>
            <div class="flex items-center p-4 bg-sky-400 text-white rounded-2xl shadow gap-4">
                <div class="bg-white p-3 rounded-2xl shadow">
                    <i class="fas fa-building text-sky-400 text-2xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">{{ number_format($companyCount) }}</div>
                    <div class="text-2xl">Perusahaan</div>
                </div>
            </div>
            <div class="flex items-center p-4 bg-orange-500 text-white rounded-2xl shadow gap-4">
                <div class="bg-white p-3 rounded-2xl shadow">
                    <i class="fas fa-check-circle text-orange-500 text-2xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">2.300</div>
                    <div class="text-2xl">Mengisi Kuisioner</div>
                </div>
            </div>
        </div>

        <!-- Statistik Chart -->
        <div class="bg-blue-100 p-6 rounded-2xl shadow">
            <div class="font-bold mb-4">Statistik</div>
            <canvas id="statistikChart" height="100"></canvas>
        </div>
    </div>

    <!-- Script -->
          <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection