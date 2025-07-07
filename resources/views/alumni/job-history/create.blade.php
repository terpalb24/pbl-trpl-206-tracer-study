@extends('layouts.app')

@php
    $alumni = auth()->user()->alumni;
@endphp

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <x-alumni.sidebar/>
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">
        <x-alumni.header title="Riwayat Kerja" />
        <div class="p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-8xl mx-auto">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Form Tambah Riwayat Kerja</h2>

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

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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

                        <!-- ✅ MODIFIKASI: Sedang Bekerja dengan notifikasi -->
                        <div>
                            <div class="flex items-start">
                                <input type="checkbox" id="is_current" name="is_current" {{ old('is_current') ? 'checked' : '' }} class="mt-1">
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
            </div>
        </div>
    </main>
</div>

<!-- Select2 & Script -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#id_company').select2({
            placeholder: "Pilih atau ketik nama perusahaan...",
            allowClear: true
        });

        const isCurrent = document.getElementById('is_current');
        const endDateSection = document.getElementById('end_date_section');

        function toggleEndDate() {
            if (isCurrent.checked) {
                endDateSection.style.display = 'none';
            } else {
                endDateSection.style.display = '';
            }
        }

        const alumniStatus = '{{ $alumni->status }}';
        
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
    document.addEventListener("DOMContentLoaded", function () {
    // Toggle sidebar
    const toggleSidebar = document.getElementById("toggle-sidebar");
    const closeSidebar = document.getElementById("close-sidebar");
    const sidebar = document.getElementById("sidebar");
    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener("click", function () {
            sidebar.classList.toggle("-translate-x-full");
        });
    }
    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener("click", function () {
            sidebar.classList.add("-translate-x-full");
        });
    }

    // Profile dropdown toggle
    const profileToggle = document.getElementById("profile-toggle");
    const profileDropdown = document.getElementById("profile-dropdown");
    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener("click", function () {
            profileDropdown.classList.toggle("hidden");
        });
        document.addEventListener("click", function (event) {
            if (
                !profileDropdown.contains(event.target) &&
                !profileToggle.contains(event.target)
            ) {
                profileDropdown.classList.add("hidden");
            }
        });
    }
    });
</script>
@endsection
