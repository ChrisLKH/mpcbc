<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-about-beliefs-merge";wp_mkdir_p($backup);
$menu=wp_get_nav_menu_items(3);if(!file_exists("$backup/menu.json"))file_put_contents("$backup/menu.json",wp_json_encode($menu,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$routes=['/beliefs/'=>'/about/','/zh-hant/beliefs/'=>'/zh-hant/about/','/zh-hans/beliefs/'=>'/zh-hans/about/'];
$rewrite=function(&$ns)use(&$rewrite,$routes){foreach($ns as &$n){if(isset($n['settings']['link']['url'])){$url=$n['settings']['link']['url'];$path=wp_parse_url($url,PHP_URL_PATH);$host=wp_parse_url($url,PHP_URL_HOST);if((!$host||$host===wp_parse_url(home_url(),PHP_URL_HOST))&&isset($routes[trailingslashit($path??'')]))$n['settings']['link']['url']=home_url($routes[trailingslashit($path)]).'#beliefs';}if(!empty($n['elements']))$rewrite($n['elements']);}};
$find=function($ns)use(&$find){foreach($ns as $n){if(($n['widgetType']??'')==='nested-tabs'&&str_contains($n['settings']['_css_classes']??'','mpcbc-topic-reader'))return $n;$r=$find($n['elements']??[]);if($r)return $r;}return null;};
$words=function($ns)use(&$words){$result=[];foreach($ns as $n){foreach(['title','editor'] as $key)if(isset($n['settings'][$key]))$result[]=$n['settings'][$key];$result=array_merge($result,$words($n['elements']??[]));}return $result;};
$report=[];
foreach(['en'=>[64,181],'zh-hant'=>[215,224],'zh-hans'=>[218,227]] as $locale=>$ids){
 [$pid,$old_id]=$ids;
 foreach($ids as $id)if(!file_exists("$backup/$id.json"))file_put_contents("$backup/$id.json",get_post_meta($id,'_elementor_data',true));
 $path="$root/elementor/$locale-about.json";$exp=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);$rewrite($exp['content']);
 $doc=Elementor\Plugin::$instance->documents->get($pid);if(!$doc->save(['elements'=>$exp['content'],'settings'=>$exp['page_settings']]))throw new RuntimeException("Save failed $pid");
 $exp['content']=json_decode(get_post_meta($pid,'_elementor_data',true),true);
 $reader=$find($exp['content']);$core=json_decode(file_get_contents("$root/content-source/beliefs-core-$locale.json"),true);
 if(count($reader['elements']??[])!==8)throw new RuntimeException('Missing eight native panels');
 foreach($core['elements'] as $i=>$panel)if($words($panel['elements'])!==$words($reader['elements'][$i]['elements'][2]['elements']))throw new RuntimeException("Doctrine changed $locale $i");
 file_put_contents($path,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 if($locale==='en')file_put_contents("$root/elementor/about-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 update_post_meta($old_id,'_mpcbc_redirect_page',$pid);update_post_meta($old_id,'_mpcbc_redirect_anchor','beliefs');
 $report[]=['locale'=>$locale,'about'=>$pid,'legacy_beliefs'=>$old_id,'statements_preserved'=>8];echo "Merged $locale About.\n";
}
// Repair references in other native page documents, keeping every other field intact.
foreach(get_posts(['post_type'=>['page','elementor_library'],'post_status'=>'publish','numberposts'=>-1]) as $page){
 if(in_array($page->ID,[64,215,218,181,224,227],true))continue;
 $raw=get_post_meta($page->ID,'_elementor_data',true);if(!$raw)continue;$data=json_decode($raw,true);if(!is_array($data))continue;$original=$data;$rewrite($data);if($data===$original)continue;
 if(!file_exists("$backup/{$page->ID}.json"))file_put_contents("$backup/{$page->ID}.json",$raw);
 $doc=Elementor\Plugin::$instance->documents->get($page->ID);if(!$doc->save(['elements'=>$data,'settings'=>get_post_meta($page->ID,'_elementor_page_settings',true)]))throw new RuntimeException('Link update failed');
}
foreach($menu as $item){$path=trailingslashit(wp_parse_url($item->url,PHP_URL_PATH)??'');if(!isset($routes[$path]))continue;update_post_meta($item->ID,'_menu_item_url',home_url($routes[$path]).'#beliefs');}
// Keep checked-in handoff exports in sync with any repaired links.
$manifest=json_decode(file_get_contents("$root/content-source/language-build-result.json"),true);
foreach($manifest as $entry){$file="$root/elementor/{$entry['locale']}-{$entry['topic']}.json";if(!file_exists($file))continue;$export=json_decode(file_get_contents($file),true);$export['content']=json_decode(get_post_meta($entry['id'],'_elementor_data',true),true);file_put_contents($file,wp_json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
Elementor\Plugin::$instance->files_manager->clear_cache();
file_put_contents("$root/content-source/about-beliefs-merge-validation.json",wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo 'Merged all statements; old page URLs redirect to About#beliefs.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
