<?php
try{
 if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
 $root='C:/Projects/mpcbc';
 $home=json_decode(file_get_contents("$root/elementor/home-content-filled.json"),true);
 $find=function($ns,$id)use(&$find){foreach($ns as $n){if(($n['settings']['_cssid']['value']??'')===$id)return $n;$found=$find($n['elements']??[],$id);if($found)return $found;}return null;};
 foreach(['home-hero.json'=>'top','home-service-rail.json'=>'sunday-times','home-times-card.json'=>'sunday-times'] as $file=>$anchor){$export=$home;$export['content']=[$find($home['content'],$anchor)];if(!$export['content'][0])throw new RuntimeException('Export anchor missing.');file_put_contents("$root/elementor/$file",wp_json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));}
 $audit=json_decode(file_get_contents("$root/content-source/migration-mcp-audit.json"),true);$pages=[];
 foreach($audit['pages'] as $page){$id=$page['id'];if(in_array($id,[511,513],true))continue;$atomic=0;$forms=0;$walk=function($ns)use(&$walk,&$atomic,&$forms){foreach($ns as $n){$type=$n['widgetType']??$n['elType'];if(str_starts_with($type,'e-'))$atomic++;elseif($type==='shortcode')$forms++;else throw new RuntimeException('Unexpected classic element');$walk($n['elements']??[]);}};$walk(json_decode(get_post_meta($id,'_elementor_data',true),true));$pages[]=['id'=>$id,'title'=>get_the_title($id),'atomic_elements'=>$atomic,'fluent_form_embeds'=>$forms];}
 $audit['pages']=$pages;$audit['converted_documents']=count($pages);$audit['atomic_elements']=array_sum(array_column($pages,'atomic_elements'));$audit['classic_form_placements']=array_sum(array_column($pages,'fluent_form_embeds'));
 unset($audit['independent_home_variants']);$audit['shared_home']=21;$audit['shared_footer']=16;$audit['independent_chinese_pairs']=50;$audit['retired_native_copies']=[511=>'published redirect to shared Home',513=>'draft archive'];$audit['home_language_switch_without_reload']=true;
 file_put_contents("$root/content-source/migration-mcp-audit.json",wp_json_encode($audit,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
 file_put_contents("$root/content-source/shared-home-footer-complete.json",wp_json_encode(['home'=>21,'footer'=>16,'copy_pairs'=>50,'originals'=>"content-source/before-home-footer-consolidation/"],JSON_PRETTY_PRINT));
 echo wp_json_encode(array_diff_key($audit,['pages'=>true]),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
