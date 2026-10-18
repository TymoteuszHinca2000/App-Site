<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class RelatedOffers extends Block
{
    public $name = 'Pozostałe oferty';

    public $slug = 'related-offers';

    public $description = 'Slider z ofertami z możliwością ustawienia kolejności.';

    public $category = 'appandsite';

    public $icon = 'screenoptions';

    public $keywords = [
        'oferty',
        'pozostałe oferty',
        'powiązane usługi',
        'related offers',
        'swiper',
        'slider',
    ];

    public $post_types = [
        'page',
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
            'limit' => get_field('limit'),
            'offers' => get_field('offers') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make(
            'related_offers_block'
        );

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

            ->addLink('button', [
                'label' => 'Odnośnik',
                'instructions' => 'Opcjonalny przycisk wyświetlany po prawej stronie nagłówka sekcji.',
                'return_format' => 'array',
            ])

            ->addNumber('limit', [
                'label' => 'Maksymalna liczba ofert',
                'instructions' => 'Np. 6. Pozostaw puste lub wpisz 0, aby wyświetlić wszystkie oferty.',
                'min' => 0,
                'step' => 1,
                'default_value' => '',
                'placeholder' => 'Wszystkie',
            ])

            ->addRelationship('offers', [
                'label' => 'Kolejność ofert',
                'instructions' => 'Wybrane tutaj oferty pojawią się jako pierwsze i dokładnie w ustawionej kolejności. Pozostałe oferty zostaną automatycznie dodane na końcu.',
                'post_type' => [
                    'offer',
                ],
                'filters' => [
                    'search',
                ],
                'elements' => [
                    'featured_image',
                ],
                'return_format' => 'id',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}