<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <div class="alert-content">
            <strong style="display: block; margin-bottom: 0.25rem;">Vui lòng kiểm tra lại thông tin:</strong>
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo $is_edit ? '/admin/posts/edit/' . $post['id'] : '/admin/posts/create'; ?>" id="postForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <?php if ($is_edit): ?>
        <input type="hidden" name="expected_updated_at" value="<?php echo htmlspecialchars($post['updated_at']); ?>">
    <?php endif; ?>

    <div class="grid-layout columns-post-editor">
        <!-- Left Column: Main Content -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Post Core Info Card -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        <span><?php echo $is_edit ? 'Chỉnh sửa nội dung bài viết' : 'Soạn thảo bài viết mới'; ?></span>
                    </h3>
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Tiêu đề bài viết <span style="color: var(--destructive)">*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" 
                        required 
                        placeholder="Nhập tiêu đề hấp dẫn..." 
                        style="font-size: 1.15rem; font-weight: 700; padding: 0.85rem 1rem;"
                        oninput="syncSerpPreview()"
                    >
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label for="slug">Slug (Đường dẫn tĩnh)</label>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="generateSlugFromTitle()" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                            Tự tạo Slug từ tiêu đề
                        </button>
                    </div>
                    <div style="position: relative; display: flex; align-items: center;">
                        <span style="position: absolute; left: 0.85rem; font-size: 0.85rem; color: var(--muted-foreground);">/blog/</span>
                        <input 
                            type="text" 
                            id="slug" 
                            name="slug" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>" 
                            placeholder="duong-dan-bai-viet"
                            style="padding-left: 4.5rem; font-size: 0.875rem;"
                            oninput="syncSerpPreview()"
                        >
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="form-group">
                    <label for="featured_image" style="font-weight: 700;">Ảnh đại diện bài viết (Featured Image)</label>
                    <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
                        <label for="featured_image_file" class="btn btn-secondary btn-sm" style="cursor: pointer;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <span>Tải ảnh từ máy tính...</span>
                        </label>
                        <input 
                            type="file" 
                            id="featured_image_file" 
                            name="featured_image_file" 
                            accept="image/jpeg,image/png,image/webp,image/gif" 
                            style="display: none;"
                            onchange="previewFeaturedImageFile(this)"
                        >
                        <span id="featuredImageFileName" style="font-size: 0.8rem; color: var(--muted-foreground);"></span>
                    </div>

                    <input 
                        type="text" 
                        id="featured_image" 
                        name="featured_image" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>" 
                        placeholder="Hoặc dán link URL ảnh (https://images.unsplash.com/... hoặc /uploads/posts/...)..."
                        oninput="updateImagePreview(this.value)"
                    >
                    
                    <!-- Real-time Image Preview Container -->
                    <div class="image-preview-container" id="imagePreviewBox" style="max-height: 220px; margin-top: 0.75rem; border-radius: calc(var(--radius) * 0.5); overflow: hidden; <?php echo empty($post['featured_image']) ? 'display: none;' : ''; ?>">
                        <img id="imagePreviewTag" src="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>" alt="Ảnh xem trước" style="max-height: 220px; width: 100%; object-fit: cover;" onerror="this.parentElement.style.display='none';">
                    </div>
                </div>

                <!-- Content Textarea -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="content">Nội dung bài viết (HTML / Markdown / Văn bản) <span style="color: var(--destructive)">*</span></label>
                    <textarea 
                        id="content" 
                        name="content" 
                        class="form-control" 
                        rows="16" 
                        required 
                        placeholder="Viết nội dung bài viết tại đây..." 
                        style="font-family: inherit; font-size: 0.95rem; line-height: 1.65; padding: 1rem;"
                    ><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                </div>

                <!-- Excerpt -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="excerpt">Tóm tắt ngắn (Excerpt - Hiển thị ngoài trang chủ &amp; Card)</label>
                    <textarea 
                        id="excerpt" 
                        name="excerpt" 
                        class="form-control" 
                        rows="3" 
                        placeholder="Để trống hệ thống sẽ tự trích dẫn đoạn đầu bài viết..."
                    ><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Right Column: Publishing Sidebar & SEO -->
        <?php include __DIR__ . '/post_form_partials/sidebar.php'; ?>
    </div>
</form>

<script src="/assets/js/post_form.js"></script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
