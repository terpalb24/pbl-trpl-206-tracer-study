@extends('layouts.app')

@section('content')
<!-- Add CSRF token for logout functionality -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="editprofil-container">

    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4 ">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="w-36 mt-2 object-contain">
            <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex flex-col p-4">
            @include('company.navbar')
        </div>
    </aside>

    <!-- Tombol Toggle Sidebar (Untuk Mobile) -->
    <button id="toggle-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 left-4 z-50">
        <i class="fas fa-bars"></i> <!-- Ikon hamburger menu -->
    </button>

        <!-- Main Content -->
        <main class="flex-grow overflow-y-auto" id="main-content">
        <!-- Header -->
        <div class="bg-white shadow-sm p-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="toggle-sidebar" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-xl text-black-800"></i>
                </button>
                <h1 class="text-2xl font-bold text-blue-800">Edit Profil</h1>
            </div>

            <!-- Profile Dropdown Button -->
            <div class="relative">
                <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
                    <!-- FOTO PROFIL -->
                    <img src="{{ asset('assets/images/profilepicture.jpg') }}"
                        alt="Foto Profil"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white" />
                        
                    <div class="text-left">
                        <p class="font-semibold leading-none">{{ $company->company_name }}</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">company</p>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <!-- Dropdown Menu -->
                <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a href="" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div> 

        <!-- Pesan Flash -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Edit Profil -->
        <form action="{{ route('company.update') }}" method="POST" class="bg-white rounded-2xl shadow-md mt-8 mx-4 md:mx-10 lg:mx-16 xl:mx-24 p-6 md:p-10 lg:p-12 xl:p-16">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 text-sm ">


            <!-- company_name -->
<div>
    <label class="block font-semibold mb-1">Company Name</label>
    <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}"
        class="w-full border px-3 py-2 rounded @error('company_name') border-red-500 @enderror">
    @error('company_name')
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>

<!-- company_address -->
<div>
    <label class="block font-semibold mb-1">Company Address</label>
    <input type="text" name="company_address" value="{{ old('company_address', $company->company_address) }}"
        class="w-full border px-3 py-2 rounded @error('company_address') border-red-500 @enderror">
    @error('company_address')
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>

<!-- company_email-->
<div>
    <label for="company_email" class="block font-semibold mb-1">Company Email</label>
    <input type="email" name="company_email" value="{{ old('company_email', $company->company_email) }}"
        class="w-full border px-3 py-2 rounded @error('company_email') border-red-500 @enderror">
    @error('company_email')
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>

<!-- company_phone_number -->
<div>
    <label for="company_phone_number" class="block font-semibold mb-1">Company Phone Number</label>
    <input type="text" name="company_phone_number" value="{{ old('company_phone_number', $company->company_phone_number) }}"
        class="w-full border px-3 py-2 rounded @error('company_phone_number') border-red-500 @enderror">
    @error('company_phone_number')
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>


            </div>

            <!-- Tombol Aksi -->
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('dashboard.company') }}"
                    class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </main>
</div>

<script>
    // Toggle sidebar visibility
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('hidden');
    });

    document.getElementById('close-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.add('hidden');
    });

    // Toggle profile dropdown
    document.getElementById('profile-toggle').addEventListener('click', function () {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('profile-dropdown');
        const toggle = document.getElementById('profile-toggle');
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Logout functionality
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
    document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('id_study');
    const input = document.getElementById('prodi-combobox');
    const list = document.getElementById('prodi-list');

    // Fungsi buat render list filter berdasarkan input
    function renderList(filter = '') {
        const filterLower = filter.toLowerCase();
        const options = Array.from(select.options).filter(opt =>
            opt.value !== '' && opt.text.toLowerCase().includes(filterLower)
        );

        if (options.length === 0) {
            list.innerHTML = '<p class="p-2 text-gray-500">Tidak ada Prodi ditemukan</p>';
        } else {
            list.innerHTML = options.map(opt =>
                `<div class="p-2 cursor-pointer hover:bg-blue-500 hover:text-white" data-value="${opt.value}">${opt.text}</div>`
            ).join('');
        }
        list.classList.remove('hidden');
    }

    // Set nilai input awal sesuai select yang terpilih
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value !== '') {
        input.value = selectedOption.text;
    }

    // Ketika input fokus, render semua option
    input.addEventListener('focus', () => {
        renderList(input.value);
    });

    // Ketika ketik di input, filter list
    input.addEventListener('input', () => {
        renderList(input.value);
    });

    // Klik di item dropdown pilih value
    list.addEventListener('click', (e) => {
        if (e.target && e.target.dataset.value) {
            const val = e.target.dataset.value;
            const label = e.target.textContent;
            select.value = val;       // Update select hidden
            input.value = label;      // Update input
            list.classList.add('hidden');
        }
    });

    // Klik di luar combobox / list -> sembunyikan dropdown
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.classList.add('hidden');
        }
    });
});
</script>
@endsection
