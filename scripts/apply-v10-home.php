<?php
// Applies content-source/v10-home.json (from build-v10-home.mjs).
// V10_TARGET=draft (default): the draft page "Home (v10 build)" (slug home-v10) for checking.
// V10_TARGET=home: replaces Home 21, only while it still matches content-source/before-v10/21.json (or was written here).
// Also sets the workbook's Home SEO title / description (custom fields read by mpcbc-translations) and image alt text.
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $a = get_users(['role' => 'administrator', 'number' => 1]); wp_set_current_user($a[0]->ID);
 $root = 'C:/Projects/mpcbc';
 $elements = json_decode(file_get_contents("$root/content-source/v10-home.json"), true, 512, JSON_THROW_ON_ERROR);
 $backup = json_decode(file_get_contents("$root/content-source/before-v10/21.json"), true);

 // alt text lives on the attachments (Atomic images read it from the media library); one language per image
 foreach ([545 => 'The church building at 302 W. Commonwealth Ave., Alhambra', 543 => 'Girls in VBS shirts waving at the camera',
  544 => 'Children sitting together during Vacation Bible School', 542 => 'Children at a craft table holding up paper flowers they made',
  546 => '', 547 => 'Rev. Caleb Cheung', 80 => 'Pastor photo (placeholder)', 1324 => 'Event image (placeholder)', 587 => 'Front page of the July 2026 newsletter'] as $id => $alt)
  update_post_meta($id, '_wp_attachment_image_alt', $alt);

 $target = getenv('V10_TARGET') ?: 'draft';
 if ($target === 'home') {
  $id = 21;
  $live = json_decode(get_post_meta($id, '_elementor_data', true), true);
  if ($live != $backup['meta']['_elementor_data'] && !get_post_meta($id, '_mpcbc_v10_home', true)) throw new RuntimeException('Home changed since the backup. Do not overwrite editor changes.');
 } else {
  $found = get_posts(['post_type' => 'page', 'post_status' => 'any', 'name' => 'home-v10', 'numberposts' => 1]);
  $id = $found ? $found[0]->ID : wp_insert_post(['post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Home (v10 build)', 'post_name' => 'home-v10']);
  update_post_meta($id, '_elementor_edit_mode', 'builder');
  update_post_meta($id, '_mpcbc_translation_group', 'home'); // body.home: transparent header over the hero (hello-mpcbc)
 }
 update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
 foreach (['mpcbc_seo_title' => 'Monterey Park Chinese Baptist Church | Alhambra, CA', 'mpcbc_seo_title_zh' => '慕道園華人浸信會 | 阿罕布拉華人教會',
  'mpcbc_seo_description' => 'A church family in Alhambra, CA, with Mandarin, Cantonese and English worship every Sunday. Everyone is welcome. Visit us or watch online.',
  'mpcbc_seo_description_zh' => '位於阿罕布拉的華人教會，每星期日有國語（普通話）及粵語中文崇拜，另設英語崇拜。歡迎您親臨或線上與我們一同敬拜。'] as $k => $v)
  update_post_meta($id, $k, $v);

 $document = Elementor\Plugin::$instance->documents->get($id);
 if (!$document->save(['elements' => $elements, 'settings' => $backup['meta']['_elementor_page_settings'] ?: []])) throw new RuntimeException('Native save failed.');
 if ($target === 'home') update_post_meta($id, '_mpcbc_v10_home', 1);

 // report anything Elementor dropped or rewrote on save (an invalid prop shows up here)
 $actual = json_decode(get_post_meta($id, '_elementor_data', true), true);
 $diffs = [];
 $cmp = function ($a, $b, $path) use (&$cmp, &$diffs) {
  if (is_array($a) && is_array($b)) { foreach (array_unique(array_merge(array_keys($a), array_keys($b))) as $k) $cmp($a[$k] ?? null, $b[$k] ?? null, "$path/$k"); }
  elseif ($a != $b && count($diffs) < 40) $diffs[] = $path . ': ' . substr(wp_json_encode($a, JSON_UNESCAPED_UNICODE), 0, 90) . ' => ' . substr(wp_json_encode($b, JSON_UNESCAPED_UNICODE), 0, 90);
 };
 $cmp($elements, $actual, '');
 // the shared footer (template 16), only while it still matches its backup (or was written by this script)
 if (getenv('V10_FOOTER')) {
  $fb = json_decode(file_get_contents("$root/content-source/before-v10/16.json"), true);
  $fl = json_decode(get_post_meta(16, '_elementor_data', true), true);
  if ($fl != $fb['meta']['_elementor_data'] && !get_post_meta(16, '_mpcbc_v10_footer', true)) throw new RuntimeException('Footer changed since the backup.');
  $footer = json_decode(file_get_contents("$root/content-source/v10-footer.json"), true, 512, JSON_THROW_ON_ERROR);
  if (!Elementor\Plugin::$instance->documents->get(16)->save(['elements' => $footer, 'settings' => $fb['meta']['_elementor_page_settings'] ?: []])) throw new RuntimeException('Footer save failed.');
  update_post_meta(16, '_mpcbc_v10_footer', 1);
  $cmp($footer, json_decode(get_post_meta(16, '_elementor_data', true), true), '/footer');
  echo "footer 16 saved\n";
 }
 Elementor\Plugin::$instance->files_manager->clear_cache();
 echo "saved to $id (" . get_permalink($id) . ")\n", $diffs ? "changed on save:\n" . implode("\n", $diffs) . "\n" : "readback identical\n";
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
