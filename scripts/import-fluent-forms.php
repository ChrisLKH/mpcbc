<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit(1);
}

wp_set_current_user( 1 );

$prayer_json = json_decode( file_get_contents( __DIR__ . '/../fluent-forms/prayer-request-form.json' ), true );
$newsletter_json = json_decode( file_get_contents( __DIR__ . '/../fluent-forms/newsletter-form.json' ), true );

function import_ff( $formData ) {
    $item = $formData[0];
    $title = $item['title'];
    $fields = json_encode( $item['form_fields'] );

    // Check if form with same title already exists
    $existing = wpFluent()->table('fluentform_forms')->where('title', $title)->first();
    if ( $existing ) {
        wpFluent()->table('fluentform_forms')->where('id', $existing->id)->update([
            'form_fields' => $fields,
            'status'      => 'published',
            'updated_at'  => current_time( 'mysql' ),
        ]);
        echo "Updated existing form '$title' (ID: {$existing->id})\n";
        return $existing->id;
        $id = wpFluent()->table('fluentform_forms')->insertGetId([
            'title'       => $title,
            'form_fields' => $fields,
            'status'      => 'published',
            'created_by'  => 1,
            'created_at'  => current_time( 'mysql' ),
            'updated_at'  => current_time( 'mysql' ),
        ]);
        echo "Created new form '$title' (ID: $id)\n";
    }

    $formSettings = [
        'confirmation' => [
            'redirectTo' => 'samePage',
            'messageToShow' => 'Thank you for your message. We will get in touch with you shortly',
            'customPage' => null,
            'samePageFormBehavior' => 'hide_form',
            'customUrl' => null,
        ],
        'restrictions' => [
            'limitNumberOfEntries' => ['enabled' => false, 'numberOfEntries' => null, 'period' => 'total', 'limitReachedMsg' => 'Maximum number of entries exceeded.'],
            'scheduleForm' => ['enabled' => false, 'start' => null, 'end' => null, 'selectedDays' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'], 'pendingMsg' => 'Form submission is not started yet.', 'expiredMsg' => 'Form submission is now closed.'],
            'requireLogin' => ['enabled' => false, 'requireLoginMsg' => 'You must be logged in to submit the form.'],
            'denyEmptySubmission' => ['enabled' => false, 'message' => "Sorry, you cannot submit an empty form. Let's hear what you wanna say."],
        ],
        'layout' => [
            'labelPlacement' => 'top',
            'helpMessagePlacement' => 'with_label',
            'errorMessagePlacement' => 'inline',
            'cssClassName' => '',
            'asteriskPlacement' => 'asterisk-right',
        ],
        'delete_entry_on_submission' => 'no',
        'appendSurveyResult' => ['enabled' => false, 'showLabel' => false, 'showCount' => false],
    ];

    global $wpdb;
    $meta_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = %d AND meta_key = 'formSettings'", $existing ? $existing->id : $id));
    $target_id = $existing ? $existing->id : $id;
    if (!$meta_exists) {
        $wpdb->insert("{$wpdb->prefix}fluentform_form_meta", [
            'form_id' => $target_id,
            'meta_key' => 'formSettings',
            'value' => json_encode($formSettings),
        ]);
    }

    return $target_id;
}

$id1 = import_ff( $prayer_json );
$id2 = import_ff( $newsletter_json );
echo "Fluent Forms import complete. Form IDs: $id1, $id2\n";
