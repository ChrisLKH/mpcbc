<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$sec8 = $data[8];
file_put_contents('C:/Users/chris/.gemini/antigravity/brain/9739df51-8c64-4e54-894b-ccf7703fff25/scratch/section8_before.json', json_encode($sec8, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$printTree = function($el, $depth = 0) use (&$printTree) {
    $indent = str_repeat('  ', $depth);
    $type = $el['widgetType'] ?? $el['elType'];
    $id = $el['id'];
    $cssid = $el['settings']['_cssid']['value'] ?? ($el['settings']['_cssid'] ?? '');
    $text = '';
    if (isset($el['settings']['title']['value'])) $text = " title: " . json_encode($el['settings']['title']['value'], JSON_UNESCAPED_UNICODE);
    if (isset($el['settings']['paragraph']['value'])) $text = " p: " . json_encode($el['settings']['paragraph']['value'], JSON_UNESCAPED_UNICODE);
    if (isset($el['settings']['text']['value'])) $text = " btn: " . json_encode($el['settings']['text']['value'], JSON_UNESCAPED_UNICODE);
    echo "$indent- [$type] id=$id cssid=$cssid$text\n";
    if (isset($el['elements'])) {
        foreach ($el['elements'] as $child) $printTree($child, $depth + 1);
    }
};

$printTree($sec8);
