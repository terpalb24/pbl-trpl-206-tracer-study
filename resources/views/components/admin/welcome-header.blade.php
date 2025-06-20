<div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg sm:rounded-xl shadow-md mb-4 sm:mb-6 overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <div class="p-4 sm:p-6 md:p-8 md:w-2/3 pl-6 sm:pl-8 md:pl-12 mt-3 sm:mt-4 md:mt-6">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-black leading-tight mb-1 sm:mb-2">Halo!</h2>
            <p class="text-xl sm:text-2xl md:text-3xl font-semibold text-black leading-tight">{{ $role ?? 'Administrator' }}</p>
        </div>
        <div class="md:w-1/3 flex items-center justify-center p-3 sm:p-4">
            <img src="{{ asset('assets/images/adminprofile.png') }}" 
                 alt="Admin Profile" 
                 class="h-24 w-24 sm:h-32 sm:w-32 md:h-40 md:w-40 object-cover">
        </div>
    </div>
</div>
