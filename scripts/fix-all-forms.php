<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;

$prayer_fields = [
    "fields" => [
        [
            "element" => "textarea",
            "attributes" => [
                "name" => "message",
                "id" => "pr-text",
                "class" => "field-prayer-text",
                "placeholder" => "Type your prayer request here...",
                "rows" => 6,
                "cols" => 50
            ],
            "settings" => [
                "container_class" => "field-wrap",
                "label" => "Your prayer request",
                "label_placement" => "top",
                "validation_rules" => [
                    "required" => [
                        "value" => true,
                        "message" => "Please enter your prayer request."
                    ]
                ]
            ],
            "editor_options" => [
                "title" => "Textarea Field"
            ]
        ],
        [
            "element" => "container",
            "attributes" => [
                "class" => ""
            ],
            "settings" => [
                "container_class" => "field-wrap"
            ],
            "columns" => [
                [
                    "width" => 50,
                    "fields" => [
                        [
                            "element" => "input_text",
                            "attributes" => [
                                "name" => "names",
                                "id" => "pr-name",
                                "type" => "text",
                                "placeholder" => "Your name"
                            ],
                            "settings" => [
                                "container_class" => "field-wrap",
                                "label" => "Name (optional)",
                                "label_placement" => "top"
                            ],
                            "editor_options" => [
                                "title" => "Name"
                            ]
                        ]
                    ]
                ],
                [
                    "width" => 50,
                    "fields" => [
                        [
                            "element" => "input_text",
                            "attributes" => [
                                "name" => "email_or_phone",
                                "id" => "pr-contact",
                                "type" => "text",
                                "placeholder" => "you@example.com or phone"
                            ],
                            "settings" => [
                                "container_class" => "field-wrap",
                                "label" => "Email or phone (optional)",
                                "label_placement" => "top"
                            ],
                            "editor_options" => [
                                "title" => "Contact info"
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            "element" => "input_radio",
            "attributes" => [
                "name" => "who_may_see",
                "id" => "pr-who",
                "type" => "radio",
                "value" => "pastors"
            ],
            "settings" => [
                "container_class" => "field-wrap",
                "label" => "Who may see this?",
                "label_placement" => "top",
                "advanced_options" => [
                    [
                        "label" => "Pastors only",
                        "value" => "pastors"
                    ],
                    [
                        "label" => "Pastors and the prayer team",
                        "value" => "team"
                    ]
                ]
            ],
            "options" => [
                "pastors" => "Pastors only",
                "team" => "Pastors and the prayer team"
            ],
            "editor_options" => [
                "title" => "Radio Field"
            ]
        ],
        [
            "element" => "input_checkbox",
            "attributes" => [
                "name" => "contact_me",
                "id" => "pr-contact-me",
                "type" => "checkbox"
            ],
            "settings" => [
                "container_class" => "field-wrap",
                "label" => "",
                "advanced_options" => [
                    [
                        "label" => "I’d like someone to contact me",
                        "value" => "yes"
                    ]
                ]
            ],
            "options" => [
                "yes" => "I’d like someone to contact me"
            ],
            "editor_options" => [
                "title" => "Checkbox Field"
            ]
        ]
    ],
    "submitButton" => [
        "element" => "button",
        "attributes" => [
            "type" => "submit",
            "class" => "ff-btn-submit btn-p"
        ],
        "settings" => [
            "container_class" => "",
            "background_color" => "#0E6B5C",
            "color" => "#ffffff",
            "button_ui" => [
                "type" => "default",
                "text" => "Send prayer request"
            ]
        ],
        "editor_options" => [
            "title" => "Submit Button"
        ]
    ]
];

$newsletter_fields = [
    "fields" => [
        [
            "element" => "input_email",
            "attributes" => [
                "name" => "email",
                "id" => "c-email",
                "type" => "email",
                "placeholder" => "you@example.com"
            ],
            "settings" => [
                "container_class" => "field-wrap-email",
                "label" => "Get the weekly email",
                "label_placement" => "top",
                "validation_rules" => [
                    "required" => [
                        "value" => true,
                        "message" => "Please enter your email."
                    ],
                    "email" => [
                        "value" => true,
                        "message" => "Please enter a valid email."
                    ]
                ]
            ],
            "editor_options" => [
                "title" => "Email Field"
            ]
        ]
    ],
    "submitButton" => [
        "element" => "button",
        "attributes" => [
            "type" => "submit",
            "class" => "ff-btn-submit btn-o"
        ],
        "settings" => [
            "container_class" => "",
            "background_color" => "transparent",
            "color" => "#16140F",
            "button_ui" => [
                "type" => "default",
                "text" => "Subscribe"
            ]
        ],
        "editor_options" => [
            "title" => "Submit Button"
        ]
    ]
];

// Update database
$wpdb->update("{$wpdb->prefix}fluentform_forms", ['form_fields' => json_encode($prayer_fields)], ['id' => 3]);
$wpdb->update("{$wpdb->prefix}fluentform_forms", ['form_fields' => json_encode($newsletter_fields)], ['id' => 4]);

// Also save to fluent-forms JSON files
file_put_contents(__DIR__ . '/../fluent-forms/prayer-request-form.json', json_encode([['title' => 'Prayer request', 'status' => 'published', 'appearance_settings' => [], 'form_fields' => $prayer_fields]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__ . '/../fluent-forms/newsletter-form.json', json_encode([['title' => 'Stay in the loop (Weekly Email)', 'status' => 'published', 'appearance_settings' => [], 'form_fields' => $newsletter_fields]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

echo "Updated forms 3 and 4.\n";

// Render Form 3
global $shortcode_tags;
$html3 = call_user_func($shortcode_tags['fluentform'], ['id' => 3], '');
echo "Form 3 HTML length: " . strlen($html3) . "\n";
echo "Has 'pr-name': " . (strpos($html3, 'pr-name') !== false ? "YES" : "NO") . "\n";
echo "Has 'pr-contact': " . (strpos($html3, 'pr-contact') !== false ? "YES" : "NO") . "\n";
echo "Has 'pr-who': " . (strpos($html3, 'pr-who') !== false ? "YES" : "NO") . "\n";
echo "Has 'pr-contact-me': " . (strpos($html3, 'pr-contact-me') !== false ? "YES" : "NO") . "\n";
echo "Has 'Send prayer request': " . (strpos($html3, 'Send prayer request') !== false ? "YES" : "NO") . "\n";
