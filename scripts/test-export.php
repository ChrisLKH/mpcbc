<?php
// Ensure Elementor is loaded
if ( ! did_action( 'elementor/loaded' ) ) {
    echo "Elementor not loaded\n";
    exit(1);
}

// Check Global Colors
$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
$kit_settings = $kit->get_settings();
echo "Active Kit ID: " . $kit->get_id() . "\n";
echo "Kit custom colors:\n";
print_r( $kit->get_settings('custom_colors') );
echo "Kit system colors:\n";
print_r( $kit->get_settings('system_colors') );
