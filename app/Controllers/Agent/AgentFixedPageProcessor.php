<?php
/** Manage optional content for dedicated public pages. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\FixedPage;

class AgentFixedPageProcessor {
    public function process($action, $scopes, $agent, $input) {
        $read = $action === 'get_fixed_page';
        $scope = $read ? 'fixed_pages:read' : 'fixed_pages:write';
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        if ($_SERVER['REQUEST_METHOD'] !== ($read ? 'GET' : 'POST')) $this->fail(405, 'Method not allowed.');
        $slug = $read ? ($_GET['slug'] ?? '') : ($input['slug'] ?? '');
        if (!is_string($slug) || !in_array($slug, FixedPage::SLUGS, true)) $this->fail(400, 'Invalid fixed page slug.');
        $old = FixedPage::get($slug);
        if ($read) $this->success(['slug' => $slug, 'override' => $old, 'uses_original_template' => $old === null]);
        if ($action === 'reset_fixed_page') {
            FixedPage::remove($slug);
            $new = null;
        } else {
            $title = $input['title'] ?? ($old['title'] ?? '');
            $description = $input['meta_description'] ?? ($old['meta_description'] ?? '');
            $content = $input['content_html'] ?? ($old['content_html'] ?? '');
            if (!is_string($title) || trim($title) === '' || mb_strlen($title) > 200) $this->fail(400, 'Invalid title.');
            if (!is_string($description) || mb_strlen($description) > 500) $this->fail(400, 'Invalid meta_description.');
            if (!is_string($content) || trim(strip_tags($content)) === '' || strlen($content) > 200000) $this->fail(400, 'Invalid content_html.');
            $new = ['title' => trim(strip_tags($title)), 'meta_description' => trim(strip_tags($description)), 'content_html' => sanitizeHtml($content)];
            FixedPage::save($slug, $new);
        }
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_' . $action, 'settings', null, $old ?: [], $new ?: []);
        $this->success(['slug' => $slug, 'override' => $new]);
    }
    private function success($data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail($code, $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
