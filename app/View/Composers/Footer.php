<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Footer extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var string[]
     */
    protected static $views = [
        'sections/footer',
    ];

    public function with()
    {
        return [
            'footer' => get_field('footer', 'options'),
        ];
    }
}
