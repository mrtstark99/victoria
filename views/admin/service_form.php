<?php
/**
 * @file views/admin/service_form.php
 * @description Administration form for creating or editing study abroad services.
 *
 * Layer:
 * - Presentation / Admin View
 *
 * Responsibilities:
 * - Render input form for service parameters (title, slug, pricing, order, description, content).
 * - Support both creation and update workflows.
 *
 * Security:
 * - Anti-CSRF token verification on form post.
 * - Entity-encoded values in form inputs.
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

$isEdit = !empty($service['id']);
$formAction = $isEdit ? '/admin/services/update/' . $service['id'] : '/admin/services/store';
$packagesValue = json_encode($service['packages'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<div class="admin-data-page admin-service-form">
<div class="admin-page-header flex items-center justify-between gap-4 mb-6">
  <div>
    <a href="/admin/services" class="inline-flex items-center gap-1 text-xs text-primary font-bold hover:underline mb-1">
      <i class="bi bi-arrow-left"></i> Quay lại danh sách dịch vụ
    </a>
  </div>
</div>

<div class="card service-editor-card">
  <form method="POST" action="<?php echo $formAction; ?>" class="space-y-6">
    <?php echo csrfField(); ?>

    <div class="service-form-grid columns-2">
      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Tên chương trình <span class="text-red-500">*</span></label>
        <input type="text" name="title" required value="<?php echo htmlspecialchars($service['title'] ?? ''); ?>" placeholder="Ví dụ: Du học Trường Nhật ngữ" class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Tên ngắn / Nhóm chương trình</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($service['name'] ?? ''); ?>" placeholder="Ví dụ: Trường Nhật ngữ" class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
      </div>
    </div>

    <div class="service-form-grid columns-2">
      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Slug tiếng Anh <span class="text-red-500">*</span></label>
        <input type="text" name="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?php echo htmlspecialchars($service['slug'] ?? ''); ?>" placeholder="japanese-language-school-program" class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none font-mono">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Icon Bootstrap (bi-*)</label>
        <input type="text" name="icon" value="<?php echo htmlspecialchars($service['icon'] ?? 'bi-briefcase'); ?>" placeholder="bi-translate, bi-briefcase..." class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none font-mono">
      </div>
    </div>

    <div class="service-form-grid columns-2">
      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Thứ tự hiển thị</label>
        <input type="number" name="display_order" value="<?php echo (int)($service['display_order'] ?? 0); ?>" class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700 uppercase">Trạng thái</label>
        <select name="status" class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none">
          <option value="active" <?php echo ($service['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Hoạt động (Hiển thị)</option>
          <option value="inactive" <?php echo ($service['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Ẩn</option>
        </select>
      </div>
    </div>

    <div class="space-y-1.5">
      <label class="block text-xs font-bold text-slate-700 uppercase">Mô tả ngắn gọn</label>
      <textarea name="description" rows="3" placeholder="Mô tả ngắn cho thẻ chương trình trên /services..." class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none resize-none"><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
    </div>

    <div class="space-y-1.5">
      <label class="block text-xs font-bold text-slate-700 uppercase">Nội dung giới thiệu chương trình (HTML)</label>
      <textarea name="content" rows="10" placeholder="Chi tiết quyền lợi, lộ trình đào tạo, điều kiện ứng tuyển..." class="w-full px-4 py-2.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:border-primary outline-none font-mono"><?php echo htmlspecialchars($service['content'] ?? ''); ?></textarea>
    </div>

    <div class="space-y-1.5">
      <label class="block text-xs font-bold text-slate-700 uppercase">Các gói hồ sơ (JSON)</label>
      <textarea name="packages_json" rows="14" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-xs focus:border-primary focus:bg-white focus:outline-none"><?php echo htmlspecialchars($packagesValue ?: '[]', ENT_QUOTES, 'UTF-8'); ?></textarea>
      <p class="text-xs leading-relaxed text-slate-500">Mỗi gói gồm <code>name</code>, <code>slug</code> tiếng Anh (ví dụ <code>standard</code>), <code>description</code>, <code>price</code>, <code>features</code> (danh sách), và tùy chọn <code>featured</code>. Slug chương trình cũng phải dùng từ tiếng Anh, chữ thường và dấu gạch nối. Tối đa 12 gói cho mỗi chương trình.</p>
    </div>

    <div class="service-form-actions">
      <a href="/admin/services" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
        Hủy bỏ
      </a>
      <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-primary hover:bg-slate-800 transition-colors shadow-sm">
        <?php echo $isEdit ? 'Lưu thay đổi' : 'Tạo dịch vụ'; ?>
      </button>
    </div>
  </form>
</div>
</div>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
