<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class OfferListing extends Block
{
    public $name = 'Listing ofert';

    public $slug = 'offer-listing';

    public $description = 'Listing ofert z możliwością ustawienia kolejności.';

    public $category = 'appandsite';

    public $icon = 'screenoptions';

    public $keywords = [
        'oferta',
        'oferty',
        'listing',
        'usługi',
        'services',
    ];

    public $post_types = [
        'page',
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
        $language = function_exists(
            'pll_current_language'
        )
            ? pll_current_language('slug')
            : null;

        $offers =
            get_field('offers_order')
            ?: [];

        $offerIds = [];

        // Oferty ustawione ręcznie
        foreach ($offers as $offer) {
            $offerId = is_object($offer)
                ? (int) $offer->ID
                : (int) $offer;

            if (!$offerId) {
                continue;
            }

            if (
                $language
                && function_exists(
                    'pll_get_post'
                )
            ) {
                $translatedOfferId =
                    pll_get_post(
                        $offerId,
                        $language
                    );

                if ($translatedOfferId) {
                    $offerId =
                        (int) $translatedOfferId;
                }
            }

            if (
                !$offerId
                || get_post_type($offerId)
                    !== 'offer'
                || get_post_status($offerId)
                    !== 'publish'
                || in_array(
                    $offerId,
                    $offerIds,
                    true
                )
            ) {
                continue;
            }

            $offerIds[] = $offerId;
        }

        // Pozostałe oferty
        $remainingQueryArgs = [
            'post_type' => 'offer',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post__not_in' => $offerIds,
            'fields' => 'ids',
            'orderby' => [
                'menu_order' => 'ASC',
                'date' => 'DESC',
            ],
            'suppress_filters' => false,
        ];

        if ($language) {
            $remainingQueryArgs['lang'] =
                $language;
        }

        $remainingOfferIds =
            get_posts(
                $remainingQueryArgs
            );

        foreach (
            $remainingOfferIds
            as $remainingOfferId
        ) {
            $remainingOfferId =
                (int) $remainingOfferId;

            if (
                !$remainingOfferId
                || in_array(
                    $remainingOfferId,
                    $offerIds,
                    true
                )
            ) {
                continue;
            }

            $offerIds[] =
                $remainingOfferId;
        }

        // Paginacja
        $perPage =
            \App\appandsite_offer_listing_per_page();

        $currentPage = max(
            1,
            (int) get_query_var('paged')
        );

        $totalOffers = count(
            $offerIds
        );

        $maxPages = $totalOffers > 0
            ? (int) ceil(
                $totalOffers / $perPage
            )
            : 1;

        $offset = (
            $currentPage - 1
        ) * $perPage;

        $currentOfferIds =
            array_slice(
                $offerIds,
                $offset,
                $perPage
            );

        // Dane ofert
        $offerItems = [];

        foreach (
            $currentOfferIds
            as $offerId
        ) {
            $offerItems[] = [
                'id' => $offerId,

                'title' =>
                    get_the_title(
                        $offerId
                    ),

                'excerpt' =>
                    get_the_excerpt(
                        $offerId
                    ),

                'url' =>
                    get_permalink(
                        $offerId
                    ),

                'image_id' =>
                    get_post_thumbnail_id(
                        $offerId
                    ),

                'features' =>
                    $this->getOfferFeatures(
                        $offerId
                    ),
            ];
        }

        return [
            'prefix' =>
                get_field('prefix'),

            'title' =>
                get_field('title'),

            'description' =>
                get_field('description'),

            'offerItems' =>
                $offerItems,

            'currentPage' =>
                $currentPage,

            'maxPages' =>
                $maxPages,

            'totalOffers' =>
                $totalOffers,
        ];
    }

    private function getOfferFeatures(
        int $postId
    ): array {
        $content =
            get_post_field(
                'post_content',
                $postId
            );

        if (!$content) {
            return [];
        }

        $blocks =
            parse_blocks(
                $content
            );

        $features =
            $this->findOfferFeaturesBlock(
                $blocks
            );

        return array_slice(
            $features,
            0,
            4
        );
    }

    private function findOfferFeaturesBlock(
        array $blocks
    ): array {
        foreach ($blocks as $block) {
            $blockName =
                $block['blockName']
                ?? '';

            if (
                $blockName
                === 'acf/offer-features'
            ) {
                return $this->extractFeatures(
                    $block
                );
            }

            if (
                !empty(
                    $block['innerBlocks']
                )
                && is_array(
                    $block['innerBlocks']
                )
            ) {
                $features =
                    $this->findOfferFeaturesBlock(
                        $block['innerBlocks']
                    );

                if (!empty($features)) {
                    return $features;
                }
            }
        }

        return [];
    }

    private function extractFeatures(
        array $block
    ): array {
        $data =
            $block['attrs']['data']
            ?? [];

        if (empty($data)) {
            return [];
        }

        $features = [];

        if (
            isset($data['features'])
            && is_array(
                $data['features']
            )
        ) {
            foreach (
                $data['features']
                as $feature
            ) {
                if (!is_array($feature)) {
                    continue;
                }

                $text = trim(
                    wp_strip_all_tags(
                        $feature['text']
                        ?? ''
                    )
                );

                if (!$text) {
                    continue;
                }

                $features[] = [
                    'text' => $text,
                ];
            }

            return $features;
        }

        $count =
            isset($data['features'])
                ? (int) $data['features']
                : 0;

        for (
            $index = 0;
            $index < $count;
            $index++
        ) {
            $text =
                $data[
                    "features_{$index}_text"
                ] ?? '';

            $text = trim(
                wp_strip_all_tags(
                    $text
                )
            );

            if (!$text) {
                continue;
            }

            $features[] = [
                'text' => $text,
            ];
        }

        return $features;
    }

    public function fields(): array
    {
        $fields =
            Builder::make(
                'offer_listing'
            );

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

            ->addRelationship(
                'offers_order',
                [
                    'label' =>
                        'Kolejność ofert',

                    'instructions' =>
                        'Wybrane tutaj oferty pojawią się jako pierwsze i dokładnie w ustawionej kolejności. Pozostałe oferty zostaną automatycznie dodane na końcu listingu.',

                    'post_type' => [
                        'offer',
                    ],

                    'filters' => [
                        'search',
                    ],

                    'elements' => [
                        'featured_image',
                    ],

                    'return_format' =>
                        'id',
                ]
            );

        return $fields->build();
    }

    public function assets(
        array $block
    ): void {
        //
    }
}