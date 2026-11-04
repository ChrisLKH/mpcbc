<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$manifest=json_decode(file_get_contents("$root/content-source/language-manifest.json"),true,512,JSON_THROW_ON_ERROR);
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$settings=get_post_meta($kit->get_id(),'_elementor_page_settings',true);$fonts=$settings['custom_typography']??[];
foreach(['zh'=>'Noto Sans TC','hans'=>'Noto Sans SC'] as $script=>$family){
 foreach(['h1'=>[64,48,36],'h2'=>[40,34,28],'card'=>[24,22,20],'body'=>[18,18,18]] as $type=>$sizes){
  $id=$type==='body'?"interior_$script":"interior_{$script}_$type";
  $fonts=array_values(array_filter($fonts,fn($f)=>$f['_id']!==$id));
  $fonts[]=['_id'=>$id,'title'=>"Interior / $script / $type",'typography_typography'=>'custom','typography_font_family'=>$family,'typography_font_size'=>['unit'=>'px','size'=>$sizes[0]],'typography_font_size_tablet'=>['unit'=>'px','size'=>$sizes[1]],'typography_font_size_mobile'=>['unit'=>'px','size'=>$sizes[2]],'typography_font_weight'=>$type==='body'?'400':'600','typography_line_height'=>['unit'=>'em','size'=>$type==='body'?1.9:1.4]];
 }
}
$settings['custom_typography']=$fonts;
$primaryMenu=wp_get_nav_menu_object('primary-menu');$settings['hello_header_menu']=$primaryMenu->term_id;
$kit->save(['settings'=>$settings]);
$locations=get_nav_menu_locations();$locations['menu-1']=$primaryMenu->term_id;set_theme_mod('nav_menu_locations',$locations);
$hubs=[];$results=[];
foreach(['zh-hant','zh-hans'] as $locale){
 $old=get_page_by_path($locale);$args=['post_type'=>'page','post_name'=>$locale,'post_title'=>$locale==='zh-hant'?'繁體中文':'简体中文','post_status'=>'publish'];if($old)$args['ID']=$old->ID;
 $hubs[$locale]=wp_insert_post($args,true);if(is_wp_error($hubs[$locale]))throw new RuntimeException('Hub creation failed.');
}
foreach($manifest as $p){
 $parent=$p['locale']==='en'?0:$hubs[$p['locale']];
 $slug=$p['topic']==='index'?$p['locale']:$p['topic'];$old=get_page_by_path(trim($p['path'],'/'));
 $args=['post_type'=>'page','post_name'=>$slug,'post_title'=>$p['title'],'post_status'=>'publish','post_parent'=>$p['topic']==='index'?0:$parent];if($old)$args['ID']=$old->ID;
 $id=wp_insert_post($args,true);if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 $exp=json_decode(file_get_contents("$root/elementor/{$p['file']}"),true,512,JSON_THROW_ON_ERROR);
 update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_template_type','wp-page');update_post_meta($id,'_wp_page_template','elementor_header_footer');
 update_post_meta($id,'_mpcbc_locale',$p['locale']);update_post_meta($id,'_mpcbc_translation_group',$p['topic']);
 $doc=Elementor\Plugin::$instance->documents->get($id);if(!$doc->save(['elements'=>$exp['content'],'settings'=>$exp['page_settings']]))throw new RuntimeException('Save failed '.$p['path']);
 if($p['locale']==='en')file_put_contents("$root/elementor/{$p['topic']}-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 $results[]=$p+['id'=>$id,'url'=>get_permalink($id)];
}
// Existing /zh/ becomes a clear directory for the two writing systems.
$hub=get_page_by_path('zh');$exp=json_decode(file_get_contents("$root/elementor/zh-hant-index.json"),true);
// The header is the single language control; the directory contains topic links only.
Elementor\Plugin::$instance->documents->get($hub->ID)->save(['elements'=>$exp['content'],'settings'=>['hide_title'=>'yes']]);
file_put_contents("$root/elementor/language-directory.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
file_put_contents("$root/elementor/zh-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$menu=wp_get_nav_menu_object('primary-menu');$items=wp_get_nav_menu_items($menu->term_id);$parentId=0;
foreach($items as $item)if($item->title==='中文')$parentId=$item->ID;
if(!$parentId)throw new RuntimeException('Language menu item missing.');
foreach(['English'=>'/','繁體中文'=>'/zh-hant/','简体中文'=>'/zh-hans/'] as $name=>$url){
 $match=0;foreach($items as $item)if($item->title===$name&&(int)$item->menu_item_parent===$parentId)$match=$item->ID;
 wp_update_nav_menu_item($menu->term_id,$match,['menu-item-title'=>$name,'menu-item-url'=>$url,'menu-item-type'=>'custom','menu-item-status'=>'publish','menu-item-parent-id'=>$parentId]);
}
// Language buttons in Welcome route to the correct script-specific index.
$raw=get_post_meta(17,'_elementor_data',true);$data=json_decode($raw,true);
$walk=function(&$ns)use(&$walk){foreach($ns as &$n){if(($n['widgetType']??'')==='button'){
 $s=&$n['settings'];if(str_contains($s['text']??'','繁體'))$s['link']['url']='/zh-hant/';if(str_contains($s['text']??'','简体'))$s['link']['url']='/zh-hans/';
 }if(!empty($n['elements']))$walk($n['elements']);}};$walk($data);Elementor\Plugin::$instance->documents->get(17)->save(['elements'=>$data]);
file_put_contents("$root/content-source/language-build-result.json",wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
Elementor\Plugin::$instance->files_manager->clear_cache();flush_rewrite_rules(false);echo count($results).' pages saved with three-language topic navigation.';
}catch(Throwable $e){echo get_class($e).': '.$e->getMessage().' line '.$e->getLine();}
