<?php
// Repaints the whole site in the taste-mockup palette (newsprint grey + bright yellow) and swaps
// Bricolage Grotesque / Geist for Hanken Grotesk. Changes values only: every page keeps pointing at
// the same Atomic variables and Global Colours. Backup: content-source/before-taste-rebuild/kit-5.json
if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new Exception('Run only on mpcbc.local');

// old value (case-insensitive) => new value
$colors = [
  '#155E75' => '#181715', // Harbor (buttons, links, accents) -> Ink
  '#0F2A33' => '#181715', // Deep sea -> Ink
  '#13232A' => '#181715', // Ink
  '#16140F' => '#181715',
  '#2B1404' => '#181715', // On persimmon
  '#0A1F26' => '#181715', // Footer
  '#0E4A5C' => '#2E2D2A', // Harbor deep / hover
  '#24434D' => '#2E2D2A',
  '#2C4A45' => '#33322E',
  '#1E3A44' => '#262522',
  '#33424A' => '#3A3934',
  '#4F6168' => '#5C5B55', // Ink soft -> Muted
  '#F6F7F5' => '#E8E8E3', // Paper ground -> Page
  '#E3EBEA' => '#DCDCD6', // Mist -> Surface
  '#E3DACB' => '#DCDCD6',
  '#EAF2F3' => '#FFFFFF', // Card tint -> Paper
  '#D9E2E1' => '#C4C4BC', // Line -> Tone 3
  '#BFCDCC' => '#B4B3AB', // Line strong
  '#F28C38' => '#FFD21A', // Persimmon -> Accent
  '#E07A26' => '#F2C200', // Persimmon hover
  '#C2600F' => '#181715', // Tick
  '#FCE3CC' => '#FFF4BF', // Persimmon soft -> Coupon input
  'rgba(10,31,38,0.12)' => 'rgba(24,23,21,0.12)',
  'rgba(10,31,38,0.97)' => 'rgba(24,23,21,0.97)',
  'rgba(15,42,51,0.88)' => 'rgba(24,23,21,0.88)',
];
$fonts = ['Bricolage Grotesque' => 'Hanken Grotesk', 'Geist' => 'Hanken Grotesk', 'Roboto' => 'Hanken Grotesk', 'Roboto Slab' => 'Hanken Grotesk'];
$map = fn($v) => $colors[strtoupper(str_replace(' ', '', $v))] ?? $colors[strtolower(str_replace(' ', '', $v))] ?? null;

$kit = (int) get_option('elementor_active_kit');
$changed = 0;

// 1. Atomic global variables
$vars = json_decode(get_post_meta($kit, '_elementor_global_variables', true), true);
foreach ($vars['data'] as $id => &$v) {
  $val = $v['value']['value'] ?? null;
  if (!is_string($val)) continue;
  $new = $v['type'] === 'global-color-variable' ? $map($val) : ($v['type'] === 'global-font-variable' ? ($fonts[$val] ?? null) : null);
  if ($new && $new !== $val) { echo "var $id $val -> $new\n"; $v['value']['value'] = $new; $v['updated_at'] = current_time('mysql'); $changed++; }
}
unset($v);
$vars['watermark'] = ($vars['watermark'] ?? 0) + 1;
update_post_meta($kit, '_elementor_global_variables', wp_slash(wp_json_encode($vars)));

// 2. Classic Global Colours / Fonts in the kit
$s = get_post_meta($kit, '_elementor_page_settings', true);
foreach (['system_colors', 'custom_colors'] as $key) foreach ($s[$key] ?? [] as $i => $c) {
  if ($new = $map($c['color'])) { echo "kit {$c['title']} {$c['color']} -> $new\n"; $s[$key][$i]['color'] = $new; $changed++; }
}
foreach (['system_typography', 'custom_typography'] as $key) foreach ($s[$key] ?? [] as $i => $t) {
  $f = $t['typography_font_family'] ?? '';
  if (isset($fonts[$f])) { $s[$key][$i]['typography_font_family'] = $fonts[$f]; $changed++; }
  // taste type system: weights 400/500 only
  if ((int) ($t['typography_font_weight'] ?? 0) > 500) $s[$key][$i]['typography_font_weight'] = '500';
}
foreach ($s as $k => $val) if (is_string($val) && str_ends_with($k, 'color') && ($new = $map($val))) { $s[$k] = $new; $changed++; }
foreach ($s as $k => $val) if (is_string($val) && str_ends_with($k, 'font_family') && isset($fonts[$val])) { $s[$k] = $fonts[$val]; $changed++; }
update_post_meta($kit, '_elementor_page_settings', $s);

\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "changed $changed values\n";
