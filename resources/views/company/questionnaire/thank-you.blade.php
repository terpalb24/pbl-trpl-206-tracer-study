@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
 {{-- Sidebar --}}
    @include('components.company.sidebar')

       <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <main class="flex-grow overflow-y-auto" id="main-content">
        {{-- Header --}}
        @include('components.company.header', ['title' => 'Kuesioner selesai'])
       

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-10 text-center">
                <div class="mb-6 text-green-600">
                    <i class="fas fa-check-circle text-7xl"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">Terima Kasih</h2>
                <p class="text-xl text-gray-600 mb-4">
                    Jawaban Anda telah berhasil disimpan. Terima kasih telah berpartisipasi dalam Tracer Study Polibatam.
                </p>
                
                <!-- Countdown Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-blue-800 mb-2">
                        <i class="fas fa-clock mr-2"></i>
                        Anda akan diarahkan kembali ke halaman pilih alumni dalam
                    </p>
                    <div class="text-3xl font-bold text-blue-600" id="countdown">10</div>
                    <p class="text-sm text-blue-600 mt-1">detik</p>
                    <button id="cancel-redirect" class="mt-2 text-sm text-blue-600 hover:text-blue-800 underline">
                        Batalkan redirect otomatis
                    </button>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('company.questionnaire.select-alumni', $id_periode ?? session('company_current_periode_id') ?? 1) }}" 
                       class="px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-users mr-2"></i> Pilih Alumni Lainnya
                    </a>
                    <a href="{{ route('company.questionnaire.index') }}" 
                       class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kuesioner
                    </a>
                    <a href="{{ route('dashboard.company') }}" 
                       class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });
    
    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });
    
    document.getElementById('profile-toggle')?.addEventListener('click', function() {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });
    
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        
        if (dropdown && toggle && !dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    document.getElementById('logout-btn')?.addEventListener('click', function(event) {
        event.preventDefault();
    
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
    
        const csrfTokenInput = document.createElement('input');
        csrfTokenInput.type = 'hidden';
        csrfTokenInput.name = '_token';
        csrfTokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
        form.appendChild(csrfTokenInput);
        document.body.appendChild(form);
        form.submit();
    });

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
        countdownElement.textContent = countdownTime;
        
        if (countdownTime <= 0 && countdownActive) {
            // Redirect to select-alumni page or fallback
            window.location.href = redirectUrl;
        } else if (countdownActive) {
            countdownTime--;
            setTimeout(updateCountdown, 1000);
        }
    }
    
    // Cancel redirect functionality
    cancelButton.addEventListener('click', function() {
        countdownActive = false;
        const countdownSection = this.closest('.bg-blue-50');
        countdownSection.innerHTML = `
            <p class="text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Redirect otomatis dibatalkan
            </p>
        `;
    });
    
    // Start countdown
    updateCountdown();
    
    // Visual enhancement - pulse effect for countdown
    setInterval(function() {
        if (countdownActive && countdownTime > 0) {
            countdownElement.classList.add('scale-110');
            setTimeout(() => {
                countdownElement.classList.remove('scale-110');
            }, 200);
        }
    }, 1000);
</script>

<style>
    .scale-110 {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }
</style>
@endsection
