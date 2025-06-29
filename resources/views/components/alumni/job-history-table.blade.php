@if($jobHistories->count() > 0)
    <!-- Tabel untuk layar sm ke atas -->
    <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200 hidden sm:block">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 sm:py-4 px-2 sm:px-4 font-medium text-gray-600 uppercase text-xs sm:text-sm">NO</th>
                    <th class="text-left py-3 sm:py-4 px-2 sm:px-4 font-medium text-gray-600 uppercase text-xs sm:text-sm">POSISI</th>
                    <th class="text-left py-3 sm:py-4 px-2 sm:px-4 font-medium text-gray-600 uppercase text-xs sm:text-sm hidden sm:table-cell">NAMA PERUSAHAAN</th>
                    <th class="text-left py-3 sm:py-4 px-2 sm:px-4 font-medium text-gray-600 uppercase text-xs sm:text-sm">PERIODE BEKERJA</th>
                    <th class="text-center py-3 sm:py-4 px-2 sm:px-4 font-medium text-gray-600 uppercase text-xs sm:text-sm">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($jobHistories as $index => $jobHistory)
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-sm sm:text-base whitespace-nowrap">{{ $index + 1 }}</td>
                    <td class="py-3 sm:py-4 px-2 sm:px-4 font-medium text-sm sm:text-base">
                        <div class="max-w-xs truncate">{{ $jobHistory->position }}</div>
                        <div class="sm:hidden text-xs text-gray-500 mt-1">
                            {{ $jobHistory->company->company_name ?? '-' }}
                        </div>
                    </td>
                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-sm sm:text-base hidden sm:table-cell">
                        <div class="max-w-xs truncate">{{ $jobHistory->company->company_name ?? '-' }}</div>
                    </td>
                    <td class="py-3 sm:py-4 px-2 sm:px-4 text-sm sm:text-base whitespace-nowrap">
                        @php
                            $start = $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->translatedFormat('F Y') : '-';
                            $end = $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->translatedFormat('F Y') : 'Sekarang';
                        @endphp
                        {{ $start }} - {{ $end }}
                    </td>
                    <td class="py-3 sm:py-4 px-2 sm:px-4">
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                            <a href="{{ route('alumni.job-history.edit', $jobHistory->id_jobhistory) }}"
                               class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700 transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit text-xs sm:text-sm"></i>
                            </a>
                            <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat kerja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200"
                                    title="Hapus">
                                    <i class="fas fa-trash text-xs sm:text-sm"></i>
                                </button>
                            </form>
                            <button type="button"
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors duration-200"
                                title="Detail"
                                onclick="showDetail({{ $jobHistory->id_jobhistory }})">
                                <i class="fas fa-info-circle text-xs sm:text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Card untuk mobile -->
    <div class="sm:hidden space-y-4">
        @foreach($jobHistories as $index => $jobHistory)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-gray-500">#{{ $index + 1 }}</span>
                <div class="flex gap-1">
                    <a href="{{ route('alumni.job-history.edit', $jobHistory->id_jobhistory) }}"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-700"
                       title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat kerja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-700"
                            title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                    <button type="button"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700"
                        title="Detail"
                        onclick="showDetail({{ $jobHistory->id_jobhistory }})">
                        <i class="fas fa-info-circle text-xs"></i>
                    </button>
                </div>
            </div>
            <div class="mb-1">
                <span class="block text-xs text-gray-500">Posisi</span>
                <span class="font-medium text-gray-900">{{ $jobHistory->position }}</span>
            </div>
            <div class="mb-1">
                <span class="block text-xs text-gray-500">Nama Perusahaan</span>
                <span class="text-gray-900">{{ $jobHistory->company->company_name ?? '-' }}</span>
            </div>
            <div class="mb-1">
                <span class="block text-xs text-gray-500">Periode Bekerja</span>
                <span class="text-gray-900">
                    @php
                        $start = $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->translatedFormat('F Y') : '-';
                        $end = $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->translatedFormat('F Y') : 'Sekarang';
                    @endphp
                    {{ $start }} - {{ $end }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Button -->
    <div class="flex flex-col sm:flex-row justify-end mt-4 sm:mt-6">
        <a href="{{ route('alumni.job-history.create') }}"
           class="bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors duration-300 text-center text-sm sm:text-base">
            <i class="fas fa-plus mr-1 sm:mr-2"></i>
            <span class="hidden sm:inline">Tambah Riwayat Kerja</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>
@else
    <x-alumni.job-history-empty />
@endif

@foreach($jobHistories as $index => $jobHistory)
    {{-- Modal Detail (letakkan di sini, di luar card/table, cukup satu per data) --}}
    <div id="modal-detail-{{ $jobHistory->id_jobhistory }}" class="fixed inset-0 z-50 hidden flex items-center justify-center backdrop-blur-sm bg-black/50 p-2 sm:p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xs sm:max-w-md lg:max-w-2xl p-4 sm:p-8 relative max-h-screen overflow-y-auto">
            <button onclick="closeDetail({{ $jobHistory->id_jobhistory }})" 
                    class="absolute top-2 right-2 sm:top-4 sm:right-4 text-gray-400 hover:text-red-600 transition-colors duration-200 p-1 sm:p-2">
                <i class="fas fa-times text-lg sm:text-xl"></i>
            </button>
            <h2 class="text-lg sm:text-xl lg:text-2xl font-bold mb-4 sm:mb-6 text-blue-800 text-center">Detail Riwayat Kerja</h2>
            <div class="space-y-4">
                <div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide">Nama Perusahaan</span>
                    <div class="text-sm sm:text-base text-gray-900 mt-1">{{ $jobHistory->company->company_name ?? '-' }}</div>
                </div>
                <div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide">Posisi</span>
                    <div class="text-sm sm:text-base text-gray-900 mt-1">{{ $jobHistory->position }}</div>
                </div>
                <div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide">Gaji</span>
                    <div class="text-sm sm:text-base text-gray-900 mt-1">Rp {{ number_format($jobHistory->salary, 0, ',', '.') }}</div>
                </div>
                <div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide">Durasi</span>
                    <div class="text-sm sm:text-base text-gray-900 mt-1">{{ $jobHistory->duration }}</div>
                </div>
                <div>
                    <span class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide">Periode Bekerja</span>
                    <div class="text-sm sm:text-base text-gray-900 mt-1">
                        @php
                            $start = $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->translatedFormat('F Y') : '-';
                            $end = $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->translatedFormat('F Y') : 'Sekarang';
                        @endphp
                        {{ $start }} - {{ $end }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
function showDetail(id) {
    const modal = document.getElementById('modal-detail-' + id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeDetail(id) {
    const modal = document.getElementById('modal-detail-' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('backdrop-blur-sm')) {
        const modals = document.querySelectorAll('[id^="modal-detail-"]');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = 'auto';
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id^="modal-detail-"]');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = 'auto';
    }
});
</script>
