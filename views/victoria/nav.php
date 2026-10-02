<?php
$victoriaNavGroups = [
    ['label' => 'Trang chủ', 'url' => '/', 'children' => []],
    ['label' => 'Chương trình', 'url' => '/services', 'children' => [
        ['label' => 'Tổng quan chương trình', 'url' => '/services'],
        ['label' => 'Du học Nhật Bản', 'url' => '/#programs'],
        ['label' => 'Đào tạo tiếng Nhật', 'url' => '/courses'],
        ['label' => 'Quy trình hồ sơ', 'url' => '/#services'],
        ['label' => 'Chi phí du học', 'url' => '/cost'],
    ]],
    ['label' => 'Tư vấn', 'url' => '/consultation', 'children' => [
        ['label' => 'Đăng ký tư vấn', 'url' => '/consultation'],
        ['label' => 'Liên hệ Victoria', 'url' => '/#contact'],
    ]],
    ['label' => 'Tin tức', 'url' => '/blog', 'children' => []],
    ['label' => 'Về Victoria', 'url' => '/about', 'children' => [
        ['label' => 'Giới thiệu', 'url' => '/about'],
        ['label' => 'Trường đối tác', 'url' => '/schools'],
        ['label' => 'Liên hệ', 'url' => '/#contact'],
    ]],
];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<header class="header-wrapper" id="main-header">
  <div class="container header-container">
    <a href="/" class="logo-link"><img src="/assets/images/VICTORIA_LOGO.svg" class="logo-img" alt="Victoria Universal"></a>
    <nav class="nav-menu" aria-label="Điều hướng chính">
      <?php foreach ($victoriaNavGroups as $group): $url = $group['url']; $active = $currentPath === (parse_url($url, PHP_URL_PATH) ?: '/'); ?>
        <div class="nav-group<?= $active ? ' is-active' : '' ?>">
          <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="nav-menu-link<?= $active ? ' active' : '' ?>"<?= $group['children'] ? ' aria-haspopup="true"' : '' ?>><?= htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') ?><?php if ($group['children']): ?><i class="bi bi-chevron-down nav-chevron" aria-hidden="true"></i><?php endif; ?></a>
          <?php if ($group['children']): ?><div class="nav-dropdown" role="menu"><?php foreach ($group['children'] as $child): ?><a role="menuitem" href="<?= htmlspecialchars($child['url'], ENT_QUOTES, 'UTF-8') ?>" class="nav-dropdown-link"><?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?></div><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </nav>
    <div class="header-right"><a href="/#contact" class="btn btn-primary header-cta">Tư vấn miễn phí</a><button class="menu-toggle" id="menu-toggle" type="button" aria-label="Mở menu" aria-expanded="false"><span></span><span></span><span></span></button></div>
  </div>
</header>
<div class="mobile-overlay" id="mobile-overlay" aria-hidden="true" inert>
  <nav class="mobile-nav-list" aria-label="Điều hướng di động">
    <?php foreach ($victoriaNavGroups as $group): ?><div class="mobile-nav-group"><a href="<?= htmlspecialchars($group['url'], ENT_QUOTES, 'UTF-8') ?>" class="mobile-nav-link"><?= htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8') ?></a><?php foreach ($group['children'] as $child): ?><a href="<?= htmlspecialchars($child['url'], ENT_QUOTES, 'UTF-8') ?>" class="mobile-nav-child"><?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?></div><?php endforeach; ?>
    <a href="<?= isLoggedIn() ? '/admin' : '/login' ?>" class="mobile-nav-link"><?= isLoggedIn() ? 'Quản trị' : 'Đăng nhập' ?></a>
  </nav>
  <div class="mobile-nav-footer"><a href="/#contact" class="btn btn-primary">Tư vấn ngay</a><p class="text-muted mobile-hotline">Hotline: <a href="tel:0964808886">0964 808 886</a></p></div>
</div>
