<div class="bg-white shadow-sm p-4 flex justify-between items-center">
    <div class="flex items-center">
        <button id="toggle-sidebar" class="mr-4 lg:hidden">
            <i class="fas fa-bars text-xl text-black-800"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-800">{{ $title ?? 'Judul Halaman' }}</h1>
    </div>

    {{-- Include profile dropdown --}}
    <x-alumni.profile-dropdown />
</div>
