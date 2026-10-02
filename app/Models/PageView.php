<?php
/**
 * PageView Model for tracking local visitor stats
 */

namespace Models;

class PageView {
    public static function record($postId = null) {
        $db = \Database::getInstance();
        
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Clean query parameters to keep the path uniform (e.g. /?page=2 -> /)
        $parsedUrl = parse_url($url, PHP_URL_PATH) ?: '/';

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Exclude admin pages from analytics views to avoid skewing metrics
        if (strpos($parsedUrl, '/admin') === 0) {
            return;
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO page_views (post_id, url, ip_address, user_agent, referer)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$postId, $parsedUrl, $ip, $ua, $referer]);
        } catch (\PDOException $e) {
            // Silently log or ignore DB errors in analytics to keep user experience smooth
            error_log("Failed to record page view: " . $e->getMessage());
        }
    }
}
