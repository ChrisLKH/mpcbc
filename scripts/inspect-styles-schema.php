<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

// Inspect styles on section 1 (This Sunday) and a heading / paragraph / button / image
function findExamples($nodes) {
    $found = [];
    $walk = function($elements) use (&$walk, &$found) {
        foreach ($elements as $el) {
            $type = $el['widgetType'] ?? $el['elType'];
            if (!isset($found[$type]) && !empty($el['styles'])) {
                $found[$type] = [
                    'id' => $el['id'],
                    'type' => $type,
                    'classes' => $el['settings']['classes'] ?? null,
                    'styles' => $el['styles']
                ];
            }
            if (isset($el['elements'])) $walk($el['elements']);
        }
    };
    $walk($nodes);
    return $found;
}

$examples = findExamples($data);
echo json_encode($examples, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
