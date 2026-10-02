<?php
/**
 * Migration: Add SEO enhancement columns and event pipeline tables
 * Run: php database/migrate_seo_enhancements.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = Database::getInstance();
$migrationFailed = false;

echo "Starting SEO Enhancements migration...\n";

// 1. Add custom_schema_json column to posts if not exists
try {
    $columns = $db->query("PRAGMA table_info(posts)")->fetchAll();
    $columnNames = array_column($columns, 'name');

    if (!in_array('custom_schema_json', $columnNames)) {
        $db->exec("ALTER TABLE posts ADD COLUMN custom_schema_json TEXT");
        echo "  ✓ Added custom_schema_json column to posts\n";
    } else {
        echo "  - custom_schema_json column already exists\n";
    }
} catch (Exception $e) {
    $migrationFailed = true;
    echo "  ✗ Error adding custom_schema_json: " . $e->getMessage() . "\n";
}

// 2. Create agent_event_hooks table
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS agent_event_hooks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_name TEXT NOT NULL,
            hook_action TEXT NOT NULL,
            hook_config TEXT,
            is_enabled INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        )
    ");
    echo "  ✓ Created agent_event_hooks table\n";
} catch (Exception $e) {
    $migrationFailed = true;
    echo "  ✗ Error creating agent_event_hooks: " . $e->getMessage() . "\n";
}

// 3. Create agent_event_log table
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS agent_event_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_name TEXT NOT NULL,
            payload TEXT,
            triggered_actions TEXT,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        )
    ");
    echo "  ✓ Created agent_event_log table\n";
} catch (Exception $e) {
    $migrationFailed = true;
    echo "  ✗ Error creating agent_event_log: " . $e->getMessage() . "\n";
}

// 4. Insert default event hooks
try {
    $existing = $db->query("SELECT COUNT(*) FROM agent_event_hooks")->fetchColumn();
    if ((int)$existing === 0) {
        $defaultHooks = [
            ['post_published', 'ping_index', '{}'],
            ['post_published', 'link_suggestions', '{}'],
            ['keyword_created', 'serp_research', '{}'],
            ['rank_dropped', 'content_refresh', '{"default_priority":"urgent"}'],
        ];

        $stmt = $db->prepare("INSERT INTO agent_event_hooks (event_name, hook_action, hook_config) VALUES (?, ?, ?)");
        foreach ($defaultHooks as $hook) {
            $stmt->execute($hook);
        }
        echo "  ✓ Inserted " . count($defaultHooks) . " default event hooks\n";
    } else {
        echo "  - Event hooks already populated\n";
    }
} catch (Exception $e) {
    $migrationFailed = true;
    echo "  ✗ Error inserting default hooks: " . $e->getMessage() . "\n";
}

if ($migrationFailed) {
    fwrite(STDERR, "\nMigration failed; see errors above.\n");
    exit(1);
}

$db->exec("PRAGMA user_version = 2");
echo "\nMigration completed successfully!\n";
