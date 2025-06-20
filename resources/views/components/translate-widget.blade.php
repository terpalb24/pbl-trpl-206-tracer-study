@props([
    'position' => 'top-right',
    'languages' => 'en,id',
    'theme' => 'light',
    'draggable' => true,
    'minimizable' => true
])

@php
$positionClasses = match($position) {
    'top-left' => 'top-16 sm:top-20 left-2 sm:left-4',
    'top-right' => 'top-16 sm:top-20 right-2 sm:right-4',
    'bottom-left' => 'bottom-2 sm:bottom-4 left-2 sm:left-4',
    'bottom-right' => 'bottom-2 sm:bottom-4 right-2 sm:right-4',
    default => 'top-16 sm:top-20 right-2 sm:right-4'
};

$themeClasses = match($theme) {
    'dark' => 'translate-widget-dark',
    'light' => 'translate-widget-light',
    'transparent' => 'translate-widget-transparent',
    default => 'translate-widget-light'
};
@endphp
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<!-- Draggable Language Switcher -->
<div id="custom_translate_widget" 
     class="fixed {{ $positionClasses }} z-50 {{ $themeClasses }} select-none {{ $draggable ? 'cursor-move' : '' }}"
     data-draggable="{{ $draggable ? 'true' : 'false' }}"
     data-minimizable="{{ $minimizable ? 'true' : 'false' }}">
    
    <!-- Drag Handle (visible when draggable) -->
    @if($draggable)
        <div id="drag_handle" class="absolute -top-2 -left-2 w-6 h-6 bg-blue-500 rounded-full shadow-lg flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 transition-opacity duration-200">
            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
            </svg>
        </div>
    @endif
    
    <!-- Minimize Button (visible when minimizable) -->
    @if($minimizable)
        <div id="minimize_button" class="absolute -top-2 -right-2 w-6 h-6 bg-gray-500 hover:bg-gray-600 rounded-full shadow-lg flex items-center justify-center cursor-pointer opacity-70 hover:opacity-100 transition-all duration-200">
            <svg id="minimize_icon" class="w-3 h-3 text-white transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
        </div>
    @endif
    
    <!-- Main Widget Content - Tanpa Background Belakang -->
    <div id="widget_content" class="rounded-lg overflow-hidden min-w-[120px] sm:min-w-[140px] max-w-[160px] sm:max-w-none transition-all duration-300">
        <!-- Header with Title Bar -->
        <div class="flex items-center justify-between bg-white/90 backdrop-blur-sm px-2 py-1 border-b border-gray-200/50 rounded-t-lg shadow-sm">
            <div class="flex items-center gap-1">
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            </div>
            <span class="text-xs text-gray-600 font-medium">Translate</span>
            @if($minimizable)
                <button id="minimize_btn" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
            @endif
        </div>
        
        <!-- Language Toggle Button -->
        <button id="language_toggle" class="flex items-center justify-between w-full px-3 sm:px-4 py-2 sm:py-3 text-gray-700 bg-white/90 backdrop-blur-sm hover:bg-white/95 transition-all duration-200 shadow-sm">
            <div class="flex items-center gap-1 sm:gap-2">
                <span id="current_flag" class="text-sm sm:text-base">🇮🇩</span>
                <span id="current_language" class="text-xs sm:text-sm font-medium truncate">Indonesia</span>
            </div>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" id="dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Language Dropdown -->
        <div id="language_dropdown" class="hidden border-t border-gray-200/50">
            <button class="language_option flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 text-gray-700 bg-white/90 backdrop-blur-sm hover:bg-blue-50/90 transition-all duration-200 w-full text-left shadow-sm" data-lang="id" data-flag="🇮🇩" data-name="Indonesia">
                <span class="text-sm sm:text-lg flex-shrink-0">🇮🇩</span>
                <span class="text-xs sm:text-sm truncate">Indonesia</span>
            </button>
            <button class="language_option flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 text-gray-700 bg-white/90 backdrop-blur-sm hover:bg-blue-50/90 transition-all duration-200 w-full text-left rounded-b-lg shadow-sm" data-lang="en" data-flag="🇺🇸" data-name="English">
                <span class="text-sm sm:text-lg flex-shrink-0">🇺🇸</span>
                <span class="text-xs sm:text-sm truncate">English</span>
            </button>
        </div>
    </div>
    
    <!-- Minimized State - Hanya Bulatan Kecil -->
    <div id="minimized_state" class="hidden bg-white rounded-full shadow-lg border border-gray-200 w-8 h-8 flex items-center justify-center cursor-pointer hover:shadow-xl transition-all duration-300">
        <span id="minimized_flag" class="text-sm">🇮🇩</span>
    </div>
    
    <!-- Position Indicator (only visible when dragging) -->
    <div id="position_indicator" class="hidden absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded whitespace-nowrap">
        <span id="indicator_text">Drag to move</span>
    </div>
