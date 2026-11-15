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

    // A small routing layer keeps language choices across shared and translated pages.
    $language_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish',
        'numberposts' => -1, 'meta_key' => '_mpcbc_translation_group' ) );
    $routes = array();
    foreach ( $language_pages as $page ) {
        $topic = get_post_meta( $page->ID, '_mpcbc_translation_group', true );
        $locale = get_post_meta( $page->ID, '_mpcbc_locale', true );
        if ( in_array( $locale, array( 'en', 'zh-hant', 'zh-hans' ), true ) ) {
            $routes[ $topic ][ $locale ] = get_permalink( $page );
        }
    }
    $routes['index']['en'] = home_url( '/' );
    $routes['home']['en'] = home_url( '/' );
    $routes['home']['zh-hant'] = home_url( '/' );
    $routes['home']['zh-hans'] = home_url( '/' );
    // Traditional text on the page and menu is converted in the browser when 简体中文 is chosen.
    wp_enqueue_script( 'hello-mpcbc-language', get_stylesheet_directory_uri() . '/language-navigation.js',
        array(), filemtime( get_stylesheet_directory() . '/language-navigation.js' ), false );
    wp_localize_script( 'hello-mpcbc-language', 'mpcbcLanguages', array(
        'home' => home_url( '/' ), 'routes' => $routes,
        'converter' => get_stylesheet_directory_uri() . '/vendor/opencc-js/t2cn.js?ver=1.4.2',
        'locale' => get_post_meta( get_queried_object_id(), '_mpcbc_locale', true ),
        'topic' => get_post_meta( get_queried_object_id(), '_mpcbc_translation_group', true ),
    ) );

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
        $desc_html = '<small class="menu-item-description" lang="zh-Hant">' . esc_html( $item->description ) . '</small>';
        // Insert right before the closing </a> tag
        $item_output = preg_replace( '/(<\/a>)/i', $desc_html . '$1', $item_output );
    }
    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'hello_mpcbc_nav_menu_description', 10, 4 );

/** Use each translated page's language metadata for the document and alternates. */
function hello_mpcbc_page_language( $attributes ) {
    $locale = get_post_meta( get_queried_object_id(), '_mpcbc_locale', true );
    $tags = array( 'en' => 'en', 'zh-hant' => 'zh-Hant', 'zh-hans' => 'zh-Hans' );
    return isset( $tags[ $locale ] )
        ? preg_replace( '/\blang="[^"]*"/', 'lang="' . $tags[ $locale ] . '"', $attributes )
        : $attributes;
}
add_filter( 'language_attributes', 'hello_mpcbc_page_language' );

/** Preserve retired page links through editable destination metadata. */
function hello_mpcbc_merged_page_redirect() {
    if ( ! is_page() || is_preview() || isset( $_GET['elementor-preview'] ) ) return;
    $target = absint( get_post_meta( get_queried_object_id(), '_mpcbc_redirect_page', true ) );
    if ( ! $target || $target === get_queried_object_id() ) return;
    $url = get_permalink( $target );
    $locale = isset( $_GET['lang'] ) ? sanitize_text_field( wp_unslash( $_GET['lang'] ) ) : get_post_meta( get_queried_object_id(), '_mpcbc_redirect_locale', true );
    if ( in_array( $locale, array( 'en', 'zh-hant', 'zh-hans' ), true ) ) $url = add_query_arg( 'lang', $locale, $url );
    $anchor = get_post_meta( get_queried_object_id(), '_mpcbc_redirect_anchor', true );
    if ( $anchor ) $url .= '#' . sanitize_title( $anchor );
    wp_safe_redirect( $url, 301 );
    exit;
}
add_action( 'template_redirect', 'hello_mpcbc_merged_page_redirect' );

function hello_mpcbc_language_alternates() {
    $group = get_post_meta( get_queried_object_id(), '_mpcbc_translation_group', true );
    if ( ! is_page() || ! $group ) { return; }
    $pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1,
        'meta_key' => '_mpcbc_translation_group', 'meta_value' => $group ) );
    $tags = array( 'en' => 'en', 'zh-hant' => 'zh-Hant', 'zh-hans' => 'zh-Hans' );
    foreach ( $pages as $page ) {
        $locale = get_post_meta( $page->ID, '_mpcbc_locale', true );
        if ( isset( $tags[ $locale ] ) ) {
            printf( '<link rel="alternate" hreflang="%s" href="%s">' . "\n", esc_attr( $tags[ $locale ] ), esc_url( get_permalink( $page ) ) );
        }
    }
}
add_action( 'wp_head', 'hello_mpcbc_language_alternates' );

/** Keep the shared header language switch on the current translated topic. */
function hello_mpcbc_language_menu_links( $items ) {
    $group = get_post_meta( get_queried_object_id(), '_mpcbc_translation_group', true );
    $pages = $group ? get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1,
        'meta_key' => '_mpcbc_translation_group', 'meta_value' => $group ) ) : array();
    $urls = array( 'en' => home_url( '/' ), 'zh-hant' => home_url( '/zh-hant/' ), 'zh-hans' => home_url( '/zh-hans/' ) );
    foreach ( $pages as $page ) { $urls[ get_post_meta( $page->ID, '_mpcbc_locale', true ) ] = get_permalink( $page ); }
    // A topic with one page (e.g. Give) holds all three languages itself.
    if ( 1 === count( $pages ) ) { $urls = array_fill_keys( array( 'en', 'zh-hant', 'zh-hans' ), get_permalink( $pages[0] ) ); }
    if ( 'index' === $group ) { $urls['en'] = home_url( '/' ); }
    if ( 'home' === $group || is_front_page() ) {
        $urls['en'] = home_url( '/' );
        $urls['zh-hant'] = home_url( '/' );
        $urls['zh-hans'] = home_url( '/' );
    }
    $locales = array( 'English' => 'en', '繁體中文' => 'zh-hant', '简体中文' => 'zh-hans' );
    // The language menu is the parent of 繁體中文 / 简体中文; Worship's "English" congregation item is not a language link.
    $parents = array();
    foreach ( $items as $item ) {
        if ( in_array( $item->title, array( '繁體中文', '简体中文' ), true ) ) $parents[] = (int) $item->menu_item_parent;
    }
    foreach ( $items as $item ) {
        if ( in_array( (int) $item->menu_item_parent, $parents, true ) && isset( $locales[ $item->title ], $urls[ $locales[ $item->title ] ] ) ) {
            $item->url = $urls[ $locales[ $item->title ] ];
            $item->classes[] = 'mpcbc-locale-' . $locales[ $item->title ];
        }
    }
    foreach ( $items as $item ) {
        if ( in_array( (int) $item->ID, $parents, true ) ) { $item->classes[] = 'mpcbc-language-menu'; }
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'hello_mpcbc_language_menu_links' );
