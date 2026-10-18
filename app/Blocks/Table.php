<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Table extends Block
{
    public $name = 'Tabela';

    public $slug = 'table';

    public $description = 'Sekcja z tabelą.';

    public $category = 'appandsite';

    public $icon = 'editor-table';

    public $keywords = [
        'tabela',
        'table',
        'oferta',
        'zakres',
    ];

    public $post_types = [
        'offer',
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
        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'column1' => get_field('column_1'),
            'column2' => get_field('column_2'),
            'column3' => get_field('column_3'),
            'rows' => get_field('rows') ?: [],
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('table_block');

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

            ->addText('column_1', [
                'label' => 'Nagłówek kolumny 1',
                'default_value' => 'Element',
            ])

            ->addText('column_2', [
                'label' => 'Nagłówek kolumny 2',
                'default_value' => 'Opis',
            ])

            ->addText('column_3', [
                'label' => 'Nagłówek kolumny 3',
                'default_value' => 'W standardzie',
            ])

            ->addRepeater('rows', [
                'label' => 'Wiersze tabeli',
                'layout' => 'block',
                'button_label' => 'Dodaj wiersz',
                'min' => 1,
            ])

                ->addText('element', [
                    'label' => 'Element',
                ])

                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 2,
                    'new_lines' => 'br',
                ])

                ->addTrueFalse('included', [
                    'label' => 'W standardzie',
                    'ui' => 1,
                    'default_value' => 1,
                    'ui_on_text' => 'Tak',
                    'ui_off_text' => 'Nie',
                ])

                ->addText('status', [
                    'label' => 'Tekst, gdy nie jest w standardzie',
                    'placeholder' => 'Opcjonalnie',
                    'default_value' => 'Opcjonalnie',
                ])

            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}