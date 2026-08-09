<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Database;
use App\Core\Security;

$config = require dirname(__DIR__) . '/config/app.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $path = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

ini_set('display_errors', $config['debug'] ? '1' : '0');
ini_set('session.use_strict_mode', '1');
session_name($config['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
Security::secureHeaders();

$database = Database::connect($config['database']);
$auth = new Auth($database);
$currentUser = $auth->user();

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}
