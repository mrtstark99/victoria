<?php
// Isolated API contract probe; never connects to the workspace database.
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/app/Helpers/post_element_library.php';
require APP_ROOT . '/app/Helpers/ui_elements_helper.php';
require APP_ROOT . '/app/Helpers/ai_guidelines_helper.php';
require APP_ROOT . '/app/Controllers/Agent/AgentGuidelinesProcessor.php';
class Database {
    public static function getInstance() {
        static $db;
        if (!$db) {
            $db = new PDO('sqlite::memory:');
            $db->exec('CREATE TABLE settings (setting_key TEXT PRIMARY KEY, setting_value TEXT, updated_at TEXT)');
            $db->exec("INSERT INTO settings VALUES ('ai_guidelines_enabled', '" . (($GLOBALS['argv'][2] ?? '1') === '1' ? '1' : '0') . "', NULL)");
            $db->exec("INSERT INTO settings VALUES ('ai_custom_system_prompt', 'Editorial marker', NULL)");
        }
        return $db;
    }
}
(new Controllers\Agent\AgentGuidelinesProcessor())->process($argv[1] ?? 'guidelines', ['posts:draft'], [], []);
