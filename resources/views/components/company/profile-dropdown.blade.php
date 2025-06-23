@php
$company = auth()->user()->company;
@endphp

<div class="relative">
    <div 
        id="profile-toggle"
        class="flex items-center cursor-pointer gap-3 
            md:bg-blue-900 md:text-white md:rounded-md md:px-4 md:py-2">
        <img src="{{ asset('assets/images/profilepicture.jpg') }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white" />
        <div class="text-left hidden md:block">
            <p class="font-semibold leading-none">{{ $company->company_name }}</p>
            <p class="text-sm text-gray-300 leading-none mt-1">Perusahaan</p>
        </div>
        <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <div id="profile-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
        <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
            <i class="fas fa-key mr-2"></i>Ganti Password
        </a>
        <a href="#" id="logout-btn" data-logout-url="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-300">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </div>
</div>

<!-- Modal Logout -->
<div id="modal-logout" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-xs sm:max-w-sm relative">
        <div class="flex flex-col items-center text-center">
            <img src="{{ asset('assets/images/logout.png') }}" alt="Logout" class="w-16 h-16 mb-3" />
            <h3 class="text-lg font-semibold mb-2 text-gray-800">Konfirmasi Logout</h3>
            <p class="text-gray-600 mb-4 text-sm">Apakah Anda yakin ingin keluar dari aplikasi?</p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full flex flex-col gap-2">
                @csrf
                <div class="flex justify-center gap-2 mt-2">
                    <button type="button" onclick="closeLogoutModal()"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-700 text-white font-semibold transition">Ya, Logout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buka modal saat tombol logout diklik
    document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('modal-logout').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
});

// Tutup modal
function closeLogoutModal() {
    document.getElementById('modal-logout').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Tutup modal jika klik backdrop
document.addEventListener('click', function(e) {
    if (e.target.id === 'modal-logout') {
        closeLogoutModal();
    }
});

// Tutup modal dengan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});
</script>