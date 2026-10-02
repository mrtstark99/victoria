<?php
/**
 * Event-Driven Pipeline Dispatcher
 * Replaces rigid time-slot scheduling with event-triggered workflows
 */

namespace Helpers;

class EventDispatcher {
    /**
     * Dispatch an event — executes all registered hooks for this event
     *
     * @param string $eventName  e.g. 'post_published', 'keyword_created', 'rank_dropped'
     * @param array  $payload    Event-specific data
     * @return array Results of all executed hooks
     */
    public static function dispatch(string $eventName, array $payload = []): array {
        $results = [];

        try {
            $db = \Database::getInstance();

            // Log the event
            $logStmt = $db->prepare(
                "INSERT INTO agent_event_log (event_name, payload, triggered_actions, created_at) VALUES (?, ?, '', datetime('now','localtime'))"
            );
            $logStmt->execute([$eventName, json_encode($payload, JSON_UNESCAPED_UNICODE)]);
            $logId = $db->lastInsertId();

            // Find enabled hooks for this event
            $hookStmt = $db->prepare(
                "SELECT * FROM agent_event_hooks WHERE event_name = ? AND is_enabled = 1 ORDER BY id"
            );
            $hookStmt->execute([$eventName]);
            $hooks = $hookStmt->fetchAll();

            if (empty($hooks)) {
                // Execute built-in default handlers
                $results = self::executeBuiltinHandlers($eventName, $payload);
            } else {
                foreach ($hooks as $hook) {
                    $hookConfig = json_decode($hook['hook_config'] ?? '{}', true) ?: [];
                    $hookResult = self::executeHook($hook['hook_action'], $payload, $hookConfig);
                    $results[] = [
                        'hook_id' => (int)$hook['id'],
                        'action' => $hook['hook_action'],
                        'result' => $hookResult
                    ];
                }
            }

            // Update log with triggered actions
            $actionSummary = array_map(fn($r) => $r['action'] ?? 'builtin', $results);
            $db->prepare("UPDATE agent_event_log SET triggered_actions = ? WHERE id = ?")
               ->execute([json_encode($actionSummary, JSON_UNESCAPED_UNICODE), $logId]);

        } catch (\Exception $e) {
            $results[] = ['action' => 'error', 'result' => ['success' => false, 'error' => $e->getMessage()]];
        }

        return $results;
    }

