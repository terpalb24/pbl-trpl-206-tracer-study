<div class="bg-blue-100 p-6 rounded-2xl shadow">
    <div class="font-bold mb-4">Statistik Status Alumni</div>
    <canvas id="statistikChart" height="100"></canvas>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>
@endpush
