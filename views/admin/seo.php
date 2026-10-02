<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<!-- Section 1: Topic Clusters -->
<?php include __DIR__ . '/seo_partials/topic_clusters.php'; ?>

<!-- Section 2: Target Keywords -->
<?php include __DIR__ . '/seo_partials/target_keywords.php'; ?>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
    // Toggle Topic Cluster Form Panel
    function toggleClusterForm() {
        const panel = document.getElementById('clusterFormPanel');
        const btn = document.getElementById('btnToggleCluster');
        if (!panel) return;
        if (panel.style.display === 'none' || panel.style.display === '') {
            panel.style.display = 'block';
            document.getElementById('cluster_name')?.focus();
            if (btn) btn.style.display = 'none';
        } else {
            panel.style.display = 'none';
            if (btn) btn.style.display = 'inline-flex';
        }
    }

    // Toggle Keyword Form Panel
    function toggleKeywordForm() {
        const panel = document.getElementById('keywordFormPanel');
        const btn = document.getElementById('btnToggleKeyword');
        if (!panel) return;
        if (panel.style.display === 'none' || panel.style.display === '') {
            panel.style.display = 'block';
            document.getElementById('keyword')?.focus();
            if (btn) btn.style.display = 'none';
        } else {
            panel.style.display = 'none';
            if (btn) btn.style.display = 'inline-flex';
        }
    }
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
