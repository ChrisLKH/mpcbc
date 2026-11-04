<?php
require 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
// Check registered nav menus
$locations = get_registered_nav_menus();
echo "Theme locations:\n";
print_r( $locations );

// Create menu 'Primary Menu' if not exists
$menu_name = 'Primary Menu';
$menu_exists = wp_get_nav_menu_object( $menu_name );

if ( ! $menu_exists ) {
    $menu_id = wp_create_nav_menu( $menu_name );
    echo "Created menu ID: $menu_id\n";
} else {
    $menu_id = $menu_exists->term_id;
    echo "Existing menu ID: $menu_id\n";
    // Delete existing items to avoid duplicates
    $existing_items = wp_get_nav_menu_items( $menu_id );
    if ( $existing_items ) {
        foreach ( $existing_items as $item ) {
            wp_delete_post( $item->ID, true );
        }
    }
}

$menu_items = [
    [
        'title'       => 'Visit',
        'description' => '初次來訪',
        'url'         => '/visit/',
        'classes'     => '',
    ],
    [
        'title'       => 'Sundays',
        'description' => '主日崇拜',
        'url'         => '/sundays/',
        'classes'     => '',
    ],
    [
        'title'       => 'Kids & Youth',
        'description' => '兒童及青少年',
        'url'         => '/kids/',
        'classes'     => '',
    ],
    [
        'title'       => 'Watch',
        'description' => '線上崇拜',
        'url'         => '/watch/',
        'classes'     => '',
    ],
    [
        'title'       => 'About',
        'description' => '關於我們',
        'url'         => '/about/',
        'classes'     => '',
    ],
    [
        'title'       => 'Give',
        'description' => '奉獻',
        'url'         => '/give/',
        'classes'     => '',
    ],
    [
        'title'       => '中文',
        'description' => '',
        'url'         => '/zh/',
        'classes'     => 'lang-switch',
    ],
    [
        'title'       => 'Plan your visit',
        'description' => '計劃來訪',
        'url'         => '/visit/',
        'classes'     => 'button',
    ],
];

foreach ( $menu_items as $item ) {
    $item_id = wp_update_nav_menu_item( $menu_id, 0, [
        'menu-item-title'       => $item['title'],
        'menu-item-description' => $item['description'],
        'menu-item-url'         => $item['url'],
        'menu-item-classes'     => $item['classes'],
        'menu-item-status'      => 'publish',
        'menu-item-type'        => 'custom',
    ] );
    echo "Added item: " . $item['title'] . " (ID: $item_id)\n";
}

// Assign to all available locations
$menu_locations = get_theme_mod( 'nav_menu_locations' );
if ( ! is_array( $menu_locations ) ) {
    $menu_locations = [];
}
foreach ( array_keys( $locations ) as $loc_key ) {
    $menu_locations[ $loc_key ] = $menu_id;
}
set_theme_mod( 'nav_menu_locations', $menu_locations );
echo "Assigned menu to locations: " . implode( ', ', array_keys( $locations ) ) . "\n";
