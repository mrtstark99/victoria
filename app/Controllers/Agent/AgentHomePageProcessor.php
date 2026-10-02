<?php
/** Agent API for safe homepage content and section layout edits. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\HomePage;

class AgentHomePageProcessor {
    public function process(string $action, array $scopes, array $agent, array $input): void {
        $write = $action === 'update_homepage';
        $scope = $write ? 'homepage:write' : 'homepage:read';
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== ($write ? 'POST' : 'GET')) $this->fail(405, 'Method not allowed.');
        if (!$write) $this->success(HomePage::get());
        if (!$input) $this->fail(400, 'JSON object required.');

        $old = HomePage::get();
        $next = $old;
        $allowedHero = array_keys($old['hero']);
        if (isset($input['hero'])) {
            if (!is_array($input['hero'])) $this->fail(400, 'hero must be an object.');
            foreach ($input['hero'] as $key => $value) {
                if (!in_array($key, $allowedHero, true) || !is_string($value) || mb_strlen($value) > 500) $this->fail(400, 'Invalid hero field: ' . $key);
                $value = trim($value);
                if (str_ends_with($key, '_url') || $key === 'image_url') {
                    if ($value !== '' && !preg_match('~^(https?://|/(?!/))~i', $value)) $this->fail(400, 'Hero URLs must be absolute HTTP(S) URLs or site paths.');
                }
                $next['hero'][$key] = $value;
            }
        }
        if (isset($input['sections'])) {
            if (!is_array($input['sections']) || !array_is_list($input['sections']) || count($input['sections']) !== count(HomePage::SECTION_KEYS) || count(array_unique($input['sections'])) !== count(HomePage::SECTION_KEYS) || array_diff($input['sections'], HomePage::SECTION_KEYS)) $this->fail(400, 'sections must contain every supported section exactly once.');
            $next['sections'] = $input['sections'];
        }
        if (isset($input['visible_sections'])) {
            if (!is_array($input['visible_sections'])) $this->fail(400, 'visible_sections must be an object.');
            foreach ($input['visible_sections'] as $key => $visible) {
                if (!in_array($key, HomePage::SECTION_KEYS, true) || !is_bool($visible)) $this->fail(400, 'Invalid visible section: ' . $key);
                $next['visible_sections'][$key] = $visible;
            }
        }
        if (isset($input['trust_items'])) {
            if (!is_array($input['trust_items']) || !array_is_list($input['trust_items']) || count($input['trust_items']) !== 4) $this->fail(400, 'trust_items must contain exactly four items.');
            foreach ($input['trust_items'] as $item) {
                if (!is_array($item) || !preg_match('/^bi-[a-z0-9-]{2,50}$/', (string)($item['icon'] ?? '')) || !is_string($item['title'] ?? null) || !is_string($item['description'] ?? null) || mb_strlen($item['title']) > 100 || mb_strlen($item['description']) > 180) $this->fail(400, 'Invalid trust item.');
            }
            $next['trust_items'] = $input['trust_items'];
        }
        HomePage::save($next);
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_update_homepage', 'settings', null, $old, $next);
        $this->success(HomePage::get());
    }

    private function success(array $data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail(int $code, string $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
