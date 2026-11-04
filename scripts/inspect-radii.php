<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$radii = [];
$walk = function($elements) use (&$walk, &$radii) {
    foreach ($elements as $el) {
        if (!empty($el['styles'])) {
            foreach ($el['styles'] as $cId => $cls) {
                foreach ($cls['variants'] as $v) {
                    if (isset($v['props']['border-radius'])) {
                        $radii[] = [
                            'el' => $el['id'],
                            'widget' => $el['widgetType'] ?? $el['elType'],
                            'bp' => $v['meta']['breakpoint'],
                            'radius' => $v['props']['border-radius']
                        ];
                    }
                }
            }
        }
        if (isset($el['elements'])) $walk($el['elements']);
    }
};
$walk($data);
echo "Found " . count($radii) . " border-radius props:\n";
foreach (array_slice($radii, 0, 10) as $r) {
    echo "  {$r['el']} ({$r['widget']}) [{$r['bp']}]: " . json_encode($r['radius']) . "\n";
}
