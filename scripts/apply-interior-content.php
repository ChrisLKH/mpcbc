<?php
try {
if (untrailingslashit(home_url())!=='http://mpcbc.local') throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-interior-build";
if(!is_dir($backup))mkdir($backup,0777,true);
function keep_backup($name,$value){$backup='C:/Projects/mpcbc/content-source/before-interior-build';if(!file_exists("$backup/$name.json"))file_put_contents("$backup/$name.json",wp_json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$settings=$kit->get_settings();keep_backup('kit-settings',$settings);
$fonts=$settings['custom_typography']??[];
$definitions=[
 ['interior_h1','Interior / page title','Bricolage Grotesque',72,48,40,'700',1.08],
 ['interior_h2','Interior / section title','Bricolage Grotesque',48,40,32,'700',1.15],
 ['interior_card','Interior / card title','Geist',26,24,22,'600',1.35],
 ['interior_subtitle','Interior / Chinese heading','Noto Sans TC',24,22,20,'500',1.5],
 ['interior_body','Interior / English body','Geist',18,18,18,'400',1.8],
 ['interior_zh','Interior / Chinese body','Noto Sans TC',18,18,18,'400',1.9],
 ['interior_refs','Interior / small heading','Geist',16,16,16,'600',1.5],
 ['interior_button','Interior / button','Geist',16,16,16,'600',1.4],
];
foreach($definitions as [$id,$name,$family,$size,$tablet,$mobile,$weight,$line]){
 $fonts=array_values(array_filter($fonts,fn($f)=>$f['_id']!==$id));
 $fonts[]=['_id'=>$id,'title'=>$name,'typography_typography'=>'custom','typography_font_family'=>$family,'typography_font_size'=>['unit'=>'px','size'=>$size],'typography_font_size_tablet'=>['unit'=>'px','size'=>$tablet],'typography_font_size_mobile'=>['unit'=>'px','size'=>$mobile],'typography_font_weight'=>$weight,'typography_line_height'=>['unit'=>'em','size'=>$line]];
}
$schema=json_decode(file_get_contents("$root/content-source/interior-schema.json"),true,512,JSON_THROW_ON_ERROR);
$preserved=$schema['kit'];
$preserved['custom_typography']=$fonts;
$preserved['hello_header_menu']=wp_get_nav_menu_object('primary-menu')->term_id;
$kit->save(['settings'=>$preserved]);
require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
$portraitId=(int)get_option('mpcbc_caleb_portrait_id');
if(!$portraitId||!get_post($portraitId)){
 $tmp=wp_tempnam('pastor-caleb.jpg');copy("$root/assets/pastor-caleb.jpg",$tmp);
 $portraitId=media_handle_sideload(['name'=>'pastor-caleb.jpg','tmp_name'=>$tmp],0,'Rev. Caleb Cheung / 張健庭牧師');
 if(is_wp_error($portraitId))throw new RuntimeException($portraitId->get_error_message());
 update_post_meta($portraitId,'_wp_attachment_image_alt','Rev. Caleb Cheung / 張健庭牧師');update_option('mpcbc_caleb_portrait_id',$portraitId);
}
$portraitUrl=wp_get_attachment_url($portraitId);$pages=[];
foreach(['about','beliefs','give','zh'] as $slug){
 $export=json_decode(file_get_contents("$root/elementor/$slug-content-filled.json"),true,512,JSON_THROW_ON_ERROR);
 $old=get_page_by_path($slug);if($old)keep_backup("page-{$old->ID}",['post'=>$old,'meta'=>get_post_meta($old->ID)]);
 $args=['post_type'=>'page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$export['title']];if($old)$args['ID']=$old->ID;
 $postId=wp_insert_post($args,true);if(is_wp_error($postId))throw new RuntimeException($postId->get_error_message());
 update_post_meta($postId,'_elementor_edit_mode','builder');update_post_meta($postId,'_elementor_template_type','wp-page');update_post_meta($postId,'_wp_page_template','elementor_header_footer');
 $walk=function(&$nodes)use(&$walk,$portraitId,$portraitUrl){foreach($nodes as &$n){if(($n['settings']['image']['url']??'')==='__CALEB_PORTRAIT__')$n['settings']['image']=['url'=>$portraitUrl,'id'=>$portraitId];if(!empty($n['elements']))$walk($n['elements']);}};$walk($export['content']);
 $doc=Elementor\Plugin::$instance->documents->get($postId);if(!$doc->save(['elements'=>$export['content'],'settings'=>$export['page_settings']]))throw new RuntimeException("Save failed $slug");
 $export['content']=json_decode(get_post_meta($postId,'_elementor_data',true),true);
 file_put_contents("$root/elementor/$slug-content-filled.json",wp_json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 $pages[$slug]=['id'=>$postId,'url'=>get_permalink($postId)];
}
// Root-qualified anchors work from interior pages as well as Home.
$menus=wp_get_nav_menu_items('primary-menu');keep_backup('menu',$menus);
$urls=['Sundays'=>'/#top','Service times and directions'=>'/#top','Sermons'=>'/#watch','Newsletter'=>'/#news','Community'=>'/#community','Kids and youth'=>'/#kids','Care and prayer'=>'/#connect','About'=>'/about/','Our story'=>'/about/#story','Beliefs'=>'/beliefs/','Pastors and staff'=>'/about/#pastors','Give'=>'/give/','中文'=>'/zh/'];
foreach($menus as $item)if(isset($urls[$item->title]))update_post_meta($item->ID,'_menu_item_url',$urls[$item->title]);
$paypal='https://www.paypal.com/donate/?hosted_button_id=PQJTK3MEFJ9M2';
foreach([21,16,17] as $id){
 $raw=get_post_meta($id,'_elementor_data',true);keep_backup("page-$id",['post'=>get_post($id),'meta'=>get_post_meta($id)]);$data=json_decode($raw,true)?:[];
 $walk=function(&$nodes)use(&$walk,$paypal){foreach($nodes as &$n){$id=$n['id'];
  if($id==='give_btn_online'){$n['settings']['text']='Give with PayPal · 透過 PayPal 奉獻';$n['settings']['link']['url']=$paypal;}
  if($id==='give_link_other'){$n['settings']['title']='Ways to give · 奉獻方式';$n['settings']['link']['url']='/give/';}
  if($id==='mpcbc_foot_zh')$n['settings']['link']['url']='/zh/';
  $footerLinks=['mpcbc_foot_explore_0'=>'/visit/','mpcbc_foot_explore_1'=>'/#watch','mpcbc_foot_explore_2'=>'/#watch','mpcbc_foot_explore_3'=>'/#news','mpcbc_foot_explore_4'=>'/#kids','mpcbc_foot_explore_5'=>'/#connect'];
  if(isset($footerLinks[$id]))$n['settings']['link']['url']=$footerLinks[$id];
  if($id==='pastor_name_0')$n['settings']['link']['url']='/about/#pastors';
  if($id==='pastors_lede')$n['settings']['title']='Knowing God is our foundation. Glorifying God is our goal.';
  if(($n['widgetType']??'')==='button'&&preg_match('/繁體|中文|简体/u',$n['settings']['text']??'')&&($n['settings']['link']['url']??'')==='/coming-soon/')$n['settings']['link']['url']='/zh/';
  if(!empty($n['elements']))$walk($n['elements']);
 }};$walk($data);
 if($id===21){
  foreach($data as &$node)if($node['id']==='pastors_root'){
   $node['elements']=array_values(array_filter($node['elements'],fn($e)=>!in_array($e['id'],['mpcbc_mission_zh','mpcbc_about_link'])));
   $node['elements'][]=['id'=>'mpcbc_mission_zh','elType'=>'widget','widgetType'=>'text-editor','settings'=>['editor'=>'<p lang="zh-Hant">認識神為基礎，榮耀神是目標。我們藉著敬拜﹑團契﹑門徒訓練﹑事奉﹑傳福音來認識和榮耀神。</p>','__globals__'=>['text_color'=>'globals/colors?id=text','typography_typography'=>'globals/typography?id=interior_zh']],'elements'=>[]];
   $node['elements'][]=['id'=>'mpcbc_about_link','elType'=>'widget','widgetType'=>'button','settings'=>['text'=>'Our story and team · 教會介紹及教牧團隊','link'=>['url'=>'/about/'],'border_radius'=>['unit'=>'px','top'=>12,'right'=>12,'bottom'=>12,'left'=>12,'isLinked'=>true],'__globals__'=>['background_color'=>'globals/colors?id=primary','button_text_color'=>'globals/colors?id=white','typography_typography'=>'globals/typography?id=interior_button']],'elements'=>[]];
  }unset($node);
 }
 $doc=Elementor\Plugin::$instance->documents->get($id);if(!$doc->save(['elements'=>$data]))throw new RuntimeException("Save failed $id");
 $file=$id===21?'home-content-filled':($id===16?'footer-content-filled':'welcome-content-filled');
 file_put_contents("$root/elementor/$file.json",wp_json_encode(['version'=>'0.4','title'=>get_the_title($id),'type'=>$id===16?'section':'page','content'=>$data,'page_settings'=>get_post_meta($id,'_elementor_page_settings',true)],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
Elementor\Plugin::$instance->files_manager->clear_cache();flush_rewrite_rules(false);
file_put_contents("$root/content-source/interior-build-result.json",wp_json_encode(['pages'=>$pages,'portrait'=>$portraitId,'fonts'=>array_column($definitions,0)],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo wp_json_encode($pages,JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) { echo get_class($e).': '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine(); }
