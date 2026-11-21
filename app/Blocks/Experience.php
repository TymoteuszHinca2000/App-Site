<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Experience extends Block
{
    public $name = 'Doświadczenie';

    public $slug = 'experience';

    public $description = 'Sekcja z obrazem w tle, prefixem, nagłówkiem i edytorem treści.';

    public $category = 'appandsite';

    public $icon = 'awards';

    public $keywords = ['doświadczenie', 'osiągnięcia', 'experience', 'tło'];

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
        'color' => ['background' => false, 'text' => false, 'gradients' => false],
        'spacing' => ['padding' => false, 'margin' => false],
    ];

    public function with(): array
    {
        return [
            'background' => get_field('background'),
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('experience');

        $fields
            ->addImage('background', [
                'label' => 'Zdjęcie w tle',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->addText('prefix', ['label' => 'Prefix'])
            ->addWysiwyg('title', [
                'label' => 'Tytuł H2',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addWysiwyg('description', [
                'label' => 'Treść',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ]);

        return $fields->build();
    }
}
