<?php
/**
 * AI Agent Work Plan, Real Calendar Scheduling & Task Model
 */

namespace Models;

use Database;
use PDO;

class AgentTask {
    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        $sql = "SELECT * FROM ai_agent_tasks WHERE 1=1";
        $params = [];

        if (isset($filters['status'])) {
            if ($filters['status'] === 'pending') {
                $sql .= " AND is_completed = 0";
            } elseif ($filters['status'] === 'completed') {
                $sql .= " AND is_completed = 1";
            }
        }

        if (!empty($filters['priority']) && in_array($filters['priority'], ['urgent', 'high', 'medium', 'low'], true)) {
            $sql .= " AND priority = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['cycle_type'])) {
            $sql .= " AND cycle_type = ?";
            $params[] = $filters['cycle_type'];
        }

        if (!empty($filters['session_slot'])) {
            $sql .= " AND session_slot = ?";
            $params[] = $filters['session_slot'];
        }

        if (isset($filters['month_num']) && $filters['month_num'] !== '' && $filters['month_num'] !== 'all') {
            $sql .= " AND month_num = ?";
            $params[] = (int)$filters['month_num'];
        }

        if (isset($filters['week_num']) && $filters['week_num'] !== '' && $filters['week_num'] !== 'all') {
            $sql .= " AND week_num = ?";
            $params[] = (int)$filters['week_num'];
        }

        if (isset($filters['is_ad_hoc']) && $filters['is_ad_hoc'] !== '') {
            $sql .= " AND is_ad_hoc = ?";
            $params[] = (int)$filters['is_ad_hoc'];
        }

