<?php

namespace App\View\Components\BlockHelper;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BlockWrapper extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $block,
        public $attributes,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.block-helper.block-wrapper');
    }
}
