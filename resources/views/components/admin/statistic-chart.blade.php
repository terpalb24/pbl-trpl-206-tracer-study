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
            <div class="flex items-center justify-between mb-4">
                <div class="text-blue-800 font-semibold text-center text-base sm:text-lg flex-1">Status Alumni</div>
                <!-- Download buttons -->
                <div class="flex gap-1 ml-2">
                    <button onclick="downloadChart('statistikChart', 'png')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download PNG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('statistikChart', 'jpeg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download JPEG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('statistikChart', 'svg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download SVG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChartData('statistikChart', 'csv', ['Bekerja', 'Tidak Bekerja', 'Melanjutkan Studi', 'Berwiraswasta', 'Sedang Mencari Kerja'], [{{ $statisticData['bekerja'] ?? 0 }}, {{ $statisticData['tidak bekerja'] ?? 0 }}, {{ $statisticData['melanjutkan studi'] ?? 0 }}, {{ $statisticData['berwiraswasta'] ?? 0 }}, {{ $statisticData['sedang mencari kerja'] ?? 0 }}])" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download CSV">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="downloadChartData('statistikChart', 'xlsx', ['Bekerja', 'Tidak Bekerja', 'Melanjutkan Studi', 'Berwiraswasta', 'Sedang Mencari Kerja'], [{{ $statisticData['bekerja'] ?? 0 }}, {{ $statisticData['tidak bekerja'] ?? 0 }}, {{ $statisticData['melanjutkan studi'] ?? 0 }}, {{ $statisticData['berwiraswasta'] ?? 0 }}, {{ $statisticData['sedang mencari kerja'] ?? 0 }}])" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download Excel">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="printChart('statistikChart', 'Status Alumni')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Print Chart">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 14H4v-3h1v3zm1 0v2h6v-2H6zm0-1h6V9H6v4z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-full h-64 sm:h-72 lg:h-80">
                <canvas id="statistikChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Chart Distribusi Tahun Lulus -->
        <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="text-blue-800 font-semibold text-center text-base sm:text-lg flex-1">Distribusi Tahun Lulus Alumni</div>
                <!-- Download buttons -->
                <div class="flex gap-1 ml-2">
                    <button onclick="downloadChart('graduationYearPieChart', 'png')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download PNG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('graduationYearPieChart', 'jpeg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download JPEG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('graduationYearPieChart', 'svg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download SVG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </button>
                    <button onclick="downloadGraduationYearData('csv')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download CSV">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="downloadGraduationYearData('xlsx')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download Excel">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="printChart('graduationYearPieChart', 'Distribusi Tahun Lulus Alumni')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Print Chart">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 14H4v-3h1v3zm1 0v2h6v-2H6zm0-1h6V9H6v4z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
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

        <!-- Diagram Bar Rata-rata Pendapatan Alumni per Range -->
        <div class="bg-white rounded-2xl shadow-md p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="text-blue-800 font-semibold text-center text-base sm:text-lg flex-1">Distribusi Rentang Gaji Alumni</div>
                <!-- Download buttons -->
                <div class="flex gap-1 ml-2">
                    <button onclick="downloadChart('salaryRangeChart', 'png')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download PNG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('salaryRangeChart', 'jpeg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download JPEG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                        </svg>
                    </button>
                    <button onclick="downloadChart('salaryRangeChart', 'svg')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download SVG">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </button>
                    <button onclick="downloadSalaryRangeData('csv')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download CSV">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="downloadSalaryRangeData('xlsx')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Download Excel">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button onclick="printChart('salaryRangeChart', 'Distribusi Rentang Gaji Alumni')" 
                            class="bg-white/80 hover:bg-white p-1 rounded shadow text-xs text-gray-600 hover:text-gray-800" 
                            title="Print Chart">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 14H4v-3h1v3zm1 0v2h6v-2H6zm0-1h6V9H6v4z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-full h-64 sm:h-72 lg:h-80">
                <canvas id="salaryRangeChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Total Alumni Mengisi Kuesioner -->
    <div class="mt-6 bg-white rounded-2xl shadow-md p-4 flex flex-col items-center justify-center">
        <div class="text-blue-800 font-semibold text-base sm:text-lg mb-2">Total Alumni Mengisi Kuesioner</div>
        <div class="text-3xl font-bold text-blue-700">
            {{ $answerCountAlumni ?? 0 }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('global-filter-form');
    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => form.submit());
    });

    // Bar Chart: Status Alumni
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

    // Pie Chart: Tahun Lulus
    const pieCtx = document.getElementById('graduationYearPieChart').getContext('2d');
    const pieLabels = @json(array_keys($graduationYearStatisticData ?? []));
    const pieValues = @json(array_values($graduationYearStatisticData ?? []));
    const pieColors = ['#2563eb', '#f59e42', '#38bdf8', '#fbbf24', '#ef4444', '#34d399', '#a78bfa', '#f472b6', '#f87171', '#60a5fa'];
    
    // Store graduation year data globally for download functions
    window.graduationYearLabels = pieLabels;
    window.graduationYearValues = pieValues;

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

    // Bar Chart: Rentang Gaji
    const salaryCtx = document.getElementById('salaryRangeChart').getContext('2d');
    const salaryLabels = @json(array_keys($salaryRanges ?? []));
    const salaryValues = @json(array_values($salaryRanges ?? []));
    
    // Store salary data globally for download functions
    window.salaryRangeLabels = salaryLabels;
    window.salaryRangeValues = salaryValues;

    new Chart(salaryCtx, {
        type: 'bar',
        data: {
            labels: salaryLabels,
            datasets: [{
                label: 'Jumlah Alumni',
                data: salaryValues,
                backgroundColor: '#AAD8E6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        callback: val => Number.isInteger(val) ? val : ''
                    }
                },
                x: {
                    ticks: {
                        font: { size: 10 },
                        autoSkip: false
                    }
                }
            }
        }
    });
});