</div>

<!-- Hidden Google Translate Element -->
<div id="google_translate_element" style="display: none !important;"></div>

@once
<style>

/* Enhanced Widget Styles with Minimize */
.translate-widget-dragging {
    transition: none !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    transform: scale(1.05) !important;
    z-index: 9999 !important;
    min-width: fit-content !important;    
}

.translate-widget-dragging .bg-white {
    background-color: rgb(249 250 251) !important;
    min-width: fit-content !important;    
}

.drag-handle-active {
    background-color: rgb(34 197 94) !important;
    transform: scale(1.1) !important;
    min-width: fit-content !important;    
}

/* Smooth transitions for non-dragging state */
#custom_translate_widget:not(.translate-widget-dragging) {
    transition: all 0.3s ease !important;
    min-width: fit-content !important;    
}

/* Minimize animations */
.widget-minimizing #widget_content {
    animation: minimizeOut 0.3s ease-in-out forwards;
}

.widget-minimizing #minimized_state {
    animation: minimizeIn 0.3s ease-in-out forwards;
}

.widget-maximizing #widget_content {
    animation: maximizeIn 0.3s ease-in-out forwards;
}

.widget-maximizing #minimized_state {
    animation: maximizeOut 0.3s ease-in-out forwards;
}

@keyframes minimizeOut {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
    100% {
        opacity: 0;
        transform: scale(0.3);
        display: none;
    }
}

@keyframes minimizeIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes maximizeOut {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
    100% {
        opacity: 0;
        transform: scale(0.3);
        display: none;
    }
}

@keyframes maximizeIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 0.5;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Ensure widget stays within viewport */
#custom_translate_widget {
    max-width: calc(100vw - 1rem);
    max-height: calc(100vh - 1rem);
}

/* Minimize button positioning */
#minimize_button {
    transition: all 0.2s ease;
}

#minimize_button:hover {
    transform: scale(1.1);
}

/* Minimized state styling */
#minimized_state {
    transition: all 0.3s ease;
    width: 2rem !important;
    height: 2rem !important;
}

#minimized_state:hover {
    transform: scale(1.15);
}

#minimized_flag {
    font-size: 0.875rem !important;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    #drag_handle, #minimize_button {
        width: 1.5rem !important;
        height: 1.5rem !important;
        top: -0.5rem !important;
    }
    
    #drag_handle {
        left: -0.5rem !important;
    }
    
    #minimize_button {
        right: -0.5rem !important;
    }
    
    #drag_handle svg, #minimize_button svg {
        width: 0.75rem !important;
        height: 0.75rem !important;
    }
    
    .translate-widget-dragging {
        transform: scale(1.02) !important;
    }
    
    #minimized_state {
        width: 1.75rem !important;
        height: 1.75rem !important;
    }
    
    #minimized_flag {
        font-size: 0.75rem !important;
    }
}

/* Pulse effect untuk minimized state yang lebih kecil */
.minimized-pulse {
    animation: pulse-small 2s infinite;
}

