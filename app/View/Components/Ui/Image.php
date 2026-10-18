<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Image extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?array $image = null,
        public ?string $class = null,
        public bool $priority = false,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if ($this->image == null) {
            return '';
        }

        return view('components.ui.image', [
            'imageCode' => wp_get_attachment_image(
                attachment_id: $this->image['id'],
                icon: $this->isIcon($this->image['url']),
                attr: [
                    'class' => $this->class,
                    'alt' => $this->image['alt'],
                    'loading' => $this->priority ? false : 'loading',
                ]
            )
        ]);
    }

    private function isIcon(string $url): bool
    {
//        check is url is svg
        return Str::endsWith($url, '.svg');
    }
}
