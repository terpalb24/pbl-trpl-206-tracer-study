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

<!-- Google Translate Widget -->
<div id="google_translate_element" 
     class="fixed {{ $positionClasses }} z-50 {{ $themeClasses }}" 
     data-languages="{{ $languages }}"
     {{ $attributes }}>
</div>

@once
<script type="text/javascript">
function googleTranslateElementInit() {
    const element = document.getElementById('google_translate_element');
    const languages = element ? element.dataset.languages : 'en,id';
    
    new google.translate.TranslateElement({
        includedLanguages: languages,
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@endonce