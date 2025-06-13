@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <x-alumni.sidebar class="lg:block hidden" />
    <main class="flex-grow overflow-y-auto relative" id="main-content">
        <x-alumni.header title="Riwayat Kerja" />
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Form Tambah Riwayat Kerja</h2>

                <form action="{{ route('alumni.job-history.store') }}" method="POST" id="job-history-form">
                    @csrf
                    <input type="hidden" name="start_date" id="start_date">
                    <input type="hidden" name="end_date" id="end_date">

                    <div class="space-y-6">
                        <!-- Nama Perusahaan -->
                        <div class="mb-4">
                            <label for="id_company" class="block text-gray-700 font-semibold mb-2">Nama Perusahaan</label>
                            <select name="id_company" id="id_company" class="input-field" onchange="toggleNewCompanyInput(this)">
                                <option value="">-- Pilih Perusahaan --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}">{{ $company->company_name }}</option>
                                @endforeach
                                <option value="__other">Perusahaan tidak ada di daftar</option>
                            </select>
                        </div>
                        <div class="mb-4" id="new-company-input" style="display:none;">
                            <label for="new_company_name" class="block text-gray-700 font-semibold mb-2">Nama Perusahaan Baru</label>
                            <input type="text" name="new_company_name" id="new_company_name" class="input-field" placeholder="Masukkan nama perusahaan">
                        </div>

                        <!-- Sedang Bekerja -->
                        <div>
                            <input type="checkbox" id="is_current" name="is_current" {{ old('is_current') ? 'checked' : '' }}>
                            <label for="is_current" class="text-base font-medium ml-2">Saya saat ini sedang bekerja di peran ini</label>
                        </div>

                        <!-- Mulai Bekerja -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Mulai Bekerja</label>
                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label for="start_month" class="block text-gray-600 mb-1">Bulan</label>
                                    <select id="start_month" class="w-full border rounded px-3 py-2" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach([
                                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                                        ] as $num => $bulan)
                                            <option value="{{ $num }}" {{ old('start_month') == $num ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label for="start_year" class="block text-gray-600 mb-1">Tahun</label>
                                    <select id="start_year" class="w-full border rounded px-3 py-2" required>
                                        <option value="">Pilih Tahun</option>
                                        @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}" {{ old('start_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                                    <select id="end_month" class="w-full border rounded px-3 py-2">
                                        <option value="">Pilih Bulan</option>
                                        @foreach([
                                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                                        ] as $num => $bulan)
                                            <option value="{{ $num }}" {{ old('end_month') == $num ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label for="end_year" class="block text-gray-600 mb-1">Tahun</label>
                                    <select id="end_year" class="w-full border rounded px-3 py-2">
                                        <option value="">Pilih Tahun</option>
                                        @for($y = date('Y'); $y >= 1990; $y--)
                                            <option value="{{ $y }}" {{ old('end_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Posisi -->
                        <div>
                            <label for="position" class="block text-gray-700 font-medium mb-2">Posisi</label>
                            <input type="text" name="position" id="position" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required placeholder="e.g. Software Engineer" value="{{ old('position') }}">
                        </div>

                        <!-- Gaji -->
                        <div>
                            <label for="salary" class="block text-gray-700 font-medium mb-2">Gaji</label>
                            <select name="salary" id="salary" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" required>
                                <option value="">Pilih range gaji...</option>
                                <option value="0" {{ old('salary') == '0' ? 'selected' : '' }}>0 - 3.000.000</option>
                                <option value="3000000" {{ old('salary') == '3000000' ? 'selected' : '' }}>3.000.000 - 4.500.000</option>
                                <option value="4500000" {{ old('salary') == '4500000' ? 'selected' : '' }}>4.500.000 - 5.000.000</option>
                                <option value="5000000" {{ old('salary') == '5000000' ? 'selected' : '' }}>5.000.000 - 5.500.000</option>
                                <option value="6000000" {{ old('salary') == '6000000' ? 'selected' : '' }}>6.000.000 - 6.500.000</option>
                                <option value="6500000" {{ old('salary') == '6500000' ? 'selected' : '' }}>6.500.000 - 7.000.000</option>
                                <option value="7000000" {{ old('salary') == '7000000' ? 'selected' : '' }}>7.000.000 - 8.000.000</option>
                                <option value="8000000" {{ old('salary') == '8000000' ? 'selected' : '' }}>8.000.000 - 9.000.000</option>
                                <option value="9000000" {{ old('salary') == '9000000' ? 'selected' : '' }}>9.000.000 - 10.000.000</option>
                                <option value="10000000" {{ old('salary') == '10000000' ? 'selected' : '' }}>10.000.000 - 12.000.000</option>
                                <option value="12000000" {{ old('salary') == '12000000' ? 'selected' : '' }}>12.000.000 - 15.000.000</option>
                                <option value="15000000" {{ old('salary') == '15000000' ? 'selected' : '' }}>15.000.000 - 20.000.000</option>
                                <option value="20000000" {{ old('salary') == '20000000' ? 'selected' : '' }}>> 20.000.000</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <a href="{{ route('alumni.job-history.index') }}" class="mr-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
                @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            </div>
        </div>
    </main>
</div>

<!-- Select2 & Script -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    
    $(document).ready(function () {
        $('#id_company').select2({
            placeholder: "Pilih atau ketik nama perusahaan...",
            allowClear: true
        });

        const isCurrent = document.getElementById('is_current');
        const endDateSection = document.getElementById('end_date_section');

        function toggleEndDate() {
            endDateSection.style.display = isCurrent.checked ? 'none' : '';
        }

        isCurrent.addEventListener('change', toggleEndDate);
        toggleEndDate();

        // Set final date format on submit
        document.getElementById('job-history-form').addEventListener('submit', function (e) {
            const sm = document.getElementById('start_month').value;
            const sy = document.getElementById('start_year').value;
            const em = document.getElementById('end_month').value;
            const ey = document.getElementById('end_year').value;

            document.getElementById('start_date').value = sm && sy ? `${sy}-${sm}-01` : '';

            if (!isCurrent.checked && em && ey) {
                document.getElementById('end_date').value = `${ey}-${em}-01`;
            } else {
                document.getElementById('end_date').value = '';
            }
        });
        
    });
    
    function toggleNewCompanyInput(select) {
    var input = document.getElementById('new-company-input');
    if (select.value === '__other') {
        input.style.display = 'block';
        document.getElementById('id_company').value = '';
    } else {
        input.style.display = 'none';
        document.getElementById('new_company_name').value = '';
    }
}
</script>
@endsection
