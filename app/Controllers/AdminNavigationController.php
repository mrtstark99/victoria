<?php
/**
 * Admin Navigation & Menu Controller
 */

namespace Controllers;

use Database;
use PDO;

class AdminNavigationController {
    public static function getNavigationSettings(): array {
        $defaultNav = [
            ['label' => 'Trang chủ', 'url' => '/', 'target' => '_self', 'is_active' => true],
            ['label' => 'Về chúng tôi', 'url' => '/about', 'target' => '_self', 'is_active' => true],
            ['label' => 'Hệ thống trường', 'url' => '/schools', 'target' => '_self', 'is_active' => true],
            ['label' => 'Dịch vụ du học', 'url' => '/services', 'target' => '_self', 'is_active' => true],
            ['label' => 'Khóa học', 'url' => '/courses', 'target' => '_self', 'is_active' => true],
            ['label' => 'Quy trình', 'url' => '/process', 'target' => '_self', 'is_active' => true],
            ['label' => 'Hồ sơ', 'url' => '/documents', 'target' => '_self', 'is_active' => true],
            ['label' => 'Chi phí', 'url' => '/cost', 'target' => '_self', 'is_active' => true],
            ['label' => 'Tư vấn Zoom', 'url' => '/consultation', 'target' => '_self', 'is_active' => true],
            ['label' => 'Hỏi đáp', 'url' => '/qa', 'target' => '_self', 'is_active' => true],
            ['label' => 'Tin tức & Cẩm nang', 'url' => '/blog', 'target' => '_self', 'is_active' => true],
            ['label' => 'Liên hệ', 'url' => '/contact', 'target' => '_self', 'is_active' => true]
        ];

        $defaultFooterCol2 = [
            'title' => 'Liên Kết Nhanh',
            'links' => [
                ['label' => 'Trang chủ', 'url' => '/'],
                ['label' => 'Về chúng tôi', 'url' => '/about'],
                ['label' => 'Hệ thống trường', 'url' => '/schools'],
                ['label' => 'Hỏi & Đáp', 'url' => '/qa'],
                ['label' => 'Tin tức & Cẩm nang', 'url' => '/blog'],
                ['label' => 'Liên hệ', 'url' => '/contact']
            ]
        ];

        $defaultFooterCol3 = [
            'title' => 'Dịch Vụ Của Chúng Tôi',
            'links' => [
                ['label' => 'Tất cả dịch vụ', 'url' => '/services'],
                ['label' => 'Khóa học tiếng Nhật', 'url' => '/courses'],
                ['label' => 'Quy trình thủ tục', 'url' => '/process'],
                ['label' => 'Kho tài liệu', 'url' => '/documents'],
                ['label' => 'Dự toán chi phí', 'url' => '/cost'],
                ['label' => 'Đăng ký tư vấn Zoom', 'url' => '/consultation']
            ]
        ];

        $defaultBottomLinks = [
            ['label' => 'Chính sách bảo mật', 'url' => '/page/chinh-sach-bao-mat'],
            ['label' => 'Điều khoản dịch vụ', 'url' => '/page/dieu-khoan-su-dung'],
            ['label' => 'Sitemap', 'url' => '/sitemap.xml']
        ];

        $rawNav = getSetting('nav_menu_items', null);
        $navItems = is_array($rawNav) ? $rawNav : (is_string($rawNav) && !empty($rawNav) ? json_decode($rawNav, true) : null);
        if (!is_array($navItems)) $navItems = $defaultNav;
        $legacyNav = [
            ['label' => 'Trang chủ', 'url' => '/', 'target' => '_self', 'is_active' => true],
            ['label' => 'Tối ưu SEO', 'url' => '/category/toi-uu-seo', 'target' => '_self', 'is_active' => true],
            ['label' => 'Hướng dẫn AI', 'url' => '/category/huong-dan', 'target' => '_self', 'is_active' => true],
            ['label' => 'Tin tức', 'url' => '/category/tin-tuc', 'target' => '_self', 'is_active' => true],
            ['label' => 'Giới thiệu', 'url' => '/page/gioi-thieu', 'target' => '_self', 'is_active' => true],
            ['label' => 'Liên hệ', 'url' => '/page/lien-he', 'target' => '_self', 'is_active' => true]
        ];
        if ($navItems === $legacyNav) $navItems = $defaultNav;

        $rawCol2 = getSetting('footer_col2_json', null);
        $footerCol2 = is_array($rawCol2) ? $rawCol2 : (is_string($rawCol2) && !empty($rawCol2) ? json_decode($rawCol2, true) : null);
        if (!is_array($footerCol2)) $footerCol2 = $defaultFooterCol2;
        $legacyFooterCol2 = [
            'title' => 'Chuyên Mục',
            'links' => [
                ['label' => 'Tối ưu SEO On-page', 'url' => '/category/toi-uu-seo'],
                ['label' => 'Hướng dẫn AI & MCP', 'url' => '/category/huong-dan'],
                ['label' => 'Tin tức Google Search', 'url' => '/category/tin-tuc'],
                ['label' => 'Bài viết mới cập nhật', 'url' => '/#latest']
            ]
        ];
        if ($footerCol2 === $legacyFooterCol2) $footerCol2 = $defaultFooterCol2;

        $rawCol3 = getSetting('footer_col3_json', null);
        $footerCol3 = is_array($rawCol3) ? $rawCol3 : (is_string($rawCol3) && !empty($rawCol3) ? json_decode($rawCol3, true) : null);
        if (!is_array($footerCol3)) $footerCol3 = $defaultFooterCol3;
        $legacyFooterCol3 = [
            'title' => 'Thông Tin & Chính Sách',
            'links' => [
                ['label' => 'Về chúng tôi', 'url' => '/page/gioi-thieu'],
                ['label' => 'Chính sách bảo mật', 'url' => '/page/chinh-sach-bao-mat'],
                ['label' => 'Điều khoản sử dụng', 'url' => '/page/dieu-khoan-su-dung'],
                ['label' => 'Liên hệ hợp tác', 'url' => '/page/lien-he']
            ]
        ];
        if ($footerCol3 === $legacyFooterCol3) $footerCol3 = $defaultFooterCol3;

        $rawBottom = getSetting('footer_bottom_links', null);
        $bottomLinks = is_array($rawBottom) ? $rawBottom : (is_string($rawBottom) && !empty($rawBottom) ? json_decode($rawBottom, true) : null);
        if (!is_array($bottomLinks)) $bottomLinks = $defaultBottomLinks;
        $legacyBottomLinks = [
            ['label' => 'Trang chủ', 'url' => '/'],
            ['label' => 'Giới thiệu', 'url' => '/page/gioi-thieu'],
            ['label' => 'Chính sách', 'url' => '/page/chinh-sach-bao-mat'],
            ['label' => 'Sitemap', 'url' => '/sitemap.xml']
        ];
        if ($bottomLinks === $legacyBottomLinks) $bottomLinks = $defaultBottomLinks;

        return [
            'nav_items' => $navItems,
            'footer_col2' => $footerCol2,
            'footer_col3' => $footerCol3,
            'bottom_links' => $bottomLinks
        ];
    }

