<?php
/** Agent API for the assigned author's profile biography. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\User;

class AgentAuthorProfileProcessor {
    public function process(string $action, array $scopes, array $agent, array $input): void {
        $write = $action === 'update_author_profile';
        $scope = $write ? 'profile:write' : 'profile:read';
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== ($write ? 'POST' : 'GET')) $this->fail(405, 'Method not allowed.');
        $authorId = (int)($agent['default_author_id'] ?? 0);
        if ($authorId <= 0) $this->fail(400, 'This agent has no assigned author profile.');
        $author = User::findById($authorId);
        if (!$author) $this->fail(404, 'Assigned author profile not found.');
        $oldBio = (string)($author['bio'] ?? '');
        $oldName = (string)($author['full_name'] ?? '');
        if ($write) {
            $bio = $input['bio'] ?? null;
            if ($bio !== null) {
                if (!is_string($bio) || mb_strlen($bio, 'UTF-8') > 1000) $this->fail(400, 'bio must be a string of at most 1,000 characters.');
            }
            $fullName = $input['full_name'] ?? ($input['name'] ?? null);
            if ($fullName !== null) {
                if (!is_string($fullName) || trim($fullName) === '' || mb_strlen($fullName, 'UTF-8') > 100) $this->fail(400, 'full_name must be a valid string of at most 100 characters.');
            }
            if ($bio === null && $fullName === null) {
                $this->fail(400, 'Either bio or full_name must be provided.');
            }

            $updates = [];
            $params = [];
            if ($bio !== null) {
                $updates[] = "bio = ?";
                $params[] = trim($bio);
            }
            if ($fullName !== null) {
                $updates[] = "full_name = ?";
                $params[] = trim($fullName);
            }
            $params[] = $authorId;
            $stmt = \Database::getInstance()->prepare("UPDATE users SET " . implode(', ', $updates) . ", updated_at = datetime('now','localtime') WHERE id = ?");
            $stmt->execute($params);

            if ($bio !== null) {
                AgentToken::logAudit($authorId, 'agent_update_author_bio', 'users', $authorId, ['bio' => $oldBio], ['bio' => trim($bio)]);
                $author['bio'] = trim($bio);
            }
            if ($fullName !== null) {
                AgentToken::logAudit($authorId, 'agent_update_author_name', 'users', $authorId, ['full_name' => $oldName], ['full_name' => trim($fullName)]);
                $author['full_name'] = trim($fullName);
            }
        }
        $this->success(['id' => (int)$author['id'], 'full_name' => $author['full_name'], 'bio' => (string)($author['bio'] ?? '')]);
    }

    private function success(array $data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail(int $code, string $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
