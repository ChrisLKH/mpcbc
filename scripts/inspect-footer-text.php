<?php
$footerData = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/16.json'), true);

$findText = function($elements) use (&$findText) {
    foreach ($elements as $el) {
        $p = $el['settings']['paragraph']['value'] ?? null;
        $t = $el['settings']['title']['value'] ?? null;
        if ($p) echo "p: $p\n";
        if ($t) echo "title: $t\n";
        if (isset($el['elements'])) $findText($el['elements']);
    }
};

$findText($footerData);
