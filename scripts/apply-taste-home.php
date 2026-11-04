<?php
// Applies content-source/taste-home.json (from build-taste-home.mjs).
// TASTE_TARGET=draft (default): a draft page "Home (taste build)" for checking.
// TASTE_TARGET=home: replaces Home 21, only while it still matches the backup in before-taste-rebuild.
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $a = get_users(['role' => 'administrator', 'number' => 1]); wp_set_current_user($a[0]->ID);
 $root = 'C:/Projects/mpcbc';
 $elements = json_decode(file_get_contents("$root/content-source/taste-home.json"), true, 512, JSON_THROW_ON_ERROR);
 $backup = json_decode(file_get_contents("$root/content-source/before-taste-rebuild/21.json"), true);

 // the one colour the old palette had no slot for
 $kit = (int) get_option('elementor_active_kit');
 $vars = json_decode(get_post_meta($kit, '_elementor_global_variables', true), true);
 if (!isset($vars['data']['e-gv-kidsfld'])) {
  $vars['data']['e-gv-kidsfld'] = ['type' => 'global-color-variable', 'label' => 'mpcbc-color-kids-field', 'value' => ['$$type' => 'color', 'value' => '#FFE680'],
   'order' => count($vars['data']) + 1, 'created_at' => current_time('mysql'), 'updated_at' => current_time('mysql')];
  $vars['watermark']++;
  update_post_meta($kit, '_elementor_global_variables', wp_slash(wp_json_encode($vars)));
 }

 // alt text lives on the attachments (Atomic images read it from the media library)
 $media = json_decode(file_get_contents("$root/content-source/taste-media.json"), true);
 foreach (['pastor-caleb.jpg' => 'Rev. Caleb Cheung, smiling, in a grey suit and red tie', 'church-commonwealth.jpg' => 'The church at 302 W. Commonwealth Ave.: a cream stucco chapel with a white steeple, palm trees and a black iron fence',
  'steeple-stamp.jpg' => 'The church’s white steeple and cross', 'vbs-crafts.jpg' => 'Children at a craft table holding up paper flowers they made',
  'vbs-wave.jpg' => 'Three girls in VBS shirts waving at the camera in the courtyard', 'vbs-floor.jpg' => 'Children in “Love God, love others” shirts building on the classroom floor'] as $f => $alt)
  update_post_meta($media[$f]['id'], '_wp_attachment_image_alt', $alt);

 $target = getenv('TASTE_TARGET') ?: 'draft';
 if ($target === 'home') {
  $id = 21;
  $live = json_decode(get_post_meta($id, '_elementor_data', true), true);
  if ($live != $backup['meta']['_elementor_data'] && !get_post_meta($id, '_mpcbc_taste_home', true)) throw new RuntimeException('Home changed since the backup. Do not overwrite editor changes.');
 } else {
  $found = get_posts(['post_type' => 'page', 'post_status' => 'any', 'name' => 'home-taste-build', 'numberposts' => 1]);
  $id = $found ? $found[0]->ID : wp_insert_post(['post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Home (taste build)', 'post_name' => 'home-taste-build']);
  update_post_meta($id, '_elementor_edit_mode', 'builder');
 }
 update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
 $document = Elementor\Plugin::$instance->documents->get($id);
 if (!$document->save(['elements' => $elements, 'settings' => $backup['meta']['_elementor_page_settings'] ?? []])) throw new RuntimeException('Native save failed.');
 if ($target === 'home') update_post_meta($id, '_mpcbc_taste_home', 1);

 // report anything Elementor dropped or rewrote on save (an invalid prop shows up here)
 $actual = json_decode(get_post_meta($id, '_elementor_data', true), true);
 $diffs = [];
 $cmp = function ($a, $b, $path) use (&$cmp, &$diffs) {
  if (is_array($a) && is_array($b)) { foreach (array_unique(array_merge(array_keys($a), array_keys($b))) as $k) $cmp($a[$k] ?? null, $b[$k] ?? null, "$path/$k"); }
  elseif ($a != $b && count($diffs) < 40) $diffs[] = $path . ': ' . substr(wp_json_encode($a), 0, 80) . ' => ' . substr(wp_json_encode($b), 0, 80);
 };
 $cmp($elements, $actual, '');
 // the shared footer, only while it still matches its backup (or was written by this script)
 if (getenv('TASTE_FOOTER')) {
  $fb = json_decode(file_get_contents("$root/content-source/before-taste-rebuild/16.json"), true);
  $fl = json_decode(get_post_meta(16, '_elementor_data', true), true);
  if ($fl != $fb['meta']['_elementor_data'] && !get_post_meta(16, '_mpcbc_taste_footer', true)) throw new RuntimeException('Footer changed since the backup.');
  $footer = json_decode(file_get_contents("$root/content-source/taste-footer.json"), true, 512, JSON_THROW_ON_ERROR);
  if (!Elementor\Plugin::$instance->documents->get(16)->save(['elements' => $footer, 'settings' => $fb['meta']['_elementor_page_settings'] ?? []])) throw new RuntimeException('Footer save failed.');
  update_post_meta(16, '_mpcbc_taste_footer', 1);
  echo "footer 16 saved\n";
 }
 Elementor\Plugin::$instance->files_manager->clear_cache();
 echo "saved to $id (" . get_permalink($id) . ")\n", $diffs ? "changed on save:\n" . implode("\n", $diffs) . "\n" : "readback identical\n";
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
