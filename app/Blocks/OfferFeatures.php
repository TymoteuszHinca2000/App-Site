<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class OfferFeatures extends Block
{
    public $name = 'Najważniejsze cechy';

    public $slug = 'offer-features';

    public $description = 'Sekcja najważniejszych cech pojedynczej oferty.';

    public $category = 'appandsite';

    public $icon = 'yes-alt';

    public $keywords = [
        'oferta',
        'cechy',
        'zalety',
        'features',
    ];

    /**
     * Blok dostępny tylko dla CPT Oferta.
     */
    public $post_types = [
        'offer',
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
        'multiple' => false,
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
            'image' => get_field('image'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('offer_features_block');

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
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addRepeater('features', [
                'label' => 'Cechy',
                'layout' => 'table',
                'button_label' => 'Dodaj cechę',
                'min' => 1,
            ])
                ->addText('text', [
                    'label' => 'Cecha',
                ])
            ->endRepeater()

            ->addImage('image', [
                'label' => 'Zdjęcie',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}