@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-50 overflow-hidden" id="editprofil-container">
    
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Edit Profil'])
   
        <!-- Content Container -->
        <div class="p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 sm:mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 sm:mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Form Edit Profil -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 lg:p-8">
                <!-- Form Header -->
                <div class="mb-6 sm:mb-8">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Edit Profil Perusahaan</h2>
                    <p class="text-sm sm:text-base text-gray-600">Perbarui informasi perusahaan Anda di bawah ini.</p>
                </div>

                <form action="{{ route('company.update') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Company Name -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="company_name" 
                                   value="{{ old('company_name', $company->company_name) }}"
                                   class="w-full border border-gray-300 px-3 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('company_name') border-red-500 @enderror"
                                   placeholder="Masukkan nama perusahaan">
                            @error('company_name')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Email -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                                Email Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="company_email" 
                                   value="{{ old('company_email', $company->company_email) }}"
                                   class="w-full border border-gray-300 px-3 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('company_email') border-red-500 @enderror"
                                   placeholder="contoh@perusahaan.com">
                            @error('company_email')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Phone Number -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="company_phone_number" 
                                   value="{{ old('company_phone_number', $company->company_phone_number) }}"
                                   class="w-full border border-gray-300 px-3 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('company_phone_number') border-red-500 @enderror"
                                   placeholder="081234567890">
                            @error('company_phone_number')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hrd Name -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                                Nama HRD
                            </label>
                            <input type="text"
                                   name="Hrd_name"
                                   value="{{ old('Hrd_name', $company->Hrd_name) }}"
                                   class="w-full border border-gray-300 px-3 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('Hrd_name') border-red-500 @enderror"
                                   placeholder="Nama HRD (jika ada)">
                            @error('Hrd_name')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Address -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm sm:text-base font-semibold text-gray-700 mb-2">
                                Alamat Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="company_address" 
                                      rows="3"
                                      class="w-full border border-gray-300 px-3 py-2 sm:py-3 rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none @error('company_address') border-red-500 @enderror"
                                      placeholder="Masukkan alamat lengkap perusahaan">{{ old('company_address', $company->company_address) }}</textarea>
                            @error('company_address')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:ml-auto">
                            <a href="{{ route('dashboard.company') }}"
                               class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 sm:py-3 rounded-lg text-center text-sm sm:text-base font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Batal
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 sm:py-3 rounded-lg text-sm sm:text-base font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center space-x-2">
                                <i class="fas fa-save"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>
@endsection