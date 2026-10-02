<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 
?>

<link rel="stylesheet" href="/assets/css/admin_dashboard.css?v=<?php echo file_exists(APP_ROOT . '/public/assets/css/admin_dashboard.css') ? filemtime(APP_ROOT . '/public/assets/css/admin_dashboard.css') : '1.0'; ?>">

<!-- Section 1: Overview Metrics Stats Grid (Same style as Analytics) -->
<?php include __DIR__ . '/dashboard_partials/overview_stats.php'; ?>

<!-- Section 2: AI Agent Work Plan & Real Calendar Scheduler Section -->
<?php include __DIR__ . '/dashboard_partials/tasks_section.php'; ?>

<!-- Modals for Creating, Editing, and Viewing Tasks -->
<?php include __DIR__ . '/dashboard_partials/task_modals.php'; ?>

<script src="/assets/js/admin_dashboard.js?v=<?php echo file_exists(APP_ROOT . '/public/assets/js/admin_dashboard.js') ? filemtime(APP_ROOT . '/public/assets/js/admin_dashboard.js') : '1.0'; ?>"></script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>

