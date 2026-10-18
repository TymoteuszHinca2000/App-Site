<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();

        $this->enqueueMainJsScriptInEditorIframe();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    private function enqueueMainJsScriptInEditorIframe(): void
    {
        /*
         * Wgranie skryptu głównego aplikacji JS do iframa edytora bloków Gutenberga -
         */
        add_action('enqueue_block_assets', function() {
            if (is_admin()) {
                wp_enqueue_script(
                    'editor-app',
                    asset('resources/js/gutenbergApp.js')->uri(),
                    ['wp-blocks'],
                    null,
                    true
                );


                add_filter('script_loader_tag', function($tag, $handle) {
                    if ($handle === 'editor-app') {
                        return str_replace(' src=', ' type="module" src=', $tag);
                    }
                    return $tag;
                }, 10, 2);
            }
        });

        add_filter('block_categories_all', function($categories, $post) {
            return array_merge(
                [
                    [
                        'slug'  => 'appandsite',
                        'title' => 'AppAndSite',
                        'icon'  => 'star-filled',
                    ],
                ],
                $categories,
            );
        }, 10, 2);
    }
}
