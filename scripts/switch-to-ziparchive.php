<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\DupPackage;

$global = GlobalEntity::getInstance();
echo "Current build mode: " . $global->getBuildMode() . " (" . $global->getArchiveEngine() . ")\n";
echo "ZipArchive available: " . (class_exists('ZipArchive') ? "Yes" : "No") . "\n";

// Set to ZipArchive
$global->setBuildMode(PackageArchive::BUILD_MODE_ZIP_ARCHIVE, true);

echo "Updated build mode: " . $global->getBuildMode() . " (" . $global->getArchiveEngine() . ")\n";

// Clean up packages
$all = DupPackage::dbSelect('1=1');
foreach ($all as $pkg) {
    echo "Deleting package " . $pkg->getId() . "...\n";
    $pkg->delete();
}

// Clean up duplicator-backups/tmp
$tmpFiles = glob('C:/Users/chris/Local Sites/mpcbc/app/public/wp-content/duplicator-backups/tmp/*');
foreach ($tmpFiles as $f) {
    if (is_file($f)) @unlink($f);
}
echo "Cleaned tmp files.\n";
