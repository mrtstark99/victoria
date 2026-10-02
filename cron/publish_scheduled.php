<?php
/**
 * CLI Cron Script: Publish Scheduled Posts
 * Usage: php cron/publish_scheduled.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$publishedCount = \Models\Post::publishScheduledPosts();
if ($publishedCount > 0) {
    \Controllers\BlogController::generateSitemapFile();
    echo "[" . date('Y-m-d H:i:s') . "] Successfully published {$publishedCount} scheduled post(s) and regenerated sitemap.\n";
} else {
    echo "[" . date('Y-m-d H:i:s') . "] No scheduled posts due for publishing.\n";
}
