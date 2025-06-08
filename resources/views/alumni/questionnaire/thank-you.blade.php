@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 text-gray-600 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-semibold">Kuesioner Alumni</h1>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <div class="bg-white rounded-xl shadow-md p-10 text-center">
                <div class="mb-6 text-green-600">
                    <i class="fas fa-check-circle text-7xl"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">Terima Kasih</h2>
                <p class="text-xl text-gray-600 mb-8">
                    Jawaban Anda telah berhasil disimpan. Terima kasih telah berpartisipasi dalam Tracer Study Polibatam.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('alumni.questionnaire.index') }}" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-list-alt mr-2"></i> Daftar Kuesioner
                    </a>
                    <a href="{{ route('dashboard.alumni') }}" class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-home mr-2"></i> Beranda
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.add('hidden');
    });
</script>
@endsection
