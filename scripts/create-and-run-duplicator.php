<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Models\GlobalEntity;
use Duplicator\Models\TemplateEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\PackageExecutionInfo;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\DupPackage;
use Duplicator\Package\Runner;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Lock\LockUtil;

// Unlock any stuck process lock first
LockUtil::unlockProcess();
$locks = glob(DUPLICATOR_SSDIR_PATH . '/building_lock_*.txt');
foreach ($locks as $l) {
    @unlink($l);
}

// Clean any tmp files
$tmpFiles = glob(DUPLICATOR_SSDIR_PATH_TMP . '/*');
foreach ($tmpFiles as $tf) {
    if (is_file($tf)) @unlink($tf);
}

// Set build mode to ZipArchive
$global = GlobalEntity::getInstance();
$global->setBuildMode(PackageArchive::BUILD_MODE_ZIP_ARCHIVE, true);
echo "Archive engine set to: " . $global->getArchiveEngine() . "\n";

// Clean existing packages
$existing = DupPackage::dbSelect('1=1');
foreach ($existing as $p) {
    echo "Deleting existing package ID " . $p->getId() . "...\n";
    $p->delete();
}

$template = TemplateEntity::getDefaultTemplate();
$storageIds = [StoragesUtil::getDefaultStorageId()];
$execInfo = new PackageExecutionInfo(AbstractPackage::EXECUTION_TYPE_MANUAL);

echo "Creating new package...\n";
$package = PackageUtils::createBackgroundPackage($template, $storageIds, $execInfo, 'MPCBC Mockup Package');
echo "Package ID: " . $package->getId() . " created with status: " . $package->getStatus() . "\n";

$iterations = 0;
while (DupPackage::isPackageRunning() && $iterations < 300) {
    $iterations++;
    try {
        Runner::process();
    } catch (Throwable $e) {
        echo "Error in process(): " . $e->getMessage() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
        break;
    }

    $package = DupPackage::getById($package->getId());
    echo "[Chunk #$iterations] Status: " . $package->getStatus() . "\n";
    
    if ($package->getStatus() == AbstractPackage::STATUS_COMPLETE) {
        echo "\n============================================\n";
        echo "PACKAGE BUILD COMPLETED SUCCESSFULLY!\n";
        echo "============================================\n";
        break;
    }
    if ($package->getStatus() < 0) {
        echo "\nPACKAGE BUILD FAILED with status: " . $package->getStatus() . "\n";
        break;
    }
}

$finalPkg = DupPackage::getById($package->getId());
echo "\nFinal Package Summary:\n";
echo "ID: " . $finalPkg->getId() . "\n";
echo "Name: " . $finalPkg->getName() . "\n";
echo "Status: " . $finalPkg->getStatus() . "\n";
echo "Archive File: " . $finalPkg->getArchiveFileName() . "\n";
if (isset($finalPkg->Installer)) {
    echo "Installer File: " . $finalPkg->Installer->getInstallerLocalName() . "\n";
}

$storageFolder = DUPLICATOR_SSDIR_PATH;
echo "Storage Folder: " . $storageFolder . "\n";

$archivePath = $storageFolder . '/' . $finalPkg->getArchiveFileName();
$installerPath = $storageFolder . '/' . ($finalPkg->Installer ? $finalPkg->Installer->getInstallerLocalName() : 'installer.php');

echo "Archive full path: " . $archivePath . " (exists: " . (file_exists($archivePath) ? 'YES (' . filesize($archivePath) . ' bytes)' : 'NO') . ")\n";
echo "Installer full path: " . $installerPath . " (exists: " . (file_exists($installerPath) ? 'YES (' . filesize($installerPath) . ' bytes)' : 'NO') . ")\n";
