@extends('layouts.app')
@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
       {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto relative" id="main-content">
        <!-- Header dengan judul -->
        <x-alumni.header title="Riwayat Kerja" />

        <!-- Content -->
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Form Tambah Riwayat Kerja</h2>
                <form action="{{ route('alumni.job-history.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="id_company" class="block text-gray-700 font-medium mb-2">Nama Perusahaan</label>
                            <select name="id_company" id="id_company" class="select2 w-full" required>
                                <option value="">Pilih atau ketik nama perusahaan...</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-gray-700 font-medium mb-2">Posisi</label>
                            <input type="text" name="position" id="position"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. Software Engineer">
                        </div>
                        <div>
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Gaji</label>
                            <input type="text" name="salary" id="salary"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. 5.000.000">
                        </div>
                        <div>
                            <label for="duration" class="block text-gray-700 font-medium mb-2">Durasi</label>
                            <input type="text" name="duration" id="duration"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. 2 tahun">
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <a href="{{ route('alumni.job-history.index') }}" class="mr-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- script JS  -->
           <script src="{{ asset('js/alumni.js') }}"></script>
@endsection