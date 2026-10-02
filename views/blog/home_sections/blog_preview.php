    <section id="blog" class="home-section home-blog bg-white py-20 lg:py-28 relative">
      <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="home-section-heading mx-auto mb-16 max-w-2xl text-center">
          <span class="home-kicker">05 — Góc kiến thức</span>
          <h2 class="text-3xl sm:text-4xl font-bold text-primary font-display">Bài viết mới nhất</h2>
          <p class="mt-4 text-lg text-muted">Cập nhật kiến thức và kinh nghiệm sống tại Nhật Bản.</p>
        </div>

        <?php if (!empty($latest_posts)): ?>
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <?php foreach (array_slice($latest_posts, 0, 3) as $index => $post): ?>
          <article class="rounded-3xl overflow-hidden bg-white shadow-soft hover:shadow-medium transition-all duration-300 border border-slate-100 card-hover reveal reveal-delay-<?php echo $index * 100; ?>">
            <div class="relative overflow-hidden group">
                <?php if ($post['featured_image']): ?>
                <img 
                src="<?php echo getPostImage($post['featured_image']); ?>" 
                alt="<?php echo htmlspecialchars($post['title']); ?>"
                loading="lazy" decoding="async"
                class="w-full h-56 sm:h-52 object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <?php else: ?>
                <div class="w-full h-56 sm:h-52 bg-slate-100 flex items-center justify-center">
                    <i class="bi bi-image text-3xl text-slate-300"></i>
                </div>
                <?php endif; ?>
                <div class="absolute top-4 left-4">
                    <span class="inline-block rounded-full bg-white/90 backdrop-blur-sm px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-primary shadow-sm">
                        <?php echo htmlspecialchars($post['category_name']); ?>
                    </span>
                </div>
            </div>
            
            <div class="p-5 sm:p-6">
              <h3 class="text-[19px] font-bold text-primary font-display mb-3 line-clamp-2 hover:text-primary transition-colors">
                <a href="/blog/<?php echo $post['slug']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
              </h3>
              <p class="text-[14px] text-muted mb-6 line-clamp-3 leading-relaxed">
                <?php echo truncateText($post['excerpt'] ?: strip_tags($post['content']), 120); ?>
              </p>
              <div class="flex items-center justify-between mt-auto border-t border-slate-100 pt-5">
                <a href="/blog/<?php echo $post['slug']; ?>" class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-2 transition-all">
                  Đọc tiếp <i class="bi bi-arrow-right"></i>
                </a>
                <span class="text-[12px] text-slate-400 font-medium"><i class="bi bi-calendar3 mr-1"></i><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center sm:p-12">
          <h3 class="font-display text-xl font-bold text-primary">Bài viết mới đang được cập nhật</h3>
          <p class="mt-3 text-sm text-muted">Victoria Universal sẽ sớm chia sẻ thêm thông tin du học Nhật Bản.</p>
        </div>
        <?php endif; ?>

        <div class="mt-14 text-center">
          <a href="/blog" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-8 py-3.5 text-[15px] font-bold text-primary transition hover:bg-slate-50 hover:border-slate-300 shadow-sm">
            Xem tất cả bài viết <i class="bi bi-arrow-right ml-2 text-primary"></i>
          </a>
        </div>
      </div>
    </section>
