<?php
$victoriaNav = [
    ['Trang chủ', '/'], ['Chương trình', '/#chuong-trinh'], ['Quy trình', '/#quy-trinh'],
    ['Dịch vụ', '/services'], ['Blog', '/blog'], ['Liên hệ', '/#lien-he']
];
include APP_ROOT . '/views/layouts/partials/head_meta.php';
?>
<body class="victoria-site">
<?php displayFlashMessage(); ?>
<header class="header-wrapper" id="main-header">
  <div class="container header-container">
    <a href="/" class="logo-link"><img src="/assets/images/VICTORIA_LOGO.svg" class="logo-img" alt="Victoria Universal"></a>
    <nav class="nav-menu" aria-label="Điều hướng chính">
      <?php foreach ($victoriaNav as [$label, $url]): ?><a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="nav-menu-link"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?>
      <?php if (isLoggedIn()): ?><a href="/admin" class="nav-menu-link">Quản trị</a><?php else: ?><a href="/login" class="nav-menu-link">Đăng nhập</a><?php endif; ?>
    </nav>
    <div class="header-right">
      <a href="/#lien-he" class="btn btn-primary header-cta">Tư vấn miễn phí</a>
      <button class="menu-toggle" id="menu-toggle" type="button" aria-label="Mở menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
<div class="mobile-overlay" id="mobile-overlay" aria-hidden="true" inert>
  <nav class="mobile-nav-list" aria-label="Điều hướng di động">
    <?php foreach ($victoriaNav as [$label, $url]): ?><a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="mobile-nav-link"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?>
    <a href="<?= isLoggedIn() ? '/admin' : '/login' ?>" class="mobile-nav-link"><?= isLoggedIn() ? 'Quản trị' : 'Đăng nhập' ?></a>
  </nav>
  <div class="mobile-nav-footer"><a href="/#lien-he" class="btn btn-primary">Tư vấn ngay</a><p class="text-muted mobile-hotline">Hotline: <a href="tel:0964808886">0964 808 886</a></p></div>
</div>
