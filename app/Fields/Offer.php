<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class Offer extends Field
{
    public function fields(): array
    {
        $fields = Builder::make('offer_details', [
            'title' => 'Dane oferty',
        ]);

        $fields
            ->setLocation('post_type', '==', 'offer');

        $fields
            ->addRepeater('offer_features', [
                'label' => 'Najważniejsze cechy',
                'layout' => 'table',
                'button_label' => 'Dodaj cechę',
            ])
                ->addText('text', [
                    'label' => 'Cecha',
                ])
            ->endRepeater();

        return $fields->build();
    }
}