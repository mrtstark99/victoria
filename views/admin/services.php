<?php
/**
 * @file views/admin/services.php
 * @description Administration services list view.
 *
 * Layer:
 * - Presentation / Admin View
 *
 * Responsibilities:
 * - Render study abroad services table with search and pagination.
 * - Provide actions for creating, editing, and deleting services.
 *
 * Security:
 * - Entity-encoded outputs preventing XSS.
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
?>

<div class="admin-data-page">
<div class="admin-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
  <div>
    <a href="/admin/services/create" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
      <i class="bi bi-plus-lg"></i> Thêm Dịch Vụ Mới
    </a>
  </div>
</div>

<!-- Search Bar -->
<div class="card admin-data-toolbar">
  <form method="GET" action="/admin/services" class="flex items-center gap-3">
    <div class="flex-1">
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo tên hoặc mô tả dịch vụ..." class="w-full px-3.5 py-2 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
    </div>
    <button type="submit" class="px-4 py-2 bg-primary hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
      <i class="bi bi-search mr-1"></i>Tìm
    </button>
    <?php if ($search !== ''): ?>
      <a href="/admin/services" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
        Đặt lại
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Services Table -->
<div class="admin-table-container admin-data-table">
  <div class="overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider">
          <th class="py-3 px-4 w-12 text-center">Thứ tự</th>
          <th class="py-3 px-4">Tên chương trình</th>
          <th class="py-3 px-4">Đường dẫn (Slug)</th>
          <th class="py-3 px-4">Gói hồ sơ</th>
          <th class="py-3 px-4 text-center">Trạng thái</th>
          <th class="py-3 px-4 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 font-medium">
        <?php if (empty($services)): ?>
          <tr>
            <td colspan="6" class="py-12 text-center text-slate-400">
              <i class="bi bi-briefcase text-3xl block mb-2"></i>
              Chưa có chương trình nào trong hệ thống.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($services as $s): ?>
            <tr class="hover:bg-slate-50/80 transition-colors">
              <td class="py-3 px-4 text-center font-bold text-slate-500">
                <?php echo (int)$s['display_order']; ?>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm flex-shrink-0">
                    <i class="bi <?php echo htmlspecialchars($s['icon'] ?: 'bi-briefcase'); ?>"></i>
                  </div>
                  <div>
                    <a href="/admin/services/edit/<?php echo $s['id']; ?>" class="font-bold text-slate-800 text-sm hover:text-primary transition-colors block">
                      <?php echo htmlspecialchars($s['title']); ?>
                    </a>
                    <span class="text-[11px] text-slate-400"><?php echo htmlspecialchars($s['name']); ?></span>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                /services/<?php echo htmlspecialchars($s['slug']); ?>
              </td>
              <td class="py-3 px-4 font-bold text-slate-700">
                <?php echo count($s['packages'] ?? []); ?> gói
              </td>
              <td class="py-3 px-4 text-center">
                <?php if ($s['status'] === 'active'): ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Hoạt động</span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Ẩn</span>
                <?php endif; ?>
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <a href="/services/<?php echo htmlspecialchars($s['slug']); ?>" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 transition-colors font-semibold mr-1" title="Xem trên web">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="/admin/services/edit/<?php echo $s['id']; ?>" class="px-2.5 py-1.5 bg-primary hover:bg-slate-800 text-white rounded-lg transition-colors font-semibold mr-1" title="Chỉnh sửa">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php if (isAdmin()): ?>
                  <a href="/admin/services/delete/<?php echo $s['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?');" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg text-red-600 transition-colors font-semibold" title="Xóa">
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
          <a href="/admin/services?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1.5 rounded-lg border <?php echo $i === $page ? 'bg-primary text-white border-primary font-bold' : 'border-slate-200 hover:bg-slate-50'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
</div>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
