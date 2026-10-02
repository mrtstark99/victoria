<?php
/** Public directory of study abroad programs. */
include APP_ROOT . '/views/layouts/header.php';
$programImages = [
    'japanese-language-school-program' => '/assets/images/program_language.jpg',
    'vocational-school-program' => '/assets/images/program_senmon.jpg',
    'specified-skilled-worker-program' => '/assets/images/program_ssw.jpg',
    'university-and-graduate-program' => '/assets/images/program_university.webp',
];
?>
<main class="bg-slate-50 min-h-screen pb-20">
  <?php
    $heroTitle = 'Các chương trình du học Nhật Bản';
    $heroEyebrow = 'Chương trình du học';
    $heroCurrent = $heroTitle;
    $heroDescription = 'Tìm hiểu từng lộ trình học tập và các gói hồ sơ phù hợp với mục tiêu của bạn.';
    include APP_ROOT . '/views/layouts/partials/page_hero.php';
  ?>

  <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
    <?php if (empty($services)): ?>
      <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center text-slate-600">Hiện chưa có chương trình nào được cập nhật.</div>
    <?php else: ?>
      <div class="grid grid-cols-1 gap-6">
        <?php foreach ($services as $program):
          $image = $programImages[$program['slug']] ?? '/assets/images/program_language.jpg';
          $benefits = $program['packages'][0]['features'] ?? [];
        ?>
          <article class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:shadow-xl md:h-[280px] md:flex-row">
            <div class="relative h-32 shrink-0 overflow-hidden bg-primary/5 md:h-full md:w-[28%]">
              <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($program['title'], ENT_QUOTES, 'UTF-8') ?>" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/15 to-transparent"></div>
              <span class="absolute bottom-4 left-5 flex h-11 w-11 items-center justify-center rounded-xl border border-white/30 bg-white/20 text-xl text-white backdrop-blur md:bottom-5 md:left-5"><i class="bi <?= htmlspecialchars($program['icon'] ?: 'bi-mortarboard', ENT_QUOTES, 'UTF-8') ?>"></i></span>
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-center px-5 py-5 md:px-7 md:py-6">
              <h2 class="mb-2 font-display text-lg font-extrabold leading-tight text-primary sm:text-xl"><?= htmlspecialchars($program['title'], ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="line-clamp-2 text-sm leading-relaxed text-slate-600"><?= htmlspecialchars($program['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
              <?php if (!empty($benefits)): ?>
                <ul class="mt-3 space-y-1.5 overflow-hidden">
                  <?php foreach (array_slice($benefits, 0, 3) as $benefit): ?>
                    <li class="flex items-start gap-2 text-xs font-medium leading-snug text-slate-700 sm:text-sm"><i class="bi bi-check-circle-fill mt-0.5 shrink-0 text-primary"></i><span class="line-clamp-1"><?= htmlspecialchars((string)$benefit, ENT_QUOTES, 'UTF-8') ?></span></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
            <div class="flex shrink-0 items-center justify-between gap-4 border-t border-slate-100 px-5 py-3 md:w-[25%] md:flex-col md:justify-center md:border-l md:border-t-0 md:px-6 md:py-8">
              <p class="whitespace-nowrap text-2xl font-black tracking-tight text-orange-600 sm:text-3xl"><?= number_format((float)($program['price'] ?? 0), 0, ',', '.') ?><span class="ml-1 text-xs font-bold text-slate-500">VND</span></p>
              <a href="/services/<?= rawurlencode($program['slug']) ?>" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-center text-xs font-bold text-white transition hover:bg-slate-800 sm:px-5 sm:text-sm">
                Xem chi tiết lộ trình <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
