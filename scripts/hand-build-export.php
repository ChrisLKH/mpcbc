<?php
if ( ! did_action( 'elementor/loaded' ) ) {
    exit(1);
}

// Ensure an admin user is set so capability checks pass
wp_set_current_user( 1 );

// Let's create an Elementor template post
$post_id = wp_insert_post([
    'post_title'  => 'Test Hand Build',
    'post_type'   => 'elementor_library',
    'post_status' => 'publish',
]);

wp_set_object_terms( $post_id, 'container', 'elementor_library_type' );
update_post_meta( $post_id, '_elementor_template_type', 'container' );
update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );

// Content structure with container, heading, button, background colour, and Global Colour (__globals__)
$elements = [
    [
        'id'       => 'test_container',
        'elType'   => 'container',
        'isInner'  => false,
        'settings' => [
            'background_background' => 'classic',
            'background_color'      => '#FAF7F2',
            '__globals__'           => [
                'background_color' => 'globals/colors?id=primary',
            ],
        ],
        'elements' => [
            [
                'id'         => 'test_heading',
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => [
                    'title'       => 'Hand-built Heading',
                    'title_color' => '#0E6B5C',
                    '__globals__' => [
                        'title_color' => 'globals/colors?id=primary',
                    ],
                ],
            ],
            [
                'id'         => 'test_button',
                'elType'     => 'widget',
                'widgetType' => 'button',
                'settings'   => [
                    'text'             => 'Hand-built Button',
                    'background_color' => '#0E6B5C',
                    '__globals__'      => [
                        'background_color' => 'globals/colors?id=primary',
                    ],
                ],
            ],
        ],
    ],
];

update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );

// Now let's use Elementor's document to export data
$document = Elementor\Plugin::$instance->documents->get( $post_id );
$export_data = $document->get_export_data();

echo "Document export data:\n";
print_r( $export_data );

// Let's also test Elementor's local source export
$reflection = new ReflectionClass( Elementor\Plugin::$instance->templates_manager->get_source( 'local' ) );
$method = $reflection->getMethod( 'prepare_template_export' );
$method->setAccessible( true );
$exported_file = $method->invoke( Elementor\Plugin::$instance->templates_manager->get_source( 'local' ), $post_id );

echo "\nFull exported JSON structure:\n";
echo $exported_file['content'] . "\n";

// Save to disk for exact comparison
file_put_contents( __DIR__ . '/../elementor/real-exported-sample.json', $exported_file['content'] );
echo "\nSaved sample to wordpress/elementor/real-exported-sample.json\n";
