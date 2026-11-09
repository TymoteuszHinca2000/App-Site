<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class ContactSection extends Block
{
    public $name = 'Kontakt - formularz i dane';

    public $slug = 'contact-section';

    public $description = 'Formularz Contact Form 7, dane kontaktowe, social media i kafel szybkiego kontaktu.';

    public $category = 'appandsite';

    public $icon = 'email-alt';

    public $keywords = ['kontakt', 'formularz', 'cf7', 'social media', 'telefon', 'email'];

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
        $contactItems = [];

        foreach ((array) (get_field('contact_items') ?: []) as $item) {
            $description = trim((string) ($item['description'] ?? ''));
            $value = trim(wp_strip_all_tags($description));
            $type = (string) ($item['type'] ?? 'auto');
            $href = '';
            $copy = '';

            if ($type === 'auto') {
                if (is_email($value)) {
                    $type = 'email';
                } elseif (preg_match('/^\+?[\d\s().-]+$/u', $value) && preg_match_all('/\d/', $value) >= 7) {
                    $type = 'phone';
                } else {
                    $type = 'text';
                }
            }

            if ($type === 'email' && is_email($value)) {
                $copy = sanitize_email($value);
                $href = 'mailto:' . $copy;
            } elseif ($type === 'phone') {
                $number = preg_replace('/[^\d+]/', '', $value);

                if (strlen(preg_replace('/\D/', '', $number)) >= 7) {
                    $copy = $value;
                    $href = 'tel:' . $number;
                }
            }

            $contactItems[] = [
                'icon' => $item['icon'] ?? null,
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => $description,
                'type' => $type,
                'href' => $href,
                'copy' => $copy,
            ];
        }

        $socialItems = [];

        foreach ((array) (get_field('social_items') ?: []) as $item) {
            $link = $item['link'] ?? null;

            if (!is_array($link) || empty($link['url'])) {
                continue;
            }

            $socialItems[] = [
                'icon' => $item['icon'] ?? null,
                'link' => $link,
            ];
        }

        $selectedForm = get_field('contact_form_id', false, false);

        if (empty($selectedForm)) {
            $selectedForm = get_field('contact_form_id');
        }

        return [
            'prefix' => get_field('prefix'),
            'title' => get_field('title'),
            'description' => get_field('description'),
            'contactFormReference' => $selectedForm,
            'formNote' => get_field('form_note'),
            'contactTitle' => get_field('contact_title'),
            'contactDescription' => get_field('contact_description'),
            'contactItems' => $contactItems,
            'socialTitle' => get_field('social_title'),
            'socialItems' => $socialItems,
            'quickIcon' => get_field('quick_icon'),
            'quickTitle' => get_field('quick_title'),
            'quickDescription' => get_field('quick_description'),
            'quickButton' => get_field('quick_button'),
        ];
    }

    public function fields(): array
    {
        $formChoices = ['' => 'Wybierz formularz'];

        $forms = get_posts([
            'post_type' => 'wpcf7_contact_form',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'suppress_filters' => true,
        ]);

        foreach ($forms as $form) {
            $formChoices[(string) $form->ID] = $form->post_title . ' (ID: ' . $form->ID . ')';
        }

        $fields = Builder::make('contact_section');

        $fields
            ->addTab('content_tab', [
                'label' => 'Treść i formularz',
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
                'toolbar' => 'basic',
                'media_upload' => 0,
            ])
            ->addSelect('contact_form_id', [
                'label' => 'Formularz Contact Form 7',
                'instructions' => 'Wybierz formularz CF7 z listy. Wartość to ID wpisu w WordPressie; Contact Form 7 sam wygeneruje poprawny shortcode (również z identyfikatorem hash).',
                'choices' => $formChoices,
                'default_value' => '',
                'return_format' => 'value',
                'ui' => 1,
                'allow_null' => 1,
            ])
            ->addText('form_note', [
                'label' => 'Krótka informacja pod formularzem',
                'placeholder' => 'Zazwyczaj odpowiadam w ciągu 24 godzin.',
            ])
            ->addTab('contact_tab', [
                'label' => 'Dane kontaktowe',
            ])
            ->addText('contact_title', [
                'label' => 'Tytuł kafelka',
                'default_value' => 'Dane kontaktowe',
            ])
            ->addTextarea('contact_description', [
                'label' => 'Opis kafelka',
                'rows' => 3,
            ])
            ->addRepeater('contact_items', [
                'label' => 'Dane kontaktowe',
                'layout' => 'block',
                'button_label' => 'Dodaj informację',
                'max' => 12,
            ])
                ->addImage('icon', [
                    'label' => 'Ikona',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                ])
                ->addText('title', [
                    'label' => 'Tytuł',
                    'required' => 1,
                ])
                ->addTextarea('description', [
                    'label' => 'Opis / e-mail / numer telefonu',
                    'rows' => 3,
                    'new_lines' => '',
                ])
                ->addSelect('type', [
                    'label' => 'Rodzaj danych',
                    'instructions' => 'Automatycznie rozpoznaje adres e-mail lub telefon. Możesz też wybrać rodzaj ręcznie.',
                    'choices' => [
                        'auto' => 'Wykrywaj automatycznie',
                        'email' => 'E-mail',
                        'phone' => 'Telefon',
                        'text' => 'Zwykły tekst',
                    ],
                    'default_value' => 'auto',
                    'ui' => 1,
                ])
            ->endRepeater()
            ->addTab('social_tab', [
                'label' => 'Social media',
            ])
            ->addText('social_title', [
                'label' => 'Tytuł nad social media',
                'default_value' => 'Znajdź mnie w social media',
            ])
            ->addRepeater('social_items', [
                'label' => 'Social media',
                'layout' => 'block',
                'button_label' => 'Dodaj social media',
                'max' => 12,
            ])
                ->addImage('icon', [
                    'label' => 'Ikona serwisu',
                    'return_format' => 'array',
                    'preview_size' => 'thumbnail',
                ])
                ->addLink('link', [
                    'label' => 'Odnośnik',
                    'return_format' => 'array',
                    'required' => 1,
                ])
            ->endRepeater()
            ->addTab('quick_tab', [
                'label' => 'Szybki kontakt',
            ])
            ->addImage('quick_icon', [
                'label' => 'Ikona kafelka',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
            ])
            ->addText('quick_title', [
                'label' => 'Tytuł',
            ])
            ->addTextarea('quick_description', [
                'label' => 'Opis',
                'rows' => 3,
                'new_lines' => '',
            ])
            ->addLink('quick_button', [
                'label' => 'Przycisk (np. WhatsApp)',
                'instructions' => 'Jeżeli nie dodasz odnośnika, przycisk się nie wyświetli.',
                'return_format' => 'array',
            ]);

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
