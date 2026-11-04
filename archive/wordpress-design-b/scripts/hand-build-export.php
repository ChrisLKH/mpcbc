<?php
/**
 * Hand-build one small Elementor container and export it to learn the exact JSON keys.
 */
if ( ! did_action( 'elementor/loaded' ) ) {
    exit(1);
}

wp_set_current_user( 1 );

$post_id = wp_insert_post([
    'post_title'  => 'Test Hand Build B',
    'post_type'   => 'elementor_library',
    'post_status' => 'publish',
]);

wp_set_object_terms( $post_id, 'container', 'elementor_library_type' );
update_post_meta( $post_id, '_elementor_template_type', 'container' );
update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );

$elements = [
    [
        'id'       => 'test_container_b',
        'elType'   => 'container',
        'isInner'  => false,
        'settings' => [
            'content_width'         => 'boxed',
            'background_background' => 'classic',
            'background_color'      => '#F8F3F4',
            '__globals__'           => [
                'background_color' => 'globals/colors?id=card_bg',
            ],
            'padding'               => [
                'unit'     => 'px',
                'top'      => '40',
                'right'    => '40',
                'bottom'   => '40',
                'left'     => '40',
                'isLinked' => true,
            ],
        ],
        'elements' => [
            [
                'id'         => 'test_heading_b',
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => [
                    'title'       => 'Hand-built Heading B',
                    'title_color' => '#943759',
                    '__globals__' => [
                        'title_color' => 'globals/colors?id=primary',
                    ],
                ],
            ],
            [
                'id'         => 'test_button_b',
                'elType'     => 'widget',
                'widgetType' => 'button',
                'settings'   => [
                    'text'             => 'Hand-built Button B',
                    'background_color' => '#943759',
                    '__globals__'      => [
                        'background_color' => 'globals/colors?id=primary',
                    ],
                    'border_radius'    => [
                        'unit'     => 'px',
                        'top'      => '999',
                        'right'    => '999',
                        'bottom'   => '999',
                        'left'     => '999',
                        'isLinked' => true,
                    ],
                ],
            ],
        ],
    ],
];

update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );

$reflection = new ReflectionClass( Elementor\Plugin::$instance->templates_manager->get_source( 'local' ) );
$method = $reflection->getMethod( 'prepare_template_export' );
$method->setAccessible( true );
$exported_file = $method->invoke( Elementor\Plugin::$instance->templates_manager->get_source( 'local' ), $post_id );

file_put_contents( 'C:/Projects/mpcbc/wordpress-design-b/elementor/real-exported-sample.json', $exported_file['content'] );
echo "Exported sample container JSON saved to wordpress-design-b/elementor/real-exported-sample.json\n";
echo "Post ID: $post_id\n";
