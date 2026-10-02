<?php
/**
 * Automated Test Suite for Blog System
 * Usage: php tests/run_tests.php
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function assertTest($condition, $description) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] $description\n";
    } else {
        $failedTests++;
        echo "  [FAIL] $description\n";
    }
}

echo "=============================================\n";
echo "   BLOG SYSTEM AUTOMATED SUITE (PHP CLI)    \n";
echo "=============================================\n\n";

// 1. Database Integrity & Migration
echo "1. Database & Schema:\n";
$db = Database::getInstance();
$integrity = $db->query("PRAGMA integrity_check")->fetchColumn();
assertTest($integrity === 'ok', "Database integrity check is OK");

$userVer = (int)$db->query("PRAGMA user_version")->fetchColumn();
assertTest($userVer >= 2, "Database schema user_version is >= 2");

// 2. CSRF & Security Helpers
echo "\n2. Security & CSRF Functions:\n";
$_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
$currentToken = $_SESSION[CSRF_TOKEN_NAME];

assertTest(verifyCSRFToken($currentToken) === true, "verifyCSRFToken validates correct token");
assertTest(verifyCSRFToken('invalid_token') === false, "verifyCSRFToken rejects invalid token");
assertTest(function_exists('validateCsrfToken'), "validateCsrfToken alias function exists");
assertTest(validateCsrfToken($currentToken) === true, "validateCsrfToken validates correct token");
assertTest(validateCsrfToken('wrong') === false, "validateCsrfToken rejects invalid token");

$cleanXss = sanitizeHtml('<p>Safe</p><script>alert(1)</script>');
assertTest(strpos($cleanXss, '<script>') === false, "sanitizeHtml strips dangerous script tags");
assertTest(strpos($cleanXss, 'Safe') !== false, "sanitizeHtml preserves safe text content");
assertTest(strpos(sanitizeHtml('<a href=javascript:alert(1)>bad</a>'), 'javascript:') === false, "sanitizeHtml rejects unquoted JavaScript URLs");
assertTest(strpos(sanitizeHtml('<a href="&#106;avascript:alert(1)">bad</a>'), 'javascript:') === false, "sanitizeHtml rejects encoded JavaScript URLs");
assertTest(strpos(sanitizeHtml('<svg><a xlink:href="javascript:alert(1)">bad</a></svg>'), '<svg') === false, "sanitizeHtml rejects SVG markup");
assertTest(strpos(sanitizeHtml('<div class="callout"><div class="callout-title">💡 Mẹo</div></div>'), '💡') === false, "Published UI blocks render without legacy emoji");
assertTest(strpos(sanitizeHtml('<a href="https://example.com" target="_blank">safe</a>'), 'rel="noopener noreferrer"') !== false, "sanitizeHtml preserves safe links with tab isolation");

$keyA = \Controllers\AgentController::idempotencyCacheKey(1, 'create_draft', 'same-key', '{"title":"A"}');
assertTest($keyA !== \Controllers\AgentController::idempotencyCacheKey(2, 'create_draft', 'same-key', '{"title":"A"}'), "Idempotency cache is isolated by agent");
assertTest($keyA !== \Controllers\AgentController::idempotencyCacheKey(1, 'publish_post', 'same-key', '{"title":"A"}'), "Idempotency cache is isolated by action");
assertTest($keyA !== \Controllers\AgentController::idempotencyCacheKey(1, 'create_draft', 'same-key', '{"title":"B"}'), "Idempotency cache is isolated by request body");

// 3. Models
echo "\n3. Database Models:\n";
$posts = \Models\Post::getPaginated(1, 5, ['status' => 'published']);
assertTest(is_array($posts), "Post::getPaginated returns array of published posts");
$suggestions = \Models\Post::getSuggestedSearchTerms();
assertTest(count($suggestions) <= 5, "Home search suggestions respect the display limit");
$suggestionsResolve = true;
foreach ($suggestions as $term) {
    if (\Models\Post::count(['status' => 'published', 'search' => $term]) < 1) $suggestionsResolve = false;
}
assertTest($suggestionsResolve, "Home search suggestions resolve to published posts");

$cats = \Models\Category::getAll();
assertTest(!empty($cats), "Category::getAll returns active categories");

$adminUser = \Models\User::findByUsername('admin');
assertTest($adminUser && $adminUser['role'] === 'admin', "Default admin user exists with admin role");

$tasks = \Models\AgentTask::getStats();
assertTest(isset($tasks['total']), "AgentTask::getStats returns task metrics");

// 4. Base URL & Host Header Validation
echo "\n4. URL Routing & Host Resolution:\n";
$_SERVER['HTTP_HOST'] = 'localhost:5000';
$url1 = \Controllers\BlogController::getBaseUrl();
assertTest($url1 === 'http://localhost:5000' || $url1 === 'https://localhost:5000', "Base URL matches allowed host");

$_SERVER['HTTP_HOST'] = 'evil-attacker.com';
$url2 = \Controllers\BlogController::getBaseUrl();
assertTest(strpos($url2, 'evil-attacker.com') === false, "Base URL rejects untrusted host header");

// 5. Throttled publishScheduledPosts
echo "\n5. Publishing Throttle:\n";
$published1 = \Models\Post::publishScheduledPosts();
assertTest(is_int($published1), "Post::publishScheduledPosts executes successfully");
$published2 = \Models\Post::publishScheduledPosts();
assertTest($published2 === 0, "Post::publishScheduledPosts throttles consecutive execution on web requests");

// 6. Security Files & Directives
echo "\n6. Security Directives & .htaccess:\n";
$uploadHtaccess = dirname(__DIR__) . '/public/uploads/.htaccess';
assertTest(file_exists($uploadHtaccess), "public/uploads/.htaccess exists");
$publicHtaccess = dirname(__DIR__) . '/public/.htaccess';
assertTest(file_exists($publicHtaccess), "public/.htaccess exists");

echo "\n=============================================\n";
echo "SUMMARY: Total: $totalTests | Passed: $passedTests | Failed: $failedTests\n";
echo "=============================================\n";

if ($failedTests > 0) {
    exit(1);
}
