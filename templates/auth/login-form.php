<main class="auth-page">
  <img class="auth-watermark" src="/assets/images/VICTORIA_LOGO.svg" alt="" aria-hidden="true">
  <section class="auth-card" aria-labelledby="login-title">
    <a href="/" class="auth-logo">
      <img src="/assets/images/VICTORIA_LOGO.svg" alt="Victoria Universal">
    </a>
    <span class="home-kicker">CHÀO MỪNG TRỞ LẠI</span>
    <h1 id="login-title">Đăng nhập tài khoản</h1>
    <p class="text-muted">Theo dõi thông tin và gửi yêu cầu tư vấn nhanh hơn.</p>
    <?php if (!empty($error)): ?>
      <div class="form-error" role="alert"><?= \App\Core\Security::escape($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login.php" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrfToken() ?>">
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" id="email" name="email" type="email" maxlength="190" required autocomplete="email" value="<?= \App\Core\Security::escape($email ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mật khẩu</label>
        <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password">
      </div>
      <button class="btn btn-primary auth-submit" type="submit">Đăng nhập</button>
    </form>
    <p class="auth-switch">Chưa có tài khoản? <a href="/register.php">Đăng ký ngay</a></p>
    <a class="auth-back" href="/">← Trở về trang chủ</a>
  </section>
</main>
