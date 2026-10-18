<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class CompanyStats extends Block
{
    public $name = 'Pasek informacji';

    public $slug = 'company-stats';

    public $description = 'Pasek najważniejszych informacji z ikoną, tytułem i opisem.';

    public $category = 'appandsite';

    public $icon = 'info-outline';

    public $keywords = [
        'statystyki',
        'informacje',
        'liczby',
        'ikony',
        'usp',
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
            'items' => get_field('items') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('company_stats');

        $fields
            ->addRepeater('items', [
                'label' => 'Elementy',
                'layout' => 'block',
                'button_label' => 'Dodaj element',
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