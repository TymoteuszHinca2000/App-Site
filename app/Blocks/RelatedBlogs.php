<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class RelatedBlogs extends Block
{
    public $name = 'Powiązane wpisy';

    public $slug = 'related-blogs';

    public $description = 'Powiązane artykuły blogowe lub najnowsze wpisy.';

    public $category = 'appandsite';

    public $icon = 'admin-post';

    public $keywords = [
        'blog',
        'artykuły',
        'powiązane wpisy',
        'pozostałe wpisy',
        'slider',
    ];

    public $post_types = [
        'page',
        'offer',
        'blog',
    ];

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

    public function with(): array
    {
        $currentPostId = (int) (get_the_ID() ?: get_queried_object_id());
        $currentPostType = get_post_type($currentPostId);

        $language = $currentPostId && function_exists('pll_get_post_language')
            ? pll_get_post_language($currentPostId, 'slug')
            : null;

        if (!$language && function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
        }

        $limit = (int) (get_field('limit') ?: 6);
        $limit = max(1, min(6, $limit));
        $categoryIds = [];

        if ($currentPostType === 'blog') {
            $terms = get_the_terms($currentPostId, 'blog_category');

            if (!is_wp_error($terms) && !empty($terms)) {
                $categoryIds = array_values(array_unique(array_map(
                    'intval',
                    wp_list_pluck($terms, 'term_id')
                )));
            }
        }

        if ($currentPostType === 'offer') {
            $selectedCategory = get_field('related_blog_category');

            if ($selectedCategory instanceof \WP_Term) {
                $selectedCategory = $selectedCategory->term_id;
            }

            $selectedCategory = (int) $selectedCategory;

            if ($selectedCategory && $language && function_exists('pll_get_term')) {
                $translatedCategory = pll_get_term($selectedCategory, $language);

                if ($translatedCategory) {
                    $selectedCategory = (int) $translatedCategory;
                }
            }

            if ($selectedCategory && term_exists($selectedCategory, 'blog_category')) {
                $categoryIds = [$selectedCategory];
            }
        }

        $queryArgs = [
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => [
                'date' => 'DESC',
                'ID' => 'DESC',
            ],
            'ignore_sticky_posts' => true,
            'suppress_filters' => false,
        ];

        if ($language) {
            $queryArgs['lang'] = $language;
        }

        if ($currentPostType === 'blog') {
            $queryArgs['post__not_in'] = [$currentPostId];
        }

        if (!empty($categoryIds)) {
            $queryArgs['tax_query'] = [
                [
                    'taxonomy' => 'blog_category',
                    'field' => 'term_id',
                    'terms' => $categoryIds,
                    'operator' => 'IN',
                    'include_children' => true,
                ],
            ];
        }

        $blogPage = get_page_by_path('blog', OBJECT, 'page');

        if ($blogPage && $language && function_exists('pll_get_post')) {
            $translatedPageId = pll_get_post($blogPage->ID, $language);

            if ($translatedPageId) {
                $blogPage = get_post($translatedPageId);
            }
        }

        $blogPageId = $blogPage ? (int) $blogPage->ID : 0;
        $blogPosts = [];

        if ($currentPostType !== 'blog' || !empty($categoryIds)) {
            $posts = get_posts($queryArgs);

            foreach ($posts as $post) {
                $postId = (int) $post->ID;
                $terms = get_the_terms($postId, 'blog_category');
                $category = !is_wp_error($terms) && !empty($terms) ? reset($terms) : null;
                $categoryUrl = '';

                if ($category && $blogPageId && function_exists('App\\appandsite_blog_page_url')) {
                    $categoryUrl = \App\appandsite_blog_page_url(
                        $blogPageId,
                        1,
                        $category->slug
                    );
                }

                $blogPosts[] = [
                    'id' => $postId,
                    'url' => get_permalink($postId),
                    'title' => html_entity_decode(
                        (string) get_the_title($postId),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    ),
                    'image_id' => (int) get_post_thumbnail_id($postId),
                    'date' => get_the_date('j F Y', $postId),
                    'reading_time' => BlogListing::readingMinutes($postId),
                    'category' => $category
                        ? html_entity_decode($category->name, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                        : '',
                    'category_url' => $categoryUrl,
                ];
            }
        }

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'button' => get_field('button'),
            'postButtonText' => get_field('post_button_text') ?: __('Czytaj wpis', 'appandsite'),
            'blogPosts' => $blogPosts,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('related_blogs_block');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
                'default_value' => 'Powiązane wpisy',
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
            ->addLink('button', [
                'label' => 'Przycisk przy nagłówku',
                'return_format' => 'array',
            ])
            ->addText('post_button_text', [
                'label' => 'Tekst odnośnika na kafelkach',
                'default_value' => 'Czytaj wpis',
            ])
            ->addNumber('limit', [
                'label' => 'Maksymalna liczba artykułów',
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default_value' => 6,
            ])
            ->addTaxonomy('related_blog_category', [
                'label' => 'Kategoria bloga dla oferty',
                'instructions' => 'Na ofercie wybierz kategorię lub pozostaw puste, aby pokazać najnowsze wpisy. Na pojedynczym blogu kategorie są pobierane automatycznie.',
                'taxonomy' => 'blog_category',
                'field_type' => 'select',
                'allow_null' => 1,
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
