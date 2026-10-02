<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$userName = $_SESSION['user_name'] ?? 'Admin';
$userRole = $_SESSION['user_role'] ?? 'editor';
$userInitial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8'));
$userAvatar = $_SESSION['user_avatar'] ?? null;
if ($userAvatar === null && !empty($_SESSION['user_id'])) {
    $u = \Models\User::findById($_SESSION['user_id']);
    $userAvatar = $u['avatar'] ?? '';
    $_SESSION['user_avatar'] = $userAvatar;
}

// Brand Settings for Admin
$siteTitle = getSetting('site_name', SITE_NAME);
$siteLogoBadge = getSetting('site_logo_badge', 'M');
$siteLogoUrl = getSetting('site_logo_url', '');
$siteLogoDisplayMode = getSetting('site_logo_display_mode', 'logo_and_text');
$siteFaviconUrl = getSetting('site_favicon_url', '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?> - <?php echo htmlspecialchars($siteTitle); ?> Admin</title>
    <?php if (!empty($siteFaviconUrl)): ?>
    <link rel="icon" href="<?php echo htmlspecialchars($siteFaviconUrl); ?>">
    <?php endif; ?>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/style.css') ? filemtime(APP_ROOT . '/public/assets/css/style.css') : '1.0'; ?>">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/admin.css') ? filemtime(APP_ROOT . '/public/assets/css/admin.css') : '1.0'; ?>">
    
    <!-- Theme Detection & Toggle Logic -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                console.warn('Admin theme init error:', e);
            }
        })();

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                try { localStorage.setItem('theme', 'light'); } catch (e) {}
            } else {
                document.documentElement.classList.add('dark');
                try { localStorage.setItem('theme', 'dark'); } catch (e) {}
            }
            document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                btn.setAttribute('aria-pressed', !isDark ? 'true' : 'false');
                btn.setAttribute('title', !isDark ? 'Chuyển sang giao diện Sáng' : 'Chuyển sang giao diện Tối');
            });
        }
    </script>
