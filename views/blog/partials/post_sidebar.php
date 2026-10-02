<?php
/**
 * Frontend Post Right Sidebar Partial
 */
?>
<aside class="post-sidebar-column">
    <div class="post-sidebar-sticky <?php echo $isSticky ? 'sticky-active' : ''; ?>">
        <?php foreach ($widgetOrder as $wKey): ?>
            <?php if ($wKey === 'trending' && $showTrending): ?>
                <!-- Widget: Trending Posts -->
                <div class="sidebar-widget">
                    <div class="sidebar-widget-header">
                        <h3 class="sidebar-widget-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                <polyline points="17 6 23 6 23 12"/>
                            </svg>
                            <span><?php echo htmlspecialchars($trendingTitle); ?></span>
                        </h3>
                    </div>
                    <div class="sidebar-trending-list">
                        <?php 
                        $rank = 1;
                        foreach ($trending as $tPost): 
                            $tImg = $tPost['featured_image'] ?: ($catImages[$tPost['category_id']] ?? $defaultImg);
                        ?>
                            <a href="/blog/<?php echo htmlspecialchars($tPost['slug']); ?>" class="trending-post-item">
                                <span class="trending-rank-num"><?php echo sprintf('%02d', $rank++); ?></span>
                                <div class="trending-item-content">
                                    <h4 class="trending-item-title"><?php echo htmlspecialchars($tPost['title']); ?></h4>
                                    <span class="trending-item-meta"><?php echo formatDate($tPost['published_at'] ?: $tPost['created_at'], 'd/m/Y'); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ($wKey === 'banner' && $showBanner): ?>
                <!-- Widget: Custom Image Banner Ads -->
                <div class="sidebar-widget sidebar-banner-widget">
                    <?php if ($bannerTitle): ?>
                        <div class="sidebar-widget-header">
                            <h3 class="sidebar-widget-title"><?php echo htmlspecialchars($bannerTitle); ?></h3>
                            <?php if ($bannerBadge): ?>
                                <span class="badge-pill-tag"><?php echo htmlspecialchars($bannerBadge); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($bannerUrl); ?>" <?php echo $bannerNewTab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="sidebar-banner-link">
                        <img src="<?php echo htmlspecialchars($bannerImage); ?>" alt="<?php echo htmlspecialchars($bannerAlt); ?>" loading="lazy">
                    </a>
                </div>

            <?php elseif ($wKey === 'newsletter' && $showNewsletter): ?>
                <!-- Widget: Newsletter Subscription -->
                <div class="sidebar-widget sidebar-newsletter-widget">
                    <div class="sidebar-widget-header">
                        <h3 class="sidebar-widget-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <span><?php echo htmlspecialchars($newsletterTitle); ?></span>
                        </h3>
                    </div>
                    <p class="sidebar-newsletter-desc">
                        <?php echo htmlspecialchars($newsletterDesc); ?>
                    </p>
                    <form onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận tin!');">
                        <div class="sidebar-newsletter-input-group">
                            <input type="email" placeholder="Email của bạn..." class="sidebar-newsletter-input" required>
                            <button type="submit" class="sidebar-newsletter-btn"><?php echo htmlspecialchars($newsletterBtn); ?></button>
                        </div>
                    </form>
                </div>

            <?php elseif ($wKey === 'cta' && $showCta): ?>
                <!-- Widget: Custom CTA Banner -->
                <div class="sidebar-widget" style="background: var(--secondary); border: 1px solid var(--border);">
                    <div class="sidebar-widget-header">
                        <h3 class="sidebar-widget-title" style="font-size: 0.95rem;">
                            <?php echo htmlspecialchars($ctaTitle); ?>
                        </h3>
                        <?php if ($ctaBadge): ?>
                            <span class="badge-pill-tag" style="font-size: 0.65rem;"><?php echo htmlspecialchars($ctaBadge); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="sidebar-newsletter-desc" style="font-size: 0.8rem; margin-bottom: 0.85rem;">
                        <?php echo htmlspecialchars($ctaDesc); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($ctaBtnUrl); ?>" class="btn btn-sm" style="width: 100%; justify-content: center; text-decoration: none;">
                        <?php echo htmlspecialchars($ctaBtnText); ?>
                    </a>
                </div>

            <?php elseif ($wKey === 'categories' && $showCategories): ?>
                <!-- Widget: Categories Quick Links -->
                <div class="sidebar-widget">
                    <div class="sidebar-widget-header">
                        <h3 class="sidebar-widget-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                            <span><?php echo htmlspecialchars($categoriesTitle); ?></span>
                        </h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <?php foreach ($sidebar_categories as $scat): ?>
                            <a href="/category/<?php echo htmlspecialchars($scat['slug']); ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0.6rem; border-radius: 6px; color: var(--foreground); font-size: 0.85rem; text-decoration: none; background: var(--card); border: 1px solid var(--border); transition: background 0.15s;">
                                <span><?php echo htmlspecialchars($scat['name']); ?></span>
                                <span style="font-size: 0.75rem; color: var(--muted-foreground);"><?php echo $scat['post_count'] ?? 0; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif (isset($customHtmlMap[$wKey]) && ($customHtmlMap[$wKey]['enabled'] ?? '1') === '1' && !empty(trim($customHtmlMap[$wKey]['content'] ?? ''))): 
                $hw = $customHtmlMap[$wKey];
            ?>
                <!-- Widget: Custom HTML Widget (<?php echo htmlspecialchars($wKey); ?>) -->
                <div class="sidebar-widget">
                    <?php if (!empty($hw['title'])): ?>
                        <div class="sidebar-widget-header">
                            <h3 class="sidebar-widget-title" style="font-size: 0.95rem;">
                                <?php echo htmlspecialchars($hw['title']); ?>
                            </h3>
                        </div>
                    <?php endif; ?>
                    <div class="sidebar-custom-html-body" style="font-size: 0.875rem; line-height: 1.6;">
                        <?php echo $hw['content']; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</aside>
