<div class="fixed top-0 left-0 right-0 z-40 bg-white shadow-sm flex justify-between px-8 h-28 items-center lg:ml-64"
     style="height: 80px;">
    <div class="flex items-center min-h-0 py-8">
        <button id="toggle-sidebar" class="mr-4 lg:hidden">
            <i class="fas fa-bars text-xl text-black-800"></i>
        </button>
        <h1 class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-blue-800 truncate">
            {{ $title ?? 'Judul Halaman' }}
        </h1>
    </div>
    <div class="flex-shrink-0 ml-2 sm:ml-4">
        <x-alumni.profile-dropdown />
    </div>
</div>
