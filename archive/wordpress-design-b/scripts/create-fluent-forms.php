<?php
/**
 * Create Fluent Forms for Design B:
 * Form 1: Connect card
 * Form 2: Prayer request
 * Form 3: Weekly email / Newsletter
 */
global $wpdb;

function create_or_update_form($title, $fieldsArray, $submitBtnText) {
    global $wpdb;

    $formFields = [
        'fields' => $fieldsArray,
        'submitButton' => [
            'element' => 'button',
            'attributes' => [
                'type' => 'submit',
                'class' => 'ff-btn-submit'
            ],
            'settings' => [
                'container_class' => '',
                'align' => 'left',
                'button_style' => 'default',
                'button_size' => 'md',
                'btn_text' => $submitBtnText
            ],
            'editor_options' => [
                'title' => 'Submit Button'
            ]
        ]
    ];

    $existing = wpFluent()->table('fluentform_forms')->where('title', $title)->first();
    if ($existing) {
        wpFluent()->table('fluentform_forms')->where('id', $existing->id)->update([
            'form_fields' => json_encode($formFields),
            'status' => 'published',
            'updated_at' => current_time('mysql'),
        ]);
        echo "Updated form '$title' (ID: {$existing->id})\n";
        $form_id = $existing->id;
    } else {
        $form_id = wpFluent()->table('fluentform_forms')->insertGetId([
            'title' => $title,
            'form_fields' => json_encode($formFields),
            'status' => 'published',
            'created_by' => 1,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ]);
        echo "Created form '$title' (ID: $form_id)\n";
    }

    $formSettings = [
        'confirmation' => [
            'redirectTo' => 'samePage',
            'messageToShow' => 'Thank you! We have received your submission.',
            'samePageFormBehavior' => 'hide_form',
        ],
        'layout' => [
            'labelPlacement' => 'top',
            'helpMessagePlacement' => 'with_label',
            'errorMessagePlacement' => 'inline',
            'cssClassName' => '',
            'asteriskPlacement' => 'asterisk-right',
        ],
    ];

    $meta_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = %d AND meta_key = 'formSettings'", $form_id));
    if (!$meta_exists) {
        $wpdb->insert("{$wpdb->prefix}fluentform_form_meta", [
            'form_id' => $form_id,
            'meta_key' => 'formSettings',
            'value' => json_encode($formSettings),
        ]);
    } else {
        $wpdb->update("{$wpdb->prefix}fluentform_form_meta", [
            'value' => json_encode($formSettings),
        ], [
            'form_id' => $form_id,
            'meta_key' => 'formSettings',
        ]);
    }

    return $form_id;
}

// 1. Connect card
$connectFields = [
    [
        'element' => 'input_text',
        'attributes' => [
            'name' => 'names',
            'id' => 'v4-name',
            'type' => 'text',
            'placeholder' => '',
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Your name 姓名',
            'label_placement' => 'top',
            'validation_rules' => [
                'required' => ['value' => true, 'message' => 'Please enter your name']
            ]
        ],
        'editor_options' => ['title' => 'Name']
    ],
    [
        'element' => 'input_text',
        'attributes' => [
            'name' => 'contact',
            'id' => 'v4-contact',
            'type' => 'text',
            'placeholder' => '',
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Email or phone 電郵或電話',
            'label_placement' => 'top',
            'validation_rules' => [
                'required' => ['value' => true, 'message' => 'Please enter your contact information']
            ]
        ],
        'editor_options' => ['title' => 'Contact']
    ],
    [
        'element' => 'select',
        'attributes' => [
            'name' => 'service',
            'id' => 'v4-service',
            'placeholder' => 'Choose a service 請選擇',
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Which service will you visit?',
            'label_placement' => 'top',
            'placeholder' => 'Choose a service 請選擇',
            'advanced_options' => [
                ['label' => 'Cantonese 粵語 · 9:30', 'value' => 'Cantonese 粵語 · 9:30'],
                ['label' => 'English · 11:00', 'value' => 'English · 11:00'],
                ['label' => 'Mandarin 國語 · 11:30', 'value' => 'Mandarin 國語 · 11:30'],
            ],
        ],
        'options' => [
            'Cantonese 粵語 · 9:30' => 'Cantonese 粵語 · 9:30',
            'English · 11:00' => 'English · 11:00',
            'Mandarin 國語 · 11:30' => 'Mandarin 國語 · 11:30',
        ],
        'editor_options' => ['title' => 'Service']
    ],
    [
        'element' => 'textarea',
        'attributes' => [
            'name' => 'note',
            'id' => 'v4-note',
            'placeholder' => "Kids' ages, accessibility needs, a question…",
            'rows' => 3,
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Anything we should know? Optional',
            'label_placement' => 'top',
        ],
        'editor_options' => ['title' => 'Note']
    ]
];
$id1 = create_or_update_form('Connect card', $connectFields, 'Send my card');

// 2. Prayer request
$prayerFields = [
    [
        'element' => 'input_text',
        'attributes' => [
            'name' => 'names',
            'id' => 'v4-pname',
            'type' => 'text',
            'placeholder' => '',
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Your name (or leave blank to stay anonymous)',
            'label_placement' => 'top',
        ],
        'editor_options' => ['title' => 'Name']
    ],
    [
        'element' => 'textarea',
        'attributes' => [
            'name' => 'request',
            'id' => 'v4-request',
            'placeholder' => 'Write in Chinese or English — both are read.',
            'rows' => 5,
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'What can we pray for? 代禱事項',
            'label_placement' => 'top',
            'validation_rules' => [
                'required' => ['value' => true, 'message' => 'Please enter your prayer request']
            ]
        ],
        'editor_options' => ['title' => 'Prayer Request']
    ],
    [
        'element' => 'input_checkbox',
        'attributes' => [
            'name' => 'private',
            'id' => 'v4-private',
            'class' => ''
        ],
        'settings' => [
            'container_class' => '',
            'label' => '',
            'options' => [
                ['label' => 'Keep this between me and the pastors. Unchecked, it is shared with the Wednesday prayer meeting.', 'value' => 'yes']
            ]
        ],
        'editor_options' => ['title' => 'Private Checkbox']
    ]
];
$id2 = create_or_update_form('Prayer request', $prayerFields, 'Send my request');

// 3. Weekly email / Newsletter
$newsletterFields = [
    [
        'element' => 'input_email',
        'attributes' => [
            'name' => 'email',
            'id' => 'v3-email',
            'type' => 'email',
            'placeholder' => 'you@example.com',
            'class' => 'ff-el-form-control'
        ],
        'settings' => [
            'container_class' => '',
            'label' => 'Email address',
            'label_placement' => 'top',
            'validation_rules' => [
                'required' => ['value' => true, 'message' => 'Please enter your email address'],
                'email' => ['value' => true, 'message' => 'Please enter a valid email address']
            ]
        ],
        'editor_options' => ['title' => 'Email']
    ]
];
$id3 = create_or_update_form('Weekly email', $newsletterFields, 'Sign up');

echo "Fluent forms created successfully:\n";
echo "Connect card: [fluentform id='$id1']\n";
echo "Prayer request: [fluentform id='$id2']\n";
echo "Weekly email: [fluentform id='$id3']\n";
