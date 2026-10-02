let currentViewingTaskData = null;
let currentTaskPage = 1;
const TASK_PAGE_SIZE = 10;

function toggleModalDisplay(modalId, isShow, focusInputId = null) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.style.display = isShow ? 'flex' : 'none';
    document.body.style.overflow = isShow ? 'hidden' : '';
    if (isShow && focusInputId) {
        setTimeout(() => { document.getElementById(focusInputId)?.focus(); }, 100);
    }
}

function openAddTaskModal() { toggleModalDisplay('addTaskModalOverlay', true, 'add_task_content'); }
function closeAddTaskModal() { toggleModalDisplay('addTaskModalOverlay', false); }

function openAddAdHocModal() { toggleModalDisplay('addAdHocTaskModalOverlay', true, 'adhoc_task_content'); }
function closeAddAdHocModal() { toggleModalDisplay('addAdHocTaskModalOverlay', false); }

function openEditTaskModal(task) {
    document.getElementById('edit_task_id').value = task.id || '';
    document.getElementById('edit_task_content').value = task.content || '';
    document.getElementById('edit_task_priority').value = task.priority || 'medium';
    document.getElementById('edit_task_category').value = task.category || 'Nội dung';
    document.getElementById('edit_task_cycle_type').value = task.cycle_type || 'daily';
    document.getElementById('edit_task_session_slot').value = task.session_slot || 'morning';
    document.getElementById('edit_task_month_num').value = task.month_num || 1;
    document.getElementById('edit_task_phase').value = task.phase || '';
    document.getElementById('edit_task_scheduled_date').value = task.scheduled_date ? task.scheduled_date.substring(0, 10) : '';
    const chkDone = document.getElementById('edit_task_is_completed');
    if (chkDone) chkDone.checked = parseInt(task.is_completed) === 1;
    document.getElementById('edit_task_notes').value = task.notes || '';

    toggleModalDisplay('editTaskModalOverlay', true, 'edit_task_content');
}
function closeEditTaskModal() { toggleModalDisplay('editTaskModalOverlay', false); }

function openViewTaskModal(task) {
    currentViewingTaskData = task;
    document.getElementById('view_task_content').textContent = task.content || '—';
    
    const isDone = parseInt(task.is_completed) === 1;
    const statusEl = document.getElementById('view_task_status_pill');
    if (statusEl) {
        statusEl.innerHTML = isDone 
            ? '<span style="color:#059669; font-weight:800; display:inline-flex; align-items:center; gap:0.3rem;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Đã hoàn thành</span>' 
            : '<span style="color:#0284c7; font-weight:800; display:inline-flex; align-items:center; gap:0.3rem;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Đang chờ xử lý</span>';
    }

    const cycleMap = { monthly: '6 Tháng (Tháng ' + (task.month_num || 1) + ')', weekly: 'Lịch Tuần', daily: 'Lịch Ngày', adhoc: 'Bổ sung / Phát sinh' };
    document.getElementById('view_task_cycle').textContent = cycleMap[task.cycle_type] || task.cycle_type || '—';
    
    const prioMap = { urgent: 'Khẩn cấp', high: 'Cao', medium: 'Trung bình', low: 'Thấp' };
    document.getElementById('view_task_priority').textContent = prioMap[task.priority] || task.priority || '—';
    document.getElementById('view_task_category').textContent = task.category || 'Nội dung';
    
    let dateStr = task.scheduled_date ? task.scheduled_date : 'Chưa đặt';
    if (task.session_slot) {
        const slotMap = { morning: 'Sáng', afternoon: 'Chiều', evening: 'Tối', full_day: 'Cả ngày' };
        dateStr += ' (' + (slotMap[task.session_slot] || task.session_slot) + ')';
    }
    document.getElementById('view_task_date').textContent = dateStr;

    const notesWrapper = document.getElementById('view_task_notes_wrapper');
    const notesEl = document.getElementById('view_task_notes');
    if (task.notes && task.notes.trim() !== '') {
        notesEl.textContent = task.notes;
        notesWrapper.style.display = 'block';
    } else {
        notesWrapper.style.display = 'none';
    }

    toggleModalDisplay('viewTaskModalOverlay', true);
}
function closeViewTaskModal() { toggleModalDisplay('viewTaskModalOverlay', false); }

function editCurrentViewingTask() {
    if (currentViewingTaskData) {
        closeViewTaskModal();
        openEditTaskModal(currentViewingTaskData);
    }
}

