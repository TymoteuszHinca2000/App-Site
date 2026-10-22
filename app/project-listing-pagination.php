<?php

namespace App;

function appandsite_project_current_category(): string
{
    $category = get_query_var('project_filter', '');

    return is_string($category) ? sanitize_title($category) : '';
}

function appandsite_project_page_url(int $pageId, int $number = 1, string $category = ''): string
{
    $url = get_permalink($pageId);

    if(!$url) {
        return '';
    }

    $category = sanitize_title($category);

    if($category !== '') {
        $url = trailingslashit($url) . $category;
    }

    if($number > 1) {
        $url = trailingslashit($url) . 'page/' . $number;
    }

    return user_trailingslashit($url);
}

function appandsite_has_project_listing(int $pageId): bool
{
    $content = (string) get_post_field('post_content', $pageId);

    return $content !== '' && has_block('acf/project-listing', $content);
}

add_filter('query_vars', function (array $vars): array {
    $vars[] = 'project_filter';

    return $vars;
});

add_action('parse_request', function (\WP $wp): void {
    $requestPath = trim((string) $wp->request, '/');

    if($requestPath === '') {
        return;
    }

    $pageIds = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);

    foreach($pageIds as $pageId) {
        $pageId = (int) $pageId;

        if(!appandsite_has_project_listing($pageId)) {
            continue;
        }

        $basePath = appandsite_get_relative_permalink_path($pageId);

        if($basePath === '') {
            continue;
        }

        $pattern = '#^' . preg_quote($basePath, '#') . '/(?:(page)/([1-9][0-9]*)|([^/]+)(?:/page/([1-9][0-9]*))?)/?$#u';

        if(!preg_match($pattern, $requestPath, $matches)) {
            continue;
        }

        $category = '';
        $page = 1;

        if(($matches[1] ?? '') === 'page') {
            $page = (int) $matches[2];
        } else {
            $category = sanitize_title(rawurldecode($matches[3] ?? ''));
            $page = !empty($matches[4]) ? (int) $matches[4] : 1;

            $terms = get_terms([
                'taxonomy' => 'project_category',
                'slug' => $category,
                'hide_empty' => false,
                'lang' => '',
            ]);

            if(is_wp_error($terms) || empty($terms)) {
                continue;
            }

            $language = function_exists('pll_get_post_language')
                ? (string) pll_get_post_language($pageId, 'slug')
                : '';

            if($language !== '' && function_exists('pll_get_term_language')) {
                $matchesLanguage = false;

                foreach($terms as $term) {
                    $termLanguage = (string) pll_get_term_language($term->term_id, 'slug');

                    if($termLanguage === '' || $termLanguage === $language) {
                        $matchesLanguage = true;
                        break;
                    }
                }

                if(!$matchesLanguage) {
                    continue;
                }
            }
        }

        $wp->query_vars = [
            'page_id' => $pageId,
            'project_filter' => $category,
        ];

        if($page > 1) {
            $wp->query_vars['paged'] = $page;
        }

        if(function_exists('pll_get_post_language')) {
            $language = pll_get_post_language($pageId, 'slug');

            if($language) {
                $wp->query_vars['lang'] = $language;
            }
        }

        return;
    }
}, 1);

add_filter('redirect_canonical', function ($redirect) {
    if(is_page() && appandsite_has_project_listing((int) get_queried_object_id())) {
        if(appandsite_project_current_category() !== '' || (int) get_query_var('paged') > 1) {
            return false;
        }
    }

    return $redirect;
});

add_filter('wpseo_canonical', function ($url) {
    if(!is_page()) {
        return $url;
    }

    $pageId = (int) get_queried_object_id();

    if(!appandsite_has_project_listing($pageId)) {
        return $url;
    }

    return appandsite_project_page_url(
        $pageId,
        max(1, (int) get_query_var('paged')),
        appandsite_project_current_category()
    ) ?: $url;
});

add_filter('rank_math/frontend/canonical', function ($url) {
    if(!is_page()) {
        return $url;
    }

    $pageId = (int) get_queried_object_id();

    if(!appandsite_has_project_listing($pageId)) {
        return $url;
    }

    return appandsite_project_page_url(
        $pageId,
        max(1, (int) get_query_var('paged')),
        appandsite_project_current_category()
    ) ?: $url;
});