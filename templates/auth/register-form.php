<main class="auth-page">
  <img class="auth-watermark" src="/assets/images/VICTORIA_LOGO.svg" alt="" aria-hidden="true">
  <section class="auth-card" aria-labelledby="register-title">
    <a href="/" class="auth-logo">
      <img src="/assets/images/VICTORIA_LOGO.svg" alt="Victoria Universal">
    </a>
    <span class="home-kicker">ĐỒNG HÀNH CÙNG VICTORIA</span>
    <h1 id="register-title">Tạo tài khoản</h1>
    <p class="text-muted">Lưu thông tin để quá trình tư vấn thuận tiện hơn.</p>
    <form method="post" action="/register.php" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrfToken() ?>">
      <div class="form-group">
        <label class="form-label" for="name">Họ và tên</label>
        <input class="form-control" id="name" name="name" maxlength="80" required autocomplete="name" value="<?= \App\Core\Security::escape($name ?? '') ?>">
        <?php if (isset($errors['name'])): ?><small class="field-error"><?= \App\Core\Security::escape($errors['name']) ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" id="email" name="email" type="email" maxlength="190" required autocomplete="email" value="<?= \App\Core\Security::escape($email ?? '') ?>">
        <?php if (isset($errors['email'])): ?><small class="field-error"><?= \App\Core\Security::escape($errors['email']) ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mật khẩu</label>
        <input class="form-control" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
        <small class="form-hint">Tối thiểu 8 ký tự, gồm chữ và số.</small>
        <?php if (isset($errors['password'])): ?><small class="field-error"><?= \App\Core\Security::escape($errors['password']) ?></small><?php endif; ?>
      </div>
      <div class="form-group">
        <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">
        <?php if (isset($errors['confirmation'])): ?><small class="field-error"><?= \App\Core\Security::escape($errors['confirmation']) ?></small><?php endif; ?>
      </div>
      <button class="btn btn-primary auth-submit" type="submit">Tạo tài khoản</button>
    </form>
    <p class="auth-switch">Đã có tài khoản? <a href="/login.php">Đăng nhập</a></p>
    <a class="auth-back" href="/">← Trở về trang chủ</a>
  </section>
</main>
