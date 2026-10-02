<?php
/**
 * @file views/blog/blog_list.php
 * @description Blog magazine archive view with search filtering, category tabs, and pagination.
 *
 * Layer:
 * - Presentation / View Page
 *
 * Responsibilities:
 * - Render blog articles grid with responsive cards and category badges.
 * - Provide search bar and suggested keyword tags.
 * - Render category filter pills and pagination controls.
 *
 * Security:
 * - HTML entity encoding for all titles, excerpts, and search queries.
 *
 * Dependencies:
 * - Layout header and footer, template_helper functions.
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

include APP_ROOT . '/views/layouts/header.php';

$searchQuery = $search_query ?? '';
$isSearching = $searchQuery !== '';
$activeCatSlug = $_GET['category'] ?? '';
?>

<main class="pt-20 bg-slate-50 min-h-screen">
  <!-- Hero Section -->
  <section class="bg-primary text-white pt-24 pb-24 relative overflow-hidden lg:pt-28">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] pointer-events-none"></div>
    <div class="mx-auto max-w-7xl px-5 lg:px-8 relative z-10 text-center">
      
      <!-- Breadcrumb -->
      <nav class="flex items-center justify-center gap-2 text-xs font-semibold text-white/60 mb-6 uppercase tracking-wider" aria-label="Breadcrumb">
        <a href="/" class="hover:text-white transition-colors">Trang chủ</a>
        <i class="bi bi-chevron-right text-[10px]"></i>
        <span class="text-white">Blog & Tin tức</span>
      </nav>

      <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1 text-xs font-bold text-white/90 mb-4 border border-white/10 uppercase tracking-widest">
        <i class="bi bi-journal-text text-[10px] text-amber-400"></i> Góc chia sẻ kiến thức
      </span>
      <h1 class="text-3xl sm:text-[2.75rem] font-black font-display tracking-tight leading-tight mb-4">Blog & Tin Tức Du Học Nhật Bản</h1>
      <p class="text-base sm:text-lg text-white/85 max-w-3xl leading-relaxed mx-auto">
        Cập nhật thông tin tuyển sinh, cẩm nang visa, kinh nghiệm săn học bổng và cuộc sống du học sinh Nhật Bản.
      </p>

      <!-- Search Bar -->
      <div class="max-w-xl mx-auto mt-8">
        <form method="GET" action="/blog" class="relative flex items-center bg-white rounded-full p-1.5 shadow-hard">
          <i class="bi bi-search text-slate-400 ml-4 mr-2 text-lg"></i>
          <input 
            type="text" 
            name="q" 
            value="<?php echo htmlspecialchars($searchQuery); ?>" 
            placeholder="Tìm kiếm bài viết, visa, học bổng, trường học..." 
            class="flex-1 bg-transparent border-none outline-none text-sm text-ink font-medium placeholder:text-slate-400"
          >
          <?php if ($isSearching): ?>
            <a href="/blog" class="p-2 text-slate-400 hover:text-slate-600 mr-1" title="Xóa tìm kiếm">
              <i class="bi bi-x-circle-fill"></i>
            </a>
          <?php endif; ?>
          <button type="submit" class="bg-primary hover:bg-slate-800 text-white rounded-full px-6 py-2.5 text-xs font-bold transition-all shadow-md">
            Tìm kiếm
          </button>
        </form>

        <!-- Suggested Search Keywords -->
        <?php if (!empty($suggested_search_terms)): ?>
        <div class="flex items-center justify-center flex-wrap gap-2 mt-4 text-xs text-white/80">
          <span class="font-semibold text-white/60">Gợi ý:</span>
          <?php foreach ($suggested_search_terms as $term): ?>
            <a href="/blog?q=<?php echo urlencode($term); ?>" class="bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full text-white transition-colors">
              <?php echo htmlspecialchars($term); ?>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <!-- Content Section -->
  <section class="py-12 sm:py-16 -mt-8 relative z-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-5">
      
      <!-- Category Filter Tabs -->
      <div class="flex flex-wrap items-center justify-center gap-2.5 mb-12">
        <a href="/blog" class="inline-flex px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold transition-all <?php echo empty($activeCatSlug) ? 'bg-primary text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'; ?>">
          Tất cả bài viết
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="/category/<?php echo htmlspecialchars($cat['slug']); ?>" 
           class="inline-flex px-5 py-2.5 rounded-full text-xs uppercase tracking-wider font-bold transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-primary">
          <?php echo htmlspecialchars($cat['name']); ?>
        </a>
        <?php endforeach; ?>
      </div>

      <!-- Articles Grid -->
      <?php if (empty($posts)): ?>
        <div class="text-center py-20 bg-white rounded-[2.5rem] border border-slate-100 shadow-soft max-w-2xl mx-auto p-8">
          <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
            <i class="bi bi-journal-x"></i>
          </div>
          <h3 class="text-xl font-bold text-primary font-display mb-2">Không tìm thấy bài viết</h3>
          <p class="text-sm text-muted mb-6">Không có bài viết nào phù hợp với từ khóa của bạn. Vui lòng thử lại với từ khóa khác.</p>
          <a href="/blog" class="inline-flex px-6 py-2.5 bg-primary text-white rounded-full text-xs font-bold hover:bg-slate-800 transition-colors">
            Xem tất cả bài viết
          </a>
        </div>
      <?php else: ?>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($posts as $post): ?>
          <article class="group bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-soft hover:shadow-medium transition-all duration-300 flex flex-col h-full hover:-translate-y-1">
            <div class="relative overflow-hidden aspect-[16/10]">
              <img src="<?php echo htmlspecialchars(getPostImage($post['featured_image'])); ?>" 
                   alt="<?php echo htmlspecialchars($post['title']); ?>"
                   loading="lazy" decoding="async"
                   class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
              <div class="absolute top-4 left-4">
                <span class="inline-block rounded-full bg-white/95 backdrop-blur-sm px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-primary shadow-sm">
                  <?php echo htmlspecialchars($post['category_name'] ?? 'Tin tức'); ?>
                </span>
              </div>
            </div>

            <div class="p-6 sm:p-7 flex flex-col flex-1">
              <h2 class="text-lg font-bold text-primary font-display mb-3 line-clamp-2 hover:text-slate-700 transition-colors">
                <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                  <?php echo htmlspecialchars($post['title']); ?>
                </a>
              </h2>
              <p class="text-xs sm:text-[13px] text-muted mb-6 line-clamp-3 leading-relaxed flex-1">
                <?php echo htmlspecialchars(truncateText($post['excerpt'] ?: strip_tags($post['content']), 110)); ?>
              </p>
              
              <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-auto text-xs text-slate-400">
                <span class="flex items-center gap-1.5"><i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="font-bold text-primary hover:gap-1.5 transition-all inline-flex items-center gap-1">
                  Đọc tiếp <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total > $per_page): 
          $totalPages = (int)ceil($total / $per_page);
        ?>
        <div class="flex items-center justify-center gap-2 mt-16">
          <?php if ($page > 1): ?>
            <a href="/blog?page=<?php echo $page - 1; ?><?php echo $isSearching ? '&q=' . urlencode($searchQuery) : ''; ?>" class="h-10 w-10 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm">
              <i class="bi bi-chevron-left text-xs"></i>
            </a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
              <span class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shadow-md">
                <?php echo $i; ?>
              </span>
            <?php elseif ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
              <a href="/blog?page=<?php echo $i; ?><?php echo $isSearching ? '&q=' . urlencode($searchQuery) : ''; ?>" class="h-10 w-10 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs hover:bg-slate-100 transition-colors shadow-sm">
                <?php echo $i; ?>
              </a>
            <?php elseif (abs($i - $page) == 3): ?>
              <span class="px-1 text-slate-400 text-xs">...</span>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a href="/blog?page=<?php echo $page + 1; ?><?php echo $isSearching ? '&q=' . urlencode($searchQuery) : ''; ?>" class="h-10 w-10 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-primary hover:text-white transition-colors shadow-sm">
              <i class="bi bi-chevron-right text-xs"></i>
            </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>

    </div>
  </section>
</main>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
