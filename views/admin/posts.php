<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 
$currentSearch = $filters['search'] ?? '';
$currentCat = $filters['category_id'] ?? '';
$currentStatus = $filters['status'] ?? '';
$hasFilters = !empty($currentSearch) || !empty($currentCat) || !empty($currentStatus);
?>

<!-- ==========================================================================
     1. Metric Statistics Cards for Posts
     ========================================================================== -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <!-- Stat 1: Total Posts -->
    <div class="stat-card">
        <div class="stat-icon-wrap sky">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Tổng bài viết</span>
            <div class="stat-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Stat 2: Published Posts -->
    <div class="stat-card">
        <div class="stat-icon-wrap emerald">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Đã xuất bản</span>
            <div class="stat-value"><?php echo number_format($stats['published'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Stat 3: Drafts & Pending -->
    <div class="stat-card">
        <div class="stat-icon-wrap amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Bản nháp / Chờ duyệt</span>
            <div class="stat-value"><?php echo number_format($stats['drafts'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Stat 4: Featured Posts -->
    <div class="stat-card">
        <div class="stat-icon-wrap indigo">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Bài nổi bật</span>
            <div class="stat-value"><?php echo number_format($stats['featured'] ?? 0); ?></div>
        </div>
    </div>

    <!-- Stat 5: Total Views -->
    <div class="stat-card">
        <div class="stat-icon-wrap rose">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Tổng lượt xem</span>
            <div class="stat-value"><?php echo number_format($stats['views'] ?? 0); ?></div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     2. Search, Filter & Action Toolbar
     ========================================================================== -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="/admin/posts" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; flex-grow: 1; max-width: 900px;">
            <!-- Keyword Search Input -->
            <div style="position: relative; flex-grow: 1; min-width: 220px;">
                <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--muted-foreground); pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($currentSearch); ?>" 
                    class="form-control" 
                    placeholder="Tìm theo tiêu đề, slug, tác giả..." 
                    style="padding-left: 2.35rem; font-size: 0.875rem;"
                >
            </div>

            <!-- Category Filter Dropdown -->
            <div style="min-width: 170px;">
                <select name="category_id" class="form-control" style="font-size: 0.875rem;">
                    <option value="">Tất cả chuyên mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $currentCat == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Filter Dropdown -->
            <div style="min-width: 150px;">
                <select name="status" class="form-control" style="font-size: 0.875rem;">
                    <option value="">All Statuses</option>
                    <option value="published" <?php echo $currentStatus === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $currentStatus === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="pending_review" <?php echo $currentStatus === 'pending_review' ? 'selected' : ''; ?>>Pending Review</option>
                    <option value="ai_draft" <?php echo $currentStatus === 'ai_draft' ? 'selected' : ''; ?>>AI Draft</option>
                </select>
            </div>

            <!-- Filter Button -->
            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.6rem 0.9rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>Lọc</span>
            </button>

            <?php if ($hasFilters): ?>
                <a href="/admin/posts" class="btn btn-outline btn-sm" title="Xóa bộ lọc" style="padding: 0.6rem 0.85rem; color: var(--muted-foreground);">
                    <span>Đặt lại</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Primary CTA: Create Post -->
        <a href="/admin/posts/create" class="btn" style="flex-shrink: 0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Viết bài mới</span>
        </a>
    </form>
</div>

<!-- ==========================================================================
     3. Posts Table with STT and Pure Title (No Slug) & English Status
     ========================================================================== -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">STT</th>
                <th style="min-width: 280px;">Tiêu đề bài viết</th>
                <th>Chuyên mục</th>
                <th>Tác giả</th>
                <th>Status</th>
                <th style="text-align: center;">Lượt xem</th>
                <th>Ngày cập nhật</th>
                <th style="text-align: right; min-width: 140px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <div class="empty-state-title">
                                <?php echo $hasFilters ? 'Không tìm thấy bài viết phù hợp' : 'Chưa có bài viết nào'; ?>
                            </div>
                            <div class="empty-state-desc">
                                <?php echo $hasFilters ? 'Hãy thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.' : 'Bắt đầu tạo bài viết đầu tiên để chia sẻ nội dung hoặc cho phép AI Agent tạo bản nháp.'; ?>
                            </div>
                            <?php if ($hasFilters): ?>
                                <a href="/admin/posts" class="btn btn-secondary btn-sm">Xóa bộ lọc</a>
                            <?php else: ?>
                                <a href="/admin/posts/create" class="btn btn-sm">Viết bài viết đầu tiên</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $startIndex = ($page - 1) * $per_page;
                foreach ($posts as $idx => $post): 
                    $stt = $startIndex + $idx + 1;
                    $statusBadges = [
                        'published' => 'badge-published',
                        'draft' => 'badge-draft',
                        'pending_review' => 'badge-pending',
                        'ai_draft' => 'badge-ai'
                    ];
                    $badgeClass = $statusBadges[$post['status']] ?? 'badge-draft';
                    
                    $statusLabels = [
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'pending_review' => 'Pending Review',
                        'ai_draft' => 'AI Draft',
                        'archived' => 'Archived'
                    ];
                    $statusText = $statusLabels[$post['status']] ?? ucfirst($post['status']);
                ?>
                    <tr>
                        <!-- Column 1: STT -->
                        <td style="text-align: center; color: var(--muted-foreground); font-weight: 600; font-size: 0.85rem;">
                            <?php echo $stt; ?>
                        </td>

                        <!-- Column 2: Title Only (No slug) -->
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                                <a href="/admin/posts/edit/<?php echo $post['id']; ?>" style="font-weight: 700; color: var(--foreground); font-size: 0.925rem; line-height: 1.35; text-decoration: none;">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                                <?php if (!empty($post['featured'])): ?>
                                    <span style="font-size: 0.65rem; background: oklch(0.92 0.08 80 / 0.2); color: oklch(0.55 0.18 80); border: 1px solid oklch(0.85 0.1 80 / 0.3); padding: 0.1rem 0.45rem; border-radius: 9999px; font-weight: 700; text-transform: uppercase;">
                                        Featured
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Column 3: Category -->
                        <td>
                            <span class="badge-pill-tag">
                                <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                        </td>

                        <!-- Column 4: Author -->
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.85rem;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700; flex-shrink: 0;">
                                    <?php echo strtoupper(mb_substr($post['author_name'] ?? 'A', 0, 1, 'UTF-8')); ?>
                                </div>
                                <span style="color: var(--foreground); font-weight: 500;"><?php echo htmlspecialchars($post['author_name'] ?? '—'); ?></span>
                            </div>
                        </td>

                        <!-- Column 5: Status (In English) -->
                        <td>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </td>

                        <!-- Column 6: Views -->
                        <td style="text-align: center;">
                            <span style="font-weight: 700; font-size: 0.875rem; color: var(--foreground);">
                                <?php echo number_format($post['views']); ?>
                            </span>
                        </td>

                        <!-- Column 7: Date -->
                        <td style="font-size: 0.8rem; color: var(--muted-foreground); white-space: nowrap;">
                            <?php echo formatDate($post['updated_at'] ?: $post['created_at'], 'd/m/Y H:i'); ?>
                        </td>

                        <!-- Column 8: Actions -->
                        <td style="text-align: right;">
                            <div class="table-actions-cell">
                                <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" target="_blank" class="table-action-btn" title="Xem bài viết trên web">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                </a>
                                <a href="/admin/posts/edit/<?php echo $post['id']; ?>" class="table-action-btn" title="Chỉnh sửa bài viết">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/admin/posts/delete/<?php echo $post['id']; ?>" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này?')">
                                    <?php echo csrfField(); ?>
                                    <button type="submit" class="table-action-btn destructive" title="Xóa bài viết" style="background: none; border: none; cursor: pointer; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
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

<!-- ==========================================================================
     4. Pagination with Query Params Retention
     ========================================================================== -->
<?php
$totalPages = (int)ceil($total / $per_page);
if ($totalPages > 1): 
    $queryParams = $_GET;
?>
    <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <span style="font-size: 0.85rem; color: var(--muted-foreground);">
            Hiển thị <strong><?php echo count($posts); ?></strong> / <strong><?php echo number_format($total); ?></strong> bài viết
        </span>

        <nav style="display: flex; gap: 0.35rem; align-items: center; background: var(--card); border: 1px solid var(--border); padding: 0.35rem; border-radius: 9999px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): 
                $queryParams['page'] = $i;
                $pageUrl = '/admin/posts?' . http_build_query($queryParams);
            ?>
                <a href="<?php echo htmlspecialchars($pageUrl); ?>" class="btn <?php echo $i === $page ? '' : 'btn-secondary'; ?>" style="width: 2.25rem; height: 2.25rem; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.85rem;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
<?php endif; ?>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
