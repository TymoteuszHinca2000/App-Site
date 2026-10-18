<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class BlogListing extends Block
{
    public $name = 'Listing bloga';

    public $slug = 'blog-listing';

    public $description = 'Wyróżniki, polecane artykuły, kategorie i paginowany blog.';

    public $category = 'appandsite';

    public $icon = 'admin-post';

    public $keywords = [
        'blog',
        'artykuły',
        'wpisy',
        'kategorie',
        'listing',
    ];

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
        'multiple' => false,
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

    public static function readingMinutes(int $postId): int
    {
        $custom = (int) get_field('blog_reading_time', $postId);

        if ($custom > 0) {
            return $custom;
        }

        $text = wp_strip_all_tags(
            strip_shortcodes((string) get_post_field('post_content', $postId))
        );

        preg_match_all('/[\p{L}\p{N}]+/u', $text, $matches);

        return max(1, (int) ceil(count($matches[0] ?? []) / 200));
    }

    private function postData(\WP_Post $post): array
    {
        $id = (int) $post->ID;
        $terms = get_the_terms($id, 'blog_category');
        $term = !is_wp_error($terms) && !empty($terms) ? reset($terms) : null;

        return [
            'id' => $id,
            'title' => get_the_title($id),
            'url' => get_permalink($id),
            'excerpt' => wp_trim_words(wp_strip_all_tags(get_the_excerpt($id)), 25, '…'),
            'image_id' => (int) get_post_thumbnail_id($id),
            'date' => get_the_date('j F Y', $id),
            'reading_time' => self::readingMinutes($id),
            'category' => $term ? $term->name : '',
            'category_slug' => $term ? $term->slug : '',
        ];
    }

    public function with(): array
    {
        $pageId = (int) (get_the_ID() ?: get_queried_object_id());
        $language = function_exists('pll_current_language') ? pll_current_language('slug') : null;
        $perPage = max(1, min(24, (int) (get_field('per_page') ?: 6)));
        $currentPage = is_admin() ? 1 : max(1, (int) get_query_var('paged'));

        $selected = is_admin()
            ? ''
            : \App\appandsite_blog_current_category();

        $termArgs = [
            'taxonomy' => 'blog_category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ];

        if ($language) {
            $termArgs['lang'] = $language;
        }

        $terms = get_terms($termArgs);
        $categories = [];

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[] = [
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url' => \App\appandsite_blog_page_url($pageId, 1, $term->slug),
                ];
            }
        }

        if (!in_array($selected, array_column($categories, 'slug'), true)) {
            $selected = '';
        }

        $featuredArgs = [
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => [
                [
                    'key' => 'blog_featured',
                    'value' => '1',
                    'compare' => '=',
                ],
            ],
            'suppress_filters' => false,
        ];

        if ($language) {
            $featuredArgs['lang'] = $language;
        }

        $featured = array_map(
            fn ($post) => $this->postData($post),
            get_posts($featuredArgs)
        );

        $queryArgs = [
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'paged' => $currentPage,
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
        ];

        if ($language) {
            $queryArgs['lang'] = $language;
        }

        if ($selected !== '') {
            $queryArgs['tax_query'] = [
                [
                    'taxonomy' => 'blog_category',
                    'field' => 'slug',
                    'terms' => [$selected],
                    'include_children' => true,
                ],
            ];
        }

        $query = new \WP_Query($queryArgs);

        $posts = array_map(
            fn ($post) => $this->postData($post),
            $query->posts
        );

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'features' => get_field('features') ?: [],
            'featuredPrefix' => get_field('featured_prefix'),
            'featuredButtonText' => get_field('featured_button_text') ?: __('Czytaj cały artykuł', 'appandsite'),
            'postButtonText' => get_field('post_button_text') ?: __('Czytaj wpis', 'appandsite'),
            'allCategoriesText' => get_field('all_categories_text') ?: __('Wszystkie', 'appandsite'),
            'categories' => $categories,
            'selectedCategory' => $selected,
            'allCategoriesUrl' => \App\appandsite_blog_page_url($pageId),
            'featuredPosts' => $featured,
            'blogPosts' => $posts,
            'currentPage' => $currentPage,
            'maxPages' => max(1, (int) $query->max_num_pages),
            'totalPosts' => (int) $query->found_posts,
            'pageId' => $pageId,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('blog_listing');

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
            ])
            ->addRepeater('features', [
                'label' => 'Wyróżniki',
                'layout' => 'block',
                'button_label' => 'Dodaj wyróżnik',
                'max' => 8,
            ])
                ->addImage('icon', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                ])
                ->addText('title', [
                    'label' => 'Tytuł',
                ])
                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 2,
                ])
            ->endRepeater()
            ->addText('featured_prefix', [
                'label' => 'Prefix polecanego artykułu',
                'default_value' => 'Polecany artykuł',
            ])
            ->addText('featured_button_text', [
                'label' => 'Tekst przycisku polecanego artykułu',
                'default_value' => 'Czytaj cały artykuł',
            ])
            ->addText('all_categories_text', [
                'label' => 'Tekst kategorii „Wszystkie”',
                'default_value' => 'Wszystkie',
            ])
            ->addText('post_button_text', [
                'label' => 'Tekst przycisku na kaflach',
                'default_value' => 'Czytaj wpis',
            ])
            ->addNumber('per_page', [
                'label' => 'Wpisów na stronę',
                'min' => 1,
                'max' => 24,
                'default_value' => 6,
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}