<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class AboutCompany extends Block
{
    public $name = 'O App&Site';

    public $slug = 'about-company';

    public $description = 'Sekcja o firmie z opisem, zdjęciem i wyróżnikami.';

    public $category = 'appandsite';

    public $icon = 'businessperson';

    public $keywords = [
        'o firmie',
        'o mnie',
        'about',
        'appandsite',
        'firma',
    ];

    public $post_types = [];

    public $blockVersion = 3;

    public $mode = 'preview';

    public $align = '';

    public $align_text = '';

    public $align_content = '';

    public $supports = [
        'align' => false,
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        'anchor' => true,
        'mode' => true,
        'multiple' => true,
        'jsx' => false,
        'color' => [
            'background' => false,
            'text' => false,
            'gradients' => false,
        ],
        'spacing' => [
            'padding' => false,
            'margin' => false,
        ],
    ];

    public function with(): array
    {
        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'button' => get_field('button'),
            'image' => get_field('image'),
            'items' => get_field('items') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('about_company');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
            ])
            ->addWysiwyg('title', [
                'label' => 'Tytuł',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addWysiwyg('description', [
                'label' => 'Opis',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addLink('button', [
                'label' => 'Przycisk',
                'return_format' => 'array',
            ])
            ->addImage('image', [
                'label' => 'Zdjęcie',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->addRepeater('items', [
                'label' => 'Wyróżniki',
                'layout' => 'block',
                'button_label' => 'Dodaj wyróżnik',
                'min' => 1,
                'max' => 4,
            ])
                ->addImage('image', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])
                ->addText('title', [
                    'label' => 'Tytuł',
                ])
                ->addText('description', [
                    'label' => 'Opis',
                ])
            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}