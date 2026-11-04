<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Libs\Scan\ScanIterator;

$scan = new ScanIterator('C:/Users/chris/Local Sites/mpcbc/app/public');
$count = 0;
$weirdPaths = [];
foreach ($scan as $node) {
    $count++;
    $path = $node->getPath();
    if (stripos($path, 'Projects') !== false) {
        $weirdPaths[] = $path;
    }
}
echo "Total scanned items: $count\n";
echo "Items with 'Projects': " . count($weirdPaths) . "\n";
print_r(array_slice($weirdPaths, 0, 10));
