<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$ref = new ReflectionClass('Duplicator\Package\DupPackage');
echo "DupPackage methods:\n";
foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
    if ($m->isStatic()) {
        echo "static " . $m->getName() . "()\n";
    }
}
