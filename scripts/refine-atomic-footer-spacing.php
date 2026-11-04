<?php
// Local invocation of Elementor's official MCP editing ability.
try {
    if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new RuntimeException('Design A only.');
    $admins = get_users(['role' => 'administrator', 'number' => 1]);
    wp_set_current_user($admins[0]->ID);
    $root = 'C:/Projects/mpcbc';
    $id = 16;
    $before = json_decode(get_post_meta($id, '_elementor_data', true), true);
    $settings = get_post_meta($id, '_elementor_page_settings', true) ?: [];
    $template = get_post_meta($id, '_wp_page_template', true);
    $backup = "$root/content-source/before-footer-spacing";
    wp_mkdir_p($backup);
    if (!file_exists("$backup/16.json")) file_put_contents("$backup/16.json", wp_json_encode(['data'=>$before,'settings'=>$settings,'template'=>$template], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    $operations = [];
    $walk = function($nodes) use (&$walk, &$operations) {
        foreach ($nodes as $node) {
            $cssid = $node['settings']['_cssid']['value'] ?? '';
            $style = '';
            if ($cssid === 'mpcbc-mpcbc_footer_root') $style = 'padding:64px 56px 40px;@media (--tablet){padding:56px 32px 32px;}@media (--mobile){padding:40px 24px 28px;}';
            if ($cssid === 'mpcbc-mpcbc_footer_grid' || ($node['id'] ?? '') === '198512d6') $style = 'gap:40px;align-items:flex-start;@media (--tablet){gap:32px;}@media (--mobile){gap:32px;flex-direction:column;}';
            if (preg_match('/^mpcbc-mpcbc_footer_col[1-4]$/', $cssid)) $style = 'gap:10px;@media (--mobile){width:100%;}';
            if (preg_match('/^mpcbc-mpcbc_foot_(sundays|explore|contact)_h$/', $cssid)) $style = 'margin:0 0 4px 0;line-height:1.5;';
            if (preg_match('/^mpcbc-mpcbc_foot_explore_\d+$/', $cssid)) $style = 'line-height:1.6;';
            if ($cssid === 'mpcbc-mpcbc_foot_address_street') $style = 'margin:6px 0 0 0;';
            if ($cssid === 'mpcbc-mpcbc_footer_bottom_bar') $style = 'margin:40px 0 0 0;padding:24px 0 0 0;@media (--mobile){margin:32px 0 0 0;padding:20px 0 0 0;}';
            if (($node['id'] ?? '') === '7f9cb23d') $style = '@media (--mobile){flex-direction:column;align-items:flex-start;gap:12px;}';
            if ($style) $operations[] = ['action'=>'update','element_id'=>$node['id'],'style'=>$style];
            $walk($node['elements'] ?? []);
        }
    };
    $walk($before);
    if (count($operations) !== 19) throw new RuntimeException('Unexpected footer structure: '.count($operations));
    $result = (new Elementor\Modules\Mcp\Abilities\Manage_Elements_Ability())->execute(['post_id'=>$id,'operations'=>$operations]);
    if (is_wp_error($result) || ($result['status'] ?? '') !== 'ok') throw new RuntimeException(wp_json_encode($result));
    $document = Elementor\Plugin::$instance->documents->get($id);
    $draft = $document->get_autosave();
    if (!$draft) throw new RuntimeException('MCP draft missing.');
    $reviewed = json_decode(get_post_meta($draft->get_id(), '_elementor_data', true), true);
    // Every non-style field, including text, URLs and IDs, must remain identical.
    $stripStyles = function($nodes) use (&$stripStyles) {
        foreach ($nodes as &$node) { unset($node['styles']); $node['elements'] = $stripStyles($node['elements'] ?? []); }
        return $nodes;
    };
    if ($stripStyles($before) !== $stripStyles($reviewed)) throw new RuntimeException('Unexpected non-style change; draft not applied.');
    if (!$document->save(['elements'=>$reviewed,'settings'=>$settings])) throw new RuntimeException('Could not apply reviewed spacing draft.');
    if ($template) update_post_meta($id, '_wp_page_template', $template);
    $actual = json_decode(get_post_meta($id, '_elementor_data', true), true);
    $exportPath = "$root/elementor/footer-content-filled.json";
    $export = json_decode(file_get_contents($exportPath), true);
    $export['content'] = $actual;
    file_put_contents($exportPath, wp_json_encode($export, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    file_put_contents("$root/content-source/atomic-compiled/16.json", wp_json_encode($actual, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    file_put_contents("$root/content-source/atomic-footer-spacing-mcp.json", wp_json_encode(['method'=>'local official Elementor MCP ability','operations'=>$operations,'result'=>$result,'non_style_fields_preserved'=>true], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    Elementor\Plugin::$instance->files_manager->clear_cache();
    echo 'Footer spacing applied through official MCP ability; '.count($operations).' style edits, content preserved.';
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage()); exit(1); }
