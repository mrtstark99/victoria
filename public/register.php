<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Flash;
use App\Core\Security;

if ($currentUser) {
    redirect('/dashboard.php');
}

$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!Security::verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
    } elseif (!hash_equals($password, (string) ($_POST['password_confirmation'] ?? ''))) {
        $errors['confirmation'] = 'Mật khẩu nhập lại chưa khớp.';
    } else {
        $errors = $auth->register($name, $email, $password);
        if ($errors === []) {
            Flash::set('success', 'Tạo tài khoản thành công.');
            redirect('/dashboard.php');
        }
    }
}

$pageTitle = 'Đăng ký — Victoria Universal';
require dirname(__DIR__) . '/templates/layouts/head.php';
if (isset($errors['form'])) {
    $error = $errors['form'];
    require dirname(__DIR__) . '/templates/partials/form-error.php';
}
require dirname(__DIR__) . '/templates/auth/register-form.php';
require dirname(__DIR__) . '/templates/layouts/end.php';
