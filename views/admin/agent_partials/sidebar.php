<?php
/**
 * Agent Sidebar Navigation Partial
 */
?>
<!-- LEFT COLUMN: SIDEBAR CONTROLS & NAVIGATION -->
<aside class="agent-sidebar">
    <!-- Agent Hub Brand Card -->
    <div class="agent-sidebar-card">
        <div class="agent-hub-brand">
            <div class="agent-hub-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                </svg>
            </div>
            <div>
                <h1 class="agent-hub-title">AI Agent Hub</h1>
                <div class="agent-status-tag">
                    <span class="status-pulse-dot"></span>
                    <span>API Ready (E-E-A-T)</span>
                </div>
            </div>
        </div>

        <!-- Quick Metrics Grid -->
        <div class="agent-quick-stats">
            <div class="quick-stat-item">
                <span class="quick-stat-num"><?php echo count($tokens ?? []); ?></span>
                <span class="quick-stat-lbl">Tokens</span>
            </div>
            <div class="quick-stat-item">
                <span class="quick-stat-num"><?php echo count($skill_docs ?? []); ?></span>
                <span class="quick-stat-lbl">Skills MD</span>
            </div>
            <div class="quick-stat-item">
                <span class="quick-stat-num">17</span>
                <span class="quick-stat-lbl">UI Blocks</span>
            </div>
        </div>
    </div>

    <!-- Vertical Navigation Menu -->
    <nav class="agent-nav-menu">
        <div class="nav-section-title">QUẢN TRỊ &amp; NỘI DUNG</div>

        <button type="button" class="agent-nav-btn active" data-tab="tab-main-guidelines" onclick="switchMainAgentTab('tab-main-guidelines')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
            </svg>
            <span>Master Prompt (Quy chuẩn)</span>
        </button>

        <button type="button" class="agent-nav-btn" data-tab="tab-main-tokens" onclick="switchMainAgentTab('tab-main-tokens')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
            <span>API Tokens</span>
            <span class="nav-badge"><?php echo count($tokens ?? []); ?></span>
        </button>

        <button type="button" class="agent-nav-btn" data-tab="tab-main-ui-elements" onclick="switchMainAgentTab('tab-main-ui-elements')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
            </svg>
            <span>Thư viện UI Elements</span>
            <span class="nav-badge"><?php echo count(getPostElementLibrary()); ?> Nhóm</span>
        </button>

        <div class="nav-section-title" style="margin-top: 0.75rem;">TÍCH HỢP &amp; HỆ THỐNG</div>

        <button type="button" class="agent-nav-btn" data-tab="tab-main-manual" onclick="switchMainAgentTab('tab-main-manual')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            </svg>
            <span>Bộ Skill &amp; Tài liệu</span>
            <span class="nav-badge count-badge"><?php echo count($skill_docs ?? []); ?> Files</span>
        </button>

        <button type="button" class="agent-nav-btn" data-tab="tab-main-activities" onclick="switchMainAgentTab('tab-main-activities')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            <span>Audit Logs</span>
        </button>
    </nav>

    <!-- Quick API Endpoint Widget -->
    <div class="agent-sidebar-card endpoint-box">
        <div class="endpoint-header">
            <span class="endpoint-lbl">API Gateway Endpoint</span>
            <button type="button" class="copy-icon-btn" title="Sao chép Endpoint" onclick="copySnippetText('<?php echo htmlspecialchars(getSystemApiEndpoint()); ?>', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
            </button>
        </div>
        <code class="endpoint-code"><?php echo htmlspecialchars(getSystemApiEndpoint()); ?></code>
    </div>

    <!-- Sidebar Action Buttons -->
    <div class="agent-sidebar-actions">
        <button type="button" class="btn btn-sm btn-outline-full" onclick="openCreateTokenModal()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Cấp Token Mới</span>
        </button>
        <a href="/admin/agent/download-manual?format=zip" class="btn btn-sm btn-secondary btn-outline-full" style="text-decoration: none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Tải Skill AI (.zip)</span>
        </a>
    </div>
</aside>
