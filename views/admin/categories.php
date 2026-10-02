<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<div class="grid-layout columns-1-2">
    <!-- Left Column: Create Form -->
    <div class="card" style="margin-bottom: 0; height: fit-content; position: sticky; top: 5rem;">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Thêm danh mục mới</span>
            </h3>
        </div>

        <form method="POST" action="/admin/categories/create">
            <?php echo csrfField(); ?>
            <div class="form-group">
                <label for="cat_name">Tên chuyên mục <span style="color:var(--destructive)">*</span></label>
                <input 
                    type="text" 
                    id="cat_name" 
                    name="name" 
                    class="form-control" 
                    required 
                    placeholder="Ví dụ: Công nghệ AI"
                    oninput="autoGenerateCategorySlug(this.value)"
                >
            </div>

            <div class="form-group">
                <label for="cat_slug">Slug (Đường dẫn tĩnh)</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 0.85rem; font-size: 0.85rem; color: var(--muted-foreground);">/category/</span>
                    <input 
                        type="text" 
                        id="cat_slug" 
                        name="slug" 
                        class="form-control" 
                        placeholder="cong-nghe-ai"
                        style="padding-left: 6rem; font-size: 0.875rem;"
                    >
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description">Mô tả ngắn</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Mô tả tóm tắt về chuyên mục này..."
                ></textarea>
            </div>

            <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 0.75rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Tạo chuyên mục</span>
            </button>
        </form>
    </div>

    <!-- Right Column: Category Table List -->
    <div class="card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title" style="margin: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <span>Danh sách chuyên mục (<?php echo count($categories); ?>)</span>
            </h3>
        </div>

        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tên chuyên mục</th>
                        <th>Đường dẫn tĩnh</th>
                        <th>Mô tả</th>
                        <th>Bài viết</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state-box">
                                    <div class="empty-state-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                        </svg>
                                    </div>
                                    <div class="empty-state-title">Chưa có chuyên mục nào</div>
                                    <div class="empty-state-desc">Hãy tạo chuyên mục đầu tiên bằng form bên trái để phân loại bài viết.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--foreground); font-size: 0.925rem;">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="/category/<?php echo htmlspecialchars($cat['slug']); ?>" target="_blank" style="font-size: 0.8rem; color: var(--muted-foreground); text-decoration: underline;">
                                        /category/<?php echo htmlspecialchars($cat['slug']); ?> ↗
                                    </a>
                                </td>
                                <td style="color: var(--muted-foreground); font-size: 0.85rem; max-width: 220px;">
                                    <?php echo htmlspecialchars($cat['description'] ?: '—'); ?>
                                </td>
                                <td>
                                    <span class="badge-pill-tag">
                                        <strong><?php echo number_format($cat['post_count']); ?></strong> bài
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="table-actions-cell">
                                        <a href="/category/<?php echo htmlspecialchars($cat['slug']); ?>" target="_blank" class="table-action-btn" title="Xem trên frontend">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                <polyline points="15 3 21 3 21 9"/>
                                                <line x1="10" y1="14" x2="21" y2="3"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="/admin/categories/delete/<?php echo $cat['id']; ?>" style="display: inline;" onsubmit="return confirm('Xóa chuyên mục này? Các bài viết thuộc chuyên mục sẽ bị chuyển về Chưa phân loại.')">
                                            <?php echo csrfField(); ?>
                                            <button type="submit" class="table-action-btn destructive" title="Xóa chuyên mục" style="background: none; border: none; cursor: pointer; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    </div>
</div>

<script>
    function autoGenerateCategorySlug(name) {
        if (!name) return;
        let slug = name.toLowerCase().trim();
        slug = slug.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        slug = slug.replace(/[đĐ]/g, 'd');
        slug = slug.replace(/[^a-z0-9\s-]/g, '');
        slug = slug.replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
        document.getElementById('cat_slug').value = slug;
    }
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
