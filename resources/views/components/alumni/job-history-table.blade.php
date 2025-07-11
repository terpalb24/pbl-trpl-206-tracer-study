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
                            <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline job-history-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition-colors duration-200 delete-job-history-btn"
                                    title="Hapus"
                                    data-company="{{ $jobHistory->company_name }}"
                                    data-position="{{ $jobHistory->position }}"
                                    data-start="{{ \Carbon\Carbon::parse($jobHistory->start_date)->format('M Y') }}"
                                    data-end="{{ $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('M Y') : 'Sekarang' }}">
                                    <i class="fas fa-trash text-xs sm:text-sm"></i>
                                </button>
                            </form>
                            <button type="button"
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors duration-200 detail-btn"
                                title="Detail"
                                data-job-id="{{ $jobHistory->id_jobhistory }}"
                                onclick="event.preventDefault(); event.stopPropagation(); console.log('Desktop button clicked for ID:', {{ $jobHistory->id_jobhistory }}); showJobHistoryDetail({{ $jobHistory->id_jobhistory }}); return false;">
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
                    <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline job-history-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 delete-job-history-btn"
                            title="Hapus"
                            data-company="{{ $jobHistory->company_name }}"
                            data-position="{{ $jobHistory->position }}"
                            data-start="{{ \Carbon\Carbon::parse($jobHistory->start_date)->format('M Y') }}"
                            data-end="{{ $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->format('M Y') : 'Sekarang' }}">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                    <button type="button"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 detail-btn"
                        title="Detail"
                        data-job-id="{{ $jobHistory->id_jobhistory }}"
                        onclick="event.preventDefault(); event.stopPropagation(); console.log('Mobile button clicked for ID:', {{ $jobHistory->id_jobhistory }}); showJobHistoryDetail({{ $jobHistory->id_jobhistory }}); return false;">
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

