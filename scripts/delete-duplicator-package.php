<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Package\DupPackage;

$p = DupPackage::getById(11);
if ($p) {
    echo "Found package 11: " . $p->getName() . "\n";
    $p->delete();
    echo "Deleted package 11.\n";
} else {
    echo "Package 11 not found.\n";
}

// Check backup directories
$dup_dirs = [
    'C:/Users/chris/Local Sites/mpcbc/app/public/wp-content/backups-dup-lite',
    'C:/Users/chris/Local Sites/mpcbc/app/public/wp-content/duplicator-backups',
];
foreach ($dup_dirs as $d) {
    if (is_dir($d)) {
        $files = glob($d . '/*');
        foreach ($files as $f) {
            if (is_file($f)) {
                @unlink($f);
                echo "Removed file: " . basename($f) . "\n";
            }
        }
    }
}

echo "Duplicator cleanup complete.\n";
