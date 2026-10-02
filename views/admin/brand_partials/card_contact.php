<?php
/**
 * Brand Contact Information Accordion Card Partial
 */
?>
<!-- Card 2: Contact Information -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleBrandAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Thông tin Liên hệ &amp; Trụ sở Doanh nghiệp</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        
        <!-- Mục 7: Email liên hệ chính -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="site_email" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Email liên hệ chính
            </label>
            <input 
                type="email" 
                id="site_email" 
                name="site_email" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_email'] ?? 'admin@example.com'); ?>" 
                placeholder="admin@example.com"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Email nhận thông báo và hiển thị ở phần thông tin liên hệ footer.</small>
        </div>

        <!-- Mục 8: Hotline -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="site_phone" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Hotline / Số điện thoại liên hệ
            </label>
            <input 
                type="text" 
                id="site_phone" 
                name="site_phone" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_phone'] ?? '+84 123 456 789'); ?>" 
                placeholder="+84 123 456 789"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Số điện thoại hotline hỗ trợ độc giả hoặc khách hàng.</small>
        </div>

        <!-- Mục 9: Địa chỉ -->
        <div class="form-group" style="margin-bottom: 0;">
            <label for="site_address" style="font-weight: 700; display: block; margin-bottom: 0.35rem;">
                Địa chỉ trụ sở / Văn phòng đại diện
            </label>
            <input 
                type="text" 
                id="site_address" 
                name="site_address" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['site_address'] ?? 'Hà Nội, Việt Nam'); ?>" 
                placeholder="Hà Nội, Việt Nam"
                oninput="updateBrandLivePreview()"
            >
            <small style="color: var(--muted-foreground); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Địa chỉ công ty hoặc cơ quan chủ quản website.</small>
        </div>

    </div>
</div>
