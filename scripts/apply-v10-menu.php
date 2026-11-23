<?php
// DESIGN-v10 / workbook header menu: About Us · Plan a Visit · Worship & Ministries (3 columns) · MPCBC KIDS ·
// News & Events · Watch Live · Give, plus the existing language switcher (129).
// Title = English, Description = 繁體 (shown instead of the title on 繁/简 pages; 简 is converted).
// Column headings carry the class mpcbc-menu-label and render as text, not links (hello-mpcbc/functions.php).
// Run with WP-CLI eval-file. The previous menu is saved to content-source/before-v10/menu.json.
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $root = 'C:/Projects/mpcbc';
 $items = wp_get_nav_menu_items(3);
 $backup = "$root/content-source/before-v10/menu.json";
 if (!file_exists($backup)) file_put_contents($backup, wp_json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

 // key, title, 繁體 description, url, parent key, CSS classes
 $spec = [
  ['about', 'About Us', '關於我們', '/about/', ''],
  ['visit', 'Plan a Visit', '計劃來訪', '/visit/', ''],
  ['worship', 'Worship & Ministries', '崇拜及事工', '#', '', 'mpcbc-mega'],
 ];
 // ponytail: congregation pages (/english/ …) are planned in the workbook but not built yet; these links 404 until they exist
 foreach ([['en', 'English', '英語堂', 'english'], ['yue', 'Cantonese', '粵語堂', 'cantonese'], ['cmn', 'Mandarin', '國語堂', 'mandarin']] as [$k, $en, $zh, $slug]) {
  $spec[] = ["col-$k", $en, $zh, '#', 'worship', 'mpcbc-menu-label'];
  $spec[] = ["$k-overview", 'Overview', '簡介', "/$slug/", "col-$k"];
  $spec[] = ["$k-sermons", 'Sermons & Livestream', '講道及直播', "/$slug/sermons/", "col-$k"];
  $spec[] = ["$k-ministries", 'Fellowship & Ministries', '團契及事工', "/$slug/#groups", "col-$k"];
 }
 $spec[] = ['kids', 'MPCBC KIDS', '兒童事工', '/kids/', ''];
 $spec[] = ['news', 'News & Events', '消息及活動', '/news/', ''];
 $spec[] = ['watch', 'Watch Live', '線上崇拜', '/#online', '', 'mpcbc-menu-watch'];
 $spec[] = ['give', 'Give', '奉獻', '/give/', '', 'mpcbc-menu-give'];

 // Remove everything except the language switcher and its children, then rebuild.
 foreach ($items as $item) if ((int) $item->ID !== 129 && (int) $item->menu_item_parent !== 129) wp_delete_post($item->ID, true);
 $ids = []; $order = 0;
 foreach ($spec as $s) {
  [$key, $title, $zh, $url, $parent] = $s;
  if (str_starts_with($url, '/')) $url = home_url($url);
  $id = wp_update_nav_menu_item(3, 0, ['menu-item-title' => $title, 'menu-item-description' => $zh, 'menu-item-url' => $url,
   'menu-item-parent-id' => $parent ? $ids[$parent] : 0, 'menu-item-position' => ++$order, 'menu-item-status' => 'publish',
   'menu-item-type' => 'custom', 'menu-item-classes' => $s[5] ?? '']);
  if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
  $ids[$key] = $id;
 }
 foreach (wp_get_nav_menu_items(3) as $item) if ((int) $item->ID === 129 || (int) $item->menu_item_parent === 129) wp_update_post(['ID' => $item->ID, 'menu_order' => ++$order]);
 echo count($spec) . " items saved; language switcher kept.\n";
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
