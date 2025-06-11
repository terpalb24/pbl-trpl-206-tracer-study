@props([
    'position' => 'top-right',
    'languages' => 'en,id',
    'theme' => 'light'
])

@php
$positionClasses = match($position) {
    'top-left' => 'top-20 left-4',
    'top-right' => 'top-20 right-4',
    'bottom-left' => 'bottom-4 left-4',
    'bottom-right' => 'bottom-4 right-4',
    default => 'top-20 right-4'
};

$themeClasses = match($theme) {
    'dark' => 'translate-widget-dark',
    'light' => 'translate-widget-light',
    'transparent' => 'translate-widget-transparent',
    default => 'translate-widget-light'
};
@endphp

<!-- Custom Language Switcher -->
<div id="custom_translate_widget" class="fixed {{ $positionClasses }} z-50 {{ $themeClasses }}">
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden min-w-[140px]">
        <button id="language_toggle" class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-2">
                <span id="current_flag">🇮🇩</span>
                <span id="current_language" class="text-sm font-medium">Indonesia</span>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform" id="dropdown_arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div id="language_dropdown" class="hidden border-t border-gray-100">
            <button class="language_option flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-blue-50 transition-colors w-full text-left" data-lang="id" data-flag="🇮🇩" data-name="Indonesia">
                <span class="text-lg">🇮🇩</span>
                <span class="text-sm">Indonesia</span>
            </button>
            <button class="language_option flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-blue-50 transition-colors w-full text-left" data-lang="en" data-flag="🇺🇸" data-name="English">
                <span class="text-lg">🇺🇸</span>
                <span class="text-sm">English</span>
            </button>
        </div>
    </div>
</div>

<!-- Hidden Google Translate Element -->
<div id="google_translate_element" style="display: none !important;"></div>

@once
<script type="text/javascript">
let currentLang = 'id';
let isUserAction = false;
let translateInitialized = false;

// Language preference management
function getSavedLanguage() {
    return localStorage.getItem('preferred_language') || 'id';
}

function saveLanguagePreference(lang) {
    localStorage.setItem('preferred_language', lang);
    console.log('Language preference saved:', lang);
}

// Detect current page language state
function detectCurrentLanguage() {
    const hash = window.location.hash;
    const body = document.body;
    
    // Check URL hash first
    if (hash.includes('googtrans')) {
        if (hash.includes('|en')) {
            return 'en';
        }
        return 'id';
    }
    
    // Check body classes for translation state
    if (body.classList.contains('translated-ltr') || body.classList.contains('translated-rtl')) {
        const googleSelect = document.querySelector('.goog-te-combo');
        if (googleSelect && googleSelect.value === 'en') {
            return 'en';
        }
        return 'id';
    }
    
    // Default to saved preference
    return getSavedLanguage();
}

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'en,id',
        autoDisplay: false
    }, 'google_translate_element');
    
    translateInitialized = true;
    
    // Apply saved language after initialization
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

    // Initialize UI with detected language
    const detectedLang = detectCurrentLanguage();
    updateUIForLanguage(detectedLang);
    console.log('Initial language detected:', detectedLang);

    // Toggle dropdown
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
        arrow.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Handle language selection
    langOptions.forEach(option => {
        option.addEventListener('click', function() {
            const lang = this.dataset.lang;
            const flag = this.dataset.flag;
            const name = this.dataset.name;
            
            if (lang === currentLang) {
                dropdown.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
                return;
            }
            
            console.log('User selected language:', lang);
            
            // Mark as user action and save preference
            isUserAction = true;
            saveLanguagePreference(lang);
            
            // Update UI immediately
            currentLangElement.textContent = name;
            currentFlagElement.textContent = flag;
            dropdown.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
            currentLang = lang;
            
            // Translate
            translatePage(lang);
        });
    });

    function translatePage(targetLang) {
        console.log('Translating to:', targetLang);
        
        if (targetLang === 'id') {
            // Reset to Indonesian - Always reload page for guaranteed reset
            resetToIndonesian();
        } else {
            // Translate to target language
            applyTranslation(targetLang);
        }
    }

    function resetToIndonesian() {
        console.log('Resetting to Indonesian - Reloading page...');
        
        // Clear all Google Translate state before reload
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=/auto/en; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        document.cookie = 'googtrans=/id/en; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        
        // Clear URL hash
        if (window.location.hash.includes('googtrans')) {
            const cleanUrl = window.location.href.split('#')[0];
            window.location.href = cleanUrl;
        } else {
            // Force reload even if no hash
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
                console.log('Available options:', googleSelect.options.length);
                
                // Debug: Log all available options
                for (let i = 0; i < googleSelect.options.length; i++) {
                    console.log('Option', i, ':', googleSelect.options[i].value, googleSelect.options[i].text);
                }
                
                // Set to target language
                googleSelect.value = targetLang;
                
                // Find and select target language option
                for (let i = 0; i < googleSelect.options.length; i++) {
                    if (googleSelect.options[i].value === targetLang) {
                        googleSelect.selectedIndex = i;
                        console.log('Selected option index:', i, 'value:', targetLang);
                        break;
                    }
                }
                
                // Trigger change event
                googleSelect.dispatchEvent(new Event('change', { bubbles: true }));
                
                // Update UI after translation
                setTimeout(() => {
                    updateUIForLanguage(targetLang);
                    isUserAction = false;
                }, 1000);
            }
        }, 100);
        
        // Timeout fallback
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
        
        // Priority 1: Check URL hash
        if (hash.includes('googtrans') && hash.includes('|en')) {
            detectedLang = 'en';
        }
        // Priority 2: Check body classes and Google select
        else if (body.classList.contains('translated-ltr') || body.classList.contains('translated-rtl')) {
            if (googleSelect && googleSelect.value === 'en') {
                detectedLang = 'en';
            }
        }
        // Priority 3: Check Google select value
        else if (googleSelect && googleSelect.value === 'en') {
            detectedLang = 'en';
        }
        
        // Update UI and save preference if changed
        if (detectedLang !== currentLang && !isUserAction) {
            console.log('State change detected:', currentLang, '->', detectedLang);
            updateUIForLanguage(detectedLang);
            saveLanguagePreference(detectedLang);
        }
    }

    // Monitor changes
    window.addEventListener('hashchange', () => {
        console.log('Hash change detected');
        setTimeout(monitorTranslationState, 100);
    });
    
    // Periodic state check
    setInterval(monitorTranslationState, 3000);
    
    // Initial state check
    setTimeout(() => {
        monitorTranslationState();
    }, 2000);
    
    // Monitor DOM mutations for translation state changes
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
});

// Initialize on page load
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