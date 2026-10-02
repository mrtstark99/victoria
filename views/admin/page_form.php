<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<?php
$isEdit = $is_edit ?? false;
$pageData = $page ?? [];
$errors = $errors ?? [];
$revisions = $revisions ?? [];
$formAction = $isEdit ? '/admin/pages/edit/' . $pageData['id'] : '/admin/pages/create';
$siteName = getSetting('site_name', SITE_NAME);
?>

<div class="admin-page-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <a href="/admin/pages" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; color: var(--muted-foreground); text-decoration: none; margin-bottom: 0.35rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Quay lại danh sách trang</span>
            </a>
        </div>
        
        <?php if ($isEdit && $pageData['status'] === 'published'): ?>
            <a href="/page/<?php echo htmlspecialchars($pageData['slug']); ?>" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.45rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Xem trang ngoài website</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo $formAction; ?>" enctype="multipart/form-data" id="pageForm">
        <?php echo csrfField(); ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="expected_updated_at" value="<?php echo htmlspecialchars($pageData['updated_at'] ?? ''); ?>">
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
            <!-- Left Column: Title, Content, SEO -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Main Details Card -->
                <div class="card" style="padding: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="title" style="font-weight: 700; font-size: 0.9rem; margin-bottom: 0.4rem; display: block;">
                            Tiêu đề trang <span style="color: var(--destructive);">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="form-control" 
                            required 
                            value="<?php echo htmlspecialchars($_POST['title'] ?? $pageData['title'] ?? ''); ?>" 
                            placeholder="Ví dụ: Giới thiệu, Chính sách bảo mật, Bảng giá dịch vụ..."
                            style="font-size: 1.1rem; font-weight: 600; padding: 0.65rem 0.85rem;"
                            oninput="autoGenerateSlug(this.value)"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="slug" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Đường dẫn tĩnh (Slug URL)
                        </label>
                        <div style="display: flex; align-items: center; background: var(--secondary); border: 1px solid var(--border); border-radius: var(--radius); padding: 0 0.75rem;">
                            <span style="color: var(--muted-foreground); font-size: 0.85rem; user-select: none;">/page/</span>
                            <input 
                                type="text" 
                                id="slug" 
                                name="slug" 
                                class="form-control" 
                                value="<?php echo htmlspecialchars($_POST['slug'] ?? $pageData['slug'] ?? ''); ?>" 
                                placeholder="gioi-thieu"
                                style="border: none; background: transparent; padding-left: 0.25rem;"
                                oninput="updateGooglePreview()"
                            >
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="excerpt" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Tóm tắt ngắn (Excerpt)
                        </label>
                        <textarea 
                            id="excerpt" 
                            name="excerpt" 
                            class="form-control" 
                            rows="2" 
                            placeholder="Tóm tắt ngắn nội dung trang (dùng cho giới thiệu hoặc mô tả sơ lược)..."
                        ><?php echo htmlspecialchars($_POST['excerpt'] ?? $pageData['excerpt'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <label for="content" style="font-weight: 700; font-size: 0.9rem; margin: 0;">
                                Nội dung trang (HTML / Rich Text)
                            </label>
                            <span style="font-size: 0.75rem; color: var(--muted-foreground);">Hỗ trợ đầy đủ định dạng HTML, thẻ tiêu đề, bảng, media</span>
                        </div>
                        <textarea 
                            id="content" 
                            name="content" 
                            class="form-control" 
                            rows="16" 
                            placeholder="Nhập nội dung trang tại đây (hỗ trợ các thẻ <h2>, <p>, <ul>, <img>, <details>...)..."
                            style="font-family: monospace; font-size: 0.875rem; line-height: 1.6;"
                        ><?php echo htmlspecialchars($_POST['content'] ?? $pageData['content'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- SEO & Open Graph Metadata Card -->
                <div class="card" style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/>
                        </svg>
                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700;">Tối ưu hóa SEO &amp; Rich Snippets</h3>
                    </div>

                    <!-- Live Google SERP Preview -->
                    <div style="background: var(--secondary); border: 1px solid var(--border); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; display: block; margin-bottom: 0.5rem;">Xem trước kết quả tìm kiếm Google (SERP Preview)</span>
                        <div style="font-family: Arial, sans-serif; background: var(--card); padding: 0.85rem; border-radius: 6px; border: 1px solid var(--border);">
                            <div style="font-size: 0.78rem; color: #4b5563; margin-bottom: 0.2rem; display: flex; align-items: center; gap: 0.35rem;">
                                <span><?php echo htmlspecialchars(defined('APP_URL') ? APP_URL : 'https://example.com'); ?></span>
                                <span>›</span>
                                <span id="serpSlugPreview">page/<?php echo htmlspecialchars($pageData['slug'] ?? 'duong-dan'); ?></span>
                            </div>
                            <div id="serpTitlePreview" style="color: #1a0dab; font-size: 1.05rem; font-weight: 500; margin-bottom: 0.25rem; line-height: 1.3;">
                                <?php echo htmlspecialchars(!empty($pageData['meta_title']) ? $pageData['meta_title'] : (($pageData['title'] ?? 'Tiêu đề trang') . ' - ' . $siteName)); ?>
                            </div>
                            <div id="serpDescPreview" style="color: #4d5156; font-size: 0.825rem; line-height: 1.4;">
                                <?php echo htmlspecialchars(!empty($pageData['meta_description']) ? $pageData['meta_description'] : 'Mô tả nội dung trang xuất hiện trên công cụ tìm kiếm Google để tăng tỷ lệ nhấp chuột (CTR)...'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="meta_title" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Thẻ Tiêu đề SEO (Meta Title)
                        </label>
                        <input 
                            type="text" 
                            id="meta_title" 
                            name="meta_title" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($_POST['meta_title'] ?? $pageData['meta_title'] ?? ''); ?>" 
                            placeholder="Để trống nếu muốn tự động sử dụng tiêu đề trang..."
                            oninput="updateGooglePreview()"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="meta_description" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Thẻ Mô tả SEO (Meta Description)
                        </label>
                        <textarea 
                            id="meta_description" 
                            name="meta_description" 
                            class="form-control" 
                            rows="2" 
                            placeholder="Mô tả súc tích nội dung trang (khoảng 150-160 ký tự)..."
                            oninput="updateGooglePreview()"
                        ><?php echo htmlspecialchars($_POST['meta_description'] ?? $pageData['meta_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="meta_keywords" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Từ khóa SEO (Meta Keywords)
                        </label>
                        <input 
                            type="text" 
                            id="meta_keywords" 
                            name="meta_keywords" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? $pageData['meta_keywords'] ?? ''); ?>" 
                            placeholder="Ví dụ: giới thiệu, liên hệ, chính sách..."
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="custom_schema_json" style="font-weight: 700; font-size: 0.825rem; margin-bottom: 0.35rem; display: block;">
                            Schema.org JSON-LD Tùy chỉnh (Tùy chọn)
                        </label>
                        <textarea 
                            id="custom_schema_json" 
                            name="custom_schema_json" 
                            class="form-control" 
                            rows="3" 
                            placeholder='{"@context": "https://schema.org", "@type": "AboutPage", ...}'
                            style="font-family: monospace; font-size: 0.8rem;"
                        ><?php echo htmlspecialchars($_POST['custom_schema_json'] ?? $pageData['custom_schema_json'] ?? ''); ?></textarea>
                    </div>
                </div>

            </div>

            <!-- Right Column: Settings & Publishing Controls -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Publish Box Card -->
                <div class="card" style="padding: 1.25rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 0.95rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        Xuất bản &amp; Trạng thái
                    </h3>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="status" style="font-weight: 600; font-size: 0.825rem; display: block; margin-bottom: 0.35rem;">
                            Trạng thái hiển thị
                        </label>
                        <select name="status" id="status" class="form-control">
                            <option value="published" <?php echo ($pageData['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>Đã xuất bản (Công khai)</option>
                            <option value="draft" <?php echo ($pageData['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                            <option value="ai_draft" <?php echo ($pageData['status'] ?? '') === 'ai_draft' ? 'selected' : ''; ?>>🤖 Bản nháp AI</option>
                            <option value="pending_review" <?php echo ($pageData['status'] ?? '') === 'pending_review' ? 'selected' : ''; ?>>Chờ kiểm duyệt</option>
                            <option value="archived" <?php echo ($pageData['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Lưu trữ (Ẩn)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="template" style="font-weight: 600; font-size: 0.825rem; display: block; margin-bottom: 0.35rem;">
                            Mẫu giao diện (Template)
                        </label>
                        <select name="template" id="template" class="form-control">
                            <option value="default" <?php echo ($pageData['template'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Mẫu chuẩn (Có sidebar)</option>
                            <option value="fullwidth" <?php echo ($pageData['template'] ?? '') === 'fullwidth' ? 'selected' : ''; ?>>Toàn chiều rộng (Full Width)</option>
                            <option value="contact" <?php echo ($pageData['template'] ?? '') === 'contact' ? 'selected' : ''; ?>>Trang liên hệ (Contact Form)</option>
                            <option value="landing" <?php echo ($pageData['template'] ?? '') === 'landing' ? 'selected' : ''; ?>>Landing Page (Tập trung CTA)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="sort_order" style="font-weight: 600; font-size: 0.825rem; display: block; margin-bottom: 0.35rem;">
                            Thứ tự sắp xếp (Số nhỏ xếp trước)
                        </label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            id="sort_order" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($pageData['sort_order'] ?? 0); ?>"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary-action" style="width: 100%; justify-content: center; padding: 0.65rem 1rem; font-weight: 700;">
                        <?php echo $isEdit ? 'Lưu thay đổi' : 'Tạo và Xuất bản trang'; ?>
                    </button>
                </div>

                <!-- Featured Image Card -->
                <div class="card" style="padding: 1.25rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 0.95rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                        Ảnh đại diện trang
                    </h3>

                    <?php if (!empty($pageData['featured_image'])): ?>
                        <div style="margin-bottom: 0.85rem; border-radius: 6px; overflow: hidden; border: 1px solid var(--border);">
                            <img src="<?php echo htmlspecialchars($pageData['featured_image']); ?>" alt="Ảnh trang" style="width: 100%; max-height: 160px; object-fit: cover; display: block;">
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label for="featured_image_file" style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Tải ảnh từ máy tính:</label>
                        <input type="file" id="featured_image_file" name="featured_image_file" accept="image/*" class="form-control" style="font-size: 0.8rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="featured_image" style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Hoặc nhập URL ảnh:</label>
                        <input type="text" id="featured_image" name="featured_image" class="form-control" value="<?php echo htmlspecialchars($pageData['featured_image'] ?? ''); ?>" placeholder="https://...">
                    </div>
                </div>

                <!-- Revisions History Card (If Edit) -->
                <?php if ($isEdit && !empty($revisions)): ?>
                    <div class="card" style="padding: 1.25rem;">
                        <h3 style="margin: 0 0 0.85rem 0; font-size: 0.95rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                            Lịch sử chỉnh sửa
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                            <?php foreach ($revisions as $rev): ?>
                                <div style="font-size: 0.78rem; border-bottom: 1px dashed var(--border); padding-bottom: 0.45rem;">
                                    <div style="font-weight: 600; color: var(--foreground);">
                                        <?php echo htmlspecialchars($rev['changed_by'] ?? 'User'); ?> (<?php echo htmlspecialchars($rev['action']); ?>)
                                    </div>
                                    <div style="color: var(--muted-foreground);">
                                        <?php echo formatDate($rev['created_at'], 'd/m/Y H:i'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </form>
</div>

<script>
function autoGenerateSlug(title) {
    const slugInput = document.getElementById('slug');
    if (slugInput && (!slugInput.value || slugInput.dataset.manual !== 'true')) {
        let str = title.toLowerCase();
        str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        str = str.replace(/[đĐ]/g, 'd');
        str = str.replace(/([^0-9a-z-\s])/g, '');
        str = str.replace(/(\s+)/g, '-');
        str = str.replace(/-+/g, '-');
        str = str.replace(/^-+|-+$/g, '');
        slugInput.value = str;
        updateGooglePreview();
    }
}

document.getElementById('slug')?.addEventListener('input', function() {
    this.dataset.manual = 'true';
});

function updateGooglePreview() {
    const titleVal = document.getElementById('meta_title')?.value || document.getElementById('title')?.value || 'Tiêu đề trang';
    const descVal = document.getElementById('meta_description')?.value || document.getElementById('excerpt')?.value || 'Mô tả nội dung trang xuất hiện trên công cụ tìm kiếm Google...';
    const slugVal = document.getElementById('slug')?.value || 'duong-dan';
    const siteName = <?php echo json_encode($siteName); ?>;

    const serpTitle = document.getElementById('serpTitlePreview');
    const serpDesc = document.getElementById('serpDescPreview');
    const serpSlug = document.getElementById('serpSlugPreview');

    if (serpTitle) serpTitle.textContent = titleVal.includes(siteName) ? titleVal : titleVal + ' - ' + siteName;
    if (serpDesc) serpDesc.textContent = descVal;
    if (serpSlug) serpSlug.textContent = 'page/' + slugVal;
}
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
