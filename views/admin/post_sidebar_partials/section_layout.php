<?php
/**
 * Layout & Sticky Options Accordion Partial
 */
?>
<!-- Section 2: Layout & Sticky Options -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <line x1="15" y1="3" x2="15" y2="21"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Cấu hình Bố cục &amp; Chân bài viết</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Sticky Sidebar -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                <div>
                    <strong style="display: block; font-size: 0.925rem; color: var(--foreground);">Cố định thanh bên khi cuộn (Sticky Sidebar)</strong>
                    <span style="font-size: 0.8rem; color: var(--muted-foreground);">Cột phải sẽ tự động trượt theo màn hình khi độc giả đọc bài viết dài.</span>
                </div>
                <label class="toggle-switch" onclick="event.stopPropagation()">
                    <input type="checkbox" name="post_sidebar_sticky" value="1" <?php echo ($settings['post_sidebar_sticky'] ?? '1') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <!-- Author Box -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                <div>
                    <strong style="display: block; font-size: 0.925rem; color: var(--foreground);">Khung giới thiệu tác giả (Author Bio Card)</strong>
                    <span style="font-size: 0.8rem; color: var(--muted-foreground);">Hiển thị ảnh đại diện, tên và tiểu sử ngắn của tác giả ở cuối nội dung.</span>
                </div>
                <label class="toggle-switch" onclick="event.stopPropagation()">
                    <input type="checkbox" name="post_author_box_enabled" value="1" <?php echo ($settings['post_author_box_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <!-- Related Posts -->
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong style="display: block; font-size: 0.925rem; color: var(--foreground);">Khối bài viết cùng chuyên mục (Related Posts)</strong>
                    <span style="font-size: 0.8rem; color: var(--muted-foreground);">Hiển thị danh sách các bài viết liên quan ở chân trang.</span>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <select name="post_related_limit" class="form-control" style="width: 100px; padding: 0.35rem 0.65rem; font-size: 0.85rem;" title="Số bài hiển thị">
                        <option value="2" <?php echo ($settings['post_related_limit'] ?? '3') == '2' ? 'selected' : ''; ?>>2 bài</option>
                        <option value="3" <?php echo ($settings['post_related_limit'] ?? '3') == '3' ? 'selected' : ''; ?>>3 bài</option>
                        <option value="4" <?php echo ($settings['post_related_limit'] ?? '3') == '4' ? 'selected' : ''; ?>>4 bài</option>
                        <option value="6" <?php echo ($settings['post_related_limit'] ?? '3') == '6' ? 'selected' : ''; ?>>6 bài</option>
                    </select>
                    <label class="toggle-switch" onclick="event.stopPropagation()">
                        <input type="checkbox" name="post_related_enabled" value="1" <?php echo ($settings['post_related_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
