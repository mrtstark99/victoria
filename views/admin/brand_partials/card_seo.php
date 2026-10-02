<?php
/**
 * Default SEO & Open Graph Accordion Card Partial
 */
?>
<!-- Card 4: Default SEO & Social Sharing -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleBrandAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><circle cx="12" cy="4"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Cấu hình SEO Mặc định &amp; Open Graph</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        
        <!-- Mô tả SEO -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="default_meta_description" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Mô tả SEO mặc định (Default Meta Description)
            </label>
            <textarea 
                id="default_meta_description" 
                name="default_meta_description" 
                class="form-control" 
                rows="2"
                placeholder="Blog chia sẻ kiến thức SEO chuyên sâu, chiến lược AI Agent và Digital Marketing..."
                oninput="updateBrandLivePreview()"
            ><?php echo htmlspecialchars($settings['default_meta_description'] ?? ''); ?></textarea>
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Sử dụng cho trang chủ hoặc các trang chưa cấu hình mô tả riêng (khuyến nghị 120-160 ký tự).</small>
        </div>

        <!-- Từ khóa SEO -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="default_meta_keywords" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Từ khóa SEO mặc định (Default Meta Keywords)
            </label>
            <input 
                type="text" 
                id="default_meta_keywords" 
                name="default_meta_keywords" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['default_meta_keywords'] ?? ''); ?>" 
                placeholder="SEO, AI Agent, Topic Cluster, Content Marketing"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Các từ khóa phân cách nhau bằng dấu phẩy (,).</small>
        </div>

        <!-- OG Image -->
        <div class="form-group" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <label style="font-weight: 700; margin: 0;">
                    Ảnh đại diện mặc định khi chia sẻ Link (Open Graph Image)
                </label>
                <span id="ogRemovedNotice" style="display: none; font-size: 0.75rem; color: var(--destructive); background: rgba(239,68,68,0.1); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600; align-items: center; gap: 0.4rem;">
                    <span>Đã đánh dấu xóa (Nhấn Lưu để áp dụng)</span>
                    <a href="javascript:void(0)" onclick="undoRemoveOgImage()" style="color: var(--primary); text-decoration: underline;">Hoàn tác</a>
                </span>
            </div>

            <?php if (!empty($settings['default_og_image'])): ?>
                <div id="ogPreviewCard" style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($settings['default_og_image']); ?>" alt="OG thumbnail" style="width: 48px; height: 28px; object-fit: cover; border-radius: 4px;">
                        <span style="font-size: 0.8rem; color: var(--foreground); font-weight: 600;"><?php echo htmlspecialchars(basename($settings['default_og_image'])); ?></span>
                    </div>
                    <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.25rem 0.6rem; height: auto;" onclick="confirmRemoveOgImage()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>Xóa ảnh OG</span>
                    </button>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <label for="default_og_image_file" class="btn btn-secondary btn-sm" style="cursor: pointer;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Tải ảnh OG từ máy tính...</span>
                </label>
                <input type="file" id="default_og_image_file" name="default_og_image_file" accept="image/*" style="display: none;" onchange="previewOgUpload(this)">
                <span id="ogFileName" style="font-size: 0.75rem; color: var(--muted-foreground);"></span>
            </div>
            <input 
                type="text" 
                id="default_og_image" 
                name="default_og_image" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['default_og_image'] ?? ''); ?>" 
                placeholder="Hoặc dán URL ảnh Open Graph (https://images.unsplash.com/...)..."
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Ảnh hiển thị khi chia sẻ liên kết trang chủ lên Facebook, Zalo, Twitter (kích thước chuẩn 1200x630px).</small>
        </div>

    </div>
</div>
