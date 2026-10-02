<?php
/** Agent settings for calculator, post sidebar and analytics. */
namespace Controllers\Agent;

use Controllers\AdminSidebarController;
use Models\AgentToken;
use Models\HomeCalculator;

class AgentSettingsProcessor {
    public function process($action, $scopes, $agent, $input) {
        $groups = [
            'get_home_calculator' => ['calculator:read', 'calculator', false],
            'update_home_calculator' => ['calculator:write', 'calculator', true],
            'get_post_sidebar' => ['sidebar:read', 'sidebar', false],
            'update_post_sidebar' => ['sidebar:write', 'sidebar', true],
            'get_analytics_settings' => ['analytics:settings:read', 'analytics', false],
            'update_analytics_settings' => ['analytics:settings:write', 'analytics', true]
        ];
        [$scope, $group, $write] = $groups[$action];
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        if ($_SERVER['REQUEST_METHOD'] !== ($write ? 'POST' : 'GET')) $this->fail(405, 'Method not allowed.');
        if (!$write) $this->success($this->read($group));
        if (!is_array($input) || !$input) $this->fail(400, 'JSON object required.');
        $old = $this->read($group);
        if ($group === 'calculator') {
            $data = $old;
            foreach ($input as $key => $value) {
                if (!array_key_exists($key, $data) || !is_array($value) || count($value) !== count($data[$key])) $this->fail(400, 'Invalid calculator group: ' . $key);
                if ($key === 'package_prices' && array_keys($value) !== array_keys($data[$key])) $this->fail(400, 'package_prices keys must match the system choices.');
                if ($key !== 'package_prices' && !array_is_list($value)) $this->fail(400, 'Price group must be a list.');
                foreach ($value as $price) if (!is_int($price) || $price < 0 || $price > 1000000000) $this->fail(400, 'Prices must be non-negative integers.');
                $data[$key] = $value;
            }
            HomeCalculator::save($data);
        } elseif ($group === 'sidebar') {
            $data = [];
            foreach ($input as $key => $value) {
                if (!array_key_exists($key, $old) || !str_starts_with($key, 'post_') || !is_scalar($value) || is_bool($value) || mb_strlen((string)$value) > 10000) $this->fail(400, 'Invalid sidebar field: ' . $key);
                $text = (string)$value;
                if ($key === 'post_sidebar_custom_html_widgets') {
                    $widgets = json_decode($text, true);
                    if (!is_array($widgets) || count($widgets) > 20) $this->fail(400, 'Invalid widgets JSON.');
                    foreach ($widgets as &$widget) {
                        if (!is_array($widget) || !isset($widget['content']) || !is_string($widget['content'])) $this->fail(400, 'Invalid widget.');
                        $widget['content'] = sanitizeHtml($widget['content']);
                    }
                    unset($widget);
                    $text = json_encode($widgets, JSON_UNESCAPED_UNICODE);
                }
                if (str_ends_with($key, '_url') && $text !== '' && !preg_match('~^(https?://|/|mailto:)~i', $text)) $this->fail(400, 'Invalid URL.');
                $data[$key] = $text;
            }
            foreach ($data as $key => $value) updateSetting($key, $value);
        } else {
            $data = [];
            if (isset($input['service_account_json']) && isset($input['remove_credentials'])) $this->fail(400, 'Choose either service_account_json or remove_credentials.');
            $numeric = ['seo_monthly_cost', 'organic_lead_value', 'kpi_organic_sessions_target', 'kpi_impressions_target', 'kpi_position_target', 'kpi_ctr_target', 'kpi_engagement_rate_target', 'kpi_avg_engagement_time_target', 'kpi_conversion_rate_target', 'kpi_roi_target'];
            foreach ($input as $key => $value) {
                if (in_array($key, $numeric, true)) {
                    if (!is_numeric($value) || (float)$value < 0) $this->fail(400, 'Invalid numeric setting: ' . $key);
                    $data[$key] = (string)(float)$value;
                } elseif ($key === 'ga_id') {
                    if (!is_string($value)) $this->fail(400, 'Invalid ga_id.');
                    $value = strtoupper(trim((string)$value));
                    if ($value !== '' && !preg_match('/^G-[A-Z0-9]+$/', $value)) $this->fail(400, 'Invalid ga_id.');
                    $data[$key] = $value;
                } elseif ($key === 'ga_property_id') {
                    if (!is_scalar($value) || !preg_match('/^\d*$/', (string)$value)) $this->fail(400, 'Invalid ga_property_id.');
                    $data[$key] = (string)$value;
                } elseif ($key === 'gsc_site_url') {
                    if (!is_string($value) || ($value !== '' && !str_starts_with($value, 'sc-domain:') && !filter_var($value, FILTER_VALIDATE_URL))) $this->fail(400, 'Invalid gsc_site_url.');
                    $data[$key] = $value;
                } elseif ($key === 'gsc_verification') {
                    if (!is_string($value) || strlen($value) > 500) $this->fail(400, 'Invalid gsc_verification.');
                    $data[$key] = $value;
                } elseif ($key === 'service_account_json') {
                    if (!is_string($value) || strlen($value) > 65536) $this->fail(400, 'Invalid service account JSON.');
                    $credentials = json_decode($value, true);
                    if (!is_array($credentials) || ($credentials['type'] ?? '') !== 'service_account' || empty($credentials['private_key']) || empty($credentials['client_email']) || ($credentials['token_uri'] ?? '') !== 'https://oauth2.googleapis.com/token' || !str_ends_with((string)$credentials['client_email'], '.gserviceaccount.com')) $this->fail(400, 'Invalid Google service account.');
                    $encrypted = analyticsEncryptSecret(json_encode($credentials, JSON_UNESCAPED_SLASHES));
                    if ($encrypted === '') $this->fail(500, 'Could not encrypt service account.');
                    $data['google_service_account_enc'] = $encrypted;
                } elseif ($key === 'remove_credentials') {
                    if ($value !== true) $this->fail(400, 'remove_credentials must be true.');
                    $data['google_service_account_enc'] = '';
                } else $this->fail(400, 'Unknown analytics field: ' . $key);
            }
            foreach ($data as $key => $value) updateSetting($key, $value);
            if (function_exists('analyticsClearCache')) analyticsClearCache();
        }
        $auditNew = $data;
        unset($auditNew['google_service_account_enc']);
        if ($group === 'analytics' && isset($data['google_service_account_enc'])) $auditNew['credentials_updated'] = $data['google_service_account_enc'] !== '';
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_' . $action, 'settings', null, $group === 'analytics' ? [] : $old, $auditNew);
        $this->success($this->read($group));
    }
    private function read($group): array {
        if ($group === 'calculator') return HomeCalculator::get();
        if ($group === 'sidebar') return AdminSidebarController::getSidebarSettings();
        $keys = ['ga_id', 'ga_property_id', 'gsc_verification', 'gsc_site_url', 'seo_monthly_cost', 'organic_lead_value', 'kpi_organic_sessions_target', 'kpi_impressions_target', 'kpi_position_target', 'kpi_ctr_target', 'kpi_engagement_rate_target', 'kpi_avg_engagement_time_target', 'kpi_conversion_rate_target', 'kpi_roi_target'];
        $rows = \Database::getInstance()->query('SELECT setting_key, setting_value FROM settings')->fetchAll(\PDO::FETCH_KEY_PAIR);
        $data = [];
        foreach ($keys as $key) $data[$key] = $rows[$key] ?? '';
        $data['credentials_configured'] = ($rows['google_service_account_enc'] ?? '') !== '';
        return $data;
    }
    private function success($data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail($code, $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
