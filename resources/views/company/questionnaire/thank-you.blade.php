@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar --}}
    @include('components.company.sidebar')

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto transition-all duration-300 lg:ml-64 pt-20" id="main-content">

        {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner Selesai'])

        <!-- Content Section -->
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-2xl mx-auto">
                <!-- Success Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 text-center">
                    <!-- Success Icon -->
                    <div class="mb-4 sm:mb-6 text-green-600">
                        <i class="fas fa-check-circle text-5xl sm:text-6xl lg:text-7xl"></i>
                    </div>
                    
                    <!-- Success Message -->
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-gray-800">Terima Kasih!</h2>
                    <p class="text-base sm:text-lg lg:text-xl text-gray-600 mb-4 sm:mb-6 leading-relaxed px-2 sm:px-0">
                        Jawaban Anda telah berhasil disimpan. Terima kasih telah berpartisipasi dalam Tracer Study Polibatam.
                    </p>
                    
                    <!-- Countdown Section -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                        <p class="text-blue-800 mb-2 text-sm sm:text-base">
                            <i class="fas fa-clock mr-2"></i>
                            Anda akan diarahkan kembali ke halaman pilih alumni dalam
                        </p>
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600 transition-transform duration-200" id="countdown">10</div>
                        <p class="text-xs sm:text-sm text-blue-600 mt-1">detik</p>
                        <button id="cancel-redirect" class="mt-2 text-xs sm:text-sm text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded px-2 py-1">
                            Batalkan redirect otomatis
                        </button>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                        <a href="{{ route('company.questionnaire.select-alumni', $id_periode ?? session('company_current_periode_id') ?? 1) }}" 
                           class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 text-sm sm:text-base font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center justify-center">
                            <i class="fas fa-users mr-2"></i> 
                            <span class="hidden sm:inline">Pilih Alumni Lainnya</span>
                            <span class="sm:hidden">Alumni Lain</span>
                        </a>
                        <a href="{{ route('company.questionnaire.index') }}" 
                           class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i> 
                            <span class="hidden sm:inline">Kembali ke Daftar Kuesioner</span>
                            <span class="sm:hidden">Daftar Kuesioner</span>
                        </a>
                        <a href="{{ route('dashboard.company') }}" 
                           class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200 text-sm sm:text-base font-medium focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i> 
                            <span class="hidden sm:inline">Kembali ke Beranda</span>
                            <span class="sm:hidden">Beranda</span>
                        </a>
                    </div>
                </div>

                <!-- Additional Information Card (Optional) -->
                <div class="mt-4 sm:mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 sm:p-6 border border-blue-200">
                    <div class="text-center">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <div class="flex items-center justify-center sm:justify-start">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                <span class="text-sm sm:text-base text-blue-800 font-medium">Informasi Selanjutnya</span>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-blue-700 mt-2 leading-relaxed">
                            Anda dapat melihat ringkasan jawaban di halaman riwayat kuesioner atau melanjutkan mengisi kuesioner untuk alumni lainnya.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Script -->
<script src="{{ asset('js/company.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Countdown functionality
        let countdownTime = 10;
        let countdownActive = true;
        const countdownElement = document.getElementById('countdown');
        const cancelButton = document.getElementById('cancel-redirect');
        
        // Get the periode ID - perbaikan pengambilan ID
        const periodeId = '{{ $id_periode ?? session("company_periode_id") ?? "" }}';
        
        // Jika tidak ada periode ID, ambil dari referrer URL
        let redirectUrl = '';
        if (periodeId) {
            redirectUrl = `{{ url('company/questionnaire') }}/${periodeId}/select-alumni`;
        } else {
            // Fallback: ambil dari document.referrer atau redirect ke index
            const referrer = document.referrer;
            if (referrer && referrer.includes('/company/questionnaire/')) {
                const matches = referrer.match(/\/company\/questionnaire\/(\d+)/);
                if (matches && matches[1]) {
                    redirectUrl = `{{ url('company/questionnaire') }}/${matches[1]}/select-alumni`;
                } else {
                    redirectUrl = '{{ route("company.questionnaire.index") }}';
                }
            } else {
                redirectUrl = '{{ route("company.questionnaire.index") }}';
            }
        }
        
        // Update countdown display
        function updateCountdown() {
            if (countdownElement) {
                countdownElement.textContent = countdownTime;
            }
            
            if (countdownTime <= 0 && countdownActive) {
                // Redirect to select-alumni page or fallback
                window.location.href = redirectUrl;
            } else if (countdownActive) {
                countdownTime--;
                setTimeout(updateCountdown, 1000);
            }
        }
        
        // Cancel redirect functionality
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                countdownActive = false;
                const countdownSection = this.closest('.bg-blue-50');
                if (countdownSection) {
                    countdownSection.innerHTML = `
                        <div class="flex items-center justify-center">
                            <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                            <p class="text-gray-600 text-sm sm:text-base">Redirect otomatis dibatalkan</p>
                        </div>
                    `;
                }
            });
        }
        
        // Start countdown
        updateCountdown();
        
        // Visual enhancement - pulse effect for countdown
        const pulseInterval = setInterval(function() {
            if (countdownActive && countdownTime > 0 && countdownElement) {
                countdownElement.classList.add('scale-110');
                setTimeout(() => {
                    countdownElement.classList.remove('scale-110');
                }, 200);
            } else if (!countdownActive || countdownTime <= 0) {
                clearInterval(pulseInterval);
            }
        }, 1000);
    });
</script>

<style>
    .scale-110 {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }
    
    /* Enhanced responsive animations */
    @media (prefers-reduced-motion: reduce) {
        .scale-110 {
            transform: none;
        }
        .transition-transform {
            transition: none;
        }
    }
</style>
@endsection
