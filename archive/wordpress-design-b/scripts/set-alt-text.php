<?php
$alts = [
    7  => 'Monterey Park Chinese Baptist Church',
    8  => 'Monterey Park Chinese Baptist Church building',
    9  => 'Church sign',
    10 => 'Rev. Peter Chan, Senior Pastor',
    11 => 'Rev. Peter Chan, Senior Pastor',
    12 => 'Rev. David Lam, Pastor',
    13 => 'Rev. Grace Wang, Pastor',
    14 => '',
    15 => '',
    16 => '',
    17 => 'Sunday school room',
    18 => 'Craft table',
    19 => 'Nursery corner',
    62 => 'Monterey Park Chinese Baptist Church sanctuary',
];

foreach ($alts as $id => $alt) {
    if ($alt === '') {
        delete_post_meta($id, '_wp_attachment_image_alt');
    } else {
        update_post_meta($id, '_wp_attachment_image_alt', $alt);
    }
    echo "Attachment $id alt set to: '" . get_post_meta($id, '_wp_attachment_image_alt', true) . "'\n";
}
