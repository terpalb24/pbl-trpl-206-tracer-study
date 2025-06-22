<!-- resources/views/components/company/header.blade.php -->
<header class="fixed top-0 left-0 right-0 z-40 bg-white shadow-sm flex justify-between items-center px-8 h-20 lg:ml-64"
        style="height:80px;">
    <div class="flex items-center min-w-0">
        <button id="toggle-sidebar" class="mr-4 lg:hidden">
            <i class="fas fa-bars text-xl text-gray-700"></i>
        </button>
        <h1 class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-blue-800 truncate">
            {{ $title ?? 'Dashboard' }}
        </h1>
    </div>
    <div class="flex-shrink-0 ml-2 sm:ml-4">
        @include('components.company.profile-dropdown')
    </div>
</header>

<!-- Google Translate Widget -->
<div class="relative z-30">
    <x-translate-widget 
        position="bottom-left" 
        :languages="['en', 'id']" 
        theme="light"
    />
</div>

<!-- Google Translate Widget CSS -->
<link rel="stylesheet" href="{{ asset('css/translate-widget.css') }}">