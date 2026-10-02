<?php
$heroTitle = (string)($heroTitle ?? '');
$heroEyebrow = (string)($heroEyebrow ?? 'Victoria Universal');
$heroCurrent = (string)($heroCurrent ?? $heroTitle);
$heroDescription = (string)($heroDescription ?? '');
$heroParent = $heroParent ?? null;
$heroEscape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>
<section class="be-page-hero relative isolate overflow-hidden bg-primary pt-20 pb-14 text-white sm:pt-24 sm:pb-16 lg:pt-28 lg:pb-20">
  <div aria-hidden="true" class="absolute -right-24 -top-32 -z-10 h-80 w-80 rounded-full bg-white/[.06]"></div>
  <div aria-hidden="true" class="absolute -bottom-24 -left-16 -z-10 h-56 w-56 rounded-full bg-primary-400/10"></div>
  <div class="be-page-hero__content mx-auto max-w-7xl px-5 lg:px-8">
    <p class="text-[11px] font-extrabold uppercase tracking-[.2em] text-primary-300"><?= $heroEscape($heroEyebrow) ?></p>
    <h1 class="be-page-hero__title mt-3 text-4xl font-black tracking-tight text-white font-display sm:text-5xl"><?= $heroEscape($heroTitle) ?></h1>
    <?php if ($heroDescription !== ''): ?><p class="be-page-hero__description mt-4 max-w-3xl text-base leading-relaxed text-white/80 sm:text-lg"><?= $heroEscape($heroDescription) ?></p><?php endif; ?>
    <nav class="be-page-hero__breadcrumb mt-6 flex flex-wrap items-center gap-2 text-xs font-semibold text-white/65" aria-label="Breadcrumb">
      <a href="/" class="transition hover:text-white">Trang chủ</a>
      <?php if (is_array($heroParent)): ?>
        <i class="bi bi-chevron-right text-[10px]"></i><a href="<?= $heroEscape($heroParent['url']) ?>" class="transition hover:text-white"><?= $heroEscape($heroParent['label']) ?></a>
      <?php endif; ?>
      <i class="bi bi-chevron-right text-[10px]"></i><span class="text-white"><?= $heroEscape($heroCurrent) ?></span>
    </nav>
  </div>
</section>