@keyframes pulse-small {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

</style>

<script type="text/javascript">
let currentLang = 'id';
let isUserAction = false;
let translateInitialized = false;
let isMinimized = false;

// Draggable functionality
let isDragging = false;
let dragStartX = 0;
let dragStartY = 0;
let widgetStartX = 0;
let widgetStartY = 0;

// Language preference management
function getSavedLanguage() {
    return localStorage.getItem('preferred_language') || 'id';
}

function saveLanguagePreference(lang) {
    localStorage.setItem('preferred_language', lang);
    console.log('Language preference saved:', lang);
}

// Minimize state management
function getSavedMinimizedState() {
    return localStorage.getItem('translate_widget_minimized') === 'true';
}

function saveMinimizedState(minimized) {
    localStorage.setItem('translate_widget_minimized', minimized.toString());
    console.log('Minimized state saved:', minimized);
}

// Position management
function getSavedPosition() {
    const saved = localStorage.getItem('translate_widget_position');
    return saved ? JSON.parse(saved) : null;
}

function saveWidgetPosition(x, y) {
    const position = { x, y, timestamp: Date.now() };
    localStorage.setItem('translate_widget_position', JSON.stringify(position));
    console.log('Widget position saved:', position);
}

function applySavedPosition() {
    const widget = document.getElementById('custom_translate_widget');
    const savedPos = getSavedPosition();
    
    if (savedPos && widget) {
        // Ensure position is within viewport
        const maxX = window.innerWidth - widget.offsetWidth - 16;
        const maxY = window.innerHeight - widget.offsetHeight - 16;
        
        const x = Math.max(16, Math.min(savedPos.x, maxX));
        const y = Math.max(16, Math.min(savedPos.y, maxY));
        
        widget.style.left = x + 'px';
        widget.style.top = y + 'px';
        widget.style.right = 'auto';
        widget.style.bottom = 'auto';
        
        console.log('Applied saved position:', { x, y });
    }
}

// Minimize/Maximize functionality
function minimizeWidget() {
    const widget = document.getElementById('custom_translate_widget');
    const widgetContent = document.getElementById('widget_content');
    const minimizedState = document.getElementById('minimized_state');
    const dropdown = document.getElementById('language_dropdown');
    const arrow = document.getElementById('dropdown_arrow');
    
    if (!widget || isMinimized) return;
    
    console.log('Minimizing widget...');
    
    // Close dropdown if open
    if (dropdown && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
    
    // Add animation class
    widget.classList.add('widget-minimizing');
    
    setTimeout(() => {
        widgetContent.classList.add('hidden');
        minimizedState.classList.remove('hidden');
        widget.classList.remove('widget-minimizing');
        
        // Update minimized flag
        const currentFlag = document.getElementById('current_flag').textContent;
        document.getElementById('minimized_flag').textContent = currentFlag;
        
        isMinimized = true;
        saveMinimizedState(true);
        
        // Add pulse effect temporarily
        minimizedState.classList.add('minimized-pulse');
        setTimeout(() => {
            minimizedState.classList.remove('minimized-pulse');
        }, 4000);
        
        console.log('Widget minimized');
    }, 150);
}

function maximizeWidget() {
    const widget = document.getElementById('custom_translate_widget');
    const widgetContent = document.getElementById('widget_content');
    const minimizedState = document.getElementById('minimized_state');
    
    if (!widget || !isMinimized) return;
    
    console.log('Maximizing widget...');
    
    // Add animation class
    widget.classList.add('widget-maximizing');
    
    setTimeout(() => {
        minimizedState.classList.add('hidden');
        widgetContent.classList.remove('hidden');
        widget.classList.remove('widget-maximizing');
        
        isMinimized = false;
        saveMinimizedState(false);
        
        console.log('Widget maximized');
    }, 150);
}

function initializeMinimize() {
    const widget = document.getElementById('custom_translate_widget');
    const minimizeBtn = document.getElementById('minimize_btn');
    const minimizeButton = document.getElementById('minimize_button');
    const minimizedState = document.getElementById('minimized_state');
    
    if (!widget || widget.dataset.minimizable !== 'true') return;
    
    console.log('Initializing minimize functionality...');
    
    // Apply saved minimized state
    const savedMinimized = getSavedMinimizedState();
    if (savedMinimized) {
        setTimeout(() => {
            minimizeWidget();
        }, 500);
    }
    
    // Minimize button in title bar
    if (minimizeBtn) {
        minimizeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (isDragging) return;
            minimizeWidget();
        });
    }
    
    // Minimize button (floating)
    if (minimizeButton) {
        minimizeButton.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (isDragging) return;
            minimizeWidget();
        });
    }
    
    // Click minimized state to maximize
    if (minimizedState) {
        minimizedState.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (isDragging) return;
            maximizeWidget();
        });
        
        // Double-click for quick toggle
        minimizedState.addEventListener('dblclick', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (isDragging) return;
            // Double click already handled by single click
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Shift + T to toggle minimize
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
            e.preventDefault();
            if (isMinimized) {
                maximizeWidget();
            } else {
                minimizeWidget();
            }
        }
    });
}

