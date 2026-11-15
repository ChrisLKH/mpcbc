<?php
/**
 * Hello MPCBC Child Theme functions and definitions.
 *
 * @package Hello_MPCBC
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue scripts and styles.
 */
function hello_mpcbc_scripts() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        '3.1.0'
    );

    // Google Fonts: Hanken Grotesk, Noto Sans TC, Noto Sans SC
    wp_enqueue_style(
        'hello-mpcbc-fonts',
        'https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;700;900&family=Noto+Sans+SC:wght@500;700;900&display=swap',
        array(),
        null
    );

    // Child theme stylesheet
    wp_enqueue_style(
        'hello-mpcbc-style',
        get_stylesheet_uri(),
        array( 'hello-elementor-parent-style', 'hello-mpcbc-fonts' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    // Navigation scroll tracking script
    wp_enqueue_script(
        'hello-mpcbc-nav',
        get_stylesheet_directory_uri() . '/nav.js',
        array(),
        filemtime( get_stylesheet_directory() . '/nav.js' ),
        true
    );

    // Video toggle script (WCAG 2.2.2 Pause/Play button)
    wp_enqueue_script(
        'hello-mpcbc-video-toggle',
        get_stylesheet_directory_uri() . '/video-toggle.js',
        array(),
        filemtime( get_stylesheet_directory() . '/video-toggle.js' ),
        true
    );

}
add_action( 'wp_enqueue_scripts', 'hello_mpcbc_scripts', 20 );

// Elementor Interactions ignore the visitor's reduce-motion setting; drop them before they start.
add_action( 'wp_head', function () {
    echo "<script>if(matchMedia('(prefers-reduced-motion: reduce)').matches)document.addEventListener('DOMContentLoaded',function(){var d=document.getElementById('elementor-interactions-data');if(d)d.remove()});</script>\n";
}, 1 );

/** Shared Home retains its native homepage header behavior. */
add_filter( 'body_class', function( $classes ) {
    if ( get_post_meta( get_queried_object_id(), '_mpcbc_translation_group', true ) === 'home' ) {
        $classes[] = 'home';
    }
    return array_unique( $classes );
} );

/** Resolve the single shared footer; its native text contains both Chinese versions. */
function hello_mpcbc_footer_template_id() {
    $titles = array( 'MPCBC – Footer', 'MPCBC - Footer' );
    foreach ( $titles as $title ) {
        $templates = get_posts( array( 'post_type'=>'elementor_library', 'post_status'=>'publish', 'title'=>$title, 'numberposts'=>1 ) );
        if ( $templates ) return $templates[0]->ID;
    }
    return 0;
}

/** Register the shared native footer before Elementor builds its Atomic CSS queue. */
function hello_mpcbc_footer_atomic_styles() {
    if ( ! class_exists( '\Elementor\Plugin' ) ) return;
    $footer_id = hello_mpcbc_footer_template_id();
    if ( $footer_id ) do_action( 'elementor/post/render', $footer_id );
}
add_action( 'wp_enqueue_scripts', 'hello_mpcbc_footer_atomic_styles', 5 );

/**
 * Display each menu item's Description field as a small second line in the navigation.
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of menu item.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 * @return string Modified menu item output.
 */
function hello_mpcbc_nav_menu_description( $item_output, $item, $depth, $args ) {
    if ( ! empty( $item->description ) ) {
        $desc_html = '<small class="menu-item-description" lang="' . ( function_exists( 'mpcbc_lang' ) && 'zh-hans' === mpcbc_lang() ? 'zh-Hans' : 'zh-Hant' ) . '">' . esc_html( $item->description ) . '</small>';
        // Insert right before the closing </a> tag
        $item_output = preg_replace( '/(<\/a>)/i', $desc_html . '$1', $item_output );
    }
    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'hello_mpcbc_nav_menu_description', 10, 4 );

/** Preserve retired page links through editable destination metadata. */
function hello_mpcbc_merged_page_redirect() {
    if ( ! is_page() || is_preview() || isset( $_GET['elementor-preview'] ) ) return;
    $target = absint( get_post_meta( get_queried_object_id(), '_mpcbc_redirect_page', true ) );
    if ( ! $target || $target === get_queried_object_id() ) return;
    $url = get_permalink( $target ); // keeps the /zh-hant/ or /zh-hans/ prefix (mpcbc-translations)
    $anchor = get_post_meta( get_queried_object_id(), '_mpcbc_redirect_anchor', true );
    if ( $anchor ) $url .= '#' . sanitize_title( $anchor );
    wp_safe_redirect( $url, 301 );
    exit;
}
add_action( 'template_redirect', 'hello_mpcbc_merged_page_redirect' );

