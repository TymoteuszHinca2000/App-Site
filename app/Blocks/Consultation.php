<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Consultation extends Block
{
    public $name = 'CTA konsultacja';

    public $slug = 'consultation';

    public $description = 'CTA z treścią, zdjęciem oraz rotującymi tekstami.';

    public $category = 'appandsite';

    public $icon = 'megaphone';

    public $keywords = [
        'cta',
        'konsultacja',
        'kontakt',
        'banner',
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
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'buttonPrimary' => get_field('button_primary'),
            'buttonSecondary' => get_field('button_secondary'),
            'image' => get_field('image'),
            'imageCaption' => get_field('image_caption'),
            'rotatingTexts' => get_field('rotating_texts') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('consultation');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
                'instructions' => 'Np. MASZ PYTANIA?',
            ])

            ->addWysiwyg('title', [
                'label' => 'Tytuł',
                'instructions' => 'Zaznacz pogrubieniem fragment, który ma być turkusowy.',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addWysiwyg('description', [
                'label' => 'Opis',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addLink('button_primary', [
                'label' => 'Przycisk główny',
                'return_format' => 'array',
            ])

            ->addLink('button_secondary', [
                'label' => 'Przycisk dodatkowy',
                'return_format' => 'array',
            ])

            ->addImage('image', [
                'label' => 'Zdjęcie',
                'instructions' => 'Najlepiej PNG/WebP z przezroczystym tłem.',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])

            ->addTextarea('image_caption', [
                'label' => 'Podpis obrazka',
                'instructions' => "Np.\nTymoteusz Hinca\nWłaściciel App&Site",
                'rows' => 3,
                'new_lines' => '',
            ])

            ->addRepeater('rotating_texts', [
                'label' => 'Rotujące teksty',
                'instructions' => 'Teksty zmieniają się automatycznie co 3 sekundy.',
                'layout' => 'block',
                'button_label' => 'Dodaj tekst',
                'min' => 1,
            ])
                ->addTextarea('text', [
                    'label' => 'Tekst',
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