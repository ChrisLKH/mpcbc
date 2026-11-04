<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-identity-and-beliefs";wp_mkdir_p($backup);
foreach([21,64,215,218,181,224,227] as $pid)if(!file_exists("$backup/$pid.json"))file_put_contents("$backup/$pid.json",get_post_meta($pid,'_elementor_data',true));
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();if(!file_exists("$backup/kit.json"))file_put_contents("$backup/kit.json",wp_json_encode(get_post_meta($kit->get_id(),'_elementor_page_settings',true),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$find=function($ns)use(&$find){foreach($ns as $n){if(($n['widgetType']??'')==='nested-accordion')return $n;$r=$find($n['elements']??[]);if($r)return $r;}return null;};
$words=function($ns)use(&$words){$result=[];foreach($ns as $n){foreach(['title','editor'] as $key)if(isset($n['settings'][$key]))$result[]=$n['settings'][$key];$result=array_merge($result,$words($n['elements']??[]));}return $result;};
$results=[];
foreach(['en'=>['about'=>64,'beliefs'=>181],'zh-hant'=>['about'=>215,'beliefs'=>224],'zh-hans'=>['about'=>218,'beliefs'=>227]] as $locale=>$topics){foreach($topics as $topic=>$pid){
 $path="$root/elementor/$locale-$topic.json";$exp=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);$old=json_decode(get_post_meta($pid,'_elementor_data',true),true);
 if($topic==='beliefs'){$a=$find($old);$b=$find($exp['content']);if($words($a['elements'])!==$words($b['elements'])||$a['settings']['items']!==$b['settings']['items'])throw new RuntimeException("Belief text changed: $locale");}
 $doc=Elementor\Plugin::$instance->documents->get($pid);if(!$doc->save(['elements'=>$exp['content'],'settings'=>$exp['page_settings']]))throw new RuntimeException("Save failed $pid");
 $exp['content']=json_decode(get_post_meta($pid,'_elementor_data',true),true);file_put_contents($path,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 if($locale==='en')file_put_contents("$root/elementor/$topic-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 $results[]=['id'=>$pid,'topic'=>$topic,'locale'=>$locale,'belief_text_preserved'=>$topic==='beliefs'?true:null];
}}
Elementor\Plugin::$instance->files_manager->clear_cache();file_put_contents("$root/content-source/beliefs-connected-validation.json",wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));echo 'Saved six connected About/Beliefs views; all belief statements and references preserved.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
