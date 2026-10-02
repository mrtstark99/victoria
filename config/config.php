<?php
/**
 * System Configuration
 */

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/error.log');

// Start secure session if not started
if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' && !empty($_SERVER['REMOTE_ADDR'])
                && in_array($_SERVER['REMOTE_ADDR'], array_map('trim', explode(',', getenv('TRUSTED_PROXIES') ?: '')), true)),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Define paths
define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('UPLOAD_URL', '/uploads/');

// Database path
define('DB_PATH', APP_ROOT . '/database/blog.db');

// Security & Environment settings
$envAuthKey = getenv('SECURE_AUTH_KEY') ?: ($_ENV['SECURE_AUTH_KEY'] ?? '');
if (!$envAuthKey) {
    // Generate or use server-specific secret
    $secretFile = APP_ROOT . '/database/.secret_key';
    if (file_exists($secretFile)) {
        $envAuthKey = trim(file_get_contents($secretFile));
    } else {
        $envAuthKey = bin2hex(random_bytes(32));
        @file_put_contents($secretFile, $envAuthKey);
    }
    @chmod($secretFile, 0600);
}
define('SECURE_AUTH_KEY', $envAuthKey);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('MAX_FILE_SIZE', 5242880); // 5MB

// Trusted proxies & Allowed hosts
$trustedProxiesEnv = getenv('TRUSTED_PROXIES') ?: ($_ENV['TRUSTED_PROXIES'] ?? '');
define('TRUSTED_PROXIES', $trustedProxiesEnv ? array_map('trim', explode(',', $trustedProxiesEnv)) : []);

$allowedHostsEnv = getenv('ALLOWED_HOSTS') ?: ($_ENV['ALLOWED_HOSTS'] ?? '');
define('ALLOWED_HOSTS', $allowedHostsEnv ? array_values(array_filter(array_map('trim', explode(',', $allowedHostsEnv)))) : []);

$appUrlEnv = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? '');
define('APP_URL', rtrim($appUrlEnv, '/'));

// File Upload Constraints
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Post Status Allowlist
define('ALLOWED_POST_STATUSES', ['draft', 'published', 'archived', 'ai_draft', 'pending_review', 'approved', 'scheduled']);

// ── SERP Analysis API Configuration ──
// Supported providers: 'serpapi', 'dataforseo', 'google_cse'
$serpProvider = getenv('SERP_API_PROVIDER') ?: ($_ENV['SERP_API_PROVIDER'] ?? 'google_cse');
$serpKey = getenv('SERP_API_KEY') ?: ($_ENV['SERP_API_KEY'] ?? '');
$serpSecondary = getenv('SERP_API_SECONDARY') ?: ($_ENV['SERP_API_SECONDARY'] ?? ''); // CSE ID or DataForSEO password
define('SERP_API_PROVIDER', $serpProvider);
define('SERP_API_KEY', $serpKey);
define('SERP_API_SECONDARY', $serpSecondary);
define('SERP_CACHE_TTL', 86400); // 24 hours

// ── IndexNow & Indexing Configuration ──
$indexNowKey = getenv('INDEXNOW_API_KEY') ?: ($_ENV['INDEXNOW_API_KEY'] ?? bin2hex(random_bytes(16)));
define('INDEXNOW_API_KEY', $indexNowKey);
define('INDEXNOW_ENABLED', (bool)(getenv('INDEXNOW_ENABLED') ?: ($_ENV['INDEXNOW_ENABLED'] ?? true)));

// ── Agent Review Enforcement ──
// When true, Agent must go through ai_draft → pending_review → approved → published
// When false, Agent with posts:publish scope can publish directly
define('AGENT_REQUIRE_REVIEW_BEFORE_PUBLISH', (bool)(getenv('AGENT_REQUIRE_REVIEW') ?: ($_ENV['AGENT_REQUIRE_REVIEW'] ?? true)));

// Default settings fallback
define('DEFAULT_POSTS_PER_PAGE', 6);
define('ADMIN_POSTS_PER_PAGE', 20);
define('SITE_NAME', 'Victoria Universal');
define('SITE_SLOGAN', 'Tư vấn du học Nhật Bản, hỗ trợ giáo dục và chương trình trao đổi sinh viên.');
define('SITE_EMAIL', 'info.duhocvictoria@gmail.com');
define('SITE_PHONE', '0964 808 886');

// Utility autoloader for models/controllers
spl_autoload_register(function ($class) {
    // Standard namespacing conversion
    $classPath = str_replace('\\', '/', $class);
    
    // PSR-4 direct path check
    $appFile = APP_ROOT . '/app/' . $classPath . '.php';
    if (file_exists($appFile)) {
        require_once $appFile;
        return;
    }
    
    // Core class directories fallback
    $directories = [
        APP_ROOT . '/app/Models/',
        APP_ROOT . '/app/Controllers/',
        APP_ROOT . '/app/Controllers/Agent/',
        APP_ROOT . '/app/Helpers/',
    ];
    
    foreach ($directories as $dir) {
        $file = $dir . basename($classPath) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Require security functions globally
require_once APP_ROOT . '/app/Helpers/security.php';
require_once APP_ROOT . '/app/Helpers/seo_helper.php';
require_once APP_ROOT . '/app/Helpers/template_helper.php';
require_once APP_ROOT . '/app/Helpers/analytics_crypto.php';
require_once APP_ROOT . '/app/Helpers/analytics_helper.php';
require_once APP_ROOT . '/app/Helpers/analytics_reporting.php';
require_once APP_ROOT . '/app/Helpers/ai_guidelines_helper.php';
require_once APP_ROOT . '/app/Helpers/post_element_library.php';

require_once APP_ROOT . '/app/Helpers/ui_elements_helper.php';
