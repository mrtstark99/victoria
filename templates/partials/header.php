<header class="header-wrapper" id="main-header">
  <div class="container header-container">
    <a href="/" class="logo-link">
      <img src="/assets/images/VICTORIA_LOGO.svg" class="logo-img" alt="Victoria Universal">
    </a>
    <nav class="nav-menu" aria-label="Điều hướng chính">
      <a href="/" class="nav-menu-link active">Trang chủ</a>
      <a href="/#chuong-trinh" class="nav-menu-link">Chương trình</a>
      <a href="/#quy-trinh" class="nav-menu-link">Quy trình</a>
      <a href="/#hoi-dap" class="nav-menu-link">Hỏi đáp</a>
      <a href="/#lien-he" class="nav-menu-link">Liên hệ</a>
      <?php if ($currentUser): ?>
        <a href="/dashboard.php" class="nav-menu-link">Tài khoản</a>
      <?php else: ?>
        <a href="/login.php" class="nav-menu-link">Đăng nhập</a>
      <?php endif; ?>
    </nav>
    <div class="header-right">
      <a href="/#lien-he" class="btn btn-primary header-cta">Tư vấn miễn phí</a>
      <button class="menu-toggle" id="menu-toggle" aria-label="Mở menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
<div class="mobile-overlay" id="mobile-overlay">
  <nav class="mobile-nav-list" aria-label="Điều hướng di động">
    <a href="/" class="mobile-nav-link">Trang chủ</a>
    <a href="/#chuong-trinh" class="mobile-nav-link">Chương trình</a>
    <a href="/#quy-trinh" class="mobile-nav-link">Quy trình</a>
    <a href="/#hoi-dap" class="mobile-nav-link">Hỏi đáp</a>
    <a href="/#lien-he" class="mobile-nav-link">Liên hệ</a>
    <a href="<?= $currentUser ? '/dashboard.php' : '/login.php' ?>" class="mobile-nav-link">
      <?= $currentUser ? 'Tài khoản' : 'Đăng nhập' ?>
    </a>
  </nav>
  <div class="mobile-nav-footer">
    <a href="/#lien-he" class="btn btn-primary">Tư vấn ngay</a>
    <p class="text-muted mobile-hotline">Hotline: <a href="tel:0964808886">0964 808 886</a></p>
  </div>
</div>
