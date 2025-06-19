@extends('layouts.app')

@php
$admin = auth()->user()->admin;
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
<!-- Sidebar -->
<aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
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
            <h1 class="text-2xl font-bold text-blue-800">Kuisioner</h1>
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
        <!-- First Card - Add Questionnaire Button -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.questionnaire.create') }}" class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-plus"></i> Tambah Kuisioner
                </a>
                <a href="{{ route('admin.questionnaires.import-export') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-file-import"></i> Import/Export Kuisioner
                </a>
                <a href="{{ route('admin.questionnaires.download-template') }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                    <i class="fas fa-download"></i> Download Template
                </a>
                <!-- Tombol Kirim Pengingat ke Semua -->
                <form action="" method="POST" id="remind-all-form" class="inline-block">
                    @csrf
                    <input type="hidden" name="id_periode" id="remind-all-id-periode" value="">
                    <button type="button" onclick="showRemindAllModal()" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        <i class="fas fa-bell"></i> Kirim Pengingat ke Semua
                    </button>
                </form>
            </div>
        </div>
        <!-- Modal Pilih Periode -->
        <div id="remindAllModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Kirim Pengingat ke Semua</h3>
                <form id="remindAllModalForm" method="POST">
                    @csrf
                    <label for="periode_id_select" class="block mb-2 text-sm font-medium text-gray-700">Pilih Periode Aktif</label>
                    <select id="periode_id_select" name="id_periode" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4" required>
                        <option value="">-- Pilih Periode Aktif --</option>
                        @foreach($periodes as $periode)
                            @if($periode->status == 'active')
                                <option value="{{ $periode->id_periode }}">{{ $periode->periode_name }} ({{ $periode->start_date->format('d M Y') }} - {{ $periode->end_date->format('d M Y') }})</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeRemindAllModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function showRemindAllModal() {
                document.getElementById('remindAllModal').classList.remove('hidden');
            }
            function closeRemindAllModal() {
                document.getElementById('remindAllModal').classList.add('hidden');
            }
            document.getElementById('remindAllModalForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const periodeId = document.getElementById('periode_id_select').value;
                if (!periodeId) {
                    alert('Pilih periode aktif terlebih dahulu!');
                    return;
                }
                // Set action form ke route yang benar
                this.action = '/admin/questionnaire/' + periodeId + '/remind-all';
                this.submit();
            });
        </script>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Second Card - Filter and List Questionnaires -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-4 border-b">
                <form method="GET" action="{{ route('admin.questionnaire.index') }}" class="flex flex-wrap items-center gap-4">
                    <!-- Filter Tahun -->
                    <div class="flex items-center space-x-2">
                        <label for="year" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Tahun:</label>
                        <select name="year" id="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[120px]">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="flex items-center space-x-2">
                        <label for="status" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Status:</label>
                        <select name="status" id="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 min-w-[120px]">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Tombol Filter dan Reset -->
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        
                        @if(request('year') || request('status'))
                            <a href="{{ route('admin.questionnaire.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Informasi Filter Aktif -->
                @if(request('year') || request('status'))
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Filter aktif: </span>
                            @if(request('year'))
                                <span class="ml-1 px-2 py-1 bg-blue-100 rounded">Tahun {{ request('year') }}</span>
                            @endif
                            @if(request('status'))
                                <span class="ml-1 px-2 py-1 bg-blue-100 rounded">Status {{ ucfirst(request('status')) }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Target Alumni</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Mulai</th>
                            <th class="px-4 py-3">Tanggal Selesai</th>
                            <th class="px-4 py-3">Tahun Dibuat</th>
                            <th class="px-4 py-3">Jumlah Kategori</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($periodes as $index => $periode)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">{{ ($periodes->currentPage() - 1) * $periodes->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 font-medium">{{ $periode->getTargetDescription() }}</div>
                                    @if($periode->target_type === 'years_ago' && !empty($periode->years_ago_list))
                                        <div class="text-xs text-blue-600 mt-1 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>Relatif dengan tahun sekarang ({{ now()->year }})
                                        </div>
                                    @elseif($periode->target_type === 'specific_years' && !empty($periode->target_graduation_years))
                                        <div class="text-xs text-purple-600 mt-1 flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>Tahun kelulusan spesifik
                                        </div>
                                    @elseif($periode->target_type === 'all')
                                        <div class="text-xs text-green-600 mt-1 flex items-center">
                                            <i class="fas fa-users mr-1"></i>Semua alumni dapat mengakses
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        {{ $periode->status == 'active' ? 'bg-green-100 text-green-800' : 
                                          ($periode->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($periode->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $periode->start_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $periode->end_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $periode->created_at->format('Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $periode->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-blue-600 bg-blue-100 rounded-full">
                                        {{ $periode->categories->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.questionnaire.show', $periode->id_periode) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('admin.questionnaire.edit', $periode->id_periode) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="{{ route('admin.questionnaire.responses', $periode->id_periode) }}" 
                                           class="text-green-600 hover:text-green-900 font-medium text-sm">
                                            <i class="fas fa-chart-bar mr-1"></i>Respons
                                        </a>
                                        
                                        @php
                                            // Check if periode can be deleted (no responses and not active)
                                            $hasResponses = \App\Models\Tb_User_Answers::where('id_periode', $periode->id_periode)->exists();
                                            $canDelete =  $periode->status !== 'active';
                                        @endphp
                                        
                                        @if($canDelete)
                                            <form action="{{ route('admin.questionnaire.destroy', $periode->id_periode) }}" 
                                                  method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirmDelete('{{ $periode->periode_name }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 font-medium text-sm">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 font-medium text-sm cursor-not-allowed" 
                                                  title="{{ $hasResponses ? 'Periode ini sudah memiliki respons dan tidak dapat dihapus' : 'Periode aktif tidak dapat dihapus' }}">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                                        @if(request('year') || request('status'))
                                            <p class="text-lg font-medium mb-1">Tidak ada periode kuesioner ditemukan</p>
                                            <p class="text-sm">Coba ubah filter atau reset filter untuk melihat semua data</p>
                                        @else
                                            <p class="text-lg font-medium mb-1">Belum ada periode kuesioner</p>
                                            <p class="text-sm">Klik tombol "Tambah Periode" untuk membuat kuesioner baru</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t text-sm text-gray-500">
                {{ $periodes->withQueryString()->links() }}
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

document.getElementById('profile-toggle').addEventListener('click', () => {
    document.getElementById('profile-dropdown').classList.toggle('hidden');
});

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profile-dropdown');
    const toggle = document.getElementById('profile-toggle');
    
    if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

document.getElementById('logout-btn').addEventListener('click', function (event) {
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

// Function for delete confirmation
function confirmDelete(periodeName) {
    return confirm(`Apakah Anda yakin ingin menghapus periode "${periodeName}"?\n\nPeringatan: Semua data terkait (kategori, pertanyaan, dan opsi) akan dihapus secara permanen dan tidak dapat dipulihkan.`);
}
</script>
@endsection
