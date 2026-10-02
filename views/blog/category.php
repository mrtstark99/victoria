<?php include APP_ROOT . '/views/layouts/header.php'; ?>

<?php
// Category images mapper
$catImages = [
    1 => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=800&q=80',
    2 => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80',
    3 => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80',
];
$defaultImg = 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=800&q=80';
?>

<?php $heroTitle = $category['name']; $heroEyebrow = 'Chuyên mục'; $heroCurrent = $category['name']; $heroDescription = $category['description'] ?? ''; include APP_ROOT . '/views/layouts/partials/page_hero.php'; ?>
<div class="category-page-wrap px-4 sm:px-6 lg:px-8 mx-auto">

    <section style="margin-bottom: 4rem;">
        <?php if (empty($posts)): ?>
            <p style="color: var(--muted-foreground); padding: 3rem 0; text-align: center;">Chưa có bài viết nào thuộc chuyên mục này.</p>
        <?php else: ?>
            <!-- 3-Column Grid -->
            <div class="category-articles-grid">
                <?php foreach ($posts as $post): ?>
                    <?php 
                    $postImg = getPostFeaturedImage($post);
                    ?>
                    <article class="article-card">
                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="article-card-media">
                            <div class="img-container aspect-16-10">
                                <img src="<?php echo htmlspecialchars($postImg); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-zoom" loading="lazy" decoding="async" onerror="this.src='https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=800&q=80'">
                            </div>
                        </a>

                        <div class="article-card-meta">
                            <span class="meta-cat-pill"><?php echo htmlspecialchars($post['category_name'] ?? 'Chung'); ?></span>
                            <span class="meta-date">&bull; <?php echo formatDate($post['published_at'] ?: $post['created_at'], 'd/m/Y'); ?></span>
                        </div>

                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                            <h2 class="article-card-title">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h2>
                        </a>

                        <p class="article-card-desc">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>

                        <div class="article-card-footer">
                            <div class="article-card-author">
                                <img src="<?php echo htmlspecialchars(getAuthorAvatar($post['author_avatar'] ?? null)); ?>" alt="<?php echo htmlspecialchars($post['author_name'] ?? 'Tác giả'); ?>" width="26" height="26" loading="lazy" style="border-radius: 50%; object-fit: cover;">
                                <span><?php echo htmlspecialchars($post['author_name'] ?? 'Administrator'); ?></span>
                            </div>
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="card-read-link" aria-label="Đọc tiếp bài <?php echo htmlspecialchars($post['title']); ?>">
                                <span>Đọc tiếp</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/>
                                    <path d="m12 5 7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php
            $totalPages = (int)ceil($total / $per_page);
            if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <nav class="pagination-nav" aria-label="Phân trang chuyên mục">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="/category/<?php echo $category['slug']; ?>?page=<?php echo $i; ?>" class="page-btn <?php echo $i === $page ? 'active' : ''; ?>" aria-label="Trang <?php echo $i; ?>" <?php echo $i === $page ? 'aria-current="page"' : ''; ?>>
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
