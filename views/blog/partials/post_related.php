<?php
/**
 * Related Articles Partial
 */
?>
<?php if (!empty($related)): ?>
    <section class="related-posts-section" aria-label="Bài viết cùng chuyên mục">
        <div class="related-header">
            <h3 class="related-title">Bài viết cùng chuyên mục</h3>
        </div>
        <div class="related-grid">
            <?php foreach ($related as $rel): ?>
                <?php 
                $relImg = $rel['featured_image'] ?: ($catImages[$rel['category_id']] ?? $defaultImg);
                ?>
                <article class="article-card">
                    <a href="/blog/<?php echo htmlspecialchars($rel['slug']); ?>" class="article-card-media">
                        <img src="<?php echo htmlspecialchars($relImg); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" loading="lazy" decoding="async">
                    </a>
                    <div class="article-card-meta">
                        <span class="meta-date"><?php echo formatDate($rel['published_at'] ?: $rel['created_at'], 'd/m/Y'); ?></span>
                    </div>
                    <a href="/blog/<?php echo htmlspecialchars($rel['slug']); ?>">
                        <h4 class="article-card-title">
                            <?php echo htmlspecialchars($rel['title']); ?>
                        </h4>
                    </a>
                    <p class="article-card-desc">
                        <?php echo htmlspecialchars($rel['excerpt']); ?>
                    </p>
                    <div class="article-card-footer">
                        <a href="/blog/<?php echo htmlspecialchars($rel['slug']); ?>" class="card-read-link">
                            <span>Xem chi tiết</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
