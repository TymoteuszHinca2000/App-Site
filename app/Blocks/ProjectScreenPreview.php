<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ProjectScreenPreview extends Block
{
    public $name = 'Podgląd strony w monitorze';

    public $slug = 'project-screen-preview';

    public $description = 'Interaktywny podgląd projektu z płynnym przewijaniem strony w monitorze.';

    public $category = 'appandsite';

    public $icon = 'desktop';

    public $keywords = ['projekt', 'realizacja', 'monitor', 'podgląd', 'strona internetowa'];

    public $post_types = ['project'];

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
            'monitorImage' => get_field('monitor_image'),
            'screenImage' => get_field('screen_image'),
            'scrollHint' => get_field('scroll_hint') ?: __('Przewiń stronę w monitorze', 'appandsite'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('project_screen_preview');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
                'default_value' => 'PODGLĄD REALIZACJI',
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
            ->addImage('monitor_image', [
                'label' => 'Monitor z przezroczystym ekranem',
                'instructions' => 'Wstaw monitor PNG/SVG o proporcjach 1397 × 1041. Środek ekranu musi być przezroczysty.',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ])
            ->addImage('screen_image', [
                'label' => 'Zrzut całej strony internetowej',
                'instructions' => 'Wstaw pełny, pionowy screenshot witryny. Najlepiej o szerokości 1440–1920 px.',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ])
            ->addText('scroll_hint', [
                'label' => 'Tekst zachęcający do przewijania',
                'default_value' => 'Przewiń stronę w monitorze',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
