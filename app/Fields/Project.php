<?php

namespace App\Fields;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Field;

class Project extends Field
{
    public function fields(): array
    {
        $fields = Builder::make('project_details', [
            'title' => 'Dane projektu',
        ]);

        $fields->setLocation('post_type', '==', 'project');

        $fields
            ->addText('project_client', [
                'label' => 'Klient / firma',
            ])
            ->addText('project_year', [
                'label' => 'Rok realizacji',
                'placeholder' => '2026',
            ])
            ->addUrl('project_url', [
                'label' => 'Adres zrealizowanej strony',
                'instructions' => 'Pełny adres URL, np. https://example.com',
            ])
            ->addText('project_url_label', [
                'label' => 'Tekst przycisku',
                'default_value' => 'Zobacz stronę',
            ]);

        return $fields->build();
    }
}