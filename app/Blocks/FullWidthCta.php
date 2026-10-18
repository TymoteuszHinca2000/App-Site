<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class FullWidthCta extends Block
{
    public $name = 'CTA pełna szerokość';

    public $slug = 'full-width-cta';

    public $description = 'Sekcja CTA na pełną szerokość z obrazem w tle.';

    public $category = 'appandsite';

    public $icon = 'cover-image';

    public $keywords = [
        'cta',
        'banner',
        'pełna szerokość',
        'full width',
        'tło',
        'przycisk',
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
            'background' => get_field('background'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make(
            'full_width_cta'
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
                'label' => 'Przycisk',
                'return_format' => 'array',
            ])

            ->addImage('background', [
                'label' => 'Zdjęcie w tle',
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