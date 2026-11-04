<?php
/**
 * Create navigation menu for Design B
 */
$locations = get_registered_nav_menus();
echo "Theme locations:\n";
print_r( $locations );

$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object( $menu_name );

if ( ! $menu_exists ) {
    $menu_id = wp_create_nav_menu( $menu_name );
    echo "Created menu ID: $menu_id\n";
} else {
    $menu_id = $menu_exists->term_id;
    echo "Existing menu ID: $menu_id\n";
    $existing_items = wp_get_nav_menu_items( $menu_id );
    if ( $existing_items ) {
        foreach ( $existing_items as $item ) {
            wp_delete_post( $item->ID, true );
        }
    }
}

// 1. About (Parent)
$about_id = wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'About',
    'menu-item-description' => '關於我們',
    'menu-item-url'         => '/#welcome',
    'menu-item-classes'     => '',
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// About Children
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Our story 我們的故事',
    'menu-item-description' => 'Fifty years in Monterey Park',
    'menu-item-url'         => '/#welcome',
    'menu-item-parent-id'   => $about_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Our pastors 教牧同工',
    'menu-item-description' => 'Three congregations, one eldership',
    'menu-item-url'         => '/#leaders',
    'menu-item-parent-id'   => $about_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Children & families 兒童及家庭',
    'menu-item-description' => 'Sunday school and nursery',
    'menu-item-url'         => '/#children',
    'menu-item-parent-id'   => $about_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Contact the office',
    'menu-item-description' => '',
    'menu-item-url'         => '/#connect',
    'menu-item-parent-id'   => $about_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// 2. Services (Parent)
$services_id = wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Services',
    'menu-item-description' => '崇拜',
    'menu-item-url'         => '/#sunday',
    'menu-item-classes'     => '',
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// Services Children
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'English Worship',
    'menu-item-description' => '11:00 AM · page & sermon archive',
    'menu-item-url'         => '/#sunday',
    'menu-item-parent-id'   => $services_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => '粵語堂 Cantonese',
    'menu-item-description' => '9:30 AM · page & sermon archive',
    'menu-item-url'         => '/#sunday',
    'menu-item-parent-id'   => $services_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => '國語堂 Mandarin',
    'menu-item-description' => '11:30 AM · page & sermon archive',
    'menu-item-url'         => '/#sunday',
    'menu-item-parent-id'   => $services_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'All services this Sunday',
    'menu-item-description' => '',
    'menu-item-url'         => '/#sunday',
    'menu-item-parent-id'   => $services_id,
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// 3. Newsletter
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Newsletter',
    'menu-item-description' => '通訊',
    'menu-item-url'         => '/#news',
    'menu-item-classes'     => '',
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// 4. Prayer
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Prayer',
    'menu-item-description' => '代禱',
    'menu-item-url'         => '/#connect',
    'menu-item-classes'     => '',
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// 5. Give (Pill button)
wp_update_nav_menu_item( $menu_id, 0, [
    'menu-item-title'       => 'Give',
    'menu-item-description' => '奉獻',
    'menu-item-url'         => '/#give',
    'menu-item-classes'     => 'button',
    'menu-item-status'      => 'publish',
    'menu-item-type'        => 'custom',
] );

// Assign to all registered menu locations
$menu_locations = get_theme_mod( 'nav_menu_locations' );
if ( ! is_array( $menu_locations ) ) {
    $menu_locations = [];
}
foreach ( array_keys( $locations ) as $loc_key ) {
    $menu_locations[ $loc_key ] = $menu_id;
}
set_theme_mod( 'nav_menu_locations', $menu_locations );
echo "Assigned menu to locations: " . implode( ', ', array_keys( $locations ) ) . "\n";