function initializeDraggable() {
    const widget = document.getElementById('custom_translate_widget');
    const dragHandle = document.getElementById('drag_handle');
    const positionIndicator = document.getElementById('position_indicator');
    const indicatorText = document.getElementById('indicator_text');
    
    if (!widget || widget.dataset.draggable !== 'true') return;
    
    console.log('Initializing draggable functionality...');
    
    // Apply saved position on load
    setTimeout(applySavedPosition, 100);
    
    function startDrag(e) {
        // Don't start drag if clicking on functional areas
        if (e.target.closest('#language_toggle') || 
            e.target.closest('#language_dropdown') ||
            e.target.closest('#minimize_btn') ||
            e.target.closest('#minimize_button') ||
            (isMinimized && e.target.closest('#minimized_state'))) {
            return;
        }
        
        isDragging = true;
        
        // Get initial positions
        const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
        const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
        
        dragStartX = clientX;
        dragStartY = clientY;
        
        const rect = widget.getBoundingClientRect();
        widgetStartX = rect.left;
        widgetStartY = rect.top;
        
        // Visual feedback
        widget.classList.add('translate-widget-dragging');
        if (dragHandle) dragHandle.classList.add('drag-handle-active');
        if (positionIndicator) {
            positionIndicator.classList.remove('hidden');
            indicatorText.textContent = 'Dragging...';
        }
        
        // Close dropdown if open
        const dropdown = document.getElementById('language_dropdown');
        const arrow = document.getElementById('dropdown_arrow');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
        
        // Prevent text selection
        document.body.style.userSelect = 'none';
        document.body.style.webkitUserSelect = 'none';
        
        console.log('Drag started at:', { clientX, clientY });
        
        e.preventDefault();
    }
    
    function doDrag(e) {
        if (!isDragging) return;
        
        const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
        const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
        
        const deltaX = clientX - dragStartX;
        const deltaY = clientY - dragStartY;
        
        let newX = widgetStartX + deltaX;
        let newY = widgetStartY + deltaY;
        
        // Constrain to viewport
        const widgetRect = widget.getBoundingClientRect();
        const maxX = window.innerWidth - widgetRect.width;
        const maxY = window.innerHeight - widgetRect.height;
        
        newX = Math.max(0, Math.min(newX, maxX));
        newY = Math.max(0, Math.min(newY, maxY));
        
        // Apply position
        widget.style.left = newX + 'px';
        widget.style.top = newY + 'px';
        widget.style.right = 'auto';
        widget.style.bottom = 'auto';
        
        // Update position indicator
        if (indicatorText) {
            const relativeX = Math.round((newX / window.innerWidth) * 100);
            const relativeY = Math.round((newY / window.innerHeight) * 100);
            indicatorText.textContent = `${relativeX}%, ${relativeY}%`;
        }
        
        e.preventDefault();
    }
    
    function endDrag(e) {
        if (!isDragging) return;
        
        isDragging = false;
        
        // Remove visual feedback
        widget.classList.remove('translate-widget-dragging');
        if (dragHandle) dragHandle.classList.remove('drag-handle-active');
        if (positionIndicator) {
            setTimeout(() => {
                positionIndicator.classList.add('hidden');
            }, 1000);
            indicatorText.textContent = 'Position saved!';
        }
        
        // Restore text selection
        document.body.style.userSelect = '';
        document.body.style.webkitUserSelect = '';
        
        // Save final position
        const rect = widget.getBoundingClientRect();
        saveWidgetPosition(rect.left, rect.top);
        
        console.log('Drag ended, position saved:', { x: rect.left, y: rect.top });
        
        e.preventDefault();
    }
    
    // Mouse events
    widget.addEventListener('mousedown', startDrag);
    document.addEventListener('mousemove', doDrag);
    document.addEventListener('mouseup', endDrag);
    
    // Touch events for mobile
    widget.addEventListener('touchstart', startDrag, { passive: false });
    document.addEventListener('touchmove', doDrag, { passive: false });
    document.addEventListener('touchend', endDrag, { passive: false });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (!isDragging) {
            // Reposition widget if it's outside viewport after resize
            const rect = widget.getBoundingClientRect();
            const maxX = window.innerWidth - rect.width;
            const maxY = window.innerHeight - rect.height;
            
            if (rect.left > maxX || rect.top > maxY) {
                const newX = Math.max(16, Math.min(rect.left, maxX));
                const newY = Math.max(16, Math.min(rect.top, maxY));
                
                widget.style.left = newX + 'px';
                widget.style.top = newY + 'px';
                
                saveWidgetPosition(newX, newY);
            }
        }
    });
    
    // Double-click to reset position (only when not minimized)
    widget.addEventListener('dblclick', function(e) {
        if (e.target.closest('#language_toggle') || 
            e.target.closest('#language_dropdown') ||
            e.target.closest('#minimize_btn') ||
            e.target.closest('#minimize_button') ||
            isMinimized) {
            return;
        }
        
        // Reset to default position
        widget.style.left = '';
        widget.style.top = '';
        widget.style.right = '1rem';
        widget.style.bottom = '1rem';
        
        // Clear saved position
        localStorage.removeItem('translate_widget_position');
        
        if (positionIndicator && indicatorText) {
            positionIndicator.classList.remove('hidden');
            indicatorText.textContent = 'Reset to default!';
            setTimeout(() => {
                positionIndicator.classList.add('hidden');
            }, 2000);
        }
        
        console.log('Widget position reset to default');
    });
}

