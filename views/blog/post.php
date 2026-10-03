<?php include APP_ROOT . '/views/layouts/header.php'; ?>

<?php
// Calculate approximate reading time (average 200 words per minute)
$wordCount = mb_strlen(strip_tags($post['content']), 'UTF-8');
$readingTime = max(1, (int)ceil($wordCount / 600));

// Parse keywords
$tags = [];
if (!empty($post['meta_keywords'])) {
    $tags = array_filter(array_map('trim', explode(',', $post['meta_keywords'])));
}

// Category images mapper
$catImages = [
    1 => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=80',
    2 => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
    3 => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
];
$defaultImg = 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80';
$post_img = getPostFeaturedImage($post);

$sidebar_settings = $sidebar_settings ?? [];
$widgetOrderStr = $sidebar_settings['post_sidebar_widget_order'] ?? 'trending,banner,newsletter,cta,categories,custom_html';
$widgetOrder = array_filter(array_map('trim', explode(',', $widgetOrderStr)));

$authorAvatar = getAuthorAvatar($post['author_avatar'] ?? null, $post['author_name'] ?? 'Admin');
$isSticky = ($sidebar_settings['post_sidebar_sticky'] ?? '1') === '1';
$showAuthorBox = ($sidebar_settings['post_author_box_enabled'] ?? '1') === '1';

// Widget 1: Trending
$showTrending = ($sidebar_settings['post_sidebar_trending_enabled'] ?? '1') === '1' && !empty($trending);
$trendingTitle = $sidebar_settings['post_sidebar_trending_title'] ?? 'Đọc nhiều nhất';

// Widget 2: Banner Ads
$showBanner = ($sidebar_settings['post_sidebar_banner_enabled'] ?? '0') === '1' && !empty($sidebar_settings['post_sidebar_banner_image']);
$bannerTitle = $sidebar_settings['post_sidebar_banner_title'] ?? '';
$bannerBadge = $sidebar_settings['post_sidebar_banner_badge'] ?? '';
$bannerImage = $sidebar_settings['post_sidebar_banner_image'] ?? '';
$bannerUrl = $sidebar_settings['post_sidebar_banner_url'] ?? '#';
$bannerAlt = $sidebar_settings['post_sidebar_banner_alt'] ?? 'Banner quảng cáo';
$bannerNewTab = ($sidebar_settings['post_sidebar_banner_new_tab'] ?? '1') === '1';

// Widget 3: Newsletter
$showNewsletter = ($sidebar_settings['post_sidebar_newsletter_enabled'] ?? '1') === '1';
$newsletterTitle = $sidebar_settings['post_sidebar_newsletter_title'] ?? 'Bản tin SEO & AI';
$newsletterDesc = $sidebar_settings['post_sidebar_newsletter_desc'] ?? 'Cập nhật các thuật toán mới nhất của Google và chiến lược AI mỗi tuần.';
$newsletterBtn = $sidebar_settings['post_sidebar_newsletter_btn'] ?? 'Gửi';

// Widget 4: Custom CTA
$showCta = ($sidebar_settings['post_sidebar_cta_enabled'] ?? '0') === '1';
$ctaTitle = $sidebar_settings['post_sidebar_cta_title'] ?? 'Tư vấn chiến lược SEO & AI';
$ctaDesc = $sidebar_settings['post_sidebar_cta_desc'] ?? 'Đồng hành cùng doanh nghiệp của bạn xây dựng hệ thống tăng trưởng hữu cơ bền vững.';
$ctaBtnText = $sidebar_settings['post_sidebar_cta_btn_text'] ?? 'Liên hệ tư vấn ngay';
$ctaBtnUrl = $sidebar_settings['post_sidebar_cta_btn_url'] ?? 'mailto:admin@example.com';
$ctaBadge = $sidebar_settings['post_sidebar_cta_badge'] ?? 'Tư vấn';

// Widget 5: Categories
$showCategories = ($sidebar_settings['post_sidebar_categories_enabled'] ?? '0') === '1' && !empty($sidebar_categories);
$categoriesTitle = $sidebar_settings['post_sidebar_categories_title'] ?? 'Chuyên mục đề xuất';

// Multiple Custom HTML Widgets
$customHtmlWidgets = [];
if (!empty($sidebar_settings['post_sidebar_custom_html_widgets'])) {
    $decoded = json_decode($sidebar_settings['post_sidebar_custom_html_widgets'], true);
    if (is_array($decoded)) {
        $customHtmlWidgets = $decoded;
    }
}
$customHtmlMap = [];
foreach ($customHtmlWidgets as $hw) {
    if (!empty($hw['id'])) {
        $customHtmlMap[$hw['id']] = $hw;
    }
}
$heroTitle = $post['title'];
$heroEyebrow = $post['category_name'] ?? 'Bài viết';
$heroCurrent = $post['title'];
$heroParent = ['url' => '/category/' . ($post['category_slug'] ?? 'tin-tuc'), 'label' => $post['category_name'] ?? 'Chuyên mục'];
$heroDescription = $post['excerpt'] ?? '';
include APP_ROOT . '/views/layouts/partials/page_hero.php';
?>

