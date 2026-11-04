<?php
$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
$settings = get_post_meta( $kit->get_id(), '_elementor_page_settings', true );

// Change primary to #7A2E5E
$settings['system_colors'][0]['color'] = '#7A2E5E';
update_post_meta( $kit->get_id(), '_elementor_page_settings', $settings );
Elementor\Plugin::$instance->files_manager->clear_cache();

// Request the page via HTTP so Elementor regenerates CSS
$html = file_get_contents( 'http://mpcbc.local/' );

// Check post-5.css on disk
$css_path = 'C:\Users\chris\Local Sites\mpcbc\app\public\wp-content\uploads\elementor\css\post-5.css';
if ( file_exists( $css_path ) ) {
    $css_content = file_get_contents( $css_path );
    echo "Found post-5.css!\n";
    echo "Contains #7A2E5E: " . ( strpos( $css_content, '#7A2E5E' ) !== false ? "YES" : "NO" ) . "\n";
} else {
    echo "post-5.css not yet on disk, checking HTML for inline kit styles...\n";
    echo "Contains #7A2E5E in HTML: " . ( strpos( $html, '#7A2E5E' ) !== false ? "YES" : "NO" ) . "\n";
}

// Restore back to #0E6B5C
$settings['system_colors'][0]['color'] = '#0E6B5C';
update_post_meta( $kit->get_id(), '_elementor_page_settings', $settings );
Elementor\Plugin::$instance->files_manager->clear_cache();
file_get_contents( 'http://mpcbc.local/' );
echo "Restored Primary to #0E6B5C.\n";
