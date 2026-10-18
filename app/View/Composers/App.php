<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    public function with()
    {
        return [
            'theme' => get_field('theme', 'options'),
            'menuMobile' => $this->getMenuMobile(),
        ];
    }

    private function getMenuMobile()
    {
        $menuMobileItems = [];

        return $menuMobileItems;
    }
}
