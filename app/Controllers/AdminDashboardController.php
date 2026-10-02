<?php
/**
 * Admin Dashboard Controller
 * Handles: Dashboard overview and AI Agent work plan / task calendar.
 * Analytics moved to AdminAnalyticsController.
 * Profile management moved to AdminProfileController.
 */

namespace Controllers;

use Models\Post;
use Models\Category;
use Models\AgentToken;
use Models\AgentTask;

class AdminDashboardController {

    public function index() {
        requireEditor();
        $db = \Database::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleTaskAction();
        }

        $totalPosts     = Post::count();
        $publishedPosts = Post::count(['status' => 'published']);
        $totalCats      = count(Category::getAll());
        $totalViews     = (int)$db->query("SELECT SUM(views) FROM posts")->fetchColumn();

        $logs = $db->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 5")->fetchAll();
        $analytics = analyticsDashboardData(28);

        $taskFilters = [];
        if (!empty($_GET['task_status'])   && in_array($_GET['task_status'],   ['pending', 'completed'], true)) $taskFilters['status']   = $_GET['task_status'];
        if (!empty($_GET['task_priority']) && in_array($_GET['task_priority'], ['urgent', 'high', 'medium', 'low'], true)) $taskFilters['priority'] = $_GET['task_priority'];
        if (!empty($_GET['task_category']) && $_GET['task_category'] !== 'all') $taskFilters['category'] = $_GET['task_category'];
        if (!empty($_GET['task_search'])) $taskFilters['search'] = trim($_GET['task_search']);

        $tasks          = AgentTask::getAll();
        $taskStats      = AgentTask::getStats();
        $taskCategories = AgentTask::getCategories();