<!-- Modal Detail untuk semua job histories -->
@if($jobHistories->count() > 0)
    @foreach($jobHistories as $jobHistory)
        <div id="modal-detail-{{ $jobHistory->id_jobhistory }}" class="fixed inset-0 z-50 hidden" style="display: none; opacity: 0; transition: opacity 0.3s ease-in-out; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(6px);">
            <!-- Perfect center container with flexbox -->
            <div class="flex items-center justify-center min-h-screen min-w-full p-4" style="min-height: 100vh; min-width: 100vw;">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-xs sm:max-w-md lg:max-w-lg xl:max-w-xl mx-auto my-auto relative transform transition-all duration-300 ease-out" style="max-height: 85vh; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" id="modal-content-{{ $jobHistory->id_jobhistory }}">
                    <div class="overflow-y-auto" style="max-height: 85vh;">
                        <div class="p-6 sm:p-8">
                            <!-- Close button -->
                            <button onclick="closeJobHistoryDetail({{ $jobHistory->id_jobhistory }})" 
                                    class="absolute top-3 right-3 sm:top-4 sm:right-4 text-gray-400 hover:text-red-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full z-10 group">
                                <i class="fas fa-times text-lg sm:text-xl group-hover:scale-110 transition-transform duration-200"></i>
                            </button>
                            
                            <!-- Header with icon -->
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full mb-4 shadow-sm">
                                    <i class="fas fa-briefcase text-blue-600 text-2xl"></i>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Detail Riwayat Kerja</h2>
                                <div class="w-16 h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full mx-auto"></div>
                            </div>
                            <!-- Content -->
                            <div class="space-y-6">
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                                    <div class="grid grid-cols-1 gap-6">
                                        <div class="space-y-1">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                <i class="fas fa-building mr-2 text-gray-400"></i>Nama Perusahaan
                                            </span>
                                            <div class="text-base text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg shadow-sm">
                                                {{ $jobHistory->company->company_name ?? 'Tidak tersedia' }}
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>Posisi
                                            </span>
                                            <div class="text-base text-gray-900 bg-white px-3 py-2 rounded-lg shadow-sm">
                                                {{ $jobHistory->position }}
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>Gaji
                                            </span>
                                            <div class="text-lg text-green-600 font-bold bg-green-50 px-3 py-2 rounded-lg shadow-sm border border-green-200">
                                                @php
                                                    $salaryRanges = [
                                                        '3000000' => '3.000.000 - 4.500.000',
                                                        '4500000' => '4.500.000 - 5.000.000',
                                                        '5000000' => '5.000.000 - 5.500.000',
                                                        '6000000' => '6.000.000 - 6.500.000',
                                                        '6500000' => '6.500.000 - 7.000.000',
                                                        '7000000' => '7.000.000 - 8.000.000',
                                                        '8000000' => '8.000.000 - 9.000.000',
                                                        '9000000' => '9.000.000 - 10.000.000',
                                                        '10000000' => '10.000.000 - 12.000.000',
                                                        '12000000' => '12.000.000 - 15.000.000',
                                                        '15000000' => '15.000.000 - 20.000.000',
                                                        '20000000' => '> 20.000.000'
                                                    ];
                                                    $formattedSalary = $salaryRanges[$jobHistory->salary] ?? ($jobHistory->salary ? 'Rp ' . number_format($jobHistory->salary, 0, ',', '.') : '-');
                                                @endphp
                                                {{ $formattedSalary }}
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                <i class="fas fa-clock mr-2 text-gray-400"></i>Durasi
                                            </span>
                                            <div class="text-base text-gray-900 bg-white px-3 py-2 rounded-lg shadow-sm">
                                                {{ $jobHistory->duration }}
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Periode Bekerja
                                            </span>
                                            <div class="bg-white px-3 py-3 rounded-lg shadow-sm">
                                                @php
                                                    $start = $jobHistory->start_date ? \Carbon\Carbon::parse($jobHistory->start_date)->translatedFormat('F Y') : '-';
                                                    $end = $jobHistory->end_date ? \Carbon\Carbon::parse($jobHistory->end_date)->translatedFormat('F Y') : 'Sekarang';
                                                @endphp
                                                <div class="flex items-center justify-center flex-wrap gap-2">
                                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                                                        <i class="fas fa-play mr-1 text-xs"></i>{{ $start }}
                                                    </span>
                                                    <i class="fas fa-arrow-right text-gray-400 mx-1"></i>
                                                    <span class="bg-{{ $jobHistory->end_date ? 'gray' : 'green' }}-100 text-{{ $jobHistory->end_date ? 'gray' : 'green' }}-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                                                        <i class="fas fa-{{ $jobHistory->end_date ? 'stop' : 'circle' }} mr-1 text-xs"></i>{{ $end }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer with close button -->
                                <div class="flex justify-center pt-4 border-t border-gray-200">
                                    <button onclick="closeJobHistoryDetail({{ $jobHistory->id_jobhistory }})" 
                                            class="px-8 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                                        <i class="fas fa-times mr-2"></i>
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<script>
// Make functions global with unique names to avoid conflicts
window.showJobHistoryDetail = function(id) {
    console.log('=== DEBUGGING JOB HISTORY MODAL ===');
    console.log('Attempting to show modal with ID:', 'modal-detail-' + id);
    console.log('Current page location:', window.location.href);
    
    // Close any other open modals first
    const allModals = document.querySelectorAll('[id^="modal-detail-"]');
    allModals.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        modal.style.opacity = '0';
    });
    
    const modal = document.getElementById('modal-detail-' + id);
    const modalContent = document.getElementById('modal-content-' + id);
    console.log('Modal element found:', modal);
    
    if (modal && modalContent) {
        console.log('Modal found, showing...');
        console.log('Modal current display:', window.getComputedStyle(modal).display);
        
        // Set initial styles for animation
        modal.style.display = 'flex';
        modal.style.opacity = '0';
        modalContent.style.transform = 'scale(0.8) translateY(20px)';
        modalContent.style.opacity = '0';
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Force reflow
        modal.offsetHeight;
        
        // Animate in
        requestAnimationFrame(() => {
            modal.style.transition = 'opacity 0.3s ease-out';
            modalContent.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            
            modal.style.opacity = '1';
            modalContent.style.transform = 'scale(1) translateY(0)';
            modalContent.style.opacity = '1';
        });
        
        console.log('After show - Modal display:', window.getComputedStyle(modal).display);
        console.log('After show - Modal classes:', modal.className);
        
    } else {
        console.error('Modal or modal content not found with ID:', 'modal-detail-' + id);
        // Debug: List all available modals
        const allModals = document.querySelectorAll('[id^="modal-detail-"]');
        console.log('Available modals:', Array.from(allModals).map(m => m.id));
        console.log('All modal elements:', allModals);
        
        // Show error to user
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: 'Detail riwayat kerja tidak dapat ditampilkan. Silakan refresh halaman.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        } else {
            alert('Detail riwayat kerja tidak dapat ditampilkan. Silakan refresh halaman.');
        }
    }
};

