<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Models\TemplateEntity;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Package\PackageExecutionInfo;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\DupPackage;
use Duplicator\Package\Runner;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Lock\LockUtil;

LockUtil::unlockProcess();

$template = TemplateEntity::getDefaultTemplate();
echo "Default template: " . $template->name . " (ID: " . $template->getId() . ")\n";

$storageIds = [LocalStorage::DEFAULT_STORAGE_ID];
echo "Storage IDs: " . implode(', ', $storageIds) . "\n";

$execInfo = PackageExecutionInfo::createManual();
echo "Exec info created.\n";

$pkg = PackageUtils::createBackgroundPackage($template, $storageIds, $execInfo, 'MPCBC Mockup Export');
echo "Package created with ID: " . $pkg->getId() . " and name: " . $pkg->getName() . "\n";
echo "Status: " . $pkg->getStatus() . "\n";
