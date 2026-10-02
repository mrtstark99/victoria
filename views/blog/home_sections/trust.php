<section class="home-trust py-6 bg-transparent border-y border-slate-100" aria-label="Cam kết của Victoria Universal">
  <div class="mx-auto max-w-7xl px-5 lg:px-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
      <?php foreach ($homeConfig['trust_items'] as $item): ?>
        <div class="flex items-center gap-3.5 rounded-2xl border border-slate-100/80 bg-white p-4 shadow-soft">
          <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-primary/10 text-2xl text-primary"><i class="bi <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></div>
          <div>
            <strong class="block font-display text-sm font-bold text-primary"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></strong>
            <small class="text-xs text-muted"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
