<?php include APP_ROOT . '/views/layouts/header.php'; ?>

<?php
$template = $page['template'] ?? 'default';
$featuredImg = $page['featured_image'] ?? '';
$siteName = getSetting('site_name', SITE_NAME);
$heroTitle = $page['title'];
$heroEyebrow = 'Trang thông tin';
$heroCurrent = $page['title'];
$heroDescription = $page['excerpt'] ?? '';
include APP_ROOT . '/views/layouts/partials/page_hero.php';
?>

<div class="page-layout-container" style="max-width: <?php echo $template === 'fullwidth' ? '1200px' : '900px'; ?>;">
    <article class="page-article-card" style="background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 2.5rem; box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);">
        <!-- Page Header -->
        <header class="page-header" style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-bottom: 0.75rem;">
                <span class="badge" style="background: var(--secondary); color: var(--foreground); font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-weight: 600; border: 1px solid var(--border);">
                    Trang thông tin
                </span>
                <span style="font-size: 0.8rem; color: var(--muted-foreground); display: inline-flex; align-items: center; gap: 0.3rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Cập nhật: <?php echo formatDate($page['updated_at'] ?? $page['created_at'], 'd/m/Y'); ?>
                </span>
            </div>

        </header>

        <!-- Featured Image if provided -->
        <?php if (!empty($featuredImg)): ?>
            <div style="margin-bottom: 2rem; border-radius: 12px; overflow: hidden; max-height: 420px; border: 1px solid var(--border);">
                <img src="<?php echo htmlspecialchars($featuredImg); ?>" alt="<?php echo htmlspecialchars($page['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
        <?php endif; ?>

        <!-- Main Body Content -->
        <div class="article-content-body page-content-body rich-text-wrapper post-content-body prose" style="font-size: 1.05rem; line-height: 1.8; color: var(--foreground);">
            <?php echo sanitizeHtml((string)($page['content'] ?? '')); ?>
        </div>

        <!-- Contact Form Box for 'contact' template -->
        <?php if ($template === 'contact'): ?>
            <div class="contact-form-box" style="margin-top: 3rem; padding: 2rem; background: var(--secondary); border: 1px solid var(--border); border-radius: 12px;">
                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700;">Gửi thông điệp trực tiếp</h3>
                <p style="margin: 0 0 1.5rem 0; font-size: 0.875rem; color: var(--muted-foreground);">
                    Điền biểu mẫu dưới đây, đội ngũ quản trị sẽ liên hệ lại với bạn sớm nhất.
                </p>

                <form onsubmit="event.preventDefault(); alert('Cảm ơn bạn! Thông điệp của bạn đã được gửi thành công.'); this.reset();" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem;">Họ và tên *</label>
                            <input type="text" required class="form-control" placeholder="Nguyễn Văn A">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem;">Địa chỉ Email *</label>
                            <input type="email" required class="form-control" placeholder="email@domain.com">
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem;">Chủ đề</label>
                        <input type="text" class="form-control" placeholder="Hợp tác, tư vấn SEO, đóng góp ý kiến...">
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem;">Nội dung tin nhắn *</label>
                        <textarea required class="form-control" rows="4" placeholder="Nội dung cần trao đổi..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary-action" style="align-self: flex-start; padding: 0.65rem 1.5rem; font-weight: 700;">
                        Gửi liên hệ ngay
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </article>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
