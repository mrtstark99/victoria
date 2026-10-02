<?php
/**
 * @file views/admin/contacts.php
 * @description Admin contacts and student inquiry management dashboard view.
 *
 * Layer:
 * - Presentation / Admin View
 *
 * Responsibilities:
 * - Render student consultation submissions table with status filtering.
 * - Provide interactive modal to update processing status and consultant notes.
 * - Provide secure deletion for authorized administrators.
 *
 * Security:
 * - HTML entity encoding for all user inquiry text.
 * - Anti-CSRF token verification on update actions.
 *
 * Dependencies:
 * - Master admin layout (admin_header, admin_footer).
 *
 * Constraints:
 * - Keep this file under 300 lines whenever practical.
 * - All comments and documentation must be written in English.
 * - Follow the project engineering rules.
 *
 * AI Maintenance Rules:
 * - Preserve existing behavior unless change is explicitly required.
 * - Update this header if responsibilities or dependencies change.
 * - Do not place secrets, credentials, or sensitive data in this file.
 */

include APP_ROOT . '/views/layouts/admin_header.php';

$statusBadges = [
    'new' => ['label' => 'Mới nhận', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
    'read' => ['label' => 'Đã đọc', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
    'replied' => ['label' => 'Đã liên hệ', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
    'processing' => ['label' => 'Đang làm hồ sơ', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
    'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
    'archived' => ['label' => 'Lưu trữ', 'class' => 'bg-slate-50 text-slate-600 border-slate-200'],
];
?>

<div class="admin-data-page">

<!-- Filters Bar -->
<div class="card admin-data-toolbar">
  <form method="GET" action="/admin/contacts" class="flex flex-wrap items-center gap-3">
    <div class="flex-1 min-w-[200px]">
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo tên, email, SĐT hoặc nội dung..." class="w-full px-3.5 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
    </div>

    <div class="w-44">
      <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
        <option value="">Tất cả trạng thái</option>
        <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>Mới nhận</option>
        <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Đã đọc</option>
        <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Đã liên hệ</option>
        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Đang làm hồ sơ</option>
        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
        <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Lưu trữ</option>
      </select>
    </div>

    <button type="submit" class="px-4 py-2 bg-primary hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
      <i class="bi bi-filter mr-1"></i>Lọc
    </button>
    <?php if ($status_filter !== '' || $search !== ''): ?>
      <a href="/admin/contacts" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
        Đặt lại
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Table Card -->
<div class="admin-table-container admin-data-table">
  <div class="overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider">
          <th class="py-3 px-4">Học viên</th>
          <th class="py-3 px-4">Thông tin liên hệ</th>
          <th class="py-3 px-4">Nhu cầu du học</th>
          <th class="py-3 px-4">Lời nhắn</th>
          <th class="py-3 px-4 text-center">Trạng thái</th>
          <th class="py-3 px-4">Ngày nhận</th>
          <th class="py-3 px-4 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 font-medium">
        <?php if (empty($contacts)): ?>
          <tr>
            <td colspan="7" class="py-12 text-center text-slate-400">
              <i class="bi bi-inbox text-3xl block mb-2"></i>
              Không tìm thấy yêu cầu tư vấn nào phù hợp.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($contacts as $c): 
            $badge = $statusBadges[$c['status']] ?? ['label' => $c['status'], 'class' => 'bg-slate-100 text-slate-600'];
          ?>
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="py-3.5 px-4">
                <span class="font-bold text-slate-800 text-sm block"><?php echo htmlspecialchars($c['name']); ?></span>
                <span class="text-[11px] text-slate-400">ID: #<?php echo $c['id']; ?></span>
              </td>
              <td class="py-3.5 px-4 space-y-0.5">
                <div><a href="tel:<?php echo htmlspecialchars($c['phone']); ?>" class="text-primary font-bold hover:underline"><i class="bi bi-telephone mr-1"></i><?php echo htmlspecialchars($c['phone']); ?></a></div>
                <div class="text-slate-500"><a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" class="hover:underline"><i class="bi bi-envelope mr-1"></i><?php echo htmlspecialchars($c['email']); ?></a></div>
              </td>
              <td class="py-3.5 px-4 space-y-0.5">
                <?php if (!empty($c['intake_period'])): ?>
                  <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-semibold">Kỳ: <?php echo htmlspecialchars($c['intake_period']); ?></span>
                <?php endif; ?>
                <?php if (!empty($c['japanese_level'])): ?>
                  <span class="inline-block px-2 py-0.5 rounded bg-amber-50 text-amber-800 text-[10px] font-semibold">Trình độ: <?php echo htmlspecialchars($c['japanese_level']); ?></span>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4 max-w-xs">
                <p class="text-slate-600 line-clamp-2" title="<?php echo htmlspecialchars($c['message']); ?>">
                  <?php echo htmlspecialchars($c['message'] ?: '—'); ?>
                </p>
                <?php if (!empty($c['notes'])): ?>
                  <p class="text-[10px] text-indigo-600 font-semibold mt-1">
                    <i class="bi bi-chat-left-text mr-0.5"></i>Ghi chú: <?php echo htmlspecialchars($c['notes']); ?>
                  </p>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="contact-status contact-status-<?php echo htmlspecialchars($c['status']); ?>">
                  <?php echo $badge['label']; ?>
                </span>
              </td>
              <td class="py-3.5 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                <?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?>
              </td>
              <td class="py-3.5 px-4 text-right whitespace-nowrap">
                <button type="button" onclick="openContactModal(<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8'); ?>)" class="px-2.5 py-1.5 bg-slate-100 hover:bg-primary hover:text-white rounded-lg text-slate-700 transition-colors font-semibold mr-1" title="Cập nhật trạng thái">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <?php if (isAdmin()): ?>
                  <a href="/admin/contacts/delete/<?php echo $c['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?');" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg text-red-600 transition-colors font-semibold" title="Xóa">
                    <i class="bi bi-trash"></i>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination Controls -->
  <?php if ($total_pages > 1): ?>
    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
      <span>Trang <?php echo $page; ?> / <?php echo $total_pages; ?></span>
      <div class="flex gap-1">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="/admin/contacts?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1.5 rounded-lg border <?php echo $i === $page ? 'bg-primary text-white border-primary font-bold' : 'border-slate-200 hover:bg-slate-50'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Update Contact Status -->
<div id="contactModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
      <h3 class="text-base font-bold text-slate-800 font-display">Cập nhật Trạng thái Tư vấn</h3>
      <button type="button" onclick="closeContactModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" action="/admin/contacts/update-status" class="space-y-4">
      <?php echo csrfField(); ?>
      <input type="hidden" name="contact_id" id="modalContactId">

      <div>
        <p class="text-xs text-slate-500 mb-1">Học viên: <strong id="modalContactName" class="text-slate-800"></strong></p>
        <p class="text-xs text-slate-500 mb-3">SĐT: <strong id="modalContactPhone" class="text-slate-800"></strong></p>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Trạng thái xử lý</label>
        <select name="status" id="modalContactStatus" class="w-full px-3 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
          <option value="new">Mới nhận</option>
          <option value="read">Đã đọc</option>
          <option value="replied">Đã liên hệ tư vấn</option>
          <option value="processing">Đang thụ lý hồ sơ</option>
          <option value="completed">Đã hoàn tất (Xuất cảnh / Nhập học)</option>
          <option value="archived">Lưu trữ / Hủy hồ sơ</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ghi chú nội bộ</label>
        <textarea name="notes" id="modalContactNotes" rows="3" placeholder="Ghi nhận kết quả cuộc gọi, nhu cầu của học viên..." class="w-full px-3 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none resize-none"></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeContactModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Đóng</button>
        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-slate-800 transition-colors">Lưu cập nhật</button>
      </div>
    </form>
  </div>
</div>

<script>
function openContactModal(contact) {
  document.getElementById('modalContactId').value = contact.id;
  document.getElementById('modalContactName').textContent = contact.name;
  document.getElementById('modalContactPhone').textContent = contact.phone;
  document.getElementById('modalContactStatus').value = contact.status;
  document.getElementById('modalContactNotes').value = contact.notes || '';
  const modal = document.getElementById('contactModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeContactModal() {
  const modal = document.getElementById('contactModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
</script>
</div>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
