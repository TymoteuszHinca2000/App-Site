<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class SingleFaq extends Block
{
    public $name = 'Single FAQ';

    public $slug = 'single-faq';

    public $description = 'FAQ przypisane do pojedynczej podstrony.';

    public $category = 'appandsite';

    public $icon = 'editor-help';

    public $keywords = [
        'single faq',
        'faq',
        'pytania',
        'odpowiedzi',
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
            'items' => get_field('items') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('single_faq_block');

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

            ->addRepeater('items', [
                'label' => 'Pytania FAQ',
                'layout' => 'block',
                'button_label' => 'Dodaj pytanie',
                'min' => 1,
            ])
                ->addText('question', [
                    'label' => 'Pytanie',
                    'required' => 1,
                ])

                ->addWysiwyg('answer', [
                    'label' => 'Odpowiedź',
                    'toolbar' => 'full',
                    'media_upload' => 0,
                ])

            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}