window.closeJobHistoryDetail = function(id) {
    console.log('Closing modal with ID:', 'modal-detail-' + id);
    const modal = document.getElementById('modal-detail-' + id);
    const modalContent = document.getElementById('modal-content-' + id);
    
    if (modal && modalContent) {
        // Animate out
        modal.style.transition = 'opacity 0.2s ease-in';
        modalContent.style.transition = 'all 0.2s ease-in';
        
        modal.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9) translateY(-10px)';
        modalContent.style.opacity = '0';
        
        setTimeout(() => {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            console.log('Modal closed successfully');
        }, 200);
    }
};

// Backward compatibility
window.showDetail = window.showJobHistoryDetail;
window.closeDetail = window.closeJobHistoryDetail;

// Enhanced DOM ready handling
function initJobHistoryModals() {
    console.log('Initializing Job History Modals...');
    console.log('Available modals on init:', document.querySelectorAll('[id^="modal-detail-"]').length);
    
    // Alternative event listener approach for buttons
    document.addEventListener('click', function(e) {
        // Handle detail buttons
        if (e.target.closest('.detail-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.detail-btn');
            const jobId = btn.getAttribute('data-job-id');
            console.log('Detail button clicked via event listener, ID:', jobId);
            if (jobId) {
                showJobHistoryDetail(jobId);
            }
            return false;
        }
        
        // Handle backdrop clicks (clicking outside modal content)
        if (e.target.id && e.target.id.startsWith('modal-detail-')) {
            const modalId = e.target.id.replace('modal-detail-', '');
            console.log('Backdrop clicked, closing modal:', modalId);
            closeJobHistoryDetail(modalId);
        }
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('Escape key pressed, closing all modals');
            const openModals = document.querySelectorAll('[id^="modal-detail-"]:not(.hidden)');
            openModals.forEach(modal => {
                const modalId = modal.id.replace('modal-detail-', '');
                closeJobHistoryDetail(modalId);
            });
        }
    });
}

// Multiple initialization attempts
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initJobHistoryModals);
} else {
    initJobHistoryModals();
}

// Fallback initialization
setTimeout(initJobHistoryModals, 100);
setTimeout(initJobHistoryModals, 500);
setTimeout(initJobHistoryModals, 1000);

// Handle job history delete with SweetAlert2
document.addEventListener('click', function(e) {
    const deleteBtn = e.target.closest('.delete-job-history-btn');
    if (deleteBtn) {
        e.preventDefault();
        console.log('Delete button clicked');
        
        const form = deleteBtn.closest('.job-history-delete-form');
        const company = deleteBtn.dataset.company || 'N/A';
        const position = deleteBtn.dataset.position || 'N/A';
        const startDate = deleteBtn.dataset.start || 'N/A';
        const endDate = deleteBtn.dataset.end || 'N/A';
        
        Swal.fire({
            title: 'Konfirmasi Hapus Riwayat Kerja',
            html: `
                <div class="text-left">
                    <p class="mb-3 text-gray-600">Apakah Anda yakin ingin menghapus riwayat kerja berikut?</p>
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Perusahaan:</span>
                                <span class="text-gray-900 font-semibold">${company}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Posisi:</span>
                                <span class="text-gray-900">${position}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Periode:</span>
                                <span class="text-gray-900">${startDate} - ${endDate}</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-red-600 font-medium">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
            reverseButtons: true,
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'text-lg font-semibold',
                content: 'text-sm'
            },
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang memproses penghapusan riwayat kerja',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                if (form) {
                    form.submit();
                }
            }
        });
    }
});
</script>
