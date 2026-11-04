<?php
try {
    if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
    $admins = get_users(['role'=>'administrator','number'=>1]); wp_set_current_user($admins[0]->ID);
    $root = 'C:/Projects/mpcbc';
    $parent = get_page_by_path('zh-hans');
    if (!$parent) throw new RuntimeException('Simplified parent missing.');
    if (get_page_by_path('zh-hans/home')) throw new RuntimeException('Independent Home already exists; do not overwrite editor changes.');
    $backup = "$root/content-source/before-independent-home"; wp_mkdir_p($backup);
    file_put_contents("$backup/home-metadata.json",wp_json_encode(['group'=>get_post_meta(21,'_mpcbc_translation_group',true),'locale'=>get_post_meta(21,'_mpcbc_locale',true)]));
    $results = [];
    foreach ([['home',21,'page',$parent->ID],['footer',16,'elementor_library',0]] as [$topic,$source,$type,$parent_id]) {
        $export = json_decode(file_get_contents("$root/elementor/zh-hans-$topic.json"),true,512,JSON_THROW_ON_ERROR);
        $id = wp_insert_post(['post_type'=>$type,'post_status'=>'publish','post_title'=>$export['title'],'post_name'=>$topic==='home'?'home':'mpcbc-footer-zh-hans','post_parent'=>$parent_id],true);
        if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
        update_post_meta($id,'_elementor_edit_mode','builder');
        update_post_meta($id,'_elementor_template_type',$topic==='home'?'wp-page':'section');
        update_post_meta($id,'_mpcbc_locale','zh-hans');
        update_post_meta($id,'_mpcbc_translation_group',$topic==='home'?'home':'shared-footer');
        $elements = $export['content'];
        // Fresh native element IDs, preserving stable CSS anchors and native style data.
        $fresh = function($nodes)use(&$fresh){foreach($nodes as &$node){$node['id']=Elementor\Utils::generate_random_string();$node['elements']=$fresh($node['elements']??[]);}return $nodes;};
        $elements = $fresh($elements);
        $settings = get_post_meta($source,'_elementor_page_settings',true) ?: [];
        $document = Elementor\Plugin::$instance->documents->get($id);
        if (!$document->save(['elements'=>$elements,'settings'=>$settings])) throw new RuntimeException('Save failed.');
        if ($topic==='home') update_post_meta($id,'_wp_page_template','elementor_header_footer');
        $actual = json_decode(get_post_meta($id,'_elementor_data',true),true);
        $export['content']=$actual;
        file_put_contents("$root/elementor/zh-hans-$topic.json",wp_json_encode($export,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        $structure=(new Elementor\Modules\Mcp\Abilities\Get_Structure_Ability())->execute(['post_id'=>$id]);
        if(is_wp_error($structure))throw new RuntimeException($structure->get_error_message());
        $results[]=['topic'=>$topic,'id'=>$id,'url'=>get_permalink($id),'mcp_readable'=>true,'editable_independently'=>true];
    }
    update_post_meta(21,'_mpcbc_translation_group','home');update_post_meta(21,'_mpcbc_locale','en');
    file_put_contents("$root/content-source/independent-home-result.json",wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    Elementor\Plugin::$instance->files_manager->clear_cache();flush_rewrite_rules(false);
    echo wp_json_encode($results,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
} catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
