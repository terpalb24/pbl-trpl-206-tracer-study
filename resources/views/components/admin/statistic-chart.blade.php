<div class="bg-blue-100 p-6 rounded-2xl shadow">
    <div class="font-bold mb-6 text-xl text-blue-900">Statistik Alumni</div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        <!-- Bar Chart: Status Alumni -->
        <div class="bg-white rounded-xl shadow flex flex-col items-center justify-center p-6">
            <div class="font-semibold mb-4 text-blue-800">Status Alumni</div>
            <canvas id="statistikChart" height="120"></canvas>
        </div>
        <!-- Pie Chart: Distribusi Tahun Lulus Alumni -->
        <div class="bg-white rounded-xl shadow flex flex-col items-center justify-center p-6">
            <div class="font-semibold mb-4 text-blue-800">Distribusi Tahun Lulus Alumni</div>
            <!-- Tambahkan filter tahun lulus -->
            <form method="GET" id="graduation-year-filter-form" class="mb-3 flex items-center gap-2">
                <label for="graduation_year_filter" class="text-sm text-gray-700">Tahun Lulus:</label>
                <select name="graduation_year_filter" id="graduation_year_filter" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 transition"
                    onchange="document.getElementById('graduation-year-filter-form').submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($allGraduationYears ?? [] as $year)
                        <option value="{{ $year }}" {{ (isset($filterGraduationYear) && $filterGraduationYear == $year) ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </form>
            <!-- Tampilkan jumlah alumni total untuk tahun yang dipilih -->
            <div class="mb-2 text-center">
                <span class="text-sm text-gray-700">
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
            <canvas id="graduationYearPieChart" height="220" width="220" style="max-width:320px;max-height:320px;"></canvas>
        </div>
        <!-- Statistik: Jumlah Alumni Mengisi Kuesioner & Rata-rata Pendapatan per Prodi -->
        <div class="flex flex-col gap-6 justify-between h-full">
            <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                <div class="font-semibold mb-1 text-blue-800">Jumlah Alumni Mengisi Kuesioner</div>
                <form method="GET" class="flex items-center gap-2 mb-2">
                    <label for="study_program" class="text-sm text-gray-700">Program Studi:</label>
                    <select name="study_program" id="study_program" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 transition w-full" onchange="this.form.submit()">
                        <option value="">Semua Program Studi</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp->id_study }}" {{ request('study_program') == $sp->id_study ? 'selected' : '' }}>
                                {{ $sp->study_program }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <div class="text-3xl font-bold text-blue-700 text-center">
                    {{ isset($respondedPerStudy) && request('study_program') ? ($respondedPerStudy[request('study_program')] ?? 0) : (isset($respondedPerStudy) ? array_sum($respondedPerStudy) : 0) }}
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                <div class="font-semibold mb-1 text-blue-800">Rata-rata Pendapatan Alumni</div>
                <form method="GET" class="flex items-center gap-2 mb-2">
                    <label for="study_program_salary" class="text-sm text-gray-700">Program Studi:</label>
                    <select name="study_program_salary" id="study_program_salary" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 transition w-full" onchange="this.form.submit()">
                        <option value="">Semua Program Studi</option>
                        @foreach($studyPrograms as $sp)
                            <option value="{{ $sp->id_study }}" {{ request('study_program_salary') == $sp->id_study ? 'selected' : '' }}>
                                {{ $sp->study_program }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <div class="text-3xl font-bold text-green-700 text-center">
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
            data: [
                {{ $statisticData['bekerja'] ?? 0 }},
                {{ $statisticData['tidak bekerja'] ?? 0 }},
                {{ $statisticData['melanjutkan studi'] ?? 0 }},
                {{ $statisticData['berwiraswasta'] ?? 0 }},
                {{ $statisticData['sedang mencari kerja'] ?? 0 }}
            ],
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
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } }
            }
        }
    });

    // Pie Chart Tahun Lulus Alumni
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
            responsive: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
