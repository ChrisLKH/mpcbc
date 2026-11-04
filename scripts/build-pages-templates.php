<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit(1);
}

wp_set_current_user( 1 );

$elem_dir = __DIR__ . '/../elementor';

// Helper function to create or update an Elementor post
function save_elementor_item( $title, $slug, $post_type, $template_file, $page_template = 'elementor_header_footer', $library_type = '' ) {
    $json_content = json_decode( file_get_contents( $template_file ), true );
    if ( ! $json_content ) {
        echo "Failed to read JSON: $template_file\n";
        return 0;
    }

    $content = $json_content['content'];
    $page_settings = isset( $json_content['page_settings'] ) ? $json_content['page_settings'] : [];

    // Find existing post
    if ( $post_type === 'page' ) {
        $existing = get_page_by_path( $slug );
    } else {
        $q = new WP_Query([
            'post_type'      => 'elementor_library',
            'title'          => $title,
            'posts_per_page' => 1,
            'post_status'    => 'any',
        ]);
        $existing = $q->have_posts() ? $q->posts[0] : null;
    }

    $post_data = [
        'post_title'   => $title,
        'post_status'  => 'publish',
        'post_type'    => $post_type,
    ];
    if ( $slug ) {
        $post_data['post_name'] = $slug;
    }

    if ( $existing ) {
        $post_data['ID'] = $existing->ID;
        $post_id = wp_update_post( $post_data );
        echo "Updated $post_type '$title' (ID: $post_id)\n";
    } else {
        $post_id = wp_insert_post( $post_data );
        echo "Created $post_type '$title' (ID: $post_id)\n";
    }

    // Set meta
    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
    update_post_meta( $post_id, '_elementor_data', wp_slash( json_encode( $content ) ) );
    update_post_meta( $post_id, '_elementor_page_settings', $page_settings );

    if ( $post_type === 'page' ) {
        update_post_meta( $post_id, '_wp_page_template', $page_template );
        update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
    } else {
        update_post_meta( $post_id, '_elementor_template_type', $library_type ?: 'container' );
        if ( $library_type ) {
            wp_set_object_terms( $post_id, $library_type, 'elementor_library_type' );
        }
    }

    // Generate css file if possible
    try {
        if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
            $css_file = Elementor\Core\Files\CSS\Post::create( $post_id );
            $css_file->update();
        }
    } catch ( Exception $e ) {}

    return $post_id;
}

// 1. Footer template: "MPCBC – Footer"
$footer_id = save_elementor_item( 'MPCBC – Footer', 'mpcbc-footer', 'elementor_library', $elem_dir . '/footer-template.json', 'elementor_header_footer', 'container' );

// 2. Home page
$home_id = save_elementor_item( 'Home', 'home', 'page', $elem_dir . '/home-full.json', 'elementor_header_footer' );

// Set Home as Front Page
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
echo "Set Home (ID: $home_id) as front page\n";

// 3. Welcome page (Canvas layout)
$welcome_id = save_elementor_item( 'Welcome', 'welcome', 'page', $elem_dir . '/welcome-canvas.json', 'elementor_canvas' );

// 4. Prayer page
$prayer_id = save_elementor_item( 'Prayer', 'prayer', 'page', $elem_dir . '/prayer-page.json', 'elementor_header_footer' );

// 5. Coming soon page
$coming_soon_id = save_elementor_item( 'Coming soon', 'coming-soon', 'page', $elem_dir . '/coming-soon-page.json', 'elementor_header_footer' );

// 6. Menu placeholder pages
save_elementor_item( 'Visit 初次來訪', 'visit', 'page', $elem_dir . '/visit-page.json', 'elementor_header_footer' );
save_elementor_item( 'Sundays 主日崇拜', 'sundays', 'page', $elem_dir . '/sundays-page.json', 'elementor_header_footer' );
save_elementor_item( 'Kids & Youth 兒童及青少年', 'kids', 'page', $elem_dir . '/kids-page.json', 'elementor_header_footer' );
save_elementor_item( 'Watch 線上崇拜', 'watch', 'page', $elem_dir . '/watch-page.json', 'elementor_header_footer' );
save_elementor_item( 'About 關於我們', 'about', 'page', $elem_dir . '/about-page.json', 'elementor_header_footer' );
save_elementor_item( 'Give 奉獻', 'give', 'page', $elem_dir . '/give-page.json', 'elementor_header_footer' );
save_elementor_item( '中文', 'zh', 'page', $elem_dir . '/zh-page.json', 'elementor_header_footer' );

// Set site title to [Church logo]
update_option( 'blogname', '[Church logo]' );

// Clear Elementor Cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "All pages and templates built successfully!\n";
