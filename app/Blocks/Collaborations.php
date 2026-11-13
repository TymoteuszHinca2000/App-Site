<?php

namespace App\Blocks;

use App\Support\Collaborations as CollaborationData;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Collaborations extends Block
{
    public $name = 'Współprace';

    public $slug = 'collaborations';

    public $description = 'Sekcja współprac pobieranych z Opcje globalne → Współprace.';

    public $category = 'appandsite';

    public $icon = 'networking';

    public $keywords = ['współprace', 'partnerzy', 'oferty', 'kod rabatowy'];

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
            'collaborations' => CollaborationData::all(),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('collaborations_block');

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
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
