<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 

// Fetch Google reports & settings passed from controller
$ga = $report['ga']['summary'] ?? [];
$gsc = $report['gsc']['summary'] ?? [];
$events = $report['ga']['events'] ?? [];
$keywords = $report['gsc']['keywords'] ?? [];

$organicSessions = (int)round($ga['sessions'] ?? 0);
$impressions = (int)($gsc['impressions'] ?? 0);
$ctr = (float)($gsc['ctr'] ?? 0) * 100;
$position = (float)($gsc['position'] ?? 0);
$engagementRate = (float)($ga['engagementRate'] ?? 0) * 100;
$avgEngagement = (float)($ga['averageEngagementTimePerSession'] ?? 0);
$leadCount = (int)($events['generate_lead'] ?? 0);
$conversionRate = $organicSessions > 0 ? ($leadCount / $organicSessions) * 100 : 0;

$monthlyCost = (float)($settings['seo_monthly_cost'] ?? 0);
$leadValue = (float)($settings['organic_lead_value'] ?? 0);
$periodCost = $monthlyCost > 0 ? ($monthlyCost / 30) * $days : 0;
$estimatedRevenue = $leadCount * $leadValue;
$organicRoi = $periodCost > 0 ? (($estimatedRevenue - $periodCost) / $periodCost) * 100 : null;

$formatNumber = fn($value): string => number_format((float)$value, 0, ',', '.');
$formatPercent = fn($value): string => number_format((float)$value, 1, ',', '.') . '%';
$formatDuration = function (float $seconds): string {
    $seconds = max(0, (int)round($seconds));
    return $seconds >= 60 ? floor($seconds / 60) . 'm ' . ($seconds % 60) . 's' : $seconds . 's';
};
$targetText = function (string $key, string $suffix = '') use ($settings): string {
    $target = (float)($settings[$key] ?? 0);
    return $target > 0 ? 'Mục tiêu: ' . number_format($target, $suffix === '%' ? 1 : 0, ',', '.') . $suffix : 'Chưa đặt mục tiêu';
};

$metricCards = [
    ['Organic Traffic (GA4)', $formatNumber($organicSessions), $targetText('kpi_organic_sessions_target'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'sky'],
    ['Hiển thị tìm kiếm (GSC)', $formatNumber($impressions), $targetText('kpi_impressions_target'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>', 'indigo'],
    ['Vị trí trung bình GSC', $position > 0 ? number_format($position, 1, ',', '.') : '—', (float)($settings['kpi_position_target'] ?? 0) > 0 ? 'Mục tiêu: Top ' . number_format((float)($settings['kpi_position_target'] ?? 0), 0, ',', '.') : 'Chưa đặt mục tiêu', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>', 'amber'],
    ['Tỷ lệ Click CTR GSC', $formatPercent($ctr), $targetText('kpi_ctr_target', '%'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>', 'emerald'],
    ['Tỷ lệ tương tác GA4', $formatPercent($engagementRate), $targetText('kpi_engagement_rate_target', '%'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>', 'sky'],
    ['Thời lượng tương tác TB', $formatDuration($avgEngagement), $targetText('kpi_avg_engagement_time_target', 's'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', 'indigo'],
    ['Tỷ lệ chuyển đổi Lead', $formatPercent($conversionRate), $targetText('kpi_conversion_rate_target', '%'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>', 'rose'],
    ['Organic ROI dự kiến', $organicRoi === null ? '—' : $formatPercent($organicRoi), $targetText('kpi_roi_target', '%'), '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>', 'emerald'],
];

$eventLabels = [
    'click_to_call' => ['Lượt click gọi điện', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>'],
    'copy_email' => ['Lượt copy email', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>'],
    'scroll_depth' => ['Lượt cuộn sâu trang', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>'],
    'lead_form_start' => ['Bắt đầu điền form', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>'],
    'form_submit_attempt' => ['Nhấn nút gửi form', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>'],
    'generate_lead' => ['Gửi lead thành công', '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>'],
];
?>

<!-- Section 1: Overview Stats & Local Trend Chart -->
<?php include __DIR__ . '/analytics_partials/local_overview.php'; ?>

<!-- Errors / Notifications -->
<?php if (!empty($report['errors'])): ?>
    <div class="alert alert-warning" style="margin-bottom: 2rem;">
        <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div class="alert-content">
            <strong style="display: block; margin-bottom: 0.25rem;">Thông báo kết nối Google APIs:</strong>
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach ($report['errors'] as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- Section 2: Google Performance Metrics, Referrers, Keywords & Events -->
<?php include __DIR__ . '/analytics_partials/google_reports.php'; ?>

<!-- Section 3: Detailed Page Performance Table -->
<?php include __DIR__ . '/analytics_partials/page_performance.php'; ?>

<!-- Section 4: Google API & KPI Settings Form -->
<?php include __DIR__ . '/analytics_partials/settings_form.php'; ?>

<!-- Load Chart.js from CDN & Assets -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/admin_analytics.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = <?php echo json_encode(array_column($local_chart_data ?? [], 'date')); ?>;
    const viewsData = <?php echo json_encode(array_column($local_chart_data ?? [], 'count')); ?>;
    initAnalyticsTrendChart(labels, viewsData);
});
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
