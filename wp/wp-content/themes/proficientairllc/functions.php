<?php

add_action('wp_enqueue_scripts', 'proficientairllc_enqueue_styles');

function proficientairllc_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/styles.css');
}

/**
 * proficientairllc functions and definitions
 *
 * @package proficientairllc
 * @since proficientairllc 1.0
 * @license GPL 2.0
 */


// Include all the SiteOrigin extras
include get_template_directory() . '/inc/settings.php';


// Load the theme specific files



