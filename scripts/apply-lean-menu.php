<?php
// Leaner primary menu (owner, 3 Nov 2026): a menu item only for something that is its own page;
// service times, directions, mission etc. live as sections inside pages. Keeps the language switcher (129).
// Run with WP-CLI eval-file. The previous menu is saved to content-source/before-lean-menu/menu.json.
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $root = 'C:/Projects/mpcbc';
 $backup = "$root/content-source/before-lean-menu"; wp_mkdir_p($backup);
 $items = wp_get_nav_menu_items(3);
 if (!file_exists("$backup/menu.json")) file_put_contents("$backup/menu.json", wp_json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

 // key, title, Chinese description, url, parent key, CSS class
 $spec = [
  ['about', 'About', '關於我們', '/about/', ''],
  ['visit', 'Visit us', '歡迎新朋友', '/visit/', 'about'],
  ['who', 'About us', '認識我們', '/about/', 'about'],
  ['pastors', 'Pastors & leaders', '牧者與領袖', '/about/#pastors', 'about'],
  ['worship', 'Worship', '崇拜與事工', '/watch/', ''],
  ['sermons', 'Sermons & livestream', '講道及網上直播', '/watch/', 'worship'],
  // ponytail: congregations link to their mpcbc.net sermon/ministry pages until their own pages exist
  ['canto', 'Cantonese', '粵語堂', 'https://mpcbc.net/category/cantonese-sermons/', 'worship'],
  ['mando', 'Mandarin', '國語堂', 'https://mpcbc.net/category/mandarin-sermons/', 'worship'],
  ['eng', 'English', '英語堂', 'https://mpcbc.net/english/', 'worship'],
  ['groups', 'Groups & fellowships', '團契及小組', '/#community', 'worship'],
  ['study', 'Bible study & Sunday school', '主日學及查經', '/#community', 'worship'],
  ['kids', 'Children', '兒童事工', '/kids/', ''],
  ['news', 'News', '教會消息與活動', '/#events', ''],
  ['events', 'Announcements & events', '教會報告及活動', '/#events', 'news'],
  ['letter', 'Newsletter', '教會通訊', '/#news', 'news'],
  ['photos', 'Photos & videos', '相片及影片', '/#community', 'news'], // ponytail: no gallery page yet
  ['give', 'Give', '奉獻', '/give/', '', 'mpcbc-menu-give'],
 ];

 // Remove everything except the language switcher and its children, then rebuild.
 foreach ($items as $item) if ((int) $item->ID !== 129 && (int) $item->menu_item_parent !== 129) wp_delete_post($item->ID, true);
 $ids = []; $order = 0; $report = [];
 foreach ($spec as $s) {
  [$key, $title, $zh, $url, $parent] = $s;
  if (str_starts_with($url, '/')) $url = home_url($url);
  $id = wp_update_nav_menu_item(3, 0, ['menu-item-title' => $title, 'menu-item-description' => $zh, 'menu-item-url' => $url,
   'menu-item-parent-id' => $parent ? $ids[$parent] : 0, 'menu-item-position' => ++$order, 'menu-item-status' => 'publish',
   'menu-item-type' => 'custom', 'menu-item-classes' => $s[5] ?? '']);
  if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
  $ids[$key] = $id;
  $report[] = compact('id', 'title', 'zh', 'url', 'parent');
 }
 foreach (wp_get_nav_menu_items(3) as $item) if ((int) $item->ID === 129 || (int) $item->menu_item_parent === 129) wp_update_post(['ID' => $item->ID, 'menu_order' => ++$order]);
 file_put_contents("$root/content-source/primary-menu-lean.json", wp_json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
 echo count($report) . " items saved; language switcher kept.\n";
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
