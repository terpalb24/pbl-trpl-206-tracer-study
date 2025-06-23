<div class="bg-blue-100 p-3 sm:p-4 lg:p-6 rounded-xl sm:rounded-2xl shadow">
    <div class="font-bold mb-4 sm:mb-6 text-lg sm:text-xl text-blue-900">Statistik Alumni</div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 items-stretch">
        <!-- Bar Chart: Status Alumni -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow flex flex-col items-center justify-center p-3 sm:p-4 lg:p-6 order-1">
            <div class="font-semibold mb-3 sm:mb-4 text-sm sm:text-base text-blue-800 text-center">Status Alumni</div>
            <div class="w-full h-64 sm:h-72 lg:h-80">
                <canvas id="statistikChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Distribusi Tahun Lulus Alumni -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow flex flex-col items-center justify-center p-3 sm:p-4 lg:p-6 order-2">
            <div class="font-semibold mb-3 sm:mb-4 text-sm sm:text-base text-blue-800 text-center">Distribusi Tahun Lulus Alumni</div>
            
            <!-- Filter tahun lulus - responsive -->
            <form method="GET" id="graduation-year-filter-form" class="mb-3 w-full">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                    <label for="graduation_year_filter" class="text-xs sm:text-sm text-gray-700 whitespace-nowrap">Tahun Lulus:</label>
                    <select name="graduation_year_filter" id="graduation_year_filter" 
                        class="w-full sm:flex-1 border border-gray-300 rounded px-2 py-1 sm:px-3 sm:py-2 text-xs sm:text-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        onchange="document.getElementById('graduation-year-filter-form').submit()">




                        <option value="">Semua Tahun</option>
                        @foreach($allGraduationYears ?? [] as $year)
                            <option value="{{ $year }}" {{ (isset($filterGraduationYear) && $filterGraduationYear == $year) ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Jumlah alumni total -->
            <div class="mb-2 sm:mb-3 text-center">
                <span class="text-xs sm:text-sm text-gray-700">
                    Jumlah Alumni:
                    <span class="font-bold text-blue-700">
                        {{
                            isset($graduationYearStatisticData) && count($graduationYearStatisticData)
                                ? array_sum($graduationYearStatisticData)
                                : 0
                        }}
                    </span>
                </span>
            </div>

            <div class="w-full h-48 sm:h-56 lg:h-64 flex items-center justify-center">
                <canvas id="graduationYearPieChart" class="max-w-full max-h-full"></canvas>
            </div>
        </div>

        <!-- Statistik: Jumlah Alumni Mengisi Kuesioner & Rata-rata Pendapatan -->
        <div class="flex flex-col gap-3 sm:gap-4 lg:gap-6 justify-between h-full order-3 lg:order-3">
            <!-- Alumni Mengisi Kuesioner -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow p-3 sm:p-4 flex flex-col gap-2">
                <div class="font-semibold mb-1 sm:mb-2 text-sm sm:text-base text-blue-800">Jumlah Alumni Mengisi Kuesioner</div>
                
                <form method="GET" class="mb-2">
                    <div class="flex flex-col gap-2">
                        <label for="study_program" class="text-xs sm:text-sm text-gray-700">Program Studi:</label>
                        <select name="study_program" id="study_program" 
                            class="border border-gray-300 rounded px-2 py-1 sm:px-3 sm:py-2 text-xs sm:text-sm focus:ring-blue-500 focus:border-blue-500 transition w-full" 
                            onchange="this.form.submit()">
                            <option value="">Semua Program Studi</option>
                            @foreach($studyPrograms as $sp)
                                <option value="{{ $sp->id_study }}" {{ request('study_program') == $sp->id_study ? 'selected' : '' }}>
                                    {{ $sp->study_program }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="text-2xl sm:text-3xl font-bold text-blue-700 text-center">
                    {{ isset($respondedPerStudy) && request('study_program') ? ($respondedPerStudy[request('study_program')] ?? 0) : (isset($respondedPerStudy) ? array_sum($respondedPerStudy) : 0) }}
                </div>
            </div>

            <!-- Rata-rata Pendapatan -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow p-3 sm:p-4 flex flex-col gap-2">
                <div class="font-semibold mb-1 sm:mb-2 text-sm sm:text-base text-blue-800">Rata-rata Pendapatan Alumni</div>
                
                <form method="GET" class="mb-2">
                    <div class="flex flex-col gap-2">
                        <label for="study_program_salary" class="text-xs sm:text-sm text-gray-700">Program Studi:</label>
                        <select name="study_program_salary" id="study_program_salary" 
                            class="border border-gray-300 rounded px-2 py-1 sm:px-3 sm:py-2 text-xs sm:text-sm focus:ring-blue-500 focus:border-blue-500 transition w-full" 
                            onchange="this.form.submit()">
                            <option value="">Semua Program Studi</option>
                            @foreach($studyPrograms as $sp)
                                <option value="{{ $sp->id_study }}" {{ request('study_program_salary') == $sp->id_study ? 'selected' : '' }}>
                                    {{ $sp->study_program }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-700 text-center break-words">
                    @php
                        $salary = 0;
                        if (isset($salaryPerStudy)) {
                            if (request('study_program_salary')) {
                                $salary = round($salaryPerStudy[request('study_program_salary')] ?? 0);
                            } else {
                                $salary = count($salaryPerStudy) ? round(array_sum($salaryPerStudy) / count($salaryPerStudy)) : 0;
                            }
                        }
                    @endphp
                    Rp {{ number_format($salary, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bar Chart Status Alumni
    const ctx = document.getElementById('statistikChart').getContext('2d');
    
    // Data untuk chart
    const chartData = [
        {{ $statisticData['bekerja'] ?? 0 }},
        {{ $statisticData['tidak bekerja'] ?? 0 }},
        {{ $statisticData['melanjutkan studi'] ?? 0 }},
        {{ $statisticData['berwiraswasta'] ?? 0 }},
        {{ $statisticData['sedang mencari kerja'] ?? 0 }}
    ];
    
    // Hitung nilai maksimum untuk menentukan skala Y yang proporsional
    const maxValue = Math.max(...chartData);
    const suggestedMax = maxValue > 0 ? Math.ceil(maxValue * 1.2) : 10; // Tambah 20% dari nilai maksimum
    
    // Tentukan step size yang dinamis berdasarkan range data
    let stepSize = 1;
    if (suggestedMax > 100) {
        stepSize = 10;
    } else if (suggestedMax > 50) {
        stepSize = 5;
    } else if (suggestedMax > 20) {
        stepSize = 2;
    }
    
    const data = {
        labels: [
            'Bekerja',
            'Tidak Bekerja',
            'Melanjutkan Studi',
            'Berwiraswasta',
            'Sedang Mencari Kerja'
        ],
        datasets: [{
            label: 'Jumlah Alumni',
            data: chartData,
            backgroundColor: [
                '#2563eb', // biru
                '#f59e42', // orange
                '#38bdf8', // sky
                '#fbbf24', // kuning
                '#ef4444', // merah
            ],
            borderRadius: 8,
        }]
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    suggestedMax: suggestedMax, // Skala maksimum yang dinamis
                    ticks: { 
                        stepSize: stepSize, // Step yang dinamis
                        precision: 0,
                        font: {
                            size: window.innerWidth < 640 ? 10 : 12
                        },
                        // Hanya tampilkan nilai bulat
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: window.innerWidth < 640 ? 8 : 10
                        }
                    }
                }
            }
        }
    });

    // Pie Chart Tahun Lulus Alumni (tidak diubah)
    const graduationYearPieCtx = document.getElementById('graduationYearPieChart').getContext('2d');
    const graduationYearLabels = {!! json_encode(array_keys($graduationYearStatisticData ?? [])) !!};
    const graduationYearValues = {!! json_encode(array_values($graduationYearStatisticData ?? [])) !!};
    const graduationYearColors = [
        '#2563eb', '#f59e42', '#38bdf8', '#fbbf24', '#ef4444', '#34d399', '#a78bfa', '#f472b6', '#f87171', '#60a5fa'
    ];
    
    new Chart(graduationYearPieCtx, {
        type: 'pie',
        data: {
            labels: graduationYearLabels,
            datasets: [{
                data: graduationYearValues,
                backgroundColor: graduationYearColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: {
                            size: window.innerWidth < 640 ? 10 : 12
                        },
                        padding: window.innerWidth < 640 ? 10 : 15
                    }
                }
            }
        }
    });
});
</script>
@endpush
