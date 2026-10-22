<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class AboutGallery extends Block
{
    public $name = 'O mnie - galeria';

    public $slug = 'about-gallery';

    public $description = 'Sekcja z opisem, wyróżnikami i galerią zdjęć z lightboxem.';

    public $category = 'appandsite';

    public $icon = 'format-gallery';

    public $keywords = [
        'o mnie',
        'galeria',
        'zdjęcia',
        'wyróżniki',
        'about',
        'gallery',
    ];

    public $post_types = [
        'page',
        'offer',
        'project',
    ];

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
            'features' => get_field('features') ?: [],
            'galleryItems' => get_field('gallery_items') ?: [],
            'reverseLayout' => (bool) get_field('reverse_layout'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('about_gallery');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
            ])

            ->addWysiwyg('title', [
                'label' => 'Tytuł H2',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addWysiwyg('description', [
                'label' => 'Opis',
                'toolbar' => 'full',
                'media_upload' => 0,
            ])

            ->addTrueFalse('reverse_layout', [
                'label' => 'Odwróć układ',
                'instructions' => 'Po włączeniu galeria znajdzie się po lewej, a treść po prawej.',
                'ui' => 1,
                'default_value' => 0,
            ])

            ->addRepeater('features', [
                'label' => 'Wyróżniki pod opisem',
                'layout' => 'block',
                'button_label' => 'Dodaj wyróżnik',
                'max' => 8,
            ])
                ->addImage('icon', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])

                ->addText('title', [
                    'label' => 'Tytuł ikony',
                ])

                ->addTextarea('description', [
                    'label' => 'Opis ikony',
                    'rows' => 3,
                ])
            ->endRepeater()

            ->addRepeater('gallery_items', [
                'label' => 'Galeria zdjęć',
                'layout' => 'block',
                'button_label' => 'Dodaj zdjęcie',
                'min' => 1,
                'max' => 12,
            ])
                ->addImage('image', [
                    'label' => 'Zdjęcie',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'required' => 1,
                ])

                ->addImage('icon', [
                    'label' => 'Ikona na zdjęciu',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])

                ->addText('title', [
                    'label' => 'Tytuł przy ikonie',
                ])

                ->addTextarea('description', [
                    'label' => 'Opis na zdjęciu',
                    'rows' => 3,
                ])
            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
