<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

echo "Duplicator active: " . (is_plugin_active('duplicator/duplicator.php') ? "Yes" : "No") . "\n";

if (class_exists('\Duplicator\Models\PackageModel')) {
    $packages = \Duplicator\Models\PackageModel::getAll();
    echo "Found " . count($packages) . " packages.\n";
    foreach ($packages as $pkg) {
        echo "ID: " . $pkg->getId() . " | Name: " . $pkg->getName() . " | Status: " . $pkg->getStatus() . "\n";
    }
} else {
    echo "PackageModel class not found. Checking DUP_Package...\n";
    if (class_exists('DUP_Package')) {
        $packages = DUP_Package::get_all();
        echo "Found " . count($packages) . " packages.\n";
    } else {
        echo "Searching for package classes...\n";
        $classes = get_declared_classes();
        $dupClasses = array_filter($classes, function($c) { return stripos($c, 'duplicator') !== false || stripos($c, 'dup_') !== false; });
        print_r($dupClasses);
    }
}

// Also check wp-content/backups-dup-lite or wp-snapshots
$upload_dir = wp_upload_dir();
echo "Upload dir: " . $upload_dir['basedir'] . "\n";
$dirs = glob('C:/Users/chris/Local Sites/mpcbc/app/public/wp-content/*dup*');
print_r($dirs);
$dirs2 = glob('C:/Users/chris/Local Sites/mpcbc/app/public/wp-snapshots/*');
print_r($dirs2);
