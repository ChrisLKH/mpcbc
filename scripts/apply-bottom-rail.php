<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-bottom-rail";wp_mkdir_p($backup);
$data=json_decode(get_post_meta(21,'_elementor_data',true),true,512,JSON_THROW_ON_ERROR);
$refinement="$root/content-source/before-rail-refinement";wp_mkdir_p($refinement);
if(!file_exists("$refinement/home.json"))file_put_contents("$refinement/home.json",wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
if(!file_exists("$backup/home.json"))file_put_contents("$backup/home.json",wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$settings=get_post_meta($kit->get_id(),'_elementor_page_settings',true);
if(!file_exists("$backup/kit.json"))file_put_contents("$backup/kit.json",wp_json_encode($settings,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
foreach(['hero_video_top'=>['Hero / video top','rgba(10,31,38,0.12)'],'hero_video_bottom'=>['Hero / video bottom','rgba(10,31,38,0.97)'],'hero_rail_line'=>['Hero / service dividers','rgba(255,255,255,0.3)']] as $id=>$value){
 $settings['custom_colors']=array_values(array_filter($settings['custom_colors'],fn($c)=>$c['_id']!==$id));$settings['custom_colors'][]=['_id'=>$id,'title'=>$value[0],'color'=>$value[1]];
}
foreach(['title'=>['Bricolage Grotesque',64,52,32,700,1.1],'church_zh'=>['Noto Sans TC',28,26,22,600,1.5],'intro'=>['Geist',22,20,18,500,1.5],'label'=>['Geist',16,16,14,600,1.4],'kicker'=>['Geist',14,14,14,600,1.5],'zh'=>['Noto Sans TC',16,16,14,500,1.5],'time'=>['Bricolage Grotesque',36,34,32,700,1.1],'small'=>['Geist',14,14,14,500,1.5]] as $type=>$v){
 $fid="rail_$type";$settings['custom_typography']=array_values(array_filter($settings['custom_typography'],fn($f)=>$f['_id']!==$fid));
 $settings['custom_typography'][]=['_id'=>$fid,'title'=>"Hero rail / $type",'typography_typography'=>'custom','typography_font_family'=>$v[0],'typography_font_size'=>['unit'=>'px','size'=>$v[1]],'typography_font_size_tablet'=>['unit'=>'px','size'=>$v[2]],'typography_font_size_mobile'=>['unit'=>'px','size'=>$v[3]],'typography_font_weight'=>(string)$v[4],'typography_line_height'=>['unit'=>'em','size'=>$v[5]]];
}
$settings['hello_header_menu']=3;if(!$kit->save(['settings'=>$settings]))throw new RuntimeException('Kit save failed.');$locations=get_nav_menu_locations();$locations['menu-1']=3;set_theme_mod('nav_menu_locations',$locations);
$dim=fn($t,$r=null,$b=null,$l=null)=>['unit'=>'px','top'=>(string)$t,'right'=>(string)($r??$t),'bottom'=>(string)($b??$t),'left'=>(string)($l??$r??$t),'isLinked'=>false];
$container=function($id,$elements,$extra=[])use($dim){return ['id'=>$id,'elType'=>'container','isInner'=>false,'settings'=>array_merge(['content_width'=>'full','flex_direction'=>'column','flex_gap'=>['unit'=>'px','size'=>12],'padding'=>$dim(0)],$extra),'elements'=>$elements];};
$heading=function($id,$title,$tag,$font,$extra=[]){return ['id'=>$id,'elType'=>'widget','widgetType'=>'heading','isInner'=>false,'settings'=>array_merge(['title'=>$title,'header_size'=>$tag,'__globals__'=>['title_color'=>'globals/colors?id=white','typography_typography'=>"globals/typography?id=rail_$font"]],$extra),'elements'=>[]];};
$button=function($id,$text,$url,$plain=false)use($dim){return ['id'=>$id,'elType'=>'widget','widgetType'=>'button','isInner'=>false,'elements'=>[],'settings'=>['text'=>$text,'link'=>['url'=>$url],'align'=>'left','border_radius'=>$dim($plain?0:28),'text_padding'=>$plain?$dim(12,0):$dim(15,24),'__globals__'=>['button_text_color'=>'globals/colors?id='.($plain?'white':'secondary'),'background_color'=>'globals/colors?id='.($plain?'hero_video_top':'white'),'typography_typography'=>'globals/typography?id=interior_button']]];};
$oldHero=null;foreach($data as $n)if($n['id']==='hero_container')$oldHero=$n;
if(!$oldHero)throw new RuntimeException('Hero missing.');
$services=[];
foreach([['canto','Cantonese','粵語','11:00'],['english','English','英語','11:00'],['mandarin','Mandarin','國語','9:00'],['children','Children','兒童','10:00']] as $i=>$v){
 [$key,$english,$chinese,$time]=$v;
 $services[]=$container("rail_service_$key",[
  $container("rail_labels_$key",[$heading("rail_name_$key",$english,'h3','label'),$heading("rail_zh_$key",$chinese,'div','zh')],['flex_direction'=>'row','flex_wrap'=>'wrap','flex_align_items'=>'baseline','flex_gap'=>['unit'=>'px','size'=>8]]),
  $container("rail_time_$key",[$heading("rail_digits_$key",$time,'div','time'),$heading("rail_am_$key",'AM','div','small')],['flex_direction'=>'row','flex_wrap'=>'nowrap','flex_align_items'=>'baseline','flex_gap'=>['unit'=>'px','size'=>4]])
 ],['width'=>['unit'=>'%','size'=>22],'width_mobile'=>['unit'=>'%','size'=>47],'flex_gap'=>['unit'=>'px','size'=>8],'border_border'=>'solid','border_width'=>$dim(0,0,0,1),'padding'=>$dim(0,0,0,20),'padding_mobile'=>$dim(0,0,0,$i%2===0?0:16),'border_width_mobile'=>$dim(0,0,0,$i%2===0?0:1),'__globals__'=>['border_color'=>'globals/colors?id=hero_rail_line']]);
}
$rail=$container('rail_services',[
 $container('rail_sunday_title',[$heading('rail_every_sunday','Sunday mornings','h2','label'),$heading('rail_every_sunday_zh','主日聚會','div','zh')],['width'=>['unit'=>'%','size'=>12],'width_tablet'=>['unit'=>'%','size'=>100],'width_mobile'=>['unit'=>'%','size'=>100],'flex_gap'=>['unit'=>'px','size'=>4],'flex_direction_tablet'=>'row','flex_gap_tablet'=>['unit'=>'px','size'=>12]]),
 $container('rail_three_services',$services,['width'=>['unit'=>'%','size'=>84],'width_tablet'=>['unit'=>'%','size'=>100],'width_mobile'=>['unit'=>'%','size'=>100],'flex_direction'=>'row','flex_wrap'=>'nowrap','flex_wrap_mobile'=>'wrap','flex_justify_content'=>'space-between','flex_gap'=>['unit'=>'px','size'=>16],'flex_gap_mobile'=>['unit'=>'px','size'=>18]])
],['flex_direction'=>'row','flex_direction_tablet'=>'column','flex_align_items'=>'center','flex_align_items_tablet'=>'stretch','flex_gap'=>['unit'=>'px','size'=>20],'flex_gap_mobile'=>['unit'=>'px','size'=>14],'_element_id'=>'sunday-times']);
$location=$container('rail_location',[
 $heading('rail_address','302 W. Commonwealth Ave., Alhambra, CA 91801','div','small'),
 $button('rail_directions','Get directions · 交通指引 ↗','https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801',true)
],['flex_direction'=>'row','flex_direction_mobile'=>'column','flex_align_items'=>'center','flex_align_items_mobile'=>'flex-start','flex_gap'=>['unit'=>'px','size'=>24],'flex_gap_mobile'=>['unit'=>'px','size'=>0],'padding'=>$dim(10,64,0,0),'padding_mobile'=>$dim(10,60,0,0),'border_border'=>'solid','border_width'=>$dim(1,0,0,0),'__globals__'=>['border_color'=>'globals/colors?id=hero_rail_line']]);
$main=$container('rail_message_area',[
 $heading('hero_heading','Monterey Park<br>Chinese Baptist Church','h1','title'),
 $heading('rail_church_name_zh','慕道園華人浸信會','div','church_zh'),
 $heading('rail_intro','Know God. Grow together.','div','intro'),
 $button('hero_link_plan','Join us this Sunday ↗','/visit/')
],['min_height'=>['unit'=>'px','size'=>400],'min_height_tablet'=>['unit'=>'px','size'=>400],'min_height_mobile'=>['unit'=>'px','size'=>260],'flex_justify_content'=>'flex-end','flex_align_items'=>'flex-start','flex_gap'=>['unit'=>'px','size'=>16],'flex_gap_mobile'=>['unit'=>'px','size'=>12]]);
$heroSettings=array_merge($oldHero['settings'],[
 'content_width'=>'boxed','boxed_width'=>['unit'=>'px','size'=>1328],
 'min_height'=>['unit'=>'custom','size'=>'max(740px, 100svh)'],'min_height_tablet'=>['unit'=>'custom','size'=>'max(820px, 100svh)'],'min_height_mobile'=>['unit'=>'custom','size'=>'max(740px, 100svh)'],
 'padding'=>$dim(128,56,24),'padding_tablet'=>$dim(112,32,24),'padding_mobile'=>$dim(112,20,20),
 'flex_justify_content'=>'flex-end','flex_align_items'=>'stretch','flex_gap'=>['unit'=>'px','size'=>24],'flex_gap_mobile'=>['unit'=>'px','size'=>20],
 'background_overlay_color_stop'=>['unit'=>'%','size'=>0],'background_overlay_color_b_stop'=>['unit'=>'%','size'=>100],
 'background_overlay_gradient_angle'=>['unit'=>'deg','size'=>180],'background_overlay_opacity'=>['unit'=>'px','size'=>1],
 '__globals__'=>['background_overlay_color'=>'globals/colors?id=hero_video_top','background_overlay_color_b'=>'globals/colors?id=hero_video_bottom'],
 '_element_id'=>'top'
]);
unset($heroSettings['background_overlay_color'],$heroSettings['background_overlay_color_b']);
$hero=['id'=>'hero_container','elType'=>'container','isInner'=>false,'settings'=>$heroSettings,'elements'=>[$main,$rail,$location]];
$after=[];foreach($data as $n){if($n['id']==='hero_container')$after[]=$hero;elseif($n['id']!=='times_outer_section')$after[]=$n;}
// Only the former hero/times card are replaced; all subsequent sections are unchanged.
$doc=Elementor\Plugin::$instance->documents->get(21);if(!$doc->save(['elements'=>$after]))throw new RuntimeException('Home save failed.');
$readback=json_decode(get_post_meta(21,'_elementor_data',true),true);$expected=array_values(array_filter($data,fn($n)=>!in_array($n['id'],['hero_container','times_outer_section'])));$actual=array_values(array_filter($readback,fn($n)=>$n['id']!=='hero_container'));if($expected!==$actual)throw new RuntimeException('Other Home sections unexpectedly changed.');
$hero=$readback[0];$rail=$hero['elements'][1];$location=$hero['elements'][2];
$exp=json_decode(file_get_contents("$root/elementor/home-content-filled.json"),true);$exp['content']=$readback;file_put_contents("$root/elementor/home-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
foreach(['home-hero.json'=>[$hero],'home-service-rail.json'=>[$rail,$location],'home-times-card.json'=>[$rail,$location]] as $name=>$content){file_put_contents("$root/elementor/$name",wp_json_encode(['version'=>'0.4','title'=>'MPCBC – Integrated service rail','type'=>'container','page_settings'=>[],'content'=>$content],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
Elementor\Plugin::$instance->files_manager->clear_cache();echo 'Bottom rail saved locally; remaining Home sections unchanged.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
