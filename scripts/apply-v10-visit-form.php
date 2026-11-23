<?php
// Workbook section 1.7 / 4: one contact form on the Visit page (/visit/#contact) — Name, Email or phone, Preferred
// language, Preferred reply, Reason, Message — that pre-selects Reason from the URL (?reason=visit|prayer|baptism|question)
// with Fluent Forms' native Dynamic Default Value {get.reason}. Created as a copy of form 5 (its settings and
// notifications), exported to fluent-forms/visit-prayer-form.json, and placed in a new #contact section on page 60.
// Run with WP-CLI eval-file. Page 60 is only changed while it matches content-source/before-v10/60.json (or was written here).
try {
 if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
 $a = get_users(['role' => 'administrator', 'number' => 1]); wp_set_current_user($a[0]->ID);
 global $wpdb;
 $root = 'C:/Projects/mpcbc';
 $title = 'Plan a visit & prayer (v10)';
 $forms = $wpdb->prefix . 'fluentform_forms';
 $meta = $wpdb->prefix . 'fluentform_form_meta';

 $text = fn($name, $label, $required) => ['element' => 'input_text', 'attributes' => ['name' => $name, 'type' => 'text', 'value' => '', 'placeholder' => '', 'class' => ''],
  'settings' => ['container_class' => '', 'label' => $label, 'label_placement' => 'top', 'admin_field_label' => '', 'help_message' => '', 'conditional_logics' => [],
   'validation_rules' => ['required' => ['value' => $required, 'message' => 'This field is required. 此欄必填。']]], 'editor_options' => ['title' => 'Simple Text', 'template' => 'inputText']];
 $radio = fn($name, $label, $options, $default = '') => ['element' => 'input_radio', 'attributes' => ['type' => 'radio', 'name' => $name, 'value' => ''],
  'settings' => ['dynamic_default_value' => $default, 'container_class' => '', 'label' => $label, 'admin_field_label' => '', 'label_placement' => 'top', 'display_type' => 'inline', 'help_message' => '',
   'advanced_options' => array_map(fn($v, $l) => ['label' => $l, 'value' => $v, 'calc_value' => '', 'image' => ''], array_keys($options), $options),
   'randomize_options' => 'no', 'enable_other_option' => 'no', 'conditional_logics' => [], 'layout_class' => '',
   'validation_rules' => ['required' => ['value' => $name === 'reason', 'message' => 'Please choose one. 請選擇一項。']]],
  'editor_options' => ['title' => 'Radio Field', 'element' => 'input-radio', 'template' => 'inputCheckable']];
 $fields = ['fields' => [
  $text('names', 'Name 姓名', true),
  $text('email_or_phone', 'Email or phone 電郵或電話', true),
  $radio('preferred_language', 'Preferred language 慣用語言', ['english' => 'English', 'cantonese' => '粵語 Cantonese', 'mandarin' => '國語 Mandarin']),
  $radio('preferred_reply', 'Preferred reply 希望的回覆方式', ['email' => 'Email 電郵', 'phone' => 'Phone 電話', 'text' => 'Text 短訊']),
  $radio('reason', 'Reason 事由', ['visit' => 'Plan a visit 計劃來訪', 'prayer' => 'Prayer request 代禱', 'baptism' => 'Baptism or membership 浸禮或入會', 'question' => 'Question 查詢'], '{get.reason}'),
  ['element' => 'textarea', 'attributes' => ['name' => 'message', 'value' => '', 'rows' => 5, 'cols' => 2, 'placeholder' => '', 'class' => ''],
   'settings' => ['container_class' => '', 'label' => 'Message 訊息', 'label_placement' => 'top', 'admin_field_label' => '', 'help_message' => '', 'conditional_logics' => [], 'validation_rules' => ['required' => ['value' => false, 'message' => '']]],
   'editor_options' => ['title' => 'Text Area', 'template' => 'inputTextarea']],
  ['element' => 'custom_html', 'attributes' => [], 'settings' => ['html_codes' => '<p>We only use your details to reply to you. 我們只會用您的資料回覆您。</p>', 'conditional_logics' => [], 'container_class' => ''],
   'editor_options' => ['title' => 'Custom HTML', 'template' => 'customHTML']],
 ]];
 $src = $wpdb->get_row("SELECT * FROM $forms WHERE id = 5", ARRAY_A);
 $fields['submitButton'] = json_decode($src['form_fields'], true)['submitButton'];
 $fields['submitButton']['settings']['button_ui']['text'] = 'Send 送出';
 foreach (['backgroundColor' => 'var(--e-global-color-primary)', 'borderRadius' => '999', 'minHeight' => '52px', 'fontWeight' => '500'] as $k => $v) $fields['submitButton']['settings']['normal_styles'][$k] = $v;
 $fields['submitButton']['settings']['hover_styles']['backgroundColor'] = 'var(--e-global-color-harbor_deep)';

 $id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM $forms WHERE title = %s", $title));
 if (!$id) {
  $row = $src; unset($row['id']);
  $row['title'] = $title; $row['created_at'] = $row['updated_at'] = current_time('mysql');
  $row['form_fields'] = wp_json_encode($fields, JSON_UNESCAPED_UNICODE);
  $wpdb->insert($forms, $row);
  $id = (int) $wpdb->insert_id;
  foreach ($wpdb->get_results("SELECT meta_key, value FROM $meta WHERE form_id = 5", ARRAY_A) as $m) $wpdb->insert($meta, ['form_id' => $id, 'meta_key' => $m['meta_key'], 'value' => $m['value']]);
 } else {
  $wpdb->update($forms, ['form_fields' => wp_json_encode($fields, JSON_UNESCAPED_UNICODE), 'updated_at' => current_time('mysql')], ['id' => $id]);
 }
 file_put_contents("$root/fluent-forms/visit-prayer-form.json", wp_json_encode(['id' => $id, 'title' => $title, 'form_fields' => $fields], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

 // page 60: keep its content, add the #contact section after it
 $backup = json_decode(file_get_contents("$root/content-source/before-v10/60.json"), true);
 $live = json_decode(get_post_meta(60, '_elementor_data', true), true);
 if ($live != $backup['meta']['_elementor_data'] && !get_post_meta(60, '_mpcbc_v10_visit', true)) throw new RuntimeException('Visit page changed since the backup.');
 $section = json_decode(file_get_contents("$root/content-source/v10-visit-contact.json"), true, 512, JSON_THROW_ON_ERROR);
 $section['elements'][0]['elements'][] = ['id' => substr(md5('visit-form'), 0, 8), 'elType' => 'widget', 'widgetType' => 'shortcode', 'settings' => ['shortcode' => "[fluentform id=\"$id\"]"], 'elements' => []];
 $elements = array_merge($backup['meta']['_elementor_data'], [$section]);
 if (!Elementor\Plugin::$instance->documents->get(60)->save(['elements' => $elements, 'settings' => $backup['meta']['_elementor_page_settings'] ?: []])) throw new RuntimeException('Visit save failed.');
 update_post_meta(60, '_mpcbc_v10_visit', 1);
 Elementor\Plugin::$instance->files_manager->clear_cache();
 echo "form $id; Visit page 60 has the #contact section\n";
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
