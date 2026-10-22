<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ProjectStats extends Block
{
    public $name = 'Informacje o projekcie';

    public $slug = 'project-stats';

    public $description = 'Pięć kafelków z informacjami o realizacji. Pierwszy automatycznie pobiera technologie projektu.';

    public $category = 'appandsite';

    public $icon = 'info-outline';

    public $keywords = [
        'projekt',
        'realizacja',
        'technologie',
        'informacje',
        'ikony',
    ];

    public $post_types = ['project'];

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
        $postId = (int) get_the_ID();

        if(!$postId || get_post_type($postId) !== 'project') {
            $postId = (int) get_queried_object_id();
        }

        $technologies = [];

        if($postId && get_post_type($postId) === 'project') {
            $terms = get_the_terms($postId, 'project_tag');

            if(!is_wp_error($terms) && !empty($terms)) {
                foreach($terms as $term) {
                    $technologies[] = wp_strip_all_tags($term->name);
                }
            }
        }

        $items = [
            [
                'image' => get_field('technology_image'),
                'title' => get_field('technology_title') ?: __('Technologie', 'appandsite'),
                'description' => implode(', ', $technologies),
            ],
        ];

        $manualItems = get_field('items') ?: [];

        foreach(array_slice($manualItems, 0, 4) as $item) {
            $items[] = [
                'image' => $item['image'] ?? null,
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        return [
            'items' => $items,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('project_stats');

        $fields
            ->addTab('technologies_tab', [
                'label' => 'Technologie projektu',
            ])
            ->addImage('technology_image', [
                'label' => 'Ikona technologii',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ])
            ->addText('technology_title', [
                'label' => 'Tytuł kafelka',
                'default_value' => 'Technologie',
            ])
            ->addMessage('technologies_info', 'Opis kafelka jest pobierany automatycznie z technologii przypisanych do projektu w panelu WordPress.')
            ->addTab('items_tab', [
                'label' => 'Pozostałe informacje',
            ])
            ->addRepeater('items', [
                'label' => 'Kafelki informacyjne',
                'instructions' => 'Dodaj cztery pozostałe kafelki. Piąty kafelek zostanie wycentrowany na urządzeniach mobilnych.',
                'layout' => 'block',
                'button_label' => 'Dodaj kafelek',
                'min' => 4,
                'max' => 4,
            ])
                ->addImage('image', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])
                ->addText('title', [
                    'label' => 'Tytuł',
                    'required' => 1,
                ])
                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 2,
                    'new_lines' => '',
                ])
            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}