<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllBrandAccordions()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/>
            </svg>
            <span id="toggleAllBrandBtnText">Mở rộng tất cả</span>
        </button>
        <button type="submit" form="brandSettingsForm" class="btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Lưu cấu hình Brand</span>
        </button>
    </div>
</div>

<form method="POST" action="/admin/brand" id="brandSettingsForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <input type="hidden" id="remove_site_logo" name="remove_site_logo" value="0">
    <input type="hidden" id="remove_site_favicon" name="remove_site_favicon" value="0">
    <input type="hidden" id="remove_default_og_image" name="remove_default_og_image" value="0">

    <div class="grid-layout columns-2-1" style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left Column: Settings Accordion Cards -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Card 1: Brand Identity & Logo -->
            <?php include __DIR__ . '/brand_partials/card_identity.php'; ?>

            <!-- Card 2: Contact Information -->
            <?php include __DIR__ . '/brand_partials/card_contact.php'; ?>

            <!-- Card 3: Social Media Links -->
            <?php include __DIR__ . '/brand_partials/card_social.php'; ?>

            <!-- Card 4: Default SEO & Social Sharing -->
            <?php include __DIR__ . '/brand_partials/card_seo.php'; ?>

            <!-- Card 5: Footer & Custom Scripts -->
            <?php include __DIR__ . '/brand_partials/card_footer.php'; ?>
        </div>

        <!-- Right Column: Live Brand Preview Simulator -->
        <?php include __DIR__ . '/brand_partials/live_preview.php'; ?>

    </div>
</form>

<script>
    window.initialLogoUrl = "<?php echo addslashes($settings['site_logo_url'] ?? ''); ?>";
    window.initialFaviconUrl = "<?php echo addslashes($settings['site_favicon_url'] ?? ''); ?>";
    window.initialOgImageUrl = "<?php echo addslashes($settings['default_og_image'] ?? ''); ?>";
</script>
<script src="/assets/js/admin_brand_settings.js"></script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
