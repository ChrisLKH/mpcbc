<?php
/**
 * Setup Elementor settings, Reading settings, and Global Colors/Fonts for Design B
 */

// 1. Reading settings: Discourage search engines
update_option( 'blog_public', '0' );
echo "blog_public set to 0 (search engines discouraged)\n";

// 2. Elementor general settings
update_option( 'elementor_disable_color_schemes', 'yes' );
update_option( 'elementor_disable_typography_schemes', 'yes' );
update_option( 'elementor_default_page_template', 'elementor_header_footer' );
// Enable flexbox containers (native containers)
update_option( 'elementor_experiment-container', 'active' );
echo "Elementor default colors & typography schemes disabled, default page template set to elementor_header_footer, containers active\n";

// 3. Essential Addons: enable only fancy-text
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
$ea_settings = [];
foreach ( $all_ea_keys as $k ) {
    $ea_settings[ $k ] = ( $k === 'fancy-text' ) ? 1 : 0;
}
update_option( 'eael_save_settings', $ea_settings );
echo "Essential Addons configured (only Fancy Text enabled)\n";

// 4. Elementor Active Kit Global Colors & Fonts
if ( did_action( 'elementor/loaded' ) ) {
    $kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
    if ( $kit ) {
        $page_settings = get_post_meta( $kit->get_id(), '_elementor_page_settings', true );
        if ( ! is_array( $page_settings ) ) {
            $page_settings = [];
        }

        // Global System Colors
        $page_settings['system_colors'] = [
            [
                '_id'   => 'primary',
                'title' => 'Primary',
                'color' => '#943759',
            ],
            [
                '_id'   => 'secondary',
                'title' => 'Secondary',
                'color' => '#1B1014',
            ],
            [
                '_id'   => 'text',
                'title' => 'Text',
                'color' => '#221419',
            ],
            [
                '_id'   => 'accent',
                'title' => 'Accent',
                'color' => '#C2410C',
            ],
        ];

        // Global Custom Colors
        $page_settings['custom_colors'] = [
            [
                '_id'   => 'primary_hover',
                'title' => 'Primary Hover',
                'color' => '#6F2841',
            ],
            [
                '_id'   => 'muted',
                'title' => 'Muted Text',
                'color' => '#6B5560',
            ],
            [
                '_id'   => 'card_bg',
                'title' => 'Card Blush',
                'color' => '#F8F3F4',
            ],
            [
                '_id'   => 'border',
                'title' => 'Border',
                'color' => '#E3D3D8',
            ],
            [
                '_id'   => 'live_sub',
                'title' => 'Live Sub',
                'color' => '#E8836B',
            ],
            [
                '_id'   => 'white',
                'title' => 'White',
                'color' => '#FFFFFF',
            ],
        ];

        // Global Typography
        $page_settings['system_typography'] = [
            [
                '_id' => 'primary',
                'title' => 'Primary',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_weight' => '600',
            ],
            [
                '_id' => 'secondary',
                'title' => 'Secondary',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_weight' => '700',
            ],
            [
                '_id' => 'text',
                'title' => 'Text',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_weight' => '400',
            ],
            [
                '_id' => 'accent',
                'title' => 'Accent',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Noto Serif TC',
                'typography_font_weight' => '600',
            ],
        ];

        // Theme Style - Buttons
        $page_settings['button_border_radius'] = [
            'unit' => 'px',
            'top' => '999',
            'right' => '999',
            'bottom' => '999',
            'left' => '999',
            'isLinked' => true,
        ];

        // Layout - Container Padding = 0 (all sides, all breakpoints)
        $page_settings['container_padding'] = [
            'unit' => 'px',
            'top' => '0',
            'right' => '0',
            'bottom' => '0',
            'left' => '0',
            'isLinked' => true,
        ];
        $page_settings['container_padding_tablet'] = [
            'unit' => 'px',
            'top' => '0',
            'right' => '0',
            'bottom' => '0',
            'left' => '0',
            'isLinked' => true,
        ];
        $page_settings['container_padding_mobile'] = [
            'unit' => 'px',
            'top' => '0',
            'right' => '0',
            'bottom' => '0',
            'left' => '0',
            'isLinked' => true,
        ];

        update_post_meta( $kit->get_id(), '_elementor_page_settings', $page_settings );
        Elementor\Plugin::$instance->files_manager->clear_cache();
        echo "Elementor Active Kit (" . $kit->get_id() . ") updated with Design B global colors, typography & 0 container padding!\n";
    }
}
