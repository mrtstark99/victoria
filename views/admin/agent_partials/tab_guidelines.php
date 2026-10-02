<?php
/**
 * Tab 1: Master System Prompt & Guidelines Partial
 */
?>
<!-- TAB 1: MASTER SYSTEM PROMPT & QUY CHUẨN -->
<div class="agent-tab-panel active" id="tab-main-guidelines">
    <form method="POST" action="/admin/agent" id="aiGuidelinesForm" onsubmit="return handleGuidelinesFormSubmit(event)">
        <?php echo csrfField(); ?>
        <input type="hidden" name="action" value="save_guidelines">

        <!-- Content Header Bar -->
        <div class="panel-header-card">
            <div class="panel-header-info">
                <h2 class="panel-title">
                    <span>Master System Prompt &amp; Quy Chuẩn E-E-A-T</span>
                    <span class="badge badge-success">Chuẩn Google Core Update</span>
                </h2>
                <p class="panel-desc">
                    Chỉ đạo toàn bộ phong cách viết, tiêu chuẩn SEO, chống AI Slop và liên kết nội bộ tự động. AI Agent nạp qua <code>GET /api/agent.php?action=system_prompt</code>.
                </p>
            </div>
            <div class="panel-header-actions">
                <button type="button" class="btn btn-secondary btn-sm" onclick="resetDefaultPrompt()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <span>Nạp Mặc Định</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="copyMasterPrompt()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    <span>Sao Chép</span>
                </button>
                <button type="submit" class="btn btn-sm btn-primary-action">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Lưu Master Prompt</span>
                </button>
            </div>
        </div>

        <!-- 4 Highlight Chips -->
        <div class="guideline-highlights-grid">
            <div class="highlight-chip">
                <span class="chip-icon">🎯</span>
                <div class="chip-content">
                    <strong>Khóa Search Intent</strong>
                    <span>Soi Top 10 đối thủ SERP trước khi sản xuất bài.</span>
                </div>
            </div>
            <div class="highlight-chip">
                <span class="chip-icon">🛡️</span>
                <div class="chip-content">
                    <strong>Chuẩn E-E-A-T Grade A</strong>
                    <span>Dẫn chứng số liệu, case study và nguồn dẫn uy tín.</span>
                </div>
            </div>
            <div class="highlight-chip">
                <span class="chip-icon">🚫</span>
                <div class="chip-content">
                    <strong>Chống AI Slop</strong>
                    <span>Cấm hoàn toàn văn mẫu sáo rỗng, mở bài lan man.</span>
                </div>
            </div>
            <div class="highlight-chip">
                <span class="chip-icon">🔗</span>
                <div class="chip-content">
                    <strong>Internal Link 2 Chiều</strong>
                    <span>Chèn link cụm Topic Cluster, tránh trang mồ côi.</span>
                </div>
            </div>
        </div>

        <!-- Prompt Code Editor Box -->
        <div class="card editor-card">
            <div class="editor-toolbar">
                <div class="editor-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    <span>Trình Soạn Thảo Master System Prompt</span>
                </div>
                <span class="editor-format-badge">Markdown + System Protocol</span>
            </div>
            <textarea class="form-control code-editor-textarea" name="ai_custom_system_prompt" id="ai_custom_system_prompt" rows="22"><?php echo htmlspecialchars($guidelines['ai_custom_system_prompt'] ?? getDefaultMasterPrompt()); ?></textarea>
        </div>
    </form>
</div>
