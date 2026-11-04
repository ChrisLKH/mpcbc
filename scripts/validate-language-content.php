<?php
$root='C:/Projects/mpcbc';$results=json_decode(file_get_contents("$root/content-source/language-build-result.json"),true);$report=[];
foreach($results as $p){
 $data=json_decode(get_post_meta($p['id'],'_elementor_data',true),true);$widgets=[];$controls=[];$walk=function($ns)use(&$walk,&$widgets,&$controls){foreach($ns as $n){$type=$n['widgetType']??$n['elType'];$widgets[$type]=($widgets[$type]??0)+1;$label=$n['settings']['text']??'';if(is_array($label))$label=$label['value']??'';if(in_array($type,['button','e-button'],true)&&in_array($label,['English','繁體中文','简体中文']))$controls[]=$n['id'];if($type==='html')throw new RuntimeException('HTML layout widget');$editor=$n['settings']['editor']??$n['settings']['paragraph']??'';if(is_array($editor))$editor=$editor['value']??'';if(preg_match('/style\s*=/i',$editor))throw new RuntimeException('Inline style');$walk($n['elements']??[]);}};$walk($data);
 if($controls)throw new RuntimeException('Duplicate language controls');
 if($p['topic']==='beliefs'&&(($widgets['nested-tabs']??0)+($widgets['e-tabs']??0))!==1)throw new RuntimeException('Missing topic reader');
 $report[]=['id'=>$p['id'],'path'=>$p['path'],'locale'=>get_post_meta($p['id'],'_mpcbc_locale',true),'widgets'=>$widgets,'language_controls_in_content'=>count($controls)];
}
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$settings=get_post_meta($kit->get_id(),'_elementor_page_settings',true);$before=json_decode(file_get_contents("$root/content-source/interior-schema.json"),true)['kit'];
if($settings['system_colors']!==$before['system_colors'])throw new RuntimeException('System palette changed');
$originalCustom=array_column($before['custom_colors'],null,'_id');$currentCustom=array_column($settings['custom_colors'],null,'_id');
foreach($originalCustom as $id=>$color)if(($currentCustom[$id]??null)!==$color)throw new RuntimeException('Existing palette changed: '.$id);
foreach(array_diff(array_keys($currentCustom),array_keys($originalCustom)) as $id)if(!in_array($id,['hero_video_top','hero_video_bottom','hero_rail_line'],true))throw new RuntimeException('Unrecognised palette addition: '.$id);
file_put_contents("$root/content-source/locale-content-validation.json",wp_json_encode(['pages'=>$report,'palette_preserved'=>true,'menu_location'=>get_nav_menu_locations()],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));echo count($report).' native page variants verified; palette preserved; no in-page language controls.';

