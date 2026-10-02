<?php
/** Topic cluster lifecycle for the SEO planner. */
namespace Controllers\Agent;

use Models\AgentToken;
use Models\SEO;

class AgentClusterProcessor {
    public function process($action, $scopes, $agent, $input) {
        $read = in_array($action, ['list_clusters', 'get_cluster'], true);
        $scope = $read ? 'seo:read' : 'seo:write';
        if (!in_array('admin', $scopes, true) && !in_array($scope, $scopes, true)) $this->fail(403, 'Missing scope [' . $scope . ']');
        if ($_SERVER['REQUEST_METHOD'] !== ($read ? 'GET' : 'POST')) $this->fail(405, 'Method not allowed.');
        if ($action === 'list_clusters') $this->success(SEO::getClusters());
        $id = (int)($read ? ($_GET['id'] ?? 0) : ($input['id'] ?? 0));
        $old = $id > 0 ? SEO::findClusterById($id) : null;
        if ($action === 'get_cluster') {
            if (!$old) $this->fail(404, 'Cluster not found.');
            $this->success($old);
        }
        if ($action !== 'create_cluster' && !$old) $this->fail(404, 'Cluster not found.');
        if ($action === 'delete_cluster') {
            SEO::deleteCluster($id);
            $new = ['id' => $id, 'deleted' => true];
        } else {
            $data = $old ?: ['planning_month' => '', 'name' => '', 'pillar_title' => '', 'pillar_url' => '', 'description' => '', 'status' => 'planned'];
            foreach (['planning_month', 'name', 'pillar_title', 'pillar_url', 'description', 'status'] as $key) if (array_key_exists($key, $input)) $data[$key] = $input[$key];
            foreach (['planning_month', 'name', 'pillar_title', 'pillar_url', 'description', 'status'] as $key) if (!is_string($data[$key]) || mb_strlen($data[$key]) > 2000) $this->fail(400, 'Invalid ' . $key . '.');
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $data['planning_month']) || trim($data['name']) === '' || trim($data['pillar_title']) === '') $this->fail(400, 'planning_month, name and pillar_title are required.');
            if (!in_array($data['status'], ['planned', 'in_progress', 'published'], true)) $this->fail(400, 'Invalid status.');
            if ($action === 'create_cluster') {
                foreach (SEO::getClusters() as $item) if ($item['planning_month'] === $data['planning_month'] && $item['name'] === $data['name']) $this->fail(409, 'Cluster already exists.');
                if (!SEO::createCluster($data['planning_month'], $data['name'], $data['pillar_title'], $data['pillar_url'], $data['description'])) $this->fail(500, 'Could not create cluster.');
                $clusters = SEO::getClusters();
                $new = null;
                foreach ($clusters as $item) if ($item['planning_month'] === $data['planning_month'] && $item['name'] === $data['name']) { $new = $item; break; }
                if ($data['status'] !== 'planned' && $new) { SEO::updateCluster($new['id'], $data); $new = SEO::findClusterById($new['id']); }
            } else {
                SEO::updateCluster($id, $data);
                $new = SEO::findClusterById($id);
            }
        }
        AgentToken::logAudit($agent['default_author_id'] ?? null, 'agent_' . $action, 'seo_topic_clusters', $new['id'] ?? $id, $old ?: [], $new ?: []);
        $this->success($new);
    }
    private function success($data): void { echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
    private function fail($code, $message): void { http_response_code($code); echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE); exit; }
}
