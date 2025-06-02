@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="editprofil-container">

    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Tombol Toggle Sidebar (Untuk Mobile) -->
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Edit Profil'])
   
        {{-- Flash Message --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4 mx-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4 mx-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Edit Profil -->
        <form action="{{ route('company.update') }}" method="POST"
            class="bg-white rounded-2xl shadow-md mt-8 mx-4 md:mx-10 lg:mx-16 xl:mx-24 p-6 md:p-10 lg:p-12 xl:p-16">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 text-sm ">

                <!-- company_name (readonly) -->
                <div>
                    <label class="block font-semibold mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}"
                        class="w-full border px-3 py-2 rounded bg-gray-100 cursor-not-allowed @error('company_name') border-red-500 @enderror"
                        readonly>
                    @error('company_name')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- company_address -->
                <div>
                    <label class="block font-semibold mb-1">Company Address</label>
                    <input type="text" name="company_address" value="{{ old('company_address', $company->company_address) }}"
                        class="w-full border px-3 py-2 rounded @error('company_address') border-red-500 @enderror">
                    @error('company_address')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- company_email -->
                <div>
                    <label class="block font-semibold mb-1">Company Email</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $company->company_email) }}"
                        class="w-full border px-3 py-2 rounded @error('company_email') border-red-500 @enderror">
                    @error('company_email')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- company_phone_number -->
                <div>
                    <label class="block font-semibold mb-1">Company Phone Number</label>
                    <input type="text" name="company_phone_number" value="{{ old('company_phone_number', $company->company_phone_number) }}"
                        class="w-full border px-3 py-2 rounded @error('company_phone_number') border-red-500 @enderror">
                    @error('company_phone_number')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('dashboard.company') }}"
                    class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </main>
</div>

 <!-- Script -->
          <script src="{{ asset('js/company.js') }}"></script>
@endsection