function filterByCycle(cycle, btn) {
    const cycleInput = document.getElementById('cycle_filter_val');
    if (cycleInput) cycleInput.value = cycle;
    document.querySelectorAll('.cycle-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyClientTaskFilters(1);
}

function applyClientTaskFilters(targetPage = 1) {
    const currentCycle = document.getElementById('cycle_filter_val')?.value || '';
    const currentMonth = document.getElementById('taskMonthNumFilter')?.value || '';
    const currentPriority = document.getElementById('task_priority_filter')?.value || '';
    const currentStatus = document.getElementById('task_status_filter')?.value || '';
    const currentCategory = document.getElementById('task_category_filter')?.value || '';
    const currentSearch = document.getElementById('task_search_filter')?.value.toLowerCase().trim() || '';

    const allRows = Array.from(document.querySelectorAll('#agentTasksTableBody tr.task-clean-row'));
    const matchedRows = [];

    allRows.forEach(row => {
        const rowCycle = row.getAttribute('data-cycle') || '';
        const rowMonth = row.getAttribute('data-month') || '';
        const rowPriority = row.getAttribute('data-priority') || '';
        const rowStatus = row.getAttribute('data-status') || '';
        const rowCategory = row.getAttribute('data-category') || '';
        const rowAdHoc = row.getAttribute('data-adhoc') || '0';
        const rowOverdue = row.getAttribute('data-overdue') || '0';
        const rowSearch = row.getAttribute('data-search') || '';

        let matchCycle = !currentCycle || (currentCycle === 'adhoc' ? (rowCycle === 'adhoc' || rowAdHoc === '1' || rowOverdue === '1') : (rowCycle === currentCycle));
        const matchMonth = (!currentMonth || rowMonth === currentMonth);
        const matchPriority = (!currentPriority || rowPriority === currentPriority);
        const matchStatus = (!currentStatus || rowStatus === currentStatus);
        const matchCategory = (!currentCategory || rowCategory === currentCategory);
        const matchSearch = (!currentSearch || rowSearch.includes(currentSearch));

        if (matchCycle && matchMonth && matchPriority && matchStatus && matchCategory && matchSearch) {
            matchedRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });

    const totalMatches = matchedRows.length;
    const totalPages = Math.max(1, Math.ceil(totalMatches / TASK_PAGE_SIZE));
    currentTaskPage = Math.min(Math.max(1, targetPage), totalPages);

    const startIndex = (currentTaskPage - 1) * TASK_PAGE_SIZE;
    const endIndex = startIndex + TASK_PAGE_SIZE;

    matchedRows.forEach((row, index) => {
        row.style.display = (index >= startIndex && index < endIndex) ? '' : 'none';
    });

    const emptyFilterRow = document.getElementById('task-empty-filter-row');
    if (emptyFilterRow) {
        emptyFilterRow.style.display = (allRows.length > 0 && totalMatches === 0) ? '' : 'none';
    }

    renderPaginationControls(totalMatches, totalPages, currentTaskPage);
}

function renderPaginationControls(totalMatches, totalPages, page) {
    const wrap = document.getElementById('taskPaginationWrap');
    const info = document.getElementById('paginationInfo');
    const nav = document.getElementById('paginationNavButtons');
    if (!wrap || !info || !nav) return;

    if (totalMatches === 0) {
        info.textContent = 'Không có kết quả';
        nav.innerHTML = '';
        return;
    }

    const startItem = (page - 1) * TASK_PAGE_SIZE + 1;
    const endItem = Math.min(page * TASK_PAGE_SIZE, totalMatches);
    info.innerHTML = `Hiển thị <strong>${startItem} - ${endItem}</strong> trong tổng số <strong>${totalMatches}</strong> kế hoạch (Trang ${page}/${totalPages})`;

    let html = `<button type="button" class="pagination-btn" ${page <= 1 ? 'disabled' : ''} onclick="applyClientTaskFilters(${page - 1})" title="Trang trước" aria-label="Trang trước"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>`;

    let maxVisibleBtns = 5;
    let startPage = Math.max(1, page - 2);
    let endPage = Math.min(totalPages, startPage + maxVisibleBtns - 1);
    if (endPage - startPage < maxVisibleBtns - 1) {
        startPage = Math.max(1, endPage - maxVisibleBtns + 1);
    }

    if (startPage > 1) {
        html += `<button type="button" class="pagination-btn" onclick="applyClientTaskFilters(1)">1</button>`;
        if (startPage > 2) html += `<span style="padding:0 0.2rem; color:#94a3b8;">...</span>`;
    }

    for (let p = startPage; p <= endPage; p++) {
        html += `<button type="button" class="pagination-btn ${p === page ? 'active' : ''}" onclick="applyClientTaskFilters(${p})">${p}</button>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += `<span style="padding:0 0.2rem; color:#94a3b8;">...</span>`;
        html += `<button type="button" class="pagination-btn" onclick="applyClientTaskFilters(${totalPages})">${totalPages}</button>`;
    }

    html += `<button type="button" class="pagination-btn" ${page >= totalPages ? 'disabled' : ''} onclick="applyClientTaskFilters(${page + 1})" title="Trang tiếp theo" aria-label="Trang tiếp theo"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    nav.innerHTML = html;
}

function resetTaskFilters() {
    const cycleInput = document.getElementById('cycle_filter_val');
    if (cycleInput) cycleInput.value = 'adhoc';
    document.querySelectorAll('.cycle-filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.cycle-filter-btn[data-cycle="adhoc"]')?.classList.add('active');

    ['taskMonthNumFilter', 'task_priority_filter', 'task_status_filter', 'task_category_filter', 'task_search_filter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    applyClientTaskFilters(1);
}

function toggleTaskComplete(taskId, checkbox) {
    const isCompleted = checkbox.checked ? 1 : 0;
    const formData = new FormData();
    formData.append('action', 'toggle_task_complete');
    formData.append('task_id', taskId);
    formData.append('is_completed', isCompleted);
    formData.append('ajax', '1');
    
    const csrfInput = document.querySelector('input[name="csrf_token"]');
    if (csrfInput) formData.append('csrf_token', csrfInput.value);

    const row = document.getElementById('task-row-' + taskId);
    const tag = document.getElementById('task-done-tag-' + taskId);

    if (isCompleted) {
        if (row) {
            row.classList.add('task-done');
            row.classList.remove('task-overdue');
            row.setAttribute('data-status', 'completed');
            row.setAttribute('data-overdue', '0');
        }
        if (tag) {
            const now = new Date();
            const d = String(now.getDate()).padStart(2, '0');
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const h = String(now.getHours()).padStart(2, '0');
            const min = String(now.getMinutes()).padStart(2, '0');
            tag.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <span>Đã xong: ${d}/${m} ${h}:${min}</span>`;
            tag.style.display = 'inline-flex';
        }
    } else {
        if (row) {
            row.classList.remove('task-done');
            row.setAttribute('data-status', 'pending');
        }
        if (tag) {
            tag.innerHTML = '';
            tag.style.display = 'none';
        }
    }

    applyClientTaskFilters(currentTaskPage);

    fetch('/admin', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.stats) {
            const s = data.stats;
            const statMap = {
                'stat_total_tasks': s.total,
                'stat_pending_tasks': s.pending,
                'stat_urgent_tasks': s.urgent_high || 0,
                'stat_adhoc_tasks': s.adhoc_pending || 0,
                'stat_completed_tasks': s.completed,
                'stat_completion_rate': (s.completion_rate || 0) + '%'
            };
            Object.keys(statMap).forEach(k => {
                const el = document.getElementById(k);
                if (el) el.textContent = statMap[k];
            });
            if (document.getElementById('stat_progress_bar')) {
                document.getElementById('stat_progress_bar').style.width = (s.completion_rate || 0) + '%';
            }
            if (document.getElementById('badgePendingCount')) {
                document.getElementById('badgePendingCount').textContent = `${s.pending} việc chờ xử lý`;
            }
        }
    })
    .catch(err => {
        console.error('Lỗi khi cập nhật trạng thái nhiệm vụ:', err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    applyClientTaskFilters(1);

    ['addTaskModalOverlay', 'addAdHocTaskModalOverlay', 'editTaskModalOverlay', 'viewTaskModalOverlay'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function(e) {
                if (e.target === el) toggleModalDisplay(id, false);
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['addTaskModalOverlay', 'addAdHocTaskModalOverlay', 'editTaskModalOverlay', 'viewTaskModalOverlay'].forEach(id => {
                toggleModalDisplay(id, false);
            });
        }
    });
});
