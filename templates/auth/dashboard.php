<main class="auth-page dashboard-page">
  <section class="auth-card dashboard-card">
    <span class="home-kicker">TÀI KHOẢN VICTORIA</span>
    <h1>Xin chào, <?= \App\Core\Security::escape($currentUser['name']) ?></h1>
    <p class="text-muted">Thông tin tài khoản của bạn được bảo vệ và chỉ dùng cho hoạt động tư vấn.</p>
    <dl class="account-details">
      <div><dt>Họ và tên</dt><dd><?= \App\Core\Security::escape($currentUser['name']) ?></dd></div>
      <div><dt>Email</dt><dd><?= \App\Core\Security::escape($currentUser['email']) ?></dd></div>
      <div><dt>Ngày tham gia</dt><dd><?= date('d/m/Y', strtotime($currentUser['created_at'])) ?></dd></div>
    </dl>
    <div class="dashboard-actions">
      <a href="/#lien-he" class="btn btn-primary">Đăng ký tư vấn</a>
      <form method="post" action="/logout.php">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::csrfToken() ?>">
        <button class="btn btn-outline" type="submit">Đăng xuất</button>
      </form>
    </div>
    <a class="auth-back" href="/">← Trở về trang chủ</a>
  </section>
</main>
