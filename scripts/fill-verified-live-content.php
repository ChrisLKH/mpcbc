<?php
// Run with WP-CLI eval-file. Changes only the verified text and links on design A.
if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
$admins=get_users(['role'=>'administrator','number'=>1]);
if(!$admins)throw new RuntimeException('No local administrator.');
wp_set_current_user($admins[0]->ID);
$root='C:/Projects/mpcbc';
$backup="$root/content-source/before-content-fill";
if (!is_dir($backup)) mkdir($backup,0777,true);
$maps='https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801';
$pdf='https://mpcbc.net/wp-content/uploads/2026/07/MPCBC-Jul-2026-Newsletter-FINAL-V3.pdf';
$en='https://mpcbc.net/english/';
$updates=[
 'hero_subtext_en'=>['editor'=>'A church family in Alhambra, with services in English, Cantonese and Mandarin.'],
 'times_header_addr'=>['title'=>'302 W. Commonwealth Ave., Alhambra'],
 'times_canto_time'=>['title'=>'11:00'], 'times_mandarin_time'=>['title'=>'9:00'],
 'times_canto_pill'=>['editor'=>'<a href="https://mpcbc.net/">Watch worship</a>'],
 'times_en_note'=>['title'=>'Fellowship Hall'], 'times_mandarin_note'=>['title'=>'Sanctuary'],
 'times_kids_note'=>['title'=>'Children’s worship'],
 'times_link_directions'=>['link'=>['url'=>$maps]],
 'watch_lede'=>['title'=>'Worship in Cantonese, English or Mandarin. Explore messages from each congregation.'],
 'watch_canto_kick'=>['title'=>'Cantonese · 11:00 AM'],
 'watch_mand_kick'=>['title'=>'Mandarin · 9:00 AM'],
 'watch_canto_desc'=>['title'=>'Sunday worship in Cantonese in the Sanctuary.'],
 'watch_en_desc'=>['title'=>'Connect Ministry meets in the Fellowship Hall for English worship.'],
 'watch_mand_desc'=>['title'=>'Sunday worship in Mandarin in the Sanctuary.'],
 'watch_canto_btn_watch'=>['text'=>'Watch worship','link'=>['url'=>'https://mpcbc.net/']],
 'watch_canto_btn_arch'=>['link'=>['url'=>'https://mpcbc.net/category/cantonese-sermons/']],
 'watch_en_btn_remind'=>['text'=>'Watch messages','link'=>['url'=>$en.'connect-ministry-sermons/']],
 'watch_mand_btn_remind'=>['text'=>'Watch worship','link'=>['url'=>'https://mpcbc.net/']],
 'watch_mand_btn_arch'=>['link'=>['url'=>'https://mpcbc.net/category/mandarin-sermons/']],
 'watch_en_btn_arch'=>['link'=>['url'=>$en.'connect-ministry-sermons/']],
 'watch_foot_text'=>['title'=>'Explore the church’s sermon archives by congregation.'],
 'watch_foot_yt'=>['text'=>'English YouTube','link'=>['url'=>'https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ']],
 'news_lede'=>['title'=>'Stories, reflections and ministry updates from our church family.'],
 'news_issue_kicker'=>['title'=>'Newsletter · July 2026'],
 'news_main_title'=>['title'=>'Faith, testimony and mission'],
 'news_main_desc'=>['title'=>'The July issue shares personal testimonies, reflections on trusting God and mission experiences in Mongolia and Hong Kong.'],
 'news_main_desc_zh'=>['title'=>'2026年7月會刊：信仰見證、信靠神與宣教分享。'],
 'news_btn_read'=>['link'=>['url'=>$en.'newsletters__trashed/newsletter-july-2026/']],
 'news_btn_pdf'=>['text'=>'Open PDF','link'=>['url'=>$pdf]],
 'news_side_h3'=>['title'=>'Explore more'], 'news_side_h3_zh'=>['title'=>'更多內容'],
 'news_side_t_1'=>['title'=>'January 2026 newsletter','link'=>['url'=>$en.'newsletters__trashed/newsletter-january-2026/']],
 'news_side_d_1'=>['title'=>'Devotionals, church reflections and mission updates from Paris.'],
 'news_side_t_2'=>['title'=>'Read the Bible in a year','link'=>['url'=>$en.'bible-in-a-year/']],
 'news_side_d_2'=>['title'=>'Learn about the Reformation Reading Plan and YouVersion Bible App.'],
 'news_side_t_3'=>['title'=>'Fellowship and Bible study','link'=>['url'=>$en.'fellowship-bible-study-and-prayer/']],
 'news_side_d_3'=>['title'=>'Explore opportunities to study, pray and grow together.'],
 'pastors_lede'=>['title'=>'Meet our church’s pastoral team. [Current congregation assignments to be confirmed.]'],
 'pastor_name_0'=>['title'=>'Rev. Caleb Cheung, Ph.D.'],
 'pastor_name_zh_0'=>['title'=>'張健庭牧師'],
 'pastor_bio_0'=>['title'=>'Senior pastor; studied at Fuller Theological Seminary and Claremont Graduate University.'],
 'pastor_name_1'=>['title'=>'Rev. Edward Lu'],
 'pastor_bio_1'=>['title'=>'Lead pastor of Connect Ministry, the church’s English-language ministry.'],
 'comm_panel_desc_1'=>['title'=>'Children’s worship is held on Sundays at 11:00 AM.'],
 'comm_panel_desc_2'=>['title'=>'Youth Sunday School meets Sundays, 10:00–10:45 AM, in the education annex.'],
 'comm_btn_2_0'=>['text'=>'Youth Sunday School','link'=>['url'=>$en.'fellowship-bible-study-and-prayer/']],
 'comm_btn_2_1'=>['text'=>'College / Career Fellowship','link'=>['url'=>$en.'fellowship-bible-study-and-prayer/']],
 'comm_panel_desc_3'=>['title'=>'Study the Bible, pray and grow together. College / Career Fellowship meets Mondays, 6:30–9:00 PM, in West Covina.'],
 'comm_btn_3_1'=>['link'=>['url'=>$en.'fellowship-bible-study-and-prayer/']],
 'tile_youth_detail'=>['title'=>'Sunday school · Sundays 10:00–10:45 AM'],
 'give_verse'=>['title'=>'Make checks payable to MPCBC and mail to the church address.'],
 'give_verse_zh'=>['title'=>'奉獻支票抬頭請寫 MPCBC，郵寄至教會地址。'],
 'give_btn_online'=>['text'=>'Ways to give','link'=>['url'=>'https://mpcbc.net/donation/']],
 'give_link_other'=>['title'=>'Giving information','link'=>['url'=>'https://mpcbc.net/donation/']],
 'mpcbc_foot_address_zh'=>['title'=>'慕道園華人浸信會'],
 'mpcbc_foot_address_street'=>['title'=>'302 W. Commonwealth Ave., Alhambra, CA 91801'],
 'mpcbc_foot_directions'=>['link'=>['url'=>$maps]],
 'mpcbc_foot_sundays_0'=>['title'=>'Cantonese 粵語 11:00 AM'],
 'mpcbc_foot_sundays_2'=>['title'=>'Mandarin 國語 9:00 AM'],
 'mpcbc_foot_contact_0'=>['title'=>'(626) 289-1284','link'=>['url'=>'tel:+16262891284']],
 'mpcbc_foot_contact_1'=>['title'=>'info@mpcbc.net','link'=>['url'=>'mailto:info@mpcbc.net']],
 'mpcbc_foot_contact_3'=>['title'=>'Instagram @mpcbconnect','link'=>['url'=>'https://www.instagram.com/mpcbconnect/']],
 'mpcbc_foot_contact_4'=>['title'=>'English YouTube','link'=>['url'=>'https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ']],
 'mpcbc_foot_since'=>['title'=>'Founded in 1977 · Alhambra, California'],
];
$seen=[];
$walk=function(&$nodes)use(&$walk,$updates,&$seen){foreach($nodes as &$n){$id=$n['id']??'';if(isset($updates[$id])){$n['settings']=array_replace($n['settings']??[],$updates[$id]);$seen[$id]=true;}if(!empty($n['elements']))$walk($n['elements']);}};
foreach([21,16] as $id){
 $raw=get_post_meta($id,'_elementor_data',true);$data=json_decode($raw,true,512,JSON_THROW_ON_ERROR);
 if(!file_exists("$backup/elementor-$id.json"))file_put_contents("$backup/elementor-$id.json",$raw);
 $walk($data);
 wp_save_post_revision($id);
 $doc=Elementor\Plugin::$instance->documents->get($id);if(!$doc->save(['elements'=>$data]))throw new RuntimeException("Elementor save failed: $id");
 if(json_decode(get_post_meta($id,'_elementor_data',true),true)!=$data)throw new RuntimeException("Elementor data mismatch: $id");
 file_put_contents("$root/elementor/".($id===21?'home-content-filled':'footer-content-filled').'.json',wp_json_encode(['version'=>'0.4','title'=>get_the_title($id),'type'=>$id===21?'page':'section','content'=>$data,'page_settings'=>get_post_meta($id,'_elementor_page_settings',true)],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
$missing=array_diff(array_keys($updates),array_keys($seen));if($missing)throw new RuntimeException('Unmatched IDs: '.implode(',',$missing));
global $wpdb;
$table=$wpdb->prefix.'fluentform_forms';
$form=$wpdb->get_row("SELECT id,form_fields FROM $table WHERE id=5");
// Locate the connected form from its distinctive options instead of assuming its ID.
foreach($wpdb->get_results("SELECT id,form_fields FROM $table") as $row){
 if(strpos($row->form_fields,'Cantonese')===false||strpos($row->form_fields,'9:30')===false)continue;
 if(!file_exists("$backup/form-{$row->id}.json"))file_put_contents("$backup/form-{$row->id}.json",$row->form_fields);
 $fields=str_replace(['9:30','11:30'],['11:00','9:00'],$row->form_fields);
 json_decode($fields,true,512,JSON_THROW_ON_ERROR);
 $wpdb->update($table,['form_fields'=>$fields],['id'=>$row->id]);
 file_put_contents("$root/fluent-forms/connect-content-filled-fields.json",$fields);
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo wp_json_encode(['updated_widgets'=>count($seen),'pages'=>[21,16],'missing'=>$missing]);
