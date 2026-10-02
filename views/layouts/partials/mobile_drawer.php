<?php
/**
 * @file views/layouts/partials/mobile_drawer.php
 * @description Slide-out mobile navigation drawer with touch-friendly links.
 *
 * Layer:
 * - Presentation / View Partial
 *
 * Responsibilities:
 * - Provide full mobile navigation coverage.
 * - Display user authentication banner or quick login CTA.
 * - Provide 1-tap contact hotline and advisory button.
 * - Handle drawer open, close, and outside-click events via clean vanilla JS.
 *
 * Security:
 * - Escapes all user metadata.
 *
 * Dependencies:
 * - Bootstrap Icons and Tailwind utility classes.
 *
 * Constraints:
 * - Keep this file under 300 lines.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

$mobileLinks = array_values(array_filter($siteNavigation['nav_items'] ?? [], function ($item) {
    return !empty($item['is_active']) && safeNavigationUrl($item['url'] ?? '') !== null;
}));
?>
<!-- Mobile Menu Overlay Drawer -->
<div id="mobile-menu" class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-md opacity-0 pointer-events-none transition-all duration-300">
  <div id="mobile-menu-panel" class="absolute top-0 right-0 w-full max-w-sm h-full bg-white shadow-2xl flex flex-col translate-x-full transition-transform duration-300 overflow-y-auto">
    
    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white sticky top-0 z-10">
      <a href="/" class="flex items-center">
        <img src="/assets/images/logo.svg" alt="Bright Education" class="h-10 w-auto">
      </a>
      <button id="mobile-menu-close" class="h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-700 hover:bg-primary hover:text-white transition-colors" aria-label="Close Menu">
        <i class="bi bi-x-lg text-lg"></i>
      </button>
    </div>

    <!-- Drawer Content -->
    <div class="flex-1 px-5 py-6 space-y-6">
      <!-- User Profile / Auth State Card -->
      <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
          <div class="flex items-center gap-3 mb-3">
            <div class="h-10 w-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-lg font-display uppercase">
              <?php echo mb_substr($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'U', 0, 1, 'utf-8'); ?>
            </div>
            <div>
              <p class="text-sm font-bold text-primary"><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'Tài khoản'); ?></p>
              <span class="text-xs text-slate-500 capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Thành viên'); ?></span>
            </div>
          </div>
          <div class="flex gap-2">
            <?php if (isAdmin() || isEditor()): ?>
              <a href="/admin" class="flex-1 text-center py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-slate-800 transition-colors">
                Quản trị
              </a>
            <?php endif; ?>
            <a href="/logout" class="flex-1 text-center py-2 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
              Đăng xuất
            </a>
          </div>
        <?php else: ?>
          <p class="text-xs text-slate-500 mb-3 font-medium">Đăng nhập tài khoản để nhận hỗ trợ hồ sơ và lịch tư vấn.</p>
          <a href="/login" class="block w-full text-center py-2.5 rounded-xl text-xs font-bold text-primary bg-white border border-slate-200 hover:border-primary transition-colors">
            <i class="bi bi-box-arrow-in-right mr-1"></i>Đăng nhập
          </a>
        <?php endif; ?>
      </div>

      <!-- Main Navigation Links -->
      <nav class="space-y-1">
        <?php foreach ($mobileLinks as $link):
          $url = safeNavigationUrl($link['url'] ?? '');
          $icon = $link['icon'] ?? 'bi-link-45deg';
        ?>
          <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo ($link['target'] ?? '_self') === '_blank' ? '_blank' : '_self'; ?>"<?php echo ($link['target'] ?? '_self') === '_blank' ? ' rel="noopener noreferrer"' : ''; ?> class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors">
            <i class="bi <?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?> text-primary text-base"></i>
            <span><?php echo htmlspecialchars($link['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <!-- Primary Mobile CTA -->
      <div class="pt-2">
        <a href="/contact" class="block w-full text-center py-3.5 rounded-2xl text-sm font-bold text-white bg-primary hover:bg-slate-800 shadow-md transition-all">
          <i class="bi bi-chat-dots-fill mr-1.5"></i>Tư vấn miễn phí
        </a>
      </div>

      <!-- Quick Hotline Card -->
      <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100 text-xs text-amber-900 space-y-1">
        <p class="font-bold flex items-center gap-1.5"><i class="bi bi-telephone-fill text-amber-600"></i> Hotline trực tiếp</p>
        <p><a href="tel:0964808886" class="font-semibold text-primary">0964 808 886</a></p>
      </div>
    </div>
  </div>
</div>

<script>
  // Mobile drawer interaction controller
  (function() {
    const toggleBtn = document.getElementById('mobile-menu-toggle');
    const closeBtn = document.getElementById('mobile-menu-close');
    const drawer = document.getElementById('mobile-menu');
    const panel = document.getElementById('mobile-menu-panel');

    function openDrawer() {
      drawer.classList.remove('opacity-0', 'pointer-events-none');
      panel.classList.remove('translate-x-full');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      drawer.classList.add('opacity-0', 'pointer-events-none');
      panel.classList.add('translate-x-full');
      document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (drawer) {
      drawer.addEventListener('click', function(e) {
        if (e.target === drawer) closeDrawer();
      });
    }
  })();
</script>
