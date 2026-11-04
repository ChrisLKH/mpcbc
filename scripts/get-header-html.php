<?php
$html = file_get_contents('http://mpcbc.local/prayer/');
if (preg_match('/<header.*<\/header>/sU', $html, $m)) {
    echo $m[0] . "\n";
} else {
    echo "No header found\n";
}
