<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-hero-visit-button";wp_mkdir_p($backup);
$data=json_decode(get_post_meta(21,'_elementor_data',true),true,512,JSON_THROW_ON_ERROR);
if(!file_exists("$backup/home.json"))file_put_contents("$backup/home.json",wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$dim=fn($t,$r,$b,$l)=>['unit'=>'px','top'=>(string)$t,'right'=>(string)$r,'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false];
$walk=function(&$ns)use(&$walk,$dim){foreach($ns as &$n){
 if($n['id']==='times_outer_section')$n['settings']['_element_id']='sunday-times';
 if(in_array($n['id'],['times_col_cantonese','times_col_english','times_col_mandarin','times_col_kids'],true)){
  $n['settings']['width_mobile']=['unit'=>'%','size'=>47];
  $n['settings']['padding_mobile']=$dim(8,8,8,8);
 }
 if($n['id']==='hero_actions_wrap'){
  $n['settings']['flex_direction_mobile']='column';$n['settings']['flex_align_items_mobile']='flex-start';$n['settings']['width']=['unit'=>'%','size'=>100];$n['settings']['padding_mobile']=$dim(0,60,0,0);$n['settings']['flex_gap']=['unit'=>'px','size'=>16];$n['settings']['flex_gap_mobile']=['unit'=>'px','size'=>4];
  $n['elements']=[
   ['id'=>'hero_link_plan','elType'=>'widget','widgetType'=>'button','isInner'=>false,'elements'=>[],'settings'=>['text'=>'Join us this Sunday →','link'=>['url'=>'#sunday-times'],'align'=>'left','text_padding'=>$dim(14,24,14,24),'border_radius'=>$dim(28,28,28,28),'__globals__'=>['button_text_color'=>'globals/colors?id=secondary','background_color'=>'globals/colors?id=white','typography_typography'=>'globals/typography?id=interior_button']]],
   ['id'=>'hero_link_plan_zh','elType'=>'widget','widgetType'=>'heading','isInner'=>false,'elements'=>[],'settings'=>['title'=>'本週主日歡迎你','header_size'=>'div','link'=>['url'=>'#sunday-times'],'__globals__'=>['title_color'=>'globals/colors?id=white','typography_typography'=>'globals/typography?id=interior_zh'],'_padding'=>$dim(4,0,4,0)]]
  ];
 }
 if(!empty($n['elements']))$walk($n['elements']);
}};$walk($data);
$doc=Elementor\Plugin::$instance->documents->get(21);if(!$doc->save(['elements'=>$data]))throw new RuntimeException('Home save failed.');
$exp=json_decode(file_get_contents("$root/elementor/home-content-filled.json"),true);$exp['content']=$data;file_put_contents("$root/elementor/home-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
foreach(['home-hero.json'=>'hero_container','home-times-card.json'=>'times_outer_section'] as $name=>$id){$p="$root/elementor/$name";$part=json_decode(file_get_contents($p),true);foreach($data as $n)if($n['id']===$id)$part['content']=[$n];file_put_contents($p,wp_json_encode($part,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
Elementor\Plugin::$instance->files_manager->clear_cache();echo 'Updated local Home visit button and service-card anchor.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
