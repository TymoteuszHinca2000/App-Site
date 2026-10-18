<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class BlogContent extends Block
{
    public $name = 'Blog Content';
    public $slug = 'blog-content';
    public $description = 'Elastyczna treść artykułu z bocznym spisem treści, CTA i nawigacją wpisów.';
    public $category = 'appandsite';
    public $icon = 'editor-table';
    public $keywords = ['blog', 'artykuł', 'treść', 'spis treści', 'content'];
    public $post_types = ['blog'];
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
            'tocTitle' => get_field('toc_title') ?: __('Spis treści', 'appandsite'),
            'tocItems' => get_field('toc_items') ?: [],
            'sidebarCards' => get_field('sidebar_cards') ?: [],
            'sections' => get_field('sections') ?: [],
            'showNavigation' => (bool) get_field('show_navigation'),
        ];
    }

    private function addHeadingFields(Builder $layout): void
    {
        $layout
            ->addText('toc_label', [
                'label' => 'Tytuł w spisie treści',
                'instructions' => 'Opcjonalny skrót. Gdy pusty, używany jest tytuł sekcji.',
            ])
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
                'toolbar' => 'full',
                'media_upload' => 0,
            ]);
    }

    public function fields(): array
    {
        $fields = Builder::make('blog_content');

        $fields
            ->addText('toc_title', [
                'label' => 'Tytuł spisu treści',
                'default_value' => 'Spis treści',
            ])
            ->addRepeater('toc_items', [
                'label' => 'Ręcznie ułożony spis treści',
                'instructions' => 'Opcjonalnie. Podaj numer sekcji (licząc od 1) i tekst odnośnika. Jeśli lista jest pusta, spis powstanie automatycznie.',
                'layout' => 'table',
                'button_label' => 'Dodaj pozycję',
            ])
                ->addText('label', [
                    'label' => 'Tytuł pozycji',
                    'required' => 1,
                ])
                ->addNumber('section_number', [
                    'label' => 'Numer sekcji',
                    'min' => 1,
                    'step' => 1,
                    'required' => 1,
                ])
            ->endRepeater()
            ->addRepeater('sidebar_cards', [
                'label' => 'Kafle pod spisem treści',
                'layout' => 'block',
                'button_label' => 'Dodaj kafel',
            ])
                ->addImage('image', [
                    'label' => 'Obrazek (opcjonalny)',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ])
                ->addText('title', [
                    'label' => 'Tytuł',
                ])
                ->addTextarea('description', [
                    'label' => 'Opis',
                    'rows' => 3,
                ])
                ->addUrl('copy_url', [
                    'label' => 'Link do skopiowania',
                ])
                ->addText('copy_label', [
                    'label' => 'Tekst przycisku kopiowania',
                    'default_value' => 'Kopiuj link',
                ])
                ->addLink('button', [
                    'label' => 'Przycisk (opcjonalny)',
                    'return_format' => 'array',
                ])
            ->endRepeater();

        $content = $fields->addFlexibleContent('sections', [
            'label' => 'Elastyczne sekcje artykułu',
            'button_label' => 'Dodaj sekcję',
            'min' => 1,
        ]);

        $textImage = $content->addLayout('text_image', [
            'label' => 'Tekst + obrazek',
        ]);

        $this->addHeadingFields($textImage);

        $textImage
            ->addImage('image', [
                'label' => 'Zdjęcie',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ])
            ->addTrueFalse('reverse_layout', [
                'label' => 'Odwróć układ (zdjęcie po lewej)',
                'ui' => 1,
                'default_value' => 0,
            ]);

        $quote = $content->addLayout('quote', [
            'label' => 'Cytat',
        ]);

        $quote
            ->addText('toc_label', [
                'label' => 'Tytuł w spisie treści (opcjonalny)',
            ])
            ->addWysiwyg('quote', [
                'label' => 'Treść cytatu',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addText('author', [
                'label' => 'Autor cytatu',
            ])
            ->addUrl('author_url', [
                'label' => 'Link do autora (opcjonalny)',
            ]);

        $compare = $content->addLayout('image_compare', [
            'label' => 'Tekst + porównanie zdjęć',
        ]);

        $this->addHeadingFields($compare);

        $compare
            ->addImage('before_image', [
                'label' => 'Zdjęcie przed',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ])
            ->addText('before_label', [
                'label' => 'Napis na zdjęciu przed',
                'default_value' => 'Przed',
            ])
            ->addImage('after_image', [
                'label' => 'Zdjęcie po',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ])
            ->addText('after_label', [
                'label' => 'Napis na zdjęciu po',
                'default_value' => 'Po',
            ])
            ->addTrueFalse('reverse_layout', [
                'label' => 'Odwróć układ',
                'ui' => 1,
                'default_value' => 0,
            ]);

        $table = $content->addLayout('table', [
            'label' => 'Tabela',
        ]);

        $this->addHeadingFields($table);

        $table
            ->addRepeater('columns', [
                'label' => 'Nagłówki kolumn',
                'layout' => 'table',
                'button_label' => 'Dodaj kolumnę',
                'min' => 1,
                'max' => 6,
            ])
                ->addText('label', [
                    'label' => 'Nagłówek',
                    'required' => 1,
                ])
            ->endRepeater()
            ->addRepeater('rows', [
                'label' => 'Wiersze tabeli',
                'layout' => 'block',
                'button_label' => 'Dodaj wiersz',
            ])
                ->addRepeater('cells', [
                    'label' => 'Komórki wiersza',
                    'layout' => 'table',
                    'button_label' => 'Dodaj komórkę',
                ])
                    ->addText('value', [
                        'label' => 'Treść komórki',
                    ])
                ->endRepeater()
            ->endRepeater();

        $text = $content->addLayout('text', [
            'label' => 'Zwykła sekcja tekstowa',
        ]);

        $this->addHeadingFields($text);

        $cta = $content->addLayout('mini_cta', [
            'label' => 'Mini CTA',
        ]);

        $cta
            ->addText('toc_label', [
                'label' => 'Tytuł w spisie treści (opcjonalny)',
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
            ->addLink('button', [
                'label' => 'Przycisk',
                'return_format' => 'array',
            ]);

        $content->endFlexibleContent();

        $fields->addTrueFalse('show_navigation', [
            'label' => 'Pokaż nawigację poprzedni / następny wpis',
            'ui' => 1,
            'default_value' => 1,
        ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
