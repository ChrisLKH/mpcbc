<?php
/**
 * Hello MPCBC B Child Theme functions and definitions.
 *
 * @package Hello_MPCBC_B
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue scripts and styles.
 */
function hello_mpcbc_b_scripts() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        '3.1.0'
    );

    // Google Fonts: Geist, Noto Sans TC, Noto Serif TC, Inter, Nanum Pen Script
    wp_enqueue_style(
        'hello-mpcbc-b-fonts',
        'https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Inter:wght@400;600;700&family=Nanum+Pen+Script&family=Noto+Sans+TC:wght@400;500;700&family=Noto+Serif+TC:wght@400;600&display=swap',
        array(),
        null
    );

    // Child theme stylesheet
    wp_enqueue_style(
        'hello-mpcbc-b-style',
        get_stylesheet_uri(),
        array( 'hello-elementor-parent-style', 'hello-mpcbc-b-fonts' ),
        '1.0.0'
    );

    // Sticky fading nav observer
    wp_enqueue_script(
        'hello-mpcbc-b-nav',
        get_stylesheet_directory_uri() . '/nav.js',
        array(),
        '1.0.0',
        true
    );

    // Announcement slider script
    wp_enqueue_script(
        'hello-mpcbc-b-slider',
        get_stylesheet_directory_uri() . '/slider.js',
        array(),
        '1.0.0',
        true
    );

    // Background video toggle script (WCAG 2.2.2)
    wp_enqueue_script(
        'hello-mpcbc-b-video-toggle',
        get_stylesheet_directory_uri() . '/video-toggle.js',
        array(),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'hello_mpcbc_b_scripts', 20 );

/**
 * Display each menu item's Description field as a small second line in the navigation.
 */
function hello_mpcbc_b_nav_menu_description( $item_output, $item, $depth, $args ) {
    if ( ! empty( $item->description ) ) {
        $desc_html = '<small class="menu-item-description" lang="zh-Hant">' . esc_html( $item->description ) . '</small>';
        // Insert right before the closing </a> tag
        $item_output = preg_replace( '/(<\/a>)/i', $desc_html . '$1', $item_output );
    }
    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'hello_mpcbc_b_nav_menu_description', 10, 4 );
