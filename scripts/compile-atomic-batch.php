<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($admins[0]->ID);$root='C:/Projects/mpcbc';$report=[];
require_once "$root/scripts/atomic-helpers.php";
foreach(glob("$root/content-source/atomic-input/*.json") as $file){$id=(int)basename($file,'.json');$input=json_decode(file_get_contents($file),true);$doc=Elementor\Plugin::$instance->documents->get($id);$compiled=Elementor\Modules\Mcp\Abilities\Utils\Composition_Compiler::make()->compile($input,$doc);
if(is_wp_error($compiled)){$report[$id]=['error'=>$compiled->get_error_code(),'message'=>$compiled->get_error_message()];echo "FAILED $id: ".$compiled->get_error_message()."\n";continue;}
mpcbc_atomic_states($compiled['elements']);
$source=json_decode(file_get_contents("$root/content-source/before-atomic-conversion/$id.json"),true);mpcbc_atomic_embeds($compiled['elements'],$source['data']);
file_put_contents("$root/content-source/atomic-compiled/$id.json",wp_json_encode($compiled['elements'],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));$report[$id]=['root_elements'=>count($compiled['elements']),'warnings'=>$compiled['warnings']];echo "Compiled $id, warnings ".count($compiled['warnings'])."\n";
}
file_put_contents("$root/content-source/atomic-compile-report.json",wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
