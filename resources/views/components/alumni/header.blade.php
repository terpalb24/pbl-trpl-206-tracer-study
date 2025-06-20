<div class="bg-white shadow-sm p-3 sm:p-4 lg:p-6 flex justify-between items-center">
    <div class="flex items-center min-w-0 flex-1">
        <button id="toggle-sidebar" class="mr-2 sm:mr-3 lg:mr-4 lg:hidden flex-shrink-0 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
            <i class="fas fa-bars text-lg sm:text-xl text-gray-800"></i>
        </button>
        <h1 class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-blue-800 truncate">
            {{ $title ?? 'Judul Halaman' }}
        </h1>
    </div>

    {{-- Include profile dropdown --}}
    <div class="flex-shrink-0 ml-2 sm:ml-4">
        <x-alumni.profile-dropdown />
    </div>
</div>
