<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-atomic-conversion";wp_mkdir_p($backup);
$pages=get_posts(['post_type'=>['page','elementor_library'],'post_status'=>['publish','draft'],'numberposts'=>-1]);$manifest=[];
foreach($pages as $p){$data=get_post_meta($p->ID,'_elementor_data',true);if(!$data)continue;$path="$backup/{$p->ID}.json";if(!file_exists($path))file_put_contents($path,wp_json_encode(['id'=>$p->ID,'title'=>$p->post_title,'data'=>json_decode($data,true),'settings'=>get_post_meta($p->ID,'_elementor_page_settings',true)],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));$manifest[]=['id'=>$p->ID,'title'=>$p->post_title,'locale'=>get_post_meta($p->ID,'_mpcbc_locale',true)];}
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();if(!file_exists("$backup/kit.json"))file_put_contents("$backup/kit.json",wp_json_encode(get_post_meta($kit->get_id(),'_elementor_page_settings',true),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
if(!file_exists("$backup/menu.json"))file_put_contents("$backup/menu.json",wp_json_encode(wp_get_nav_menu_items(3),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
file_put_contents("$backup/manifest.json",wp_json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$helper='Elementor\Modules\Mcp\Abilities\Utils\Widget_Context_Helper';$widgets=[];$errors=[];
foreach(array_merge(Elementor\Plugin::$instance->widgets_manager->get_widget_types(),Elementor\Plugin::$instance->elements_manager->get_element_types()) as $type=>$instance){if(!str_starts_with($type,'e-'))continue;try{$widgets[$type]=$instance->get_config();}catch(Throwable $e){$errors[$type]=$e->getMessage();}}
file_put_contents("$root/content-source/atomic-eligible-widgets.json",wp_json_encode($widgets,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo 'Backed up '.count($manifest).' native documents and kit. Atomic types: '.implode(', ',array_keys($widgets)).' Errors: '.wp_json_encode($errors);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
