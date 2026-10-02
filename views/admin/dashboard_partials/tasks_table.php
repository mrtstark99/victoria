<?php
/**
 * Admin Tasks Table Matrix & Pagination Partial
 * Unified with the design system of Analytics & Admin Tables
 */
?>
<!-- Clean Task Table Matrix -->
<div class="admin-table-container">
    <table class="admin-table" id="tasksTable" style="width: 100%; border-collapse: collapse; margin: 0;">
        <thead>
            <tr>
                <th style="width: 44px; text-align: center; padding: 0.85rem 0.4rem;"></th>
                <th style="width: 60px; text-align: center; padding: 0.85rem 0.4rem;">ID</th>
                <th style="padding: 0.85rem 0.85rem; text-align: left; min-width: 260px;">Nhiệm vụ &amp; Kế hoạch thực tế</th>
                <th style="width: 110px; padding: 0.85rem 0.5rem; text-align: left;">Chu kỳ</th>
                <th style="width: 180px; min-width: 160px; padding: 0.85rem 0.65rem; text-align: left;">Chuyên mục</th>
                <th style="width: 120px; padding: 0.85rem 0.5rem; text-align: left;">Mức ưu tiên</th>
                <th style="width: 110px; padding: 0.85rem 0.5rem; text-align: left;">Ngày / Hạn</th>
                <th style="width: 110px; min-width: 110px; text-align: right; padding: 0.85rem 0.85rem; white-space: nowrap;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="agentTasksTableBody">
            <?php if (empty($tasks)): ?>
                <tr id="task-empty-initial-row">
                    <td colspan="8" style="padding: 0;">
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </div>
                            <div class="empty-state-title">Chưa có lịch biểu nào được lập trong hệ thống</div>
                            <div class="empty-state-desc">Hãy bắt đầu bằng cách thêm kế hoạch làm việc mới cho AI Agent hoặc nhóm biên tập.</div>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="openAddTaskModal()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                <span>Thêm kế hoạch thủ công</span>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <tr id="task-empty-filter-row" style="display: none;">
                    <td colspan="8" style="padding: 0;">
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                            </div>
                            <div class="empty-state-title">Không tìm thấy kế hoạch phù hợp</div>
                            <div class="empty-state-desc">Hãy thử thay đổi từ khóa hoặc điều chỉnh các bộ lọc chu kỳ và trạng thái.</div>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="resetTaskFilters()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                <span>Xóa bộ lọc</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php foreach ($tasks as $task): ?>
                    <?php
                    $isDone = !empty($task['is_completed']);
                    $isAdHoc = !empty($task['is_ad_hoc']) || ($task['cycle_type'] ?? '') === 'adhoc';
                    $p = $task['priority'] ?? 'medium';
                    $cType = $task['cycle_type'] ?? 'daily';
                    $sSlot = $task['session_slot'] ?? 'morning';
                    $mNum = (int)($task['month_num'] ?? 1);

                    // Kiểm tra quá hạn
                    $isOverdue = false;
                    $todayStr = date('Y-m-d');
                    $taskDateRaw = !empty($task['scheduled_date']) ? substr($task['scheduled_date'], 0, 10) : (!empty($task['deadline']) ? substr($task['deadline'], 0, 10) : '');
                    if (!$isDone && !empty($taskDateRaw) && $taskDateRaw < $todayStr) {
                        $isOverdue = true;
                    }

                    $cycleLabel = match($cType) {
                        'monthly' => 'Tháng ' . $mNum,
                        'weekly' => 'Tuần',
                        'daily' => 'Ngày',
                        'adhoc' => 'Phát sinh',
                        default => 'Nhiệm vụ'
                    };

                    $prioLabel = match($p) {
                        'urgent' => 'Khẩn cấp',
                        'high' => 'Cao',
                        'low' => 'Thấp',
                        default => 'Trung bình'
                    };

                    $slotLabel = match($sSlot) {
                        'morning' => 'Sáng',
                        'afternoon' => 'Chiều',
                        'evening' => 'Tối',
                        default => ''
                    };

                    $dateDisplay = '—';
                    if (!empty($task['scheduled_date'])) {
                        $dateDisplay = date('d/m/Y', strtotime($task['scheduled_date']));
                    } elseif (!empty($task['deadline'])) {
                        $dateDisplay = date('d/m/Y', strtotime($task['deadline']));
                    }

                    $searchIndex = mb_strtolower(($task['id'] ?? '') . ' ' . ($task['content'] ?? '') . ' ' . ($task['phase'] ?? '') . ' ' . ($task['scheduled_date'] ?? '') . ' ' . ($task['notes'] ?? '') . ' ' . ($task['category'] ?? '') . ($isOverdue ? ' quá hạn overdue' : ''), 'UTF-8');
                    ?>
                    <tr 
                        id="task-row-<?php echo $task['id']; ?>" 
                        class="task-clean-row <?php echo $isDone ? 'task-done' : ($isOverdue ? 'task-overdue' : ''); ?>" 
                        data-cycle="<?php echo htmlspecialchars($cType); ?>"
                        data-month="<?php echo $mNum; ?>"
                        data-status="<?php echo $isDone ? 'completed' : 'pending'; ?>"
                        data-priority="<?php echo htmlspecialchars($p); ?>"
                        data-category="<?php echo htmlspecialchars($task['category'] ?? 'Nội dung'); ?>"
                        data-adhoc="<?php echo $isAdHoc ? '1' : '0'; ?>"
                        data-overdue="<?php echo $isOverdue ? '1' : '0'; ?>"
                        data-search="<?php echo htmlspecialchars($searchIndex); ?>"
                    >
                        <!-- Column 1: Checkbox -->
                        <td style="text-align: center; vertical-align: top; padding: 0.95rem 0.4rem;">
                            <input 
                                type="checkbox" 
                                class="clean-checkbox" 
                                id="chk-task-<?php echo $task['id']; ?>"
                                <?php echo $isDone ? 'checked' : ''; ?>
                                onchange="toggleTaskComplete(<?php echo $task['id']; ?>, this)"
                                title="<?php echo $isDone ? 'Đánh dấu chưa làm' : 'Đánh dấu hoàn thành'; ?>"
                            >
                        </td>

                        <!-- Column 2: ID -->
                        <td style="text-align: center; vertical-align: top; padding: 0.95rem 0.4rem;">
                            <code class="task-id-badge">
                                #<?php echo $task['id']; ?>
                            </code>
                        </td>

                        <!-- Column 3: Content & Meta Context Chips -->
                        <td style="vertical-align: top; padding: 0.95rem 0.85rem; word-break: break-word; text-align: left;">
                            <div 
                                class="task-title-text" 
                                id="task-content-<?php echo $task['id']; ?>" 
                                style="font-weight: 700; font-size: 0.9rem; color: var(--foreground); line-height: 1.45; cursor: pointer;"
                                onclick='openViewTaskModal(<?php echo htmlspecialchars(json_encode($task, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, "UTF-8"); ?>)'
                                title="Nhấp để xem chi tiết đầy đủ"
                            >
                                <?php echo nl2br(htmlspecialchars(trim($task['content']))); ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.45rem; font-size: 0.75rem; flex-wrap: wrap;">
                                <?php if (!empty($task['scheduled_date'])): ?>
                                    <span class="task-meta-chip">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <span><?php echo date('d/m/Y', strtotime($task['scheduled_date'])); ?></span>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($task['phase'])): ?>
                                    <span class="task-meta-chip">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>
                                        </svg>
                                        <span><?php echo htmlspecialchars($task['phase']); ?></span>
                                    </span>
                                <?php endif; ?>

                                <?php if ($slotLabel): ?>
                                    <span class="task-meta-chip">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span>Phiên: <?php echo $slotLabel; ?></span>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($task['notes'])): ?>
                                    <button 
                                        type="button" 
                                        class="task-meta-chip chip-has-note" 
                                        onclick='openViewTaskModal(<?php echo htmlspecialchars(json_encode($task, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, "UTF-8"); ?>)'
                                        title="Có ghi chú chi tiết - Nhấp để xem"
                                    >
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                        <span>Ghi chú</span>
                                    </button>
                                <?php endif; ?>

                                <?php if (!empty($task['completed_at'])): ?>
                                    <span id="task-done-tag-<?php echo $task['id']; ?>" class="task-done-badge">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Đã xong: <?php echo date('d/m H:i', strtotime($task['completed_at'])); ?></span>
                                    </span>
                                <?php else: ?>
                                    <span id="task-done-tag-<?php echo $task['id']; ?>" class="task-done-badge" style="display: none;"></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Column 4: Chu kỳ -->
                        <td style="vertical-align: top; padding: 0.95rem 0.5rem; white-space: nowrap; text-align: left;">
                            <span class="cycle-badge-pill cycle-badge-<?php echo htmlspecialchars($cType); ?>">
                                <?php echo htmlspecialchars($cycleLabel); ?>
                            </span>
                        </td>

                        <!-- Column 5: Chuyên mục -->
                        <td style="vertical-align: top; padding: 0.95rem 0.65rem; text-align: left;">
                            <div style="font-weight: 600; font-size: 0.85rem; color: var(--foreground); line-height: 1.4; word-break: break-word;">
                                <?php echo htmlspecialchars($task['category'] ?? 'Nội dung'); ?>
                            </div>
                        </td>

                        <!-- Column 6: Mức ưu tiên -->
                        <td style="vertical-align: top; padding: 0.95rem 0.5rem; white-space: nowrap; text-align: left;">
                            <span class="task-prio-pill task-prio-<?php echo htmlspecialchars($p); ?>">
                                <?php echo htmlspecialchars($prioLabel); ?>
                            </span>
                        </td>

                        <!-- Column 7: Ngày / Hạn -->
                        <td style="vertical-align: top; padding: 0.95rem 0.5rem; white-space: nowrap; font-size: 0.825rem; text-align: left;">
                            <div style="font-weight: 700; color: <?php echo $isOverdue ? '#e11d48' : 'var(--foreground)'; ?>; font-variant-numeric: tabular-nums;">
                                <?php echo $dateDisplay; ?>
                            </div>
                            <?php if ($isOverdue): ?>
                                <div style="margin-top: 0.25rem;">
                                    <span class="task-overdue-tag">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                            <line x1="12" y1="9" x2="12" y2="13"/>
                                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                                        </svg>
                                        <span>Quá hạn</span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Column 8: Thao tác -->
                        <td style="text-align: right; vertical-align: top; padding: 0.95rem 0.85rem; white-space: nowrap;">
                            <div class="table-actions-cell">
                                <!-- Nút Xem chi tiết -->
                                <button 
                                    type="button" 
                                    class="task-action-btn-styled" 
                                    title="Xem chi tiết đầy đủ"
                                    aria-label="Xem chi tiết đầy đủ"
                                    onclick='openViewTaskModal(<?php echo htmlspecialchars(json_encode($task, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, "UTF-8"); ?>)'
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>

                                <!-- Nút Sửa -->
                                <button 
                                    type="button" 
                                    class="task-action-btn-styled" 
                                    title="Sửa kế hoạch"
                                    aria-label="Sửa kế hoạch"
                                    onclick='openEditTaskModal(<?php echo htmlspecialchars(json_encode($task, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, "UTF-8"); ?>)'
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                <!-- Nút Xóa -->
                                <form method="POST" action="/admin" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa kế hoạch này?')">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete_task">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="task-action-btn-styled destructive" title="Xóa kế hoạch" aria-label="Xóa kế hoạch">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
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

<!-- Pagination Controls (10 items / page) -->
<div class="task-pagination-wrap" id="taskPaginationWrap">
    <div style="font-size: 0.825rem; font-weight: 600; color: var(--muted-foreground);" id="paginationInfo">
        Hiển thị 0 kế hoạch
    </div>
    <div style="display: flex; align-items: center; gap: 0.35rem;" id="paginationNavButtons">
        <!-- Rendered by JS -->
    </div>
</div>

