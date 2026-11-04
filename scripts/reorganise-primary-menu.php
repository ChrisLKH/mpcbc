<?php
try {
if(untrailingslashit(home_url())!=='http://mpcbc.local')throw new RuntimeException('Design A only.');
$root='C:/Projects/mpcbc';$backup="$root/content-source/before-menu-grouping";wp_mkdir_p($backup);
$items=wp_get_nav_menu_items(3);if(!file_exists("$backup/menu.json"))file_put_contents("$backup/menu.json",wp_json_encode($items,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$maps='https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801';
$spec=[
 [110,'Sundays','主日','/#sunday-times',0],
 [111,'Service times','聚會時間','/#sunday-times',110],
 [112,'Sunday worship','主日崇拜','/#watch',110],
 [0,'Directions','交通指南',$maps,110],
 [116,'Sermons','講道','/#sermons',0],
 [113,'Cantonese sermons','粵語講道','https://mpcbc.net/category/cantonese-sermons/',116],
 [114,'English sermons','英語講道','https://mpcbc.net/english/connect-ministry-sermons/',116],
 [115,'Mandarin sermons','國語講道','https://mpcbc.net/category/mandarin-sermons/',116],
 [118,'Church life','教會生活','/#community',0],
 [119,'Kids and youth','兒童及青少年','/#kids',118],
 [120,'Fellowships and groups','團契小組','/#community',118],
 [122,'Care and prayer','關懷代禱','/#connect',118],
 [121,'Bible reading plan','讀經計劃','https://mpcbc.net/english/bible-in-a-year/',118],
 [117,'Newsletter','通訊','/#news',118],
 [123,'About','關於我們','/about/',0],
 [124,'Our story','教會歷史','/about/#story',123],
 [127,'Our mission','使命宣言','/about/#mission',123],
 [126,'Our pastoral team','教牧團隊','/about/#pastors',123],
 [125,'What we believe','信仰告白','/about/#beliefs',123],
 [128,'Give','奉獻','/give/',0],
];
$report=[];$order=0;
foreach($spec as $entry){[$id,$title,$description,$url,$parent]=$entry;
 if(!$id)foreach($items as $item)if($item->title===$title&&(int)$item->menu_item_parent===$parent){$id=$item->ID;break;}
 if(str_starts_with($url,'/'))$url=home_url($url);
 $saved=wp_update_nav_menu_item(3,$id,['menu-item-title'=>$title,'menu-item-description'=>$description,'menu-item-url'=>$url,'menu-item-parent-id'=>$parent,'menu-item-position'=>++$order,'menu-item-status'=>'publish','menu-item-type'=>'custom']);
 if(is_wp_error($saved))throw new RuntimeException($saved->get_error_message());
 $report[]=['id'=>$saved,'title'=>$title,'description'=>$description,'url'=>$url,'parent'=>$parent,'order'=>$order];
}
// Retain the existing single language menu and its three locale children.
foreach($items as $item){if((int)$item->ID!==129&&(int)$item->menu_item_parent!==129)continue;wp_update_post(['ID'=>$item->ID,'menu_order'=>++$order]);}
file_put_contents("$root/content-source/primary-menu-grouped.json",wp_json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
echo 'Grouped Sundays, Sermons, Church life, About, Give and the existing language switcher. All destinations are existing content.';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
