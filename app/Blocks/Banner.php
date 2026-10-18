<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Banner extends Block
{
    public $name = 'Banner';

    public $slug = 'banner';

    public $description = 'Banner podstron z breadcrumbami, treścią, elementami oraz tłem zdjęciowym lub filmowym.';

    public $category = 'appandsite';

    public $icon = 'cover-image';

    public $keywords = [
        'banner',
        'hero',
        'podstrona',
        'galeria',
        'video',
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
            'title' => get_field('title'),
            'description' => get_field('description'),
            'items' => get_field('items') ?: [],
            'backgroundType' => get_field('background_type'),
            'backgroundGallery' => get_field('background_gallery') ?: [],
            'backgroundVideo' => get_field('background_video'),
            'backgroundVideoPoster' => get_field('background_video_poster'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('banner');

        $fields
            ->addWysiwyg('title', [
                'label' => 'Tytuł H1',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addWysiwyg('description', [
                'label' => 'Opis',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addRepeater('items', [
                'label' => 'Elementy',
                'layout' => 'block',
                'button_label' => 'Dodaj element',
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
                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 2,
                    'new_lines' => '',
                ])
            ->endRepeater()
            ->addSelect('background_type', [
                'label' => 'Typ tła',
                'choices' => [
                    'gallery' => 'Galeria zdjęć',
                    'video' => 'Film',
                ],
                'default_value' => 'gallery',
                'ui' => 1,
            ])
            ->addGallery('background_gallery', [
                'label' => 'Galeria tła',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->conditional('background_type', '==', 'gallery')
            ->addFile('background_video', [
                'label' => 'Film w tle',
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'mp4,webm',
            ])
            ->conditional('background_type', '==', 'video')
            ->addImage('background_video_poster', [
                'label' => 'Poster filmu',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->conditional('background_type', '==', 'video');

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}