// JavaScript functions for chart download and data export
function downloadChart(chartId, format) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    
    const link = document.createElement('a');
    link.download = `chart_${chartId}_${new Date().toISOString().split('T')[0]}.${format}`;
    
    if (format === 'png' || format === 'jpeg') {
        link.href = canvas.toDataURL(`image/${format}`);
    } else if (format === 'svg') {
        // For SVG, we'll use the PNG as fallback since Chart.js doesn't natively support SVG
        link.href = canvas.toDataURL('image/png');
        link.download = `chart_${chartId}_${new Date().toISOString().split('T')[0]}.png`;
    }
    
    link.click();
}

function downloadChartData(chartId, format, labels, values) {
    const filename = `chart_data_${chartId}_${new Date().toISOString().split('T')[0]}`;
    
    if (format === 'csv') {
        let csvContent = 'Label,Value\n';
        for (let i = 0; i < labels.length; i++) {
            csvContent += `"${labels[i]}",${values[i]}\n`;
        }
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename + '.csv';
        link.click();
    } else if (format === 'xlsx') {
        const data = [];
        data.push(['Label', 'Value']);
        for (let i = 0; i < labels.length; i++) {
            data.push([labels[i], values[i]]);
        }
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Chart Data');
        XLSX.writeFile(wb, filename + '.xlsx');
    }
}

function downloadGraduationYearData(format) {
    const labels = window.graduationYearLabels || [];
    const values = window.graduationYearValues || [];
    
    if (labels.length === 0 || values.length === 0) {
        alert('No data available for download');
        return;
    }
    
    downloadChartData('graduationYearPieChart', format, labels, values);
}

function downloadSalaryRangeData(format) {
    const labels = window.salaryRangeLabels || [];
    const values = window.salaryRangeValues || [];
    
    if (labels.length === 0 || values.length === 0) {
        alert('No data available for download');
        return;
    }
    
    downloadChartData('salaryRangeChart', format, labels, values);
}

function printChart(chartId, chartTitle) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get the chart image as base64
    const chartImage = canvas.toDataURL('image/png');
    
    // Create HTML content for printing
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Chart - ${chartTitle}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    text-align: center;
                }
                .chart-container {
                    max-width: 800px;
                    margin: 0 auto;
                }
                h1 {
                    color: #1e40af;
                    margin-bottom: 20px;
                }
                img {
                    max-width: 100%;
                    height: auto;
                }
                @media print {
                    body { margin: 0; }
                    .chart-container { max-width: 100%; }
                }
            </style>
        </head>
        <body>
            <div class="chart-container">
                <h1>${chartTitle}</h1>
                <img src="${chartImage}" alt="Chart">
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for the image to load before printing
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}
</script>
@endpush
