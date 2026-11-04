<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-migration-links";wp_mkdir_p($backup);$report=[];
$walk=function(&$value,&$count)use(&$walk){foreach($value as $key=>&$child){if(is_array($child))$walk($child,$count);elseif($key==='url'&&is_string($child)&&preg_match('~^/(?!/)~',$child)){$child=home_url($child);$count++;}}};
foreach(get_posts(['post_type'=>['page','elementor_library'],'post_status'=>'publish','numberposts'=>-1]) as $p){
 $raw=get_post_meta($p->ID,'_elementor_data',true);$data=json_decode($raw,true);if(!is_array($data))continue;$count=0;$walk($data,$count);if(!$count)continue;
 if(!file_exists("$backup/{$p->ID}.json"))file_put_contents("$backup/{$p->ID}.json",$raw);
 $admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
 $doc=Elementor\Plugin::$instance->documents->get($p->ID);if(!$doc->save(['elements'=>$data,'settings'=>get_post_meta($p->ID,'_elementor_page_settings',true)]))throw new RuntimeException('Save failed');
 $report[]=['id'=>$p->ID,'title'=>$p->post_title,'site_relative_links_normalised'=>$count];
}
// Refresh existing document exports and Home component handoffs from the saved data.
$map=json_decode(file_get_contents("$root/content-source/language-build-result.json"),true);
foreach($map as $p){$file="$root/elementor/{$p['locale']}-{$p['topic']}.json";if(!file_exists($file))continue;$exp=json_decode(file_get_contents($file),true);$exp['content']=json_decode(get_post_meta($p['id'],'_elementor_data',true),true);file_put_contents($file,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
foreach([21=>'home-content-filled.json',16=>'footer-content-filled.json'] as $id=>$filename){$path="$root/elementor/$filename";$exp=json_decode(file_get_contents($path),true);$exp['content']=json_decode(get_post_meta($id,'_elementor_data',true),true);file_put_contents($path,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));if($id===21){$exp['content']=[$exp['content'][0]];file_put_contents("$root/elementor/home-hero.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}}
Elementor\Plugin::$instance->files_manager->clear_cache();file_put_contents("$root/content-source/migration-links-validation.json",wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
