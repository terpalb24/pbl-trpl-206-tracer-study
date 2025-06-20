@props([
    'position' => 'top-right',
    'languages' => 'en,id',
    'theme' => 'light',
    'draggable' => true
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

<!-- Draggable Language Switcher -->
<div id="custom_translate_widget" 
     class="fixed {{ $positionClasses }} z-50 {{ $themeClasses }} select-none {{ $draggable ? 'cursor-move' : '' }}"
     data-draggable="{{ $draggable ? 'true' : 'false' }}">
    
    <!-- Drag Handle (visible when draggable) -->
    @if($draggable)
        <div id="drag_handle" class="absolute -top-2 -left-2 w-6 h-6 bg-blue-500 rounded-full shadow-lg flex items-center justify-center cursor-grab active:cursor-grabbing opacity-70 hover:opacity-100 transition-opacity duration-200">
            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
            </svg>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden min-w-[120px] sm:min-w-[140px] max-w-[160px] sm:max-w-none transition-all duration-200 hover:shadow-xl">
        <button id="language_toggle" class="flex items-center justify-between w-full px-3 sm:px-4 py-2 sm:py-3 text-gray-700 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex items-center gap-1 sm:gap-2">
                <span id="current_flag" class="text-sm sm:text-base">🇮🇩</span>
                <span id="current_language" class="text-xs sm:text-sm font-medium truncate">Indonesia</span>
            </div>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" id="dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div id="language_dropdown" class="hidden border-t border-gray-100">
            <button class="language_option flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 text-gray-700 hover:bg-blue-50 transition-colors duration-200 w-full text-left" data-lang="id" data-flag="🇮🇩" data-name="Indonesia">
                <span class="text-sm sm:text-lg flex-shrink-0">🇮🇩</span>
                <span class="text-xs sm:text-sm truncate">Indonesia</span>
            </button>
            <button class="language_option flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-3 text-gray-700 hover:bg-blue-50 transition-colors duration-200 w-full text-left" data-lang="en" data-flag="🇺🇸" data-name="English">
                <span class="text-sm sm:text-lg flex-shrink-0">🇺🇸</span>
                <span class="text-xs sm:text-sm truncate">English</span>
            </button>
        </div>
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
/* Draggable Widget Styles */
.translate-widget-dragging {
    transition: none !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    transform: scale(1.05) !important;
    z-index: 9999 !important;
}

.translate-widget-dragging .bg-white {
    background-color: rgb(249 250 251) !important;
}

.drag-handle-active {
    background-color: rgb(34 197 94) !important;
    transform: scale(1.1) !important;
}

/* Smooth transitions for non-dragging state */
#custom_translate_widget:not(.translate-widget-dragging) {
    transition: all 0.3s ease !important;
}

/* Ensure widget stays within viewport */
#custom_translate_widget {
    max-width: calc(100vw - 1rem);
    max-height: calc(100vh - 1rem);
}

/* Mobile optimizations for dragging */
@media (max-width: 640px) {
    #drag_handle {
        width: 2rem !important;
        height: 2rem !important;
        top: -0.75rem !important;
        left: -0.75rem !important;
    }
    
    #drag_handle svg {
        width: 1rem !important;
        height: 1rem !important;
    }
    
    .translate-widget-dragging {
        transform: scale(1.02) !important;
    }
}

/* Theme variations */
.translate-widget-dark .bg-white {
    background-color: rgb(31 41 55) !important;
    border-color: rgb(75 85 99) !important;
}

.translate-widget-dark .text-gray-700 {
    color: rgb(209 213 219) !important;
}

.translate-widget-dark .hover\:bg-gray-50:hover {
    background-color: rgb(55 65 81) !important;
}

.translate-widget-transparent .bg-white {
    background-color: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(8px) !important;
}
</style>

<script type="text/javascript">
let currentLang = 'id';
let isUserAction = false;
let translateInitialized = false;

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
        if (e.target.closest('#language_toggle') || e.target.closest('#language_dropdown')) {
            return; // Don't start drag if clicking on functional areas
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
    
    // Double-click to reset position
    widget.addEventListener('dblclick', function(e) {
        if (e.target.closest('#language_toggle') || e.target.closest('#language_dropdown')) {
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

    // Initialize UI with detected language
    const detectedLang = detectCurrentLanguage();
    updateUIForLanguage(detectedLang);
    console.log('Initial language detected:', detectedLang);

    // Toggle dropdown
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isDragging) return; // Don't toggle during drag
        
        dropdown.classList.toggle('hidden');
        arrow.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isDragging) return;
        
        if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Handle language selection
    langOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (isDragging) return;
            
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
        
        if (lang === 'en') {
            currentLang = 'en';
            if (currentLangElement) currentLangElement.textContent = 'English';
            if (currentFlagElement) currentFlagElement.textContent = '🇺🇸';
        } else {
            currentLang = 'id';
            if (currentLangElement) currentLangElement.textContent = 'Indonesia';
            if (currentFlagElement) currentFlagElement.textContent = '🇮🇩';
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
        const dropdown = document.getElementById('language_dropdown');
        const arrow = document.getElementById('dropdown_arrow');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
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

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@endonce