</head>
<body>
    <div class="admin-layout">
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar Navigation Drawer -->
        <aside class="sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="/admin/dashboard" class="sidebar-brand" aria-label="<?php echo htmlspecialchars($siteTitle); ?> Admin" style="display: inline-flex; align-items: center; gap: 0.65rem; text-decoration: none; min-width: 0;">
                    <?php if ($siteLogoDisplayMode === 'text_only'): ?>
                        <span class="logo" style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($siteTitle); ?>
                        </span>
                    <?php elseif ($siteLogoDisplayMode === 'logo_only'): ?>
                        <?php if (!empty($siteLogoUrl)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogoUrl); ?>" alt="<?php echo htmlspecialchars($siteTitle); ?>" style="max-height: 32px; max-width: 140px; object-fit: contain;">
                        <?php else: ?>
                            <div class="logo-badge"><?php echo htmlspecialchars($siteLogoBadge); ?></div>
                        <?php endif; ?>
                    <?php else: /* logo_and_text */ ?>
                        <?php if (!empty($siteLogoUrl)): ?>
                            <img src="<?php echo htmlspecialchars($siteLogoUrl); ?>" alt="<?php echo htmlspecialchars($siteTitle); ?>" style="max-height: 30px; max-width: 110px; object-fit: contain;">
                        <?php else: ?>
                            <div class="logo-badge"><?php echo htmlspecialchars($siteLogoBadge); ?></div>
                        <?php endif; ?>
                        <span class="logo" style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($siteTitle); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <button type="button" class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="Đóng menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="sidebar-body">
                <!-- Group 1: General -->
                <div class="sidebar-group">
                    <span class="sidebar-section-title">Tổng quan</span>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="/admin/dashboard" class="sidebar-link <?php echo $currentPath === '/admin/dashboard' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                                </svg>
                                <span>Bảng điều khiển</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/analytics" class="sidebar-link <?php echo $currentPath === '/admin/analytics' ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                                <span>Thống kê &amp; Phân tích</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Content Management -->
                <div class="sidebar-group">
                    <span class="sidebar-section-title">Quản lý nội dung</span>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="/admin/posts" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/posts') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Bài viết</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/pages" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/pages') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                                <span>Trang tĩnh (Pages)</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/categories" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/categories') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                                </svg>
                                <span>Danh mục</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/post-sidebar" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/post-sidebar') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                    <line x1="15" y1="3" x2="15" y2="21"/>
                                </svg>
                                <span>Cột phải bài viết</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/services" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/services') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                </svg>
                                <span>Dịch vụ du học</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/contacts" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/contacts') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <span>Tư vấn &amp; Liên hệ</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Optimization & AI -->
                <div class="sidebar-group">
                    <span class="sidebar-section-title">Tối ưu &amp; Tự động</span>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="/admin/seo" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/seo') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m4.93 4.93 4.24 4.24"/>
                                    <path d="m14.83 9.17 4.24-4.24"/>
                                    <path d="m14.83 14.83 4.24 4.24"/>
                                    <path d="m9.17 14.83-4.24 4.24"/>
                                    <circle cx="12" cy="12" r="4"/>
                                </svg>
                                <span>Kế hoạch SEO</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/agent" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/agent') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="12" x="3" y="6" rx="2"/>
                                    <circle cx="9" cy="12" r="1.5"/>
                                    <circle cx="15" cy="12" r="1.5"/>
                                    <path d="M12 2v4"/>
                                    <path d="m8 2 1 4"/>
                                    <path d="m16 2-1 4"/>
                                    <path d="M9 18v3"/>
                                    <path d="M15 18v3"/>
                                </svg>
                                <span>Cấu hình AI Agent</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 4: System & Account -->
                <div class="sidebar-group">
                    <span class="sidebar-section-title">Hệ thống</span>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="/admin/navigation" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/navigation') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="3" y1="12" x2="21" y2="12"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                    <line x1="3" y1="18" x2="21" y2="18"/>
                                </svg>
                                <span>Menu &amp; Điều hướng</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/brand" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/brand') || str_starts_with($currentPath, '/admin/settings') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                                    <path d="M2 12h20"/>
                                </svg>
                                <span>Cài đặt Website &amp; Brand</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/profile" class="sidebar-link <?php echo str_starts_with($currentPath, '/admin/profile') ? 'active' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Cài đặt tài khoản</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="sidebar-footer">
                <div class="sidebar-user-card" style="position: relative;">
                    <a href="/admin/profile" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: inherit; flex-grow: 1; min-width: 0;" title="Quản lý tài khoản">
                        <?php if (!empty($userAvatar)): ?>
                            <img src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar" class="user-avatar-badge" style="object-fit: cover; padding: 0;">
                        <?php else: ?>
                            <div class="user-avatar-badge"><?php echo htmlspecialchars($userInitial); ?></div>
                        <?php endif; ?>
                        <div class="user-meta-info">
                            <span class="user-meta-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-meta-role"><?php echo htmlspecialchars($userRole); ?></span>
                        </div>
                    </a>
                    <a href="/logout" title="Đăng xuất" style="margin-left: auto; color: var(--destructive); display: flex; align-items: center; padding: 0.35rem; border-radius: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Top Sticky Header -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button type="button" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Mở menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                    <div class="page-title-wrap">
                        <h1 class="page-title"><?php echo htmlspecialchars($page_title ?? 'Bảng điều khiển'); ?></h1>
                    </div>
                </div>

                <div class="topbar-right">
                    <!-- Quick CTA: Viết bài mới -->
                    <a href="/admin/posts/create" class="topbar-btn" title="Viết bài mới">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </a>

                    <!-- View Frontend Button -->
                    <a href="/" target="_blank" class="topbar-btn" title="Xem website người dùng">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>

                    <!-- Theme Toggle -->
                    <button type="button" onclick="toggleTheme()" class="topbar-btn theme-toggle-btn" aria-label="Đổi giao diện" title="Đổi giao diện Sáng / Tối">
                        <svg class="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        </svg>
                        <svg class="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Main Body Container -->
            <main class="admin-main-content">
                <?php displayFlashMessage(); ?>
