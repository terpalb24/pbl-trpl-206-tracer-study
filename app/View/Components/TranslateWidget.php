<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TranslateWidget extends Component
{
    public $position;
    public $languages;
    public $theme;

    /**
     * Create a new component instance.
     *
     * @param string $position
     * @param array $languages
     * @param string $theme
     */
    public function __construct($position = 'top-right', $languages = ['en', 'id'], $theme = 'light')
    {
        $this->position = $position;
        $this->languages = implode(',', $languages);
        $this->theme = $theme;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.translate-widget');
    }
}