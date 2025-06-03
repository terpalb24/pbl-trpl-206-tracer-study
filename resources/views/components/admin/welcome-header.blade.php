<div class="bg-gradient-to-r from-white via-sky-300 to-orange-300 rounded-lg shadow-md mb-6 overflow-hidden">
    <div class="flex flex-col md:flex-row">
        <div class="p-4 md:w-2/3 pl-12 mt-6">
            <h2 class="text-4xl font-bold text-black leading-tight mb-2">Halo!</h2>
            <p class="text-3xl font-semibold text-black leading-tight">{{ $role ?? 'Administrator' }}</p>
        </div>
        <div class="md:w-1/3 flex items-center justify-center p-4">
            <img src="{{ asset('assets/images/adminprofile.png') }}" alt="Admin Profile" class="h-40 w-40 object-cover">
        </div>
    </div>
</div>
