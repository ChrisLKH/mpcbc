<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';$var_ability=new Elementor\Modules\Mcp\Abilities\Global_Variables_Resource_Ability();$current=$var_ability->execute();
if(!file_exists("$root/content-source/before-atomic-conversion/variables.json"))file_put_contents("$root/content-source/before-atomic-conversion/variables.json",$current);
$kit=Elementor\Plugin::$instance->kits_manager->get_active_kit();$service=new Elementor\Modules\Variables\Services\Variables_Service(new Elementor\Modules\Variables\Storage\Variables_Repository($kit),new Elementor\Modules\Variables\Services\Batch_Operations\Batch_Processor());$variables=$service->get_variables_list();file_put_contents("$root/content-source/atomic-current-variables.json",wp_json_encode($variables,JSON_PRETTY_PRINT));$labels=array_column((array)$variables,'label');$wanted=json_decode(file_get_contents("$root/content-source/atomic-variable-input.json"),true);$ops=array_values(array_filter($wanted,fn($v)=>!in_array($v['label'],$labels,true)));
$ops=array_values(array_filter($ops,fn($v)=>$v['type']!=='global-size-variable'));
$manage=new Elementor\Modules\Mcp\Abilities\Manage_Variable_Ability();foreach(array_chunk($ops,50) as $chunk){$r=$manage->execute(['operations'=>$chunk]);if(is_wp_error($r))throw new RuntimeException($r->get_error_message());if(($r['status']??'')!=='ok')throw new RuntimeException(wp_json_encode($r));file_put_contents("$root/content-source/atomic-variable-result.json",wp_json_encode($r,JSON_PRETTY_PRINT));echo 'Created variable batch: '.($r['status']??'unknown')."\n";}
$pilotFile="$root/content-source/atomic-pilot.json";$pilot=file_exists($pilotFile)?json_decode(file_get_contents($pilotFile),true):[];
$sourceId=$pilot['source']??65;$input=json_decode(file_get_contents("$root/content-source/atomic-input/$sourceId.json"),true);$source=json_decode(file_get_contents("$root/content-source/before-atomic-conversion/$sourceId.json"),true);
$pid=$pilot['id']??wp_insert_post(['post_type'=>'page','post_status'=>'draft','post_title'=>'Atomic migration preview — Give']);if(is_wp_error($pid))throw new RuntimeException($pid->get_error_message());
update_post_meta($pid,'_elementor_edit_mode','builder');update_post_meta($pid,'_wp_page_template','elementor_header_footer');
wp_update_post(['ID'=>$pid,'post_status'=>'publish','post_name'=>'atomic-conversion-preview']);
$doc=Elementor\Plugin::$instance->documents->get($pid);$compiler=Elementor\Modules\Mcp\Abilities\Utils\Composition_Compiler::make();$compiled=$compiler->compile($input,$doc);
if(is_wp_error($compiled))throw new RuntimeException($compiled->get_error_code().': '.$compiled->get_error_message());
require_once "$root/scripts/atomic-helpers.php";mpcbc_atomic_states($compiled['elements']);mpcbc_atomic_embeds($compiled['elements'],$source['data']);
$mutator=Elementor\Core\Utils\Document\Document_Mutator::instance();$tree=[];foreach($compiled['elements'] as $element){$tree=$mutator->insert_subtree($tree,'document',null,$element);if(is_wp_error($tree))throw new RuntimeException($tree->get_error_message());}
if(!$doc->save(['elements'=>$tree,'settings'=>$source['settings']]))throw new RuntimeException('Draft save failed.');
file_put_contents($pilotFile,wp_json_encode(['id'=>$pid,'source'=>$sourceId,'url'=>get_permalink($pid),'warnings'=>$compiled['warnings']],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
wp_mkdir_p("$root/content-source/atomic-compiled");file_put_contents("$root/content-source/atomic-compiled/$sourceId.json",get_post_meta($pid,'_elementor_data',true));Elementor\Plugin::$instance->files_manager->clear_cache();echo 'Pilot '.$pid.' saved. Warnings: '.wp_json_encode($compiled['warnings']);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
