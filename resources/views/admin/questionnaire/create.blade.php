@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu h-12 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Tambah Periode Kuesioner</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('admin.questionnaire.index') }}" class="text-blue-600 hover:underline">Kuesioner</a></li>
                    <li><span class="text-gray-500">/</span></li>
                    <li class="text-gray-700">Tambah Periode</li>
                </ol>
            </nav>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Tambah Periode Questionnaire</h2>
                    <p class="text-gray-600">Buat periode questionnaire baru dengan target alumni yang spesifik</p>
                </div>

                <form action="{{ route('admin.questionnaire.store') }}" method="POST">
                    @csrf
                    
                    <!-- Tanggal Periode -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>Tanggal Mulai
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ old('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-check mr-2"></i>Tanggal Selesai
                            </label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Target Alumni Section -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            <i class="fas fa-users mr-2"></i>Target Alumni
                        </label>
                        
                        <!-- Target Type Selection -->
                        <div class="space-y-4 mb-6">
                            <!-- All Alumni -->
                            <label class="flex items-start p-4 bg-blue-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-blue-100" id="target-all">
                                <input type="radio" 
                                       name="target_type" 
                                       value="all"
                                       class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                       {{ old('target_type', 'all') === 'all' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-sm font-medium text-blue-800">Semua Alumni</span>
                                    <p class="text-xs text-blue-600 mt-1">Questionnaire dapat diakses oleh semua alumni terdaftar</p>
                                </div>
                            </label>

                            <!-- Years Ago -->
                            <label class="flex items-start p-4 bg-green-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-100" id="target-years-ago">
                                <input type="radio" 
                                       name="target_type" 
                                       value="years_ago"
                                       class="mt-1 mr-3 text-green-600 focus:ring-green-500"
                                       {{ old('target_type') === 'years_ago' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-sm font-medium text-green-800">Alumni N Tahun Lalu</span>
                                    <p class="text-xs text-green-600 mt-1">Target alumni berdasarkan berapa tahun lalu mereka lulus (relatif dengan tahun sekarang: {{ now()->year }})</p>
                                </div>
                            </label>

                            <!-- Specific Years -->
                            <label class="flex items-start p-4 bg-purple-50 rounded-lg border-2 cursor-pointer transition-all hover:bg-purple-100" id="target-specific">
                                <input type="radio" 
                                       name="target_type" 
                                       value="specific_years"
                                       class="mt-1 mr-3 text-purple-600 focus:ring-purple-500"
                                       {{ old('target_type') === 'specific_years' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-sm font-medium text-purple-800">Tahun Kelulusan Spesifik</span>
                                    <p class="text-xs text-purple-600 mt-1">Target alumni berdasarkan tahun kelulusan yang spesifik</p>
                                </div>
                            </label>
                        </div>

                        <!-- Years Ago Options -->
                        <div id="years-ago-section" class="hidden border border-green-200 rounded-lg p-4 bg-green-50">
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-sm font-medium text-green-800">
                                    Pilih Alumni yang Lulus Berapa Tahun Lalu
                                </label>
                                <div class="text-xs space-x-2">
                                    <button type="button" 
                                            id="select-all-years-ago" 
                                            class="text-green-600 hover:text-green-800 font-medium">
                                        Pilih Semua
                                    </button>
                                    <span class="text-green-400">|</span>
                                    <button type="button" 
                                            id="deselect-all-years-ago" 
                                            class="text-green-600 hover:text-green-800 font-medium">
                                        Hapus Semua
                                    </button>
                                </div>
                            </div>
                            
                            @if($yearsAgoOptions->isNotEmpty())
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                    @foreach($yearsAgoOptions as $option)
                                        <label class="flex items-center justify-between p-3 bg-white rounded border cursor-pointer hover:bg-green-50 transition-colors">
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       name="years_ago_list[]" 
                                                       value="{{ $option['years_ago'] }}"
                                                       class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 years-ago-checkbox"
                                                       {{ in_array($option['years_ago'], old('years_ago_list', [])) ? 'checked' : '' }}>
                                                <div class="ml-2">
                                                    <span class="text-sm font-medium text-gray-700">{{ $option['years_ago'] }} tahun lalu</span>
                                                    <p class="text-xs text-gray-500">({{ $option['year'] }})</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">
                                                {{ $option['count'] }} alumni
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500">
                                        Belum ada data alumni dari tahun-tahun sebelumnya.
                                    </p>
                                </div>
                            @endif
                            
                            @error('years_ago_list')
                                <p class="mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Specific Years Options -->
                        <div id="specific-years-section" class="hidden border border-purple-200 rounded-lg p-4 bg-purple-50">
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-sm font-medium text-purple-800">
                                    Pilih Tahun Kelulusan Spesifik
                                </label>
                                <div class="text-xs space-x-2">
                                    <button type="button" 
                                            id="select-all-specific-years" 
                                            class="text-purple-600 hover:text-purple-800 font-medium">
                                        Pilih Semua
                                    </button>
                                    <span class="text-purple-400">|</span>
                                    <button type="button" 
                                            id="deselect-all-specific-years" 
                                            class="text-purple-600 hover:text-purple-800 font-medium">
                                        Hapus Semua
                                    </button>
                                </div>
                            </div>
                            
                            @if(!empty($graduationYears))
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                    @foreach($graduationYears as $year)
                                        <label class="flex items-center justify-between p-3 bg-white rounded border cursor-pointer hover:bg-purple-50 transition-colors">
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       name="target_graduation_years[]" 
                                                       value="{{ $year }}"
                                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 specific-year-checkbox"
                                                       {{ in_array($year, old('target_graduation_years', [])) ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm font-medium text-gray-700">{{ $year }}</span>
                                            </div>
                                            <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded">
                                                {{ $graduationYearsWithCount[$year] ?? 0 }} alumni
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500">
                                        Belum ada data alumni dengan tahun kelulusan.
                                    </p>
                                </div>
                            @endif
                            
                            @error('target_graduation_years')
                                <p class="mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Preview Target Alumni -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-800 mb-2">
                                <i class="fas fa-eye mr-2"></i>Preview Target Alumni:
                            </h4>
                            <p id="target-preview" class="text-sm text-gray-700 font-medium">Semua Alumni</p>
                            <p id="alumni-count" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.questionnaire.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetTypeRadios = document.querySelectorAll('input[name="target_type"]');
    const yearsAgoSection = document.getElementById('years-ago-section');
    const specificYearsSection = document.getElementById('specific-years-section');
    const targetPreview = document.getElementById('target-preview');
    const alumniCount = document.getElementById('alumni-count');
    
    // Data alumni
    const alumniData = @json($graduationYearsWithCount);
    const yearsAgoData = @json($yearsAgoOptions);
    const totalAlumni = Object.values(alumniData).reduce((sum, count) => sum + count, 0);
    const currentYear = {{ now()->year }};

    // Handle target type changes
    targetTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all sections first
            yearsAgoSection.classList.add('hidden');
            specificYearsSection.classList.add('hidden');
            
            // Reset all selections
            document.querySelectorAll('.years-ago-checkbox, .specific-year-checkbox').forEach(cb => {
                cb.checked = false;
            });
            
            // Show relevant section
            if (this.value === 'years_ago') {
                yearsAgoSection.classList.remove('hidden');
            } else if (this.value === 'specific_years') {
                specificYearsSection.classList.remove('hidden');
            }
            
            // Update preview
            updatePreview();
            
            // Update border colors
            updateBorderColors(this.value);
        });
    });

    // Update border colors based on selection
    function updateBorderColors(selectedType) {
        document.getElementById('target-all').classList.remove('border-blue-500');
        document.getElementById('target-years-ago').classList.remove('border-green-500');
        document.getElementById('target-specific').classList.remove('border-purple-500');
        
        if (selectedType === 'all') {
            document.getElementById('target-all').classList.add('border-blue-500');
        } else if (selectedType === 'years_ago') {
            document.getElementById('target-years-ago').classList.add('border-green-500');
        } else if (selectedType === 'specific_years') {
            document.getElementById('target-specific').classList.add('border-purple-500');
        }
    }

    // Handle checkbox changes
    document.querySelectorAll('.years-ago-checkbox, .specific-year-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updatePreview);
    });

    // Select/Deselect all buttons for years ago
    document.getElementById('select-all-years-ago')?.addEventListener('click', function() {
        document.querySelectorAll('.years-ago-checkbox').forEach(cb => cb.checked = true);
        updatePreview();
    });

    document.getElementById('deselect-all-years-ago')?.addEventListener('click', function() {
        document.querySelectorAll('.years-ago-checkbox').forEach(cb => cb.checked = false);
        updatePreview();
    });

    // Select/Deselect all buttons for specific years
    document.getElementById('select-all-specific-years')?.addEventListener('click', function() {
        document.querySelectorAll('.specific-year-checkbox').forEach(cb => cb.checked = true);
        updatePreview();
    });

    document.getElementById('deselect-all-specific-years')?.addEventListener('click', function() {
        document.querySelectorAll('.specific-year-checkbox').forEach(cb => cb.checked = false);
        updatePreview();
    });

    function updatePreview() {
        const selectedTargetType = document.querySelector('input[name="target_type"]:checked')?.value;
        
        if (selectedTargetType === 'all') {
            targetPreview.textContent = 'Semua Alumni';
            alumniCount.textContent = `Total: ${totalAlumni} alumni terdaftar`;
            
        } else if (selectedTargetType === 'years_ago') {
            const selectedYearsAgo = Array.from(document.querySelectorAll('.years-ago-checkbox:checked'))
                .map(cb => parseInt(cb.value))
                .sort((a, b) => a - b);

            if (selectedYearsAgo.length === 0) {
                targetPreview.textContent = 'Belum ada periode yang dipilih';
                alumniCount.textContent = 'Pilih minimal satu periode tahun lalu';
            } else {
                const descriptions = selectedYearsAgo.map(yearsAgo => {
                    const year = currentYear - yearsAgo;
                    return `${yearsAgo} tahun lalu (${year})`;
                });

                const totalSelectedAlumni = selectedYearsAgo.reduce((sum, yearsAgo) => {
                    const year = (currentYear - yearsAgo).toString();
                    return sum + (alumniData[year] || 0);
                }, 0);

                targetPreview.textContent = `Alumni Lulusan: ${descriptions.join(', ')}`;
                alumniCount.textContent = `Total: ${totalSelectedAlumni} alumni dari ${selectedYearsAgo.length} periode`;
            }
            
        } else if (selectedTargetType === 'specific_years') {
            const selectedYears = Array.from(document.querySelectorAll('.specific-year-checkbox:checked'))
                .map(cb => cb.value)
                .sort((a, b) => b - a);

            if (selectedYears.length === 0) {
                targetPreview.textContent = 'Belum ada tahun yang dipilih';
                alumniCount.textContent = 'Pilih minimal satu tahun kelulusan';
            } else {
                const totalSelectedAlumni = selectedYears.reduce((sum, year) => {
                    return sum + (alumniData[year] || 0);
                }, 0);

                targetPreview.textContent = `Alumni Lulusan Tahun: ${selectedYears.join(', ')}`;
                alumniCount.textContent = `Total: ${totalSelectedAlumni} alumni dari ${selectedYears.length} tahun kelulusan`;
            }
        }
    }

    // Initialize on page load
    const initialTargetType = document.querySelector('input[name="target_type"]:checked')?.value || 'all';
    
    if (initialTargetType === 'years_ago') {
        yearsAgoSection.classList.remove('hidden');
    } else if (initialTargetType === 'specific_years') {
        specificYearsSection.classList.remove('hidden');
    }
    
    updateBorderColors(initialTargetType);
    updatePreview();

    // Profile dropdown functionality
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

    // Logout functionality
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

    // Sidebar functionality
    document.getElementById('toggle-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('hidden');
    });
});
</script>
@endsection
