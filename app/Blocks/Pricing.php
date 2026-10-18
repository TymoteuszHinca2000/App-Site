<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Pricing extends Block
{
    public $name = 'Cennik';

    public $slug = 'pricing';

    public $description = 'Sekcja z opisem usługi i kartą cenową.';

    public $category = 'appandsite';

    public $icon = 'money-alt';

    public $keywords = [
        'cennik',
        'cena',
        'pakiet',
        'pricing',
        'price',
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
            'price_prefix' => get_field('price_prefix'),
            'price_title' => get_field('price_title'),
            'price_content' => get_field('price_content'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('pricing_block');

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

            ->addText('price_prefix', [
                'label' => 'Prefix cennika',
            ])

            ->addWysiwyg('price_title', [
                'label' => 'Tytuł / cena',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])

            ->addWysiwyg('price_content', [
                'label' => 'Lista punktowana',
                'instructions' => 'Dodaj listę punktowaną w edytorze WYSIWYG.',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}