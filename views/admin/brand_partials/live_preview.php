<?php
/**
 * Live Brand Preview Simulator Right Column Partial
 */
?>
<!-- Right Column: Live Brand Preview Simulator -->
<div>
    <div class="card" style="position: sticky; top: calc(var(--topbar-height) + 1.5rem); margin-bottom: 0; background: var(--secondary);">
        <div class="card-header" style="margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
            <h4 class="card-title" style="font-size: 0.95rem; display: flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <span>Mô phỏng hiển thị Thương hiệu</span>
            </h4>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            <!-- 1. Simulated Browser Tab -->
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.4rem; display: block;">1. Tab trình duyệt (Favicon &amp; Tiêu đề):</span>
                <div style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 0.6rem 0.85rem; display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                    <img id="previewTabFavicon" src="<?php echo !empty($settings['site_favicon_url']) ? htmlspecialchars($settings['site_favicon_url']) : 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%236366f1%22/><text y=%22.9em%22 font-size=%2280%22 x=%2250%%22 text-anchor=%22middle%22 fill=%22white%22 font-weight=%22bold%22>M</text></svg>'; ?>" alt="Favicon" style="width: 18px; height: 18px; object-fit: contain; border-radius: 3px;">
                    <span id="previewTabTitle" style="color: var(--foreground); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                        <?php echo htmlspecialchars(($settings['site_name'] ?? 'MinimaList') . ' - ' . ($settings['site_slogan'] ?? 'Chia sẻ kiến thức SEO & AI')); ?>
                    </span>
                    <span style="color: var(--muted-foreground); font-size: 0.9rem; cursor: default;">×</span>
                </div>
            </div>
            
            <!-- 2. Simulated Header Navigation Bar -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase;">2. Thanh Header thực tế:</span>
                    <span id="previewDisplayModeTag" style="font-size: 0.65rem; color: #6366f1; background: rgba(99,102,241,0.1); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 700;">Logo + Text</span>
                </div>
                <div style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; overflow: hidden;">
                    
                    <!-- Header Logo Wrap (Unified) -->
                    <div class="logo-wrap" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <!-- Image Logo Icon -->
                        <img id="previewImageLogoImg" src="<?php echo htmlspecialchars($settings['site_logo_url'] ?? ''); ?>" alt="Site Logo" style="<?php echo empty($settings['site_logo_url']) ? 'display: none;' : 'display: block;'; ?> max-height: 28px; max-width: 110px; object-fit: contain;">
                        
                        <!-- Badge Icon (if no image) -->
                        <div id="previewBadge" class="logo-badge" style="<?php echo !empty($settings['site_logo_url']) ? 'display: none;' : 'display: flex;'; ?> width: 26px; height: 26px; align-items: center; justify-content: center; background: #6366f1; color: #fff; font-weight: 800; font-size: 0.8rem; border-radius: 5px;">
                            <?php echo htmlspecialchars($settings['site_logo_badge'] ?? 'M'); ?>
                        </div>

                        <!-- Brand Text -->
                        <span id="previewBrandText" class="logo" style="font-weight: 800; font-size: 1.05rem; letter-spacing: -0.02em; display: inline-flex; align-items: center; color: var(--foreground);">
                            <?php echo htmlspecialchars($settings['site_name'] ?? 'MinimaList'); ?>
                        </span>
                    </div>

                    <!-- Sample Nav links -->
                    <div style="display: flex; gap: 0.4rem; font-size: 0.75rem; color: var(--muted-foreground); align-items: center;">
                        <span style="color: var(--foreground); font-weight: 600;">Trang chủ</span>
                        <span>SEO</span>
                        <span>AI</span>
                    </div>
                </div>
            </div>

            <!-- 3. Simulated Footer Info Box -->
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.4rem; display: block;">3. Chân trang Footer:</span>
                <div style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem; font-size: 0.8rem;">
                    <strong id="previewFooterTitle" style="color: var(--foreground); display: block; font-size: 1rem; margin-bottom: 0.35rem;">
                        <?php echo htmlspecialchars($settings['site_name'] ?? 'MinimaList'); ?>
                    </strong>
                    <p id="previewFooterDesc" style="color: var(--muted-foreground); margin: 0 0 0.75rem; line-height: 1.4;">
                        <?php echo htmlspecialchars($settings['footer_about_text'] ?: ($settings['site_slogan'] ?? 'Chia sẻ kiến thức SEO & AI thực chiến')); ?>
                    </p>

                    <!-- Simulated Contact Info -->
                    <div id="previewContactBox" style="display: flex; flex-direction: column; gap: 0.3rem; margin-bottom: 0.75rem; font-size: 0.75rem; color: var(--muted-foreground); border-top: 1px dashed var(--border); padding-top: 0.6rem;">
                        <div id="previewEmailWrap" style="display: flex; gap: 0.4rem; align-items: center;">
                            <span>✉</span>
                            <span id="previewEmailText"><?php echo htmlspecialchars($settings['site_email'] ?? 'admin@example.com'); ?></span>
                        </div>
                        <div id="previewPhoneWrap" style="display: flex; gap: 0.4rem; align-items: center;">
                            <span>☎</span>
                            <span id="previewPhoneText"><?php echo htmlspecialchars($settings['site_phone'] ?? '+84 123 456 789'); ?></span>
                        </div>
                        <div id="previewAddressWrap" style="display: flex; gap: 0.4rem; align-items: center;">
                            <span>⚲</span>
                            <span id="previewAddressText"><?php echo htmlspecialchars($settings['site_address'] ?? 'Hà Nội, Việt Nam'); ?></span>
                        </div>
                    </div>

                    <!-- Simulated Socials Bar -->
                    <div id="previewSocialsBar" style="display: flex; gap: 0.4rem; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); flex-wrap: wrap;">
                        <span id="pSocialFb" style="font-size: 0.7rem; padding: 0.15rem 0.45rem; background: var(--secondary); border-radius: 4px; color: var(--foreground); <?php echo empty($settings['social_facebook']) ? 'opacity: 0.35;' : 'opacity: 1; font-weight: 700;'; ?>">Facebook</span>
                        <span id="pSocialTw" style="font-size: 0.7rem; padding: 0.15rem 0.45rem; background: var(--secondary); border-radius: 4px; color: var(--foreground); <?php echo empty($settings['social_twitter']) ? 'opacity: 0.35;' : 'opacity: 1; font-weight: 700;'; ?>">Twitter/X</span>
                        <span id="pSocialGh" style="font-size: 0.7rem; padding: 0.15rem 0.45rem; background: var(--secondary); border-radius: 4px; color: var(--foreground); <?php echo empty($settings['social_github']) ? 'opacity: 0.35;' : 'opacity: 1; font-weight: 700;'; ?>">GitHub</span>
                        <span id="pSocialLi" style="font-size: 0.7rem; padding: 0.15rem 0.45rem; background: var(--secondary); border-radius: 4px; color: var(--foreground); <?php echo empty($settings['social_linkedin']) ? 'opacity: 0.35;' : 'opacity: 1; font-weight: 700;'; ?>">LinkedIn</span>
                        <span id="pSocialYt" style="font-size: 0.7rem; padding: 0.15rem 0.45rem; background: var(--secondary); border-radius: 4px; color: var(--foreground); <?php echo empty($settings['social_youtube']) ? 'opacity: 0.35;' : 'opacity: 1; font-weight: 700;'; ?>">YouTube</span>
                    </div>

                    <div id="previewFooterCopyright" style="font-size: 0.75rem; color: var(--muted-foreground);">
                        © <?php echo date('Y'); ?> <strong id="previewFooterBrand"><?php echo htmlspecialchars($settings['site_name'] ?? 'MinimaList'); ?></strong>. All rights reserved.
                    </div>
                </div>
            </div>

            <!-- 4. Simulated Google Search Snippet -->
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.4rem; display: block;">4. Kết quả tìm kiếm Google (SEO SERP):</span>
                <div style="background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1rem; font-size: 0.8rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.7rem; color: var(--muted-foreground); margin-bottom: 0.25rem;">
                        <span style="color: #10b981;"><?php echo htmlspecialchars(rtrim(getSystemBaseUrl(), '/'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <h5 id="previewGoogleTitle" style="color: #3b82f6; font-size: 0.95rem; margin: 0 0 0.25rem; font-weight: 600; line-height: 1.3;">
                        <?php echo htmlspecialchars(($settings['site_name'] ?? 'MinimaList') . ' - ' . ($settings['site_slogan'] ?? 'Chia sẻ kiến thức SEO & AI')); ?>
                    </h5>
                    <p id="previewGoogleDesc" style="color: var(--muted-foreground); margin: 0; font-size: 0.75rem; line-height: 1.4;">
                        <?php echo htmlspecialchars($settings['default_meta_description'] ?: ($settings['site_slogan'] ?? 'Không gian chia sẻ kiến thức chọn lọc về SEO On-page, cấu trúc Topic Cluster...')); ?>
                    </p>
                </div>
            </div>

        </div>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <button type="submit" form="brandSettingsForm" class="btn" style="width: 100%; justify-content: center;">
                Lưu toàn bộ cấu hình Brand
            </button>
        </div>
    </div>
</div>
