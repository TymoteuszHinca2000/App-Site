<?php

namespace App;

function appandsite_offer_listing_per_page(): int
{
    return 6;
}

function appandsite_page_has_offer_listing(int $postId): bool
{
    $content = (string) get_post_field(
        'post_content',
        $postId
    );

    return $content !== ''
        && has_block(
            'acf/offer-listing',
            $content
        );
}

function appandsite_get_relative_permalink_path(int $postId): string
{
    $permalink = get_permalink($postId);

    if (!$permalink) {
        return '';
    }

    $path = trim(
        (string) wp_parse_url(
            $permalink,
            PHP_URL_PATH
        ),
        '/'
    );

    $homePath = trim(
        (string) wp_parse_url(
            home_url('/'),
            PHP_URL_PATH
        ),
        '/'
    );

    if ($homePath !== '') {
        if ($path === $homePath) {
            return '';
        }

        if (str_starts_with($path, $homePath . '/')) {
            $path = substr(
                $path,
                strlen($homePath) + 1
            );
        }
    }

    return trim($path, '/');
}

function appandsite_get_offer_listing_paginated_url(
    int $pageId,
    int $pageNumber
): string {
    $permalink = get_permalink($pageId);

    if (!$permalink) {
        return '';
    }

    if ($pageNumber <= 1) {
        return $permalink;
    }

    return user_trailingslashit(
        trailingslashit($permalink)
        . 'page/'
        . $pageNumber,
        'paged'
    );
}

add_action('init', function () {
    $pageIds = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => true,
    ]);

    foreach ($pageIds as $pageId) {
        $pageId = (int) $pageId;

        if (!appandsite_page_has_offer_listing($pageId)) {
            continue;
        }

        $path = appandsite_get_relative_permalink_path(
            $pageId
        );

        if ($path === '') {
            continue;
        }

        $query = 'index.php?page_id='
            . $pageId
            . '&paged=$matches[1]';

        if (function_exists('pll_get_post_language')) {
            $language = pll_get_post_language(
                $pageId,
                'slug'
            );

            if ($language) {
                $query .= '&lang='
                    . rawurlencode($language);
            }
        }

        add_rewrite_rule(
            '^'
            . preg_quote($path, '#')
            . '/page/([0-9]+)/?$',
            $query,
            'top'
        );
    }
}, 50);

add_action('init', function () {
    $rewriteVersion = '2026-10-12-1';

    $optionName =
        'appandsite_offer_listing_rewrite_version';

    if (
        get_option($optionName)
        === $rewriteVersion
    ) {
        return;
    }

    flush_rewrite_rules(false);

    update_option(
        $optionName,
        $rewriteVersion,
        false
    );
}, 999);

add_action('save_post_page', function (
    $postId,
    $post,
    $update
) {
    if (
        wp_is_post_autosave($postId)
        || wp_is_post_revision($postId)
    ) {
        return;
    }

    delete_option(
        'appandsite_offer_listing_rewrite_version'
    );
}, 20, 3);

add_filter('redirect_canonical', function (
    $redirectUrl,
    $requestedUrl
) {
    $paged = (int) get_query_var('paged');

    if (
        $paged < 2
        || !is_page()
    ) {
        return $redirectUrl;
    }

    $pageId = (int) get_queried_object_id();

    if (
        !$pageId
        || !appandsite_page_has_offer_listing(
            $pageId
        )
    ) {
        return $redirectUrl;
    }

    return false;
}, 10, 2);

add_action('template_redirect', function () {
    $paged = (int) get_query_var('paged');

    if (
        $paged < 2
        || !is_page()
    ) {
        return;
    }

    $pageId = (int) get_queried_object_id();

    if (
        !$pageId
        || !appandsite_page_has_offer_listing(
            $pageId
        )
    ) {
        return;
    }

    $queryArgs = [
        'post_type' => 'offer',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => false,
    ];

    if (
        function_exists(
            'pll_get_post_language'
        )
    ) {
        $language = pll_get_post_language(
            $pageId,
            'slug'
        );

        if ($language) {
            $queryArgs['lang'] =
                $language;
        }
    }

    $totalOffers = count(
        get_posts($queryArgs)
    );

    $maxPages = max(
        1,
        (int) ceil(
            $totalOffers
            / appandsite_offer_listing_per_page()
        )
    );

    if ($paged <= $maxPages) {
        return;
    }

    global $wp_query;

    $wp_query->set_404();

    status_header(404);

    nocache_headers();
}, 1);

add_filter('wp_get_canonical_url', function (
    $canonicalUrl,
    $post
) {
    if (
        !($post instanceof \WP_Post)
        || $post->post_type !== 'page'
        || !appandsite_page_has_offer_listing(
            (int) $post->ID
        )
    ) {
        return $canonicalUrl;
    }

    $paged = (int) get_query_var('paged');

    if ($paged < 2) {
        return $canonicalUrl;
    }

    $url =
        appandsite_get_offer_listing_paginated_url(
            (int) $post->ID,
            $paged
        );

    return $url ?: $canonicalUrl;
}, 10, 2);

add_filter('wpseo_canonical', function (
    $canonicalUrl
) {
    $paged = (int) get_query_var('paged');

    if (
        $paged < 2
        || !is_page()
    ) {
        return $canonicalUrl;
    }

    $pageId = (int) get_queried_object_id();

    if (
        !$pageId
        || !appandsite_page_has_offer_listing(
            $pageId
        )
    ) {
        return $canonicalUrl;
    }

    $url =
        appandsite_get_offer_listing_paginated_url(
            $pageId,
            $paged
        );

    return $url ?: $canonicalUrl;
});

add_filter(
    'rank_math/frontend/canonical',
    function ($canonicalUrl) {
        $paged = (int) get_query_var('paged');

        if (
            $paged < 2
            || !is_page()
        ) {
            return $canonicalUrl;
        }

        $pageId =
            (int) get_queried_object_id();

        if (
            !$pageId
            || !appandsite_page_has_offer_listing(
                $pageId
            )
        ) {
            return $canonicalUrl;
        }

        $url =
            appandsite_get_offer_listing_paginated_url(
                $pageId,
                $paged
            );

        return $url ?: $canonicalUrl;
    }
);