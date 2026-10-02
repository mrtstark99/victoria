<?php
/**
 * SEO Topic Clusters Partial
 */
?>
<div style="margin-bottom: 3rem;">
    <!-- Section Header Toolbar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div class="stat-icon-wrap sky" style="width: 36px; height: 36px; border-radius: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><circle cx="12" cy="4"/>
                </svg>
            </div>
            <div>
                <h2 style="font-weight: 800; font-size: 1.2rem; letter-spacing: -0.02em; margin: 0;">
                    Cụm chủ đề nội dung (Topic Clusters)
                </h2>
                <p style="font-size: 0.8rem; color: var(--muted-foreground); margin: 0.15rem 0 0;">
                    Các bài viết trụ cột (Pillar Content) và nhóm chủ đề liên kết nội bộ
                </p>
            </div>
        </div>
        
        <!-- Toggle Button to Open Form -->
        <button type="button" class="btn btn-sm" onclick="toggleClusterForm()" id="btnToggleCluster">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Thêm Topic Cluster</span>
        </button>
    </div>

    <!-- Hidden Collapsible Cluster Form Panel -->
    <div id="clusterFormPanel" class="card" style="display: none; margin-bottom: 1.5rem; border: 1px solid var(--foreground); animation: slideDown 0.25s ease;">
        <div class="card-header" style="margin-bottom: 1rem; padding-bottom: 0.75rem;">
            <h3 class="card-title" style="font-size: 1rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                <span>Tạo Topic Cluster mới</span>
            </h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleClusterForm()" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                ✕ Đóng
            </button>
        </div>

        <form method="POST" action="/admin/seo/cluster">
            <?php echo csrfField(); ?>
            <div class="grid-layout columns-2" style="margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="planning_month">Tháng lập kế hoạch <span style="color:var(--destructive)">*</span></label>
                    <input 
                        type="text" 
                        id="planning_month" 
                        name="planning_month" 
                        class="form-control" 
                        required 
                        value="<?php echo date('Y-m'); ?>" 
                        placeholder="YYYY-MM (ví dụ: <?php echo date('Y-m'); ?>)"
                    >
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="cluster_name">Tên cụm chủ đề <span style="color:var(--destructive)">*</span></label>
                    <input type="text" id="cluster_name" name="name" class="form-control" required placeholder="Ví dụ: Tối ưu hiệu năng Web">
                </div>
            </div>

            <div class="grid-layout columns-2" style="margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="pillar_title">Tiêu đề bài viết Pillar <span style="color:var(--destructive)">*</span></label>
                    <input type="text" id="pillar_title" name="pillar_title" class="form-control" required placeholder="Hướng dẫn toàn tập tối ưu Web 2026...">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="pillar_url">URL Pillar bài viết</label>
                    <input type="text" id="pillar_url" name="pillar_url" class="form-control" placeholder="/blog/huong-dan-toi-uu-web">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.65rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="toggleClusterForm()">Hủy</button>
                <button type="submit" class="btn">Lưu Topic Cluster</button>
            </div>
        </form>
    </div>

    <!-- Clusters Table -->
    <div class="card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th>Cụm chủ đề</th>
                        <th>Bài viết Pillar</th>
                        <th style="text-align: right;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clusters)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state-box" style="padding: 2.5rem 1rem;">
                                    <div class="empty-state-title" style="font-size: 0.95rem;">Chưa có Topic Cluster nào</div>
                                    <div class="empty-state-desc" style="font-size: 0.8rem; margin-bottom: 1rem;">Lập các cụm chủ đề để liên kết internal links hiệu quả và làm khung kiến trúc bài viết.</div>
                                    <button type="button" class="btn btn-sm" onclick="toggleClusterForm()">Thêm Topic Cluster đầu tiên</button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clusters as $clus): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($clus['planning_month']); ?></code></td>
                                <td><strong style="color: var(--foreground); font-size: 0.9rem;"><?php echo htmlspecialchars($clus['name']); ?></strong></td>
                                <td>
                                    <?php if ($clus['pillar_url']): ?>
                                        <a href="<?php echo htmlspecialchars($clus['pillar_url']); ?>" style="text-decoration: underline; font-weight: 600; color: var(--foreground);" target="_blank">
                                            <?php echo htmlspecialchars($clus['pillar_title']); ?> ↗
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--muted-foreground);"><?php echo htmlspecialchars($clus['pillar_title']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;"><span class="badge badge-published"><?php echo htmlspecialchars($clus['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
