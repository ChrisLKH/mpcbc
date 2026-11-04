<?php
// Read-only export of published sermons for mockups. No writes.
$q = new WP_Query(['post_type'=>'sermon','post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'date','order'=>'DESC','no_found_rows'=>true]);
$out = [];
$terms = function($id,$tax){ $t = get_the_terms($id,$tax); if(!$t||is_wp_error($t)) return []; return array_map(fn($x)=>['slug'=>$x->slug,'name'=>$x->name,'meta'=>get_term_meta($x->term_id)], $t); };
foreach ($q->posts as $p) {
  $out[] = ['id'=>$p->ID,'title'=>$p->post_title,'date'=>get_the_date('Y-m-d',$p),'time'=>get_the_date('H:i',$p),
    'video'=>get_post_meta($p->ID,'_mpcbc_video_id',true),'scripture'=>get_post_meta($p->ID,'_mpcbc_scripture',true),
    'raw'=>get_post_meta($p->ID,'_mpcbc_raw_title',true),
    'ministry'=>array_map(fn($x)=>$x['slug'],$terms($p->ID,'ministry')),
    'speaker'=>array_map(fn($x)=>$x['name'],$terms($p->ID,'speaker')),
    'series'=>array_map(fn($x)=>$x['name'],$terms($p->ID,'series'))];
}
$tax = [];
foreach (['speaker','series','ministry'] as $t) { foreach (get_terms(['taxonomy'=>$t,'hide_empty'=>false]) as $x) $tax[$t][] = ['slug'=>$x->slug,'name'=>$x->name,'count'=>$x->count,'meta'=>array_map(fn($v)=>$v[0],get_term_meta($x->term_id))]; }
file_put_contents(getenv('OUT'), json_encode(['sermons'=>$out,'tax'=>$tax], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo count($out)." sermons\n";
