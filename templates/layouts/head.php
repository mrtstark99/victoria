<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= \App\Core\Security::escape($pageTitle ?? 'Victoria Universal') ?></title>
  <meta name="description" content="Tư vấn du học Nhật Bản, hỗ trợ giáo dục và chương trình trao đổi sinh viên.">
  <link rel="icon" type="image/svg+xml" href="/assets/images/VICTORIA_LOGO.svg">
<?php
$stylesheets = [
    '01-base.css', '02-components.css', '03-navigation.css', '04-hero-main.css',
    '05-hero-media.css', '06-hero-details.css', '07-programs-a.css', '08-programs-b.css',
    '09-programs-c.css', '10-process.css', '11-contact.css', '12-footer.css',
    '13-floating.css', '14-responsive.css', '15-reveal.css', '16-auth.css',
];
foreach ($stylesheets as $stylesheet):
?>
  <link rel="stylesheet" href="/assets/css/<?= $stylesheet ?>?v=<?= filemtime(dirname(__DIR__, 2) . '/public/assets/css/' . $stylesheet) ?>">
<?php endforeach; ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<?php require dirname(__DIR__) . '/partials/flash.php'; ?>
