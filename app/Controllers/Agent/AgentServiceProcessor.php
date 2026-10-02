<?php
/** API actions for managing study abroad services. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\Service;

class AgentServiceProcessor {
    public function process($action, $scopes, $agent, $input) {
        $scope = [
            'services' => 'services:read', 'list_services' => 'services:read',
            'get_service' => 'services:read', 'create_service' => 'services:write',
            'update_service' => 'services:write', 'activate_service' => 'services:publish',
            'deactivate_service' => 'services:publish', 'delete_service' => 'services:delete'
        ][$action];
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) {
            $this->fail(403, 'Missing scope [' . $scope . ']');
        }
        $read = in_array($action, ['services', 'list_services', 'get_service'], true);
        if ($_SERVER['REQUEST_METHOD'] !== ($read ? 'GET' : 'POST')) {
            header('Allow: ' . ($read ? 'GET' : 'POST'));
            $this->fail(405, 'Method not allowed.');
        }
        if (!is_array($input)) $this->fail(400, 'JSON object required.');
        if (!$read && ($input['user_confirmed'] ?? false) !== true) {
            $this->fail(428, 'Explicit user confirmation is required. Present the exact service change and target (/services) to the user, wait for approval, then retry with user_confirmed=true.');
        }

        if ($action === 'services' || $action === 'list_services') {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 20)));
            $search = trim((string)($_GET['search'] ?? ''));
            $this->success(Service::getPaginated($page, $perPage, $search));
        }
        if ($action === 'get_service') {
            $id = (int)($_GET['id'] ?? 0);
            $slug = trim((string)($_GET['slug'] ?? ''));
            if ($id <= 0 && $slug === '') $this->fail(400, 'id or slug is required.');
            $service = $id > 0 ? Service::findById($id) : Service::findBySlug($slug);
            if (!$service) $this->fail(404, 'Service not found.');
            $this->success($service);
        }

        $id = (int)($input['id'] ?? 0);
        $existing = null;
        if ($action !== 'create_service') {
            if ($id <= 0) $this->fail(400, 'Valid id is required.');
            $existing = Service::findById($id);
            if (!$existing) $this->fail(404, 'Service not found.');
            if (isset($input['expected_updated_at']) && $input['expected_updated_at'] !== $existing['updated_at']) {
                $this->fail(409, 'Service changed since it was read.');
            }
            if ($action === 'update_service' && $existing['status'] === 'active' && !in_array('admin', $scopes, true) && !in_array('services:publish', $scopes, true)) {
                $this->fail(403, 'Updating an active service requires [services:publish].');
            }
        }
        if ($action === 'delete_service') {
            Service::delete($id);
            $this->audit($agent, $action, $id, $existing, []);
            $this->success(['id' => $id]);
        }

        if ($action === 'activate_service' || $action === 'deactivate_service') {
            $data = $existing;
            $data['status'] = $action === 'activate_service' ? 'active' : 'inactive';
        } else {
            $data = $existing ?: ['name' => '', 'title' => '', 'description' => '', 'content' => '', 'icon' => 'bi-briefcase', 'price' => 0, 'packages' => [], 'display_order' => 0, 'status' => 'inactive'];
            foreach (['name', 'title', 'slug', 'description', 'content', 'icon', 'price', 'packages', 'display_order'] as $field) {
                if (array_key_exists($field, $input)) $data[$field] = $input[$field];
            }
            // Content editors may prepare an inactive service; activation requires a separate scope.
            if (isset($input['status']) && $input['status'] !== 'inactive') $this->fail(400, 'Use activate_service to publish.');
            if (isset($input['status'])) $data['status'] = 'inactive';
            if ($action === 'create_service') $data['status'] = 'inactive';
            foreach (['name', 'title', 'description', 'content', 'icon'] as $field) {
                if (!is_string($data[$field])) $this->fail(400, $field . ' must be a string.');
                $data[$field] = trim($data[$field]);
            }
            $data['title'] = trim(strip_tags($data['title']));
            $data['name'] = trim(strip_tags($data['name']));
            $data['icon'] = trim(strip_tags($data['icon']));
            if ($data['title'] === '' || mb_strlen($data['title']) > 200) $this->fail(400, 'title must contain 1-200 characters.');
            if ($data['name'] === '') $data['name'] = $data['title'];
            if (!isset($data['slug']) || !is_string($data['slug']) || trim($data['slug']) === '') {
                $this->fail(400, 'An English slug is required; use lowercase English words separated by hyphens.');
            }
            try {
                $data['slug'] = Service::validateEnglishSlug($data['slug']);
            } catch (\InvalidArgumentException $exception) {
                $this->fail(400, $exception->getMessage());
            }
            $collision = Service::findBySlug($data['slug']);
            if ($collision && (int)$collision['id'] !== $id) $this->fail(409, 'slug already exists.');
            if (!is_numeric($data['price']) || (float)$data['price'] < 0) $this->fail(400, 'price must be non-negative.');
            if (array_key_exists('packages', $input)) {
                try {
                    $data['packages'] = Service::normalizePackages($input['packages']);
                } catch (\InvalidArgumentException $exception) {
                    $this->fail(400, $exception->getMessage());
                }
            } else {
                $data['packages'] = $existing['packages'] ?? [];
            }
            if (filter_var($data['display_order'], FILTER_VALIDATE_INT) === false) $this->fail(400, 'display_order must be an integer.');
        }
        if ($action === 'create_service') {
            $id = Service::create($data);
        } else {
            Service::update($id, $data);
        }
        $this->audit($agent, $action, $id, $existing ?: [], $data);
        $this->success(Service::findById($id));
    }

    private function audit($agent, $action, $id, $old, $new) {
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_' . $action, 'services', $id, $old, $new);
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
