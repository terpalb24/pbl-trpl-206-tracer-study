@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">

    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar/>

    {{-- Konten Utama --}}
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
         <x-alumni.header title="Beranda" />

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
           
   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
   @if(session('password_updated'))
   <script>
       Swal.fire({
           icon: 'success',
           title: 'Berhasil!',
           text: '{{ session('password_updated') }}',
           showConfirmButton: true,
           confirmButtonText: 'OK',
           confirmButtonColor: '#10a2b9'
       });
   </script>
   @endif
@endsection
