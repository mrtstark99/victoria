<?php
/**
 * Cron: Event Monitor — Rank Drop Detection
 * Usage: php cron/event_monitor.php
 * Recommended: Run every 6 hours via crontab
 *
 * Checks analytics opportunities for rank drops and dispatches
 * 'rank_dropped' events to auto-create content refresh tasks.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

use Helpers\EventDispatcher;

echo "[" . date('Y-m-d H:i:s') . "] Event Monitor: Starting rank drop detection...\n";

try {
    // Check if analytics data is available
    if (!function_exists('analyticsPagePerformanceData')) {
        echo "[" . date('Y-m-d H:i:s') . "] Analytics helper not available. Skipping.\n";
        exit(0);
    }

    $perfData = analyticsPagePerformanceData(28, false);
    $eventsDispatched = 0;

    foreach ($perfData['performance'] ?? [] as $item) {
        $url = $item['url'] ?? '';
        $gsc = $item['gsc'] ?? [];

        $position = $gsc['position'] ?? 0;
        $impressions = $gsc['impressions'] ?? 0;

        // Rule: Page was in top 10 (position < 10) but has dropped significantly
        // or page has high impressions but very poor position (>20)
        if ($position > 15 && $impressions > 200) {
            // Check if we already created a task for this URL recently (avoid duplicates)
            $db = \Database::getInstance();
            $checkStmt = $db->prepare(
                "SELECT COUNT(*) FROM agent_event_log WHERE event_name = 'rank_dropped' AND payload LIKE ? AND created_at > datetime('now', '-7 days', 'localtime')"
            );
            $checkStmt->execute(['%' . $url . '%']);
            $recentCount = (int)$checkStmt->fetchColumn();

            if ($recentCount === 0) {
                EventDispatcher::dispatch('rank_dropped', [
                    'url' => $url,
                    'new_position' => $position,
                    'impressions' => $impressions,
                    'reason' => 'Position dropped below 15 with significant impressions'
                ]);
                $eventsDispatched++;
                echo "[" . date('Y-m-d H:i:s') . "] Rank drop detected: {$url} (position: {$position}, impressions: {$impressions})\n";
            }
        }
    }

    // Also check for orphan pages (published but no impressions at all)
    $db = \Database::getInstance();
    $orphanStmt = $db->query("
        SELECT p.id, p.title, p.slug, p.published_at
        FROM posts p
        WHERE p.status = 'published'
        AND p.published_at IS NOT NULL
        AND p.published_at < datetime('now', '-14 days', 'localtime')
        AND p.views < 5
        ORDER BY p.published_at ASC
        LIMIT 10
    ");
    $orphanPosts = $orphanStmt->fetchAll();

    foreach ($orphanPosts as $post) {
        $checkStmt = $db->prepare(
            "SELECT COUNT(*) FROM agent_event_log WHERE event_name = 'rank_dropped' AND payload LIKE ? AND created_at > datetime('now', '-14 days', 'localtime')"
        );
        $checkStmt->execute(['%' . $post['slug'] . '%']);

        if ((int)$checkStmt->fetchColumn() === 0) {
            EventDispatcher::dispatch('rank_dropped', [
                'url' => '/blog/' . $post['slug'],
                'post_id' => (int)$post['id'],
                'title' => $post['title'],
                'reason' => 'Published 14+ days ago with near-zero traffic — likely not indexed or poorly optimized'
            ]);
            $eventsDispatched++;
            echo "[" . date('Y-m-d H:i:s') . "] Low-traffic alert: {$post['title']} (slug: {$post['slug']})\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Event Monitor completed. Dispatched {$eventsDispatched} event(s).\n";

} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    exit(1);
}
