<?php
/**
 * @file public/index.php
 * @description Main Front Controller with route dispatching for Bright Education.
 *
 * Layer:
 * - Presentation / Front Controller & Routing
 *
 * Responsibilities:
 * - Bootstrap application configuration and database connection.
 * - Parse incoming HTTP request URI and dispatch to corresponding controllers.
 * - Serve public blog, services, institutional pages, contact forms, AI agent endpoint, and admin panel.
 *
 * Security:
 * - Route-level parameter sanitization and strictly bounded regex matching.
 * - Global exception catching with error logging and generic 500 responses.
 *
 * Dependencies:
 * - config/config.php, config/database.php
 * - Controllers namespace (BlogController, ServiceController, ContactController, PageController, Admin controllers)
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

// If running in PHP built-in server and file exists, serve it directly
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_file($filePath) && parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) !== '/sitemap.xml') {
        return false;
    }
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

// Route parser
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$db = Database::getInstance(); // Trigger DB schema initialization

// Dispatches routes
try {
    // ----------------------------------------------------
    // Public Front Pages
    // ----------------------------------------------------
    if ($path === '/') {
        (new Controllers\BlogController())->index();
    } elseif ($path === '/blog') {
        (new Controllers\BlogController())->blogList();
    } elseif (preg_match('#^/blog/([a-z0-9-]+)$#i', $path, $matches)) {
        (new Controllers\BlogController())->showPost($matches[1]);
    } elseif (preg_match('#^/page/([a-z0-9-]+)$#i', $path, $matches)) {
        (new Controllers\BlogController())->showPage($matches[1]);
    } elseif (preg_match('#^/category/([a-z0-9-]+)$#i', $path, $matches)) {
        (new Controllers\BlogController())->showCategory($matches[1]);
    } elseif ($path === '/services') {
        (new Controllers\ServiceController())->index();
    } elseif (preg_match('#^/services/([a-z0-9-]+)$#i', $path, $matches)) {
        (new Controllers\ServiceController())->show($matches[1]);
    } elseif ($path === '/contact') {
        (new Controllers\ContactController())->index();
    } elseif ($path === '/api/contact') {
        (new Controllers\ContactController())->submit();
    } elseif ($path === '/about') {
        (new Controllers\PageController())->about();
    } elseif ($path === '/schools') {
        (new Controllers\PageController())->schools();
    } elseif ($path === '/courses') {
        (new Controllers\PageController())->courses();
    } elseif ($path === '/process') {
        (new Controllers\PageController())->process();
    } elseif ($path === '/documents') {
        (new Controllers\PageController())->documents();
    } elseif ($path === '/cost' || $path === '/pricing') {
        (new Controllers\PageController())->cost();
    } elseif ($path === '/consultation') {
        (new Controllers\PageController())->consultation();
    } elseif ($path === '/qa') {
        (new Controllers\PageController())->qa();
    } elseif ($path === '/sitemap.xml') {
        (new Controllers\SitemapController())->sitemap();
    } elseif ($path === '/robots.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        $robotsFile = __DIR__ . '/robots.txt';
        if (file_exists($robotsFile)) {
            readfile($robotsFile);
        } else {
            echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\n";
        }
        exit;
    } elseif ($path === '/api/agent' || $path === '/api/agent.php') {
        require __DIR__ . '/api/agent.php';
        exit;
    } elseif ($path === '/login') {
        $ctrl = new Controllers\AuthController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->login() : $ctrl->showLogin();
    } elseif ($path === '/logout') {
        (new Controllers\AuthController())->logout();
    }

    // ----------------------------------------------------
    // Admin Dashboard & Operations
    // ----------------------------------------------------
    elseif ($path === '/admin' || $path === '/admin/dashboard') {
        (new Controllers\AdminDashboardController())->index();
    } elseif ($path === '/admin/analytics') {
        (new Controllers\AdminAnalyticsController())->analytics();
    } elseif ($path === '/admin/analytics/settings') {
        (new Controllers\AdminAnalyticsController())->saveAnalyticsSettings();
    } elseif ($path === '/admin/profile') {
        $ctrl = new Controllers\AdminProfileController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->updateProfile() : $ctrl->profile();
    } elseif ($path === '/admin/profile/password') {
        (new Controllers\AdminProfileController())->updatePassword();
    } elseif ($path === '/admin/post-sidebar') {
        $ctrl = new Controllers\AdminSidebarController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->save() : $ctrl->index();
    } elseif ($path === '/admin/navigation') {
        $ctrl = new Controllers\AdminNavigationController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->save() : $ctrl->index();
    } elseif ($path === '/admin/brand' || $path === '/admin/settings') {
        $ctrl = new Controllers\AdminBrandController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->save() : $ctrl->index();
    } elseif ($path === '/admin/agent') {
        (new Controllers\AdminAgentController())->manageAgent();
    } elseif ($path === '/admin/agent/download-manual') {
        (new Controllers\AdminAgentManualController())->download();
    }

    // Posts Management
    elseif ($path === '/admin/posts') {
        (new Controllers\AdminPostController())->index();
    } elseif ($path === '/admin/posts/create') {
        (new Controllers\AdminPostController())->create();
    } elseif (preg_match('#^/admin/posts/edit/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminPostController())->edit((int)$matches[1]);
    } elseif (preg_match('#^/admin/posts/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminPostController())->delete((int)$matches[1]);
    }

    // Services Management
    elseif ($path === '/admin/services') {
        (new Controllers\AdminServiceController())->index();
    } elseif ($path === '/admin/services/create') {
        (new Controllers\AdminServiceController())->create();
    } elseif ($path === '/admin/services/store') {
        (new Controllers\AdminServiceController())->store();
    } elseif (preg_match('#^/admin/services/edit/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminServiceController())->edit((int)$matches[1]);
    } elseif (preg_match('#^/admin/services/update/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminServiceController())->update((int)$matches[1]);
    } elseif (preg_match('#^/admin/services/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminServiceController())->delete((int)$matches[1]);
    }

    // Consultations & Contacts Management
    elseif ($path === '/admin/contacts') {
        (new Controllers\AdminContactController())->index();
    } elseif ($path === '/admin/contacts/update-status') {
        (new Controllers\AdminContactController())->updateStatus();
    } elseif (preg_match('#^/admin/contacts/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminContactController())->delete((int)$matches[1]);
    }

    // Pages Management
    elseif ($path === '/admin/pages') {
        (new Controllers\AdminPageController())->index();
    } elseif ($path === '/admin/pages/create') {
        (new Controllers\AdminPageController())->create();
    } elseif (preg_match('#^/admin/pages/edit/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminPageController())->edit((int)$matches[1]);
    } elseif (preg_match('#^/admin/pages/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminPageController())->delete((int)$matches[1]);
    }

    // Categories Management
    elseif ($path === '/admin/categories') {
        (new Controllers\AdminCategoryController())->index();
    } elseif ($path === '/admin/categories/create') {
        (new Controllers\AdminCategoryController())->create();
    } elseif (preg_match('#^/admin/categories/edit/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminCategoryController())->edit((int)$matches[1]);
    } elseif (preg_match('#^/admin/categories/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminCategoryController())->delete((int)$matches[1]);
    }

    // SEO Strategy Management
    elseif ($path === '/admin/seo') {
        (new Controllers\AdminSEOController())->index();
    } elseif ($path === '/admin/seo/cluster') {
        (new Controllers\AdminSEOController())->createCluster();
    } elseif ($path === '/admin/seo/keyword') {
        (new Controllers\AdminSEOController())->createKeyword();
    } elseif (preg_match('#^/admin/seo/keyword/delete/(\d+)$#', $path, $matches)) {
        (new Controllers\AdminSEOController())->deleteKeyword((int)$matches[1]);
    }

    // 404 Fallback
    else {
        header('HTTP/1.0 404 Not Found');
        die('Page not found');
    }
} catch (\Throwable $e) {
    error_log('Front controller error: ' . $e);
    header('HTTP/1.0 500 Internal Server Error');
    die('Internal Server Error');
}
