<?php

namespace App;

/**
 * Render the current editor content for Yoast, including dynamic ACF blocks.
 * Nothing is saved, and the standard REST nonce and post permissions apply.
 */
add_action('rest_api_init', function () {
    register_rest_route('appandsite/v1', '/seo-content/(?P<id>\d+)', [
        'methods' => \WP_REST_Server::CREATABLE,
        'permission_callback' => function (\WP_REST_Request $request) {
            return current_user_can('edit_post', (int) $request['id']);
        },
        'args' => [
            'content' => [
                'type' => 'string',
                'required' => true,
            ],
        ],
        'callback' => function (\WP_REST_Request $request) {
            $editedPost = get_post((int) $request['id']);

            if (! $editedPost) {
                return new \WP_Error('post_not_found', __('Nie znaleziono wpisu.', 'appandsite'), ['status' => 404]);
            }

            $previousPost = $GLOBALS['post'] ?? null;
            $GLOBALS['post'] = $editedPost;
            setup_postdata($editedPost);

            try {
                return rest_ensure_response([
                    'content' => do_blocks($request->get_param('content')),
                ]);
            } finally {
                $GLOBALS['post'] = $previousPost;

                if ($previousPost instanceof \WP_Post) {
                    setup_postdata($previousPost);
                } else {
                    wp_reset_postdata();
                }
            }
        },
    ]);
});
