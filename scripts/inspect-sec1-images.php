<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$sec1 = $data[1];
$thumbs = [];
$walk = function($elements) use (&$walk, &$thumbs) {
    foreach ($elements as $el) {
        if (($el['widgetType'] ?? $el['elType']) === 'e-image') {
            $thumbs[] = [
                'id' => $el['id'],
                'settings' => $el['settings'],
                'styles' => $el['styles']
            ];
        }
        if (isset($el['elements'])) $walk($el['elements']);
    }
};
$walk([$sec1]);
echo json_encode($thumbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
