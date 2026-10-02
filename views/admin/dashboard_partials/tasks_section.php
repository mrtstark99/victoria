<?php
/**
 * AI Agent Work Plan & Tasks Section Partial
 * Styled to match Analytics pages with unified cards, tokens, and SVG icons
 */
?>
<!-- AI AGENT WORK PLAN & REAL CALENDAR SCHEDULER SECTION -->
<div class="card" id="tasks" style="margin-bottom: 2rem;">
    <!-- Unified Card Header -->
    <div class="card-header">
        <div class="card-header-left">
            <div class="stat-icon-wrap indigo" style="width: 34px; height: 34px; border-radius: 10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <h3 class="card-title">Kế hoạch công việc &amp; Nhiệm vụ AI</h3>
                <div class="card-subtitle">Lịch trình tự động hóa sản xuất nội dung, tối ưu SEO và công việc định kỳ</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <span id="badgePendingCount" class="badge-pill-tag" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.25); font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span><?php echo (int)($task_stats['pending'] ?? 0); ?> việc chờ xử lý</span>
            </span>

            <!-- Nút Thêm Kế Hoạch Chuẩn -->
            <button type="button" class="btn btn-secondary btn-sm" onclick="openAddTaskModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Thêm Kế Hoạch</span>
            </button>

            <!-- Nút Việc Phát Sinh -->
            <button type="button" class="btn btn-secondary btn-sm" onclick="openAddAdHocModal()" style="border-color: rgba(217, 119, 6, 0.3); background: rgba(245, 158, 11, 0.08); color: #b45309;" title="Thêm việc đột xuất/phát sinh trong ngày">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #d97706;">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                <span>Việc Phát Sinh</span>
            </button>
        </div>
    </div>

    <!-- KPI Metric Grid (Unified with Stat Cards) -->
    <div class="task-kpi-grid">
        <!-- 1: Tổng nhiệm vụ -->
        <div class="task-kpi-card">
            <div class="stat-icon-wrap" style="background: var(--secondary); color: var(--foreground); width: 34px; height: 34px; border-radius: 9px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
            </div>
            <div>
                <div class="task-kpi-label">Tổng nhiệm vụ</div>
                <strong class="task-kpi-val" id="stat_total_tasks"><?php echo (int)($task_stats['total'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- 2: Chờ xử lý -->
        <div class="task-kpi-card">
            <div class="stat-icon-wrap sky" style="width: 34px; height: 34px; border-radius: 9px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="task-kpi-label" style="color: #0284c7;">Chờ xử lý</div>
                <strong class="task-kpi-val" style="color: #0284c7;" id="stat_pending_tasks"><?php echo (int)($task_stats['pending'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- 3: Khẩn cấp / Cao -->
        <div class="task-kpi-card">
            <div class="stat-icon-wrap rose" style="width: 34px; height: 34px; border-radius: 9px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="task-kpi-label" style="color: #dc2626;">Khẩn cấp / Cao</div>
                <strong class="task-kpi-val" style="color: #dc2626;" id="stat_urgent_tasks"><?php echo (int)($task_stats['urgent_high'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- 4: Phát sinh -->
        <div class="task-kpi-card">
            <div class="stat-icon-wrap amber" style="width: 34px; height: 34px; border-radius: 9px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <div>
                <div class="task-kpi-label" style="color: #d97706;">Phát sinh</div>
                <strong class="task-kpi-val" style="color: #d97706;" id="stat_adhoc_tasks"><?php echo (int)($task_stats['adhoc_pending'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- 5: Đã xong -->
        <div class="task-kpi-card">
            <div class="stat-icon-wrap emerald" style="width: 34px; height: 34px; border-radius: 9px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="task-kpi-label" style="color: #059669;">Đã xong</div>
                <strong class="task-kpi-val" style="color: #059669;" id="stat_completed_tasks"><?php echo (int)($task_stats['completed'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- 6: Tiến độ -->
        <div class="task-kpi-card" style="flex-direction: column; justify-content: center; align-items: stretch; gap: 0.35rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span class="task-kpi-label">Tiến độ</span>
                <strong style="font-size: 0.95rem; color: #059669; font-weight: 800;" id="stat_completion_rate"><?php echo (int)($task_stats['completion_rate'] ?? 0); ?>%</strong>
            </div>
            <div style="width: 100%; height: 7px; background: var(--secondary); border-radius: 9999px; overflow: hidden; border: 1px solid var(--border);">
                <div id="stat_progress_bar" style="width: <?php echo (int)($task_stats['completion_rate'] ?? 0); ?>%; height: 100%; background: #059669; border-radius: 9999px; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    <!-- Cycle Filters & Search Control Bar -->
    <div class="task-filter-bar">
        <!-- Segmented Cycle Filters -->
        <div class="cycle-filter-pills">
            <button type="button" class="clean-filter-btn active cycle-filter-btn" data-cycle="adhoc" onclick="filterByCycle('adhoc', this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                <span>Phát sinh</span>
            </button>
            <button type="button" class="clean-filter-btn cycle-filter-btn" data-cycle="daily" onclick="filterByCycle('daily', this)">
                <span>Lịch Ngày</span>
            </button>
            <button type="button" class="clean-filter-btn cycle-filter-btn" data-cycle="weekly" onclick="filterByCycle('weekly', this)">
                <span>Lịch Tuần</span>
            </button>
            <button type="button" class="clean-filter-btn cycle-filter-btn" data-cycle="monthly" onclick="filterByCycle('monthly', this)">
                <span>6 Tháng</span>
            </button>
            <button type="button" class="clean-filter-btn cycle-filter-btn" data-cycle="" onclick="filterByCycle('', this)">
                <span>Tất cả (<?php echo (int)($task_stats['total'] ?? 0); ?>)</span>
            </button>
            <input type="hidden" name="cycle_filter_val" id="cycle_filter_val" value="adhoc">
        </div>

        <!-- Search & Sub Filters -->
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <select id="taskMonthNumFilter" onchange="applyClientTaskFilters(1)" class="form-control" style="width: auto; padding: 0.35rem 0.65rem; font-size: 0.825rem; height: 34px; font-weight: 600;">
                <option value="">Tất cả tháng</option>
                <option value="1">Tháng 1</option>
                <option value="2">Tháng 2</option>
                <option value="3">Tháng 3</option>
                <option value="4">Tháng 4</option>
                <option value="5">Tháng 5</option>
                <option value="6">Tháng 6</option>
            </select>

            <select id="task_priority_filter" onchange="applyClientTaskFilters(1)" class="form-control" style="width: auto; padding: 0.35rem 0.65rem; font-size: 0.825rem; height: 34px; font-weight: 600;">
                <option value="">Mức ưu tiên</option>
                <option value="urgent">Khẩn cấp</option>
                <option value="high">Cao</option>
                <option value="medium">Trung bình</option>
                <option value="low">Thấp</option>
            </select>

            <select id="task_status_filter" onchange="applyClientTaskFilters(1)" class="form-control" style="width: auto; padding: 0.35rem 0.65rem; font-size: 0.825rem; height: 34px; font-weight: 600;">
                <option value="">Trạng thái</option>
                <option value="pending">Chờ xử lý</option>
                <option value="completed">Đã xong</option>
            </select>

            <select id="task_category_filter" onchange="applyClientTaskFilters(1)" class="form-control" style="width: auto; padding: 0.35rem 0.65rem; font-size: 0.825rem; height: 34px; font-weight: 600;">
                <option value="">Chuyên mục</option>
                <?php foreach ($task_categories as $catName): ?>
                    <option value="<?php echo htmlspecialchars($catName); ?>"><?php echo htmlspecialchars($catName); ?></option>
                <?php endforeach; ?>
            </select>

            <div style="display: flex; align-items: center; gap: 0.3rem;">
                <input type="text" id="task_search_filter" class="form-control" placeholder="Tìm kiếm nội dung..." style="height: 34px; font-size: 0.825rem; width: 170px;" oninput="applyClientTaskFilters(1)">
                <button type="button" class="btn btn-secondary btn-sm" style="height: 34px; padding: 0 0.65rem; display: inline-flex; align-items: center; justify-content: center;" onclick="resetTaskFilters()" title="Đặt lại bộ lọc" aria-label="Đặt lại bộ lọc">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Clean Task Table Matrix & Pagination Included -->
    <?php include __DIR__ . '/tasks_table.php'; ?>
</div>

