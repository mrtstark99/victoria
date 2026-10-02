<?php
/**
 * Widget 2: Banner Ads / Display Accordion Partial
 */
?>
<!-- Widget 2: Banner Ads / Display -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <circle cx="9" cy="9" r="2"/>
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Widget: Banner hình ảnh / Quảng cáo (Banner Ads)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
            <label class="toggle-switch">
                <input type="checkbox" id="post_sidebar_banner_enabled" name="post_sidebar_banner_enabled" value="1" <?php echo ($settings['post_sidebar_banner_enabled'] ?? '0') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                <span class="toggle-slider"></span>
            </label>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div class="grid-layout columns-2" style="margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_banner_title">Tiêu đề Widget (Nếu muốn hiển thị header)</label>
                <input 
                    type="text" 
                    id="post_sidebar_banner_title" 
                    name="post_sidebar_banner_title" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_banner_title'] ?? 'Khám phá đối tác'); ?>" 
                    placeholder="Khám phá đối tác"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_banner_badge">Nhãn Badge</label>
                <input 
                    type="text" 
                    id="post_sidebar_banner_badge" 
                    name="post_sidebar_banner_badge" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_banner_badge'] ?? 'Tài trợ'); ?>" 
                    placeholder="Tài trợ / Quảng cáo"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
        </div>

        <!-- Banner Image Upload & URL -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <input type="hidden" id="remove_banner_image" name="remove_banner_image" value="0">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <label style="font-weight: 700; margin: 0;">Ảnh Banner</label>
                <span id="bannerRemovedNotice" style="display: none; font-size: 0.75rem; color: var(--destructive); background: rgba(239,68,68,0.1); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600; align-items: center; gap: 0.4rem;">
                    <span>Đã đánh dấu xóa (Nhấn Lưu để áp dụng)</span>
                    <a href="javascript:void(0)" onclick="undoRemoveBanner()" style="color: var(--primary); text-decoration: underline;">Hoàn tác</a>
                </span>
            </div>

            <?php if (!empty($settings['post_sidebar_banner_image'])): ?>
                <div id="bannerCardBox" style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($settings['post_sidebar_banner_image']); ?>" alt="Banner thumbnail" style="width: 48px; height: 28px; object-fit: cover; border-radius: 4px;">
                        <span style="font-size: 0.8rem; color: var(--foreground); font-weight: 600;"><?php echo htmlspecialchars(basename($settings['post_sidebar_banner_image'])); ?></span>
                    </div>
                    <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.25rem 0.6rem; height: auto;" onclick="confirmRemoveBanner()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>Xóa Banner</span>
                    </button>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <label for="banner_image_file" class="btn btn-secondary btn-sm" style="cursor: pointer;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Tải ảnh Banner từ máy tính...</span>
                </label>
                <input type="file" id="banner_image_file" name="banner_image_file" accept="image/*" style="display: none;" onchange="previewBannerFile(this)">
                <span id="bannerFileName" style="font-size: 0.75rem; color: var(--muted-foreground);"></span>
            </div>
            <input 
                type="text" 
                id="post_sidebar_banner_image" 
                name="post_sidebar_banner_image" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['post_sidebar_banner_image'] ?? ''); ?>" 
                placeholder="Hoặc dán URL ảnh Banner (https://images.unsplash.com/...)..."
                oninput="previewBannerUrl(this.value)"
            >
            
            <!-- Banner Image Preview Box -->
            <div id="bannerPreviewContainer" style="max-height: 180px; margin-top: 0.75rem; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); <?php echo empty($settings['post_sidebar_banner_image']) ? 'display: none;' : 'display: block;'; ?>">
                <img id="bannerPreviewImg" src="<?php echo htmlspecialchars($settings['post_sidebar_banner_image'] ?? ''); ?>" alt="Banner preview" style="width: 100%; max-height: 180px; object-fit: cover;">
            </div>
        </div>

        <div class="grid-layout columns-2" style="margin-bottom: 0.75rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_banner_url">Đường dẫn liên kết khi Click (Target URL)</label>
                <input 
                    type="url" 
                    id="post_sidebar_banner_url" 
                    name="post_sidebar_banner_url" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_banner_url'] ?? 'https://example.com'); ?>" 
                    placeholder="https://..."
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_banner_alt">Văn bản thay thế (Alt Text cho SEO)</label>
                <input 
                    type="text" 
                    id="post_sidebar_banner_alt" 
                    name="post_sidebar_banner_alt" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_banner_alt'] ?? 'Banner quảng cáo'); ?>" 
                    placeholder="Banner quảng cáo"
                >
            </div>
        </div>

        <label style="font-size: 0.825rem; color: var(--foreground); cursor: pointer; display: flex; align-items: center; gap: 0.45rem;">
            <input type="checkbox" name="post_sidebar_banner_new_tab" value="1" <?php echo ($settings['post_sidebar_banner_new_tab'] ?? '1') === '1' ? 'checked' : ''; ?>>
            <span>Mở liên kết trong tab mới (<code>target="_blank" rel="noopener sponsored"</code>)</span>
        </label>
    </div>
</div>
