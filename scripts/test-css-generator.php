<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);
wp_set_current_user(1);

$post_id = wp_insert_post([
    'post_title' => 'Test Layout CSS',
    'post_type' => 'elementor_library',
    'post_status' => 'publish'
]);
update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $post_id, '_elementor_template_type', 'container' );
update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );

// Test settings
$elements = [
    [
        'id' => 'row_parent',
        'elType' => 'container',
        'isInner' => false,
        'settings' => [
            'content_width' => 'boxed',
            'boxed_width' => [ 'unit' => 'px', 'size' => 1328 ],
            'flex_direction' => 'row',
            'flex_direction_tablet' => 'row',
            'flex_direction_mobile' => 'column',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'space-between',
            'flex_align_items' => 'center',
            'flex_gap' => [ 'column' => '18', 'row' => '18', 'unit' => 'px', 'isLinked' => true ],
        ],
        'elements' => [
            [
                'id' => 'card_child_1',
                'elType' => 'container',
                'isInner' => true,
                'settings' => [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 25 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 50 ],
                    'width_mobile' => [ 'unit' => '%', 'size' => 100 ],
                    'min_height' => [ 'unit' => 'px', 'size' => 560 ],
                ],
                'elements' => [
                    [
                        'id' => 'card_h3',
                        'elType' => 'widget',
                        'widgetType' => 'heading',
                        'isInner' => false,
                        'settings' => [
                            'title' => 'English',
                            'typography_typography' => 'custom',
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 40 ],
                            'typography_font_size_tablet' => [ 'unit' => 'px', 'size' => 32 ],
                            'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 24 ],
                        ],
                        'elements' => []
                    ]
                ]
            ]
        ]
    ]
];

update_post_meta( $post_id, '_elementor_data', wp_slash( json_encode( $elements ) ) );

$css = Elementor\Core\Files\CSS\Post::create( $post_id );
$css->update();

echo "=== GENERATED CSS FOR TEST POST $post_id ===\n";
echo file_get_contents( $css->get_path() );
echo "\n============================================\n";

wp_delete_post( $post_id, true );
