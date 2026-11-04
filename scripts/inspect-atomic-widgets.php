<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

function inspectType($nodes, $targetWidgetType) {
    $walk = function($elements) use (&$walk, $targetWidgetType) {
        foreach ($elements as $el) {
            $type = $el['widgetType'] ?? $el['elType'];
            if ($type === $targetWidgetType && !empty($el['styles'])) {
                return $el;
            }
            if (isset($el['elements'])) {
                $res = $walk($el['elements']);
                if ($res) return $res;
            }
        }
        return null;
    };
    return $walk($nodes);
}

$types = ['e-heading', 'e-paragraph', 'e-button', 'e-image'];
$results = [];
foreach ($types as $t) {
    $el = inspectType($data, $t);
    if ($el) {
        $results[$t] = [
            'id' => $el['id'],
            'settings' => $el['settings'],
            'styles' => $el['styles']
        ];
    }
}

file_put_contents('C:/Users/chris/.gemini/antigravity/brain/9739df51-8c64-4e54-894b-ccf7703fff25/scratch/inspect_atomic_widgets.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Saved to inspect_atomic_widgets.json\n";
