<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/wordpress/scripts/welcome-rev77.json'), true);
echo "Root elements count: " . count($data[0]['elements']) . "\n";
foreach ($data[0]['elements'] as $idx => $el) {
    echo "  [{$idx}] ID: " . $el['id'] . " (elType: " . $el['elType'] . ")\n";
}
