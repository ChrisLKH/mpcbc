<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$allRows = \Duplicator\Package\DupPackage::dbSelect();
echo "All package rows in DB: " . count($allRows) . "\n";
foreach ($allRows as $r) {
    echo "ID: " . $r['id'] . " | Name: " . $r['name'] . " | Status: " . $r['status'] . "\n";
}

echo "Running Runner::process()...\n";
$result = \Duplicator\Package\Runner::process();
echo "Process result: " . var_export($result, true) . "\n";
