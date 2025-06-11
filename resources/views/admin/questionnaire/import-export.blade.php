@extends('layouts.app')

@section('content')
<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Import/Export Kuisioner</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <div class="bg-white rounded-xl shadow-md p-6 md:p-10 mb-6 mt-4 mx-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Import Section -->
            <div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-blue-900 mb-1 flex items-center">
                        <i class="fas fa-upload mr-2"></i> Import Kuisioner
                    </h2>
                    <p class="text-sm text-gray-600 mb-3">
                        Upload file Excel untuk import kuesioner. Pastikan mengikuti format template yang disediakan.
                    </p>
                </div>
                <form action="{{ route('admin.questionnaires.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                    @csrf
                    <label for="periode_id" class="text-sm font-medium text-gray-700">Pilih Periode</label>
                    <select name="periode_id" id="periode_id"
                        class="border border-gray-300 rounded-md px-4 py-2 w-full text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('periode_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id_periode }}" {{ old('periode_id') == $periode->id_periode ? 'selected' : '' }}>
                                {{ $periode->periode_name ?? 'Periode #' . $periode->id_periode }} ({{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('periode_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    <input type="file" name="file" id="file"
                        class="border border-gray-300 rounded-md px-4 py-2 w-full text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('file') border-red-500 @enderror"
                        required>
                    @error('file')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    <div class="flex gap-2 mt-2">
                        <button type="submit" class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                            <i class="fas fa-upload"></i> Import
                        </button>
                        <a href="{{ route('admin.questionnaires.download-template') }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </form>
            </div>
            <!-- Export Section -->
            <div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-green-800 mb-1 flex items-center">
                        <i class="fas fa-file-export mr-2"></i> Export Kuisioner
                    </h2>
                    <p class="text-sm text-gray-600 mb-3">
                        Export seluruh kuesioner ke file Excel berdasarkan periode.
                    </p>
                </div>
                <form action="{{ route('admin.questionnaires.export') }}" method="GET" class="flex flex-col gap-3">
                    <label for="periode_id_export" class="text-sm font-medium text-gray-700">Pilih Periode</label>
                    <select name="periode_id" id="periode_id_export"
                        class="border border-gray-300 rounded-md px-4 py-2 w-full text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('periode_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id_periode }}" {{ request('periode_id') == $periode->id_periode ? 'selected' : '' }}>
                                {{ $periode->periode_name ?? 'Periode #' . $periode->id_periode }} ({{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('periode_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold text-sm transition duration-200">
                        <i class="fas fa-file-export"></i> Export Questionnaires
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-blue-50 rounded-md p-4 mb-6">
            <h3 class="font-semibold text-blue-700 mb-2">Petunjuk Format Import/Export</h3>
            <ol class="list-decimal pl-5 text-sm text-blue-700 space-y-1">
                <li>Download template untuk melihat format yang diperlukan.</li>
                <li>Isi <b>Category Name</b>, <b>Category Order</b>, <b>For Type</b> (alumni/company/both).</li>
                <li>Isi <b>Question Text</b>, <b>Question Type</b> (text/option/multiple/rating/scale/date/location/numeric/email), <b>Question Order</b>.</li>
                <li>Kolom <b>Before Text</b> dan <b>After Text</b> untuk teks sebelum/sesudah input (opsional).</li>
                <li>Untuk tipe <b>scale</b>, isi <b>Scale Min Label</b> dan <b>Scale Max Label</b>.</li>
                <li>Untuk tipe pilihan (option/multiple/rating/scale), isi <b>Options</b> dipisahkan dengan tanda |.</li>
                <li>Jika ada opsi "Other", isi <b>Other Option Indexes</b> (index dimulai dari 0, pisahkan dengan koma jika lebih dari satu).</li>
                <li>Jika opsi "Other" memiliki before/after text, isi <b>Other Before Texts</b> dan <b>Other After Texts</b> sesuai urutan opsi (pisahkan dengan |).</li>
            </ol>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('admin.questionnaire.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition">
                Kembali ke Daftar Kuisioner
            </a>
        </div>
    </div>
</x-layout-admin>
@endsection

@if(isset($export_mode) && $export_mode)
    <script>
        // Scroll ke export section jika export_mode aktif
        window.onload = function() {
            document.getElementById('periode_id_export').scrollIntoView({behavior: 'smooth'});
        }
    </script>
@endif
