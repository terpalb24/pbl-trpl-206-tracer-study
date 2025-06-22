<aside
    id="sidebar"
    {{ $attributes->merge([
        'class' => 'fixed top-0 left-0 w-64 h-screen bg-blue-950 text-white flex flex-col z-50 overflow-y-auto transition-transform duration-300 -translate-x-full lg:translate-x-0'
    ]) }}
    style="will-change: transform;"
>
    <!-- Logo dan tombol close -->
    <div class="flex flex-col items-center justify-between p-4">
        <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class="h-12 mt-2 object-contain">
        <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="flex flex-col p-4">
        @include('alumni.sidebar')
    </div>
</aside>
