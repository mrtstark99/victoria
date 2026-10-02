<?php
/**
 * @file views/layouts/partials/navbar.php
 * @description Desktop and responsive top navbar component.
 *
 * Layer:
 * - Presentation / View Partial
 *
 * Responsibilities:
 * - Render branding logo and primary navigation links.
 * - Highlight active navigation route.
 * - Render authentication state (login button or profile dropdown).
 * - Trigger mobile navigation drawer.
 *
 * Security:
 * - Escapes session usernames to prevent XSS.
 *
 * Dependencies:
 * - Authentication helper functions (isLoggedIn, isAdmin, isEditor).
 *
 * Constraints:
 * - Keep this file focused on navigation rendering.
 * - Keep this file under 300 lines.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

$navItems = $siteNavigation['nav_items'] ?? [];
?>
<header class="group fixed inset-x-0 top-0 z-50 transition-all duration-300" id="main-header">
  <div class="w-full h-20 border-b border-gray-100/50 bg-white/95 backdrop-blur-md transition-all duration-300" id="header-inner">
    <div class="mx-auto flex h-full max-w-[1500px] items-center justify-between gap-5 px-5 lg:px-8">
      <!-- Logo -->
      <a class="flex shrink-0 items-center group 2xl:mr-3" href="/">
        <img src="/assets/images/logo.svg" alt="Bright Education" class="h-16 w-auto max-w-none transition-transform group-hover:scale-105">
      </a>
      
      <!-- Desktop Navigation Menu -->
      <nav class="hidden min-w-0 items-center gap-0.5 text-[14px] font-medium text-muted 2xl:flex">
        <?php foreach ($navItems as $item):
          $url = safeNavigationUrl($item['url'] ?? '');
          if ($url === null || empty($item['is_active'])) continue;
          $itemPath = parse_url($url, PHP_URL_PATH) ?: '';
          $isActive = ($itemPath === '/' && $currentPath === '/') ||
                      ($itemPath !== '' && $itemPath !== '/' && str_starts_with($currentPath, $itemPath));
          $activeClass = $isActive ? '!text-primary font-bold border-b-2 border-primary pb-1' : 'hover:text-primary transition-colors';
        ?>
          <a class="nav-link px-2.5 py-1 <?php echo $activeClass; ?>" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="<?php echo ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self'; ?>"<?php echo ($item['target'] ?? '_self') === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
            <?php echo htmlspecialchars($item['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
          </a>
        <?php endforeach; ?>

        <?php if (function_exists('isLoggedIn') && isLoggedIn() && (isAdmin() || isEditor())): ?>
          <a class="nav-link !text-primary font-bold bg-primary/5 rounded-lg px-2.5 py-1 hover:bg-primary/10 transition-colors" href="/admin">
            <i class="bi bi-shield-check mr-1"></i>Quản trị
          </a>
        <?php endif; ?>
      </nav>
      
      <!-- Right Action Items -->
      <div class="flex shrink-0 items-center gap-3">
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
          <div class="relative group nav-dropdown-trigger hidden sm:block">
            <button class="flex max-w-44 items-center gap-2 whitespace-nowrap text-sm font-semibold text-slate-700 hover:text-primary transition-colors px-2 py-1 rounded-lg">
              <i class="bi bi-person-circle text-lg text-primary"></i>
              <span class="truncate"><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'Tài khoản'); ?></span>
              <i class="bi bi-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
            </button>
            <div class="nav-dropdown absolute right-0 top-full mt-2 w-48 rounded-2xl bg-white p-2 shadow-tinted border border-slate-100 z-50 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200">
              <?php if (isAdmin() || isEditor()): ?>
                <a href="/admin" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">
                  <i class="bi bi-speedometer2 text-primary"></i> Bảng điều khiển
                </a>
              <?php endif; ?>
              <a href="/admin/profile" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">
                <i class="bi bi-person text-primary"></i> Hồ sơ
              </a>
              <div class="my-1 border-t border-slate-100"></div>
              <a href="/logout" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="/login" class="hidden sm:inline-flex rounded-full px-4 py-2 text-sm font-semibold text-primary bg-primary-50 hover:bg-primary-100 transition-colors">
            Đăng nhập
          </a>
        <?php endif; ?>
        
        <a class="hidden shrink-0 whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-bold text-white sm:inline-flex bg-primary hover:bg-slate-800 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all" href="/contact">
          Tư vấn miễn phí
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobile-menu-toggle" class="flex shrink-0 2xl:hidden h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-primary border border-slate-200 transition-colors hover:bg-slate-100" aria-label="Toggle Menu">
          <i class="bi bi-list text-2xl"></i>
        </button>
      </div>
    </div>
  </div>
</header>
