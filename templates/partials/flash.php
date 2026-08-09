<?php
$flash = \App\Core\Flash::pull();
$flashType = $flash['type'] ?? 'info';
$flashMeta = match ($flashType) {
    'success' => ['label' => 'Thành công', 'icon' => 'bi-check2'],
    'error' => ['label' => 'Có lỗi xảy ra', 'icon' => 'bi-exclamation-lg'],
    default => ['label' => 'Thông báo', 'icon' => 'bi-info-lg'],
};
?>
<?php if ($flash): ?>
  <div class="flash flash-<?= \App\Core\Security::escape($flashType) ?>" role="status" aria-live="polite" data-toast data-toast-duration="5200">
    <span class="toast-icon" aria-hidden="true"><i class="bi <?= $flashMeta['icon'] ?>"></i></span>
    <span class="toast-content">
      <strong><?= $flashMeta['label'] ?></strong>
      <span><?= \App\Core\Security::escape($flash['message']) ?></span>
    </span>
    <button class="toast-close" type="button" aria-label="Đóng thông báo" data-toast-close>
      <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>
    <span class="toast-progress" aria-hidden="true"></span>
  </div>
<?php endif; ?>
