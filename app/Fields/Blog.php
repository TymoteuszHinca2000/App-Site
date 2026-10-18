<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class Blog extends Field
{
    public function fields(): array
    {
        $fields = Builder::make('blog_details', [
            'title' => 'Ustawienia wpisu blogowego',
        ]);

        $fields->setLocation('post_type', '==', 'blog');

        $fields
            ->addNumber('blog_reading_time', [
                'label' => 'Czas czytania (minuty)',
                'instructions' => 'Podaj liczbę minut. Puste pole oznacza automatyczne oszacowanie na podstawie treści.',
                'min' => 1,
                'step' => 1,
                'placeholder' => 'Np. 8',
            ])
            ->addTrueFalse('blog_featured', [
                'label' => 'Polecany artykuł',
                'instructions' => 'Włącz, aby wpis pojawił się w sliderze polecanych artykułów.',
                'ui' => 1,
                'default_value' => 0,
            ]);

        return $fields->build();
    }
}