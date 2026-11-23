<?php
// Applies content-source/v10-pages.json (from build-v10-pages.mjs): each entry updates an existing page (id) or the
// page with that slug (created if missing). An existing page is backed up once to content-source/before-v10/<id>.json
// and is only overwritten while it still matches that backup or was written by this script (meta _mpcbc_v10_page).
// V10_PAGES=about,english limits the run. Also sets the workbook's SEO fields (read by mpcbc-translations).
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $a = get_users(['role' => 'administrator', 'number' => 1]); wp_set_current_user($a[0]->ID);
 $root = 'C:/Projects/mpcbc';
 $pages = json_decode(file_get_contents("$root/content-source/v10-pages.json"), true, 512, JSON_THROW_ON_ERROR);
 $only = array_filter(explode(',', (string) getenv('V10_PAGES')));
 foreach ($pages as $key => $p) {
  if ($only && !in_array($key, $only, true)) continue;
  if (!empty($p['id'])) {
   $id = (int) $p['id'];
  } else {
   $found = get_posts(['post_type' => 'page', 'post_status' => 'any', 'name' => $p['slug'], 'post_parent' => 0, 'numberposts' => 1]);
   $id = $found ? $found[0]->ID : wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $p['title'], 'post_name' => $p['slug']]);
  }
  $file = "$root/content-source/before-v10/$id.json";
  $live = json_decode((string) get_post_meta($id, '_elementor_data', true), true);
  if (!file_exists($file)) {
   file_put_contents($file, wp_json_encode(['post' => get_post($id), 'meta' => ['_elementor_data' => $live, '_elementor_page_settings' => get_post_meta($id, '_elementor_page_settings', true), '_wp_page_template' => get_post_meta($id, '_wp_page_template', true)]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
  } else {
   $backup = json_decode(file_get_contents($file), true);
   if ($live != $backup['meta']['_elementor_data'] && !get_post_meta($id, '_mpcbc_v10_page', true)) throw new RuntimeException("$key ($id) changed since the backup. Do not overwrite editor changes.");
  }
  if (!empty($p['id']) && get_the_title($id) !== $p['title']) wp_update_post(['ID' => $id, 'post_title' => $p['title']]);
  update_post_meta($id, '_elementor_edit_mode', 'builder');
  update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
  [$t, $tz, $d, $dz] = $p['seo'];
  foreach (['mpcbc_seo_title' => $t, 'mpcbc_seo_title_zh' => $tz, 'mpcbc_seo_description' => $d, 'mpcbc_seo_description_zh' => $dz] as $k => $v) update_post_meta($id, $k, $v);
  if (!Elementor\Plugin::$instance->documents->get($id)->save(['elements' => $p['elements'], 'settings' => get_post_meta($id, '_elementor_page_settings', true) ?: []])) throw new RuntimeException("$key: native save failed.");
  update_post_meta($id, '_mpcbc_v10_page', 1);

  // report anything Elementor dropped or rewrote on save (an invalid prop shows up here)
  $actual = json_decode(get_post_meta($id, '_elementor_data', true), true);
  $diffs = [];
  $cmp = function ($x, $y, $path) use (&$cmp, &$diffs) {
   if (is_array($x) && is_array($y)) { foreach (array_unique(array_merge(array_keys($x), array_keys($y))) as $k) $cmp($x[$k] ?? null, $y[$k] ?? null, "$path/$k"); }
   elseif ($x != $y && !str_ends_with($path, 'editor_settings/title') && count($diffs) < 30) $diffs[] = $path . ': ' . substr(wp_json_encode($x, JSON_UNESCAPED_UNICODE), 0, 90) . ' => ' . substr(wp_json_encode($y, JSON_UNESCAPED_UNICODE), 0, 90);
  };
  $cmp($p['elements'], $actual, '');
  echo "$key saved to $id (" . get_permalink($id) . ")\n", $diffs ? "  changed on save:\n  " . implode("\n  ", $diffs) . "\n" : "  readback identical\n";
 }
 Elementor\Plugin::$instance->files_manager->clear_cache();
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
