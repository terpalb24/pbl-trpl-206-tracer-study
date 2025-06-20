@extends('layouts.app')

@php
    $admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<x-layout-admin>
    <x-slot name="sidebar">
        <x-admin.sidebar />
    </x-slot>

    <x-slot name="header">
        <x-admin.header>Edit Kategori</x-admin.header>
        <x-admin.profile-dropdown></x-admin.profile-dropdown>
    </x-slot>

    <!-- Container utama dengan responsive padding -->
    <div class="px-3 sm:px-4 lg:px-6 max-w-7xl mx-auto py-4 sm:py-6">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li><a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" class="text-blue-600 hover:underline">Detail Periode</a></li>
                <li><span class="text-gray-500">/</span></li>
                <li class="text-gray-700">Edit Kategori</li>
            </ol>
        </nav>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-3 rounded mb-4" id="success-alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('success') }}</span>
                    <button type="button" class="ml-auto" onclick="document.getElementById('success-alert').style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-3 rounded mb-4" id="error-alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm sm:text-base">{{ session('error') }}</span>
                    <button type="button" class="ml-auto" onclick="document.getElementById('error-alert').style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md border border-gray-200">
            <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                    Edit Kategori
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Ubah informasi kategori: {{ $category->category_name }}
                </p>
            </div>

            <form action="{{ route('admin.questionnaire.category.update', [$periode->id_periode, $category->id_category]) }}" method="POST" class="px-4 sm:px-6 py-4 sm:py-6">
                @csrf
                @method('PUT')
                
                <!-- Nama Kategori -->
                <div class="mb-4 sm:mb-6">
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-blue-600 mr-1"></i>
                        Nama Kategori
                    </label>
                    <input type="text" 
                           name="category_name" 
                           id="category_name" 
                           value="{{ old('category_name', $category->category_name) }}" 
                           required
                           class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                           placeholder="Contoh: Data Pribadi">
                    @error('category_name')
                        <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Grid untuk Order dan For Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                    <!-- Urutan -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up text-green-600 mr-1"></i>
                            Urutan
                        </label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               value="{{ old('order', $category->order) }}" 
                               required
                               min="1"
                               class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Contoh: 1">
                        @error('order')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- For Type -->
                    <div>
                        <label for="for_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users text-purple-600 mr-1"></i>
                            Target Pengguna
                        </label>
                        <select name="for_type" 
                                id="for_type" 
                                required
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="both" {{ old('for_type', $category->for_type) == 'both' ? 'selected' : '' }}>
                                <i class="fas fa-users"></i> Alumni & Perusahaan
                            </option>
                            <option value="alumni" {{ old('for_type', $category->for_type) == 'alumni' ? 'selected' : '' }}>
                                <i class="fas fa-graduation-cap"></i> Alumni Saja
                            </option>
                            <option value="company" {{ old('for_type', $category->for_type) == 'company' ? 'selected' : '' }}>
                                <i class="fas fa-building"></i> Perusahaan Saja
                            </option>
                        </select>
                        @error('for_type')
                            <p class="text-red-500 text-xs sm:text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Status Dependency Section -->
                <div class="mb-4 sm:mb-6" id="status-dependency-section">
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                        <div class="flex items-start mb-3">
                            <input type="checkbox" 
                                   name="is_status_dependent" 
                                   id="is_status_dependent" 
                                   value="1" 
                                   {{ old('is_status_dependent', $category->is_status_dependent) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-0.5 flex-shrink-0">
                            <div class="ml-3">
                                <label for="is_status_dependent" class="text-sm font-medium text-gray-700 cursor-pointer">
                                    <i class="fas fa-user-check text-orange-600 mr-1"></i>
                                    Kategori bergantung pada status alumni
                                </label>
                                <p class="text-xs text-gray-600 mt-1">
                                    Jika diaktifkan, hanya alumni dengan status tertentu yang dapat mengakses kategori ini
                                </p>
                            </div>
                        </div>

                        <div id="alumni-status-options" class="mt-4 {{ old('is_status_dependent', $category->is_status_dependent) ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-check-square text-blue-600 mr-1"></i>
                                Pilih Status Alumni yang Dapat Mengakses Kategori Ini:
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach(\App\Models\Tb_Category::getAlumniStatusOptions() as $value => $label)
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                                        <input type="checkbox" 
                                               name="required_alumni_status[]" 
                                               id="status_{{ $value }}" 
                                               value="{{ $value }}"
                                               {{ in_array($value, old('required_alumni_status', $category->required_alumni_status ?? [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded flex-shrink-0">
                                        <span class="ml-3 text-sm text-gray-700 break-words">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('required_alumni_status')
                                <p class="text-red-500 text-xs sm:text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800 mb-1">Informasi Penting</h4>
                            <ul class="text-xs sm:text-sm text-blue-700 space-y-1">
                                <li>• Perubahan urutan akan mempengaruhi tampilan kategori dalam kuesioner</li>
                                <li>• Mengubah target pengguna mungkin mempengaruhi akses kategori yang sudah ada</li>
                                <li>• Status dependency hanya berlaku untuk alumni, tidak untuk perusahaan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 sm:pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                       class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base order-2 sm:order-1">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" 
                            class="flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base order-1 sm:order-2">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-md border border-gray-200 mt-4 sm:mt-6">
            <div class="px-4 sm:px-6 py-4 sm:py-6 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-eye mr-2 text-green-600"></i>
                    Preview Kategori
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Pratinjau bagaimana kategori akan tampil dalam kuesioner
                </p>
            </div>
            <div class="px-4 sm:px-6 py-4 sm:py-6">
                <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border-l-4 border-blue-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 space-y-2 sm:space-y-0">
                        <h4 id="preview-name" class="text-sm sm:text-base font-semibold text-gray-800">
                            {{ $category->category_name }}
                        </h4>
                        <div class="flex items-center space-x-2">
                            <span id="preview-order" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                Urutan: {{ $category->order }}
                            </span>
                            <span id="preview-target" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $category->for_type == 'alumni' ? 'bg-blue-100 text-blue-800' : 
                                   ($category->for_type == 'company' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                <i class="fas {{ $category->for_type == 'alumni' ? 'fa-graduation-cap' : ($category->for_type == 'company' ? 'fa-building' : 'fa-users') }} mr-1"></i>
                                {{ $category->for_type == 'alumni' ? 'Alumni' : ($category->for_type == 'company' ? 'Perusahaan' : 'Alumni & Perusahaan') }}
                            </span>
                        </div>
                    </div>
                    <div id="preview-status" class="text-xs sm:text-sm text-gray-600 {{ $category->is_status_dependent ? '' : 'hidden' }}">
                        <i class="fas fa-user-check text-orange-600 mr-1"></i>
                        Terbatas untuk status: <span id="preview-status-list" class="font-medium">
                            @if($category->required_alumni_status)
                                {{ implode(', ', array_map(function($status) {
                                    return \App\Models\Tb_Category::getAlumniStatusOptions()[$status] ?? $status;
                                }, $category->required_alumni_status)) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status dependency toggle
        const isStatusDependentCheckbox = document.getElementById('is_status_dependent');
        const alumniStatusOptions = document.getElementById('alumni-status-options');
        const previewStatus = document.getElementById('preview-status');
        
        isStatusDependentCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            if (isChecked) {
                alumniStatusOptions.classList.remove('hidden');
                previewStatus.classList.remove('hidden');
            } else {
                alumniStatusOptions.classList.add('hidden');
                previewStatus.classList.add('hidden');
                
                // Uncheck all status options
                const checkboxes = alumniStatusOptions.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                updateStatusPreview();
            }
        });

        // Real-time preview updates
        const categoryNameInput = document.getElementById('category_name');
        const orderInput = document.getElementById('order');
        const forTypeSelect = document.getElementById('for_type');
        
        const previewName = document.getElementById('preview-name');
        const previewOrder = document.getElementById('preview-order');
        const previewTarget = document.getElementById('preview-target');
        
        categoryNameInput.addEventListener('input', function() {
            previewName.textContent = this.value || 'Nama Kategori';
        });
        
        orderInput.addEventListener('input', function() {
            previewOrder.textContent = 'Urutan: ' + (this.value || '?');
        });
        
        forTypeSelect.addEventListener('change', function() {
            const value = this.value;
            let text, classes, icon;
            
            switch(value) {
                case 'alumni':
                    text = 'Alumni';
                    classes = 'bg-blue-100 text-blue-800';
                    icon = 'fa-graduation-cap';
                    break;
                case 'company':
                    text = 'Perusahaan';
                    classes = 'bg-green-100 text-green-800';
                    icon = 'fa-building';
                    break;
                default:
                    text = 'Alumni & Perusahaan';
                    classes = 'bg-purple-100 text-purple-800';
                    icon = 'fa-users';
            }
            
            previewTarget.className = `inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${classes}`;
            previewTarget.innerHTML = `<i class="fas ${icon} mr-1"></i>${text}`;
        });

        // Update status preview
        function updateStatusPreview() {
            const statusCheckboxes = document.querySelectorAll('input[name="required_alumni_status[]"]:checked');
            const statusList = document.getElementById('preview-status-list');
            
            const selectedStatuses = Array.from(statusCheckboxes).map(cb => {
                const label = cb.closest('label').querySelector('span').textContent.trim();
                return label;
            });
            
            statusList.textContent = selectedStatuses.length > 0 ? selectedStatuses.join(', ') : 'Tidak ada yang dipilih';
        }

        // Add event listeners to status checkboxes
        const statusCheckboxes = document.querySelectorAll('input[name="required_alumni_status[]"]');
        statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateStatusPreview);
        });
    });
    </script>

    <script src="{{ asset('js/script.js') }}"></script>
</x-layout-admin>
@endsection
