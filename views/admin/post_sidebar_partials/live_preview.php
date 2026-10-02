<?php
/**
 * Sidebar Preview Simulator Right Column Partial
 */
?>
<!-- Right Column: Sidebar Preview Simulator -->
<div>
    <div class="card" style="position: sticky; top: calc(var(--topbar-height) + 1.5rem); margin-bottom: 0; background: var(--secondary);">
        <div class="card-header" style="margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
            <h4 class="card-title" style="font-size: 0.95rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Mô phỏng cột phải thực tế</span>
            </h4>
        </div>

        <!-- Empty Simulator Notice -->
        <div id="previewEmptyNotice" style="display: none; padding: 1.5rem; text-align: center; color: var(--muted-foreground); font-size: 0.825rem; background: var(--card); border-radius: 8px; border: 1px dashed var(--border);">
            Chưa có Widget nào được kích hoạt hiển thị. Bật công tắc của các Widget ở bên trái để xem mô phỏng.
        </div>

        <!-- Live Preview Simulator Container -->
        <div id="sidebarLivePreviewList" style="display: flex; flex-direction: column; gap: 1rem;">
            
            <!-- Preview Trending Widget -->
            <div class="preview-widget-box" data-preview-key="trending" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <strong style="font-size: 0.875rem; color: var(--foreground); display: flex; align-items: gap: 0.4rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                        <span id="previewTrendingTitle"><?php echo htmlspecialchars($settings['post_sidebar_trending_title'] ?? 'Đọc nhiều nhất'); ?></span>
                    </strong>
                    <span style="font-size: 0.7rem; color: var(--muted-foreground); font-weight: 600;">Nổi bật</span>
                </div>
                <div id="previewTrendingList" style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.8rem;">
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-weight: 800; color: var(--muted-foreground); font-size: 0.75rem;">01</span>
                        <span style="color: var(--foreground); font-weight: 600; line-height: 1.3;">Chiến lược xây dựng Topic Cluster đỉnh cao</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-weight: 800; color: var(--muted-foreground); font-size: 0.75rem;">02</span>
                        <span style="color: var(--foreground); font-weight: 600; line-height: 1.3;">Tối ưu Core Web Vitals chuẩn Google</span>
                    </div>
                </div>
            </div>

            <!-- Preview Banner Widget -->
            <div class="preview-widget-box" data-preview-key="banner" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                <div id="previewBannerHeader" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong id="previewBannerTitle" style="font-size: 0.85rem; color: var(--foreground);">
                        <?php echo htmlspecialchars($settings['post_sidebar_banner_title'] ?? 'Khám phá đối tác'); ?>
                    </strong>
                    <span id="previewBannerBadge" class="badge-pill-tag" style="font-size: 0.65rem;"><?php echo htmlspecialchars($settings['post_sidebar_banner_badge'] ?? 'Tài trợ'); ?></span>
                </div>
                <div style="width: 100%; min-height: 110px; background: var(--secondary); border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px dashed var(--border);">
                    <img id="previewBannerImg" src="<?php echo htmlspecialchars($settings['post_sidebar_banner_image'] ?? ''); ?>" alt="Banner" style="width: 100%; max-height: 160px; object-fit: cover; <?php echo empty($settings['post_sidebar_banner_image']) ? 'display: none;' : 'display: block;'; ?>">
                    <span id="previewBannerPlaceholder" style="font-size: 0.75rem; color: var(--muted-foreground); <?php echo !empty($settings['post_sidebar_banner_image']) ? 'display: none;' : 'display: block;'; ?>">Khung Banner 300x150</span>
                </div>
            </div>

            <!-- Preview Newsletter Widget -->
            <div class="preview-widget-box" data-preview-key="newsletter" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                <strong style="font-size: 0.875rem; color: var(--foreground); display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span id="previewNewsletterTitle"><?php echo htmlspecialchars($settings['post_sidebar_newsletter_title'] ?? 'Bản tin SEO & AI'); ?></span>
                </strong>
                <p id="previewNewsletterDesc" style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0 0 0.75rem; line-height: 1.4;">
                    <?php echo htmlspecialchars($settings['post_sidebar_newsletter_desc'] ?? 'Cập nhật các thuật toán mới nhất...'); ?>
                </p>
                <div style="display: flex; gap: 0.4rem;">
                    <input type="email" placeholder="Email của bạn..." class="form-control" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;" disabled>
                    <button type="button" id="previewNewsletterBtn" class="btn btn-sm" style="padding: 0.35rem 0.65rem;" disabled><?php echo htmlspecialchars($settings['post_sidebar_newsletter_btn'] ?? 'Gửi'); ?></button>
                </div>
            </div>

            <!-- Preview CTA Widget -->
            <div class="preview-widget-box" data-preview-key="cta" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                    <strong id="previewCtaTitle" style="font-size: 0.875rem; color: var(--foreground);">
                        <?php echo htmlspecialchars($settings['post_sidebar_cta_title'] ?? 'Tư vấn chiến lược SEO & AI'); ?>
                    </strong>
                    <span id="previewCtaBadge" class="badge-pill-tag" style="font-size: 0.65rem;"><?php echo htmlspecialchars($settings['post_sidebar_cta_badge'] ?? 'Tư vấn'); ?></span>
                </div>
                <p id="previewCtaDesc" style="font-size: 0.75rem; color: var(--muted-foreground); margin: 0 0 0.75rem; line-height: 1.4;">
                    <?php echo htmlspecialchars($settings['post_sidebar_cta_desc'] ?? 'Đồng hành cùng doanh nghiệp của bạn...'); ?>
                </p>
                <button type="button" id="previewCtaBtn" class="btn btn-sm" style="width: 100%; justify-content: center;" disabled>
                    <?php echo htmlspecialchars($settings['post_sidebar_cta_btn_text'] ?? 'Liên hệ tư vấn ngay'); ?>
                </button>
            </div>

            <!-- Preview Categories Widget -->
            <div class="preview-widget-box" data-preview-key="categories" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                <strong style="font-size: 0.875rem; color: var(--foreground); display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.5rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/></svg>
                    <span id="previewCategoriesTitle"><?php echo htmlspecialchars($settings['post_sidebar_categories_title'] ?? 'Chuyên mục đề xuất'); ?></span>
                </strong>
                <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.3rem 0.5rem; background: var(--secondary); border-radius: 4px;">
                        <span>Tối ưu SEO On-page</span>
                        <span style="color: var(--muted-foreground);">12</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.3rem 0.5rem; background: var(--secondary); border-radius: 4px;">
                        <span>Hướng dẫn AI</span>
                        <span style="color: var(--muted-foreground);">8</span>
                    </div>
                </div>
            </div>

            <!-- Preview Custom HTML Widgets -->
            <?php foreach ($customHtmlWidgets as $hw): 
                $hwId = $hw['id'] ?? 'html_1';
                $hwTitle = $hw['title'] ?? 'Tiện ích HTML';
                $hwContent = $hw['content'] ?? '';
            ?>
                <div class="preview-widget-box" data-preview-key="<?php echo $hwId; ?>" style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;">
                    <strong style="font-size: 0.875rem; color: var(--foreground); display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.35rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        <span class="preview-html-title"><?php echo htmlspecialchars($hwTitle ?: 'Tiện ích tùy chỉnh'); ?></span>
                    </strong>
                    <div class="preview-html-content" style="font-size: 0.75rem; color: var(--muted-foreground); padding: 0.5rem; background: var(--secondary); border-radius: 4px; border: 1px dashed var(--border); word-break: break-word;">
                        <?php echo !empty($hwContent) ? $hwContent : '[Mã HTML / Widget tự do]'; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <button type="submit" form="sidebarSettingsForm" class="btn" style="width: 100%; justify-content: center;">
                Lưu cấu hình cột phải
            </button>
        </div>
    </div>
</div>
