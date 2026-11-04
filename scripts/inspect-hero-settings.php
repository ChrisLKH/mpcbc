<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);
$hero = $data[0];
echo "hero_root settings:\n";
print_r($hero['settings']);
echo "hero_actions_wrap settings:\n";
print_r($hero['elements'][3]['settings']);
echo "hero_btn_group settings:\n";
print_r($hero['elements'][3]['elements'][0]['settings']);

