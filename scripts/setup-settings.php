<?php
// 1. Discourage search engines
update_option( 'blog_public', '0' );
echo "blog_public set to 0 (search engines discouraged)\n";

// 2. Elementor settings
update_option( 'elementor_disable_color_schemes', 'yes' );
update_option( 'elementor_disable_typography_schemes', 'yes' );
update_option( 'elementor_default_page_template', 'elementor_header_footer' );
echo "Elementor default colors & typography schemes disabled, default page template set to elementor_header_footer\n";

// 3. Essential Addons: only fancy-text enabled
$ea_settings = get_option( 'eael_save_settings', [] );
// Turn off all eael elements, enable only fancy-text
$all_ea_keys = [
    'post-grid', 'post-timeline', 'fancy-text', 'creative-btn', 'static-product', 'event-calendar',
    'team-members', 'testimonials', 'flip-box', 'info-box', 'dual-color-header', 'cta-box',
    'flip-carousel', 'interactive-cards', 'ninja-form', 'gravity-form', 'caldera-form', 'wpforms',
    'contact-form-7', 'weforms', 'formidable-form', 'typeform', 'mailchimp', 'price-table',
    'price-menu', 'filter-gallery', 'image-accordion', 'image-hotspots', 'image-comparison',
    'feature-list', 'toggle', 'progress-bar', 'interactive-promo', 'counter', 'tooltip',
    'advanced-tabs', 'advanced-accordion', 'data-table', 'betterdocs-category-grid',
    'betterdocs-category-box', 'betterdocs-search-form'
];

foreach ( $all_ea_keys as $k ) {
    $ea_settings[ $k ] = ( $k === 'fancy-text' ) ? 1 : 0;
}
update_option( 'eael_save_settings', $ea_settings );
echo "Essential Addons configured (only Fancy Text is enabled)\n";
