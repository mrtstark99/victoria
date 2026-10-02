<?php
/**
 * Admin Analytics Controller
 * Handles Google Analytics & Search Console overview & settings.
 */

namespace Controllers;

use Models\Post;
use Models\Category;

class AdminAnalyticsController {

    public function analytics() {
        requireEditor();
        $db = \Database::getInstance();

        $days    = (int)($_GET['days'] ?? 28);
        $days    = in_array($days, [7, 28, 90], true) ? $days : 28;
        $refresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';

        $realViews  = (int)$db->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
        $viewsToday = (int)$db->query("SELECT COUNT(*) FROM page_views WHERE date(created_at) = date('now', 'localtime')")->fetchColumn();

        $localChartData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dateKey = date('Y-m-d', time() - $i * 86400);
            $label   = date('d/m',   time() - $i * 86400);
            $stmtD   = $db->prepare("SELECT COUNT(*) FROM page_views WHERE date(created_at) = ?");
            $stmtD->execute([$dateKey]);
            $localChartData[] = ['date' => $label, 'count' => (int)$stmtD->fetchColumn()];
        }

        $mobileViews  = (int)$db->query("SELECT COUNT(*) FROM page_views WHERE user_agent LIKE '%Mobile%' OR user_agent LIKE '%Android%' OR user_agent LIKE '%iPhone%' OR user_agent LIKE '%iPad%'")->fetchColumn();
        $desktopViews = max(0, $realViews - $mobileViews);

        $referers = $db->query("SELECT referer, COUNT(*) as count FROM page_views GROUP BY referer ORDER BY count DESC LIMIT 5")->fetchAll();

        $report   = analyticsDashboardData($days, $refresh);
        $pagePerf = analyticsPagePerformanceData($days, $refresh);
        $perfPage = max(1, (int)($_GET['perf_page'] ?? 1));
        $perfPerPage = 20;
        $perfTotal = count($pagePerf['performance']);
        $perfPage = min($perfPage, max(1, (int)ceil($perfTotal / $perfPerPage)));
        $perfOffset = ($perfPage - 1) * $perfPerPage;
        $pagePerformanceRows = array_slice($pagePerf['performance'], $perfOffset, $perfPerPage);

        $stmtSettings = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($stmtSettings->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $totalPosts     = Post::count();
        $publishedPosts = Post::count(['status' => 'published']);
        $totalCats      = count(Category::getAll());
        $totalViews     = (int)$db->query("SELECT SUM(views) FROM posts")->fetchColumn();

        view('admin/analytics', [
            'total_posts'      => $totalPosts,
            'published_posts'  => $publishedPosts,
            'total_cats'       => $totalCats,
            'total_views'      => $totalViews,
            'report'           => $report,
            'page_perf'        => $pagePerformanceRows,
            'page_perf_total'  => $perfTotal,
            'real_views'       => $realViews,
            'views_today'      => $viewsToday,
            'local_chart_data' => $localChartData,
            'mobile_views'     => $mobileViews,
            'desktop_views'    => $desktopViews,
            'referers'         => $referers,
            'settings'         => $settings,
            'days'             => $days,
            'page_title'       => 'Thống kê & Báo cáo'
        ]);
    }

    public function saveAnalyticsSettings() {
        requireAdmin();
        $db = \Database::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!verifyCSRFToken($token)) {
                $_SESSION['flash_error'] = 'Phiên làm việc hết hạn, vui lòng thử lại.';
                redirect('/admin/analytics');
            }

            $gaId           = strtoupper(trim((string)($_POST['ga_id'] ?? '')));
            $propertyId     = preg_replace('/\D+/', '', (string)($_POST['ga_property_id'] ?? ''));
            $gscVerification = trim((string)($_POST['gsc_verification'] ?? ''));
            $gscSiteUrl     = trim((string)($_POST['gsc_site_url'] ?? ''));

            $errors = [];
            if ($gaId !== '' && !preg_match('/^G-[A-Z0-9]+$/', $gaId)) {
                $errors[] = 'Measurement ID phải có dạng G-XXXXXXXXXX.';
            }
            if ($propertyId !== '' && !preg_match('/^\d+$/', $propertyId)) {
                $errors[] = 'GA4 Property ID chỉ gồm chữ số.';
            }
            if ($gscSiteUrl !== ''
                && !str_starts_with($gscSiteUrl, 'sc-domain:')
                && !filter_var($gscSiteUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'Search Console property phải là sc-domain:domain.com hoặc URL đầy đủ.';
            }

            $credentialJson = trim((string)($_POST['service_account_json'] ?? ''));
            if (isset($_FILES['credentials_file']) && $_FILES['credentials_file']['error'] === UPLOAD_ERR_OK) {
                if ((int)$_FILES['credentials_file']['size'] > 65536) {
                    $errors[] = 'File Service Account vượt quá 64 KB.';
                } else {
                    $credentialJson = trim((string)file_get_contents($_FILES['credentials_file']['tmp_name']));
                }
            }

            $encryptedCredentials = null;
            if ($credentialJson !== '') {
                $credentials = json_decode($credentialJson, true);
                if (!is_array($credentials)
                    || ($credentials['type'] ?? '') !== 'service_account'
                    || empty($credentials['client_email'])
                    || empty($credentials['private_key'])
                    || ($credentials['token_uri'] ?? '') !== 'https://oauth2.googleapis.com/token'
                    || !str_ends_with((string)$credentials['client_email'], '.gserviceaccount.com')) {
                    $errors[] = 'Service Account JSON không đúng định dạng của Google Cloud.';
                } else {
                    $encryptedCredentials = analyticsEncryptSecret(json_encode($credentials, JSON_UNESCAPED_SLASHES));
                    if ($encryptedCredentials === '') {
                        $errors[] = 'Không thể mã hóa credentials trên máy chủ.';
                    }
                }
            }

            if (!empty($errors)) {
                $_SESSION['flash_error'] = implode(' ', $errors);
                redirect('/admin/analytics');
            }

            $upsertSetting = function (string $key, string $value) use ($db) {
                $stmt = $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text')");
                $stmt->execute([$key, $value]);
            };

            $upsertSetting('ga_id', $gaId);
            $upsertSetting('ga_property_id', $propertyId);
            $upsertSetting('gsc_verification', $gscVerification);
            $upsertSetting('gsc_site_url', $gscSiteUrl);

            $numericKeys = [
                'seo_monthly_cost', 'organic_lead_value',
                'kpi_organic_sessions_target', 'kpi_impressions_target',
                'kpi_position_target', 'kpi_ctr_target',
                'kpi_engagement_rate_target', 'kpi_avg_engagement_time_target',
                'kpi_conversion_rate_target', 'kpi_roi_target'
            ];
            foreach ($numericKeys as $key) {
                $val = max(0.0, (float)($_POST[$key] ?? 0.0));
                $upsertSetting($key, (string)$val);
            }

            if (!empty($_POST['remove_credentials'])) {
                $upsertSetting('google_service_account_enc', '');
            } elseif ($encryptedCredentials !== null) {
                $upsertSetting('google_service_account_enc', $encryptedCredentials);
            }

            analyticsClearCache();
            redirect('/admin/analytics?refresh=1', 'Đã lưu cấu hình đo lường. Dữ liệu Google sẽ được cập nhật.');
        }
        redirect('/admin/analytics');
    }
}
