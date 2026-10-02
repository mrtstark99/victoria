<?php
/**
 * Category Database Model
 */

namespace Models;

use Database;
use PDO;

class Category {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT c.*, COUNT(p.id) as post_count 
            FROM categories c 
            LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
            GROUP BY c.id 
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findBySlug($slug) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create($name, $slug, $description = '') {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $slug, $description]);
    }

    public static function update($id, $name, $slug, $description = '') {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE categories 
            SET name = ?, slug = ?, description = ?, updated_at = datetime('now','localtime') 
            WHERE id = ?
        ");
        return $stmt->execute([$name, $slug, $description, $id]);
    }

    public static function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
