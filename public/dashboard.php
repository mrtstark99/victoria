<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
if (!$currentUser) {
    redirect('/login.php');
}
$pageTitle = 'Tài khoản — Victoria Universal';
require dirname(__DIR__) . '/templates/layouts/head.php';
require dirname(__DIR__) . '/templates/partials/header.php';
require dirname(__DIR__) . '/templates/auth/dashboard.php';
require dirname(__DIR__) . '/templates/layouts/end.php';
