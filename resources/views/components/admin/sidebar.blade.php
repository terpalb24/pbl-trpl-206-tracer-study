<div class="flex flex-col items-center justify-between p-4">
    <img src="{{ asset('assets/images/Group 3.png') }}" alt="Tracer Study Polibatam Logo" class=" h-36 mt-2 object-contain">
    <button id="close-sidebar" class="text-white text-xl lg:hidden focus:outline-none absolute top-4 right-4">
        <i class="fas fa-times"></i>
    </button>
</div>
<div class="flex flex-col p-4">
    @include('admin.sidebar')
</div>
