<?php
require 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

// Clear error.log
$log_file = 'C:/Users/chris/Local Sites/mpcbc/logs/php/error.log';
file_put_contents( $log_file, '' );

// Regenerate CSS / clear cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Elementor CSS cache cleared.\n";

// Load every page
$pages = [
    'http://mpcbc.local/',
    'http://mpcbc.local/welcome/',
    'http://mpcbc.local/prayer/',
    'http://mpcbc.local/coming-soon/',
    'http://mpcbc.local/visit/',
    'http://mpcbc.local/sundays/',
    'http://mpcbc.local/kids/',
    'http://mpcbc.local/watch/',
    'http://mpcbc.local/about/',
    'http://mpcbc.local/give/',
    'http://mpcbc.local/zh/',
];

foreach ( $pages as $url ) {
    $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
    $content = file_get_contents( $url, false, $ctx );
    echo "Fetched $url (" . strlen($content) . " bytes)\n";
}

$errors = file_get_contents( $log_file );
if ( trim( $errors ) === '' ) {
    echo "\nSUCCESS: logs/php/error.log is completely CLEAN!\n";
} else {
    echo "\nWARNINGS/ERRORS in logs/php/error.log:\n$errors\n";
}
