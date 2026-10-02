<?php
/**
 * Footer & Custom Scripts Accordion Card Partial
 */
?>
<!-- Card 5: Footer & Custom Scripts -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleBrandAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Chân trang (Footer) &amp; Mã nhúng Tùy chỉnh</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        
        <!-- Footer About Text -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="footer_about_text" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Đoạn văn bản giới thiệu ở chân trang (Footer About Text)
            </label>
            <textarea 
                id="footer_about_text" 
                name="footer_about_text" 
                class="form-control" 
                rows="2"
                placeholder="Nền tảng chia sẻ kiến thức SEO, AI và tối ưu hóa hệ thống dữ liệu hiện đại."
                oninput="updateBrandLivePreview()"
            ><?php echo htmlspecialchars($settings['footer_about_text'] ?? ''); ?></textarea>
        </div>

        <!-- Footer Copyright -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="footer_copyright" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Văn bản Bản quyền Chân trang (Footer Copyright)
            </label>
            <input 
                type="text" 
                id="footer_copyright" 
                name="footer_copyright" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['footer_copyright'] ?? '© {year} MinimaList. All rights reserved.'); ?>" 
                placeholder="© {year} MinimaList. All rights reserved."
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">(Sử dụng <code>{year}</code> để tự động hiển thị năm hiện tại: <?php echo date('Y'); ?>)</small>
        </div>

        <!-- Custom Header Code -->
        <div class="form-group" style="margin-bottom: 1.25rem; padding-top: 0.75rem; border-top: 1px solid var(--border);">
            <label for="custom_header_code" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Mã Header Tùy chỉnh (Chèn vào thẻ <code>&lt;head&gt;</code>)
            </label>
            <textarea 
                id="custom_header_code" 
                name="custom_header_code" 
                class="form-control" 
                rows="3" 
                placeholder="&lt;!-- Google Tag Manager, Google Analytics, Custom Meta... --&gt;&#10;&lt;script&gt;...&lt;/script&gt;"
                style="font-family: monospace; font-size: 0.8rem;"
            ><?php echo htmlspecialchars($settings['custom_header_code'] ?? ''); ?></textarea>
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Nhúng mã Google Search Console verification, Google Analytics GA4, Google Tag Manager hoặc CSS tùy biến.</small>
        </div>

        <!-- Custom Footer Code -->
        <div class="form-group" style="margin-bottom: 0;">
            <label for="custom_footer_code" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Mã Footer Tùy chỉnh (Chèn trước thẻ đóng <code>&lt;/body&gt;</code>)
            </label>
            <textarea 
                id="custom_footer_code" 
                name="custom_footer_code" 
                class="form-control" 
                rows="3" 
                placeholder="&lt;!-- Live Chat widget, Tracking Pixels, Custom JS Scripts... --&gt;&#10;&lt;script&gt;...&lt;/script&gt;"
                style="font-family: monospace; font-size: 0.8rem;"
            ><?php echo htmlspecialchars($settings['custom_footer_code'] ?? ''); ?></textarea>
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Nhúng mã Live Chat (Tawk.to, Crisp), Facebook Pixel, TikTok Pixel hoặc JavaScript hỗ trợ.</small>
        </div>

    </div>
</div>
