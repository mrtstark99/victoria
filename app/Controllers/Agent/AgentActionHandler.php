<?php
/**
 * Agent Action Handler
 * Processes POST requests for Agent Token management, tasks, and guideline settings.
 */

namespace Controllers\Agent;

use Models\AgentToken;
use Models\AgentTask;

class AgentActionHandler {

    public static function handle(): array {
        $db = \Database::getInstance();
        $errors = [];

        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($token)) {
            if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => 'Phiên làm việc hết hạn, vui lòng tải lại trang.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $errors[] = 'Phiên làm việc hết hạn, vui lòng thử lại.';
            return $errors;
        }

        $action = $_POST['action'] ?? '';
        if ($action === 'generate' || $action === 'create_token') {
            $name = sanitizeInput($_POST['token_name'] ?? '');
            $authorId = (int)($_POST['default_author_id'] ?? 0);
            $ips = sanitizeInput($_POST['allowed_ips'] ?? '');
            $selectedScopes = $_POST['scopes'] ?? [];

            if (empty($name)) $errors[] = 'Vui lòng điền tên agent.';
            if ($authorId <= 0) $errors[] = 'Vui lòng chọn tác giả mặc định.';
            if (empty($selectedScopes)) $errors[] = 'Vui lòng chọn ít nhất một quyền hạn.';

            if (!$errors) {
                $raw = 'ai_agent_' . bin2hex(random_bytes(24));
                $scopesStr = implode(',', $selectedScopes);
                AgentToken::create($name, $raw, $authorId, $scopesStr, $ips === '' ? null : $ips);
                $_SESSION['agent_success'] = 'Tạo Agent Token mới thành công.';
                $_SESSION['new_agent_raw'] = $raw;
                $_SESSION['new_agent_name'] = $name;
                redirect('/admin/agent#tokens');
            }
        } elseif ($action === 'update_token') {
            $tokenId = (int)($_POST['token_id'] ?? 0);
            $name = sanitizeInput($_POST['token_name'] ?? '');
            $authorId = (int)($_POST['default_author_id'] ?? 0);
            $ips = sanitizeInput($_POST['allowed_ips'] ?? '');
            $selectedScopes = $_POST['scopes'] ?? [];

            $existingToken = AgentToken::findById($tokenId);
            if (!$existingToken) {
                redirect('/admin/agent#tokens', 'Không tìm thấy Token cần chỉnh sửa.', 'error');
            }

            if (empty($name)) $errors[] = 'Tên agent không được để trống.';
            if ($authorId <= 0) $errors[] = 'Vui lòng chọn tác giả mặc định.';
            if (empty($selectedScopes)) $errors[] = 'Vui lòng chọn ít nhất một quyền hạn.';

            if (!$errors) {
                $scopesStr = implode(',', $selectedScopes);
                AgentToken::update($tokenId, $name, $authorId, $scopesStr, $ips === '' ? null : $ips);
                $userId = $_SESSION['user_id'] ?? null;
                AgentToken::logAudit($userId, 'UPDATE_AGENT_TOKEN', 'ai_agent_tokens', $tokenId, $existingToken, [
                    'token_name' => $name, 'default_author_id' => $authorId, 'permissions' => $scopesStr, 'allowed_ips' => $ips
                ]);
                redirect('/admin/agent#tokens', 'Cập nhật thông tin và phân quyền Token thành công.');
            }
        } elseif ($action === 'revoke') {
            $id = (int)($_POST['revoke_token_id'] ?? 0);
            if ($id > 0) {
                AgentToken::revoke($id);
                redirect('/admin/agent#tokens', 'Đã thu hồi token thành công.');
            }
        } elseif ($action === 'save_guidelines') {
            $guidelineErrors = [];
            $saved = saveAIGuidelines($_POST, $guidelineErrors);
            if (!$saved) {
                $_SESSION['flash_error'] = implode("\n", $guidelineErrors);
                redirect('/admin/agent#guidelines');
            } else {
                $userId = $_SESSION['user_id'] ?? null;
                $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, ip_address, user_agent) VALUES (?, 'UPDATE_AI_GUIDELINES', 'settings', ?, ?)");
                $stmtLog->execute([$userId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);
                redirect('/admin/agent#guidelines', 'Cập nhật cấu hình hướng dẫn viết bài cho AI Agent thành công.');
            }
        } elseif ($action === 'create_task') {
            self::handleCreateTask();
        } elseif ($action === 'create_adhoc_task') {
            self::handleCreateAdhocTask();
        } elseif ($action === 'update_task') {
            self::handleUpdateTask();
        } elseif ($action === 'generate_calendar_schedule' || $action === 'seed_standard_roadmap') {
            $startMonth = !empty($_POST['start_month']) ? trim((string)$_POST['start_month']) : date('Y-m');
            $res = \Models\AgentTaskScheduler::generateRealCalendarSchedule($startMonth);
            $userId = $_SESSION['user_id'] ?? null;
            AgentToken::logAudit($userId, 'GENERATE_REAL_SCHEDULE', 'ai_agent_tasks', 0, [], $res);
            $_SESSION['agent_success'] = "Đã lên lịch biểu thực tế thành công bắt đầu từ Tháng 1 ({$res['start_month']}). Đã thêm {$res['created_tasks']} nhiệm vụ.";
            redirect('/admin/agent#tasks');
        } elseif ($action === 'reset_testing_database') {
            $res = \Models\AgentTaskScheduler::resetAllTestingData();
            $userId = $_SESSION['user_id'] ?? null;
            AgentToken::logAudit($userId, 'RESET_TESTING_DATABASE', 'all_tables', 0, [], $res);
            if ($res['success']) {
                $_SESSION['agent_success'] = $res['message'];
            } else {
                $_SESSION['flash_error'] = $res['message'];
            }
            redirect('/admin/agent#tasks');
        } elseif ($action === 'delete_task') {
            $taskId = (int)($_POST['task_id'] ?? 0);
            $existingTask = AgentTask::findById($taskId);
            if ($existingTask) {
                AgentTask::delete($taskId);
                $userId = $_SESSION['user_id'] ?? null;
                AgentToken::logAudit($userId, 'ADMIN_DELETE_TASK', 'ai_agent_tasks', $taskId, $existingTask, ['deleted' => true]);
                $_SESSION['agent_success'] = 'Đã xóa kế hoạch làm việc.';
            }
            redirect('/admin/agent#tasks');
        } elseif ($action === 'toggle_task_complete') {
            $taskId = (int)($_POST['task_id'] ?? 0);
            $isCompleted = isset($_POST['is_completed']) ? (!empty($_POST['is_completed']) ? true : false) : null;
            $notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : null;
            $updated = AgentTask::toggleComplete($taskId, $isCompleted, $notes);

            if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                header('Content-Type: application/json; charset=utf-8');
                if ($updated) {
                    echo json_encode(['success' => true, 'task' => $updated, 'stats' => AgentTask::getStats()], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Không tìm thấy kế hoạch.'], JSON_UNESCAPED_UNICODE);
                }
                exit;
            }
            redirect('/admin/agent#tasks', 'Đã cập nhật trạng thái hoàn thành.');
        }

