<?php
/**
 * @file app/Models/Contact.php
 * @description Contact inquiry model for lead generation and student advisory requests.
 *
 * Layer:
 * - Domain / Persistence Model
 *
 * Responsibilities:
 * - Store incoming consultation and contact inquiries securely.
 * - Retrieve paginated inquiries for admin review.
 * - Update status (new, read, replied, processing, completed, archived).
 * - Maintain internal advisor notes and assignment.
 *
 * Security:
 * - Parameterized PDO prepared statements.
 * - Input fields sanitized before insertion.
 *
 * Dependencies:
 * - Database singleton for SQLite PDO connection.
 *
 * Constraints:
 * - Keep this file focused on a single responsibility.
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

namespace Models;

use Database;
use PDO;

class Contact {
    /** Apply a persistent per-IP submission limit for the public contact form. */
    public static function allowSubmission(string $ip, int $limit = 5, int $windowSeconds = 3600): bool {
        $db = Database::getInstance();
        $db->exec("CREATE TABLE IF NOT EXISTS contact_submission_limits (
            ip_address TEXT PRIMARY KEY,
            window_started_at INTEGER NOT NULL,
            submission_count INTEGER NOT NULL DEFAULT 0
        )");
        $now = time();
        $db->prepare("DELETE FROM contact_submission_limits WHERE window_started_at < ?")
            ->execute([$now - max($windowSeconds, 86400)]);
        $stmt = $db->prepare("INSERT INTO contact_submission_limits (ip_address, window_started_at, submission_count)
            VALUES (?, ?, 0) ON CONFLICT(ip_address) DO NOTHING");
        $stmt->execute([$ip, $now]);
        $stmt = $db->prepare("UPDATE contact_submission_limits SET window_started_at = ?, submission_count = 0
            WHERE ip_address = ? AND window_started_at <= ?");
        $stmt->execute([$now, $ip, $now - $windowSeconds]);
        $stmt = $db->prepare("UPDATE contact_submission_limits SET submission_count = submission_count + 1
            WHERE ip_address = ? AND submission_count < ?");
        $stmt->execute([$ip, $limit]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Create a new contact inquiry record.
     *
     * @param array $data
     * @return int Inserted ID
     */
    public static function create(array $data) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO contacts (
                name, email, phone, subject, message, intake_period,
                japanese_level, status, ip_address, user_agent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', ?, ?)
        ");
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            trim($data['phone'] ?? ''),
            trim($data['subject'] ?? 'Đăng ký tư vấn du học'),
            trim($data['message'] ?? ''),
            trim($data['intake_period'] ?? ''),
            trim($data['japanese_level'] ?? ''),
            $data['ip_address'] ?? '',
            $data['user_agent'] ?? ''
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Retrieve paginated contact submissions with optional status and search filters.
     *
     * @param int $page
     * @param int $perPage
     * @param string $statusFilter
     * @param string $search
     * @return array
     */
    public static function getPaginated($page = 1, $perPage = 20, $statusFilter = '', $search = '') {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];

        if (!empty($statusFilter)) {
            $where[] = "status = ?";
            $params[] = $statusFilter;
        }

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)";
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $db->prepare("SELECT COUNT(*) FROM contacts $whereClause");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $query = "
            SELECT * FROM contacts 
            $whereClause 
            ORDER BY created_at DESC 
            LIMIT $perPage OFFSET $offset
        ";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int)ceil($total / $perPage)
        ];
    }

    /**
     * Find inquiry by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM contacts WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Update inquiry processing status and optional advisor notes.
     *
     * @param int $id
     * @param string $status
     * @param string|null $notes
     * @return bool
     */
    public static function updateStatus($id, $status, $notes = null) {
        $db = Database::getInstance();
        if ($notes !== null) {
            $stmt = $db->prepare("
                UPDATE contacts SET status = ?, notes = ?, updated_at = datetime('now','localtime') 
                WHERE id = ?
            ");
            return $stmt->execute([$status, $notes, (int)$id]);
        }
        $stmt = $db->prepare("
            UPDATE contacts SET status = ?, updated_at = datetime('now','localtime') 
            WHERE id = ?
        ");
        return $stmt->execute([$status, (int)$id]);
    }

    /**
     * Delete inquiry by ID.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}
