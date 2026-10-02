<?php
/** Agent access to contact and consultation inquiries. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\Contact;

class AgentContactProcessor {
    public function process($action, $scopes, $agent, $input) {
        $scope = in_array($action, ['contacts', 'get_contact'], true) ? 'contacts:read' : ($action === 'delete_contact' ? 'contacts:delete' : 'contacts:write');
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        $read = $scope === 'contacts:read';
        if ($_SERVER['REQUEST_METHOD'] !== ($read ? 'GET' : 'POST')) $this->fail(405, 'Method not allowed.');
        if ($action === 'contacts') {
            $status = trim((string)($_GET['status'] ?? ''));
            if ($status !== '' && !in_array($status, self::statuses(), true)) $this->fail(400, 'Invalid status.');
            $this->success(Contact::getPaginated(max(1, (int)($_GET['page'] ?? 1)), min(50, max(1, (int)($_GET['per_page'] ?? 20))), $status, trim((string)($_GET['search'] ?? ''))));
        }
        $id = (int)($read ? ($_GET['id'] ?? 0) : ($input['id'] ?? 0));
        if ($id <= 0) $this->fail(400, 'Valid id required.');
        $old = Contact::findById($id);
        if (!$old) $this->fail(404, 'Contact not found.');
        if ($action === 'get_contact') $this->success($old);
        if ($action === 'update_contact') {
            $status = $input['status'] ?? $old['status'];
            if (!is_string($status) || !in_array($status, self::statuses(), true)) $this->fail(400, 'Invalid status.');
            $notes = array_key_exists('notes', $input) ? $input['notes'] : null;
            if ($notes !== null && (!is_string($notes) || mb_strlen($notes) > 10000)) $this->fail(400, 'Invalid notes.');
            Contact::updateStatus($id, $status, $notes);
            $new = Contact::findById($id);
        } else {
            Contact::delete($id);
            $new = ['deleted' => true];
        }
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_' . $action, 'contacts', $id, $old, $new);
        $this->success($new);
    }
    private static function statuses(): array { return ['new', 'read', 'replied', 'processing', 'completed', 'archived']; }
    private function success($data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail($code, $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
