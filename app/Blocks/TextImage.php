<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class TextImage extends Block
{
    public $name = 'Text Image';

    public $slug = 'text-image';

    public $description = 'Sekcja tekstowa ze zdjęciem.';

    public $category = 'appandsite';

    public $icon = 'align-pull-left';

    public $keywords = [
        'tekst',
        'zdjęcie',
        'text',
        'image',
        'content',
    ];

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
            'content' => get_field('content'),
            'image' => get_field('image'),
            'reverseLayout' => (bool) get_field('reverse_layout'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('text_image');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
            ])

            ->addWysiwyg('title', [
                'label' => 'Tytuł H2',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addWysiwyg('content', [
                'label' => 'Treść',
                'instructions' => 'Dodaj opis oraz listę punktowaną.',
                'toolbar' => 'full',
                'media_upload' => 0,
            ])

            ->addImage('image', [
                'label' => 'Zdjęcie',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])

            ->addTrueFalse('reverse_layout', [
                'label' => 'Odwróć układ',
                'instructions' => 'Po włączeniu zdjęcie będzie po lewej stronie, a treść po prawej.',
                'ui' => 1,
                'default_value' => 0,
                'ui_on_text' => 'Tak',
                'ui_off_text' => 'Nie',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}