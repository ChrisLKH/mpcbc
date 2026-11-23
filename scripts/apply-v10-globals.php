<?php
// DESIGN-v10 globals: backs up Home, footer, Visit and the kit (once), then repaints the site in the v10 palette
// (warm cream + navy) by variable ID, gives the main variables readable names, adds the new tokens, and sets
// Theme Style buttons. Pages keep pointing at the same variables, so every page recolours without edits.
// Run with scripts/wp.ps1 eval-file. Backups: content-source/before-v10/.
if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new Exception('Run only on mpcbc.local');

$root = 'C:/Projects/mpcbc';
$kit = (int) get_option('elementor_active_kit');

// ---------- 1. backups (never overwritten) ----------
$dir = "$root/content-source/before-v10";
if (!is_dir($dir)) {
  mkdir($dir, 0777, true);
  foreach ([21, 16, 60] as $id) {
    $meta = [];
    foreach (['_elementor_data', '_elementor_page_settings', '_wp_page_template', '_elementor_edit_mode'] as $k) {
      $v = get_post_meta($id, $k, true);
      $meta[$k] = $k === '_elementor_data' ? json_decode($v, true) : $v;
    }
    file_put_contents("$dir/$id.json", wp_json_encode(['post' => get_post($id), 'meta' => $meta], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
  }
  file_put_contents("$dir/kit-$kit.json", wp_json_encode([
    '_elementor_global_variables' => json_decode(get_post_meta($kit, '_elementor_global_variables', true), true),
    '_elementor_page_settings' => get_post_meta($kit, '_elementor_page_settings', true),
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
  foreach (['style.css', 'functions.php', 'footer.php', 'nav.js'] as $f) copy("$root/hello-mpcbc/$f", "$dir/$f");
  copy("$root/plugins/mpcbc-sermons/mpcbc-sermons.php", "$dir/mpcbc-sermons.php");
  echo "backups written to $dir\n";
}

// ---------- 2. Atomic variables, by ID: [value, readable label or null to keep] ----------
$NIGHT = 'rgba(18,26,38,%s)';
$set = [
  'e-gv-c70cdc4' => ['#1D2430', 'mpcbc-ink'],
  'e-gv-1771cc2' => ['#1D2430', null],
  'e-gv-5a81035' => ['#1D2430', null],
  'e-gv-96b5dde' => ['#1D2430', null],
  'e-gv-f7f3384' => ['#1D2430', null],
  'e-gv-f7cfe2a' => ['#FBF8F5', 'mpcbc-bg'],
  'e-gv-1984c94' => ['#FFFFFF', 'mpcbc-surface'],
  'e-gv-824ebc4' => ['#F4EEEA', 'mpcbc-fill'],
  'e-gv-3a42021' => ['#F4EEEA', null],
  'e-gv-3cc97d2' => ['#E7DFD9', 'mpcbc-line'],
  'e-gv-468ea41' => ['#E7DFD9', null],
  'e-gv-7317e11' => ['#5B6472', 'mpcbc-muted'],
  'e-gv-32ae709' => ['#121A26', 'mpcbc-night'],
  'e-gv-55a0d95' => ['#162E4C', 'mpcbc-accent-hover'],
  'e-gv-8cf32a6' => ['#162E4C', null],
  'e-gv-0c168a6' => ['#D4A43A', 'mpcbc-gold'],      // was the yellow accent; v10 keeps gold for decoration only
  'e-gv-f8f865d' => ['#D4A43A', null],
  'e-gv-428b859' => ['#FFF1C2', 'mpcbc-kids-band'],
  'e-gv-kidsfld' => ['#FFF1C2', null],
  'e-gv-468b90b' => ['#C42B35', 'mpcbc-live'],
  'e-gv-1a6ea38' => ['#C42B35', null],
  'e-gv-6b30939' => [sprintf($NIGHT, '0.12'), null],
  'e-gv-588b9f7' => [sprintf($NIGHT, '0.97'), null],
  'e-gv-63e4e5d' => [sprintf($NIGHT, '0.88'), null],
  'e-gv-f911c08' => [sprintf($NIGHT, '0.42'), null],
  'e-gv-b6fea0c' => [sprintf($NIGHT, '0.72'), null],
  'e-gv-11121dc' => [sprintf($NIGHT, '0.25'), null],
];
$fontLabels = ['e-gv-d649b5e' => 'mpcbc-font-en', 'e-gv-2635213' => 'mpcbc-font-tc', 'e-gv-99d935c' => 'mpcbc-font-sc'];
$new = [ // tokens the old palette had no slot for
  'e-gv-v10acc' => ['#1F3F66', 'mpcbc-accent'],
  'e-gv-v10ntx' => ['#C9D2DE', 'mpcbc-night-text'],
  'e-gv-v10mnt' => ['#CFEBDD', 'mpcbc-kids-mint'],
  'e-gv-v10sky' => ['#D3E5F6', 'mpcbc-kids-sky'],
];

$vars = json_decode(get_post_meta($kit, '_elementor_global_variables', true), true);
$now = current_time('mysql');
$changed = 0;
foreach ($set + $fontLabels as $id => $want) {
  if (!isset($vars['data'][$id])) throw new Exception("missing variable $id");
  $v = &$vars['data'][$id];
  [$value, $label] = is_array($want) ? $want : [null, $want];
  if ($value !== null && $v['value']['value'] !== $value) { $v['value']['value'] = $value; $changed++; }
  if ($label && $v['label'] !== $label) { $v['label'] = $label; $changed++; }
  $v['updated_at'] = $now;
  unset($v);
}
foreach ($new as $id => [$value, $label]) {
  if (isset($vars['data'][$id])) { $vars['data'][$id]['value']['value'] = $value; continue; }
  $vars['data'][$id] = ['type' => 'global-color-variable', 'label' => $label, 'value' => ['$$type' => 'color', 'value' => $value],
    'order' => count($vars['data']) + 1, 'created_at' => $now, 'updated_at' => $now];
  $changed++;
}
$labels = array_count_values(array_map(fn($x) => $x['label'], array_filter($vars['data'], fn($x) => empty($x['deleted']))));
if ($dupes = array_filter($labels, fn($n) => $n > 1)) throw new Exception('duplicate labels: ' . implode(',', array_keys($dupes)));
$vars['watermark'] = ($vars['watermark'] ?? 0) + 1;
update_post_meta($kit, '_elementor_global_variables', wp_slash(wp_json_encode($vars)));

// ---------- 3. classic Global Colours (used by Fluent Forms, the header CSS and classic widgets), by _id ----------
$kc = [
  'primary' => ['Accent (navy)', '#1F3F66'], 'secondary' => ['Night', '#121A26'], 'text' => ['Ink', '#1D2430'], 'accent' => ['Accent (navy)', '#1F3F66'],
  'paper' => ['Page (cream)', '#FBF8F5'], 'mist' => ['Fill', '#F4EEEA'], 'stone' => ['Fill (legacy)', '#F4EEEA'], 'ink_soft' => ['Muted', '#5B6472'],
  'harbor_deep' => ['Accent hover', '#162E4C'], 'persimmon_soft' => ['Kids band', '#FFF1C2'], 'on_persimmon' => ['Ink (legacy)', '#1D2430'],
  'persimmon_hover' => ['Accent hover (legacy)', '#162E4C'], 'tick' => ['Ink (legacy tick)', '#1D2430'], 'live' => ['Live', '#C42B35'],
  'live_text' => ['Live (legacy text)', '#C42B35'], 'card_tint' => ['Surface (legacy tint)', '#FFFFFF'], 'line' => ['Line', '#E7DFD9'],
  'line_strong' => ['Line (legacy strong)', '#E7DFD9'], 'footer' => ['Night (footer)', '#121A26'], 'white' => ['Surface', '#FFFFFF'],
  'hero_video_top' => ['Hero / video top', sprintf($NIGHT, '0.12')], 'hero_video_bottom' => ['Hero / video bottom', sprintf($NIGHT, '0.97')],
  'hero_rail_line' => ['Hero / dividers', 'rgba(255,255,255,0.3)'],
  'gold' => ['Gold (decoration only)', '#D4A43A'], 'night_text' => ['Night text', '#C9D2DE'], 'kids_mint' => ['Kids mint', '#CFEBDD'], 'kids_sky' => ['Kids sky', '#D3E5F6'],
];
$s = get_post_meta($kit, '_elementor_page_settings', true);
$seen = [];
foreach (['system_colors', 'custom_colors'] as $key) foreach ($s[$key] ?? [] as $i => $c) {
  if (!isset($kc[$c['_id']])) continue;
  [$s[$key][$i]['title'], $s[$key][$i]['color']] = $kc[$c['_id']];
  $seen[$c['_id']] = 1;
}
foreach ($kc as $id => [$title, $color]) if (!isset($seen[$id])) $s['custom_colors'][] = ['_id' => $id, 'title' => $title, 'color' => $color];

// ---------- 4. Theme Style: body and buttons ----------
$s['__globals__'] = array_merge($s['__globals__'] ?? [], [
  'button_background_color' => 'globals/colors?id=primary',
  'button_hover_background_color' => 'globals/colors?id=harbor_deep',
  'button_text_color' => 'globals/colors?id=white',
  'button_hover_text_color' => 'globals/colors?id=white',
  'body_color' => 'globals/colors?id=text',
  'body_background_color' => 'globals/colors?id=paper',
]);
$s['body_background_background'] = 'classic';
$s['button_border_radius'] = ['unit' => 'px', 'top' => '999', 'right' => '999', 'bottom' => '999', 'left' => '999', 'isLinked' => true];
$s['button_typography_font_weight'] = '500';
$s['button_padding'] = ['unit' => 'px', 'top' => '14', 'right' => '26', 'bottom' => '14', 'left' => '26', 'isLinked' => false]; // 22 + 28 = 50px
$s['button_padding_mobile'] = $s['button_padding'];
$s['body_typography_typography'] = 'custom';
$s['body_typography_font_family'] = 'Hanken Grotesk';
$s['body_typography_font_size'] = ['unit' => 'px', 'size' => 17];
$s['body_typography_line_height'] = ['unit' => 'em', 'size' => 1.55];
$s['default_generic_fonts'] = 'Noto Sans TC, sans-serif'; // Chinese lines on English pages fall back to Noto, not the system font
update_post_meta($kit, '_elementor_page_settings', $s);

\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "changed $changed variable values/labels; kit colours and Theme Style set\n";
