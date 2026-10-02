<?php
/**
 * Page Model for Static & Dynamic CMS Pages
 */

namespace Models;

use Database;
use PDO;

class Page {
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.full_name as author_name, u.username as author_username, u.avatar as author_avatar
            FROM pages p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public static function findBySlug($slug) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.full_name as author_name, u.username as author_username, u.avatar as author_avatar
            FROM pages p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.slug = ?
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function getPaginated($page = 1, $perPage = 10, $filters = []) {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT p.*, u.full_name as author_name 
            FROM pages p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.slug LIKE ? OR p.content LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY p.sort_order ASC, p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$perPage;
        $params[] = (int)$offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count($filters = []) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM pages WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR slug LIKE ? OR content LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getStats() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'ai_draft' THEN 1 ELSE 0 END) as ai_draft,
                SUM(CASE WHEN status = 'pending_review' THEN 1 ELSE 0 END) as pending_review
            FROM pages
        ");
        return $stmt->fetch() ?: ['total' => 0, 'published' => 0, 'draft' => 0, 'ai_draft' => 0, 'pending_review' => 0];
    }

    public static function getAllPublished() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT id, title, slug, template, updated_at, created_at, published_at
            FROM pages
            WHERE status = 'published'
            ORDER BY sort_order ASC, title ASC
        ");
        return $stmt->fetchAll();
    }

    public static function create($data) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO pages (
                title, slug, excerpt, content, template, featured_image,
                author_id, status, sort_order, meta_title, meta_description,
                meta_keywords, custom_schema_json, published_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $publishedAt = ($data['status'] ?? 'draft') === 'published' ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;

        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['excerpt'] ?? null,
            $data['content'] ?? '',
            $data['template'] ?? 'default',
            $data['featured_image'] ?? null,
            $data['author_id'] ?? 1,
            $data['status'] ?? 'draft',
            (int)($data['sort_order'] ?? 0),
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['meta_keywords'] ?? null,
            $data['custom_schema_json'] ?? null,
            $publishedAt
        ]);

        $pageId = (int)$db->lastInsertId();

        // Create initial revision
        self::saveRevision($pageId, $data, 'create', $data['changed_by'] ?? 'System');

        return $pageId;
    }

    public static function update($id, $data, $expectedUpdatedAt = null) {
        $db = Database::getInstance();
        
        $existing = self::findById($id);
        if (!$existing) return false;

        $publishedAt = $existing['published_at'];
        if (($data['status'] ?? $existing['status']) === 'published' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $where = $expectedUpdatedAt !== null ? 'WHERE id = ? AND updated_at = ?' : 'WHERE id = ?';
        $stmt = $db->prepare("
            UPDATE pages SET
                title = ?,
                slug = ?,
                excerpt = ?,
                content = ?,
                template = ?,
                featured_image = ?,
                status = ?,
                sort_order = ?,
                meta_title = ?,
                meta_description = ?,
                meta_keywords = ?,
                custom_schema_json = ?,
                published_at = ?,
                updated_at = strftime('%Y-%m-%d %H:%M:%f','now','localtime')
            {$where}
        ");

        $result = $stmt->execute([
            $data['title'] ?? $existing['title'],
            $data['slug'] ?? $existing['slug'],
            $data['excerpt'] ?? $existing['excerpt'],
            $data['content'] ?? $existing['content'],
            $data['template'] ?? $existing['template'],
            $data['featured_image'] ?? $existing['featured_image'],
            $data['status'] ?? $existing['status'],
            isset($data['sort_order']) ? (int)$data['sort_order'] : $existing['sort_order'],
            $data['meta_title'] ?? $existing['meta_title'],
            $data['meta_description'] ?? $existing['meta_description'],
            $data['meta_keywords'] ?? $existing['meta_keywords'],
            $data['custom_schema_json'] ?? $existing['custom_schema_json'],
            $publishedAt,
            (int)$id,
            ...($expectedUpdatedAt !== null ? [$expectedUpdatedAt] : [])
        ]);

        if (!$result || $stmt->rowCount() === 0) return false;

        // Save revision
        self::saveRevision($id, $data, 'update', $data['changed_by'] ?? 'System');

        return $result;
    }

    public static function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public static function incrementViews($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pages SET views = views + 1 WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public static function saveRevision($pageId, $data, $action = 'edit', $changedBy = 'System') {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO page_revisions (
                page_id, title, slug, excerpt, content, meta_title,
                meta_description, meta_keywords, author_id, action, changed_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            (int)$pageId,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['excerpt'] ?? '',
            $data['content'] ?? '',
            $data['meta_title'] ?? '',
            $data['meta_description'] ?? '',
            $data['meta_keywords'] ?? '',
            $data['author_id'] ?? null,
            $action,
            $changedBy
        ]);
    }

    public static function getRevisions($pageId, $limit = 10) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM page_revisions 
            WHERE page_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([(int)$pageId, (int)$limit]);
        return $stmt->fetchAll();
    }
}
