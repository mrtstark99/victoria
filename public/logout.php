<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Security;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Yêu cầu không hợp lệ.');
}
$auth->logout();
redirect('/');
