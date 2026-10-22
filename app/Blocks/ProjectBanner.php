<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ProjectBanner extends Block
{
    public $name = 'Banner projektu';

    public $slug = 'project-banner';

    public $description = 'Banner pojedynczej realizacji z breadcrumbami, kategorią, zdjęciem lub filmem i dwoma przyciskami.';

    public $category = 'appandsite';

    public $icon = 'cover-image';

    public $keywords = ['projekt', 'realizacja', 'portfolio', 'banner'];

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

    public function with(): array
    {
        return [
            'title' => get_field('title'),
            'description' => get_field('description'),
            'buttonPrimary' => get_field('button_primary'),
            'buttonSecondary' => get_field('button_secondary'),
            'backgroundType' => get_field('background_type') ?: 'featured',
            'backgroundGallery' => get_field('background_gallery') ?: [],
            'backgroundVideo' => get_field('background_video'),
            'backgroundVideoPoster' => get_field('background_video_poster'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('project_banner');

        $fields
            ->addWysiwyg('title', [
                'label' => 'Tytuł H1 (puste = nazwa projektu)',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addWysiwyg('description', [
                'label' => 'Opis (pusty = zajawka projektu)',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addLink('button_primary', [
                'label' => 'Przycisk główny (pusty = link projektu z danych realizacji)',
                'return_format' => 'array',
            ])
            ->addLink('button_secondary', [
                'label' => 'Przycisk dodatkowy (obrysowany)',
                'return_format' => 'array',
            ])
            ->addSelect('background_type', [
                'label' => 'Tło bannera',
                'choices' => [
                    'featured' => 'Zdjęcie wyróżniające projektu',
                    'gallery' => 'Galeria zdjęć',
                    'video' => 'Film',
                ],
                'default_value' => 'featured',
                'ui' => 1,
            ])
            ->addGallery('background_gallery', [
                'label' => 'Galeria tła',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->conditional('background_type', '==', 'gallery')
            ->addFile('background_video', [
                'label' => 'Film w tle',
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'mp4,webm',
            ])
            ->conditional('background_type', '==', 'video')
            ->addImage('background_video_poster', [
                'label' => 'Poster filmu',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ])
            ->conditional('background_type', '==', 'video');

        return $fields->build();
    }

    public function assets(array $block): void
    {
        // Animacja galerii wykorzystuje istniejący alpBanner.
    }
}
