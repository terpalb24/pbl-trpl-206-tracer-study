@extends('layouts.app')

@section('content')

@php
    $alumni = auth()->user()->alumni ?? auth()->user();
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar/>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">
        <x-alumni.header title="Riwayat Kerja" />

        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Edit Riwayat Kerja</h2>

                <!-- ✅ TAMBAHAN: Notifikasi status alumni -->
                @if($alumni->status !== 'bekerja')
                    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-amber-600 mr-3 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-amber-800 mb-1">Perhatian Status Profil</h4>
                                <p class="text-sm text-amber-700 mb-2">
                                    Status profil Anda saat ini: <strong>"{{ ucfirst($alumni->status) }}"</strong>
                                </p>
                                <p class="text-sm text-amber-700">
                                    Jika Anda mencentang "Saya saat ini sedang bekerja di perusahaan ini", 
                                    status profil akan otomatis diperbarui menjadi <strong>"Bekerja"</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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

                        <!-- ✅ MODIFIKASI: Checkbox Saat Ini Bekerja dengan notifikasi -->
                        <div>
                            <div class="flex items-start">
                                <input type="checkbox" id="is_current" name="is_current"
                                    {{ old('is_current', !$jobHistory->end_date) ? 'checked' : '' }}>
                                <div class="ml-2">
                                    <label for="is_current" class="text-base font-medium">Saya saat ini sedang bekerja di perusahaan ini</label>
                                    @if($alumni->status !== 'bekerja')
                                        <p class="text-sm text-amber-600 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Mencentang opsi ini akan mengubah status profil Anda menjadi "Bekerja"
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Mulai Bekerja -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Mulai Bekerja</label>
                            <div class="flex gap-4">
                                <div class="w-1/2">
                                    <label for="start_month" class="block text-gray-600 mb-1">Bulan</label>
                                    <select name="start_month" id="start_month" class="w-full border rounded px-3 py-2" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach([
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ] as $num => $bulan)
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
                                        @foreach([
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ] as $num => $bulan)
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
                                <option value="0" {{ old('salary', $jobHistory->salary) == '0' ? 'selected' : '' }}>0 - 3.000.000</option>
                                <option value="3000000" {{ old('salary', $jobHistory->salary) == '3000000' ? 'selected' : '' }}>3.000.000 - 4.500.000</option>
                                <option value="4500000" {{ old('salary', $jobHistory->salary) == '4500000' ? 'selected' : '' }}>4.500.000 - 5.000.000</option>
                                <option value="5000000" {{ old('salary', $jobHistory->salary) == '5000000' ? 'selected' : '' }}>5.000.000 - 5.500.000</option>
                                <option value="6000000" {{ old('salary', $jobHistory->salary) == '6000000' ? 'selected' : '' }}>6.000.000 - 6.500.000</option>
                                <option value="6500000" {{ old('salary', $jobHistory->salary) == '6500000' ? 'selected' : '' }}>6.500.000 - 7.000.000</option>
                                <option value="7000000" {{ old('salary', $jobHistory->salary) == '7000000' ? 'selected' : '' }}>7.000.000 - 8.000.000</option>
                                <option value="8000000" {{ old('salary', $jobHistory->salary) == '8000000' ? 'selected' : '' }}>8.000.000 - 9.000.000</option>
                                <option value="9000000" {{ old('salary', $jobHistory->salary) == '9000000' ? 'selected' : '' }}>9.000.000 - 10.000.000</option>
                                <option value="10000000" {{ old('salary', $jobHistory->salary) == '10000000' ? 'selected' : '' }}>10.000.000 - 12.000.000</option>
                                <option value="12000000" {{ old('salary', $jobHistory->salary) == '12000000' ? 'selected' : '' }}>12.000.000 - 15.000.000</option>
                                <option value="15000000" {{ old('salary', $jobHistory->salary) == '15000000' ? 'selected' : '' }}>15.000.000 - 20.000.000</option>
                                <option value="20000000" {{ old('salary', $jobHistory->salary) == '20000000' ? 'selected' : '' }}>> 20.000.000</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit"
                                class="bg-blue-600 text-white font-semibold px-6 py-3 rounded hover:bg-blue-700 transition duration-300">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </main>
</div>

<!-- Select2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ PERBAIKAN: JavaScript dengan SweetAlert2 -->
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#id_company').select2({
        placeholder: "Pilih atau ketik nama perusahaan...",
        allowClear: true
    });

    const isCurrent = document.getElementById('is_current');
    const endDateSection = document.getElementById('end_date_section');
    const alumniStatus = '{{ $alumni->status }}';

    function toggleEndDate() {
        if (isCurrent.checked) {
            endDateSection.style.display = 'none';
        } else {
            endDateSection.style.display = '';
        }
    }

    isCurrent.addEventListener('change', function() {
        // Jika checkbox dicentang dan status bukan "bekerja"
        if (this.checked && alumniStatus !== 'bekerja') {
            // Tampilkan SweetAlert2 konfirmasi
            Swal.fire({
                title: 'Konfirmasi Perubahan Status',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Dengan mencentang opsi ini, status profil Anda akan diperbarui:</p>
                        <div class="bg-gray-50 p-3 rounded-lg mb-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status saat ini:</span>
                                <span class="font-semibold text-red-600">"{{ ucfirst($alumni->status) }}"</span>
                            </div>
                            <div class="flex items-center justify-center my-2">
                                <i class="fas fa-arrow-down text-blue-500"></i>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status baru:</span>
                                <span class="font-semibold text-green-600">"Bekerja"</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">Apakah Anda setuju dengan perubahan ini?</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: '<i class="fas fa-check mr-2"></i>Ya, Setuju',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Tidak',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-lg',
                    title: 'text-lg font-bold',
                    confirmButton: 'font-semibold',
                    cancelButton: 'font-semibold'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // User mengkonfirmasi, toggle end date
                    toggleEndDate();
                    
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        title: 'Perubahan Dikonfirmasi!',
                        text: 'Status profil akan diperbarui menjadi "Bekerja" setelah form disimpan.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        customClass: {
                            popup: 'rounded-lg'
                        }
                    });
                } else {
                    // User membatalkan, uncheck checkbox
                    this.checked = false;
                    toggleEndDate();
                }
            });
        } else {
            // Jika checkbox unchecked atau status sudah "bekerja"
            toggleEndDate();
        }
    });

    // Initialize toggle state
    toggleEndDate();
});
</script>
<!-- script JS  -->
<script src="{{ asset('js/alumni.js') }}"></script>
@endsection
