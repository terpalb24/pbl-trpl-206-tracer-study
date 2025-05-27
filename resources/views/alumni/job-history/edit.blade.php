@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Sidebar (same as index) -->
    <div class="fixed inset-y-0 left-0 w-64 bg-blue-900">
        <div class="flex items-center px-6 py-4">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3"></div>
                <div class="text-white">
                    <div class="font-semibold">Tracer Study</div>
                    <div class="text-sm">Polibatam</div>
                </div>
            </div>
        </div>
        
        <nav class="mt-8">
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-blue-800">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L2 12.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-4.586l.293.293a1 1 0 001.414-1.414l-9-9z"></path>
                </svg>
                Beranda
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-blue-800">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
                Kuisioner
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white bg-blue-800 border-r-4 border-white">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                Riwayat
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-blue-800">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                Profil
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center">
                    <button class="mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Edit Riwayat Kerja</h1>
                </div>
                
                <div class="flex items-center">
                    <div class="relative">
                        <button class="flex items-center px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800">
                            <img src="https://ui-avatars.com/api/?name=Andri&background=3b82f6&color=fff" alt="Avatar" class="w-8 h-8 rounded-full mr-2">
                            <div class="text-left">
                                <div class="font-medium">Andri</div>
                                <div class="text-sm opacity-75">Alumni</div>
                            </div>
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <form action="{{ route('alumni.job-history.update', $jobHistory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan</label>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   placeholder="e.g PT Budak Korporat"
                                   value="{{ old('company_name', $jobHistory->company_name) }}"
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_name') border-red-500 @enderror">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
                            <input type="text" 
                                   id="position" 
                                   name="position" 
                                   placeholder="e.g Fullstack Developer"
                                   value="{{ old('position', $jobHistory->position) }}"
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">Gaji</label>
                            <input type="number" 
                                   id="salary" 
                                   name="salary" 
                                   placeholder="e.g 100.000.000"
                                   value="{{ old('salary', $jobHistory->salary) }}"
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('salary') border-red-500 @enderror">
                            @error('salary')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Durasi</label>
                            <input type="text" 
                                   id="duration" 
                                   name="duration" 
                                   placeholder="e.g 10 Tahun"
                                   value="{{ old('duration', $jobHistory->duration) }}"
                                   class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('duration') border-red-500 @enderror">
                            @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <a href="{{ route('alumni.job-history.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection