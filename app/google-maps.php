<?php

add_filter('acf/fields/google_map/api', function (array $api): array {
    $settings = get_field('google_maps', 'option');
    $key = is_array($settings) ? trim((string) ($settings['api_key'] ?? '')) : '';

    if ($key !== '') {
        $api['key'] = $key;
    }

    return $api;
}, 100);
