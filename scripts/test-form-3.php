<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;
$form = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fluentform_forms WHERE id = 3");
echo "Form 3 row:\n";
echo "ID: " . $form->id . "\n";
echo "Title: " . $form->title . "\n";
echo "Status: " . $form->status . "\n";
echo "Type: " . $form->type . "\n";
echo "form_fields length: " . strlen($form->form_fields) . "\n";
echo "form_fields:\n" . substr($form->form_fields, 0, 500) . "\n";

// Let's call fluentFormApi
if ( function_exists('wpFluentForm') ) {
    echo "wpFluentForm exists!\n";
    $app = wpFluentForm();
    $rendered = $app->view->make('public.form.form', ['form_id' => 3]);
    echo "Rendered length: " . strlen($rendered) . "\n";
} else {
    echo "wpFluentForm function not found.\n";
}
