<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ShopAbout extends Block
{
    public $name = 'O motywie';

    public $slug = 'shop-about';

    public $description = 'Opis motywu z kaflami i bocznym panelem treści.';

    public $category = 'appandsite';

    public $icon = 'align-pull-right';

    public $keywords = ['sklep', 'motyw', 'opis', 'kafle', 'zawartość'];

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
            'panelTitle' => get_field('panel_title'),
            'panelDescription' => get_field('panel_description'),
            'panelButton' => get_field('panel_button'),
            'panelCaption' => get_field('panel_caption'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('shop_about');

        $fields
            ->addTab('content_tab', ['label' => 'Treść i kafle'])
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
            ->endRepeater()
            ->addTab('panel_tab', ['label' => 'Panel boczny'])
            ->addWysiwyg('panel_title', ['label' => 'Tytuł H3', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('panel_description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addLink('panel_button', ['label' => 'Odnośnik', 'return_format' => 'array'])
            ->addWysiwyg('panel_caption', ['label' => 'Podpis', 'toolbar' => 'basic', 'media_upload' => 0]);

        return $fields->build();
    }
}
