@php
$company = auth()->user()->company;
@endphp
<div class="relative">
    <div class="flex items-center bg-blue-900 text-white rounded-md px-4 py-2 cursor-pointer gap-3" id="profile-toggle">
        <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
        <div class="text-left">
            <p class="font-semibold leading-none">{{ $company->company_name }}</p>
            <p class="text-sm text-gray-300 leading-none mt-1">Perusahaan</p>
        </div>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
        <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
            <i class="fas fa-key mr-2"></i>Ganti Password
        </a>
        <a href="" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </div>
</div>
