<?php
/**
 * Footer template for Hello MPCBC Child Theme.
 *
 * Renders the Elementor template titled "MPCBC – Footer" on every page,
 * followed by wp_footer().
 *
 * @package Hello_MPCBC
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$footer_rendered = false;

if ( class_exists( '\Elementor\Plugin' ) ) {
    // Look up template titled "MPCBC – Footer" (or "MPCBC - Footer")
    $footer_query = new WP_Query( array(
        'post_type'      => 'elementor_library',
        'title'          => 'MPCBC – Footer',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ) );

    // Also check standard hyphen if en-dash was not used
    if ( ! $footer_query->have_posts() ) {
        $footer_query = new WP_Query( array(
            'post_type'      => 'elementor_library',
            'title'          => 'MPCBC - Footer',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );
    }

    if ( $footer_query->have_posts() ) {
        $footer_id = $footer_query->posts[0]->ID;
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
        $footer_rendered = true;
    }
    wp_reset_postdata();
}

// Fallback footer if template is not yet published
if ( ! $footer_rendered ) {
    ?>
    <footer id="colophon" class="site-footer mpcbc-fallback-footer" role="contentinfo" style="background:#0A1F26; color:#ffffff; padding:48px 56px; display:flex; justify-content:space-between; align-items:center; font-family:'Geist',sans-serif;">
        <div>
            <p style="margin:0 0 6px; font-weight:600;">Monterey Park Chinese Baptist Church</p>
            <p style="margin:0; font-size:14px; opacity:0.75;">Since 1976 · Monterey Park, California</p>
        </div>
        <div>
            <span lang="zh-Hant" style="font-size:15px; opacity:0.85;">[教會中文名稱]</span>
        </div>
    </footer>
    <?php
}

wp_footer();
?>
</body>
</html>
