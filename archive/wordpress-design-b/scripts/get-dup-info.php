<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc-2/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc-2/app/public/wp-load.php';

use Duplicator\Package\DupPackage;

$package = DupPackage::getById(1);
if ($package) {
    echo "ID: " . $package->getId() . "\n";
    echo "Status: " . $package->getStatus() . "\n";
    echo "Archive File: " . $package->getArchiveFileName() . "\n";
    echo "Archive Path: " . $package->getArchivePath() . "\n";
    echo "Installer File: " . $package->Installer->File . "\n";
    echo "Installer Full Path: " . $package->Installer->getInstallerPath() . "\n";
}
