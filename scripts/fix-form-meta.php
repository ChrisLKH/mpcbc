<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;

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

foreach ([3, 4] as $form_id) {
    // Check if formSettings exists
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = %d AND meta_key = 'formSettings'", $form_id));
    if ($exists) {
        $wpdb->update("{$wpdb->prefix}fluentform_form_meta", ['value' => json_encode($formSettings)], ['id' => $exists]);
    } else {
        $wpdb->insert("{$wpdb->prefix}fluentform_form_meta", [
            'form_id' => $form_id,
            'meta_key' => 'formSettings',
            'value' => json_encode($formSettings),
        ]);
    }
}

echo "Added formSettings to forms 3 and 4.\n";

// Test rendering
global $shortcode_tags;
$out3 = call_user_func($shortcode_tags['fluentform'], ['id' => 3], '');
echo "Form 3 rendered length: " . strlen($out3) . "\n";

$out4 = call_user_func($shortcode_tags['fluentform'], ['id' => 4], '');
echo "Form 4 rendered length: " . strlen($out4) . "\n";
