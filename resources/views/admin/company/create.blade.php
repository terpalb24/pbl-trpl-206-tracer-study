@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
     <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

   <x-slot name="header">
         <x-admin.header>Pengguna alumni</x-admin.header>
        <x-admin.profile-dropdown>Tambah Daftar pengguna Alumni</x-admin.profile-dropdown>
    </x-slot>

        <!-- Form Section -->
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.company.store') }}" method="POST" class="bg-white rounded-xl shadow-md p-6 md:p-10">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Nama Perusahaan -->
                    <div>
                        <label for="company_name" class="block font-semibold mb-1">Nama Perusahaan</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" class="w-full border rounded px-3 py-2">
                        @error('company_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Email Perusahaan -->
                    <div>
                        <label for="company_email" class="block font-semibold mb-1">Email Perusahaan</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email') }}" class="w-full border rounded px-3 py-2">
                        @error('company_email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Alamat Perusahaan -->
                    <div class="md:col-span-2">
                        <label for="company_address" class="block font-semibold mb-1">Alamat Perusahaan</label>
                        <textarea name="company_address" id="company_address" rows="3" class="w-full border rounded px-3 py-2">{{ old('company_address') }}</textarea>
                        @error('company_address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="md:col-span-2">
                        <label for="company_phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                        <input type="text" name="company_phone_number" id="company_phone_number" value="{{ old('company_phone_number') }}" class="w-full border rounded px-3 py-2">
                        @error('company_phone_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('admin.company.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded font-semibold hover:bg-gray-700 transition mr-4">
                        Kembali
                    </a>
                    <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded font-semibold hover:bg-blue-600 transition">
                        Tambah Perusahaan
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
   <!-- script JS  -->
           <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection



