<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Scan\ScanIterator;

$paths = WpArchiveUtils::getArchiveListPaths();
unset($paths['wpconfig']);
echo "Paths for common path:\n";
print_r($paths);
echo "Common Path: " . var_export(SnapIO::getCommonPath($paths), true) . "\n";

$root = WpArchiveUtils::getTargetRootPath();
$scan = new ScanIterator('C:/Users/chris/Local Sites/mpcbc/app/public');
$firstNode = null;
foreach ($scan as $node) {
    if (is_file($node->getPath())) {
        $firstNode = $node;
        break;
    }
}

echo "Root: " . var_export($root, true) . "\n";
echo "Node Path: " . var_export($firstNode->getPath(), true) . "\n";
echo "RelPath: " . var_export(SnapIO::getRelativePath($firstNode->getPath(), $root), true) . "\n";

FileIndexManager::setRootPathMap($root);
$reflector = new ReflectionClass(FileIndexManager::class);
$method = $reflector->getMethod('beforeWrite');
$method->setAccessible(true);

$mgr = new FileIndexManager(DUPLICATOR_SSDIR_PATH_TMP . '/test_index.txt', true);
$result = $method->invoke($mgr, ['path' => $firstNode->getPath()]);
echo "beforeWrite result: " . var_export($result, true) . "\n";
