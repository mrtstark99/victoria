<?php
/**
 * Admin Post Sidebar & Layout Settings Controller
 */

namespace Controllers;

use Database;
use PDO;

class AdminSidebarController
{
    private static $defaultSettings = [
        'post_sidebar_sticky' => '1',
        'post_sidebar_widget_order' => 'trending,banner,newsletter,cta,categories',
        
        // Widget 1: Trending
        'post_sidebar_trending_enabled' => '1',
        'post_sidebar_trending_title' => 'Đọc nhiều nhất',
        'post_sidebar_trending_limit' => '4',
        
        // Widget 2: Banner Ads / Display
        'post_sidebar_banner_enabled' => '0',
        'post_sidebar_banner_title' => 'Khám phá đối tác',
        'post_sidebar_banner_badge' => 'Tài trợ',
        'post_sidebar_banner_image' => '',
        'post_sidebar_banner_url' => 'https://example.com',
        'post_sidebar_banner_alt' => 'Banner quảng cáo',
        'post_sidebar_banner_new_tab' => '1',

        // Widget 3: Newsletter
        'post_sidebar_newsletter_enabled' => '1',
        'post_sidebar_newsletter_title' => 'Bản tin SEO & AI',
        'post_sidebar_newsletter_desc' => 'Cập nhật các thuật toán mới nhất của Google và chiến lược AI mỗi tuần.',
        'post_sidebar_newsletter_btn' => 'Gửi',

        // Widget 4: Custom CTA Banner
        'post_sidebar_cta_enabled' => '0',
        'post_sidebar_cta_title' => 'Tư vấn chiến lược SEO & AI',
        'post_sidebar_cta_desc' => 'Đồng hành cùng doanh nghiệp của bạn xây dựng hệ thống tăng trưởng hữu cơ bền vững.',
        'post_sidebar_cta_btn_text' => 'Liên hệ tư vấn ngay',
        'post_sidebar_cta_btn_url' => 'mailto:admin@example.com',
        'post_sidebar_cta_badge' => 'Tư vấn',

        // Widget 5: Categories Quick Links
        'post_sidebar_categories_enabled' => '0',
        'post_sidebar_categories_title' => 'Chuyên mục đề xuất',

        // Multiple Custom HTML Widgets JSON
        'post_sidebar_custom_html_widgets' => '[]',

        // Page layout items
        'post_author_box_enabled' => '1',
        'post_related_enabled' => '1',
        'post_related_limit' => '3'
    ];

    public static function getSidebarSettings(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'post_%'");
        $saved = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $merged = array_merge(self::$defaultSettings, $saved);

        // Auto-migration for legacy single custom html widget if present
        if (empty($merged['post_sidebar_custom_html_widgets']) || $merged['post_sidebar_custom_html_widgets'] === '[]') {
            if (!empty($merged['post_sidebar_custom_html_content'])) {
                $legacy = [
                    [
                        'id' => 'custom_html_1',
                        'title' => $merged['post_sidebar_custom_html_title'] ?? 'Tiện ích HTML',
                        'content' => $merged['post_sidebar_custom_html_content'],
                        'enabled' => $merged['post_sidebar_custom_html_enabled'] ?? '1'
                    ]
                ];
                $merged['post_sidebar_custom_html_widgets'] = json_encode($legacy, JSON_UNESCAPED_UNICODE);
            }
        }

        return $merged;
    }

    public function index()
    {
        requireEditor();
        $settings = self::getSidebarSettings();

        view('admin/post_sidebar', [
            'settings' => $settings,
            'page_title' => 'Cài đặt cột phải bài viết & Widget'
        ]);
    }

    public function save()
    {
        requireEditor();
        $db = Database::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!verifyCSRFToken($token)) {
                redirect('/admin/post-sidebar', 'Phiên làm việc hết hạn, vui lòng thử lại.', 'error');
            }

