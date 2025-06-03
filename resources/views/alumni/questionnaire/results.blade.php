@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
   {{-- Sidebar Komponen --}}
    <x-alumni.sidebar class="lg:block hidden" />

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
    <!-- Header -->
    
    <x-alumni.header title="Kuesioner" />
        <!-- Content Section -->
        <div class="p-6">
            @if($userAnswers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-clipboard-list text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Belum Ada Riwayat</h2>
                    <p class="text-gray-600">
                        Anda belum mengisi kuesioner apapun saat ini.
                    </p>
                    <a href="{{ route('alumni.questionnaire.index') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Lihat Kuesioner
                    </a>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Kuesioner Yang Telah Anda Isi</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Periode</th>
                                    <th class="py-3 px-4 text-left">Tanggal Pengisian</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($userAnswers as $userAnswer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $userAnswer->periode->periode_name }}</td>
                                        <td class="py-3 px-4">{{ $userAnswer->updated_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3 px-4">
                                            @if($userAnswer->status == 'completed')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('alumni.questionnaire.response-detail', [$userAnswer->id_periode, $userAnswer->id_user_answer]) }}" 
                                               class="text-blue-600 hover:underline">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                            @if($userAnswer->periode->status == 'active' && $userAnswer->status == 'draft')
                                                <a href="{{ route('alumni.questionnaire.fill', [$userAnswer->id_periode]) }}" 
                                                   class="text-green-600 hover:underline ml-3">
                                                    <i class="fas fa-edit mr-1"></i> Lanjutkan
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- script JS  -->
<script src="{{ asset('./js/alumni.js') }}"></script>
@endsection