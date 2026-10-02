<?php
/** Read and update the structured public cost page configuration. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\CostPage;

class AgentCostProcessor {
    public function process($action, $scopes, $agent, $input) {
        $write = $action === 'update_cost_page';
        $required = $write ? 'cost:write' : 'cost:read';
        if (!in_array('admin', $scopes, true) && !in_array($required, $scopes, true)) $this->fail(403, 'Missing scope [' . $required . ']');
        if ($_SERVER['REQUEST_METHOD'] !== ($write ? 'POST' : 'GET')) $this->fail(405, 'Method not allowed.');
        if ($write && (!is_array($input) || ($input['user_confirmed'] ?? false) !== true)) {
            $this->fail(428, 'Explicit user confirmation is required. Present the exact cost-page change and target (/cost) to the user, wait for approval, then retry with user_confirmed=true.');
        }
        $old = CostPage::get();
        if (!$write) $this->success($old);
        if (!is_array($input)) $this->fail(400, 'JSON object required.');
        if (isset($input['expected_updated_at']) && $input['expected_updated_at'] !== $old['updated_at']) $this->fail(409, 'Cost page changed since it was read.');
        $data = $old;
        unset($data['updated_at']);
        foreach (['title', 'intro', 'exchange_rate', 'sections'] as $key) {
            if (array_key_exists($key, $input)) $data[$key] = $input[$key];
        }
        foreach (['title' => 200, 'intro' => 1000] as $field => $limit) {
            if (!is_string($data[$field]) || trim($data[$field]) === '' || mb_strlen($data[$field]) > $limit) $this->fail(400, 'Invalid ' . $field . '.');
            $data[$field] = trim($data[$field]);
        }
        if (!is_numeric($data['exchange_rate']) || $data['exchange_rate'] <= 0 || $data['exchange_rate'] > 10000) $this->fail(400, 'Invalid exchange_rate.');
        $data['exchange_rate'] = (float)$data['exchange_rate'];
        if (!is_array($data['sections']) || !array_is_list($data['sections']) || count($data['sections']) < 1 || count($data['sections']) > 12) $this->fail(400, 'sections must contain 1-12 items.');
        foreach ($data['sections'] as $section) {
            if (!is_array($section) || count($section) !== 4 || count(array_intersect(['title', 'description', 'columns', 'rows'], array_keys($section))) !== 4) $this->fail(400, 'Invalid section structure.');
            foreach (['title', 'description'] as $field) {
                if (!is_string($section[$field]) || trim($section[$field]) === '' || mb_strlen($section[$field]) > 1000) $this->fail(400, 'Invalid section ' . $field . '.');
            }
            if (!is_array($section['columns']) || !array_is_list($section['columns']) || count($section['columns']) < 2 || count($section['columns']) > 6) $this->fail(400, 'Invalid columns.');
            if (!is_array($section['rows']) || !array_is_list($section['rows']) || count($section['rows']) > 50) $this->fail(400, 'Invalid rows.');
            foreach (array_merge([$section['columns']], $section['rows']) as $row) {
                if (!is_array($row) || !array_is_list($row) || count($row) !== count($section['columns'])) $this->fail(400, 'Row width must match columns.');
                foreach ($row as $cell) if (!is_string($cell) || mb_strlen($cell) > 500) $this->fail(400, 'Invalid table cell.');
            }
        }
        CostPage::save($data);
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_update_cost_page', 'settings', null, $old, $data);
        $this->success(CostPage::get());
    }

    private function success($data) {
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function fail($code, $message) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
