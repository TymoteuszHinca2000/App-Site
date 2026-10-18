<?php

/**
 * WordPress Admin Customization.
 */

namespace App;

/**
 * Hide default Posts, Comments and Theme Editor from admin menu.
 *
 * @return void
 */
add_action('admin_menu', function () {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
    remove_menu_page('tools.php');
});

/**
 * Disable theme and plugin file editor.
 *
 * @return void
 */
add_action('init', function () {
    if (! defined('DISALLOW_FILE_EDIT')) {
        define('DISALLOW_FILE_EDIT', true);
    }
});

/**
 * Remove unnecessary items from admin bar.
 *
 * @return void
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('customize');
}, 999);
