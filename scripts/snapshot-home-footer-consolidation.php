<?php
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$root='C:/Projects/mpcbc/content-source/before-home-footer-consolidation';
wp_mkdir_p($root);
foreach([21,16,511,513] as $id){
 if(file_exists("$root/$id.json"))throw new RuntimeException('Snapshot already exists; do not overwrite.');
 $record=['id'=>$id,'title'=>get_the_title($id),'status'=>get_post_status($id),'data'=>json_decode(get_post_meta($id,'_elementor_data',true),true),'settings'=>get_post_meta($id,'_elementor_page_settings',true)?:[],'template'=>get_post_meta($id,'_wp_page_template',true),'locale'=>get_post_meta($id,'_mpcbc_locale',true),'group'=>get_post_meta($id,'_mpcbc_translation_group',true)];
 file_put_contents("$root/$id.json",wp_json_encode($record,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
echo 'Saved four current WordPress documents before consolidation.';