<!-- Scroll Reading Progress Bar -->
<div id="readingProgressBar"></div>

<div class="post-layout-container pt-24 px-4 sm:px-6 lg:px-8">
    <!-- 2-Column Split Layout -->
    <div class="post-2col-layout">
        <!-- Main Content (Left Column) -->
        <main class="post-main-column">
            <!-- Article Header -->
            <header class="post-header">
                <div class="post-badges-row">
                    <a href="/category/<?php echo htmlspecialchars($post['category_slug'] ?? 'tin-tuc'); ?>" class="post-cat-badge">
                        <?php echo htmlspecialchars($post['category_name'] ?? 'Chung'); ?>
                    </a>
                    <div class="post-info-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span><?php echo $readingTime; ?> phút đọc</span>
                    </div>
                    <div class="post-info-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span><?php echo number_format($post['views']); ?> lượt xem</span>
                    </div>
                    <div class="post-info-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span><?php echo formatDate($post['published_at'] ?: $post['created_at'], 'd/m/Y'); ?></span>
                    </div>
                </div>

                <!-- Author & Social Share Bar -->
                <div class="post-author-share-bar">
                    <div class="post-author-info">
                        <img src="<?php echo htmlspecialchars($authorAvatar); ?>" alt="Tác giả" class="post-author-avatar" width="44" height="44" style="object-fit: cover;">
                        <div>
                            <h3 class="post-author-name"><?php echo htmlspecialchars($post['author_name'] ?? 'Tư vấn viên du học'); ?></h3>
                            <p class="post-author-role">Tư vấn viên du học</p>
                        </div>
                    </div>

                    <div class="post-share-actions" aria-label="Chia sẻ bài viết">
                        <button type="button" class="share-action-btn" onclick="copyPostLink()" title="Sao chép liên kết" aria-label="Sao chép liên kết">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </button>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonical_url); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" rel="noopener noreferrer" class="share-action-btn" title="Twitter / X" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonical_url); ?>" target="_blank" rel="noopener noreferrer" class="share-action-btn" title="Facebook" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($canonical_url); ?>" target="_blank" rel="noopener noreferrer" class="share-action-btn" title="LinkedIn" aria-label="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect x="2" y="9" width="4" height="12"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Cover Image -->
            <div class="post-hero-image-wrap">
                <img src="<?php echo htmlspecialchars($post_img); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" fetchpriority="high" decoding="async" onerror="this.src='https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80'">
            </div>

            <!-- In-Article Table of Contents (TOC) -->
            <?php if (!empty($toc)): ?>
                <nav class="toc-container" id="tableOfContents" aria-label="Mục lục bài viết">
                    <div class="toc-header-bar" onclick="toggleTOC()">
                        <div class="toc-heading">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <span>Mục lục nội dung</span>
                        </div>
                        <span class="toc-toggle-indicator" id="tocToggleText">Thu gọn</span>
                    </div>
                    <ul class="toc-nav-list" id="tocList">
                        <?php foreach ($toc as $item): ?>
                            <li class="toc-nav-item level-<?php echo $item['level']; ?>">
                                <a href="#<?php echo htmlspecialchars($item['id']); ?>">
                                    <span>&bull;</span>
                                    <span><?php echo htmlspecialchars($item['label'] ?? $item['text'] ?? ''); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <!-- Main Post Body HTML Content -->
            <article class="article-content-body post-content-body rich-text-wrapper prose">
                <?php echo $post['content']; ?>
            </article>

            <!-- Tags Section -->
            <?php if (!empty($tags)): ?>
                <div class="post-bottom-tags-bar">
                    <div class="post-tags-list">
                        <span class="tag-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>Từ khóa:
                        </span>
                        <?php foreach ($tags as $tag): ?>
                            <a href="/?q=<?php echo urlencode($tag); ?>" class="post-tag-pill">
                                #<?php echo htmlspecialchars($tag); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Author Box -->
            <?php if ($showAuthorBox): ?>
                <div class="author-bio-card">
                    <img src="<?php echo htmlspecialchars($authorAvatar); ?>" alt="<?php echo htmlspecialchars($post['author_name'] ?? 'Tác giả'); ?>" class="author-bio-avatar" width="64" height="64" loading="lazy" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff&size=128'">
                    <div class="author-bio-content">
                        <span class="author-bio-title"><?php echo htmlspecialchars($post['author_name'] ?? 'Tư vấn viên du học'); ?></span>
                        <p class="author-bio-desc"><?php echo htmlspecialchars($post['author_bio'] ?? '', ENT_QUOTES, 'UTF-8') ?: 'Tác giả tại ' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . '.'; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <!-- Sidebar (Right Column) -->
        <?php include __DIR__ . '/partials/post_sidebar.php'; ?>
    </div>

    <!-- Related Articles Section -->
    <?php include __DIR__ . '/partials/post_related.php'; ?>
</div>

<!-- Toast notification for copy link -->
<div id="copyToast" class="copy-toast">✓ Đã sao chép liên kết bài viết!</div>

<script src="/assets/js/post.js"></script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
