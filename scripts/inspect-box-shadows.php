<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$shadows = [];
$walk = function($elements) use (&$walk, &$shadows) {
    foreach ($elements as $el) {
        if (!empty($el['styles'])) {
            foreach ($el['styles'] as $cId => $cls) {
                foreach ($cls['variants'] as $v) {
                    if (isset($v['props']['box-shadow'])) {
                        $shadows[] = [
                            'el' => $el['id'],
                            'widget' => $el['widgetType'] ?? $el['elType'],
                            'bp' => $v['meta']['breakpoint'],
                            'shadow' => $v['props']['box-shadow']
                        ];
                    }
                }
            }
        }
        if (isset($el['elements'])) $walk($el['elements']);
    }
};
$walk($data);
echo "Found " . count($shadows) . " box-shadow props:\n";
echo json_encode(array_slice($shadows, 0, 5), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
