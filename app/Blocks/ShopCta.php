<?php

namespace App\Blocks;

use App\Support\ShopOrderData;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ShopCta extends Block
{
    public $name = 'CTA motywu';

    public $slug = 'shop-cta';

    public $description = 'CTA z linkiem zamówienia używanym również w listingu sklepu.';

    public $category = 'appandsite';

    public $icon = 'megaphone';

    public $keywords = ['sklep', 'motyw', 'zamówienie', 'cta'];

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
        'multiple' => false,
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
            'price' => get_field('price'),
            'orderButton' => ShopOrderData::link(get_field('order_url', false, false)),
            'buttonSecondary' => get_field('button_secondary'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('shop_cta');

        $fields
            ->addText('prefix', ['label' => 'Prefix'])
            ->addWysiwyg('title', ['label' => 'Tytuł H2', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addText('price', [
                'label' => 'Cena',
                'instructions' => 'Dowolny tekst, np. 1000 zł lub 1000 funtów. Ta sama cena pojawi się na kaflu w listingu sklepu.',
            ])
            ->addLink('order_url', [
                'label' => 'Odnośnik',
                'return_format' => 'array',
            ])
            ->addLink('button_secondary', [
                'label' => 'Przycisk dodatkowy (opcjonalnie)',
                'return_format' => 'array',
            ]);

        return $fields->build();
    }
}
