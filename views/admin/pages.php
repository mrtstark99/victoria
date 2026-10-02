<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<div class="admin-page-container">
    <div class="admin-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/navigation" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.45rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                <span>Cấu hình Menu</span>
            </a>
            <a href="/admin/pages/create" class="btn btn-primary-action" style="display: inline-flex; align-items: center; gap: 0.45rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Tạo trang mới</span>
            </a>
        </div>
    </div>

    <!-- Filters and Search Bar -->
    <div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="/admin/pages" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex-grow: 1; min-width: 220px; position: relative;">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                    class="form-control" 
                    placeholder="Tìm kiếm theo tiêu đề hoặc slug..."
                    style="padding-left: 2.25rem;"
                >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>

            <div style="min-width: 160px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="published" <?php echo ($filters['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                    <option value="draft" <?php echo ($filters['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                    <option value="ai_draft" <?php echo ($filters['status'] ?? '') === 'ai_draft' ? 'selected' : ''; ?>>Bản nháp AI</option>
                    <option value="pending_review" <?php echo ($filters['status'] ?? '') === 'pending_review' ? 'selected' : ''; ?>>Chờ duyệt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Lọc</button>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                <a href="/admin/pages" class="btn btn-secondary" style="color: var(--destructive); padding: 0.5rem 0.75rem;" title="Xóa bộ lọc">✕</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Pages Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: var(--secondary); border-bottom: 1px solid var(--border); font-size: 0.8rem; color: var(--muted-foreground); text-transform: uppercase;">
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700;">Tiêu đề &amp; Đường dẫn</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Mẫu giao diện</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Trạng thái</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Lượt xem</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Ngày tạo</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="6" style="padding: 3rem 1rem; text-align: center; color: var(--muted-foreground);">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 0.75rem auto; display: block; opacity: 0.6;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <div>Chưa có trang nào phù hợp với bộ lọc.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border); transition: background 0.15s ease;" onmouseover="this.style.background='var(--secondary)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.2rem;">
                                        <a href="/admin/pages/edit/<?php echo $p['id']; ?>" style="color: var(--foreground); text-decoration: none;">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 0.78rem; color: var(--muted-foreground); display: flex; align-items: center; gap: 0.4rem;">
                                        <span>/page/<?php echo htmlspecialchars($p['slug']); ?></span>
                                        <?php if ($p['status'] === 'published'): ?>
                                            <a href="/page/<?php echo htmlspecialchars($p['slug']); ?>" target="_blank" title="Xem trang trực tiếp" style="color: var(--primary);">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-size: 0.85rem;">
                                    <span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 6px; background: var(--secondary); border: 1px solid var(--border); font-size: 0.75rem; font-weight: 600;">
                                        <?php 
                                        $tplMap = ['default' => 'Mặc định', 'fullwidth' => 'Toàn màn hình', 'contact' => 'Trang Liên hệ', 'landing' => 'Landing Page'];
                                        echo htmlspecialchars($tplMap[$p['template']] ?? $p['template']); 
                                        ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php if ($p['status'] === 'published'): ?>
                                        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #16a34a; font-weight: 700; border: 1px solid rgba(34, 197, 94, 0.3);">Đã xuất bản</span>
                                    <?php elseif ($p['status'] === 'ai_draft'): ?>
                                        <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #9333ea; font-weight: 700; border: 1px solid rgba(168, 85, 247, 0.3);">🤖 Bản nháp AI</span>
                                    <?php elseif ($p['status'] === 'pending_review'): ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #d97706; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.3);">Chờ duyệt</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--secondary); color: var(--muted-foreground); border: 1px solid var(--border);">Bản nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; font-size: 0.85rem; color: var(--foreground); font-weight: 600;">
                                    <?php echo number_format($p['views']); ?>
                                </td>
                                <td style="padding: 1rem; font-size: 0.8rem; color: var(--muted-foreground);">
                                    <?php echo formatDate($p['created_at'], 'd/m/Y H:i'); ?>
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                                        <a href="/admin/pages/edit/<?php echo $p['id']; ?>" class="btn btn-secondary btn-xs" title="Chỉnh sửa">
                                            Sửa
                                        </a>
                                        <form method="POST" action="/admin/pages/delete/<?php echo $p['id']; ?>" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trang này không?');" style="display: inline;">
                                            <?php echo csrfField(); ?>
                                            <button type="submit" class="btn btn-secondary btn-xs" style="color: var(--destructive);" title="Xóa trang">
                                                Xóa
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

        <!-- Pagination -->
        <?php if ($total > $per_page): ?>
            <div style="padding: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.825rem; color: var(--muted-foreground);">
                    Trang <?php echo $page; ?> / <?php echo ceil($total / $per_page); ?>
                </span>
                <div style="display: flex; gap: 0.35rem;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : ''; ?>" class="btn btn-secondary btn-sm">Trước</a>
                    <?php endif; ?>
                    <?php if ($page * $per_page < $total): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : ''; ?>" class="btn btn-secondary btn-sm">Sau</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
