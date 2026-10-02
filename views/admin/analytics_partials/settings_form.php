<?php
/**
 * Google API Configuration & KPI Setup Form Partial
 */
?>
<div class="card" style="margin-bottom: 2rem;">
    <details class="setup-details">
        <summary style="font-weight: 800; font-size: 1.1rem; cursor: pointer; user-select: none; outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
            <span style="display: flex; align-items: center; gap: 0.6rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                <span>Cấu hình tích hợp API Google &amp; Thiết lập KPI</span>
            </span>
            <svg class="chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s; color: var(--muted-foreground);"><polyline points="6 9 12 15 18 9"/></svg>
        </summary>
        <div style="margin-top: 1.25rem; border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <form method="POST" action="/admin/analytics/settings" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                
                <div class="grid-layout columns-2" style="margin-bottom: var(--spacing);">
                    <!-- Google credentials fields -->
                    <div>
                        <h4 style="font-weight: 700; font-size: 0.95rem; margin-bottom: 1rem; color: var(--foreground);">Google Analytics &amp; Search Console</h4>
                        <div class="form-group">
                            <label for="ga_id">GA4 Measurement ID</label>
                            <input type="text" id="ga_id" name="ga_id" class="form-control" value="<?php echo htmlspecialchars($settings['ga_id'] ?? ''); ?>" placeholder="G-XXXXXXXXXX">
                        </div>
                        <div class="form-group">
                            <label for="ga_property_id">GA4 Property ID</label>
                            <input type="text" id="ga_property_id" name="ga_property_id" class="form-control" value="<?php echo htmlspecialchars($settings['ga_property_id'] ?? ''); ?>" placeholder="Ví dụ: 123456789">
                        </div>
                        <div class="form-group">
                            <label for="gsc_site_url">Search Console Site URL</label>
                            <input type="text" id="gsc_site_url" name="gsc_site_url" class="form-control" value="<?php echo htmlspecialchars($settings['gsc_site_url'] ?? ''); ?>" placeholder="sc-domain:domain.com hoặc https://domain.com/">
                        </div>
                        <div class="form-group">
                            <label for="gsc_verification">Mã xác minh Google Search Console (HTML Meta tag)</label>
                            <input type="text" id="gsc_verification" name="gsc_verification" class="form-control" value="<?php echo htmlspecialchars($settings['gsc_verification'] ?? ''); ?>" placeholder="Nhập mã content của thẻ meta">
                        </div>
                        <div class="form-group">
                            <label for="credentials_file">Tệp khóa JSON tài khoản dịch vụ (Service Account)</label>
                            <input type="file" id="credentials_file" name="credentials_file" class="form-control" accept=".json">
                            <textarea class="form-control" name="service_account_json" rows="3" style="font-size: 0.75rem; margin-top: 0.5rem;" placeholder="Hoặc dán trực tiếp nội dung JSON Service Account tại đây..."></textarea>
                            
                            <?php if (!empty($settings['google_service_account_enc'])): ?>
                                <div style="margin-top: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 0.8rem; color: oklch(0.5 0.18 145); font-weight: 700;">✓ Đã tải lên và mã hóa tệp Service Account (AES-256)</span>
                                    <label style="font-size: 0.8rem; color: var(--destructive); cursor: pointer;">
                                        <input type="checkbox" name="remove_credentials" value="1"> Xóa credentials
                                    </label>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 0.8rem; color: var(--muted-foreground); display: block; margin-top: 0.5rem;">Chưa có tài khoản dịch vụ. Hãy tải lên tệp JSON để kích hoạt kết nối API.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- KPI & financial parameters fields -->
                    <div>
                        <h4 style="font-weight: 700; font-size: 0.95rem; margin-bottom: 1rem; color: var(--foreground);">Mục tiêu KPI &amp; Tài chính SEO</h4>
                        <div class="grid-layout columns-2" style="gap: 1rem; margin-bottom: 0;">
                            <div class="form-group">
                                <label for="seo_monthly_cost">Chi phí SEO/tháng (VND)</label>
                                <input type="number" id="seo_monthly_cost" name="seo_monthly_cost" class="form-control" value="<?php echo htmlspecialchars($settings['seo_monthly_cost'] ?? '0'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="organic_lead_value">Giá trị/lead (VND)</label>
                                <input type="number" id="organic_lead_value" name="organic_lead_value" class="form-control" value="<?php echo htmlspecialchars($settings['organic_lead_value'] ?? '0'); ?>">
                            </div>
                        </div>
                        <div class="grid-layout columns-2" style="gap: 1rem; margin-bottom: 0;">
                            <div class="form-group">
                                <label for="kpi_organic_sessions_target">Mục tiêu Organic Traffic</label>
                                <input type="number" id="kpi_organic_sessions_target" name="kpi_organic_sessions_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_organic_sessions_target'] ?? '0'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="kpi_impressions_target">Mục tiêu Impressions</label>
                                <input type="number" id="kpi_impressions_target" name="kpi_impressions_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_impressions_target'] ?? '0'); ?>">
                            </div>
                        </div>
                        <div class="grid-layout columns-2" style="gap: 1rem; margin-bottom: 0;">
                            <div class="form-group">
                                <label for="kpi_position_target">Vị trí từ khóa mục tiêu</label>
                                <input type="number" id="kpi_position_target" name="kpi_position_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_position_target'] ?? '0'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="kpi_ctr_target">CTR mục tiêu (%)</label>
                                <input type="number" step="0.1" id="kpi_ctr_target" name="kpi_ctr_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_ctr_target'] ?? '0'); ?>">
                            </div>
                        </div>
                        <div class="grid-layout columns-2" style="gap: 1rem; margin-bottom: 0;">
                            <div class="form-group">
                                <label for="kpi_engagement_rate_target">Tỷ lệ tương tác mục tiêu (%)</label>
                                <input type="number" step="0.1" id="kpi_engagement_rate_target" name="kpi_engagement_rate_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_engagement_rate_target'] ?? '0'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="kpi_avg_engagement_time_target">Số giây tương tác mục tiêu</label>
                                <input type="number" id="kpi_avg_engagement_time_target" name="kpi_avg_engagement_time_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_avg_engagement_time_target'] ?? '0'); ?>">
                            </div>
                        </div>
                        <div class="grid-layout columns-2" style="gap: 1rem; margin-bottom: 0;">
                            <div class="form-group">
                                <label for="kpi_conversion_rate_target">Tỷ lệ chuyển đổi mục tiêu (%)</label>
                                <input type="number" step="0.1" id="kpi_conversion_rate_target" name="kpi_conversion_rate_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_conversion_rate_target'] ?? '0'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="kpi_roi_target">Tỷ lệ ROI mục tiêu (%)</label>
                                <input type="number" step="0.1" id="kpi_roi_target" name="kpi_roi_target" class="form-control" value="<?php echo htmlspecialchars($settings['kpi_roi_target'] ?? '0'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--border);">
                    <button type="submit" class="btn">Lưu cấu hình đo lường</button>
                </div>
            </form>
        </div>
    </details>
</div>
