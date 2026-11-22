<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ShopFeatures extends Block
{
    public $name = 'Cechy motywu';

    public $slug = 'shop-features';

    public $description = 'Sekcja z nagłówkiem, opisem i kaflami z ikonami dla motywu sklepu.';

    public $category = 'appandsite';

    public $icon = 'screenoptions';

    public $keywords = ['sklep', 'motyw', 'cechy', 'kafle', 'ikony'];

    public $post_types = ['shop', 'page'];

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
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'features' => get_field('features') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('shop_features');

        $fields
            ->addWysiwyg('prefix', ['label' => 'Prefix', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('title', ['label' => 'Tytuł H2', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addRepeater('features', [
                'label' => 'Kafle',
                'layout' => 'block',
                'button_label' => 'Dodaj kafel',
            ])
                ->addImage('icon', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])
                ->addWysiwyg('title', ['label' => 'Tytuł H3', 'toolbar' => 'basic', 'media_upload' => 0])
                ->addWysiwyg('description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->endRepeater();

        return $fields->build();
    }
}
