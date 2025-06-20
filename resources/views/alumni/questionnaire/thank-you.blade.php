@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <x-alumni.header title="Kuesioner Alumni" />

        <!-- Content Section -->
        <div class="p-3 sm:p-4 lg:p-6">
            <div class="bg-white rounded-xl shadow-md p-6 sm:p-8 lg:p-10 text-center max-w-4xl mx-auto">
                <!-- Success Icon -->
                <div class="mb-4 sm:mb-6 text-green-600">
                    <i class="fas fa-check-circle text-4xl sm:text-5xl lg:text-7xl animate-pulse"></i>
                </div>

                <!-- Title -->
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4 text-gray-800">
                    Terima Kasih!
                </h2>

                <!-- Description -->
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 mb-6 sm:mb-8 max-w-2xl mx-auto leading-relaxed">
                    Jawaban Anda telah berhasil disimpan. Terima kasih telah berpartisipasi dalam Tracer Study Polibatam.
                </p>

                <!-- Additional Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8 border border-blue-200">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                        <div class="flex items-center text-blue-600">
                            <i class="fas fa-info-circle mr-2 text-lg sm:text-xl"></i>
                            <span class="text-sm sm:text-base font-medium">Informasi</span>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-sm sm:text-base text-gray-700">
                                Data Anda akan digunakan untuk meningkatkan kualitas pendidikan di Polibatam.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
                    <a href="{{ route('alumni.questionnaire.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-list-alt mr-2"></i>
                        <span>Daftar Kuesioner</span>
                    </a>
                    <a href="{{ route('dashboard.alumni') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-home mr-2"></i>
                        <span>Beranda</span>
                    </a>
                </div>

                <!-- Additional Actions -->
                <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                    <p class="text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4">
                        Ingin melakukan tindakan lainnya?
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
                        <a href="{{ route('alumni.questionnaire.results') }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-chart-line mr-1 sm:mr-2"></i>
                            <span>Lihat Hasil Kuesioner</span>
                        </a>
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <a href="{{ route('alumni.profile.index') }}" 
                           class="inline-flex items-center text-green-600 hover:text-green-800 text-xs sm:text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-user-edit mr-1 sm:mr-2"></i>
                            <span>Update Profil</span>
                        </a>
                    </div>
                </div>

                <!-- Completion Badge -->
                <div class="mt-6 sm:mt-8">
                    <div class="inline-flex items-center px-3 sm:px-4 py-2 bg-green-100 text-green-800 rounded-full text-xs sm:text-sm font-medium">
                        <i class="fas fa-award mr-1 sm:mr-2"></i>
                        <span>Kuesioner Berhasil Diselesaikan</span>
                    </div>
                </div>

                <!-- Thank You Message -->
                <div class="mt-6 sm:mt-8 bg-gray-50 rounded-lg p-3 sm:p-4">
                    <p class="text-xs sm:text-sm text-gray-600 italic">
                        "Kontribusi Anda sangat berharga untuk kemajuan Politeknik Negeri Batam. 
                        Semoga sukses selalu dalam karier Anda!"
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Enhanced JavaScript -->
<script>
    // Enhanced sidebar toggle functionality
    const toggleButton = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    const closeButton = document.getElementById('close-sidebar');

    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            if (sidebar) {
                sidebar.classList.add('hidden');
            }
        });
    }

    // Auto-hide sidebar on outside click for mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 1024 && sidebar && !sidebar.contains(e.target) && !toggleButton.contains(e.target)) {
            sidebar.classList.add('hidden');
        }
    });

    // Celebration animation on load
    window.addEventListener('load', () => {
        const icon = document.querySelector('.fa-check-circle');
        if (icon) {
            icon.style.transform = 'scale(1.1)';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 300);
        }
    });

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
</script>

@endsection
