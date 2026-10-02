<?php include APP_ROOT . '/views/layouts/header.php'; ?>
<main class="be-static-page bg-slate-50 min-h-screen">
  <?php $heroTitle = $fixed_page['title']; $heroEyebrow = 'Victoria Universal'; $heroCurrent = $fixed_page['title']; include APP_ROOT . '/views/layouts/partials/page_hero.php'; ?>
  <section class="be-fixed-content"><div class="be-static-container prose prose-slate"><?= $fixed_page['content_html'] ?></div></section>
</main>
<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
