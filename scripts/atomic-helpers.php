<?php
/** Native Atomic style adjustments not exposed by the current MCP CSS parser. */
function mpcbc_atomic_states(array &$nodes): void {
 foreach($nodes as &$node){
  if(($node['elType']??'')==='e-tab')foreach($node['styles']??[] as $style_id=>$style)foreach($style['variants']??[] as $i=>$variant)if(($variant['meta']['state']??'')==='active')$node['styles'][$style_id]['variants'][$i]['meta']['state']='e--selected';
  if(!empty($node['elements']))mpcbc_atomic_states($node['elements']);
 }
}

/** Keep working Fluent Forms embeds; Elementor Free has no Atomic shortcode. */
function mpcbc_atomic_embeds(array &$nodes,array $source): void {
 $embeds=[];$collect=function($ns)use(&$collect,&$embeds){foreach($ns as $n){if(($n['widgetType']??'')==='shortcode')$embeds['mpcbc-embed-'.$n['id']]=$n;$collect($n['elements']??[]);}};$collect($source);
 $insert=function(&$ns)use(&$insert,$embeds){foreach($ns as &$n){$anchor=$n['settings']['_cssid']['value']??'';if(isset($embeds[$anchor]))$n['elements']=[$embeds[$anchor]];elseif(!empty($n['elements']))$insert($n['elements']);}};$insert($nodes);
}