    /**
     * Execute a specific hook action
     */
    private static function executeHook(string $action, array $payload, array $config): array {
        try {
            switch ($action) {
                case 'ping_index':
                    if (!empty($payload['post_id'])) {
                        return IndexingService::onPostPublished((int)$payload['post_id']);
                    }
                    return ['success' => false, 'error' => 'No post_id in payload'];

                case 'create_task':
                    return self::autoCreateTask($payload, $config);

                case 'link_suggestions':
                    if (!empty($payload['post_id'])) {
                        // Just log a suggestion task — actual linking requires agent intervention
                        return self::autoCreateTask([
                            'content' => 'Kiểm tra và chèn internal link 2 chiều cho bài mới: ' . ($payload['title'] ?? "Post #{$payload['post_id']}"),
                            'category' => 'SEO On-page',
                            'priority' => 'high'
                        ], $config);
                    }
                    return ['success' => false, 'error' => 'No post_id in payload'];

                case 'regenerate_sitemap':
                    return IndexingService::regenerateSitemap();

                case 'serp_research':
                    if (!empty($payload['keyword'])) {
                        return self::autoCreateTask([
                            'content' => 'Nghiên cứu SERP và tạo dàn ý cho từ khóa: ' . $payload['keyword'],
                            'category' => 'Nội dung',
                            'priority' => $payload['priority'] ?? 'medium'
                        ], $config);
                    }
                    return ['success' => false, 'error' => 'No keyword in payload'];

                case 'content_refresh':
                    return self::autoCreateTask([
                        'content' => 'Tối ưu lại nội dung bài viết: ' . ($payload['title'] ?? $payload['url'] ?? 'Unknown'),
                        'category' => 'SEO On-page',
                        'priority' => 'high',
                        'notes' => 'Rank dropped. Position: ' . ($payload['position'] ?? 'N/A') . '. Reason: ' . ($payload['reason'] ?? 'performance decline')
                    ], $config);

                default:
                    return ['success' => false, 'error' => "Unknown hook action: {$action}"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Built-in default handlers for known events (when no custom hooks exist)
     */
    private static function executeBuiltinHandlers(string $eventName, array $payload): array {
        $results = [];

        switch ($eventName) {
            case 'post_published':
                // Auto: ping index + regenerate sitemap + suggest internal links
                if (!empty($payload['post_id'])) {
                    $results[] = [
                        'action' => 'ping_index',
                        'result' => IndexingService::onPostPublished((int)$payload['post_id'])
                    ];
                    $results[] = [
                        'action' => 'create_internal_link_task',
                        'result' => self::autoCreateTask([
                            'content' => '[Auto] Kiểm tra và chèn internal link 2 chiều cho bài mới xuất bản: ' . ($payload['title'] ?? ''),
                            'category' => 'SEO On-page',
                            'priority' => 'high'
                        ], [])
                    ];
                }
                break;

            case 'keyword_created':
                // Auto: create task to research SERP + outline
                $results[] = [
                    'action' => 'create_serp_research_task',
                    'result' => self::autoCreateTask([
                        'content' => '[Auto] Nghiên cứu SERP (analyze_serp + serp_outline) cho từ khóa mới: ' . ($payload['keyword'] ?? ''),
                        'category' => 'Nội dung',
                        'priority' => 'medium'
                    ], [])
                ];
                break;

            case 'draft_created':
                // Auto: create task to validate post
                $results[] = [
                    'action' => 'create_validation_task',
                    'result' => self::autoCreateTask([
                        'content' => '[Auto] Validate và kiểm tra E-E-A-T cho bài nháp mới: ' . ($payload['title'] ?? ''),
                        'category' => 'Kiểm duyệt',
                        'priority' => 'medium'
                    ], [])
                ];
                break;

            case 'rank_dropped':
                // Auto: create urgent content refresh task
                $results[] = [
                    'action' => 'create_content_refresh_task',
                    'result' => self::autoCreateTask([
                        'content' => '[Auto-Urgent] Tối ưu lại nội dung — bài viết bị giảm thứ hạng: ' . ($payload['url'] ?? ''),
                        'category' => 'SEO On-page',
                        'priority' => 'urgent',
                        'notes' => 'Previous position: ' . ($payload['old_position'] ?? '?') . ' → Current: ' . ($payload['new_position'] ?? '?')
                    ], [])
                ];
                break;
        }

        return $results;
    }

    /**
     * Auto-create a task in the agent task system
     */
    private static function autoCreateTask(array $taskData, array $config): array {
        try {
            $db = \Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO ai_agent_tasks (content, priority, category, cycle_type, session_slot, scheduled_date, is_ad_hoc, is_completed, notes, created_by, created_at, updated_at)
                VALUES (?, ?, ?, 'adhoc', 'full_day', ?, 1, 0, ?, 'agent', datetime('now','localtime'), datetime('now','localtime'))
            ");
            $stmt->execute([
                $taskData['content'] ?? 'Auto-generated task',
                $taskData['priority'] ?? $config['default_priority'] ?? 'medium',
                $taskData['category'] ?? $config['default_category'] ?? 'Bổ sung & Phát sinh',
                date('Y-m-d'),
                $taskData['notes'] ?? null
            ]);

            return ['success' => true, 'task_id' => (int)$db->lastInsertId()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get event log entries (for Agent API)
     */
    public static function getEventLog(int $limit = 50, ?string $eventFilter = null): array {
        $db = \Database::getInstance();
        $sql = "SELECT * FROM agent_event_log";
        $params = [];

        if ($eventFilter) {
            $sql .= " WHERE event_name = ?";
            $params[] = $eventFilter;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $db->prepare($sql);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all hooks
     */
    public static function getHooks(?string $eventFilter = null): array {
        $db = \Database::getInstance();
        $sql = "SELECT * FROM agent_event_hooks";
        $params = [];

        if ($eventFilter) {
            $sql .= " WHERE event_name = ?";
            $params[] = $eventFilter;
        }

        $sql .= " ORDER BY event_name, id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Create a new hook
     */
    public static function createHook(string $eventName, string $hookAction, array $hookConfig = [], bool $enabled = true): int {
        $db = \Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO agent_event_hooks (event_name, hook_action, hook_config, is_enabled, created_at) VALUES (?, ?, ?, ?, datetime('now','localtime'))"
        );
        $stmt->execute([$eventName, $hookAction, json_encode($hookConfig, JSON_UNESCAPED_UNICODE), $enabled ? 1 : 0]);
        return (int)$db->lastInsertId();
    }

    /**
     * Update a hook
     */
    public static function updateHook(int $id, array $fields): bool {
        $db = \Database::getInstance();
        $sets = [];
        $params = [];
        foreach ($fields as $key => $val) {
            if (in_array($key, ['event_name', 'hook_action', 'hook_config', 'is_enabled'])) {
                $sets[] = "{$key} = ?";
                $params[] = $key === 'hook_config' && is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
            }
        }
        if (empty($sets)) return false;
        $params[] = $id;
        return $db->prepare("UPDATE agent_event_hooks SET " . implode(', ', $sets) . " WHERE id = ?")->execute($params);
    }
}
