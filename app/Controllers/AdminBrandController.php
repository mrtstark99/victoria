<?php
/**
 * Admin Brand & Website Configuration Controller
 */

namespace Controllers;

use Database;
use PDO;

class AdminBrandController
{
    private static $defaultBrandSettings = [
        'site_name' => 'MinimaList',
        'site_slogan' => 'Chia sẻ kiến thức SEO & AI thực chiến',
        'site_logo_display_mode' => 'logo_and_text',
        'site_logo_badge' => 'M',
        'site_logo_url' => '',
        'site_favicon_url' => '',
        'site_email' => 'admin@example.com',
        'site_phone' => '+84 123 456 789',
        'site_address' => 'Việt Nam',
        'social_facebook' => 'https://facebook.com',
        'social_twitter' => 'https://twitter.com',
        'social_github' => 'https://github.com',
        'social_linkedin' => 'https://linkedin.com',
        'social_youtube' => '',
        'default_meta_description' => 'Không gian chia sẻ kiến thức chọn lọc về SEO On-page, cấu trúc Topic Cluster và ứng dụng AI Agent vào sáng tạo nội dung bền vững.',
        'default_meta_keywords' => 'SEO, AI Agent, Topic Cluster, Content Marketing, Google Search',
        'default_og_image' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&h=630&q=80',
        'footer_about_text' => 'Không gian chia sẻ kiến thức chọn lọc về SEO On-page, cấu trúc Topic Cluster và ứng dụng AI Agent vào sáng tạo nội dung bền vững.',
        'footer_copyright' => '© {year} MinimaList. All rights reserved.',
        'custom_header_code' => '',
        'custom_footer_code' => ''
    ];

    public static function getBrandSettings(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'site_%' OR setting_key LIKE 'social_%' OR setting_key LIKE 'default_%' OR setting_key LIKE 'footer_%' OR setting_key LIKE 'custom_%'");
        $saved = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        return array_merge(self::$defaultBrandSettings, $saved);
    }

    public function index()
    {
        requireAdmin();
        $settings = self::getBrandSettings();

        view('admin/brand_settings', [
            'settings' => $settings,
            'page_title' => 'Cài đặt Brand & Website'
        ]);
    }

    public function save()
    {
        requireAdmin();
        $db = Database::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!verifyCSRFToken($token)) {
                redirect('/admin/brand', 'Phiên làm việc hết hạn, vui lòng thử lại.', 'error');
            }

            $uploadDir = UPLOAD_PATH . 'brand/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Handle Logo File Upload
            $logoUrl = trim($_POST['site_logo_url'] ?? '');
            if (!empty($_POST['remove_site_logo'])) {
                $logoUrl = '';
            } elseif (isset($_FILES['site_logo_file']) && $_FILES['site_logo_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['site_logo_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif']) && $file['size'] <= MAX_FILE_SIZE) {
                    $filename = 'logo_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                        $logoUrl = '/uploads/brand/' . $filename;
                    }
                }
            }

            // Handle Favicon File Upload
            $faviconUrl = trim($_POST['site_favicon_url'] ?? '');
            if (!empty($_POST['remove_site_favicon'])) {
                $faviconUrl = '';
            } elseif (isset($_FILES['site_favicon_file']) && $_FILES['site_favicon_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['site_favicon_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['ico', 'png', 'svg', 'webp', 'jpg', 'jpeg']) && $file['size'] <= MAX_FILE_SIZE) {
                    $filename = 'favicon_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                        $faviconUrl = '/uploads/brand/' . $filename;
                    }
                }
            }

            // Handle Default OG Image Upload
            $ogImageUrl = trim($_POST['default_og_image'] ?? '');
            if (!empty($_POST['remove_default_og_image'])) {
                $ogImageUrl = '';
            } elseif (isset($_FILES['default_og_image_file']) && $_FILES['default_og_image_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['default_og_image_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ALLOWED_IMAGE_TYPES) && $file['size'] <= MAX_FILE_SIZE) {
                    $filename = 'og_default_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                        $ogImageUrl = '/uploads/brand/' . $filename;
                    }
                }
            }

            $fields = [
                'site_name' => trim($_POST['site_name'] ?? 'MinimaList'),
                'site_slogan' => trim($_POST['site_slogan'] ?? ''),
                'site_logo_display_mode' => in_array($_POST['site_logo_display_mode'] ?? '', ['logo_and_text', 'logo_only', 'text_only']) ? $_POST['site_logo_display_mode'] : 'logo_and_text',
                'site_logo_badge' => trim($_POST['site_logo_badge'] ?? 'M'),
                'site_logo_url' => $logoUrl,
                'site_favicon_url' => $faviconUrl,
                'site_email' => trim($_POST['site_email'] ?? ''),
                'site_phone' => trim($_POST['site_phone'] ?? ''),
                'site_address' => trim($_POST['site_address'] ?? ''),
                'social_facebook' => trim($_POST['social_facebook'] ?? ''),
                'social_twitter' => trim($_POST['social_twitter'] ?? ''),
                'social_github' => trim($_POST['social_github'] ?? ''),
                'social_linkedin' => trim($_POST['social_linkedin'] ?? ''),
                'social_youtube' => trim($_POST['social_youtube'] ?? ''),
                'default_meta_description' => trim($_POST['default_meta_description'] ?? ''),
                'default_meta_keywords' => trim($_POST['default_meta_keywords'] ?? ''),
                'default_og_image' => $ogImageUrl,
                'footer_about_text' => trim($_POST['footer_about_text'] ?? ''),
                'footer_copyright' => trim($_POST['footer_copyright'] ?? ''),
                'custom_header_code' => $_POST['custom_header_code'] ?? '',
                'custom_footer_code' => $_POST['custom_footer_code'] ?? ''
            ];

            foreach ($fields as $key => $val) {
                updateSetting($key, $val, 'text');
            }

            // Audit log
            $userId = $_SESSION['user_id'] ?? null;
            $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, ip_address, user_agent) VALUES (?, 'UPDATE_BRAND_SETTINGS', 'settings', ?, ?)");
            $stmtLog->execute([$userId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);

            redirect('/admin/brand', 'Cập nhật cấu hình Brand và Website thành công.');
        }
        redirect('/admin/brand');
    }
}
