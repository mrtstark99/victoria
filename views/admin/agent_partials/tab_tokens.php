<?php
/**
 * Tab 2: API Bearer Tokens Partial
 */
?>
<!-- TAB 2: API BEARER TOKENS -->
<div class="agent-tab-panel" id="tab-main-tokens">
    <div class="panel-header-card">
        <div class="panel-header-info">
            <h2 class="panel-title">
                <span>Danh Sách API Bearer Tokens</span>
                <span class="badge badge-success">Bảo Mật SHA-256</span>
            </h2>
            <p class="panel-desc">
                Cấp quyền truy cập REST API an toàn cho từng AI Agent hoặc IDE Workspace (Cursor, Claude, Antigravity).
            </p>
        </div>
        <div class="panel-header-actions">
            <button type="button" class="btn btn-sm btn-primary-action" onclick="openCreateTokenModal()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>+ Cấp Token Mới</span>
            </button>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="admin-table-container" style="border: none; border-radius: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tên Agent</th>
                        <th>Tác giả</th>
                        <th>Quyền hạn (Scopes)</th>
                        <th>IP Allowlist</th>
                        <th style="text-align: center;">Lượt gọi</th>
                        <th>Ngày tạo</th>
                        <th style="text-align: right; width: 90px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tokens)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--muted-foreground);">
                                Chưa có Token nào được cấp. Hãy tạo Token đầu tiên để kết nối AI Agent.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tokens as $tk): 
                            $scopesArr = array_filter(array_map('trim', explode(',', $tk['permissions'] ?? '')));
                        ?>
                            <tr>
                                <td>
                                    <strong style="font-size: 0.85rem; color: var(--foreground); display: block;"><?php echo htmlspecialchars($tk['token_name']); ?></strong>
                                    <span style="font-size: 0.7rem; font-family: monospace; color: var(--muted-foreground);">#<?php echo $tk['id']; ?></span>
                                </td>
                                <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($tk['author_name'] ?? 'Admin'); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                        <?php foreach ($scopesArr as $sc): ?>
                                            <span class="scope-chip"><?php echo htmlspecialchars($sc); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td style="font-size: 0.75rem; font-family: monospace; color: var(--muted-foreground);">
                                    <?php echo !empty($tk['allowed_ips']) ? htmlspecialchars($tk['allowed_ips']) : 'Tất cả (*)'; ?>
                                </td>
                                <td style="text-align: center; font-size: 0.8rem; font-weight: 700;">
                                    <?php echo number_format((int)($tk['request_count'] ?? 0)); ?>
                                </td>
                                <td style="font-size: 0.75rem; color: var(--muted-foreground); white-space: nowrap;">
                                    <?php echo formatDate($tk['created_at'], 'd/m/Y H:i'); ?>
                                </td>
                                <td style="text-align: right;">
                                    <div class="table-actions-cell">
                                        <button type="button" class="table-action-btn" title="Sửa" onclick="openEditTokenModal(<?php echo htmlspecialchars(json_encode($tk)); ?>)">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <form method="POST" action="/admin/agent" style="display: inline;" onsubmit="return confirm('Thu hồi Token này?')">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="action" value="revoke">
                                            <input type="hidden" name="revoke_token_id" value="<?php echo $tk['id']; ?>">
                                            <button type="submit" class="table-action-btn destructive" title="Thu hồi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
