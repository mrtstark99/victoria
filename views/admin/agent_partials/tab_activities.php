<?php
/**
 * Tab 5: Audit Logs Partial
 */
?>
<!-- TAB 5: AUDIT LOGS -->
<div class="agent-tab-panel" id="tab-main-activities">
    <div class="panel-header-card">
        <div class="panel-header-info">
            <h2 class="panel-title">
                <span>Nhật Ký Hoạt Động (Audit Logs)</span>
                <span class="badge badge-info"><?php echo number_format($total_activities ?? count($activities ?? [])); ?> sự kiện</span>
            </h2>
            <p class="panel-desc">
                Lịch sử toàn bộ các thao tác gọi REST API, cập nhật quy chuẩn và phân quyền của AI Agent.
            </p>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="admin-table-container" style="border: none; border-radius: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                        <th>Bảng dữ liệu</th>
                        <th>Record ID</th>
                        <th>Thực hiện bởi</th>
                        <th>Địa chỉ IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--muted-foreground);">
                                Chưa có nhật ký hoạt động nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $log): ?>
                            <tr>
                                <td style="font-size: 0.75rem; color: var(--muted-foreground); white-space: nowrap;">
                                    <?php echo formatDate($log['created_at'], 'd/m/Y H:i:s'); ?>
                                </td>
                                <td>
                                    <span class="scope-chip" style="font-weight: 700; color: var(--primary);">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.75rem; font-family: monospace;"><?php echo htmlspecialchars($log['table_name'] ?? '—'); ?></td>
                                <td style="font-size: 0.75rem; font-family: monospace;">#<?php echo htmlspecialchars($log['record_id'] ?? '—'); ?></td>
                                <td style="font-size: 0.8rem; font-weight: 600;"><?php echo htmlspecialchars($log['user_name'] ?? 'Agent'); ?></td>
                                <td style="font-size: 0.75rem; font-family: monospace; color: var(--muted-foreground);"><?php echo htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
