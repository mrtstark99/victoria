<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Flash;
use App\Core\Security;

if ($currentUser) {
    redirect('/dashboard.php');
}

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $ip = Security::clientIp();
    if (!Security::verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
    } elseif ($auth->tooManyAttempts($email, $ip)) {
        $error = 'Bạn đã thử quá nhiều lần. Vui lòng đợi 15 phút.';
    } elseif ($auth->login($email, (string) ($_POST['password'] ?? ''))) {
        $auth->clearAttempts($email, $ip);
        Flash::set('success', 'Đăng nhập thành công.');
        redirect('/dashboard.php');
    } else {
        $auth->recordFailedAttempt($email, $ip);
        $error = 'Email hoặc mật khẩu không đúng.';
    }
}

$pageTitle = 'Đăng nhập — Victoria Universal';
require dirname(__DIR__) . '/templates/layouts/head.php';
require dirname(__DIR__) . '/templates/auth/login-form.php';
require dirname(__DIR__) . '/templates/layouts/end.php';
