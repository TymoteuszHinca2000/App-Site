<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class Header extends Field
{
    public $name = 'Header';

    public $slug = 'header';

    public $title = 'Opcje globalne - Header';

    public $capability = 'edit_theme_options';

    public $position = 1;

    public $menu = true;

    public $parent = 'theme';

    public $icon = null;

    public $redirect = true;

    public $post = 'options';

    public $autoload = true;

    public $settings = [];

    public function updateButton(): string
    {
        return __('Update', 'acf');
    }

    public function updatedMessage(): string
    {
        return __('Header Updated', 'acf');
    }

    public function fields(): array
    {
        $fields = Builder::make('header');

        $fields
            ->addGroup('header', [
                'label' => 'Header',
                'layout' => 'block',
            ])
                ->addImage('logo', [
                    'label' => 'Logo',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ])
                ->addLink('button', [
                    'label' => 'Przycisk',
                    'return_format' => 'array',
                ])
            ->endGroup();

        return $fields->build();
    }
}