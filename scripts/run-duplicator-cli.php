<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Package\DupPackage;
use Duplicator\Package\Runner;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Lock\LockUtil;

// Unlock any stuck process lock first
LockUtil::unlockProcess();

$active = DupPackage::getNextActive();
if (!$active) {
    echo "No active package found.\n";
    $all = DupPackage::dbSelect('1=1');
    echo "Total packages: " . count($all) . "\n";
    foreach ($all as $pkg) {
        echo "ID: " . $pkg->getId() . " | Status: " . $pkg->getStatus() . " | Name: " . $pkg->getName() . "\n";
    }
    exit;
}

echo "Active package found: " . $active->getId() . " (" . $active->getName() . "), status: " . $active->getStatus() . "\n";

$iterations = 0;
while (DupPackage::isPackageRunning() && $iterations < 100) {
    $iterations++;
    echo "Processing chunk #$iterations... (status: " . $active->getStatus() . ")\n";
    try {
        Runner::process();
    } catch (Throwable $e) {
        echo "Error in process(): " . $e->getMessage() . "\n";
        break;
    }
    
    // Refresh package
    $active = DupPackage::getById($active->getId());
    echo "Status after chunk: " . $active->getStatus() . "\n";
    if ($active->getStatus() == AbstractPackage::STATUS_COMPLETE) {
        echo "PACKAGE COMPLETED SUCCESSFULLY!\n";
        break;
    }
    if ($active->getStatus() < 0) {
        echo "PACKAGE FAILED with status: " . $active->getStatus() . "\n";
        break;
    }
    sleep(1);
}

echo "\nFinal details:\n";
$active = DupPackage::getById($active->getId());
echo "ID: " . $active->getId() . "\n";
echo "Status: " . $active->getStatus() . "\n";
echo "Archive file: " . $active->getArchiveFileName() . "\n";
echo "Installer file: " . $active->getInstallerFileName() . "\n";
