<?php

namespace App\Options;

use Log1x\AcfComposer\Builder;
use Log1x\AcfComposer\Options as Field;

class GoogleMaps extends Field
{
    public $name = 'Google Maps';

    public $slug = 'google-maps';

    public $title = 'Opcje globalne - Google Maps';

    public $capability = 'edit_theme_options';

    public $position = 3;

    public $menu = true;

    public $parent = 'theme';

    public $icon = null;

    public $redirect = false;

    public $post = 'options';

    public $autoload = true;

    public $settings = [];

    public function fields(): array
    {
        $fields = Builder::make('google_maps_settings')
            ->setGroupConfig('title', 'Ustawienia Google Maps');

        $fields
            ->addGroup('google_maps', [
                'label' => 'Google Maps',
                'layout' => 'block',
            ])
                ->addText('api_key', [
                    'label' => 'Google Maps API Key',
                    'instructions' => 'Klucz przeglądarkowy. W Google Cloud ogranicz go do domen witryny i usług Maps JavaScript API oraz Places API, jeśli używasz pola mapy ACF.',
                    'required' => 0,
                ])
            ->endGroup();

        return $fields->build();
    }
}
