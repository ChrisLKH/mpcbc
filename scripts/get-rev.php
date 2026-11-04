<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$rev = get_post_meta(77, '_elementor_data', true);
if ($rev) {
    echo "Found revision 77\n";
    file_put_contents('C:/Projects/mpcbc/wordpress/scripts/welcome-rev77.json', $rev);
} else {
    echo "Revision 77 not found\n";
}
