<!-- resources/views/components/company/header.blade.php -->
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
        <div class="flex justify-between items-center">
            <!-- Left Section: Toggle Button + Title -->
            <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                <!-- Toggle Button (Mobile Only) -->
                <button id="toggle-sidebar" class="flex-shrink-0 p-2 rounded-lg hover:bg-gray-100 lg:hidden focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="fas fa-bars text-lg sm:text-xl text-gray-700 hover:text-blue-600"></i>
                </button>
                
                <!-- Page Title -->
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-blue-800 truncate">
                        {{ $title ?? 'Dashboard' }}
                    </h1>
                </div>
            </div>

            <!-- Right Section: Profile Dropdown -->
            <div class="flex items-center space-x-3 sm:space-x-4 flex-shrink-0">
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    @include('components.company.profile-dropdown')
                </div>
            </div>
        </div>
        
        <!-- Mobile Secondary Actions (if needed) -->
        <div class="mt-3 flex items-center space-x-3 sm:hidden">
            <!-- Additional mobile-specific actions can go here -->
        </div>
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