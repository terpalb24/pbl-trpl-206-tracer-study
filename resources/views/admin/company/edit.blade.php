@extends('layouts.app')

@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
     <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

  <x-slot name="header">
          <x-admin.header>Pengguna alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>
    {{-- Konten utama (default slot) --}}
    <div class="p-6">
        <form
            action="{{ route('admin.company.update', $company->id_company) }}"
            method="POST"
            class="bg-white rounded-xl shadow-md p-6 md:p-10"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id" class="block font-semibold mb-1">ID Company</label>
                    <input
                        type="text"
                        name="id"
                        id="id"
                        value="{{ $company->id_company }}"
                        readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100"
                    />
                </div>

                <div>
                    <label for="company_name" class="block font-semibold mb-1">Nama Perusahaan</label>
                    <input
                        type="text"
                        name="company_name"
                        id="company_name"
                        value="{{ old('company_name', $company->company_name) }}"
                        class="w-full border rounded px-3 py-2"
                    />
                    @error('company_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_email" class="block font-semibold mb-1">Email</label>
                    <input
                        type="email"
                        name="company_email"
                        id="company_email"
                        value="{{ old('company_email', $company->company_email) }}"
                        class="w-full border rounded px-3 py-2"
                    />
                    @error('company_email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        name="company_phone_number"
                        id="company_phone_number"
                        value="{{ old('company_phone_number', $company->company_phone_number) }}"
                        class="w-full border rounded px-3 py-2"
                    />
                    @error('company_phone_number')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="company_address" class="block font-semibold mb-1">Alamat</label>
                    <textarea
                        name="company_address"
                        id="company_address"
                        rows="3"
                        class="w-full border rounded px-3 py-2 resize-none"
                    >{{ old('company_address', $company->company_address) }}</textarea>
                    @error('company_address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a
                    href="{{ route('admin.company.index') }}"
                    class="bg-gray-200 text-gray-700 py-2 px-6 rounded-lg font-semibold hover:bg-gray-300 transition-colors"
                >
                    Kembali
                </a>
                <button
                    type="submit"
                    class="bg-blue-800 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-900 transition-colors"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <!-- script JS  -->
           <script src="{{ asset('js/script.js') }}"></script>
    @endpush
</x-layout-admin>
@endsection
