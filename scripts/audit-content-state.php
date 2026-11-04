<?php
// Read-only inventory; writes reports only into design A.
$out = 'C:/Projects/mpcbc/content-source';
$report = ['url'=>home_url(), 'front_page'=>(int)get_option('page_on_front'), 'elementor_version'=>ELEMENTOR_VERSION,
 'atomic_active'=>Elementor\Plugin::$instance->experiments->is_feature_active('e_atomic_elements'),
 'mcp_exposed'=>Elementor\Modules\Mcp\Module::is_site_mcp_exposure_enabled(), 'pages'=>[]];
foreach(get_posts(['post_type'=>['page','elementor_library'],'post_status'=>['publish','draft'],'numberposts'=>-1]) as $post){
 $raw=get_post_meta($post->ID,'_elementor_data',true); $data=json_decode($raw,true) ?: [];
 $widgets=[];$copy=[];
 $walk=function($nodes)use(&$walk,&$widgets,&$copy){foreach($nodes as $n){$type=$n['widgetType']??$n['elType']??'unknown';$widgets[$type]=($widgets[$type]??0)+1;
 foreach(['title','editor','text','description','html','shortcode'] as $key)if(!empty($n['settings'][$key])&&is_string($n['settings'][$key]))$copy[]=['id'=>$n['id'],'type'=>$type,'key'=>$key,'text'=>wp_strip_all_tags($n['settings'][$key])];
 $walk($n['elements']??[]);}};$walk($data);
 $report['pages'][]=['id'=>$post->ID,'title'=>$post->post_title,'slug'=>$post->post_name,'status'=>$post->post_status,'widgets'=>$widgets,'copy'=>$copy];
 file_put_contents("$out/local-elementor-{$post->ID}.json",wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
file_put_contents("$out/local-state.json",wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo wp_json_encode(['pages'=>count($report['pages']),'atomic_active'=>$report['atomic_active'],'mcp_exposed'=>$report['mcp_exposed']]);
