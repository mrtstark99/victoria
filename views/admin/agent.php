<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 
?>

<link rel="stylesheet" href="/assets/css/admin_agent.css">
<?php foreach (['typography_toc', 'ui_takeaways_headings', 'ui_callouts', 'ui_comparisons_tables', 'ui_steps_timeline_metrics', 'ui_faq_cta_download'] as $previewStyle): ?>
<link rel="stylesheet" href="/assets/css/post/<?php echo $previewStyle; ?>.css?v=<?php echo filemtime(APP_ROOT . '/public/assets/css/post/' . $previewStyle . '.css'); ?>">
<?php endforeach; ?>

<!-- Modals & Token Generated Notification -->
<?php include __DIR__ . '/agent_partials/modals.php'; ?>

<!-- Flash Message Notifications -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-error" style="margin-bottom: 1.25rem;">
        <ul style="margin: 0; padding-left: 1.25rem;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Main Two-Column Workspace Layout -->
<div class="agent-workspace-layout">
    <!-- Left Column: Sidebar Controls & Navigation -->
    <?php include __DIR__ . '/agent_partials/sidebar.php'; ?>

    <!-- Right Column: Main Content Panels -->
    <main class="agent-content-area">
        <!-- Tab 1: Master System Prompt & Guidelines -->
        <?php include __DIR__ . '/agent_partials/tab_guidelines.php'; ?>

        <!-- Tab 2: API Bearer Tokens -->
        <?php include __DIR__ . '/agent_partials/tab_tokens.php'; ?>

        <!-- Tab 3: UI Elements Showcase -->
        <?php include __DIR__ . '/agent_partials/tab_ui_elements.php'; ?>

        <!-- Tab 4: Skill Files & Docs -->
        <?php include __DIR__ . '/agent_partials/tab_manual.php'; ?>

        <!-- Tab 5: Audit Logs -->
        <?php include __DIR__ . '/agent_partials/tab_activities.php'; ?>
    </main>
</div>

<!-- Marked JS for Markdown Parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    window.DEFAULT_MASTER_SYSTEM_PROMPT = <?php echo json_encode(
        getDefaultMasterPrompt(),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ); ?>;
</script>
<script src="/assets/js/admin_agent.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.initAgentApp(<?php echo !empty($new_token) ? 'true' : 'false'; ?>);
    });
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
