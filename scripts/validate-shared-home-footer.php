<?php
try{
 if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
 $root='C:/Projects/mpcbc';$copy=json_decode(file_get_contents("$root/content-source/shared-language-copy-map.json"),true);$trees=[];$results=[];
 $plain=fn($v)=>html_entity_decode(wp_strip_all_tags($v),ENT_QUOTES|ENT_HTML5,'UTF-8');
 foreach([21,16] as $id){
  $tree=json_decode(get_post_meta($id,'_elementor_data',true),true);$nodes=[];$links=[];$forms=0;
  $index=function($ns)use(&$index,&$nodes,&$links,&$forms){foreach($ns as $n){$nodes[$n['id']]=$n;if(isset($n['settings']['link']))$links[]=wp_json_encode($n['settings']['link']);if(($n['widgetType']??'')==='shortcode')$forms++;$index($n['elements']??[]);}};$index($tree);
  foreach($copy as $pair){if($pair['document']!==$id)continue;foreach(['traditional','simplified'] as $language){$node=$nodes[$pair[$language.'_id']]??null;$value=$node['settings'][$pair['field']]['value']??null;if(!is_string($value)||$plain($value)!==$plain($pair[$language]))throw new RuntimeException('Chinese copy not preserved.');}}
  $source=json_decode(file_get_contents("$root/content-source/before-home-footer-consolidation/$id.json"),true);$originalLinks=[];$collect=function($ns)use(&$collect,&$originalLinks){foreach($ns as $n){if(isset($n['settings']['link']))$originalLinks[]=wp_json_encode($n['settings']['link']);$collect($n['elements']??[]);}};$collect($source['data']);sort($links);sort($originalLinks);if($links!==$originalLinks)throw new RuntimeException('Shared link count/value changed.');
  $results[]=['id'=>$id,'copy_pairs'=>count(array_filter($copy,fn($p)=>$p['document']===$id)),'both_wordings_preserved'=>true,'shared_links_preserved_once'=>true,'form_embeds'=>$forms];
 }
 if(get_post_meta(21,'_wp_page_template',true)!=='elementor_header_footer')throw new RuntimeException('Home template changed.');
 if((int)get_post_meta(511,'_mpcbc_redirect_page',true)!==21||get_post_status(513)!=='draft')throw new RuntimeException('Legacy archive state wrong.');
 file_put_contents("$root/content-source/shared-home-footer-validation.json",wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
 echo wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
