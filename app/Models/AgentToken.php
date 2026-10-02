<?php
/**
 * Agent token, audit logs, rate limiters, and idempotency mapping logic
 */

namespace Models;

use Database;
use PDO;

class AgentToken {
    public static function findByHash($hash) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM ai_agent_tokens WHERE token_hash = ? LIMIT 1");
        $stmt->execute([$hash]);
        return $stmt->fetch();
    }

    public static function getActiveTokens() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT t.*, u.full_name as author_name 
            FROM ai_agent_tokens t 
            LEFT JOIN users u ON t.default_author_id = u.id 
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function getAll() {
        return self::getActiveTokens();
    }

    public static function create($name, $rawToken, $defaultAuthorId, $permissions, $allowedIps = null) {
        $db = Database::getInstance();
        $hash = hash('sha256', $rawToken);
        $stmt = $db->prepare("
            INSERT INTO ai_agent_tokens (token_name, token_hash, permissions, default_author_id, allowed_ips)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $hash, $permissions, $defaultAuthorId, $allowedIps]);
    }

    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM ai_agent_tokens WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch() ?: null;
    }

    public static function update($id, $name, $defaultAuthorId, $permissions, $allowedIps = null) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE ai_agent_tokens 
            SET token_name = ?, 
                default_author_id = ?, 
                permissions = ?, 
                allowed_ips = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $defaultAuthorId, $permissions, $allowedIps, (int)$id]);
    }

    public static function revoke($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE ai_agent_tokens SET revoked_at = datetime('now','localtime') WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function logAudit($userId, $action, $table, $recordId, $oldVals = null, $newVals = null) {
        $db = Database::getInstance();
        $ip = getClientIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $db->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $userId, $action, $table, $recordId,
            $oldVals ? json_encode($oldVals, JSON_UNESCAPED_UNICODE) : null,
            $newVals ? json_encode($newVals, JSON_UNESCAPED_UNICODE) : null,
            $ip, $ua
        ]);
    }

    public static function recordUsage($tokenId) {
        $db = Database::getInstance();
        $ip = getClientIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $db->prepare("
            UPDATE ai_agent_tokens 
            SET request_count = COALESCE(request_count, 0) + 1,
                last_ip = ?,
                last_user_agent = ?
            WHERE id = ?
        ");
        return $stmt->execute([$ip, $ua, $tokenId]);
    }

    public static function checkRateLimit($tokenId, $limit = 60) {
        $db = Database::getInstance();
        $minute = date('Y-m-d H:i');
        
        $db->prepare("INSERT OR IGNORE INTO ai_agent_rate_limits (token_id, minute_bucket, request_count) VALUES (?, ?, 0)")
           ->execute([$tokenId, $minute]);
        $db->prepare("UPDATE ai_agent_rate_limits SET request_count = request_count + 1 WHERE token_id = ? AND minute_bucket = ?")
           ->execute([$tokenId, $minute]);
        
        $stmt = $db->prepare("SELECT request_count FROM ai_agent_rate_limits WHERE token_id = ? AND minute_bucket = ?");
        $stmt->execute([$tokenId, $minute]);
        $count = (int)$stmt->fetchColumn();
        return $count <= $limit;
    }

    public static function getIdempotentResponse($key) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT response_payload FROM api_idempotency_keys WHERE idempotency_key = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn();
    }

    public static function saveIdempotentResponse($key, $payload) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT OR REPLACE INTO api_idempotency_keys (idempotency_key, response_payload) VALUES (?, ?)");
        return $stmt->execute([$key, $payload]);
    }

    public static function countActivities() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) FROM audit_logs WHERE action LIKE 'agent_%'");
        return (int)$stmt->fetchColumn();
    }

    public static function getRecentActivities($page = 1, $perPage = 20) {
        $db = Database::getInstance();
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("
            SELECT a.*, u.full_name as author_name, u.avatar as author_avatar
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.action LIKE 'agent_%'
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
