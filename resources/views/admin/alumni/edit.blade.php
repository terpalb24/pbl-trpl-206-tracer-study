@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>


     <x-slot name="header">
            <x-admin.header>Alumni</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Main Content Slot -->
    <form
        action="{{ route('admin.alumni.update', $alumni->nim) }}"
        method="POST"
        class="p-6 bg-white rounded-xl shadow-md max-w-5xl mx-auto mt-6 mb-12"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- NIM (readonly) -->
            <div>
                <label for="nim" class="block font-semibold mb-1">NIM</label>
                <input
                    type="text"
                    name="nim"
                    id="nim"
                    value="{{ old('nim', $alumni->nim) }}"
                    readonly
                    class="w-full border rounded px-3 py-2 bg-gray-100"
                />
                @error('nim')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- NIK -->
            <div>
                <label for="nik" class="block font-semibold mb-1">NIK</label>
                <input
                    type="text"
                    name="nik"
                    id="nik"
                    value="{{ old('nik', $alumni->nik) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('nik')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama -->
            <div>
                <label for="name" class="block font-semibold mb-1">Nama</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $alumni->name) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $alumni->email) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div>
                <label for="phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                <input
                    type="text"
                    name="phone_number"
                    id="phone_number"
                    value="{{ old('phone_number', $alumni->phone_number) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('phone_number')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label for="gender" class="block font-semibold mb-1">Jenis Kelamin</label>
                <select
                    name="gender"
                    id="gender"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option
                        value="pria"
                        {{ old('gender', $alumni->gender) == 'pria' ? 'selected' : '' }}
                    >
                        pria
                    </option>
                    <option
                        value="wanita"
                        {{ old('gender', $alumni->gender) == 'wanita' ? 'selected' : '' }}
                    >
                        wanita
                    </option>
                </select>
                @error('gender')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label for="date_of_birth" class="block font-semibold mb-1">Tanggal Lahir</label>
                <input
                    type="date"
                    name="date_of_birth"
                    id="date_of_birth"
                    value="{{ old('date_of_birth', $alumni->date_of_birth) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('date_of_birth')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prodi -->
            <div>
                <label for="id_study" class="block font-semibold mb-1">Prodi</label>
                <select
                    name="id_study"
                    id="id_study"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="">-- Pilih Prodi --</option>
                    @foreach ($prodi as $prodis)
                    <option
                        value="{{ $prodis->id_study }}"
                        {{ old('id_study', $alumni->id_study) == $prodis->id_study ? 'selected' : '' }}
                    >
                        {{ $prodis->study_program }}
                    </option>
                    @endforeach
                </select>
                @error('id_study')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block font-semibold mb-1">Status</label>
                <select
                    name="status"
                    id="status"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="">-- Pilih Status --</option>
                    <option value="bekerja" {{ old('status', $alumni->status) == 'bekerja' ? 'selected' : '' }}>
                        Bekerja
                    </option>
                    <option value="tidak bekerja" {{ old('status', $alumni->status) == 'tidak bekerja' ? 'selected' : '' }}>
                        Tidak Bekerja
                    </option>
                    <option value="melanjutkan studi" {{ old('status', $alumni->status) == 'melanjutkan studi' ? 'selected' : '' }}>
                        Melanjutkan Studi
                    </option>
                      <option value="berwiraswasta" {{ old('status', $alumni->status) == 'berwiraswasta' ? 'selected' : '' }}>
                       Berwiraswasta
                    </option>  <option value="sedang mencari kerja" {{ old('status', $alumni->status) == 'sedang mencari kerja' ? 'selected' : '' }}>
                       Sedang Mencari Kerja
                    </option>
                </select>
                @error('status')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- IPK -->
            <div>
                <label for="ipk" class="block font-semibold mb-1">IPK</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="4"
                    name="ipk"
                    id="ipk"
                    value="{{ old('ipk', $alumni->ipk) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('ipk')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Angkatan -->
            <div>
                <label for="batch" class="block font-semibold mb-1">Angkatan</label>
                <input
                    type="number"
                    name="batch"
                    id="batch"
                    value="{{ old('batch', $alumni->batch) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('batch')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tahun Lulus -->
            <div>
                <label for="graduation_year" class="block font-semibold mb-1">Tahun Lulus</label>
                <input
                    type="number"
                    name="graduation_year"
                    id="graduation_year"
                    value="{{ old('graduation_year', $alumni->graduation_year) }}"
                    class="w-full border rounded px-3 py-2"
                />
                @error('graduation_year')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="address" class="block font-semibold mb-1">Alamat</label>
                <textarea
                    name="address"
                    id="address"
                    rows="3"
                    class="w-full border rounded px-3 py-2 resize-none"
                >{{ old('address', $alumni->address) }}</textarea>
                @error('address')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <a
                href="{{ route('admin.alumni.index') }}"
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

</x-layout-admin>

  <!-- script JS  -->
           <script src="{{ asset('js/script.js') }}"></script>
@endsection
