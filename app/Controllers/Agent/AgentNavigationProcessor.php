<?php
/**
 * Agent Navigation & Menu / Footer Processor (MCP API Layer)
 */

namespace Controllers\Agent;

use Controllers\AdminNavigationController;
use Models\AgentToken;

class AgentNavigationProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasNavRead = in_array('admin', $scopes) || in_array('navigation:read', $scopes) || in_array('brand:read', $scopes) || in_array('settings:read', $scopes);
        $hasNavWrite = in_array('admin', $scopes) || in_array('navigation:write', $scopes) || in_array('brand:write', $scopes) || in_array('settings:write', $scopes);

        switch ($action) {
            case 'get_navigation':
            case 'navigation':
            case 'get_menus':
                if (!$hasNavRead) $this->forbidden('Missing scope [navigation:read]');
                $nav = AdminNavigationController::getNavigationSettings();
                $this->success($nav, 'Navigation and footer settings retrieved successfully.');
                break;

            case 'update_navigation':
            case 'update_menus':
                if (!$hasNavWrite) $this->forbidden('Missing scope [navigation:write]');
                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object containing navigation data.');
                }

                $updated = [];

                // 1. Update Header Nav items if provided
                if (isset($input['nav_items']) && is_array($input['nav_items'])) {
                    $validItems = [];
                    foreach ($input['nav_items'] as $item) {
                        $label = trim($item['label'] ?? '');
                        $url = trim($item['url'] ?? '');
                        if ($label !== '' && $url !== '') {
                            $validItems[] = [
                                'label' => $label,
                                'url' => $url,
                                'target' => ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self',
                                'is_active' => !isset($item['is_active']) || (bool)$item['is_active']
                            ];
                        }
                    }
                    updateSetting('nav_menu_items', json_encode($validItems, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['nav_items'] = $validItems;
                }

                // 2. Update Footer Column 2 if provided
                if (isset($input['footer_col2']) && is_array($input['footer_col2'])) {
                    $col2Title = trim($input['footer_col2']['title'] ?? 'Chuyên Mục');
                    $col2Links = [];
                    foreach ($input['footer_col2']['links'] ?? [] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $col2Links[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    $footerCol2 = ['title' => $col2Title, 'links' => $col2Links];
                    updateSetting('footer_col2_json', json_encode($footerCol2, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['footer_col2'] = $footerCol2;
                }

                // 3. Update Footer Column 3 if provided
                if (isset($input['footer_col3']) && is_array($input['footer_col3'])) {
                    $col3Title = trim($input['footer_col3']['title'] ?? 'Thông Tin & Chính Sách');
                    $col3Links = [];
                    foreach ($input['footer_col3']['links'] ?? [] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $col3Links[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    $footerCol3 = ['title' => $col3Title, 'links' => $col3Links];
                    updateSetting('footer_col3_json', json_encode($footerCol3, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['footer_col3'] = $footerCol3;
                }

                // 4. Update Bottom Links if provided
                if (isset($input['bottom_links']) && is_array($input['bottom_links'])) {
                    $bottomLinks = [];
                    foreach ($input['bottom_links'] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $bottomLinks[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    updateSetting('footer_bottom_links', json_encode($bottomLinks, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['bottom_links'] = $bottomLinks;
                }

                if (empty($updated)) {
                    $this->error('No valid navigation fields provided for update.');
                }

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_navigation', 'settings', null, [], $updated);

                $this->success([
                    'updated' => $updated,
                    'current_navigation' => AdminNavigationController::getNavigationSettings()
                ], 'Navigation settings updated successfully.');
                break;

            case 'get_footer':
            case 'footer':
                if (!$hasNavRead) $this->forbidden('Missing scope [navigation:read] or [brand:read]');
                $nav = AdminNavigationController::getNavigationSettings();
                $this->success([
                    'footer_about_text' => getSetting('footer_about_text', ''),
                    'footer_copyright' => getSetting('footer_copyright', ''),
                    'footer_col2' => $nav['footer_col2'],
                    'footer_col3' => $nav['footer_col3'],
                    'bottom_links' => $nav['bottom_links']
                ], 'Footer configuration retrieved successfully.');
                break;

            case 'update_footer':
                if (!$hasNavWrite) $this->forbidden('Missing scope [navigation:write] or [brand:write]');
                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object containing footer fields.');
                }

                $updated = [];
                if (isset($input['footer_about_text'])) {
                    updateSetting('footer_about_text', trim($input['footer_about_text']), 'text');
                    $updated['footer_about_text'] = trim($input['footer_about_text']);
                }
                if (isset($input['footer_copyright'])) {
                    updateSetting('footer_copyright', trim($input['footer_copyright']), 'text');
                    $updated['footer_copyright'] = trim($input['footer_copyright']);
                }
                if (isset($input['footer_col2']) && is_array($input['footer_col2'])) {
                    $col2Title = trim($input['footer_col2']['title'] ?? 'Chuyên Mục');
                    $col2Links = [];
                    foreach ($input['footer_col2']['links'] ?? [] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $col2Links[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    $footerCol2 = ['title' => $col2Title, 'links' => $col2Links];
                    updateSetting('footer_col2_json', json_encode($footerCol2, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['footer_col2'] = $footerCol2;
                }
                if (isset($input['footer_col3']) && is_array($input['footer_col3'])) {
                    $col3Title = trim($input['footer_col3']['title'] ?? 'Thông Tin & Chính Sách');
                    $col3Links = [];
                    foreach ($input['footer_col3']['links'] ?? [] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $col3Links[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    $footerCol3 = ['title' => $col3Title, 'links' => $col3Links];
                    updateSetting('footer_col3_json', json_encode($footerCol3, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['footer_col3'] = $footerCol3;
                }
                if (isset($input['bottom_links']) && is_array($input['bottom_links'])) {
                    $bottomLinks = [];
                    foreach ($input['bottom_links'] as $lnk) {
                        $lbl = trim($lnk['label'] ?? '');
                        $u = trim($lnk['url'] ?? '');
                        if ($lbl !== '' && $u !== '') {
                            $bottomLinks[] = ['label' => $lbl, 'url' => $u];
                        }
                    }
                    updateSetting('footer_bottom_links', json_encode($bottomLinks, JSON_UNESCAPED_UNICODE), 'json');
                    $updated['bottom_links'] = $bottomLinks;
                }

                if (empty($updated)) {
                    $this->error('No valid footer fields provided for update.');
                }

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_footer', 'settings', null, [], $updated);

                $this->success([
                    'updated' => $updated
                ], 'Footer settings updated successfully.');
                break;

            default:
                $this->error('Unsupported navigation action.');
        }
    }

    private function success($data, $message = 'Success') {
        echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error($message, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function forbidden($message) {
        $this->error("Forbidden: {$message}", 403);
    }
}
