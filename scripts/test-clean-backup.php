<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
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
use Duplicator\Libs\Snap\SnapIO;

LockUtil::unlockProcess();

// Clean lock files
foreach (glob(DUPLICATOR_SSDIR_PATH . '/building_lock_*.txt') as $l) {
    @unlink($l);
}

// Clean tmp files
foreach (glob(DUPLICATOR_SSDIR_PATH_TMP . '/*') as $tf) {
    if (is_file($tf)) @unlink($tf);
}

// Set build mode to ZipArchive and DB engine to PHP
$global = GlobalEntity::getInstance();
$global->setBuildMode(PackageArchive::BUILD_MODE_ZIP_ARCHIVE, true);
$global->setZipArchiveMode(PackageArchive::ZIP_MODE_SINGLE_THREAD, true);
$global->setMysqldumpEnabled(false, true);
echo "Archive engine: " . $global->getArchiveEngine() . "\n";
echo "Zip mode: " . ($global->getZipArchiveMode() == PackageArchive::ZIP_MODE_SINGLE_THREAD ? 'Single-Thread' : 'Multi-Thread') . "\n";
echo "Mysqldump enabled: " . ($global->isMysqldumpEnabled() ? 'YES' : 'NO') . "\n";

// Clean existing packages
foreach (DupPackage::dbSelect('1=1') as $p) {
    echo "Deleting package " . $p->getId() . "...\n";
    $p->delete();
}

$template = TemplateEntity::getDefaultTemplate();
$storageIds = [StoragesUtil::getDefaultStorageId()];
$execInfo = new PackageExecutionInfo(AbstractPackage::EXECUTION_TYPE_MANUAL);

echo "Creating package...\n";
$package = PackageUtils::createBackgroundPackage($template, $storageIds, $execInfo, 'MPCBC Production Backup');
echo "Package ID: " . $package->getId() . " created.\n";

echo "Running fresh scanFiles(true)...\n";
$scanDone = $package->Archive->scanFiles(true);
while (!$scanDone) {
    $scanDone = $package->Archive->scanFiles(false);
}
echo "Scan complete! Files: " . $package->Archive->FileCount . ", Dirs: " . $package->Archive->DirCount . "\n";

echo "Creating scan report...\n";
$report = $package->createScanReport();
$package->logScanReport();

// Transition to START
$package->setStatus(AbstractPackage::STATUS_START);
$package->save();
echo "Package transitioned to STATUS_START (" . $package->getStatus() . "). Saved.\n";

echo "Now processing build chunks via Runner::process()...\n";
$iterations = 0;
while (DupPackage::isPackageRunning() && $iterations < 1000) {
    $iterations++;
    try {
        Runner::process();
    } catch (Throwable $e) {
        echo "Error in process(): " . $e->getMessage() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
        break;
    }

    $package = DupPackage::getById($package->getId());
    if ($iterations % 5 === 0 || $package->getStatus() != AbstractPackage::STATUS_ARCSTART) {
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

$finalPkg = DupPackage::getById($package->getId());
echo "\nFinal Details:\n";
echo "ID: " . $finalPkg->getId() . "\n";
echo "Status: " . $finalPkg->getStatus() . "\n";
echo "Archive File: " . $finalPkg->getArchiveFileName() . "\n";

$archivePath = DUPLICATOR_SSDIR_PATH . '/' . $finalPkg->getArchiveFileName();
echo "Archive Path: " . $archivePath . " (exists: " . (file_exists($archivePath) ? 'YES (' . round(filesize($archivePath) / 1024 / 1024, 2) . ' MB)' : 'NO') . ")\n";

// Look for installer file in DUPLICATOR_SSDIR_PATH
$installers = glob(DUPLICATOR_SSDIR_PATH . '/*installer*.php');
echo "Found installer files:\n";
foreach ($installers as $inst) {
    echo " - " . $inst . " (" . filesize($inst) . " bytes)\n";
}
