<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class GoogleMap extends Block
{
    public $name = 'Mapa Google';

    public $slug = 'google-map';

    public $description = 'Mapa w kolorach App&Site, kafle informacyjne i interaktywne lokalizacje.';

    public $category = 'appandsite';

    public $icon = 'location-alt';

    public $keywords = ['mapa', 'google maps', 'lokalizacja', 'kontakt', 'dojazd'];

    public $post_types = ['page', 'offer', 'project', 'blog'];

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
        $settings = get_field('google_maps', 'option');
        $apiKey = is_array($settings) ? trim((string) ($settings['api_key'] ?? '')) : '';
        $locations = [];

        foreach ((array) (get_field('locations') ?: []) as $item) {
            $point = $item['map_point'] ?? null;
            $coordinates = trim((string) ($item['coordinates'] ?? ''));
            $method = (string) ($item['location_method'] ?? 'manual');
            $lat = null;
            $lng = null;

            if ($method !== 'map' && $coordinates !== '') {
                $parts = array_map('trim', explode(',', $coordinates));

                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $lat = (float) $parts[0];
                    $lng = (float) $parts[1];
                }
            }

            if (($lat === null || $lng === null) && is_array($point) && isset($point['lat'], $point['lng'])) {
                if (is_numeric($point['lat']) && is_numeric($point['lng'])) {
                    $lat = (float) $point['lat'];
                    $lng = (float) $point['lng'];
                }
            }

            if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng) || abs($lat) > 90 || abs($lng) > 180) {
                continue;
            }

            $title = trim(wp_strip_all_tags((string) ($item['name'] ?? '')));
            $address = trim(wp_strip_all_tags((string) ($item['address'] ?? '')));

            if ($address === '') {
                $address = trim(wp_strip_all_tags((string) ($point['address'] ?? '')));
            }

            $locations[] = [
                'title' => $title ?: __('Nasza lokalizacja', 'appandsite'),
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                'directionsUrl' => 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($lat . ',' . $lng),
            ];
        }

        $language = function_exists('pll_current_language')
            ? (string) pll_current_language('slug')
            : substr((string) determine_locale(), 0, 2);

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'directionsButton' => get_field('directions_button') ?: null,
            'items' => get_field('items') ?: [],
            'mapLocations' => $locations,
            'mapConfig' => [
                'apiKey' => $apiKey,
                'language' => in_array($language, ['pl', 'en', 'de'], true) ? $language : 'pl',
                'zoom' => max(3, min(19, (int) (get_field('zoom') ?: 13))),
                'locations' => $locations,
            ],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('google_map_block');

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
            ->addLink('directions_button', [
                'label' => 'Przycisk wyznaczania trasy',
                'instructions' => 'Wybierz adres URL, tekst i sposób otwarcia. Jeśli nie uzupełnisz odnośnika, przycisk nie będzie wyświetlany.',
                'return_format' => 'array',
            ])
            ->addRepeater('items', [
                'label' => 'Kafle informacyjne',
                'layout' => 'block',
                'button_label' => 'Dodaj kafelek',
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
                    'rows' => 3,
                    'new_lines' => '',
                ])
            ->endRepeater()
            ->addRepeater('locations', [
                'label' => 'Lokalizacje na mapie',
                'instructions' => 'Zaznacz lokalizację na mapie w panelu. Możesz dodać kilka adresów.',
                'layout' => 'block',
                'button_label' => 'Dodaj lokalizację',
                'min' => 1,
            ])
                ->addText('name', [
                    'label' => 'Nazwa miejsca',
                    'required' => 1,
                ])
                ->addSelect('location_method', [
                    'label' => 'Sposób podania lokalizacji',
                    'choices' => [
                        'manual' => 'Wpisz współrzędne ręcznie',
                        'map' => 'Wybierz punkt na mapie Google',
                    ],
                    'default_value' => 'manual',
                    'ui' => 1,
                ])
                ->addText('coordinates', [
                    'label' => 'Współrzędne (szerokość, długość)',
                    'instructions' => 'Wpisz np. 54.52770013461404, 18.494124841459232. Używaj kropek w liczbach i przecinka między współrzędnymi.',
                    'placeholder' => '54.52770013461404, 18.494124841459232',
                ])
                ->conditional('location_method', '==', 'manual')
                ->addGoogleMap('map_point', [
                    'label' => 'Położenie na mapie',
                    'required' => 0,
                    'center_lat' => 54.5189,
                    'center_lng' => 18.5305,
                    'zoom' => 12,
                    'height' => 320,
                ])
                ->conditional('location_method', '==', 'map')
                ->addTextarea('address', [
                    'label' => 'Adres widoczny pod mapą (opcjonalnie)',
                    'instructions' => 'Jeśli puste, pokażemy adres z wybranej lokalizacji.',
                    'rows' => 2,
                    'new_lines' => '',
                ])
            ->endRepeater()
            ->addNumber('zoom', [
                'label' => 'Przybliżenie przy jednej lokalizacji',
                'default_value' => 13,
                'min' => 3,
                'max' => 19,
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