// Detect current page language state
function detectCurrentLanguage() {
    const hash = window.location.hash;
    const body = document.body;
    
    if (hash.includes('googtrans')) {
        if (hash.includes('|en')) {
            return 'en';
        }
        return 'id';
    }
    
    if (body.classList.contains('translated-ltr') || body.classList.contains('translated-rtl')) {
        const googleSelect = document.querySelector('.goog-te-combo');
        if (googleSelect && googleSelect.value === 'en') {
            return 'en';
        }
        return 'id';
    }
    
    return getSavedLanguage();
}

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'en,id',
        autoDisplay: false
    }, 'google_translate_element');
    
    translateInitialized = true;
    
    setTimeout(() => {
        const savedLang = getSavedLanguage();
        const currentDetected = detectCurrentLanguage();
        
        console.log('Saved language:', savedLang);
        console.log('Detected language:', currentDetected);
        
        if (savedLang === 'en' && currentDetected !== 'en') {
            console.log('Auto-applying English translation...');
            applyTranslation('en');
        }
        
        updateUIForLanguage(savedLang);
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('language_toggle');
    const dropdown = document.getElementById('language_dropdown');
    const arrow = document.getElementById('dropdown_arrow');
    const currentLangElement = document.getElementById('current_language');
    const currentFlagElement = document.getElementById('current_flag');
    const langOptions = document.querySelectorAll('.language_option');

    // Initialize draggable functionality
    initializeDraggable();
    
    // Initialize minimize functionality
    initializeMinimize();

    // Initialize UI with detected language
    const detectedLang = detectCurrentLanguage();
    updateUIForLanguage(detectedLang);
    console.log('Initial language detected:', detectedLang);

    // Toggle dropdown
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isDragging || isMinimized) return; // Don't toggle during drag or when minimized
        
        dropdown.classList.toggle('hidden');
        arrow.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isDragging || isMinimized) return;
        
        if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Handle language selection
    langOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (isDragging || isMinimized) return;
            
            const lang = this.dataset.lang;
            const flag = this.dataset.flag;
            const name = this.dataset.name;
            
            if (lang === currentLang) {
                dropdown.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
                return;
            }
            
            console.log('User selected language:', lang);
            
            isUserAction = true;
            saveLanguagePreference(lang);
            
            currentLangElement.textContent = name;
            currentFlagElement.textContent = flag;
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
            currentLang = lang;
            
            translatePage(lang);
        });
    });

    function translatePage(targetLang) {
        console.log('Translating to:', targetLang);
        
        if (targetLang === 'id') {
            resetToIndonesian();
        } else {
            applyTranslation(targetLang);
        }
    }

    function resetToIndonesian() {
        console.log('Resetting to Indonesian - Reloading page...');
        
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=/auto/en; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=/id/en; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        
        if (window.location.hash.includes('googtrans')) {
            const cleanUrl = window.location.href.split('#')[0];
            window.location.href = cleanUrl;
        } else {
            window.location.reload();
        }
    }

    function applyTranslation(targetLang) {
        console.log('Applying translation to:', targetLang);
        
        const checkGoogleTranslate = setInterval(() => {
            const googleSelect = document.querySelector('.goog-te-combo');
            if (googleSelect) {
                clearInterval(checkGoogleTranslate);
                
                console.log('Google Translate element found, applying translation...');
                
                googleSelect.value = targetLang;
                
                for (let i = 0; i < googleSelect.options.length; i++) {
                    if (googleSelect.options[i].value === targetLang) {
                        googleSelect.selectedIndex = i;
                        console.log('Selected option index:', i, 'value:', targetLang);
                        break;
                    }
                }
                
                googleSelect.dispatchEvent(new Event('change', { bubbles: true }));
                
                setTimeout(() => {
                    updateUIForLanguage(targetLang);
                    isUserAction = false;
                }, 1000);
            }
        }, 100);
        
        setTimeout(() => {
            clearInterval(checkGoogleTranslate);
            if (!document.querySelector('.goog-te-combo')) {
                console.log('Google Translate initialization timeout');
            }
            isUserAction = false;
        }, 10000);
    }

    function updateUIForLanguage(lang) {
        const currentLangElement = document.getElementById('current_language');
        const currentFlagElement = document.getElementById('current_flag');
        const minimizedFlag = document.getElementById('minimized_flag');
        
        if (lang === 'en') {
            currentLang = 'en';
            if (currentLangElement) currentLangElement.textContent = 'English';
            if (currentFlagElement) currentFlagElement.textContent = '🇺🇸';
            if (minimizedFlag) minimizedFlag.textContent = '🇺🇸';
        } else {
            currentLang = 'id';
            if (currentLangElement) currentLangElement.textContent = 'Indonesia';
            if (currentFlagElement) currentFlagElement.textContent = '🇮🇩';
            if (minimizedFlag) minimizedFlag.textContent = '🇮🇩';
        }
        
        console.log('UI updated for language:', lang);
    }

    // Enhanced state monitoring
    function monitorTranslationState() {
        if (!translateInitialized) return;
        
        const hash = window.location.hash;
        const body = document.body;
        const googleSelect = document.querySelector('.goog-te-combo');
        
        let detectedLang = 'id';
        
        if (hash.includes('googtrans') && hash.includes('|en')) {
            detectedLang = 'en';
        }
        else if (body.classList.contains('translated-ltr') || body.classList.contains('translated-rtl')) {
            if (googleSelect && googleSelect.value === 'en') {
                detectedLang = 'en';
            }
        }
        else if (googleSelect && googleSelect.value === 'en') {
            detectedLang = 'en';
        }
        
        if (detectedLang !== currentLang && !isUserAction) {
            console.log('State change detected:', currentLang, '->', detectedLang);
            updateUIForLanguage(detectedLang);
            saveLanguagePreference(detectedLang);
        }
    }

    window.addEventListener('hashchange', () => {
        console.log('Hash change detected');
        setTimeout(monitorTranslationState, 100);
    });
    
    setInterval(monitorTranslationState, 3000);
    
    setTimeout(() => {
        monitorTranslationState();
    }, 2000);
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && 
                mutation.target === document.body && 
                mutation.attributeName === 'class') {
                setTimeout(monitorTranslationState, 500);
            }
        });
    });
    
    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class']
    });

    // Handle window resize for mobile optimization
    window.addEventListener('resize', function() {
        if (!isMinimized) {
            const dropdown = document.getElementById('language_dropdown');
            const arrow = document.getElementById('dropdown_arrow');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }
    });
});

window.addEventListener('load', function() {
    console.log('Page loaded, checking language state...');
    setTimeout(() => {
        const detectedLang = detectCurrentLanguage();
        console.log('Final language detection on load:', detectedLang);
    }, 1000);
});
</script>
@endonce