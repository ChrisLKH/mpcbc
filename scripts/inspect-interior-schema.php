<?php
try {
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();
$a=Elementor\Plugin::$instance->elements_manager->create_element_instance(['id'=>'schema01','elType'=>'widget','widgetType'=>'nested-accordion','settings'=>[],'elements'=>[]]);
$method=new ReflectionMethod($a,'get_default_children_elements');$method->setAccessible(true);
$data=['kit'=>$kit->get_settings(),'accordion_defaults'=>$a->get_settings(),'accordion_controls'=>$a->get_controls(),'accordion_children'=>$method->invoke($a),'menus'=>wp_get_nav_menu_items('primary-menu')];
file_put_contents('C:/Projects/mpcbc/content-source/interior-schema.json',wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo 'Saved native accordion defaults and kit settings.';
} catch (Throwable $e) { echo get_class($e).': '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine(); }
