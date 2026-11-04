<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/16.json'), true);

$radii = [];
$walk = function($elements) use (&$walk, &$radii) {
    foreach ($elements as $el) {
        if (!empty($el['styles'])) {
            foreach ($el['styles'] as $cId => $cls) {
                foreach ($cls['variants'] as $v) {
                    if (isset($v['props']['border-radius'])) {
                        $radii[] = [
                            'id' => $el['id'],
                            'widget' => $el['widgetType'] ?? $el['elType'],
                            'r' => $v['props']['border-radius']
                        ];
                    }
                }
            }
        }
        if (isset($el['elements'])) $walk($el['elements']);
    }
};
$walk($data);
echo "Footer border-radius props count: " . count($radii) . "\n";
foreach ($radii as $r) {
    echo "  {$r['id']} ({$r['widget']}): " . json_encode($r['r']) . "\n";
}
