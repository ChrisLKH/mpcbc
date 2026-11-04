<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);$root='C:/Projects/mpcbc';$pilot=json_decode(file_get_contents("$root/content-source/atomic-pilot.json"),true);$id=$pilot['id'];$tree=json_decode(get_post_meta($id,'_elementor_data',true),true);
$find=function($ns)use(&$find){foreach($ns as $n){if(($n['widgetType']??'')==='e-heading')return $n;$r=$find($n['elements']??[]);if($r)return $r;}return null;};$heading=$find($tree);if(!$heading)throw new RuntimeException('No Atomic heading');
$ability=new Elementor\Modules\Mcp\Abilities\Manage_Elements_Ability();$result=$ability->execute(['post_id'=>$id,'operations'=>[['action'=>'update','element_id'=>$heading['id'],'settings'=>['title'=>$heading['settings']['title']['value']]]]]);
if(is_wp_error($result))throw new RuntimeException($result->get_error_message());file_put_contents("$root/content-source/atomic-mcp-edit-proof.json",wp_json_encode(['pilot_id'=>$id,'internal_ability_noop_update'=>$result,'network_connection'=>false],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));echo wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
