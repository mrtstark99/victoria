<?php
$cost = $cost ?? \Models\CostPage::get();
$esc = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
include APP_ROOT . '/views/layouts/header.php';
?>
<main class="pt-20 bg-slate-50 min-h-screen">
  <?php $heroTitle = $cost['title']; $heroEyebrow = 'Bảng kê minh bạch'; $heroCurrent = 'Chi tiết chi phí'; $heroDescription = $cost['intro']; include APP_ROOT . '/views/layouts/partials/page_hero.php'; ?>
  <section class="py-16 -mt-10 relative z-20">
    <div class="mx-auto max-w-7xl px-5 lg:px-8 space-y-12">
      <?php foreach ($cost['sections'] as $index => $section): ?>
      <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-soft">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
          <div class="w-full lg:w-1/3">
            <span class="inline-block py-1 px-3.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase mb-4">Danh mục <?= $index + 1 ?></span>
            <h2 class="text-2xl font-bold text-midnight font-display mb-4"><?= $esc($section['title']) ?></h2>
            <p class="text-slate-500 text-sm leading-relaxed"><?= $esc($section['description']) ?></p>
          </div>
          <div class="w-full lg:w-2/3 overflow-x-auto rounded-2xl border border-slate-100 shadow-soft">
            <table class="w-full text-left border-collapse text-xs sm:text-sm font-sans">
              <thead><tr class="border-b border-slate-100 bg-slate-50">
                <?php foreach ($section['columns'] as $column): ?><th class="py-4 px-4 font-bold text-midnight"><?= $esc($column) ?></th><?php endforeach; ?>
              </tr></thead>
              <tbody class="divide-y divide-slate-100">
                <?php foreach ($section['rows'] as $row): ?><tr>
                  <?php foreach ($row as $cellIndex => $cell): ?><td class="py-4 px-4 text-slate-700 <?= $cellIndex === count($row) - 1 ? 'font-bold text-primary text-right' : '' ?>"><?= $esc($cell) ?></td><?php endforeach; ?>
                </tr><?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <p class="text-sm text-slate-500">Tỷ giá tham khảo: 1 JPY = <?= $esc(number_format((float)$cost['exchange_rate'], 0, ',', '.')) ?> VNĐ. Chi phí thực tế có thể thay đổi.</p>
    </div>
  </section>
</main>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
