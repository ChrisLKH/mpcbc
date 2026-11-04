<?php
$html = file_get_contents('http://mpcbc-2.local/');
if (preg_match('/<header.*?<\/header>/s', $html, $m)) {
    echo $m[0] . "\n";
} else {
    echo "No header found\n";
}
