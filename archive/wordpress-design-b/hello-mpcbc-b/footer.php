<?php
/**
 * Footer template for Hello MPCBC B Child Theme.
 *
 * Renders the Elementor template titled "MPCBC – Footer" on every page,
 * followed by wp_footer().
 *
 * @package Hello_MPCBC_B
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
        echo '<footer id="colophon" class="site-footer" role="contentinfo">' . \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id ) . '</footer>';
        $footer_rendered = true;
    }
    wp_reset_postdata();
}

// Fallback footer if template is not yet published
if ( ! $footer_rendered ) {
    ?>
    <footer id="colophon" class="site-footer mpcbc-fallback-footer" role="contentinfo">
        <div>
            <p>Monterey Park Chinese Baptist Church</p>
            <p>Since 1976 · Monterey Park, California</p>
        </div>
        <div>
            <span lang="zh-Hant">蒙迪園華人浸信會</span>
        </div>
    </footer>
    <?php
}

wp_footer();
?>
</body>
</html>
