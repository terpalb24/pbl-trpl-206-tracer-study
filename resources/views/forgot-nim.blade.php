@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-100 to-blue-200 px-4 py-10">
    <div class="w-full max-w-5xl mx-auto">
        <!-- Logo dan Judul -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/images/polteklogo.png') }}" alt="Logo Polibatam" class="h-20 mx-auto mb-4">
            <h2 class="text-3xl font-bold text-sky-800 mb-2">Tracer Study</h2>
            <p class="text-sky-700">Politeknik Negeri Batam</p>
        </div>

        <!-- Card Utama -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-sky-200">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-sky-400 to-blue-500 px-6 py-4">
                <h3 class="text-xl font-semibold text-white mb-1">Daftar NIM & Nama Alumni</h3>
                <p class="text-sky-100 text-sm">Cari NIM atau nama alumni yang terdaftar di sistem</p>
            </div>

            <!-- Form Pencarian -->
            <div class="p-6 border-b border-sky-100 bg-gradient-to-b from-sky-50 to-white">
                <form method="GET" action="{{ route('forgot-nim') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sky-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="w-full pl-10 pr-4 py-2 border border-sky-200 rounded-lg focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-colors bg-white text-sky-800 placeholder-sky-400"
                               placeholder="Masukkan NIM atau Nama Alumni..." 
                               value="{{ request('search') }}">
                    </div>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2 bg-sky-500 hover:bg-sky-600 text-white font-medium rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-search"></i>
                        <span>Cari Alumni</span>
                    </button>
                </form>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-sky-50 text-sky-700 uppercase text-xs tracking-wider">
                            <th class="px-6 py-3 text-left font-semibold">NIM</th>
                            <th class="px-6 py-3 text-left font-semibold">Nama</th>
                        </tr>
                    </thead>
                <tbody class="divide-y divide-sky-100">
                    @forelse($alumni as $alumnus)
                    <tr class="hover:bg-sky-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full font-mono text-sm font-medium bg-sky-100 text-sky-700 border border-sky-200">
                                {{ $alumnus->nim }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-sky-800">{{ $alumnus->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-sky-400">
                                <i class="fas fa-search text-4xl mb-3 text-sky-300"></i>
                                <p class="text-sm font-medium">Tidak ada data yang ditemukan</p>
                                <p class="text-xs text-sky-400/80 mt-1">Coba gunakan kata kunci pencarian yang berbeda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($alumni->hasPages())
        <div class="px-6 py-4 bg-gradient-to-t from-sky-50 to-white border-t border-sky-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-sky-600">
                    Menampilkan {{ $alumni->firstItem() ?? 0 }} - {{ $alumni->lastItem() ?? 0 }}
                    dari {{ $alumni->total() }} alumni
                </p>
                <div class="flex justify-center sm:justify-end">
                    <nav class="flex items-center space-x-1" aria-label="Pagination">
                        {{-- Previous Page --}}
                        @if ($alumni->onFirstPage())
                            <span class="px-3 py-2 text-sm text-sky-400 bg-sky-50 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $alumni->previousPageUrl() }}" 
                               class="px-3 py-2 text-sm text-sky-600 hover:bg-sky-100 rounded-lg transition duration-200">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($alumni->getUrlRange(max(1, $alumni->currentPage() - 2), min($alumni->lastPage(), $alumni->currentPage() + 2)) as $page => $url)
                            @if ($page == $alumni->currentPage())
                                <span class="px-3 py-2 text-sm text-white bg-sky-500 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" 
                                   class="px-3 py-2 text-sm text-sky-600 hover:bg-sky-100 rounded-lg transition duration-200">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page --}}
                        @if ($alumni->hasMorePages())
                            <a href="{{ $alumni->nextPageUrl() }}" 
                               class="px-3 py-2 text-sm text-sky-600 hover:bg-sky-100 rounded-lg transition duration-200">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm text-sky-400 bg-sky-50 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="p-6 bg-gradient-to-b from-white to-sky-50 border-t border-sky-100">
            <div class="flex justify-center">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center px-6 py-2.5 rounded-lg text-sky-600 hover:text-sky-700 hover:bg-sky-100 transition duration-200 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
