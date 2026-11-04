<?php
try {
$root='C:/Projects/mpcbc';
$before=json_decode(file_get_contents("$root/content-source/before-bottom-rail/home.json"),true,512,JSON_THROW_ON_ERROR);
$after=json_decode(get_post_meta(21,'_elementor_data',true),true,512,JSON_THROW_ON_ERROR);
$originalRest=array_values(array_filter($before,fn($n)=>!in_array($n['id'],['hero_container','times_outer_section'])));
$currentRest=array_values(array_filter($after,fn($n)=>$n['id']!=='hero_container'));
if($originalRest!==$currentRest)throw new RuntimeException('Other Home sections changed.');
$nodes=[];$walk=function($ns)use(&$walk,&$nodes){foreach($ns as $n){$nodes[$n['id']]=$n;$walk($n['elements']??[]);}};$walk([$after[0]]);
$widgets=[];foreach($nodes as $n){if(($n['elType']??'')==='widget'){
 $type=$n['widgetType'];$widgets[$type]=($widgets[$type]??0)+1;
 if(!in_array($type,['heading','button']))throw new RuntimeException('Unexpected hero widget.');
 if(preg_match('/style\s*=/',wp_json_encode($n['settings'])))throw new RuntimeException('Inline style.');
 if(empty($n['settings']['__globals__']))throw new RuntimeException('Missing Global styling.');
}}
foreach(['canto'=>'11:00','english'=>'11:00','mandarin'=>'9:00','children'=>'10:00'] as $key=>$time)if($nodes["rail_digits_$key"]['settings']['title']!==$time)throw new RuntimeException('Service time mismatch.');
if($nodes['rail_services']['settings']['_element_id']!=='sunday-times')throw new RuntimeException('Missing service anchor.');
$exp=json_decode(file_get_contents("$root/elementor/home-content-filled.json"),true);if($exp['content']!==$after)throw new RuntimeException('Export differs from saved Home.');
file_put_contents("$root/content-source/bottom-rail-validation.json",wp_json_encode(['site'=>home_url(),'other_home_sections_unchanged'=>true,'native_widget_counts'=>$widgets,'service_times_verified'=>true,'global_styles'=>true,'export_matches_saved_home'=>true,'no_inline_styles'=>true,'menu_location'=>get_nav_menu_locations()],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));echo 'Verified native rail, facts, export, Global styles and unchanged remaining Home sections.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