            // Handle Banner Image Upload
            $uploadDir = UPLOAD_PATH . 'widgets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $bannerImage = trim($_POST['post_sidebar_banner_image'] ?? '');
            if (!empty($_POST['remove_banner_image'])) {
                $bannerImage = '';
            } elseif (isset($_FILES['banner_image_file']) && $_FILES['banner_image_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['banner_image_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_IMAGE_TYPES) && $file['size'] <= MAX_FILE_SIZE) {
                    $filename = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                        $bannerImage = '/uploads/widgets/' . $filename;
                    }
                }
            }

            // Process Multiple Custom HTML Widgets
            $customHtmlWidgets = [];
            if (isset($_POST['custom_html_widgets']) && is_array($_POST['custom_html_widgets'])) {
                foreach ($_POST['custom_html_widgets'] as $wData) {
                    $wId = trim($wData['id'] ?? ('html_' . time() . '_' . bin2hex(random_bytes(2))));
                    $wTitle = trim($wData['title'] ?? 'Tiện ích HTML');
                    $wContent = (string)($wData['content'] ?? '');
                    $wEnabled = isset($wData['enabled']) && $wData['enabled'] === '1' ? '1' : '0';
                    $customHtmlWidgets[] = [
                        'id' => $wId,
                        'title' => $wTitle,
                        'content' => $wContent,
                        'enabled' => $wEnabled
                    ];
                }
            }
            $customHtmlWidgetsJson = json_encode($customHtmlWidgets, JSON_UNESCAPED_UNICODE);

            $keys = [
                'post_sidebar_sticky' => 'boolean',
                'post_sidebar_widget_order' => 'text',
                
                // Trending
                'post_sidebar_trending_enabled' => 'boolean',
                'post_sidebar_trending_title' => 'text',
                'post_sidebar_trending_limit' => 'number',

                // Banner Ads
                'post_sidebar_banner_enabled' => 'boolean',
                'post_sidebar_banner_title' => 'text',
                'post_sidebar_banner_badge' => 'text',
                'post_sidebar_banner_image' => 'text',
                'post_sidebar_banner_url' => 'text',
                'post_sidebar_banner_alt' => 'text',
                'post_sidebar_banner_new_tab' => 'boolean',

                // Newsletter
                'post_sidebar_newsletter_enabled' => 'boolean',
                'post_sidebar_newsletter_title' => 'text',
                'post_sidebar_newsletter_desc' => 'text',
                'post_sidebar_newsletter_btn' => 'text',

                // Custom CTA
                'post_sidebar_cta_enabled' => 'boolean',
                'post_sidebar_cta_title' => 'text',
                'post_sidebar_cta_desc' => 'text',
                'post_sidebar_cta_btn_text' => 'text',
                'post_sidebar_cta_btn_url' => 'text',
                'post_sidebar_cta_badge' => 'text',

                // Categories
                'post_sidebar_categories_enabled' => 'boolean',
                'post_sidebar_categories_title' => 'text',

                // Custom HTML Widgets
                'post_sidebar_custom_html_widgets' => 'json',

                // Post Content & Footer items
                'post_author_box_enabled' => 'boolean',
                'post_related_enabled' => 'boolean',
                'post_related_limit' => 'number'
            ];

            $upsertStmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, setting_type, updated_at)
                VALUES (?, ?, ?, datetime('now','localtime'))
                ON CONFLICT(setting_key) DO UPDATE SET
                    setting_value = excluded.setting_value,
                    setting_type = excluded.setting_type,
                    updated_at = datetime('now','localtime')
            ");

            foreach ($keys as $key => $type) {
                if ($key === 'post_sidebar_banner_image') {
                    $value = $bannerImage;
                } elseif ($key === 'post_sidebar_custom_html_widgets') {
                    $value = $customHtmlWidgetsJson;
                } elseif ($type === 'boolean') {
                    $value = isset($_POST[$key]) && $_POST[$key] === '1' ? '1' : '0';
                } elseif ($type === 'number') {
                    $value = (string)max(1, min(12, (int)($_POST[$key] ?? 3)));
                } else {
                    $value = trim((string)($_POST[$key] ?? ''));
                }

                $upsertStmt->execute([$key, $value, $type]);
            }

            // Audit log
            $userId = $_SESSION['user_id'] ?? null;
            $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, ip_address, user_agent) VALUES (?, 'UPDATE_SIDEBAR_SETTINGS', 'settings', ?, ?)");
            $stmtLog->execute([$userId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);

            redirect('/admin/post-sidebar', 'Cập nhật cấu hình Widget và Cột phải thành công.');
        }
        redirect('/admin/post-sidebar');
    }
}
