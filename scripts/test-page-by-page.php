<?php
require 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$log_file = 'C:/Users/chris/Local Sites/mpcbc/logs/php/error.log';

$pages = [
    'http://mpcbc.local/'              => 'Home',
    'http://mpcbc.local/welcome/'      => 'Welcome',
    'http://mpcbc.local/prayer/'       => 'Prayer',
    'http://mpcbc.local/coming-soon/'  => 'Coming soon',
    'http://mpcbc.local/visit/'        => 'Visit',
    'http://mpcbc.local/sundays/'      => 'Sundays',
    'http://mpcbc.local/kids/'         => 'Kids',
    'http://mpcbc.local/watch/'        => 'Watch',
    'http://mpcbc.local/about/'        => 'About',
    'http://mpcbc.local/give/'         => 'Give',
    'http://mpcbc.local/zh/'           => 'ZH',
];

foreach ( $pages as $url => $label ) {
    file_put_contents( $log_file, '' );
    $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
    $content = file_get_contents( $url, false, $ctx );
    $errors = trim( file_get_contents( $log_file ) );
    if ( $errors !== '' ) {
        echo "[ERROR] {$label} ({$url}):\n{$errors}\n\n";
    } else {
        echo "[OK] {$label} ({$url}) - Clean!\n";
    }
}
