<?php
/**
 * Task Modals Partial
 * Unified with the design system of Analytics and Admin Layout
 */
?>
<!-- Modal 1: Thêm Kế Hoạch Chuẩn Mới -->
<div id="addTaskModalOverlay" class="task-modal-overlay" style="display: none;">
    <div class="task-modal-container" style="max-width: 620px;">
        <div class="task-modal-header">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="stat-icon-wrap emerald" style="width: 34px; height: 34px; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--foreground);">Thêm Kế Hoạch Làm Việc Mới</h3>
            </div>
            <button type="button" onclick="closeAddTaskModal()" class="task-modal-close-btn" aria-label="Đóng">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="/admin" id="addTaskForm" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="create_task">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="add_task_content" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">
                    Nội dung nhiệm vụ <span style="color:var(--destructive)">*</span>
                </label>
                <textarea id="add_task_content" name="task_content" rows="3" class="form-control" required placeholder="Ví dụ: Viết bài Pillar 'Chiến lược Content SEO 2026' chuẩn E-E-A-T..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_cycle_type" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Chu kỳ</label>
                    <select id="add_task_cycle_type" name="task_cycle_type" class="form-control">
                        <option value="daily" selected>Lịch Ngày (Daily)</option>
                        <option value="weekly">Lịch Tuần (Weekly)</option>
                        <option value="monthly">6 Tháng (Monthly)</option>
                        <option value="adhoc">Bổ sung / Phát sinh</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_session_slot" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Phiên trong ngày</label>
                    <select id="add_task_session_slot" name="task_session_slot" class="form-control">
                        <option value="morning" selected>Buổi Sáng (Audit / Lên outline)</option>
                        <option value="afternoon">Buổi Chiều (Sản xuất Content)</option>
                        <option value="evening">Buổi Tối (Tối ưu On-page)</option>
                        <option value="full_day">Cả ngày</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_scheduled_date" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ngày thực hiện</label>
                    <input type="date" id="add_task_scheduled_date" name="task_scheduled_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_month_num" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Tháng thứ</label>
                    <select id="add_task_month_num" name="task_month_num" class="form-control">
                        <option value="1">Tháng 1</option>
                        <option value="2">Tháng 2</option>
                        <option value="3">Tháng 3</option>
                        <option value="4">Tháng 4</option>
                        <option value="5">Tháng 5</option>
                        <option value="6">Tháng 6</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_priority" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Mức ưu tiên</label>
                    <select id="add_task_priority" name="task_priority" class="form-control">
                        <option value="urgent">Khẩn cấp</option>
                        <option value="high">Cao</option>
                        <option value="medium" selected>Trung bình</option>
                        <option value="low">Thấp</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_phase" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Giai đoạn (Phase)</label>
                    <input type="text" id="add_task_phase" name="task_phase" class="form-control" placeholder="Ví dụ: Tuần 1, Giai đoạn 1...">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_task_category" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Danh mục</label>
                    <input type="text" id="add_task_category" name="task_category" class="form-control" value="Nội dung" list="taskCategorySuggestionsDash">
                    <datalist id="taskCategorySuggestionsDash">
                        <?php foreach ($task_categories as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="add_task_notes" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ghi chú thêm</label>
                <textarea id="add_task_notes" name="task_notes" rows="2" class="form-control" placeholder="Yêu cầu chi tiết, prompt đặc thù, liên kết bài viết..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary" onclick="closeAddTaskModal()">Hủy</button>
                <button type="submit" class="btn" style="font-weight: 700;">Thêm kế hoạch</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Thêm Việc Phát Sinh (Ad-hoc Task Modal) -->
<div id="addAdHocTaskModalOverlay" class="task-modal-overlay" style="display: none;">
    <div class="task-modal-container" style="max-width: 540px;">
        <div class="task-modal-header">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="stat-icon-wrap amber" style="width: 34px; height: 34px; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--foreground);">Thêm Việc Bổ Sung / Phát Sinh</h3>
            </div>
            <button type="button" onclick="closeAddAdHocModal()" class="task-modal-close-btn" aria-label="Đóng">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="/admin" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="create_adhoc_task">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="adhoc_task_content" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">
                    Nhiệm vụ cần làm gấp <span style="color:var(--destructive)">*</span>
                </label>
                <textarea id="adhoc_task_content" name="task_content" rows="3" class="form-control" required placeholder="Ví dụ: Bổ sung FAQ cho bài viết đang hot, cập nhật số liệu mới..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="adhoc_task_scheduled_date" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ngày thực hiện</label>
                    <input type="date" id="adhoc_task_scheduled_date" name="task_scheduled_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="adhoc_task_priority" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Mức ưu tiên</label>
                    <select id="adhoc_task_priority" name="task_priority" class="form-control">
                        <option value="urgent">Khẩn cấp</option>
                        <option value="high" selected>Cao</option>
                        <option value="medium">Trung bình</option>
                        <option value="low">Thấp</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="adhoc_task_category" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Chuyên mục</label>
                <input type="text" id="adhoc_task_category" name="task_category" class="form-control" value="Bổ sung &amp; Phát sinh">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="adhoc_task_notes" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ghi chú</label>
                <input type="text" id="adhoc_task_notes" name="task_notes" class="form-control" placeholder="Ghi chú thêm cho AI hoặc nhóm biên tập...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary" onclick="closeAddAdHocModal()">Hủy</button>
                <button type="submit" class="btn" style="font-weight: 700;">Thêm việc phát sinh</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Chỉnh Sửa Kế Hoạch -->
<div id="editTaskModalOverlay" class="task-modal-overlay" style="display: none;">
    <div class="task-modal-container" style="max-width: 620px;">
        <div class="task-modal-header">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="stat-icon-wrap" style="background: var(--secondary); color: var(--foreground); width: 34px; height: 34px; border-radius: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--foreground);">Chỉnh Sửa Kế Hoạch</h3>
            </div>
            <button type="button" onclick="closeEditTaskModal()" class="task-modal-close-btn" aria-label="Đóng">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="/admin" id="editTaskForm" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="update_task">
            <input type="hidden" name="task_id" id="edit_task_id" value="">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="edit_task_content" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">
                    Nội dung nhiệm vụ <span style="color:var(--destructive)">*</span>
                </label>
                <textarea id="edit_task_content" name="task_content" rows="3" class="form-control" required></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_cycle_type" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Chu kỳ</label>
                    <select id="edit_task_cycle_type" name="task_cycle_type" class="form-control">
                        <option value="daily">Lịch Ngày (Daily)</option>
                        <option value="weekly">Lịch Tuần (Weekly)</option>
                        <option value="monthly">6 Tháng (Monthly)</option>
                        <option value="adhoc">Bổ sung / Phát sinh</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_session_slot" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Phiên trong ngày</label>
                    <select id="edit_task_session_slot" name="task_session_slot" class="form-control">
                        <option value="morning">Buổi Sáng</option>
                        <option value="afternoon">Buổi Chiều</option>
                        <option value="evening">Buổi Tối</option>
                        <option value="full_day">Cả ngày</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_scheduled_date" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ngày thực hiện</label>
                    <input type="date" id="edit_task_scheduled_date" name="task_scheduled_date" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_month_num" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Tháng thứ</label>
                    <select id="edit_task_month_num" name="task_month_num" class="form-control">
                        <option value="1">Tháng 1</option>
                        <option value="2">Tháng 2</option>
                        <option value="3">Tháng 3</option>
                        <option value="4">Tháng 4</option>
                        <option value="5">Tháng 5</option>
                        <option value="6">Tháng 6</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_priority" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Mức ưu tiên</label>
                    <select id="edit_task_priority" name="task_priority" class="form-control">
                        <option value="urgent">Khẩn cấp</option>
                        <option value="high">Cao</option>
                        <option value="medium">Trung bình</option>
                        <option value="low">Thấp</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_phase" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Giai đoạn (Phase)</label>
                    <input type="text" id="edit_task_phase" name="task_phase" class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_task_category" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Danh mục</label>
                    <input type="text" id="edit_task_category" name="task_category" class="form-control">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="edit_task_notes" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Ghi chú thêm</label>
                <textarea id="edit_task_notes" name="task_notes" rows="2" class="form-control"></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: flex; align-items: center; gap: 0.55rem; cursor: pointer; user-select: none; font-size: 0.88rem; font-weight: 700;">
                    <input type="checkbox" name="task_is_completed" id="edit_task_is_completed" value="1" style="width: 18px; height: 18px; accent-color: #059669; cursor: pointer;">
                    <span>Đã hoàn thành</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary" onclick="closeEditTaskModal()">Hủy</button>
                <button type="submit" class="btn" style="font-weight: 700;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Xem Chi Tiết Nhiệm Vụ (Quick View Modal) -->
<div id="viewTaskModalOverlay" class="task-modal-overlay" style="display: none;">
    <div class="task-modal-container" style="max-width: 580px;">
        <div class="task-modal-header">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="stat-icon-wrap sky" style="width: 34px; height: 34px; border-radius: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--foreground);">Chi Tiết Nhiệm Vụ AI</h3>
                    <div id="view_task_status_pill" style="font-size: 0.75rem; font-weight: 700; margin-top: 0.15rem;"></div>
                </div>
            </div>
            <button type="button" onclick="closeViewTaskModal()" class="task-modal-close-btn" aria-label="Đóng">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            <!-- Content Box -->
            <div class="task-view-content-box">
                <div style="font-size: 0.725rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.03em;">Nội Dung Nhiệm Vụ</div>
                <div id="view_task_content" style="font-size: 0.95rem; font-weight: 700; color: var(--foreground); line-height: 1.55; white-space: pre-wrap;"></div>
            </div>

            <!-- Meta Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.85rem;">
                <div class="task-view-meta-card">
                    <span class="task-view-meta-title">Chu kỳ / Phân loại</span>
                    <strong id="view_task_cycle" class="task-view-meta-val"></strong>
                </div>
                <div class="task-view-meta-card">
                    <span class="task-view-meta-title">Mức độ ưu tiên</span>
                    <strong id="view_task_priority" class="task-view-meta-val"></strong>
                </div>
                <div class="task-view-meta-card">
                    <span class="task-view-meta-title">Chuyên mục</span>
                    <strong id="view_task_category" class="task-view-meta-val"></strong>
                </div>
                <div class="task-view-meta-card">
                    <span class="task-view-meta-title">Ngày thực hiện / Phiên</span>
                    <strong id="view_task_date" class="task-view-meta-val"></strong>
                </div>
            </div>

            <!-- Notes wrapper -->
            <div id="view_task_notes_wrapper" style="display: none;" class="task-view-notes-box">
                <div style="font-size: 0.75rem; font-weight: 800; color: #1d4ed8; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>Ghi Chú &amp; Hướng Dẫn:</span>
                </div>
                <div id="view_task_notes" style="font-size: 0.85rem; color: #1e3a8a; line-height: 1.5; white-space: pre-wrap; font-weight: 500;"></div>
            </div>

            <!-- Modal Footer -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 0.25rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="editCurrentViewingTask()" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    <span>Chỉnh sửa nhiệm vụ</span>
                </button>
                <button type="button" class="btn btn-sm" onclick="closeViewTaskModal()" style="font-weight: 700;">Đóng</button>
            </div>
        </div>
    </div>
</div>

