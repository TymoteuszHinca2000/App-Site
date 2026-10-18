<?php

namespace App;

function appandsite_blog_current_category(): string
{
    $category = get_query_var('blog_filter', '');

    if (!$category && isset($_GET['blog_filter']) && is_string($_GET['blog_filter'])) {
        $category = wp_unslash($_GET['blog_filter']);
    }

    return is_string($category) ? sanitize_title($category) : '';
}

function appandsite_blog_page_url(int $pageId, int $number = 1, string $category = ''): string
{
    $url = get_permalink($pageId);

    if (!$url) {
        return '';
    }

    $category = sanitize_title($category);

    if ($category !== '') {
        $url = trailingslashit($url) . $category;
    }

    if ($number > 1) {
        $url = trailingslashit($url) . 'page/' . $number;
    }

    return user_trailingslashit($url);
}

function appandsite_has_blog_listing(int $pageId): bool
{
    $content = (string) get_post_field('post_content', $pageId);

    return $content !== '' && has_block('acf/blog-listing', $content);
}

function appandsite_blog_listing_per_page(int $pageId): int
{
    $blocks = parse_blocks((string) get_post_field('post_content', $pageId));

    $walk = function (array $blocks) use (&$walk): ?int {
        foreach ($blocks as $block) {
            if (($block['blockName'] ?? '') === 'acf/blog-listing') {
                return max(1, min(24, (int) ($block['attrs']['data']['per_page'] ?? 6)));
            }

            if (!empty($block['innerBlocks'])) {
                $found = $walk($block['innerBlocks']);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    };

    return $walk($blocks) ?? 6;
}

add_filter('query_vars', function (array $vars): array {
    $vars[] = 'blog_filter';

    return $vars;
});

add_action('parse_request', function (\WP $wp): void {
    $requestPath = trim((string) $wp->request, '/');

    if ($requestPath === '') {
        return;
    }

    $pageIds = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    foreach ($pageIds as $pageId) {
        $pageId = (int) $pageId;

        if (!appandsite_has_blog_listing($pageId)) {
            continue;
        }

        $basePath = appandsite_get_relative_permalink_path($pageId);

        if ($basePath === '') {
            continue;
        }

        $pattern = '#^' . preg_quote($basePath, '#') . '/([^/]+)(?:/page/([1-9][0-9]*))?/?$#u';

        if (!preg_match($pattern, $requestPath, $matches)) {
            continue;
        }

        $slug = sanitize_title(rawurldecode($matches[1]));

        if ($slug === '') {
            continue;
        }

        $terms = get_terms([
            'taxonomy' => 'blog_category',
            'slug' => $slug,
            'hide_empty' => false,
            'lang' => '',
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }

        $language = function_exists('pll_get_post_language')
            ? (string) pll_get_post_language($pageId, 'slug')
            : '';

        $matchingTerm = false;

        foreach ($terms as $term) {
            if ($language !== '' && function_exists('pll_get_term_language')) {
                $termLanguage = (string) pll_get_term_language($term->term_id, 'slug');

                if ($termLanguage !== '' && $termLanguage !== $language) {
                    continue;
                }
            }

            $matchingTerm = true;
            break;
        }

        if (!$matchingTerm) {
            continue;
        }

        $wp->query_vars = [
            'page_id' => $pageId,
            'blog_filter' => $slug,
        ];

        if (!empty($matches[2])) {
            $wp->query_vars['paged'] = (int) $matches[2];
        }

        if ($language !== '') {
            $wp->query_vars['lang'] = $language;
        }

        return;
    }
}, 1);

add_filter('redirect_canonical', function ($redirect) {
    if (!is_page()) {
        return $redirect;
    }

    $pageId = (int) get_queried_object_id();

    if (!appandsite_has_blog_listing($pageId)) {
        return $redirect;
    }

    if (appandsite_blog_current_category() !== '' || (int) get_query_var('paged') > 1) {
        return false;
    }

    return $redirect;
});

add_filter('wpseo_canonical', function ($url) {
    if (!is_page()) {
        return $url;
    }

    $pageId = (int) get_queried_object_id();

    if (!appandsite_has_blog_listing($pageId)) {
        return $url;
    }

    $page = max(1, (int) get_query_var('paged'));
    $category = appandsite_blog_current_category();

    return appandsite_blog_page_url($pageId, $page, $category) ?: $url;
});

add_filter('wp_get_canonical_url', function ($url, $post) {
    if (!($post instanceof \WP_Post) || $post->post_type !== 'page') {
        return $url;
    }

    if (!appandsite_has_blog_listing((int) $post->ID)) {
        return $url;
    }

    $page = max(1, (int) get_query_var('paged'));
    $category = appandsite_blog_current_category();

    return appandsite_blog_page_url((int) $post->ID, $page, $category) ?: $url;
}, 10, 2);

add_filter('rank_math/frontend/canonical', function ($url) {
    if (!is_page()) {
        return $url;
    }

    $pageId = (int) get_queried_object_id();

    if (!appandsite_has_blog_listing($pageId)) {
        return $url;
    }

    $page = max(1, (int) get_query_var('paged'));
    $category = appandsite_blog_current_category();

    return appandsite_blog_page_url($pageId, $page, $category) ?: $url;
});

add_action('template_redirect', function () {
    if (!is_page()) {
        return;
    }

    $pageId = (int) get_queried_object_id();

    if (!appandsite_has_blog_listing($pageId)) {
        return;
    }

    $page = max(1, (int) get_query_var('paged'));
    $category = appandsite_blog_current_category();

    if (isset($_GET['blog_filter']) && $category !== '') {
        $terms = get_terms([
            'taxonomy' => 'blog_category',
            'slug' => $category,
            'hide_empty' => false,
            'lang' => '',
        ]);

        if (!is_wp_error($terms) && !empty($terms)) {
            $newUrl = appandsite_blog_page_url($pageId, $page, $category);

            if ($newUrl) {
                wp_safe_redirect($newUrl, 301);
                exit;
            }
        }
    }

    if ($page < 2) {
        return;
    }

    $perPage = appandsite_blog_listing_per_page($pageId);

    $args = [
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'suppress_filters' => false,
    ];

    if (function_exists('pll_get_post_language')) {
        $language = pll_get_post_language($pageId, 'slug');

        if ($language) {
            $args['lang'] = $language;
        }
    }

    if ($category !== '') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'blog_category',
                'field' => 'slug',
                'terms' => [$category],
                'include_children' => true,
            ],
        ];
    }

    $query = new \WP_Query($args);
    $maxPages = max(1, (int) ceil($query->found_posts / $perPage));

    if ($page <= $maxPages) {
        return;
    }

    global $wp_query;

    $wp_query->set_404();
    status_header(404);
    nocache_headers();
}, 1);


