<?php

namespace App\Blocks;

use App\Support\ShopOrderData;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ShopListing extends Block
{
    public $name = 'Listing sklepu';

    public $slug = 'shop-listing';

    public $description = 'Lista opublikowanych motywów WordPress bez kategorii i paginacji.';

    public $category = 'appandsite';

    public $icon = 'store';

    public $keywords = ['sklep', 'motywy', 'WordPress', 'listing'];

    public $post_types = ['page'];

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
        $args = [
            'post_type' => 'shop',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ];

        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if ($language) {
                $args['lang'] = $language;
            }
        }

        $query = new \WP_Query($args);

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'shopItems' => array_map(
                static function (\WP_Post $post): array {
                    $order = ShopOrderData::forPost((int) $post->ID);

                    return [
                        'id' => (int) $post->ID,
                        'title' => get_the_title($post),
                        'description' => trim(wp_strip_all_tags($post->post_excerpt)),
                        'url' => get_permalink($post),
                        'image_id' => (int) get_post_thumbnail_id($post),
                        'order_url' => $order['order_url'],
                        'order_title' => $order['order_title'],
                        'order_target' => $order['order_target'],
                        'price' => $order['price'],
                    ];
                },
                $query->posts
            ),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('shop_listing');

        $fields
            ->addText('prefix', ['label' => 'Prefix'])
            ->addWysiwyg('title', ['label' => 'Tytuł H2', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0]);

        return $fields->build();
    }
}
