<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Post Types
    |--------------------------------------------------------------------------
    |
    | Post types to be registered with Extended CPTs
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'post_types' => [
        'blog' => [
            'menu_icon' => 'dashicons-admin-post',
            'supports' => [
                'title',
                'editor',
                'author',
                'revisions',
                'thumbnail',
                'excerpt',
            ],
            'show_in_rest' => true,
            'names' => [
                'singular' => 'Wpis blogowy',
                'plural' => 'Wpisy blogowe',
                'slug' => 'blog',
            ],
            'labels' => [
                'menu_name' => 'Blog',
                'all_items' => 'Wszystkie wpisy',
                'add_new' => 'Dodaj nowy',
                'add_new_item' => 'Dodaj nowy wpis',
                'edit_item' => 'Edytuj wpis',
                'new_item' => 'Nowy wpis',
                'view_item' => 'Zobacz wpis',
                'view_items' => 'Zobacz wpisy',
                'search_items' => 'Szukaj wpisów',
                'not_found' => 'Nie znaleziono wpisów',
                'not_found_in_trash' => 'Nie znaleziono wpisów w koszu',
            ],
        ],

        'offer' => [
            'menu_icon' => 'dashicons-screenoptions',
            'supports' => [
                'title',
                'editor',
                'revisions',
                'thumbnail',
                'excerpt',
            ],
            'show_in_rest' => true,
            'has_archive' => false,
            'public' => true,
            'names' => [
                'singular' => 'Oferta',
                'plural' => 'Oferty',
                'slug' => 'oferta',
            ],
            'labels' => [
                'menu_name' => 'Oferta',
                'all_items' => 'Wszystkie oferty',
                'add_new' => 'Dodaj nową',
                'add_new_item' => 'Dodaj nową ofertę',
                'edit_item' => 'Edytuj ofertę',
                'new_item' => 'Nowa oferta',
                'view_item' => 'Zobacz ofertę',
                'view_items' => 'Zobacz oferty',
                'search_items' => 'Szukaj ofert',
                'not_found' => 'Nie znaleziono ofert',
                'not_found_in_trash' => 'Nie znaleziono ofert w koszu',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Taxonomies
    |--------------------------------------------------------------------------
    |
    | Taxonomies to be registered with Extended CPTs library
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'taxonomies' => [
        'blog_category' => [
            'post_types' => ['blog'],
            'meta_box' => 'radio',
            'names' => [
                'singular' => 'Kategoria',
                'plural' => 'Kategorie',
                'slug' => 'kategoria-bloga',
            ],
            'labels' => [
                'all_items' => 'Wszystkie kategorie',
                'edit_item' => 'Edytuj kategorię',
                'view_item' => 'Zobacz kategorię',
                'update_item' => 'Zaktualizuj kategorię',
                'add_new_item' => 'Dodaj nową kategorię',
                'new_item_name' => 'Nazwa nowej kategorii',
                'search_items' => 'Szukaj kategorii',
                'popular_items' => 'Popularne kategorie',
                'not_found' => 'Nie znaleziono kategorii',
            ],
        ],
    ],
];