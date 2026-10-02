<?php
/**
 * Brand Identity & Logo Accordion Card Partial
 */
?>
<!-- Card 1: Brand Identity & Logo -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleBrandAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Nhận diện Thương hiệu, Logo &amp; Favicon</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        
        <!-- Mục 1: Kiểu hiển thị Logo Header -->
        <div class="form-group" style="margin-bottom: 1.25rem; background: var(--secondary); padding: 1rem; border-radius: calc(var(--radius) * 0.75); border: 1px solid var(--border);">
            <label for="site_logo_display_mode" style="font-weight: 700; display: block; margin-bottom: 0.35rem; color: var(--foreground);">
                Kiểu hiển thị Logo trên Header
            </label>
            <select 
                id="site_logo_display_mode" 
                name="site_logo_display_mode" 
                class="form-control" 
                onchange="updateBrandLivePreview()"
                style="font-weight: 600;"
            >
                <option value="logo_and_text" <?php echo ($settings['site_logo_display_mode'] ?? 'logo_and_text') === 'logo_and_text' ? 'selected' : ''; ?>>
                    ✦ Logo (Ảnh hoặc Icon Badge) + Tên Brand (Chữ) - Khuyên dùng
                </option>
                <option value="logo_only" <?php echo ($settings['site_logo_display_mode'] ?? '') === 'logo_only' ? 'selected' : ''; ?>>
                    ✦ Chỉ hiển thị Logo (Chỉ ảnh hoặc Icon Badge, ẩn chữ tên Brand)
                </option>
                <option value="text_only" <?php echo ($settings['site_logo_display_mode'] ?? '') === 'text_only' ? 'selected' : ''; ?>>
                    ✦ Chỉ hiển thị Tên Brand (Chữ thuần, không kèm ảnh/badge)
                </option>
            </select>
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.35rem;">
                Tùy chọn cách thanh điều hướng Header hiển thị Logo ảnh / Icon kết hợp cùng tên thương hiệu.
            </small>
        </div>

        <!-- Mục 2: Tên Website / Thương hiệu -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="site_name" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Tên Website / Thương hiệu (Brand Name) <span style="color:var(--destructive)">*</span>
            </label>
            <input 
                type="text" 
                id="site_name" 
                name="site_name" 
                class="form-control" 
                required 
                value="<?php echo htmlspecialchars($settings['site_name'] ?? 'MinimaList'); ?>" 
                placeholder="MinimaList"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Tên đại diện xuất hiện trên Logo Header, Tiêu đề trang, Email và Chân trang Footer.</small>
        </div>

        <!-- Mục 3: Khẩu hiệu Slogan -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="site_slogan" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Khẩu hiệu (Slogan / Tagline)
            </label>
            <input 
                type="text" 
                id="site_slogan" 
                name="site_slogan" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_slogan'] ?? 'Chia sẻ kiến thức SEO & AI'); ?>" 
                placeholder="Chia sẻ kiến thức SEO & AI"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Khẩu hiệu ngắn gọn giới thiệu mục tiêu hoặc định vị của website.</small>
        </div>

        <!-- Mục 4: Chữ Badge Biểu tượng Logo -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="site_logo_badge" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Chữ Badge Biểu tượng Logo (Icon Badge)
            </label>
            <input 
                type="text" 
                id="site_logo_badge" 
                name="site_logo_badge" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_logo_badge'] ?? 'M'); ?>" 
                placeholder="M" 
                maxlength="3"
                style="max-width: 160px;"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Ký tự viết tắt hiển thị trong khung màu tím nổi bật khi chưa tải ảnh Logo lên (1-3 ký tự, e.g. M, AI, PRO).</small>
        </div>

        <!-- Mục 5: Ảnh Logo Tùy chỉnh -->
        <div class="form-group" style="margin-bottom: 1.25rem; padding-top: 0.75rem; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <label style="font-weight: 700; margin: 0;">
                    Ảnh Logo Tùy chỉnh (Thay thế icon badge nếu có ảnh)
                </label>
                <span id="logoRemovedNotice" style="display: none; font-size: 0.75rem; color: var(--destructive); background: rgba(239,68,68,0.1); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600; align-items: center; gap: 0.4rem;">
                    <span>Đã đánh dấu xóa (Nhấn Lưu để áp dụng)</span>
                    <a href="javascript:void(0)" onclick="undoRemoveLogo()" style="color: var(--primary); text-decoration: underline;">Hoàn tác</a>
                </span>
            </div>

            <?php if (!empty($settings['site_logo_url'])): ?>
                <div id="logoPreviewCard" style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($settings['site_logo_url']); ?>" alt="Logo thumbnail" style="max-height: 28px; max-width: 100px; object-fit: contain; background: #fff; padding: 2px 4px; border-radius: 4px; border: 1px solid var(--border);">
                        <span style="font-size: 0.8rem; color: var(--foreground); font-weight: 600;"><?php echo htmlspecialchars(basename($settings['site_logo_url'])); ?></span>
                    </div>
                    <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.25rem 0.6rem; height: auto;" onclick="confirmRemoveLogo()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>Xóa Logo</span>
                    </button>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <label for="site_logo_file" class="btn btn-secondary btn-sm" style="cursor: pointer;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Tải Logo từ máy tính...</span>
                </label>
                <input type="file" id="site_logo_file" name="site_logo_file" accept="image/*" style="display: none;" onchange="previewLogoUpload(this)">
                <span id="logoFileName" style="font-size: 0.75rem; color: var(--muted-foreground);"></span>
            </div>
            <input 
                type="text" 
                id="site_logo_url" 
                name="site_logo_url" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_logo_url'] ?? ''); ?>" 
                placeholder="Hoặc dán URL ảnh Logo (https://... hoặc /uploads/brand/...)..."
                oninput="updateBrandLivePreview()"
            >
        </div>

        <!-- Mục 6: Favicon -->
        <div class="form-group" style="margin-bottom: 0; padding-top: 0.75rem; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <label style="font-weight: 700; margin: 0;">
                    Biểu tượng Tab trình duyệt (Favicon)
                </label>
                <span id="faviconRemovedNotice" style="display: none; font-size: 0.75rem; color: var(--destructive); background: rgba(239,68,68,0.1); padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600; align-items: center; gap: 0.4rem;">
                    <span>Đã đánh dấu xóa (Nhấn Lưu để áp dụng)</span>
                    <a href="javascript:void(0)" onclick="undoRemoveFavicon()" style="color: var(--primary); text-decoration: underline;">Hoàn tác</a>
                </span>
            </div>

            <?php if (!empty($settings['site_favicon_url'])): ?>
                <div id="faviconPreviewCard" style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <img src="<?php echo htmlspecialchars($settings['site_favicon_url']); ?>" alt="Favicon thumbnail" style="width: 22px; height: 22px; object-fit: contain;">
                        <span style="font-size: 0.8rem; color: var(--foreground); font-weight: 600;"><?php echo htmlspecialchars(basename($settings['site_favicon_url'])); ?></span>
                    </div>
                    <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.25rem 0.6rem; height: auto;" onclick="confirmRemoveFavicon()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>Xóa Favicon</span>
                    </button>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <label for="site_favicon_file" class="btn btn-secondary btn-sm" style="cursor: pointer;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span>Tải Favicon từ máy tính (.ico, .png, .svg)...</span>
                </label>
                <input type="file" id="site_favicon_file" name="site_favicon_file" accept=".ico,.png,.svg,.webp" style="display: none;" onchange="previewFaviconUpload(this)">
                <span id="faviconFileName" style="font-size: 0.75rem; color: var(--muted-foreground);"></span>
            </div>
            <input 
                type="text" 
                id="site_favicon_url" 
                name="site_favicon_url" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_favicon_url'] ?? ''); ?>" 
                placeholder="Hoặc dán URL Favicon (/favicon.ico)..."
                oninput="updateBrandLivePreview()"
            >
        </div>

    </div>
</div>