        view('admin/dashboard', [
            'total_posts'     => $totalPosts,
            'published_posts' => $publishedPosts,
            'total_cats'      => $totalCats,
            'total_views'     => $totalViews,
            'recent_logs'     => $logs,
            'analytics'       => $analytics,
            'tasks'           => $tasks,
            'task_stats'      => $taskStats,
            'task_categories' => $taskCategories,
            'task_filters'    => $taskFilters,
            'page_title'      => 'Bảng điều khiển quản trị'
        ]);
    }

    private function handleTaskAction(): void {
        $action = $_POST['action'] ?? '';
        $isAjax = !empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if (!in_array($action, ['create_task', 'create_adhoc_task', 'update_task', 'delete_task', 'toggle_task_complete', 'generate_calendar_schedule', 'seed_standard_roadmap', 'reset_testing_database'], true)) {
            return;
        }

        if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'CSRF token không hợp lệ']);
                exit;
            }
            redirect('/admin', 'CSRF token không hợp lệ', 'error');
        }

        if ($action === 'create_task') {
            $content = trim($_POST['task_content'] ?? '');
            if ($content !== '') {
                $priority  = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'medium';
                $category  = trim($_POST['task_category'] ?? 'Nội dung');
                $cycleType = in_array($_POST['task_cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) ? $_POST['task_cycle_type'] : 'daily';
                $sessionSlot = in_array($_POST['task_session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) ? $_POST['task_session_slot'] : 'morning';
                $scheduledDate = !empty($_POST['task_scheduled_date']) ? trim($_POST['task_scheduled_date']) : null;
                $monthNum = isset($_POST['task_month_num']) ? (int)$_POST['task_month_num'] : 1;
                $weekNum = !empty($_POST['task_week_num']) ? (int)$_POST['task_week_num'] : null;
                $phase = !empty($_POST['task_phase']) ? trim($_POST['task_phase']) : null;
                $deadline = !empty($_POST['task_deadline']) ? $_POST['task_deadline'] : null;
                $notes = trim($_POST['task_notes'] ?? '');

                $taskId = AgentTask::create([
                    'priority'       => $priority,
                    'category'       => $category,
                    'content'        => $content,
                    'cycle_type'     => $cycleType,
                    'session_slot'   => $sessionSlot,
                    'scheduled_date' => $scheduledDate,
                    'month_num'      => $monthNum,
                    'week_num'       => $weekNum,
                    'phase'          => $phase,
                    'deadline'       => $deadline,
                    'notes'          => $notes,
                    'created_by'     => 'admin'
                ]);

                AgentToken::logAudit($_SESSION['user_id'] ?? 1, 'ADMIN_CREATE_TASK', 'ai_agent_tasks', $taskId, [], [
                    'content' => $content, 'category' => $category, 'priority' => $priority, 'cycle_type' => $cycleType
                ]);
                $_SESSION['flash_success'] = 'Đã thêm kế hoạch làm việc mới thành công.';
            } else {
                $_SESSION['flash_error'] = 'Vui lòng nhập nội dung kế hoạch.';
            }
            redirect('/admin#tasks');
        }

        if ($action === 'create_adhoc_task') {
            $content = trim((string)($_POST['task_content'] ?? ''));
            $category = trim((string)($_POST['task_category'] ?? 'Bổ sung & Phát sinh'));
            $priority = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'high';
            $notes = isset($_POST['task_notes']) ? trim((string)$_POST['task_notes']) : null;
            $scheduledDate = !empty($_POST['task_scheduled_date']) ? trim((string)$_POST['task_scheduled_date']) : date('Y-m-d');

            if ($content === '') {
                $_SESSION['flash_error'] = 'Nội dung nhiệm vụ phát sinh không được để trống.';
            } else {
                $taskId = AgentTask::createAdHocTask($content, $notes, $priority, $scheduledDate, $category);
                $userId = $_SESSION['user_id'] ?? 1;
                AgentToken::logAudit($userId, 'ADMIN_CREATE_ADHOC_TASK', 'ai_agent_tasks', $taskId, [], ['content' => $content, 'date' => $scheduledDate]);
                $_SESSION['flash_success'] = 'Đã thêm nhiệm vụ bổ sung/phát sinh vào lịch ngày ' . date('d/m/Y', strtotime($scheduledDate)) . '.';
            }
            redirect('/admin#tasks');
        }

        if ($action === 'update_task') {
            $taskId  = (int)($_POST['task_id'] ?? 0);
            $content = trim($_POST['task_content'] ?? '');
            if ($taskId > 0 && $content !== '') {
                $existingTask = AgentTask::findById($taskId);
                if (!$existingTask) {
                    redirect('/admin#tasks', 'Không tìm thấy nhiệm vụ cần sửa.', 'error');
                }

                $priority  = in_array($_POST['task_priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $_POST['task_priority'] : 'medium';
                $category  = trim($_POST['task_category'] ?? 'Nội dung');
                $cycleType = in_array($_POST['task_cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) ? $_POST['task_cycle_type'] : ($existingTask['cycle_type'] ?? 'daily');
                $sessionSlot = in_array($_POST['task_session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) ? $_POST['task_session_slot'] : ($existingTask['session_slot'] ?? 'morning');
                $scheduledDate = array_key_exists('task_scheduled_date', $_POST) ? (!empty($_POST['task_scheduled_date']) ? trim((string)$_POST['task_scheduled_date']) : null) : $existingTask['scheduled_date'];
                $monthNum = isset($_POST['task_month_num']) ? (int)$_POST['task_month_num'] : (int)($existingTask['month_num'] ?? 1);
                $weekNum = array_key_exists('task_week_num', $_POST) ? (!empty($_POST['task_week_num']) ? (int)$_POST['task_week_num'] : null) : $existingTask['week_num'];
                $isAdHoc = array_key_exists('task_is_ad_hoc', $_POST) ? (!empty($_POST['task_is_ad_hoc']) ? 1 : 0) : (int)($existingTask['is_ad_hoc'] ?? 0);
                $phase = array_key_exists('task_phase', $_POST) ? (!empty($_POST['task_phase']) ? trim((string)$_POST['task_phase']) : null) : ($existingTask['phase'] ?? null);
                $deadline  = !empty($_POST['task_deadline']) ? $_POST['task_deadline'] : null;
                $notes     = trim($_POST['task_notes'] ?? '');
                $isCompleted = !empty($_POST['task_is_completed']) ? 1 : 0;

                AgentTask::update($taskId, [
                    'content'        => $content,
                    'category'       => $category,
                    'priority'       => $priority,
                    'cycle_type'     => $cycleType,
                    'session_slot'   => $sessionSlot,
                    'scheduled_date' => $scheduledDate,
                    'month_num'      => $monthNum,
                    'week_num'       => $weekNum,
                    'is_ad_hoc'      => $isAdHoc,
                    'phase'          => $phase,
                    'deadline'       => $deadline,
                    'notes'          => $notes,
                    'is_completed'   => $isCompleted
                ]);

                AgentToken::logAudit($_SESSION['user_id'] ?? 1, 'ADMIN_UPDATE_TASK', 'ai_agent_tasks', $taskId, $existingTask, ['content' => $content]);
                $_SESSION['flash_success'] = 'Đã cập nhật kế hoạch làm việc thành công.';
            } else {
                $_SESSION['flash_error'] = 'Dữ liệu kế hoạch không hợp lệ.';
            }
            redirect('/admin#tasks');
        }

        if ($action === 'generate_calendar_schedule' || $action === 'seed_standard_roadmap') {
            $startMonth = !empty($_POST['start_month']) ? trim((string)$_POST['start_month']) : date('Y-m');
            $res = \Models\AgentTaskScheduler::generateRealCalendarSchedule($startMonth);
            $userId = $_SESSION['user_id'] ?? 1;
            AgentToken::logAudit($userId, 'GENERATE_REAL_SCHEDULE', 'ai_agent_tasks', 0, [], $res);
            $_SESSION['flash_success'] = "Đã lên lịch biểu thực tế thành công bắt đầu từ Tháng 1 ({$res['start_month']}). Đã tạo {$res['created_tasks']} nhiệm vụ.";
            redirect('/admin#tasks');
        }

        if ($action === 'reset_testing_database') {
            $res = \Models\AgentTaskScheduler::resetAllTestingData();
            $userId = $_SESSION['user_id'] ?? 1;
            AgentToken::logAudit($userId, 'RESET_TESTING_DATABASE', 'all_tables', 0, [], $res);
            if ($res['success']) {
                $_SESSION['flash_success'] = $res['message'];
            } else {
                $_SESSION['flash_error'] = $res['message'];
            }
            redirect('/admin#tasks');
        }

        if ($action === 'delete_task') {
            $taskId = (int)($_POST['task_id'] ?? 0);
            if ($taskId > 0) {
                $oldTask = AgentTask::findById($taskId);
                AgentTask::delete($taskId);
                AgentToken::logAudit($_SESSION['user_id'] ?? 1, 'ADMIN_DELETE_TASK', 'ai_agent_tasks', $taskId, $oldTask ?: [], ['deleted' => true]);
                $_SESSION['flash_success'] = 'Đã xóa kế hoạch làm việc.';
            }
            redirect('/admin#tasks');
        }

        if ($action === 'toggle_task_complete') {
            $taskId     = (int)($_POST['task_id'] ?? 0);
            $isCompleted = !empty($_POST['is_completed']) ? 1 : 0;
            $notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : null;
            if ($taskId > 0) {
                AgentTask::toggleComplete($taskId, $isCompleted, $notes);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'stats' => AgentTask::getStats()]);
                    exit;
                }
            } elseif ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
                exit;
            }
            redirect('/admin#tasks');
        }
    }

    // Compatibility proxies
    public function analytics() {
        (new AdminAnalyticsController())->analytics();
    }

    public function saveAnalyticsSettings() {
        (new AdminAnalyticsController())->saveAnalyticsSettings();
    }

    public function profile() {
        (new AdminProfileController())->profile();
    }

    public function updateProfile() {
        (new AdminProfileController())->updateProfile();
    }

    public function updatePassword() {
        (new AdminProfileController())->updatePassword();
    }
}
