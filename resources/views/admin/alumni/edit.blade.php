@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="flex min-h-screen w-full bg-gray-100 overflow-hidden" id="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-menu w-64 bg-blue-950 text-white flex flex-col transition-all duration-300" id="sidebar">
        <div class="flex flex-col items-center justify-between p-4">
            <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Logo" class="w-36 mt-2 object-contain" />
            <button id="close-sidebar" class="text-white text-xl lg:hidden absolute top-4 right-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col p-4">
            @include('admin.navbar')
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
                <h1 class="text-2xl font-bold text-blue-800">Edit Data Alumni</h1>
            </div>
            <div class="relative">
                <div
                    class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3"
                    id="profile-toggle"
                >
                    <img
                        src="{{ asset('assets/images/profilepicture.jpg') }}"
                        alt="Foto Profil"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white"
                    />
                    <div class="text-left">
                        <p class="font-semibold leading-none">Administrator</p>
                        <p class="text-sm text-gray-300 leading-none mt-1">Admin</p>
                    </div>
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div
                    id="profile-dropdown"
                    class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden"
                >
                    <a
                        href="{{ route('password.change') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300"
                    >
                        <i class="fas fa-key mr-2"></i>Ganti Password
                    </a>
                    <a
                        href="#"
                        id="logout-btn"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form
                action="{{ route('admin.alumni.update', $alumni->nim) }}"
                method="POST"
                class="bg-white rounded-xl shadow-md p-6 md:p-10"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIM (readonly) -->
                    <div>
                        <label for="nim" class="block font-semibold mb-1">NIM</label>
                        <input
                            type="text"
                            name="nim"
                            id="nim"
                            value="{{ old('nim', $alumni->nim) }}"
                            readonly
                            class="w-full border rounded px-3 py-2 bg-gray-100"
                        />
                        @error('nim')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIK -->
                    <div>
                        <label for="nik" class="block font-semibold mb-1">NIK</label>
                        <input
                            type="text"
                            name="nik"
                            id="nik"
                            value="{{ old('nik', $alumni->nik) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('nik')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block font-semibold mb-1">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $alumni->name) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-semibold mb-1">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $alumni->email) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="phone_number" class="block font-semibold mb-1">Nomor Telepon</label>
                        <input
                            type="text"
                            name="phone_number"
                            id="phone_number"
                            value="{{ old('phone_number', $alumni->phone_number) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('phone_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="gender" class="block font-semibold mb-1">Jenis Kelamin</label>
                        <select
                            name="gender"
                            id="gender"
                            class="w-full border rounded px-3 py-2"
                        >
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option
                                value="male"
                                {{ old('gender', $alumni->gender) == 'male' ? 'selected' : '' }}
                            >
                                male
                            </option>
                            <option
                                value="female"
                                {{ old('gender', $alumni->gender) == 'female' ? 'selected' : '' }}
                            >
                                female
                            </option>
                        </select>
                        @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="date_of_birth" class="block font-semibold mb-1">Tanggal Lahir</label>
                        <input
                            type="date"
                            name="date_of_birth"
                            id="date_of_birth"
                            value="{{ old('date_of_birth', $alumni->date_of_birth) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('date_of_birth')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prodi -->
                    <div>
                        <label for="id_study" class="block font-semibold mb-1">Prodi</label>
                        <select
                            name="id_study"
                            id="id_study"
                            class="w-full border rounded px-3 py-2"
                        >
                            <option value="">-- Pilih Prodi --</option>
                            @foreach ($prodi as $prodis)
                            <option
                                value="{{ $prodis->id_study }}"
                                {{ old('id_study', $alumni->id_study) == $prodis->id_study ? 'selected' : '' }}
                            >
                                {{ $prodis->study_program }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_study')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status (dropdown) -->
                    <div>
                        <label for="status" class="block font-semibold mb-1">Status</label>
                        <select
                            name="status"
                            id="status"
                            class="w-full border rounded px-3 py-2"
                        >
                            <option value="">-- Pilih Status --</option>
                            <option value="worked" {{ old('status', $alumni->status) == 'worked' ? 'selected' : '' }}>
                                Worked
                            </option>
                            <option value="not worked" {{ old('status', $alumni->status) == 'not worked' ? 'selected' : '' }}>
                                Not Worked
                            </option>
                            <option value="Melanjutkan studi" {{ old('status', $alumni->status) == 'Melanjutkan studi' ? 'selected' : '' }}>
                                Lanjut Studi
                            </option>
                        </select>
                        @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- IPK -->
                    <div>
                        <label for="ipk" class="block font-semibold mb-1">IPK</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="4"
                            name="ipk"
                            id="ipk"
                            value="{{ old('ipk', $alumni->ipk) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('ipk')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Angkatan -->
                    <div>
                        <label for="batch" class="block font-semibold mb-1">Angkatan</label>
                        <input
                            type="number"
                            name="batch"
                            id="batch"
                            value="{{ old('batch', $alumni->batch) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('batch')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Lulus -->
                    <div>
                        <label for="graduation_year" class="block font-semibold mb-1">Tahun Lulus</label>
                        <input
                            type="number"
                            name="graduation_year"
                            id="graduation_year"
                            value="{{ old('graduation_year', $alumni->graduation_year) }}"
                            class="w-full border rounded px-3 py-2"
                        />
                        @error('graduation_year')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="address" class="block font-semibold mb-1">Alamat</label>
                        <textarea
                            name="address"
                            id="address"
                            rows="3"
                            class="w-full border rounded px-3 py-2 resize-none"
                        >{{ old('address', $alumni->address) }}</textarea>
                        @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a
                        href="{{ route('admin.alumni.index') }}"
                        class="bg-gray-200 text-gray-700 py-2 px-6 rounded-lg font-semibold hover:bg-gray-300 transition-colors"
                    >
                        Kembali
                    </a>
                    <button
                        type="submit"
                        class="bg-blue-800 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-900 transition-colors"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const profileToggle = document.getElementById('profile-toggle');
    const profileDropdown = document.getElementById('profile-dropdown');
    const logoutBtn = document.getElementById('logout-btn');

    toggleSidebar?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });

    closeSidebar?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
    });

    profileToggle?.addEventListener('click', () => {
        profileDropdown.classList.toggle('hidden');
    });

    logoutBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('Yakin ingin logout?')) {
            window.location.href = "{{ route('logout') }}";
        }
    });

    if (window.innerWidth < 1024) {
        sidebar.classList.add('-translate-x-full');
    }
</script>
@endsection
