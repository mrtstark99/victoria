<?php
/**
 * AI Agent Work Plan & Task Operations Processor
 */

namespace Controllers\Agent;

use Models\AgentTask;
use Models\AgentToken;

class AgentTaskProcessor {
    public function process(string $action, array $scopes, array $agent, array $input): void {
        $hasRead = in_array('admin', $scopes, true) || in_array('tasks:read', $scopes, true) || in_array('posts:read', $scopes, true) || in_array('posts:draft', $scopes, true);
        $hasWrite = in_array('admin', $scopes, true) || in_array('tasks:write', $scopes, true) || in_array('posts:draft', $scopes, true);

        switch ($action) {
            case 'tasks':
            case 'list_tasks':
            case 'get_tasks':
                if (!$hasRead) {
                    $this->forbidden('Missing scope [tasks:read]');
                }

                $filters = [];
                if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
                elseif (!empty($input['status'])) $filters['status'] = $input['status'];

                if (!empty($_GET['priority'])) $filters['priority'] = $_GET['priority'];
                elseif (!empty($input['priority'])) $filters['priority'] = $input['priority'];

                if (!empty($_GET['cycle_type'])) $filters['cycle_type'] = $_GET['cycle_type'];
                elseif (!empty($input['cycle_type'])) $filters['cycle_type'] = $input['cycle_type'];

                if (!empty($_GET['session_slot'])) $filters['session_slot'] = $_GET['session_slot'];
                elseif (!empty($input['session_slot'])) $filters['session_slot'] = $input['session_slot'];

                if (isset($_GET['month_num'])) $filters['month_num'] = $_GET['month_num'];
                elseif (isset($input['month_num'])) $filters['month_num'] = $input['month_num'];

                if (isset($_GET['week_num'])) $filters['week_num'] = $_GET['week_num'];
                elseif (isset($input['week_num'])) $filters['week_num'] = $input['week_num'];

                if (isset($_GET['is_ad_hoc'])) $filters['is_ad_hoc'] = $_GET['is_ad_hoc'];
                elseif (isset($input['is_ad_hoc'])) $filters['is_ad_hoc'] = $input['is_ad_hoc'];

                if (!empty($_GET['scheduled_date'])) $filters['scheduled_date'] = $_GET['scheduled_date'];
                elseif (!empty($input['scheduled_date'])) $filters['scheduled_date'] = $input['scheduled_date'];

                if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
                elseif (!empty($input['category'])) $filters['category'] = $input['category'];

                if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
                elseif (!empty($input['search'])) $filters['search'] = $input['search'];

                if (!empty($_GET['limit'])) $filters['limit'] = (int)$_GET['limit'];
                elseif (!empty($input['limit'])) $filters['limit'] = (int)$input['limit'];

                $tasks = AgentTask::getAll($filters);
                $stats = AgentTask::getStats();
                $categories = AgentTask::getCategories();

                $this->success([
                    'tasks' => $tasks,
                    'stats' => $stats,
                    'categories' => $categories,
                    'count' => count($tasks)
                ], 'Agent work plan tasks retrieved successfully.');
                break;

            case 'create_task':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [tasks:write]');
                }

                $content = trim((string)($input['content'] ?? ''));
                if ($content === '') {
                    $this->error('Missing task content parameter.');
                }

                $data = [
                    'content' => $content,
                    'priority' => in_array($input['priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $input['priority'] : 'medium',
                    'category' => trim((string)($input['category'] ?? 'Nội dung')),
                    'cycle_type' => in_array($input['cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) ? $input['cycle_type'] : 'daily',
                    'session_slot' => in_array($input['session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) ? $input['session_slot'] : 'morning',
                    'scheduled_date' => !empty($input['scheduled_date']) ? trim((string)$input['scheduled_date']) : null,
                    'month_num' => isset($input['month_num']) ? (int)$input['month_num'] : 1,
                    'week_num' => !empty($input['week_num']) ? (int)$input['week_num'] : null,
                    'is_ad_hoc' => !empty($input['is_ad_hoc']) || ($input['cycle_type'] ?? '') === 'adhoc' ? 1 : 0,
                    'phase' => !empty($input['phase']) ? trim((string)$input['phase']) : null,
                    'deadline' => !empty($input['deadline']) ? trim((string)$input['deadline']) : (!empty($input['scheduled_date']) ? $input['scheduled_date'] : null),
                    'is_completed' => !empty($input['is_completed']) ? 1 : 0,
                    'notes' => isset($input['notes']) ? trim((string)$input['notes']) : null,
                    'created_by' => 'agent'
                ];

                $taskId = AgentTask::create($data);
                $task = AgentTask::findById($taskId);

                AgentToken::logAudit(
                    $agent['default_author_id'] ?? 1,
                    'agent_create_task',
                    'ai_agent_tasks',
                    $taskId,
                    [],
                    $task ?: $data
                );

                $this->success($task, 'Task created successfully.');
                break;

            case 'create_adhoc_task':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [tasks:write]');
                }

                $content = trim((string)($input['content'] ?? ''));
                if ($content === '') {
                    $this->error('Missing task content parameter.');
                }

                $notes = isset($input['notes']) ? trim((string)$input['notes']) : null;
                $priority = in_array($input['priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $input['priority'] : 'high';
                $scheduledDate = !empty($input['scheduled_date']) ? trim((string)$input['scheduled_date']) : date('Y-m-d');
                $category = trim((string)($input['category'] ?? 'Bổ sung & Phát sinh'));

                $taskId = AgentTask::createAdHocTask($content, $notes, $priority, $scheduledDate, $category);
                $task = AgentTask::findById($taskId);

                AgentToken::logAudit(
                    $agent['default_author_id'] ?? 1,
                    'agent_create_adhoc_task',
                    'ai_agent_tasks',
                    $taskId,
                    [],
                    $task
                );

                $this->success($task, 'Ad-hoc task logged successfully into today schedule.');
                break;

            case 'update_task':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [tasks:write]');
                }

                $id = (int)($input['task_id'] ?? $input['id'] ?? $_GET['task_id'] ?? $_GET['id'] ?? 0);
                if ($id <= 0) {
                    $this->error('Missing or invalid task id parameter.');
                }

                $oldTask = AgentTask::findById($id);
                if (!$oldTask) {
                    $this->error('Task not found.', 404);
                }

                $data = [];
                if (isset($input['content'])) $data['content'] = trim((string)$input['content']);
                if (isset($input['priority']) && in_array($input['priority'], ['urgent', 'high', 'medium', 'low'], true)) {
                    $data['priority'] = $input['priority'];
                }
                if (isset($input['category'])) $data['category'] = trim((string)$input['category']);
                if (isset($input['cycle_type']) && in_array($input['cycle_type'], ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true)) {
                    $data['cycle_type'] = $input['cycle_type'];
                }
                if (isset($input['session_slot']) && in_array($input['session_slot'], ['morning', 'afternoon', 'evening', 'full_day'], true)) {
                    $data['session_slot'] = $input['session_slot'];
                }
                if (array_key_exists('scheduled_date', $input)) $data['scheduled_date'] = $input['scheduled_date'];
                if (array_key_exists('month_num', $input)) $data['month_num'] = (int)$input['month_num'];
                if (array_key_exists('week_num', $input)) $data['week_num'] = !empty($input['week_num']) ? (int)$input['week_num'] : null;
                if (array_key_exists('is_ad_hoc', $input)) $data['is_ad_hoc'] = !empty($input['is_ad_hoc']) ? 1 : 0;
                if (array_key_exists('phase', $input)) $data['phase'] = !empty($input['phase']) ? trim((string)$input['phase']) : null;
                if (array_key_exists('deadline', $input)) $data['deadline'] = $input['deadline'];
                if (array_key_exists('is_completed', $input)) $data['is_completed'] = $input['is_completed'];
                if (array_key_exists('notes', $input)) $data['notes'] = $input['notes'];

                AgentTask::update($id, $data);
                $updatedTask = AgentTask::findById($id);

                AgentToken::logAudit(
                    $agent['default_author_id'] ?? 1,
                    'agent_update_task',
                    'ai_agent_tasks',
                    $id,
                    $oldTask,
                    $updatedTask ?: $data
                );

                $this->success($updatedTask, 'Task updated successfully.');
                break;

            case 'complete_task':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [tasks:write]');
                }

                $id = (int)($input['task_id'] ?? $input['id'] ?? $_GET['task_id'] ?? $_GET['id'] ?? 0);
                if ($id <= 0) {
                    $this->error('Missing or invalid task id parameter.');
                }

                $oldTask = AgentTask::findById($id);
                if (!$oldTask) {
                    $this->error('Task not found.', 404);
                }

                $notes = isset($input['notes']) ? trim((string)$input['notes']) : null;
                $updatedTask = AgentTask::toggleComplete($id, true, $notes);

                AgentToken::logAudit(
                    $agent['default_author_id'] ?? 1,
                    'agent_complete_task',
                    'ai_agent_tasks',
                    $id,
                    $oldTask,
                    $updatedTask ?: ['is_completed' => 1, 'notes' => $notes]
                );

                $this->success($updatedTask, 'Task marked as completed successfully.');
                break;

            case 'delete_task':
                if (!$hasWrite) {
                    $this->forbidden('Missing scope [tasks:write]');
                }

                $id = (int)($input['task_id'] ?? $input['id'] ?? $_GET['task_id'] ?? $_GET['id'] ?? 0);
                if ($id <= 0) {
                    $this->error('Missing or invalid task id parameter.');
                }

                $oldTask = AgentTask::findById($id);
                if (!$oldTask) {
                    $this->error('Task not found.', 404);
                }

                AgentTask::delete($id);

                AgentToken::logAudit(
                    $agent['default_author_id'] ?? 1,
                    'agent_delete_task',
                    'ai_agent_tasks',
                    $id,
                    $oldTask,
                    ['deleted' => true, 'content' => $oldTask['content']]
                );

                $this->success(['id' => $id], 'Task deleted successfully.');
                break;

            default:
                $this->error('Invalid task action parameter.');
                break;
        }
    }

    private function success($data, string $message = ''): void {
        $resp = ['success' => true, 'data' => $data];
        if ($message !== '') {
            $resp['message'] = $message;
        }
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function error(string $msg, int $code = 400): void {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function forbidden(string $msg): void {
        $this->error("Forbidden: " . $msg, 403);
    }
}
