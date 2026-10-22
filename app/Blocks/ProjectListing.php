<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ProjectListing extends Block
{
    public $name = 'Listing projektów';

    public $slug = 'project-listing';

    public $description = 'Lista realizacji z wyróżnikami, kategoriami, technologiami i paginacją.';

    public $category = 'appandsite';

    public $icon = 'portfolio';

    public $keywords = [
        'projekty',
        'portfolio',
        'realizacje',
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

    private function projectData(\WP_Post $post): array
    {
        $postId = (int) $post->ID;

        $categories = get_the_terms($postId, 'project_category');
        $category = !is_wp_error($categories) && !empty($categories)
            ? reset($categories)
            : null;

        $technologies = get_the_terms($postId, 'project_tag');
        $tags = [];

        if(!is_wp_error($technologies) && !empty($technologies)) {
            foreach($technologies as $technology) {
                $tags[] = [
                    'name' => $technology->name,
                    'slug' => $technology->slug,
                ];
            }
        }

        return [
            'id' => $postId,
            'title' => html_entity_decode(get_the_title($postId), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'url' => get_permalink($postId),
            'excerpt' => wp_trim_words(wp_strip_all_tags(get_the_excerpt($postId)), 24, '…'),
            'image_id' => (int) get_post_thumbnail_id($postId),
            'category' => $category ? $category->name : '',
            'category_slug' => $category ? $category->slug : '',
            'tags' => array_slice($tags, 0, 6),
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
            : \App\appandsite_project_current_category();

        $termArgs = [
            'taxonomy' => 'project_category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ];

        if($language) {
            $termArgs['lang'] = $language;
        }

        $terms = get_terms($termArgs);
        $categories = [];

        if(!is_wp_error($terms)) {
            foreach($terms as $term) {
                $categories[] = [
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url' => \App\appandsite_project_page_url($pageId, 1, $term->slug),
                ];
            }
        }

        if(!in_array($selected, array_column($categories, 'slug'), true)) {
            $selected = '';
        }

        $queryArgs = [
            'post_type' => 'project',
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'paged' => $currentPage,
            'orderby' => [
                'date' => 'DESC',
                'ID' => 'DESC',
            ],
            'ignore_sticky_posts' => true,
        ];

        if($language) {
            $queryArgs['lang'] = $language;
        }

        if($selected !== '') {
            $queryArgs['tax_query'] = [
                [
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => [$selected],
                    'include_children' => true,
                ],
            ];
        }

        $query = new \WP_Query($queryArgs);

        $projects = array_map(
            fn ($post) => $this->projectData($post),
            $query->posts
        );

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'features' => get_field('features') ?: [],
            'projectButtonText' => get_field('project_button_text') ?: __('Zobacz realizację', 'appandsite'),
            'allCategoriesText' => get_field('all_categories_text') ?: __('Wszystkie', 'appandsite'),
            'categories' => $categories,
            'selectedCategory' => $selected,
            'allCategoriesUrl' => \App\appandsite_project_page_url($pageId),
            'projects' => $projects,
            'currentPage' => $currentPage,
            'maxPages' => max(1, (int) $query->max_num_pages),
            'totalProjects' => (int) $query->found_posts,
            'pageId' => $pageId,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('project_listing');

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
            ->addText('all_categories_text', [
                'label' => 'Tekst przycisku Wszystkie',
                'default_value' => 'Wszystkie',
            ])
            ->addText('project_button_text', [
                'label' => 'Tekst przycisku na kafelkach',
                'default_value' => 'Zobacz realizację',
            ])
            ->addNumber('per_page', [
                'label' => 'Projektów na stronę',
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