<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-about-redesign";wp_mkdir_p($backup);
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$settings=get_post_meta($kit->get_id(),'_elementor_page_settings',true);
if(!file_exists("$backup/kit-settings.json"))file_put_contents("$backup/kit-settings.json",wp_json_encode($settings,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$fonts=$settings['custom_typography']??[];
foreach(['en'=>'Geist','tc'=>'Noto Sans TC','sc'=>'Noto Sans SC'] as $script=>$family){
 foreach(['hero'=>[64,52,40,700,1.12],'section'=>[44,36,30,700,1.2],'mission'=>[34,30,26,600,1.35],'card'=>[26,24,22,600,1.35],'body'=>[18,18,18,400,1.8],'intro'=>[21,20,19,400,1.6],'label'=>[14,14,14,600,1.6],'nav'=>[16,16,16,600,1.5],'year'=>[32,30,26,700,1.2]] as $type=>$v){
  $fid="about_{$script}_$type";$fonts=array_values(array_filter($fonts,fn($f)=>$f['_id']!==$fid));
  $fonts[]=['_id'=>$fid,'title'=>"About / $script / $type",'typography_typography'=>'custom','typography_font_family'=>$script==='en'&&in_array($type,['hero','section','mission','year'])?'Bricolage Grotesque':$family,'typography_font_size'=>['unit'=>'px','size'=>$v[0]],'typography_font_size_tablet'=>['unit'=>'px','size'=>$v[1]],'typography_font_size_mobile'=>['unit'=>'px','size'=>$v[2]],'typography_font_weight'=>(string)$v[3],'typography_line_height'=>['unit'=>'em','size'=>$v[4]]];
 }
}
$settings['custom_typography']=$fonts;$settings['hello_header_menu']=3;$kit->save(['settings'=>$settings]);$locations=get_nav_menu_locations();$locations['menu-1']=3;set_theme_mod('nav_menu_locations',$locations);
require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
$photo=get_option('mpcbc_about_church_photo_id');
if(!$photo){$tmp=wp_tempnam('about-church.jpg');copy("$root/assets/about-church.jpg",$tmp);$photo=media_handle_sideload(['name'=>'about-church.jpg','tmp_name'=>$tmp],64,'Monterey Park Chinese Baptist Church building');if(is_wp_error($photo))throw new RuntimeException($photo->get_error_message());update_option('mpcbc_about_church_photo_id',$photo);update_post_meta($photo,'_wp_attachment_image_alt','Monterey Park Chinese Baptist Church building');}
$portrait=get_option('mpcbc_caleb_portrait_id');if(!$portrait)throw new RuntimeException('Existing Caleb portrait missing.');
foreach(['en'=>64,'zh-hant'=>215,'zh-hans'=>218] as $locale=>$pid){
 if(!file_exists("$backup/$pid.json"))file_put_contents("$backup/$pid.json",get_post_meta($pid,'_elementor_data',true));
 $file="$root/elementor/$locale-about.json";$exp=json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
 $walk=function(&$nodes)use(&$walk,$photo,$portrait){foreach($nodes as &$n){if(($n['widgetType']??'')==='image'){$target=$n['settings']['image']['url'];if($target==='__ABOUT_CHURCH__'||$target==='__CALEB_PORTRAIT__'){$id=$target==='__ABOUT_CHURCH__'?$photo:$portrait;$n['settings']['image']=['url'=>wp_get_attachment_url($id),'id'=>$id];}}if(!empty($n['elements']))$walk($n['elements']);}};$walk($exp['content']);
 $doc=Elementor\Plugin::$instance->documents->get($pid);if(!$doc->save(['elements'=>$exp['content'],'settings'=>$exp['page_settings']]))throw new RuntimeException("Save failed $pid");
 file_put_contents($file,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
 if($locale==='en')file_put_contents("$root/elementor/about-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
 echo "$locale About saved ($pid).\n";
}
Elementor\Plugin::$instance->files_manager->clear_cache();
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
