<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
$pageTitle = 'Victoria Universal — Tư vấn du học Nhật Bản';

require dirname(__DIR__) . '/templates/layouts/head.php';
require dirname(__DIR__) . '/templates/partials/header.php';
?>
<main>
<?php
require dirname(__DIR__) . '/templates/home/hero.php';
require dirname(__DIR__) . '/templates/home/trust.php';
require dirname(__DIR__) . '/templates/home/programs.php';
require dirname(__DIR__) . '/templates/home/process.php';
require dirname(__DIR__) . '/templates/home/contact.php';
?>
</main>
<?php
require dirname(__DIR__) . '/templates/partials/footer.php';
require dirname(__DIR__) . '/templates/partials/floating.php';
require dirname(__DIR__) . '/templates/layouts/end.php';
