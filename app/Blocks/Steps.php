<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Steps extends Block
{
    public $name = 'Etapy';

    public $slug = 'steps';

    public $description = 'Sekcja z etapami realizacji.';

    public $category = 'appandsite';

    public $icon = 'editor-ol';

    public $keywords = [
        'etapy',
        'proces',
        'kroki',
        'steps',
        'process',
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
            'steps' => get_field('steps') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('steps_block');

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

            ->addRepeater('steps', [
                'label' => 'Etapy',
                'layout' => 'block',
                'button_label' => 'Dodaj etap',
                'min' => 1,
            ])
                ->addText('title', [
                    'label' => 'Tytuł',
                ])

                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 3,
                    'new_lines' => 'br',
                ])

            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}