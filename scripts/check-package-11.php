<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Package\DupPackage;
use Duplicator\Package\AbstractPackage;

$p = DupPackage::getById(11);
echo "Package ID: " . $p->getId() . "\n";
echo "Name: " . $p->getName() . "\n";
echo "Status: " . $p->getStatus() . " (STATUS_COMPLETE=" . AbstractPackage::STATUS_COMPLETE . ")\n";
echo "Archive File: " . $p->getArchiveFileName() . "\n";
echo "Archive Size: " . round($p->Archive->Size / 1024 / 1024, 2) . " MB\n";
echo "Dir count: " . $p->Archive->DirCount . "\n";
echo "File count: " . $p->Archive->FileCount . "\n";
