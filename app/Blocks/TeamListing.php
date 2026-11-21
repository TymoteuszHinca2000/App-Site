<?php

namespace App\Blocks;

use App\Support\TeamProfileData;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class TeamListing extends Block
{
    public $name = 'Listing zespołu';

    public $slug = 'team-listing';

    public $description = 'Lista pracowników pobierająca dane z bloku Profil pracownika. Bez paginacji.';

    public $category = 'appandsite';

    public $icon = 'groups';

    public $keywords = ['zespół', 'pracownicy', 'listing'];

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
        'multiple' => true,
        'jsx' => false,
        'color' => ['background' => false, 'text' => false, 'gradients' => false],
        'spacing' => ['padding' => false, 'margin' => false],
    ];

    public function with(): array
    {
        $args = [
            'post_type' => 'team',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ];

        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if ($language) {
                $args['lang'] = $language;
            }
        }

        $query = new \WP_Query($args);

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'button' => get_field('button'),
            'members' => array_map(
                static fn (\WP_Post $post): array => TeamProfileData::forPost((int) $post->ID),
                $query->posts
            ),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('team_listing');

        $fields
            ->addText('prefix', ['label' => 'Prefix'])
            ->addWysiwyg('title', ['label' => 'Tytuł H2', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addLink('button', ['label' => 'Przycisk', 'return_format' => 'array']);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
