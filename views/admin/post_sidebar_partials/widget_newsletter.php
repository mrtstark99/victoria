<?php
/**
 * Widget 3: Newsletter Accordion Partial
 */
?>
<!-- Widget 3: Newsletter -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Widget: Bản tin nhận tin tức (Newsletter)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
            <label class="toggle-switch">
                <input type="checkbox" id="post_sidebar_newsletter_enabled" name="post_sidebar_newsletter_enabled" value="1" <?php echo ($settings['post_sidebar_newsletter_enabled'] ?? '1') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                <span class="toggle-slider"></span>
            </label>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div class="grid-layout columns-2" style="margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_newsletter_title">Tiêu đề Widget</label>
                <input 
                    type="text" 
                    id="post_sidebar_newsletter_title" 
                    name="post_sidebar_newsletter_title" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_newsletter_title'] ?? 'Bản tin SEO & AI'); ?>" 
                    placeholder="Bản tin SEO & AI"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_newsletter_btn">Chữ nút gửi</label>
                <input 
                    type="text" 
                    id="post_sidebar_newsletter_btn" 
                    name="post_sidebar_newsletter_btn" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_newsletter_btn'] ?? 'Gửi'); ?>" 
                    placeholder="Gửi"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="post_sidebar_newsletter_desc">Mô tả ngắn</label>
            <textarea 
                id="post_sidebar_newsletter_desc" 
                name="post_sidebar_newsletter_desc" 
                class="form-control" 
                rows="2"
                oninput="updateLiveSidebarPreview()"
            ><?php echo htmlspecialchars($settings['post_sidebar_newsletter_desc'] ?? 'Cập nhật các thuật toán mới nhất của Google và chiến lược AI mỗi tuần.'); ?></textarea>
        </div>
    </div>
</div>
