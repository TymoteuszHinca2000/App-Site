<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class WhyAppSite extends Block
{
    public $name = 'Dlaczego App&Site';

    public $slug = 'why-appsite';

    public $description = 'Sekcja z trzema animowanymi statystykami i dwoma przyciskami.';

    public $category = 'appandsite';

    public $icon = 'chart-bar';

    public $keywords = ['dlaczego', 'statystyki', 'liczby', 'o firmie', 'why'];

    public $post_types = ['page'];

    public $blockVersion = 3;

    public $mode = 'preview';

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
        $defaults = [
            ['number' => 40, 'description' => 'zrealizowanych stron WordPress'],
            ['number' => 50, 'description' => 'przeszkolonych klientów'],
            ['number' => 60, 'description' => 'wdrożeń Consent Mode v2'],
        ];

        $saved = get_field('items') ?: [];
        $items = [];

        foreach ($defaults as $index => $default) {
            $row = $saved[$index] ?? [];

            $items[] = [
                'image' => $row['image'] ?? null,
                'number' => is_numeric($row['number'] ?? null) ? max(0, (int) $row['number']) : $default['number'],
                'suffix' => array_key_exists('suffix', $row) ? (string) $row['suffix'] : '+',
                'description' => trim((string) ($row['description'] ?? '')) ?: $default['description'],
            ];
        }

        return [
            'prefix' => get_field('prefix') ?: 'Dlaczego warto?',
            'title' => get_field('title') ?: 'Dlaczego <strong>App&Site?</strong>',
            'description' => get_field('description') ?: '<p>Tworzymy nowoczesne, szybkie i bezpieczne strony WordPress, które wspierają rozwój Twojego biznesu.</p>',
            'items' => $items,
            'buttonPrimary' => get_field('button_primary'),
            'buttonSecondary' => get_field('button_secondary'),
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('why_appsite');

        $fields
            ->addText('prefix', [
                'label' => 'Prefix',
                'default_value' => 'Dlaczego warto?',
            ])
            ->addWysiwyg('title', [
                'label' => 'Tytuł H2 (pogrubienie = zielony tekst)',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'default_value' => 'Dlaczego <strong>App&Site?</strong>',
            ])
            ->addWysiwyg('description', [
                'label' => 'Opis',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'default_value' => '<p>Tworzymy nowoczesne, szybkie i bezpieczne strony WordPress, które wspierają rozwój Twojego biznesu.</p>',
            ])
            ->addRepeater('items', [
                'label' => 'Trzy statystyki',
                'instructions' => 'Uzupełnij trzy elementy. Ikony można wgrać w WordPressie; bez pliku wyświetlą się ikony domyślne.',
                'layout' => 'block',
                'button_label' => 'Dodaj statystykę',
                'min' => 3,
                'max' => 3,
            ])
                ->addImage('image', [
                    'label' => 'Ikona (opcjonalnie)',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                ])
                ->addNumber('number', [
                    'label' => 'Liczba',
                    'min' => 0,
                    'step' => 1,
                    'required' => 1,
                ])
                ->addText('suffix', [
                    'label' => 'Dopisek po liczbie, np. +',
                    'default_value' => '+',
                ])
                ->addTextarea('description', [
                    'label' => 'Opis statystyki',
                    'rows' => 2,
                ])
            ->endRepeater()
            ->addLink('button_primary', [
                'label' => 'Przycisk główny',
                'return_format' => 'array',
            ])
            ->addLink('button_secondary', [
                'label' => 'Przycisk dodatkowy',
                'return_format' => 'array',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
    }
}
