<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$report=['wordpress'=>get_bloginfo('version'),'elementor'=>ELEMENTOR_VERSION,'theme'=>get_stylesheet(),'php'=>PHP_VERSION,'menu_locations'=>get_nav_menu_locations(),'pages'=>[]];
$gate='Elementor\Modules\Mcp\Utils\Mcp_V4_Gate';$report['atomic_editor_active']=class_exists($gate)?$gate::is_atomic_editor_active():null;
$mcp='Elementor\MCP\Composer\Admin\McpSettingsController';$report['mcp_enabled']=class_exists($mcp)?$mcp::is_enabled():null;
$helper='Elementor\Modules\Mcp\Abilities\Utils\Widget_Context_Helper';$report['v3_mcp_allowlist']=class_exists($helper)?$helper::V3_ALLOWLIST:[];
foreach([21,64,215,218,65,233,236,16] as $id){
 $raw=get_post_meta($id,'_elementor_data',true);$data=json_decode($raw,true);$counts=[];$legacy=0;$atomic=0;$broken_media=[];$visit_links=[];$placeholder_count=0;
 $walk=function($ns)use(&$walk,&$counts,&$legacy,&$atomic,&$broken_media,&$visit_links,&$placeholder_count){foreach($ns as $n){$type=$n['widgetType']??$n['elType'];$counts[$type]=($counts[$type]??0)+1;if(str_starts_with($type,'e-'))$atomic++;else $legacy++;
  $s=$n['settings']??[];if(str_contains($s['link']['url']??'','/visit/'))$visit_links[]=$n['id'];
  foreach(['title','editor','text'] as $key)if(isset($s[$key]))$placeholder_count+=preg_match_all('/\[[^\]]+\]/u',wp_strip_all_tags($s[$key]));
  foreach(['image','background_image','background_video_fallback'] as $key){$url=$s[$key]['url']??'';if($url&&str_contains($url,home_url('/wp-content/uploads/'))){$upload=wp_upload_dir();$file=str_replace($upload['baseurl'],$upload['basedir'],$url);if(!file_exists($file))$broken_media[]=$url;}}
  $walk($n['elements']??[]);
 }};$walk($data??[]);
 $report['pages'][]=['id'=>$id,'title'=>get_the_title($id),'legacy_nodes'=>$legacy,'atomic_nodes'=>$atomic,'types'=>$counts,'missing_media'=>$broken_media,'visit_placeholder_links'=>$visit_links,'bracketed_text_count'=>$placeholder_count];
}
$report['redirects']=[];foreach([181,224,227] as $id)$report['redirects'][]=['id'=>$id,'target'=>(int)get_post_meta($id,'_mpcbc_redirect_page',true),'anchor'=>get_post_meta($id,'_mpcbc_redirect_anchor',true)];
$report['theme_files']=array_map('basename',glob(get_stylesheet_directory().'/*'));
$report['theme_bytes']=[];foreach(['style.css','functions.php','footer.php','nav.js','video-toggle.js','language-navigation.js'] as $file)$report['theme_bytes'][$file]=filesize(get_stylesheet_directory().'/'.$file);
file_put_contents('C:/Projects/mpcbc/content-source/migration-mcp-audit.json',wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));echo wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
