<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class HistoryTimeline extends Block
{
    public $name = 'Historia - timeline';

    public $slug = 'history-timeline';

    public $description = 'Pionowa oś czasu z etapami rozwoju firmy.';

    public $category = 'appandsite';

    public $icon = 'clock';

    public $keywords = [
        'historia',
        'timeline',
        'oś czasu',
        'etapy',
        'rozwój',
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
            'items' => get_field('items') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('history_timeline');

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

            ->addRepeater('items', [
                'label' => 'Etapy historii',
                'layout' => 'block',
                'button_label' => 'Dodaj etap',
                'min' => 1,
            ])
                ->addText('date', [
                    'label' => 'Data',
                ])

                ->addImage('icon', [
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
                    'rows' => 4,
                ])
            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}