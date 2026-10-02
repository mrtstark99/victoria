<section class="trust">
  <div class="container">
    <div class="trust-card">
      <?php foreach (($homeConfig['trust_items'] ?? []) as $item): ?>
        <div class="trust-item">
          <div class="trust-icon"><i class="bi <?= htmlspecialchars($item['icon'] ?? 'bi-star', ENT_QUOTES, 'UTF-8') ?>"></i></div>
          <div class="trust-text"><strong><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></small></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
