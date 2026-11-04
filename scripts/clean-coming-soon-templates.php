<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$post_map = [
    'coming-soon-page.json' => 19,
    'visit-page.json'       => 60,
    'sundays-page.json'     => 61,
    'kids-page.json'        => 62,
    'watch-page.json'       => 63,
    'about-page.json'       => 64,
    'give-page.json'        => 65,
    'zh-page.json'          => 66,
];

function clean_elements(&$elements) {
    foreach ($elements as &$el) {
        if (($el['widgetType'] ?? '') === 'text-editor' && !empty($el['settings']['editor'])) {
            $html = $el['settings']['editor'];
            if (strpos($html, 'style=') !== false) {
                // Check if zh
                if (strpos($html, 'lang="zh-Hant"') !== false) {
                    // Extract text
                    $text = strip_tags($html);
                    $el['settings']['editor'] = "<p lang=\"zh-Hant\">{$text}</p>";
                    $el['settings']['text_color'] = '#5C564D';
                    $el['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink_soft' ];
                    $el['settings']['align'] = 'center';
                    $el['settings']['typography_typography'] = 'custom';
                    $el['settings']['typography_font_family'] = 'Noto Sans TC';
                    $el['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 28 ];
                    $el['settings']['typography_line_height'] = [ 'unit' => 'em', 'size' => 1.5 ];
                } else {
                    $text = strip_tags($html);
                    $el['settings']['editor'] = "<p>{$text}</p>";
                    $el['settings']['text_color'] = '#5C564D';
                    $el['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink_soft' ];
                    $el['settings']['align'] = 'center';
                    $el['settings']['typography_typography'] = 'custom';
                    $el['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 18 ];
                    $el['settings']['typography_line_height'] = [ 'unit' => 'em', 'size' => 1.6 ];
                }
            }
        }
        if (!empty($el['elements'])) {
            clean_elements($el['elements']);
        }
    }
}

foreach ($post_map as $filename => $post_id) {
    $path = "C:/Projects/mpcbc/wordpress/elementor/{$filename}";
    if (!file_exists($path)) continue;

    $doc = json_decode(file_get_contents($path), true);
    clean_elements($doc['content']);

    file_put_contents($path, json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    update_post_meta($post_id, '_elementor_data', wp_slash(json_encode($doc['content'])));
    echo "Cleaned {$filename} and Post {$post_id}.\n";
}

Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done.\n";