add_filter('post_type_link', function ($url, $post, $leavename) {
    if (!($post instanceof \WP_Post) || $post->post_type !== 'blog' || $leavename || !$post->post_name) {
        return $url;
    }

    $terms = get_the_terms($post->ID, 'blog_category');

    if (is_wp_error($terms) || empty($terms)) {
        return $url;
    }

    $category = reset($terms);
    $baseUrl = untrailingslashit($url);
    $postSuffix = '/' . $post->post_name;

    if (substr($baseUrl, -strlen($postSuffix)) !== $postSuffix) {
        return $url;
    }

    $newUrl = substr($baseUrl, 0, -strlen($postSuffix))
        . '/' . $category->slug
        . $postSuffix;

    return user_trailingslashit($newUrl, 'single');
}, 50, 3);

add_action('parse_request', function (\WP $wp) {
    $requestPath = trim((string) $wp->request, '/');

    if ($requestPath === '') {
        return;
    }

    $pageIds = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    foreach ($pageIds as $pageId) {
        $pageId = (int) $pageId;

        if (!appandsite_has_blog_listing($pageId)) {
            continue;
        }

        $basePath = appandsite_get_relative_permalink_path($pageId);

        if ($basePath === '') {
            continue;
        }

        $pattern = '#^' . preg_quote($basePath, '#') . '/([^/]+)/([^/]+)/?$#u';

        if (!preg_match($pattern, $requestPath, $matches)) {
            continue;
        }

        $categorySlug = sanitize_title(rawurldecode($matches[1]));
        $postSlug = sanitize_title(rawurldecode($matches[2]));

        $blogPost = get_page_by_path($postSlug, OBJECT, 'blog');

        if (!$blogPost || $blogPost->post_status !== 'publish') {
            continue;
        }

        $postCategories = wp_get_post_terms($blogPost->ID, 'blog_category', [
            'fields' => 'slugs',
        ]);

        if (
            is_wp_error($postCategories) ||
            !in_array($categorySlug, $postCategories, true)
        ) {
            continue;
        }

        $wp->query_vars = [
            'post_type' => 'blog',
            'name' => $blogPost->post_name,
        ];

        if (function_exists('pll_get_post_language')) {
            $language = pll_get_post_language($blogPost->ID, 'slug');

            if ($language) {
                $wp->query_vars['lang'] = $language;
            }
        }

        return;
    }
}, 20);

add_action('template_redirect', function () {
    if (!is_singular('blog') || is_preview()) {
        return;
    }

    $permalink = get_permalink(get_queried_object_id());

    if (!$permalink) {
        return;
    }

    $expectedPath = trim((string) wp_parse_url($permalink, PHP_URL_PATH), '/');
    $currentPath = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    if ($expectedPath === $currentPath) {
        return;
    }

    wp_safe_redirect($permalink, 301);
    exit;
}, 20);
