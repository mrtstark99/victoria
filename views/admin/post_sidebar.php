<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 

$currentOrderStr = $settings['post_sidebar_widget_order'] ?? 'trending,banner,newsletter,cta,categories';
$currentOrder = array_filter(array_map('trim', explode(',', $currentOrderStr)));

// Load multiple custom HTML widgets
$customHtmlWidgets = [];
if (!empty($settings['post_sidebar_custom_html_widgets'])) {
    $decoded = json_decode($settings['post_sidebar_custom_html_widgets'], true);
    if (is_array($decoded)) {
        $customHtmlWidgets = $decoded;
    }
}

$standardWidgets = [
    'trending' => 'Bài viết đọc nhiều nhất (Trending)',
    'banner' => 'Banner hình ảnh / Quảng cáo (Banner Ads)',
    'newsletter' => 'Bản tin nhận tin tức (Newsletter)',
    'cta' => 'Khung Kêu gọi hành động (Custom CTA)',
    'categories' => 'Chuyên mục đề xuất (Categories)'
];

// Map of all widgets currently available
$allWidgetTitles = $standardWidgets;
foreach ($customHtmlWidgets as $hw) {
    $hwId = $hw['id'] ?? 'html_1';
    $allWidgetTitles[$hwId] = 'Widget HTML: ' . ($hw['title'] ?: 'Tùy chỉnh');
}

// Ensure all existing widgets exist in order
foreach (array_keys($allWidgetTitles) as $wKey) {
    if (!in_array($wKey, $currentOrder)) {
        $currentOrder[] = $wKey;
    }
}
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllAccordions()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/>
            </svg>
            <span id="toggleAllBtnText">Mở rộng tất cả</span>
        </button>
        <button type="submit" form="sidebarSettingsForm" class="btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Lưu thay đổi</span>
        </button>
    </div>
</div>

<form method="POST" action="/admin/post-sidebar" id="sidebarSettingsForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <input type="hidden" id="post_sidebar_widget_order" name="post_sidebar_widget_order" value="<?php echo htmlspecialchars(implode(',', $currentOrder)); ?>">

    <div class="grid-layout columns-2-1" style="display: grid; grid-template-columns: 1.55fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Settings & Widget Accordions -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Section 1: Widget Ordering Manager -->
            <?php include __DIR__ . '/post_sidebar_partials/section_ordering.php'; ?>

            <!-- Section 2: Layout & Sticky Options -->
            <?php include __DIR__ . '/post_sidebar_partials/section_layout.php'; ?>

            <!-- Widget 1: Trending Reads -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_trending.php'; ?>

            <!-- Widget 2: Banner Ads / Display -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_banner.php'; ?>

            <!-- Widget 3: Newsletter -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_newsletter.php'; ?>

            <!-- Widget 4: Custom CTA Banner -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_cta.php'; ?>

            <!-- Widget 5: Categories Quick Links -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_categories.php'; ?>

            <!-- Section 3: Multiple Custom HTML Widgets Manager -->
            <?php include __DIR__ . '/post_sidebar_partials/widget_custom_html.php'; ?>
        </div>

        <!-- Right Column: Sidebar Preview Simulator -->
        <?php include __DIR__ . '/post_sidebar_partials/live_preview.php'; ?>

    </div>
</form>

<script>
    window.initialCustomHtmlCount = <?php echo count($customHtmlWidgets); ?>;
</script>
<script src="/assets/js/admin_post_sidebar_preview.js"></script>
<script src="/assets/js/admin_post_sidebar.js"></script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
