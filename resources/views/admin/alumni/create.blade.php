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
         <x-admin.header>Alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>
           

    <form action="{{ route('admin.alumni.store') }}" method="POST" class="bg-white rounded-xl shadow-md p-6 md:p-10 m-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Semua input tetap sama seperti yang sudah kamu buat -->
            <div>
                <label for="nim" class="block font-semibold mb-1">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim') }}" class="w-full border rounded px-3 py-2">
                @error('nim')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nik" class="block font-semibold mb-1">NIK</label>
                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="w-full border rounded px-3 py-2">
                @error('nik')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="name" class="block font-semibold mb-1">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="w-full border rounded px-3 py-2">
                @error('phone_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="gender" class="block font-semibold mb-1">Jenis Kelamin</label>
                <select name="gender" id="gender" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="pria" {{ old('gender') == 'pria' ? 'selected' : '' }}>pria</option>
                    <option value="wanita" {{ old('gender') == 'wanita' ? 'selected' : '' }}>wanita</option>
                </select>
                @error('gender')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="date_of_birth" class="block font-semibold mb-1">Tanggal Lahir</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border rounded px-3 py-2">
                @error('date_of_birth')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="id_study" class="block font-semibold mb-1">Prodi</label>
                <select name="id_study" id="id_study" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodi as $prodis)
                        <option value="{{ $prodis->id_study }}" {{ old('id_study') == $prodis->id_study ? 'selected' : '' }}>
                            {{ $prodis->study_program }}
                        </option>
                    @endforeach
                </select>
                @error('id_study')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="batch" class="block font-semibold mb-1">Angkatan</label>
                <input type="number" name="batch" id="batch" value="{{ old('batch') }}" class="w-full border rounded px-3 py-2">
                @error('batch')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="graduation_year" class="block font-semibold mb-1">Tahun Lulus</label>
                <input type="number" name="graduation_year" id="graduation_year" value="{{ old('graduation_year') }}" class="w-full border rounded px-3 py-2">
                @error('graduation_year')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="ipk" class="block font-semibold mb-1">IPK</label>
                <input type="number" step="0.01" name="ipk" id="ipk" value="{{ old('ipk') }}" class="w-full border rounded px-3 py-2">
                @error('ipk')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block font-semibold mb-1">Status</label>
                <select name="status" id="status" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Status --</option>
                    <option value="Bekerja" {{ old('status') == 'Bekerja' ? 'selected' : '' }}>bekerja</option>
                    <option value="Tidak Bekerja" {{ old('status') == 'Tidak Bekerja' ? 'selected' : '' }}>tidak bekerja</option>
                    <option value="Melanjutkan Studi" {{ old('status') == 'Melanjutkan Studi' ? 'selected' : '' }}>Melanjutkan Studi</option>
                    <option value="Berwiraswasta" {{ old('status') == 'Berwiraswasta' ? 'selected' : '' }}>Berwiraswasta</option>
                    <option value="Sedang Mencari Kerja" {{ old('status') == 'Sedang Mencari Kerja' ? 'selected' : '' }}>Sedang Mencari Kerja</option>
                </select>
                @error('status')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block font-semibold mb-1">Alamat</label>
                <textarea name="address" id="address" rows="3" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('admin.alumni.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded font-semibold hover:bg-gray-700 transition mr-4">
                Kembali
            </a>
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded font-semibold hover:bg-blue-600 transition">
                Tambah Alumni
            </button>
        </div>
    </form>

    <script>
        document.getElementById('toggle-sidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('hidden');
        });

        document.getElementById('profile-toggle').addEventListener('click', () => {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        });

        document.getElementById('logout-btn').addEventListener('click', function (event) {
            event.preventDefault();

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';

            const csrfTokenInput = document.createElement('input');
            csrfTokenInput.type = 'hidden';
            csrfTokenInput.name = '_token';
            csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.appendChild(csrfTokenInput);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</x-layout-admin>
@endsection