        if (!empty($filters['scheduled_date'])) {
            $sql .= " AND scheduled_date = ?";
            $params[] = $filters['scheduled_date'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (content LIKE ? OR notes LIKE ? OR category LIKE ? OR phase LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        // Sort: Incomplete first, then Scheduled Date / Deadline ASC, Priority ASC, ID DESC
        $sql .= " ORDER BY 
            is_completed ASC, 
            CASE 
                WHEN scheduled_date IS NOT NULL AND scheduled_date != '' THEN scheduled_date
                WHEN deadline IS NOT NULL AND deadline != '' THEN deadline 
                ELSE '9999-12-31' 
            END ASC,
            CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                WHEN 'low' THEN 4 
                ELSE 5 
            END ASC,
            id DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . (int)$filters['offset'];
            }
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM ai_agent_tasks WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $priority = in_array($data['priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) ? $data['priority'] : 'medium';
        $category = trim((string)($data['category'] ?? 'Khác'));
        if ($category === '') $category = 'Khác';
        
        $cycleType = in_array($data['cycle_type'] ?? '', ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true) 
            ? $data['cycle_type'] 
            : 'daily';
            
        $sessionSlot = in_array($data['session_slot'] ?? '', ['morning', 'afternoon', 'evening', 'full_day'], true) 
            ? $data['session_slot'] 
            : 'morning';
            
        $phase = !empty($data['phase']) ? trim((string)$data['phase']) : null;
        $content = trim((string)($data['content'] ?? ''));
        $scheduledDate = !empty($data['scheduled_date']) ? trim((string)$data['scheduled_date']) : null;
        $deadline = !empty($data['deadline']) ? trim((string)$data['deadline']) : ($scheduledDate ?: null);
        $monthNum = isset($data['month_num']) ? (int)$data['month_num'] : 1;
        $weekNum = isset($data['week_num']) ? (int)$data['week_num'] : null;
        $isAdHoc = !empty($data['is_ad_hoc']) || $cycleType === 'adhoc' ? 1 : 0;
        $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;
        $isCompleted = !empty($data['is_completed']) ? 1 : 0;
        $notes = isset($data['notes']) ? trim((string)$data['notes']) : null;
        $createdBy = ($data['created_by'] ?? '') === 'admin' ? 'admin' : 'agent';
        $completedAt = $isCompleted ? date('Y-m-d H:i:s') : null;

        $stmt = $db->prepare("
            INSERT INTO ai_agent_tasks (
                priority, category, cycle_type, session_slot, scheduled_date, 
                month_num, week_num, phase, content, deadline, 
                is_completed, is_ad_hoc, parent_id, notes, created_by, 
                completed_at, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now','localtime'), datetime('now','localtime'))
        ");
        $stmt->execute([
            $priority, $category, $cycleType, $sessionSlot, $scheduledDate,
            $monthNum, $weekNum, $phase, $content, $deadline,
            $isCompleted, $isAdHoc, $parentId, $notes, $createdBy,
            $completedAt
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $current = self::findById($id);
        if (!$current) return false;

        $priority = in_array($data['priority'] ?? '', ['urgent', 'high', 'medium', 'low'], true) 
            ? $data['priority'] 
            : $current['priority'];
            
        $category = isset($data['category']) ? trim((string)$data['category']) : $current['category'];
        if ($category === '') $category = 'Khác';

        $cycleType = isset($data['cycle_type']) && in_array($data['cycle_type'], ['daily', 'weekly', 'monthly', 'quarterly', 'adhoc'], true)
            ? $data['cycle_type']
            : ($current['cycle_type'] ?? 'daily');

        $sessionSlot = isset($data['session_slot']) && in_array($data['session_slot'], ['morning', 'afternoon', 'evening', 'full_day'], true)
            ? $data['session_slot']
            : ($current['session_slot'] ?? 'morning');

        $scheduledDate = array_key_exists('scheduled_date', $data) ? (!empty($data['scheduled_date']) ? trim((string)$data['scheduled_date']) : null) : $current['scheduled_date'];
        $monthNum = array_key_exists('month_num', $data) ? (int)$data['month_num'] : (int)($current['month_num'] ?? 1);
        $weekNum = array_key_exists('week_num', $data) ? (!empty($data['week_num']) ? (int)$data['week_num'] : null) : $current['week_num'];
        $isAdHoc = array_key_exists('is_ad_hoc', $data) ? (!empty($data['is_ad_hoc']) ? 1 : 0) : (int)($current['is_ad_hoc'] ?? 0);
        $parentId = array_key_exists('parent_id', $data) ? (!empty($data['parent_id']) ? (int)$data['parent_id'] : null) : $current['parent_id'];
        $phase = array_key_exists('phase', $data) ? (!empty($data['phase']) ? trim((string)$data['phase']) : null) : ($current['phase'] ?? null);
        $content = isset($data['content']) ? trim((string)$data['content']) : $current['content'];
        $deadline = array_key_exists('deadline', $data) ? (!empty($data['deadline']) ? trim((string)$data['deadline']) : null) : $current['deadline'];
        
        $isCompleted = array_key_exists('is_completed', $data) ? (!empty($data['is_completed']) ? 1 : 0) : (int)$current['is_completed'];
        
        $completedAt = $current['completed_at'];
        if ($isCompleted && !$current['is_completed']) {
            $completedAt = date('Y-m-d H:i:s');
        } elseif (!$isCompleted) {
            $completedAt = null;
        }

        $notes = array_key_exists('notes', $data) ? trim((string)$data['notes']) : $current['notes'];

        $stmt = $db->prepare("
            UPDATE ai_agent_tasks 
            SET priority = ?, 
                category = ?, 
                cycle_type = ?,
                session_slot = ?,
                scheduled_date = ?,
                month_num = ?,
                week_num = ?,
                is_ad_hoc = ?,
                parent_id = ?,
                phase = ?,
                content = ?, 
                deadline = ?, 
                is_completed = ?, 
                notes = ?, 
                completed_at = ?, 
                updated_at = datetime('now','localtime')
            WHERE id = ?
        ");
        return $stmt->execute([
            $priority, $category, $cycleType, $sessionSlot, $scheduledDate,
            $monthNum, $weekNum, $isAdHoc, $parentId, $phase,
            $content, $deadline, $isCompleted, $notes, $completedAt,
            $id
        ]);
    }

    public static function toggleComplete(int $id, ?bool $isCompleted = null, ?string $notes = null): ?array {
        $current = self::findById($id);
        if (!$current) return null;

        $newState = ($isCompleted !== null) ? ($isCompleted ? 1 : 0) : ($current['is_completed'] ? 0 : 1);
        $completedAt = $newState ? date('Y-m-d H:i:s') : null;
        $updatedNotes = ($notes !== null && $notes !== '') ? $notes : $current['notes'];

        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE ai_agent_tasks 
            SET is_completed = ?, 
                completed_at = ?, 
                notes = ?, 
                updated_at = datetime('now','localtime')
            WHERE id = ?
        ");
        $stmt->execute([$newState, $completedAt, $updatedNotes, $id]);
        return self::findById($id);
    }

    public static function delete(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM ai_agent_tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getStats(): array {
        $db = Database::getInstance();
        
        $total = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks")->fetchColumn();
        $completed = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE is_completed = 1")->fetchColumn();
        $pending = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE is_completed = 0")->fetchColumn();
        $urgentHigh = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE is_completed = 0 AND priority IN ('urgent', 'high')")->fetchColumn();
        $overdue = (int)$db->query("
            SELECT COUNT(*) FROM ai_agent_tasks 
            WHERE is_completed = 0 
              AND deadline IS NOT NULL 
              AND deadline != '' 
              AND date(deadline) < date('now', 'localtime')
        ")->fetchColumn();

        // Counts by cycle types
        $dailyCount = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE cycle_type = 'daily' AND is_completed = 0")->fetchColumn();
        $weeklyCount = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE cycle_type = 'weekly' AND is_completed = 0")->fetchColumn();
        $monthlyCount = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE cycle_type = 'monthly' AND is_completed = 0")->fetchColumn();
        $adHocCount = (int)$db->query("SELECT COUNT(*) FROM ai_agent_tasks WHERE (is_ad_hoc = 1 OR cycle_type = 'adhoc') AND is_completed = 0")->fetchColumn();

        $rate = $total > 0 ? (int)round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'urgent_high' => $urgentHigh,
            'overdue' => $overdue,
            'daily_pending' => $dailyCount,
            'weekly_pending' => $weeklyCount,
            'monthly_pending' => $monthlyCount,
            'adhoc_pending' => $adHocCount,
            'completion_rate' => $rate
        ];
    }

    public static function getCategories(): array {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT DISTINCT category FROM ai_agent_tasks WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: ['Nội dung & Viết bài', 'Tối ưu On-page', 'Kỹ thuật SEO & Index', 'Phân tích & Đo lường', 'Bổ sung & Phát sinh', 'Khác'];
    }

    /**
     * Create an Ad-hoc / Supplementary Task for today or a specific date
     */
    public static function createAdHocTask(string $content, ?string $notes = null, string $priority = 'high', ?string $scheduledDate = null, string $category = 'Bổ sung & Phát sinh'): int {
        $date = $scheduledDate ?: date('Y-m-d');
        return self::create([
            'content' => $content,
            'priority' => in_array($priority, ['urgent', 'high', 'medium', 'low'], true) ? $priority : 'high',
            'category' => $category,
            'cycle_type' => 'adhoc',
            'session_slot' => 'full_day',
            'scheduled_date' => $date,
            'deadline' => $date,
            'month_num' => 1,
            'phase' => '⚡ Phát sinh trong ngày (' . date('d/m/Y', strtotime($date)) . ')',
            'is_ad_hoc' => 1,
            'notes' => $notes ?: 'Nhiệm vụ đột xuất phát sinh (lỗi, cập nhật admin, yêu cầu mới)',
            'created_by' => 'admin'
        ]);
    }

    /**
     * @deprecated Use AgentTaskScheduler::generateRealCalendarSchedule() instead.
     * @see \Models\AgentTaskScheduler::generateRealCalendarSchedule()
     */
    public static function generateRealCalendarSchedule(?string $startMonth = null): array {
        return AgentTaskScheduler::generateRealCalendarSchedule($startMonth);
    }

    /**
     * @deprecated Use AgentTaskScheduler::resetAllTestingData() instead.
     * @see \Models\AgentTaskScheduler::resetAllTestingData()
     */
    public static function resetAllTestingData(): array {
        return AgentTaskScheduler::resetAllTestingData();
    }
}
