<?php
try {
 if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
 $root='C:/Projects/mpcbc';
 $audit=json_decode(file_get_contents("$root/content-source/migration-mcp-audit.json"),true);
 $variants=json_decode(file_get_contents("$root/content-source/independent-home-result.json"),true);
 $ids=array_unique(array_merge(array_column($audit['pages'],'id'),array_column($variants,'id')));
 $pages=[];
 foreach($ids as $id){
  $atomic=0;$classic=0;$walk=function($nodes)use(&$walk,&$atomic,&$classic){foreach($nodes as $n){$type=$n['widgetType']??$n['elType'];if(str_starts_with($type,'e-'))$atomic++;elseif($type==='shortcode')$classic++;else throw new RuntimeException('Unexpected non-Atomic type '.$type);$walk($n['elements']??[]);}};
  $walk(json_decode(get_post_meta($id,'_elementor_data',true),true));
  $pages[]=['id'=>$id,'title'=>get_the_title($id),'atomic_elements'=>$atomic,'fluent_form_embeds'=>$classic];
 }
 $audit['pages']=$pages;$audit['converted_documents']=count($pages);$audit['atomic_elements']=array_sum(array_column($pages,'atomic_elements'));$audit['classic_form_placements']=array_sum(array_column($pages,'fluent_form_embeds'));
 $audit['independent_home_variants']=$variants;$audit['menu_script_conversion_only']=true;$audit['footer_spacing_mcp_operations']=19;
 file_put_contents("$root/content-source/migration-mcp-audit.json",wp_json_encode($audit,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 echo wp_json_encode(array_diff_key($audit,['pages'=>true]),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
