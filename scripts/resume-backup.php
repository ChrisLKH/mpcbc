<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Package\DupPackage;
use Duplicator\Package\Runner;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Lock\LockUtil;

LockUtil::unlockProcess();

// Clean lock files
foreach (glob(DUPLICATOR_SSDIR_PATH . '/building_lock_*.txt') as $l) {
    @unlink($l);
}

echo "Resuming running packages...\n";
$iterations = 0;
while (DupPackage::isPackageRunning() && $iterations < 2000) {
    $iterations++;
    try {
        Runner::process();
    } catch (Throwable $e) {
        echo "Error in process(): " . $e->getMessage() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
        break;
    }

    $package = DupPackage::getNextActive(false);
    if ($package === null) {
        echo "No more running packages.\n";
        break;
    }
    if ($iterations % 20 === 0 || $package->getStatus() != AbstractPackage::STATUS_ARCSTART) {
        echo "[Chunk #$iterations] Status: " . $package->getStatus() . "\n";
    }

    if ($package->getStatus() == AbstractPackage::STATUS_COMPLETE) {
        echo "\n============================================\n";
        echo "SUCCESS! PACKAGE BUILD COMPLETED!\n";
        echo "============================================\n";
        break;
    }
    if ($package->getStatus() < 0) {
        echo "\nFAILED with status: " . $package->getStatus() . "\n";
        break;
    }
}

// Find newest package
$packages = DupPackage::dbSelect('1=1 ORDER BY id DESC LIMIT 1');
if (!empty($packages)) {
    $finalPkg = reset($packages);
    echo "\nFinal Details:\n";
    echo "ID: " . $finalPkg->getId() . "\n";
    echo "Status: " . $finalPkg->getStatus() . "\n";
    echo "Archive File: " . $finalPkg->getArchiveFileName() . "\n";

    $archivePath = DUPLICATOR_SSDIR_PATH . '/' . $finalPkg->getArchiveFileName();
    echo "Archive Path: " . $archivePath . " (exists: " . (file_exists($archivePath) ? 'YES (' . round(filesize($archivePath) / 1024 / 1024, 2) . ' MB)' : 'NO') . ")\n";

    $installers = glob(DUPLICATOR_SSDIR_PATH . '/*installer*.php');
    echo "Found installer files:\n";
    foreach ($installers as $inst) {
        echo " - " . $inst . " (" . filesize($inst) . " bytes)\n";
    }
}
