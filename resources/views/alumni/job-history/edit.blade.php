@extends('layouts.app')

@section('content)

@php
    $alumni = auth()->user()->alumni ?? auth()->user();
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <x-alumni.header title="Riwayat Kerja" />

        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Edit Riwayat Kerja</h2>
                <form action="{{ route('alumni.job-history.update', $jobHistory->id_jobhistory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        <!-- Nama Perusahaan -->
                        <div>
                            <label for="id_company" class="block text-gray-700 font-medium mb-2">Nama Perusahaan</label>
                            <select name="id_company" id="id_company" class="select2 w-full mb-2" required>
                                <option value="">Pilih atau ketik nama perusahaan...</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}"
                                        {{ old('id_company', $jobHistory->id_company) == $company->id_company ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Checkbox -->
                        <div>
                            <input type="checkbox" id="is_current" name="is_current"
                                {{ old('is_current', !$jobHistory->end_date) ? 'checked' : '' }}>
                            <label for="is_current" class="text-base font-medium ml-2">Saya saat ini sedang bekerja di peran ini</label>
                        </div>
                        <!-- Mulai Bekerja -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Mulai Bekerja</label>
                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label for="start_month" class="block text-gray-600 mb-1">Bulan</label>
                                    <select name="start_month" id="start_month" class="w-full border rounded px-3 py-2" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $bulan)
                                            <option value="{{ $num }}"
                                                {{ old('start_month', $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->format('m') : '') == $num ? 'selected' : '' }}>
                                                {{ $bulan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label for="start_year" class="block text-gray-600 mb-1">Tahun</label>
                                    <select name="start_year" id="start_year" class="w-full border rounded px-3 py-2" required>
                                        <option value="">Pilih Tahun</option>
                                        @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}"
                                                {{ old('start_year', $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->format('Y') : '') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Selesai Bekerja -->
                        <div id="end_date_section">
                            <label class="block text-gray-700 font-medium mb-2">Selesai Bekerja</label>
                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label for="end_month" class="block text-gray-600 mb-1">Bulan</label>
                                    <select name="end_month" id="end_month" class="w-full border rounded px-3 py-2">
                                        <option value="">Pilih Bulan</option>
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $bulan)
                                            <option value="{{ $num }}"
                                                {{ old('end_month', $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('m') : '') == $num ? 'selected' : '' }}>
                                                {{ $bulan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label for="end_year" class="block text-gray-600 mb-1">Tahun</label>
                                    <select name="end_year" id="end_year" class="w-full border rounded px-3 py-2">
                                        <option value="">Pilih Tahun</option>
                                        @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}"
                                                {{ old('end_year', $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('Y') : '') == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Posisi -->
                        <div>
                            <label for="position" class="block text-gray-700 font-medium mb-2">Posisi</label>
                            <input type="text" name="position" id="position"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required placeholder="e.g. Software Engineer"
                                value="{{ old('position', $jobHistory->position) }}">
                        </div>
                        <!-- Gaji -->
                        <div>
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Gaji</label>
                            <select name="salary" id="salary"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
                                required>
                                <option value="">Pilih range gaji...</option>
                                <option value="0-3000000" {{ old('salary', $jobHistory->salary) == '0-3000000' ? 'selected' : '' }}>0 - 3.000.000</option>
                                <option value="3000000-4500000" {{ old('salary', $jobHistory->salary) == '3000000-4500000' ? 'selected' : '' }}> >3.000.000 - 4.500.000</option>
                                <option value="4500000-5000000" {{ old('salary', $jobHistory->salary) == '4500000-5000000' ? 'selected' : '' }}> >4.500.000 - 5.000.000</option>
                                <option value="5000000-5500000" {{ old('salary', $jobHistory->salary) == '5000000-5500000' ? 'selected' : '' }}> >5.000.000 - 5.500.000</option>
                                <option value="6000000-6500000" {{ old('salary', $jobHistory->salary) == '6000000-6500000' ? 'selected' : '' }}> >6.000.000 - 6.500.000</option>
                                <option value="6500000-7000000" {{ old('salary', $jobHistory->salary) == '6500000-7000000' ? 'selected' : '' }}> >6.500.000 - 7.000.000</option>
                                <option value="7000000-8000000" {{ old('salary', $jobHistory->salary) == '7000000-8000000' ? 'selected' : '' }}> >7.000.000 - 8.000.000</option>
                                <option value="8000000-9000000" {{ old('salary', $jobHistory->salary) == '8000000-9000000' ? 'selected' : '' }}> >8.000.000 - 9.000.000</option>
                                <option value="9000000-10000000" {{ old('salary', $jobHistory->salary) == '9000000-10000000' ? 'selected' : '' }}> >9.000.000 - 10.000.000</option>
                                <option value="10000000-12000000" {{ old('salary', $jobHistory->salary) == '10000000-12000000' ? 'selected' : '' }}> >10.000.000 - 12.000.000</option>
                                <option value="12000000-15000000" {{ old('salary', $jobHistory->salary) == '12000000-15000000' ? 'selected' : '' }}> >12.000.000 - 15.000.000</option>
                                <option value="15000000-20000000" {{ old('salary', $jobHistory->salary) == '15000000-20000000' ? 'selected' : '' }}> >15.000.000 - 20.000.000</option>
                                <option value="20000001" {{ old('salary', $jobHistory->salary) == '20000001' ? 'selected' : '' }}> >20.000.000</option>
                            </select>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isCurrent = document.getElementById('is_current');
    const endDateSection = document.getElementById('end_date_section');
    function toggleEndDate() {
        if (isCurrent.checked) {
            endDateSection.style.display = 'none';
            document.getElementById('end_month').value = '';
            document.getElementById('end_year').value = '';
        } else {
            endDateSection.style.display = '';
        }
    }
    isCurrent.addEventListener('change', toggleEndDate);
    toggleEndDate();
});
</script>
@endsection
