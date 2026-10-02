<?php
/**
 * Tab 4: Skill Files & Docs FAQ Accordion Partial
 */
?>
<!-- TAB 4: BỘ SKILL & TÀI LIỆU DẠNG FAQ ACCORDION (10 FILES .MD) -->
<div class="agent-tab-panel" id="tab-main-manual">
    <!-- Header Card with Expand/Collapse & ZIP Actions -->
    <div class="panel-header-card">
        <div class="panel-header-info">
            <h2 class="panel-title">
                <span>Bộ Kỹ Năng (Skill) &amp; Tài Liệu Tích Hợp AI Agent</span>
                <span class="badge badge-success"><?php echo count($skill_docs ?? []); ?> Files .MD</span>
            </h2>
            <p class="panel-desc">
                Tra cứu trực tiếp từng tệp module Markdown bên dưới hoặc tải trọn gói ZIP để cài vào Antigravity / Cursor / Claude Workspace.
            </p>
        </div>
        <div class="panel-header-actions">
            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllSkillAccordions()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>
                <span id="btnToggleAllSkillsText">Mở rộng tất cả</span>
            </button>

            <a href="/admin/agent/download-manual?format=zip" class="btn btn-sm btn-primary-action" style="text-decoration: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Tải Trọn Bộ Skill (.zip)</span>
            </a>
        </div>
    </div>

    <!-- Parameters Overview Strip -->
    <div class="system-endpoints-strip">
        <div class="endpoint-strip-item">
            <span class="strip-label">Base URL Hệ Thống:</span>
            <code><?php echo htmlspecialchars(getSystemBaseUrl()); ?></code>
        </div>
        <div class="endpoint-strip-item">
            <span class="strip-label">API Gateway Endpoint:</span>
            <code><?php echo htmlspecialchars(getSystemApiEndpoint()); ?></code>
        </div>
    </div>

    <!-- Skill Files FAQ Accordion List -->
    <div class="skill-faq-list">
        <?php if (empty($skill_docs)): ?>
            <div class="card" style="padding: 2.5rem; text-align: center; color: var(--muted-foreground);">
                Chưa tìm thấy các tệp skill trong thư mục <code>cms-seo-agent-skill/</code>.
            </div>
        <?php else: ?>
            <?php 
                $docIndex = 0;
                foreach ($skill_docs as $fileKey => $doc): 
                    $docIndex++;
                    $uniqueId = md5($fileKey);
                    $isOpen = ($docIndex === 1); // Open the first item (SKILL.md) by default
            ?>
                <div class="card skill-faq-card <?php echo $isOpen ? 'active' : ''; ?>" id="skill_card_<?php echo $uniqueId; ?>">
                    <!-- FAQ Accordion Header -->
                    <div class="skill-faq-header" onclick="toggleSkillAccordion('<?php echo $uniqueId; ?>')">
                        <div class="skill-faq-header-left">
                            <span class="skill-doc-icon"><?php echo $doc['icon']; ?></span>
                            <div>
                                <h3 class="skill-doc-title"><?php echo htmlspecialchars($doc['title']); ?></h3>
                                <div class="skill-doc-meta">
                                    <span class="skill-filename-badge"><code><?php echo htmlspecialchars($doc['filename']); ?></code></span>
                                    <span class="badge" style="font-size: 0.65rem; background: var(--secondary); border: 1px solid var(--border); color: var(--muted-foreground);"><?php echo htmlspecialchars($doc['badge']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="skill-faq-header-right">
                            <button type="button" class="btn btn-secondary btn-xs" onclick="event.stopPropagation(); copySkillFile('raw_skill_<?php echo $uniqueId; ?>', this)" title="Sao chép toàn bộ Markdown tệp này">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                <span>Sao chép .md</span>
                            </button>

                            <div class="faq-chevron-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Accordion Body -->
                    <div class="skill-faq-body" style="display: <?php echo $isOpen ? 'block' : 'none'; ?>;">
                        <!-- Hidden Raw Markdown Data -->
                        <textarea id="raw_skill_<?php echo $uniqueId; ?>" style="display: none;"><?php echo htmlspecialchars($doc['content']); ?></textarea>

                        <!-- View Mode Selector Toolbar -->
                        <div class="skill-doc-view-bar">
                            <div class="doc-view-mode-toggle">
                                <button type="button" class="doc-mode-btn active" id="btnModeRendered_<?php echo $uniqueId; ?>" onclick="switchSkillDocViewMode('<?php echo $uniqueId; ?>', 'rendered')">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Đã định dạng</span>
                                </button>
                                <button type="button" class="doc-mode-btn" id="btnModeRaw_<?php echo $uniqueId; ?>" onclick="switchSkillDocViewMode('<?php echo $uniqueId; ?>', 'raw')">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                    <span>Mã nguồn .md</span>
                                </button>
                            </div>

                            <span style="font-size: 0.725rem; color: var(--muted-foreground);">Tệp: <code>cms-seo-agent-skill/<?php echo htmlspecialchars($doc['filename']); ?></code></span>
                        </div>

                        <!-- Rendered Markdown Container -->
                        <div id="rendered_skill_<?php echo $uniqueId; ?>" class="agent-docs-content skill-file-rendered">
                            <div style="padding: 1.5rem; text-align: center; color: var(--muted-foreground); font-size: 0.825rem;">
                                Đang tải nội dung...
                            </div>
                        </div>

                        <!-- Raw Markdown Textarea Container -->
                        <div id="rawbox_skill_<?php echo $uniqueId; ?>" style="display: none; margin-top: 0.75rem;">
                            <textarea class="form-control code-editor-textarea" rows="20" readonly><?php echo htmlspecialchars($doc['content']); ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
