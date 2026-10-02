<?php
/** Optional content overrides for dedicated public routes. */
namespace Models;

class FixedPage {
    public const SLUGS = ['about', 'schools', 'courses', 'process', 'documents', 'consultation'];
    public static function get(string $slug): ?array {
        if (!in_array($slug, self::SLUGS, true)) return null;
        $stmt = \Database::getInstance()->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute(['fixed_page_' . $slug]);
        $raw = $stmt->fetchColumn();
        $data = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : null);
        return is_array($data) ? $data : null;
    }
    public static function save(string $slug, array $data): void {
        updateSetting('fixed_page_' . $slug, $data, 'json');
    }
    public static function remove(string $slug): void {
        $db = \Database::getInstance();
        $db->prepare('DELETE FROM settings WHERE setting_key = ?')->execute(['fixed_page_' . $slug]);
    }
}
