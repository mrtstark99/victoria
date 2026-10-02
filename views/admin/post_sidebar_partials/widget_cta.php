<?php
/**
 * Widget 4: Custom CTA Banner Accordion Partial
 */
?>
<!-- Widget 4: Custom CTA Banner -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Widget: Khung Kêu gọi hành động (Custom CTA)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
            <label class="toggle-switch">
                <input type="checkbox" id="post_sidebar_cta_enabled" name="post_sidebar_cta_enabled" value="1" <?php echo ($settings['post_sidebar_cta_enabled'] ?? '0') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                <span class="toggle-slider"></span>
            </label>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div class="grid-layout columns-2" style="margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_cta_title">Tiêu đề CTA</label>
                <input 
                    type="text" 
                    id="post_sidebar_cta_title" 
                    name="post_sidebar_cta_title" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_cta_title'] ?? 'Tư vấn chiến lược SEO & AI'); ?>" 
                    placeholder="Tư vấn chiến lược..."
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_cta_badge">Nhãn Badge</label>
                <input 
                    type="text" 
                    id="post_sidebar_cta_badge" 
                    name="post_sidebar_cta_badge" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_cta_badge'] ?? 'Tư vấn'); ?>" 
                    placeholder="Hot / Tư vấn / Khóa học"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="post_sidebar_cta_desc">Mô tả nội dung CTA</label>
            <textarea 
                id="post_sidebar_cta_desc" 
                name="post_sidebar_cta_desc" 
                class="form-control" 
                rows="2"
                oninput="updateLiveSidebarPreview()"
            ><?php echo htmlspecialchars($settings['post_sidebar_cta_desc'] ?? 'Đồng hành cùng doanh nghiệp của bạn xây dựng hệ thống tăng trưởng hữu cơ bền vững.'); ?></textarea>
        </div>

        <div class="grid-layout columns-2" style="margin-bottom: 0;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_cta_btn_text">Văn bản nút bấm</label>
                <input 
                    type="text" 
                    id="post_sidebar_cta_btn_text" 
                    name="post_sidebar_cta_btn_text" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_cta_btn_text'] ?? 'Liên hệ tư vấn ngay'); ?>" 
                    placeholder="Liên hệ tư vấn ngay"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_cta_btn_url">Đường dẫn liên kết (URL / Mailto / Tel)</label>
                <input 
                    type="text" 
                    id="post_sidebar_cta_btn_url" 
                    name="post_sidebar_cta_btn_url" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_cta_btn_url'] ?? 'mailto:admin@example.com'); ?>" 
                    placeholder="https://... hoặc mailto:..."
                >
            </div>
        </div>
    </div>
</div>
