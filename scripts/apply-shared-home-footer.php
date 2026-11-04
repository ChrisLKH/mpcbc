<?php
try {
 if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
 $a=get_users(['role'=>'administrator','number'=>1]);wp_set_current_user($a[0]->ID);
 $root='C:/Projects/mpcbc';$result=[];
 if(file_exists("$root/content-source/shared-home-footer-complete.json"))throw new RuntimeException('Consolidation already complete. Do not overwrite editor changes.');
 foreach([[21,'home'],[16,'footer']] as [$id,$name]){
  $source=json_decode(file_get_contents("$root/content-source/before-home-footer-consolidation/$id.json"),true);
  $elements=json_decode(file_get_contents("$root/content-source/shared-$name-compiled.json"),true,512,JSON_THROW_ON_ERROR);
  $live=json_decode(get_post_meta($id,'_elementor_data',true),true);
  $prior=$id===21&&file_exists("$root/content-source/shared-home-readback.json")?json_decode(file_get_contents("$root/content-source/shared-home-readback.json"),true):null;
  $previousExport=json_decode(file_get_contents("$root/elementor/$name-content-filled.json"),true)['content']??null;
  if($live!=$elements&&$live!==$source['data']&&$live!==$prior&&$live!==$previousExport)throw new RuntimeException('Live document changed since snapshot. Do not overwrite editor changes.');
  $ids=[];$atomic=0;$forms=0;
  $validate=function($nodes)use(&$validate,&$ids,&$atomic,&$forms){foreach($nodes as $node){if(isset($ids[$node['id']]))throw new RuntimeException('Duplicate native ID');$ids[$node['id']]=true;$type=$node['widgetType']??$node['elType'];if(str_starts_with($type,'e-'))$atomic++;elseif($type==='shortcode')$forms++;else throw new RuntimeException('Unexpected classic type');$validate($node['elements']??[]);}};
  $validate($elements);
  $document=Elementor\Plugin::$instance->documents->get($id);
  if(!$document->save(['elements'=>$elements,'settings'=>$source['settings']]))throw new RuntimeException('Native save failed.');
  if($source['template'])update_post_meta($id,'_wp_page_template',$source['template']);
  $actual=json_decode(get_post_meta($id,'_elementor_data',true),true);
  if($actual!=$elements)throw new RuntimeException('Native readback differs; review before retiring old pages.');
  $export=json_decode(file_get_contents("$root/elementor/$name-content-filled.json"),true);$export['content']=$actual;
  file_put_contents("$root/elementor/$name-content-filled.json",wp_json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
  file_put_contents("$root/content-source/atomic-compiled/$id.json",wp_json_encode($actual,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
  $read=(new Elementor\Modules\Mcp\Abilities\Get_Structure_Ability())->execute(['post_id'=>$id]);if(is_wp_error($read))throw new RuntimeException($read->get_error_message());
  $result[]=['id'=>$id,'document'=>$name,'atomic_elements'=>$atomic,'form_embeds'=>$forms,'mcp_readable'=>true];
 }
 // Preserve the old native documents as recoverable archives. Old Home links
 // continue to redirect to the shared page with the appropriate reading preference.
 wp_update_post(['ID'=>511,'post_title'=>'Legacy redirect — Simplified Home']);
 delete_post_meta(511,'_mpcbc_translation_group');
 update_post_meta(511,'_mpcbc_redirect_page',21);update_post_meta(511,'_mpcbc_redirect_locale','zh-hans');
 wp_update_post(['ID'=>513,'post_status'=>'draft','post_title'=>'Archived — Simplified footer before consolidation']);
 delete_post_meta(513,'_mpcbc_translation_group');
 file_put_contents("$root/content-source/shared-home-footer-result.json",wp_json_encode(['shared'=>$result,'home_redirect_archive'=>511,'footer_draft_archive'=>513,'runtime_content_translation'=>false],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 Elementor\Plugin::$instance->files_manager->clear_cache();
 echo wp_json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
