<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class RelatedProjects extends Block
{
    public $name = 'Pozostałe realizacje';

    public $slug = 'related-projects';

    public $description = 'Slider realizacji z tej samej kategorii projektu lub najnowszych projektów na innych stronach.';

    public $category = 'appandsite';

    public $icon = 'portfolio';

    public $keywords = [
        'projekty',
        'realizacje',
        'portfolio',
        'powiązane projekty',
        'slider',
    ];

    public $post_types = [
        'page',
        'project',
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
        $currentPostType = $currentPostId ? get_post_type($currentPostId) : '';

        $language = $currentPostId && function_exists('pll_get_post_language')
            ? pll_get_post_language($currentPostId, 'slug')
            : null;

        if (!$language && function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
        }

        $limit = max(1, min(6, (int) (get_field('limit') ?: 6)));
        $categoryIds = [];

        if ($currentPostType === 'project') {
            $currentTerms = get_the_terms($currentPostId, 'project_category');

            if (!is_wp_error($currentTerms) && !empty($currentTerms)) {
                $categoryIds = array_values(array_unique(array_map(
                    'intval',
                    wp_list_pluck($currentTerms, 'term_id')
                )));
            }
        }

        $queryArgs = [
            'post_type' => 'project',
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

        if ($currentPostType === 'project' && $currentPostId) {
            $queryArgs['post__not_in'] = [$currentPostId];
        }

        if (!empty($categoryIds)) {
            $queryArgs['tax_query'] = [
                [
                    'taxonomy' => 'project_category',
                    'field' => 'term_id',
                    'terms' => $categoryIds,
                    'operator' => 'IN',
                    'include_children' => true,
                ],
            ];
        }

        $projectsPage = get_page_by_path('projekty', OBJECT, 'page');

        if ($projectsPage && $language && function_exists('pll_get_post')) {
            $translatedPageId = pll_get_post($projectsPage->ID, $language);

            if ($translatedPageId) {
                $projectsPage = get_post($translatedPageId);
            }
        }

        $projectsPageId = $projectsPage ? (int) $projectsPage->ID : 0;
        $projects = [];

        foreach (get_posts($queryArgs) as $project) {
            $projectId = (int) $project->ID;
            $terms = get_the_terms($projectId, 'project_category');
            $category = !is_wp_error($terms) && !empty($terms) ? reset($terms) : null;
            $categoryUrl = '';

            if ($category && $projectsPageId && function_exists('App\\appandsite_project_page_url')) {
                $categoryUrl = \App\appandsite_project_page_url(
                    $projectsPageId,
                    1,
                    $category->slug
                );
            }

            $technologies = get_the_terms($projectId, 'project_tag');
            $tags = [];

            if (!is_wp_error($technologies) && !empty($technologies)) {
                foreach ($technologies as $technology) {
                    $tags[] = html_entity_decode(
                        (string) $technology->name,
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );
                }
            }

            $projects[] = [
                'id' => $projectId,
                'url' => get_permalink($projectId),
                'title' => html_entity_decode(
                    (string) get_the_title($projectId),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                ),
                'excerpt' => html_entity_decode(
                    wp_trim_words(wp_strip_all_tags((string) get_the_excerpt($projectId)), 25, '…'),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                ),
                'image_id' => (int) get_post_thumbnail_id($projectId),
                'date' => get_the_date('j F Y', $projectId),
                'category' => $category ? html_entity_decode(
                    (string) $category->name,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                ) : '',
                'category_url' => $categoryUrl,
                'tags' => array_slice($tags, 0, 6),
            ];
        }

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'button' => get_field('button'),
            'projectButtonText' => get_field('project_button_text') ?: __('Zobacz realizację', 'appandsite'),
            'projects' => $projects,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('related_projects_block');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
                'default_value' => 'POZOSTAŁE REALIZACJE',
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
            ->addText('project_button_text', [
                'label' => 'Tekst odnośnika na kafelkach',
                'default_value' => 'Zobacz realizację',
            ])
            ->addNumber('limit', [
                'label' => 'Maksymalna liczba realizacji',
                'instructions' => 'Na pojedynczej realizacji: inne projekty ze wspólnej kategorii. Na pozostałych stronach: najnowsze projekty.',
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default_value' => 6,
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
