<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class Collaborations extends Field
{
    public $name = 'Współprace';

    public $slug = 'collaborations';

    public $title = 'Opcje globalne - Współprace';

    public $capability = 'edit_theme_options';

    public $position = 4;

    public $menu = true;

    public $parent = 'theme';

    public $icon = null;

    public $redirect = false;

    public $post = 'options';

    public $autoload = true;

    public $settings = [];

    public function fields(): array
    {
        $fields = Builder::make('collaborations_settings')
            ->setGroupConfig('title', 'Współprace');

        $fields
            ->addRepeater('collaborations', [
                'label' => 'Współprace',
                'instructions' => 'Dodaj współprace w kolejności, w której mają pojawiać się w blokach Współprace.',
                'layout' => 'block',
                'button_label' => 'Dodaj współpracę',
            ])
                ->addImage('logo', [
                    'label' => 'Logo',
                    'instructions' => 'Logo wyświetlane w sliderze współprac.',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ])
                ->addText('prefix', [
                    'label' => 'Prefix w sliderze',
                ])
                ->addText('title', [
                    'label' => 'Tytuł w sliderze',
                    'required' => 1,
                ])
                ->addWysiwyg('description', [
                    'label' => 'Opis w sliderze',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ])
                ->addImage('sidebar_logo', [
                    'label' => 'Logo kafla we wpisie',
                    'instructions' => 'Osobny obrazek wyświetlany zamiast tytułu u góry kafla we wpisie. Wybierz logo przeznaczone do małego kafla.',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ])
                ->addText('sidebar_title', [
                    'label' => 'Tytuł kafla we wpisie',
                    'instructions' => 'Wyświetlany, gdy nie wybierzesz loga kafla. Po dodaniu loga służy jako jego opis dla czytników ekranu.',
                ])
                ->addWysiwyg('sidebar_description', [
                    'label' => 'Opis kafla we wpisie',
                    'instructions' => 'Uzupełnij osobno opis bocznego kafla. Pusty opis pozostanie pusty, niezależnie od opisu w sliderze.',
                    'toolbar' => 'basic',
                    'media_upload' => 0,
                ])
                ->addRepeater('features', [
                    'label' => 'Wyróżniki',
                    'instructions' => 'Wyświetlane tylko w sliderze, nie w bocznym kaflu wpisu',
                    'layout' => 'block',
                    'button_label' => 'Dodaj wyróżnik',
                ])
                    ->addImage('icon', [
                        'label' => 'Ikona',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
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
                ->addText('action_title', [
                    'label' => 'Tytuł dolnej belki (link lub kod)',
                ])
                ->addTextarea('action_description', [
                    'label' => 'Opis dolnej belki (link lub kod)',
                    'rows' => 3,
                    'new_lines' => '',
                ])
                ->addUrl('affiliate_url', [
                    'label' => 'Link afiliacyjny',
                ])
                ->addText('affiliate_label', [
                    'label' => 'Tekst linku afiliacyjnego',
                    'default_value' => 'Przejdź do oferty',
                ])
                ->addText('discount_code', [
                    'label' => 'Kod rabatowy',
                    'instructions' => 'W kaflu wpisu kod zastępuje przycisk afiliacyjny.',
                ])
                ->addText('copy_label', [
                    'label' => 'Tekst przycisku kopiowania',
                    'default_value' => 'Kopiuj kod',
                ])
            ->endRepeater();

        return $fields->build();
    }
}
