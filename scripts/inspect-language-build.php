<?php
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();
$settings=get_post_meta($kit->get_id(),'_elementor_page_settings',true);
echo wp_json_encode(['locations'=>get_nav_menu_locations(),'menus'=>wp_get_nav_menus(),'header'=>array_filter($settings,fn($k)=>str_contains($k,'header')||str_contains($k,'menu'),ARRAY_FILTER_USE_KEY),'all_setting_keys'=>array_keys($settings)]);