    public function index() {
        requireAdmin();
        $settings = self::getNavigationSettings();
        $pages = \Models\Page::getAllPublished();
        $categories = \Models\Category::getAll();

        view('admin/navigation', [
            'settings' => $settings,
            'pages' => $pages,
            'categories' => $categories,
            'page_title' => 'Cấu hình Menu Điều Hướng & Footer'
        ]);
    }

    public function save() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
                redirect('/admin/navigation', 'Phiên làm việc hết hạn.', 'error');
            }

            // 1. Process Header Nav Items
            $navLabels = $_POST['nav_label'] ?? [];
            $navUrls = $_POST['nav_url'] ?? [];
            $navTargets = $_POST['nav_target'] ?? [];
            $navActive = $_POST['nav_active'] ?? [];
            $navKeys = $_POST['nav_key'] ?? [];

            $navItems = [];
            for ($i = 0; $i < min(count($navLabels), 12); $i++) {
                $label = trim($navLabels[$i] ?? '');
                $url = safeNavigationUrl($navUrls[$i] ?? '');
                $navKey = (string)($navKeys[$i] ?? $i);
                if ($label !== '' && $url !== null) {
                    $navItems[] = [
                        'label' => $label,
                        'url' => $url,
                        'target' => ($navTargets[$i] ?? '_self') === '_blank' ? '_blank' : '_self',
                        'is_active' => isset($navActive[$navKey])
                    ];
                }
            }

            // 2. Process Footer Col 2
            $col2Title = trim($_POST['footer_col2_title'] ?? 'Chuyên Mục');
            $col2Labels = $_POST['col2_label'] ?? [];
            $col2Urls = $_POST['col2_url'] ?? [];
            $col2Links = [];
            for ($i = 0; $i < count($col2Labels); $i++) {
                $label = trim($col2Labels[$i] ?? '');
                $url = trim($col2Urls[$i] ?? '');
                $url = safeNavigationUrl($url);
                if ($label !== '' && $url !== null) {
                    $col2Links[] = ['label' => $label, 'url' => $url];
                }
            }
            $footerCol2 = ['title' => $col2Title, 'links' => $col2Links];

            // 3. Process Footer Col 3
            $col3Title = trim($_POST['footer_col3_title'] ?? 'Thông Tin & Chính Sách');
            $col3Labels = $_POST['col3_label'] ?? [];
            $col3Urls = $_POST['col3_url'] ?? [];
            $col3Links = [];
            for ($i = 0; $i < count($col3Labels); $i++) {
                $label = trim($col3Labels[$i] ?? '');
                $url = trim($col3Urls[$i] ?? '');
                $url = safeNavigationUrl($url);
                if ($label !== '' && $url !== null) {
                    $col3Links[] = ['label' => $label, 'url' => $url];
                }
            }
            $footerCol3 = ['title' => $col3Title, 'links' => $col3Links];

            // 4. Process Footer Bottom Links
            $botLabels = $_POST['bot_label'] ?? [];
            $botUrls = $_POST['bot_url'] ?? [];
            $bottomLinks = [];
            for ($i = 0; $i < count($botLabels); $i++) {
                $label = trim($botLabels[$i] ?? '');
                $url = trim($botUrls[$i] ?? '');
                $url = safeNavigationUrl($url);
                if ($label !== '' && $url !== null) {
                    $bottomLinks[] = ['label' => $label, 'url' => $url];
                }
            }

            updateSetting('nav_menu_items', json_encode($navItems, JSON_UNESCAPED_UNICODE), 'json');
            updateSetting('footer_col2_json', json_encode($footerCol2, JSON_UNESCAPED_UNICODE), 'json');
            updateSetting('footer_col3_json', json_encode($footerCol3, JSON_UNESCAPED_UNICODE), 'json');
            updateSetting('footer_bottom_links', json_encode($bottomLinks, JSON_UNESCAPED_UNICODE), 'json');

            // Audit log
            $db = Database::getInstance();
            $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, ip_address, user_agent) VALUES (?, 'UPDATE_NAVIGATION_MENUS', 'settings', ?, ?)");
            $stmtLog->execute([$_SESSION['user_id'] ?? null, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);

            redirect('/admin/navigation', 'Cập nhật Menu Điều Hướng và Chân Trang thành công.');
        }
        redirect('/admin/navigation');
    }
}
