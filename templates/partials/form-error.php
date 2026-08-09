<div class="global-form-error" role="alert" data-toast data-toast-duration="7200">
  <span class="toast-icon" aria-hidden="true"><i class="bi bi-exclamation-lg"></i></span>
  <span class="toast-content">
    <strong>Không thể thực hiện</strong>
    <span><?= \App\Core\Security::escape($error) ?></span>
  </span>
  <button class="toast-close" type="button" aria-label="Đóng thông báo" data-toast-close>
    <i class="bi bi-x-lg" aria-hidden="true"></i>
  </button>
  <span class="toast-progress" aria-hidden="true"></span>
</div>
