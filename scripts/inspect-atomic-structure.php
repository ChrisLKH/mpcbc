<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

echo "Type of data: " . (is_array($data) ? "array" : gettype($data)) . "\n";
echo "Count of items: " . count($data) . "\n";
if (isset($data[0])) {
    echo "First element keys: " . implode(', ', array_keys($data[0])) . "\n";
    echo "First element id: " . ($data[0]['id'] ?? '') . "\n";
    echo "First element elType: " . ($data[0]['elType'] ?? '') . "\n";
    if (isset($data[0]['styles'])) {
        echo "First element styles count: " . count($data[0]['styles']) . "\n";
    }
}
// Check where 'styles' exist
$findStyles = function($nodes, $path = '') use (&$findStyles) {
    foreach ($nodes as $i => $n) {
        $p = "$path/$i:{$n['id']}:{$n['elType']}";
        if (isset($n['styles'])) {
            echo "Node has styles at $p (count: " . count($n['styles']) . ")\n";
        }
        if (isset($n['settings']['classes'])) {
            echo "Node has settings.classes at $p: " . json_encode($n['settings']['classes']) . "\n";
        }
        if (isset($n['elements'])) {
            $findStyles($n['elements'], $p);
        }
    }
};
// Sample first 2 sections
$sample = array_slice($data, 0, 2);
$findStyles($sample);
