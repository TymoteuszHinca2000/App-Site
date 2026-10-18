<?php

namespace App;

use Illuminate\Support\Facades\Vite;

add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

add_filter('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    $dependencies = json_decode(
        Vite::content('editor.deps.json')
    );

    foreach ($dependencies as $dependency) {
        if (! wp_script_is($dependency)) {
            wp_enqueue_script($dependency);
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

add_action('after_setup_theme', function () {
    remove_theme_support('block-templates');

    register_nav_menus([
        'primary_navigation' => __(
            'Primary Navigation',
            'appandsite'
        ),

        'footer_navigation' => __(
            'Stopka',
            'appandsite'
        ),
    ]);

    remove_theme_support('core-block-patterns');

    add_theme_support('title-tag');

    add_theme_support('post-thumbnails');

    add_theme_support('responsive-embeds');

    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);
}, 20);

/*
|--------------------------------------------------------------------------
| HEADER MENU
|--------------------------------------------------------------------------
*/

add_filter('nav_menu_link_attributes', function (
    $atts,
    $item,
    $args,
    $depth
) {
    if (
        ($args->theme_location ?? '') !==
        'primary_navigation'
    ) {
        return $atts;
    }

    $variant =
        $args->header_variant
        ?? 'desktop';

    $activeClasses = [
        'current-menu-item',
        'current-menu-parent',
        'current-menu-ancestor',
        'current_page_item',
        'current_page_parent',
        'current_page_ancestor',
    ];

    $isActive = !empty(
        array_intersect(
            $activeClasses,
            (array) ($item->classes ?? [])
        )
    );

    if ($variant === 'mobile') {
        $baseClasses = '
            relative
            block
            w-fit
            max-w-full
            py-3
            text-[16px]
            font-semibold
            leading-[1.4]
            transition-colors
            duration-300

            after:absolute
            after:bottom-[7px]
            after:left-0
            after:h-[2px]
            after:w-full
            after:origin-left
            after:scale-x-0
            after:rounded-full
            after:bg-brand
            after:transition-transform
            after:duration-500
            after:ease-out

            hover:text-brand
            hover:after:scale-x-100

            focus-visible:outline-2
            focus-visible:outline-offset-4
            focus-visible:outline-brand

            motion-reduce:transition-none
            motion-reduce:after:transition-none
        ';
    } else {
        $baseClasses = '
            relative
            inline-flex
            items-center
            justify-center
            whitespace-nowrap
            py-2
            text-[14px]
            font-semibold
            leading-[1.4]
            transition-colors
            duration-300

            after:absolute
            after:bottom-[2px]
            after:left-0
            after:h-[2px]
            after:w-full
            after:origin-left
            after:scale-x-0
            after:rounded-full
            after:bg-brand
            after:transition-transform
            after:duration-500
            after:ease-out

            hover:text-brand
            hover:after:scale-x-100

            focus-visible:outline-2
            focus-visible:outline-offset-4
            focus-visible:outline-brand

            motion-reduce:transition-none
            motion-reduce:after:transition-none
        ';
    }

    $baseClasses = preg_replace(
        '/\s+/',
        ' ',
        trim($baseClasses)
    );

    $stateClasses = $isActive
        ? 'text-brand after:scale-x-100'
        : 'text-white/90';

    $atts['class'] = trim(
        ($atts['class'] ?? '') .
        ' ' .
        $baseClasses .
        ' ' .
        $stateClasses
    );

    if (
        in_array(
            'current-menu-item',
            (array) ($item->classes ?? []),
            true
        )
    ) {
        $atts['aria-current'] = 'page';
    }

    return $atts;
}, 10, 4);

/*
|--------------------------------------------------------------------------
| FOOTER MENU
|--------------------------------------------------------------------------
*/

add_filter('nav_menu_link_attributes', function (
    $atts,
    $item,
    $args,
    $depth
) {
    if (
        ($args->theme_location ?? '') !==
        'footer_navigation'
    ) {
        return $atts;
    }

    $activeClasses = [
        'current-menu-item',
        'current-menu-parent',
        'current-menu-ancestor',
        'current_page_item',
        'current_page_parent',
        'current_page_ancestor',
    ];

    $isActive = !empty(
        array_intersect(
            $activeClasses,
            (array) ($item->classes ?? [])
        )
    );

    $baseClasses = '
        relative
        inline-flex
        items-center
        py-2
        whitespace-nowrap
        text-[13px]
        font-semibold
        leading-[1.4]
        transition-colors
        duration-300

        after:absolute
        after:bottom-[2px]
        after:left-0
        after:h-px
        after:w-full
        after:origin-left
        after:scale-x-0
        after:bg-brand
        after:transition-transform
        after:duration-300
        after:ease-out

        hover:text-brand
        hover:after:scale-x-100

        focus-visible:outline-2
        focus-visible:outline-offset-4
        focus-visible:outline-brand

        motion-reduce:transition-none
        motion-reduce:after:transition-none
    ';

    $baseClasses = preg_replace(
        '/\s+/',
        ' ',
        trim($baseClasses)
    );

    $stateClasses = $isActive
        ? 'text-brand after:scale-x-100'
        : 'text-white/80';

    $atts['class'] = trim(
        ($atts['class'] ?? '') .
        ' ' .
        $baseClasses .
        ' ' .
        $stateClasses
    );

    if (
        in_array(
            'current-menu-item',
            (array) ($item->classes ?? []),
            true
        )
    ) {
        $atts['aria-current'] = 'page';
    }

    return $atts;
}, 20, 4);