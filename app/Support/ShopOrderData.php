<?php

namespace App\Support;

class ShopOrderData
{
    public static function forPost(int $postId): array
    {
        $data = self::findOrderData(parse_blocks((string) get_post_field('post_content', $postId, 'raw')));

        // Zachowaj starsze linki, dopóki wpis nie ma własnego bloku CTA.
        $url = $data === null ? get_post_meta($postId, 'shop_order_url', true) : ($data['order_url'] ?? '');
        $button = self::link($url);
        $price = $data['price'] ?? '';

        return [
            'order_url' => $button['url'],
            'order_title' => $button['title'],
            'order_target' => $button['target'],
            'price' => is_string($price) ? sanitize_text_field($price) : '',
        ];
    }

    public static function link(mixed $value): array
    {
        $link = is_array($value) ? $value : ['url' => $value];
        $url = $link['url'] ?? '';
        $title = $link['title'] ?? '';

        return [
            'url' => esc_url_raw(is_string($url) ? trim($url) : ''),
            'title' => is_string($title) && trim($title) !== '' ? sanitize_text_field($title) : __('Zamów motyw', 'appandsite'),
            'target' => ($link['target'] ?? '') === '_blank' ? '_blank' : '_self',
        ];
    }

    private static function findOrderData(array $blocks): ?array
    {
        foreach ($blocks as $block) {
            if (($block['blockName'] ?? '') === 'acf/shop-cta') {
                $data = $block['attrs']['data'] ?? [];

                return is_array($data) ? $data : [];
            }

            if (!empty($block['innerBlocks'])) {
                $data = self::findOrderData($block['innerBlocks']);
                if ($data !== null) {
                    return $data;
                }
            }
        }

        return null;
    }
}
