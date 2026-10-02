<?php
/**
 * Admin Post Form Sidebar Partial: Publishing actions, Google SERP Simulation, SEO Metadata
 */
?>
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Publishing Actions Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title" style="font-size: 1rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 14 14"/>
                </svg>
                <span>Thiết lập xuất bản</span>
            </h3>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status">Trạng thái bài viết</label>
            <select id="status" name="status" class="form-control">
                <option value="draft" <?php echo (isset($post['status']) && $post['status'] === 'draft') ? 'selected' : ''; ?>>Draft (Bản nháp)</option>
                <option value="ai_draft" <?php echo (isset($post['status']) && $post['status'] === 'ai_draft') ? 'selected' : ''; ?>>AI Draft (Bản nháp AI)</option>
                <option value="pending_review" <?php echo (isset($post['status']) && $post['status'] === 'pending_review') ? 'selected' : ''; ?>>Pending Review (Chờ duyệt)</option>
                <option value="published" <?php echo (!isset($post['status']) || $post['status'] === 'published') ? 'selected' : ''; ?>>Published (Công khai)</option>
            </select>
        </div>

        <!-- Category -->
        <div class="form-group">
            <label for="category_id">Chuyên mục <span style="color: var(--destructive)">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Chọn chuyên mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($post['category_id']) && $post['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Featured Toggle Switch -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="margin-bottom: 0.4rem;">Đánh dấu nổi bật</label>
            <label class="toggle-switch-wrapper" for="featured">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--foreground);">Bài viết nổi bật (Spotlight Hero)</span>
                <div class="toggle-switch">
                    <input type="checkbox" id="featured" name="featured" value="1" <?php echo (isset($post['featured']) && $post['featured']) ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </div>
            </label>
        </div>

        <!-- Action CTA Buttons -->
        <div style="display: flex; flex-direction: column; gap: 0.65rem; border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span><?php echo $is_edit ? 'Cập nhật bài viết' : 'Lưu &amp; Xuất bản'; ?></span>
            </button>
            <a href="/admin/posts" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Google Search Snippet Simulation Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title" style="font-size: 0.95rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Mô phỏng hiển thị Google</span>
            </h3>
        </div>

        <div class="serp-preview-box">
            <div class="serp-preview-url">
                <span><?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'minima.vn'); ?> › blog › <span id="serpSlugPreview"><?php echo htmlspecialchars($post['slug'] ?? 'duong-dan'); ?></span></span>
            </div>
            <div class="serp-preview-title" id="serpTitlePreview">
                <?php echo htmlspecialchars(!empty($post['meta_title']) ? $post['meta_title'] : ($post['title'] ?? 'Tiêu đề bài viết')); ?>
            </div>
            <div class="serp-preview-desc" id="serpDescPreview">
                <?php echo htmlspecialchars(!empty($post['meta_description']) ? $post['meta_description'] : 'Mô tả tóm tắt nội dung bài viết sẽ hiển thị ở đây trên kết quả tìm kiếm Google...'); ?>
            </div>
        </div>
    </div>

    <!-- SEO Metadata Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title" style="font-size: 0.95rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="m4.93 4.93 4.24 4.24"/>
                    <path d="m14.83 9.17 4.24-4.24"/>
                    <circle cx="12" cy="12" r="4"/>
                </svg>
                <span>Tối ưu SEO Metadata</span>
            </h3>
        </div>

        <div class="form-group">
            <label for="meta_title">SEO Meta Title</label>
            <input 
                type="text" 
                id="meta_title" 
                name="meta_title" 
                class="form-control" 
                value="<?php echo htmlspecialchars($post['meta_title'] ?? ''); ?>" 
                placeholder="Để trống sẽ tự động lấy tiêu đề bài viết..."
                oninput="syncSerpPreview()"
            >
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label for="meta_description">SEO Meta Description</label>
                <span id="descCharCount" style="font-size: 0.75rem; color: var(--muted-foreground);">0/160</span>
            </div>
            <textarea 
                id="meta_description" 
                name="meta_description" 
                class="form-control" 
                rows="3" 
                placeholder="Mô tả hấp dẫn khoảng 150-160 ký tự..."
                oninput="syncSerpPreview()"
            ><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="meta_keywords">SEO Keywords (Phân cách bằng dấu phẩy)</label>
            <input 
                type="text" 
                id="meta_keywords" 
                name="meta_keywords" 
                class="form-control" 
                value="<?php echo htmlspecialchars($post['meta_keywords'] ?? ''); ?>" 
                placeholder="tu-khoa-1, tu-khoa-2, seo blog"
            >
        </div>
    </div>
</div>
