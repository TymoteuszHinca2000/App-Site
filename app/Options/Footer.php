<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class Footer extends Field
{
    public $name = 'Stopka';

    public $slug = 'footer';

    public $title = 'Opcje globalne - Stopka';

    public $capability = 'edit_theme_options';

    public $position = 2;

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
        return __('Footer Updated', 'acf');
    }

    public function fields(): array
    {
        $fields = Builder::make('footer')
            ->setGroupConfig('title', 'Stopka');

        $fields
            ->addGroup('footer', [
                'label' => 'Stopka',
                'layout' => 'block',
            ])

                ->addImage('logo', [
                    'label' => 'Logo',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ])

                ->addTextarea('description', [
                    'label' => 'Opis pod logo',
                    'rows' => 3,
                    'new_lines' => 'br',
                ])

                ->addRepeater('social_media', [
                    'label' => 'Social media',
                    'layout' => 'table',
                    'button_label' => 'Dodaj social media',
                ])

                    ->addSelect('type', [
                        'label' => 'Serwis',
                        'choices' => [
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'linkedin' => 'LinkedIn',
                            'youtube' => 'YouTube',
                            'tiktok' => 'TikTok',
                            'x' => 'X / Twitter',
                        ],
                        'ui' => 1,
                    ])

                    ->addLink('link', [
                        'label' => 'Link',
                        'return_format' => 'array',
                    ])

                ->endRepeater()

                ->addText('copyright', [
                    'label' => 'Copyright',
                    'instructions' => 'Np. © 2026 Sites&App. Wszelkie prawa zastrzeżone.',
                ])

                ->addLink('privacy_policy', [
                    'label' => 'Polityka prywatności',
                    'return_format' => 'array',
                ])

            ->endGroup();

        return $fields->build();
    }
}