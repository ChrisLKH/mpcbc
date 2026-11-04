<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$sec4 = $data[4];
$panels = [
    'mpcbc-comm_panel_con_1',
    'mpcbc-comm_panel_con_2',
    'mpcbc-comm_panel_con_3',
    'mpcbc-comm_panel_con_4'
];

$findPanel = function($elements) use (&$findPanel, $panels) {
    foreach ($elements as $el) {
        $cssid = $el['settings']['_cssid']['value'] ?? ($el['settings']['_cssid'] ?? '');
        if (in_array($cssid, $panels, true)) {
            echo "Panel $cssid:\n";
            echo "  styles: " . json_encode($el['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        }
        if (isset($el['elements'])) $findPanel($el['elements']);
    }
};

$findPanel([$sec4]);
