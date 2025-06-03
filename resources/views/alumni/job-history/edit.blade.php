@extends('layouts.app')

@section('content')

@php
    $alumni = auth()->user()->alumni ?? auth()->user(); // fallback jika tidak ada relasi alumni
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header dengan judul -->
        <x-alumni.header title="Riwayat Kerja" />

        <!-- Content -->
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Edit Riwayat Kerja</h2>
                <form action="{{ route('alumni.job-history.update', $jobHistory->id_jobhistory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="id_company" class="block text-gray-700 font-medium mb-2">Nama Perusahaan</label>
                            <select name="id_company" id="id_company" class="select2 w-full" required>
                                <option value="">Pilih atau ketik nama perusahaan...</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}"
                                        {{ old('id_company', $jobHistory->id_company) == $company->id_company ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="position" class="block text-gray-700 font-medium mb-2">Posisi</label>
                            <input type="text" name="position" id="position"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. Software Engineer"
                                value="{{ old('position', $jobHistory->position) }}">
                        </div>
                        <div>
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Gaji</label>
                            <input type="text" name="salary" id="salary"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. 5.000.000"
                                value="{{ old('salary', number_format($jobHistory->salary, 0, ',', '.')) }}">
                        </div>
                    
                        <div>
                            <label for="start_date" class="block text-gray-700 font-medium mb-2">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required value="{{ old('start_date', $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div>
                            <label for="end_date" class="block text-gray-700 font-medium mb-2">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required value="{{ old('end_date', $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <a href="{{ route('alumni.job-history.index') }}" class="mr-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- script JS -->
<script src="{{ asset('js/alumni.js') }}"></script>
@endsection
