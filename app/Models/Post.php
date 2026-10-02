<?php
/**
 * Post Database Model
 */

namespace Models;

use Database;
use PDO;

class Post {
    public static function getSuggestedSearchTerms(int $limit = 5): array {
        $limit = max(0, min($limit, 10));
        if ($limit === 0) return [];

        $db = Database::getInstance();
        $rows = $db->query("SELECT meta_keywords FROM posts
            WHERE status = 'published' AND meta_keywords IS NOT NULL AND TRIM(meta_keywords) <> ''
            ORDER BY views DESC, COALESCE(published_at, created_at) DESC LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        $keywordGroups = array_map(static function ($value) {
            return array_values(array_filter(array_map('trim', explode(',', $value)), static fn($term) => $term !== ''));
        }, $rows);

        $terms = [];
        $seen = [];
        for ($position = 0; $position < 10 && count($terms) < $limit; $position++) {
            foreach ($keywordGroups as $keywords) {
                $term = $keywords[$position] ?? '';
                $key = mb_strtolower($term, 'UTF-8');
                if ($term === '' || mb_strlen($term, 'UTF-8') > 80 || isset($seen[$key])) continue;
                $seen[$key] = true;
                $terms[] = $term;
                if (count($terms) >= $limit) break;
            }
        }
        return $terms;
    }

    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findBySlug($slug) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.full_name as author_name, u.avatar as author_avatar, u.bio as author_bio, c.name as category_name, c.slug as category_slug 
            FROM posts p 
            LEFT JOIN users u ON p.author_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.slug = ? LIMIT 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function getPaginated($page, $perPage, $filters = []) {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, u.full_name as author_name, u.avatar as author_avatar, c.name as category_name, c.slug as category_slug 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(p.title LIKE ? OR p.slug LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ? OR p.meta_keywords LIKE ? OR u.full_name LIKE ?)";
            $sw = '%' . $filters['search'] . '%';
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
        }

        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$perPage;
        $params[] = (int)$offset;

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count($filters = []) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM posts p LEFT JOIN users u ON p.author_id = u.id";
        $where = [];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(p.title LIKE ? OR p.slug LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ? OR p.meta_keywords LIKE ? OR u.full_name LIKE ?)";
            $sw = '%' . $filters['search'] . '%';
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
            $params[] = $sw;
        }

        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getStats() {
        $db = Database::getInstance();
        
        $total = (int)$db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
        $published = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
        $drafts = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status != 'published'")->fetchColumn();
        $featured = (int)$db->query("SELECT COUNT(*) FROM posts WHERE featured = 1")->fetchColumn();
        $totalViews = (int)$db->query("SELECT COALESCE(SUM(views), 0) FROM posts")->fetchColumn();

        return [
            'total' => $total,
            'published' => $published,
            'drafts' => $drafts,
            'featured' => $featured,
            'views' => $totalViews
        ];
    }

    public static function create($data) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO posts (title, slug, excerpt, content, featured_image, category_id, author_id, status, featured, meta_title, meta_description, meta_keywords, custom_schema_json, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $exec = $stmt->execute([
            $data['title'], $data['slug'], $data['excerpt'] ?? '', $data['content'] ?? '',
            $data['featured_image'] ?? null, $data['category_id'] ?? null, $data['author_id'],
            $data['status'] ?? 'draft', $data['featured'] ?? 0,
            $data['meta_title'] ?? null, $data['meta_description'] ?? null, $data['meta_keywords'] ?? null,
            $data['custom_schema_json'] ?? null,
            $data['published_at'] ?? null
        ]);
        if ($exec) {
            $newId = $db->lastInsertId();
            self::saveRevision($newId, $data, 'created', $data['changed_by'] ?? 'user');
            return $newId;
        }
        return false;
    }

    public static function update($id, $data, $expectedUpdatedAt = null) {
        $db = Database::getInstance();
        $oldPost = self::findById($id);
        if (!$oldPost) return false;

        $where = $expectedUpdatedAt !== null ? 'WHERE id = ? AND updated_at = ?' : 'WHERE id = ?';
        $stmt = $db->prepare("
            UPDATE posts 
            SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, category_id = ?, status = ?, featured = ?, 
                meta_title = ?, meta_description = ?, meta_keywords = ?, custom_schema_json = ?, published_at = ?, updated_at = strftime('%Y-%m-%d %H:%M:%f','now','localtime')
            {$where}
        ");
        $exec = $stmt->execute([
            $data['title'], $data['slug'], $data['excerpt'], $data['content'],
            $data['featured_image'], $data['category_id'], $data['status'], $data['featured'],
            $data['meta_title'], $data['meta_description'], $data['meta_keywords'],
            $data['custom_schema_json'] ?? $oldPost['custom_schema_json'] ?? null,
            $data['published_at'],
            $id,
            ...($expectedUpdatedAt !== null ? [$expectedUpdatedAt] : [])
        ]);
        if ($exec && $stmt->rowCount() > 0) {
            self::saveRevision($id, $data, 'updated', $data['changed_by'] ?? 'user');
            return true;
        }
        return false;
    }

    public static function saveRevision($postId, $data, $action, $changedBy) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO post_revisions (post_id, title, slug, excerpt, content, meta_title, meta_description, meta_keywords, author_id, action, changed_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $postId, $data['title'], $data['slug'], $data['excerpt'] ?? '', $data['content'] ?? '',
            $data['meta_title'] ?? null, $data['meta_description'] ?? null, $data['meta_keywords'] ?? null,
            $data['author_id'] ?? null, $action, $changedBy
        ]);
    }

    public static function incrementViews($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getRevisions($postId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM post_revisions WHERE post_id = ? ORDER BY created_at DESC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public static function publishScheduledPosts() {
        $lockFile = sys_get_temp_dir() . '/blog_publish_scheduled.lock';
        if (php_sapi_name() !== 'cli' && file_exists($lockFile) && (time() - filemtime($lockFile) < 60)) {
            return 0;
        }
        @touch($lockFile);

        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE posts 
            SET status = 'published', updated_at = datetime('now','localtime') 
            WHERE status = 'scheduled' AND published_at IS NOT NULL AND published_at <= datetime('now','localtime')
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }
}
