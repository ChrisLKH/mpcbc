<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);$root='C:/Projects/mpcbc';$data=json_decode(get_post_meta(21,'_elementor_data',true),true);$ops=[];
$walk=function($ns)use(&$walk,&$ops){foreach($ns as $n){if(($n['settings']['_cssid']['value']??'')==='mpcbc-community_nested_tabs'){
 $ops[]=['action'=>'update','element_id'=>$n['id'],'style'=>'@media (--tablet){flex-direction:column;}'];
 foreach($n['elements'] as $menu)if(($menu['elType']??'')==='e-tabs-menu'){
  $ops[]=['action'=>'update','element_id'=>$menu['id'],'style'=>'@media (--tablet){width:100%;flex-direction:row;flex-wrap:wrap;}'];
  foreach($menu['elements'] as $tab){$ops[]=['action'=>'update','element_id'=>$tab['id'],'style'=>'@media (--tablet){width:auto;}'];foreach($tab['elements'] as $label)$ops[]=['action'=>'update','element_id'=>$label['id'],'style'=>'@media (--tablet){font-size:20px;}@media (--mobile){font-size:18px;}'];}
 }
 }$walk($n['elements']??[]);}};$walk($data);
$result=(new Elementor\Modules\Mcp\Abilities\Manage_Elements_Ability())->execute(['post_id'=>21,'operations'=>$ops]);if(is_wp_error($result)||($result['status']??'')!=='ok')throw new RuntimeException(wp_json_encode($result));
$document=Elementor\Plugin::$instance->documents->get(21);$draft=$document->get_autosave();if(!$draft)throw new RuntimeException('Native MCP preview not found');$reviewed=json_decode(get_post_meta($draft->get_id(),'_elementor_data',true),true);$source=json_decode(file_get_contents("$root/content-source/before-atomic-conversion/21.json"),true);if(!$document->save(['elements'=>$reviewed,'settings'=>$source['settings']]))throw new RuntimeException('Could not apply reviewed local style refinement');update_post_meta(21,'_wp_page_template','elementor_header_footer');
$actual=json_decode(get_post_meta(21,'_elementor_data',true),true);$path="$root/elementor/home-content-filled.json";$exp=json_decode(file_get_contents($path),true);$exp['content']=$actual;file_put_contents($path,wp_json_encode($exp,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));file_put_contents("$root/content-source/atomic-compiled/21.json",wp_json_encode($actual,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
Elementor\Plugin::$instance->files_manager->clear_cache();file_put_contents("$root/content-source/atomic-tab-refinement.json",wp_json_encode($result,JSON_PRETTY_PRINT));echo 'Native MCP ability refined '.count($ops).' tab/label styles; no content changed.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
