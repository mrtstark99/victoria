<?php
/**
 * Agent Brand & Default SEO Settings Processor
 */

namespace Controllers\Agent;

use Controllers\AdminBrandController;
use Models\AgentToken;

class AgentBrandProcessor {
    public function process($action, $scopes, $agent, $input) {
        $hasBrandRead = in_array('admin', $scopes) || in_array('brand:read', $scopes) || in_array('settings:read', $scopes);
        $hasBrandWrite = in_array('admin', $scopes) || in_array('brand:write', $scopes) || in_array('settings:write', $scopes);
        $hasSeoRead = in_array('admin', $scopes) || in_array('seo:read', $scopes) || in_array('brand:read', $scopes);
        $hasSeoWrite = in_array('admin', $scopes) || in_array('seo:write', $scopes) || in_array('brand:write', $scopes);

        switch ($action) {
            case 'brand':
            case 'get_brand':
                if (!$hasBrandRead) $this->forbidden('Missing scope [brand:read]');
                $settings = AdminBrandController::getBrandSettings();
                $settings['chat_facebook_url'] = getSetting('chat_facebook_url', '');
                $settings['zalo_url'] = getSetting('zalo_url', '');
                $this->success($settings, 'Brand settings retrieved successfully.');
                break;

            case 'update_brand':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!$hasBrandWrite) $this->forbidden('Missing scope [brand:write]');
                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object containing settings to update.');
                }

                $allowedFields = [
                    'site_name',
                    'site_slogan',
                    'site_logo_display_mode',
                    'site_logo_badge',
                    'site_logo_url',
                    'site_favicon_url',
                    'site_email',
                    'site_phone',
                    'site_address',
                    'chat_facebook_url',
                    'zalo_url',
                    'social_facebook',
                    'social_twitter',
                    'social_github',
                    'social_linkedin',
                    'social_youtube',
                    'social_tiktok',
                    'default_meta_description',
                    'default_meta_keywords',
                    'default_og_image',
                    'footer_about_text',
                    'footer_copyright',
                ];

                $updated = [];
                foreach ($input as $key => $value) {
                    if (in_array($key, $allowedFields, true)) {
                        $valStr = is_string($value) ? trim($value) : (string)$value;
                        if ($key === 'site_logo_display_mode' && !in_array($valStr, ['logo_and_text', 'logo_only', 'text_only'], true)) {
                            continue;
                        }
                        if (in_array($key, ['chat_facebook_url', 'zalo_url'], true) && $valStr !== '') {
                            $scheme = strtolower((string)parse_url($valStr, PHP_URL_SCHEME));
                            if (!filter_var($valStr, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                                $this->error($key . ' must be a full HTTP or HTTPS URL.');
                            }
                        }
                        $updated[$key] = $valStr;
                    }
                }

                if (empty($updated)) {
                    $this->error('No valid brand settings provided for update.');
                }

                foreach ($updated as $key => $valStr) updateSetting($key, $valStr, 'text');

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_brand', 'settings', null, [], $updated);
                $currentSettings = AdminBrandController::getBrandSettings();
                $currentSettings['chat_facebook_url'] = getSetting('chat_facebook_url', '');
                $currentSettings['zalo_url'] = getSetting('zalo_url', '');
                $this->success([
                    'updated_fields' => $updated,
                    'current_settings' => $currentSettings
                ], 'Brand settings updated successfully.');
                break;

            case 'default_seo':
            case 'get_default_seo':
                if (!$hasSeoRead) $this->forbidden('Missing scope [seo:read] or [brand:read]');
                $data = [
                    'site_name' => getSetting('site_name', SITE_NAME),
                    'site_slogan' => getSetting('site_slogan', SITE_SLOGAN),
                    'default_meta_description' => getSetting('default_meta_description', ''),
                    'default_meta_keywords' => getSetting('default_meta_keywords', ''),
                    'default_og_image' => getSetting('default_og_image', ''),
                    'ga_id' => getSetting('ga_id', ''),
                    'gsc_verification' => getSetting('gsc_verification', '')
                ];
                $this->success($data, 'Default SEO & Open Graph settings retrieved successfully.');
                break;

            case 'update_default_seo':
                if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') $this->error('Method not allowed.', 405);
                if (!$hasSeoWrite) $this->forbidden('Missing scope [seo:write] or [brand:write]');
                if (empty($input) || !is_array($input)) {
                    $this->error('Payload must be a JSON object containing SEO settings.');
                }

                $allowedSeo = [
                    'default_meta_description',
                    'default_meta_keywords',
                    'default_og_image',
                    'ga_id',
                    'gsc_verification'
                ];

                $updated = [];
                foreach ($input as $key => $value) {
                    if (in_array($key, $allowedSeo, true)) {
                        $valStr = is_string($value) ? trim($value) : (string)$value;
                        updateSetting($key, $valStr, 'text');
                        $updated[$key] = $valStr;
                    }
                }

                if (empty($updated)) {
                    $this->error('No valid SEO fields provided for update.');
                }

                AgentToken::logAudit($agent['default_author_id'], 'agent_update_default_seo', 'settings', null, [], $updated);
                $this->success([
                    'updated_fields' => $updated
                ], 'Default SEO & Open Graph settings updated successfully.');
                break;

            default:
                $this->error('Unsupported brand action.');
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
