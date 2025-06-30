<div class="bg-blue-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow">
    <div class="font-bold mb-6 text-xl sm:text-2xl text-blue-900">Statistik Alumni</div>

    <form method="GET" id="global-filter-form" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Program Studi -->
        <div class="flex flex-col">
            <label for="study_program" class="text-sm font-medium text-gray-700 mb-1">Program Studi</label>
            <select name="study_program" id="study_program"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                <option value="">Semua Program Studi</option>
                @foreach($studyPrograms as $sp)
                    <option value="{{ $sp->id_study }}" {{ request('study_program') == $sp->id_study ? 'selected' : '' }}>
                        {{ $sp->study_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Tahun Lulus -->
        <div class="flex flex-col">
            <label for="graduation_year_filter" class="text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
            <select name="graduation_year_filter" id="graduation_year_filter"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                <option value="">Semua Tahun</option>
                @foreach($allGraduationYears ?? [] as $year)
                    <option value="{{ $year }}" {{ request('graduation_year_filter') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        <!-- Chart Status Alumni -->
        <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col justify-between">
            <div class="text-blue-800 font-semibold text-center text-base sm:text-lg mb-4">Status Alumni</div>
            <div class="w-full h-64 sm:h-72 lg:h-80">
                <canvas id="statistikChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Chart Distribusi Tahun Lulus -->
        <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col justify-between">
            <div class="text-blue-800 font-semibold text-center text-base sm:text-lg mb-4">Distribusi Tahun Lulus Alumni</div>
            <div class="mb-3 text-center">
                <span class="text-sm text-gray-600">Jumlah Alumni:</span>
                <span class="font-bold text-blue-700 text-lg">
                    {{ isset($graduationYearStatisticData) && count($graduationYearStatisticData) ? array_sum($graduationYearStatisticData) : 0 }}
                </span>
            </div>
            <div class="w-full h-48 sm:h-56 lg:h-64 flex items-center justify-center">
                <canvas id="graduationYearPieChart" class="max-w-full max-h-full"></canvas>
            </div>
        </div>

        <!-- Kuesioner & Pendapatan -->
        <div class="flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col">
                <div class="text-blue-800 font-semibold text-base sm:text-lg mb-2">Alumni Mengisi Kuesioner</div>
                <div class="text-3xl font-bold text-blue-700 text-center">
                    {{ isset($respondedPerStudy) && request('study_program') ? ($respondedPerStudy[request('study_program')] ?? 0) : (isset($respondedPerStudy) ? array_sum($respondedPerStudy) : 0) }}
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col">
                <div class="text-blue-800 font-semibold text-base sm:text-lg mb-2">Rata-rata Pendapatan Alumni</div>
                <div class="text-2xl font-bold text-green-700 text-center break-words">
                    @php
                        $salary = 0;
                        if (isset($salaryPerStudy)) {
                            if (request('study_program')) {
                                $salary = round($salaryPerStudy[request('study_program')] ?? 0);
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
    // Filter auto-submit
    const form = document.getElementById('global-filter-form');
    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => form.submit());
    });

    // Bar Chart
    const barCtx = document.getElementById('statistikChart').getContext('2d');
    const chartData = [
        {{ $statisticData['bekerja'] ?? 0 }},
        {{ $statisticData['tidak bekerja'] ?? 0 }},
        {{ $statisticData['melanjutkan studi'] ?? 0 }},
        {{ $statisticData['berwiraswasta'] ?? 0 }},
        {{ $statisticData['sedang mencari kerja'] ?? 0 }}
    ];
    const maxValue = Math.max(...chartData);
    const suggestedMax = maxValue > 0 ? Math.ceil(maxValue * 1.2) : 10;
    let stepSize = 1;
    if (suggestedMax > 100) stepSize = 10;
    else if (suggestedMax > 50) stepSize = 5;
    else if (suggestedMax > 20) stepSize = 2;

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Bekerja', 'Tidak Bekerja', 'Melanjutkan Studi', 'Berwiraswasta', 'Sedang Mencari Kerja'],
            datasets: [{
                label: 'Jumlah Alumni',
                data: chartData,
                backgroundColor: ['#2563eb', '#f59e42', '#38bdf8', '#fbbf24', '#ef4444'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax,
                    ticks: {
                        stepSize,
                        precision: 0,
                        callback: val => Number.isInteger(val) ? val : '',
                        font: { size: window.innerWidth < 640 ? 10 : 12 }
                    }
                },
                x: {
                    ticks: {
                        font: { size: window.innerWidth < 640 ? 8 : 10 }
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('graduationYearPieChart').getContext('2d');
    const pieLabels = {!! json_encode(array_keys($graduationYearStatisticData ?? [])) !!};
    const pieValues = {!! json_encode(array_values($graduationYearStatisticData ?? [])) !!};
    const pieColors = ['#2563eb', '#f59e42', '#38bdf8', '#fbbf24', '#ef4444', '#34d399', '#a78bfa', '#f472b6', '#f87171', '#60a5fa'];

    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieValues,
                backgroundColor: pieColors,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: window.innerWidth < 640 ? 10 : 12 },
                        padding: window.innerWidth < 640 ? 10 : 15
                    }
                }
            }
        }
    });
});
</script>
@endpush