        return $errors;
    }

    private static function handleCreateTask(): void {
        $content = trim((string)($_POST['task_content'] ?? ''));
        $category = trim((string)($_POST['task_category'] ?? 'Nội dung'));
        $priority = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'medium';
        $cycleType = in_array($_POST['task_cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) ? $_POST['task_cycle_type'] : 'daily';
        $sessionSlot = in_array($_POST['task_session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) ? $_POST['task_session_slot'] : 'morning';
        $scheduledDate = !empty($_POST['task_scheduled_date']) ? trim((string)$_POST['task_scheduled_date']) : null;
        $monthNum = isset($_POST['task_month_num']) ? (int)$_POST['task_month_num'] : 1;
        $weekNum = !empty($_POST['task_week_num']) ? (int)$_POST['task_week_num'] : null;
        $isAdHoc = !empty($_POST['task_is_ad_hoc']) || $cycleType === 'adhoc' ? 1 : 0;
        $phase = !empty($_POST['task_phase']) ? trim((string)$_POST['task_phase']) : null;
        $deadline = !empty($_POST['task_deadline']) ? trim((string)$_POST['task_deadline']) : ($scheduledDate ?: null);
        $notes = isset($_POST['task_notes']) ? trim((string)$_POST['task_notes']) : null;
        $isCompleted = !empty($_POST['task_is_completed']) ? 1 : 0;

        if ($content === '') {
            $_SESSION['flash_error'] = 'Nội dung kế hoạch không được để trống.';
        } else {
            $taskId = AgentTask::create([
                'content' => $content, 'category' => $category, 'priority' => $priority,
                'cycle_type' => $cycleType, 'session_slot' => $sessionSlot, 'scheduled_date' => $scheduledDate,
                'month_num' => $monthNum, 'week_num' => $weekNum, 'is_ad_hoc' => $isAdHoc,
                'phase' => $phase, 'deadline' => $deadline, 'notes' => $notes,
                'is_completed' => $isCompleted, 'created_by' => 'admin'
            ]);

            $userId = $_SESSION['user_id'] ?? null;
            AgentToken::logAudit($userId, 'ADMIN_CREATE_TASK', 'ai_agent_tasks', $taskId, [], [
                'content' => $content, 'category' => $category, 'priority' => $priority,
                'cycle_type' => $cycleType, 'session_slot' => $sessionSlot, 'scheduled_date' => $scheduledDate, 'deadline' => $deadline
            ]);
            $_SESSION['agent_success'] = 'Thêm kế hoạch làm việc mới thành công.';
        }
        redirect('/admin/agent#tasks');
    }

    private static function handleCreateAdhocTask(): void {
        $content = trim((string)($_POST['task_content'] ?? ''));
        $category = trim((string)($_POST['task_category'] ?? 'Bổ sung & Phát sinh'));
        $priority = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'high';
        $notes = isset($_POST['task_notes']) ? trim((string)$_POST['task_notes']) : null;
        $scheduledDate = !empty($_POST['task_scheduled_date']) ? trim((string)$_POST['task_scheduled_date']) : date('Y-m-d');

        if ($content === '') {
            $_SESSION['flash_error'] = 'Nội dung nhiệm vụ phát sinh không được để trống.';
        } else {
            $taskId = AgentTask::createAdHocTask($content, $notes, $priority, $scheduledDate, $category);
            $userId = $_SESSION['user_id'] ?? null;
            AgentToken::logAudit($userId, 'ADMIN_CREATE_ADHOC_TASK', 'ai_agent_tasks', $taskId, [], ['content' => $content, 'date' => $scheduledDate]);
            $_SESSION['agent_success'] = 'Đã thêm nhiệm vụ bổ sung/phát sinh vào lịch ngày ' . date('d/m/Y', strtotime($scheduledDate)) . '.';
        }
        redirect('/admin/agent#tasks');
    }

    private static function handleUpdateTask(): void {
        $taskId = (int)($_POST['task_id'] ?? 0);
        $existingTask = AgentTask::findById($taskId);
        if (!$existingTask) {
            redirect('/admin/agent#tasks', 'Không tìm thấy nhiệm vụ cần sửa.', 'error');
        }

        $content = trim((string)($_POST['task_content'] ?? ''));
        $category = trim((string)($_POST['task_category'] ?? 'Nội dung'));
        $priority = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'medium';
        $cycleType = in_array($_POST['task_cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) ? $_POST['task_cycle_type'] : ($existingTask['cycle_type'] ?? 'daily');
        $sessionSlot = in_array($_POST['task_session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) ? $_POST['task_session_slot'] : ($existingTask['session_slot'] ?? 'morning');
        $scheduledDate = array_key_exists('task_scheduled_date', $_POST) ? (!empty($_POST['task_scheduled_date']) ? trim((string)$_POST['task_scheduled_date']) : null) : $existingTask['scheduled_date'];
        $monthNum = isset($_POST['task_month_num']) ? (int)$_POST['task_month_num'] : (int)($existingTask['month_num'] ?? 1);
        $weekNum = array_key_exists('task_week_num', $_POST) ? (!empty($_POST['task_week_num']) ? (int)$_POST['task_week_num'] : null) : $existingTask['week_num'];
        $isAdHoc = array_key_exists('task_is_ad_hoc', $_POST) ? (!empty($_POST['task_is_ad_hoc']) ? 1 : 0) : (int)($existingTask['is_ad_hoc'] ?? 0);
        $phase = array_key_exists('task_phase', $_POST) ? (!empty($_POST['task_phase']) ? trim((string)$_POST['task_phase']) : null) : ($existingTask['phase'] ?? null);
        $deadline = !empty($_POST['task_deadline']) ? trim((string)$_POST['task_deadline']) : null;
        $notes = isset($_POST['task_notes']) ? trim((string)$_POST['task_notes']) : null;
        $isCompleted = !empty($_POST['task_is_completed']) ? 1 : 0;

        if ($content === '') {
            $_SESSION['flash_error'] = 'Nội dung kế hoạch không được để trống.';
        } else {
            AgentTask::update($taskId, [
                'content' => $content, 'category' => $category, 'priority' => $priority,
                'cycle_type' => $cycleType, 'session_slot' => $sessionSlot, 'scheduled_date' => $scheduledDate,
                'month_num' => $monthNum, 'week_num' => $weekNum, 'is_ad_hoc' => $isAdHoc,
                'phase' => $phase, 'deadline' => $deadline, 'notes' => $notes,
                'is_completed' => $isCompleted
            ]);

            $userId = $_SESSION['user_id'] ?? null;
            AgentToken::logAudit($userId, 'ADMIN_UPDATE_TASK', 'ai_agent_tasks', $taskId, $existingTask, [
                'content' => $content, 'category' => $category, 'priority' => $priority,
                'cycle_type' => $cycleType, 'session_slot' => $sessionSlot, 'scheduled_date' => $scheduledDate,
                'deadline' => $deadline, 'is_completed' => $isCompleted
            ]);
            $_SESSION['agent_success'] = 'Cập nhật kế hoạch làm việc thành công.';
        }
        redirect('/admin/agent#tasks');
    }
}
