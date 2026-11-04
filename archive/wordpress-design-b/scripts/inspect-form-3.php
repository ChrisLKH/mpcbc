<?php
global $wpdb;
$forms = $wpdb->get_results("SELECT id, title, form_fields FROM {$wpdb->prefix}fluentform_forms");
foreach ($forms as $f) {
    echo "Form ID: {$f->id}, Title: {$f->title}\n";
    if ($f->id == 3 || $f->title === 'Connect card') {
        echo "Fields:\n" . $f->form_fields . "\n\n";
    }
}
