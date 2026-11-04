<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-beliefs-reader";wp_mkdir_p($backup);
$menus=wp_get_nav_menu_items(3);
if(!file_exists("$backup/menu.json"))file_put_contents("$backup/menu.json",wp_json_encode($menus,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$words=function($ns)use(&$words){$result=[];foreach($ns as $n){foreach(['title','editor'] as $key)if(isset($n['settings'][$key]))$result[]=$n['settings'][$key];$result=array_merge($result,$words($n['elements']??[]));}return $result;};
$find=function($ns)use(&$find){foreach($ns as $n){if(($n['widgetType']??'')==='nested-tabs')return $n;$v=$find($n['elements']??[]);if($v)return $v;}return null;};
$report=[];
foreach(['en'=>181,'zh-hant'=>224,'zh-hans'=>227] as $locale=>$pid){
 if(!file_exists("$backup/$pid.json"))file_put_contents("$backup/$pid.json",get_post_meta($pid,'_elementor_data',true));
 $path="$root/elementor/$locale-beliefs.json";$exp=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);
 $core=json_decode(file_get_contents("$root/content-source/beliefs-core-$locale.json"),true);$reader=$find($exp['content']);
 if(count($reader['elements'])!==8)throw new RuntimeException('Eight native panels required');
 foreach($core['elements'] as $i=>$panel){if($words($panel['elements'])!==$words($reader['elements'][$i]['elements'][2]['elements']))throw new RuntimeException("Doctrine mismatch $locale $i");}
 $doc=Elementor\Plugin::$instance->documents->get($pid);if(!$doc->save(['elements'=>$exp['content'],'settings'=>$exp['page_settings']]))throw new RuntimeException("Save failed $pid");
 $exp['content']=json_decode(get_post_meta($pid,'_elementor_data',true),true);
 $saved=$find($exp['content']);foreach($core['elements'] as $i=>$panel){if($words($panel['elements'])!==$words($saved['elements'][$i]['elements'][2]['elements']))throw new RuntimeException("Readback mismatch $locale $i");}
 file_put_contents($path,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 if($locale==='en')file_put_contents("$root/elementor/beliefs-content-filled.json",wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 $report[]=['id'=>$pid,'locale'=>$locale,'native_panels'=>8,'doctrine_and_references_preserved'=>true];echo "Saved $locale reader.\n";
}
// Reuse the former Campus map slot for Mission; directions already live under Sundays.
$changes=[123=>['About','關於我們','/about/',0,14],124=>['Our story','教會歷史','/about/#story',123,15],127=>['Our mission','使命宣言','/about/#mission',123,16],126=>['Our pastoral team','教牧團隊','/about/#pastors',123,17],125=>['What we believe','信仰告白','/beliefs/',123,18]];
foreach($changes as $id=>$v){$result=wp_update_nav_menu_item(3,$id,['menu-item-title'=>$v[0],'menu-item-description'=>$v[1],'menu-item-url'=>home_url($v[2]),'menu-item-parent-id'=>$v[3],'menu-item-position'=>$v[4],'menu-item-status'=>'publish','menu-item-type'=>'custom']);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());}
Elementor\Plugin::$instance->files_manager->clear_cache();
file_put_contents("$root/content-source/beliefs-reader-validation.json",wp_json_encode(['pages'=>$report,'about_menu'=>array_values($changes)],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo 'About menu and all three Topic readers saved.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
