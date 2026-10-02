<?php
/**
 * SEO Target Keywords Partial
 */
?>
<div>
    <!-- Section Header Toolbar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div class="stat-icon-wrap amber" style="width: 36px; height: 36px; border-radius: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div>
                <h2 style="font-weight: 800; font-size: 1.2rem; letter-spacing: -0.02em; margin: 0;">
                    Từ khóa mục tiêu (Target Keywords)
                </h2>
                <p style="font-size: 0.8rem; color: var(--muted-foreground); margin: 0.15rem 0 0;">
                    Danh sách từ khóa SEO, ý định tìm kiếm (Intent) và thứ tự ưu tiên
                </p>
            </div>
        </div>

        <!-- Toggle Button to Open Keyword Form -->
        <button type="button" class="btn btn-sm" onclick="toggleKeywordForm()" id="btnToggleKeyword">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Thêm từ khóa</span>
        </button>
    </div>

    <!-- Hidden Collapsible Keyword Form Panel -->
    <div id="keywordFormPanel" class="card" style="display: none; margin-bottom: 1.5rem; border: 1px solid var(--foreground); animation: slideDown 0.25s ease;">
        <div class="card-header" style="margin-bottom: 1rem; padding-bottom: 0.75rem;">
            <h3 class="card-title" style="font-size: 1rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Thêm từ khóa mục tiêu mới</span>
            </h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleKeywordForm()" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                ✕ Đóng
            </button>
        </div>

        <form method="POST" action="/admin/seo/keyword">
            <?php echo csrfField(); ?>
            <div class="grid-layout columns-3" style="margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="kw_month">Tháng kế hoạch <span style="color:var(--destructive)">*</span></label>
                    <input 
                        type="text" 
                        id="kw_month" 
                        name="planning_month" 
                        class="form-control" 
                        required 
                        value="<?php echo date('Y-m'); ?>" 
                        placeholder="<?php echo date('Y-m'); ?>"
                    >
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="keyword">Từ khóa mục tiêu <span style="color:var(--destructive)">*</span></label>
                    <input type="text" id="keyword" name="keyword" class="form-control" required placeholder="Ví dụ: hoc lap trinh php 2026">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="cluster_id">Thuộc Topic Cluster</label>
                    <select id="cluster_id" name="cluster_id" class="form-control">
                        <option value="">-- Chọn cụm chủ đề --</option>
                        <?php foreach ($clusters as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['planning_month']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid-layout columns-2" style="margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="intent">Search Intent</label>
                    <select id="intent" name="intent" class="form-control">
                        <option value="informational">Informational (Thông tin)</option>
                        <option value="commercial">Commercial (Thương mại)</option>
                        <option value="transactional">Transactional (Giao dịch)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="priority">Mức độ ưu tiên</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="high">High (Ưu tiên cao)</option>
                        <option value="medium" selected>Medium (Trung bình)</option>
                        <option value="low">Low (Thấp)</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.65rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="toggleKeywordForm()">Hủy</button>
                <button type="submit" class="btn">Lưu từ khóa mục tiêu</button>
            </div>
        </form>
    </div>

    <!-- Keywords Table -->
    <div class="card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th>Từ khóa</th>
                        <th>Cluster</th>
                        <th>Intent</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keywords)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-box" style="padding: 3rem 1.5rem;">
                                    <div class="empty-state-title" style="font-size: 0.95rem;">Chưa có từ khóa nào</div>
                                    <div class="empty-state-desc" style="font-size: 0.8rem; margin-bottom: 1rem;">Thêm từ khóa mục tiêu để theo dõi và cung cấp gợi ý cho AI Agent.</div>
                                    <button type="button" class="btn btn-sm" onclick="toggleKeywordForm()">Thêm từ khóa đầu tiên</button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($keywords as $kw): ?>
                            <?php
                            $intentBadges = [
                                'informational' => 'badge-pending',
                                'commercial' => 'badge-published',
                                'transactional' => 'badge-ai'
                            ];
                            $iBadge = $intentBadges[$kw['intent']] ?? 'badge-draft';
                            ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($kw['planning_month']); ?></code></td>
                                <td><strong style="color: var(--foreground); font-size: 0.9rem;"><?php echo htmlspecialchars($kw['keyword']); ?></strong></td>
                                <td>
                                    <span class="badge-pill-tag" style="font-size: 0.75rem;">
                                        <?php echo htmlspecialchars($kw['cluster_name'] ?? '—'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $iBadge; ?>">
                                        <?php echo htmlspecialchars($kw['intent']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($kw['priority'] === 'high'): ?>
                                        <span style="font-weight: 700; font-size: 0.75rem; color: var(--destructive); text-transform: uppercase;">Cao</span>
                                    <?php elseif ($kw['priority'] === 'medium'): ?>
                                        <span style="font-weight: 600; font-size: 0.75rem; color: var(--foreground); text-transform: uppercase;">Vừa</span>
                                    <?php else: ?>
                                        <span style="font-weight: 500; font-size: 0.75rem; color: var(--muted-foreground); text-transform: uppercase;">Thấp</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-draft"><?php echo htmlspecialchars($kw['status']); ?></span></td>
                                <td style="text-align: right;">
                                    <form method="POST" action="/admin/seo/keyword/delete/<?php echo $kw['id']; ?>" style="display: inline;" onsubmit="return confirm('Xóa từ khóa này?')">
                                        <?php echo csrfField(); ?>
                                        <button type="submit" class="table-action-btn destructive" title="Xóa từ khóa" style="background: none; border: none; cursor: pointer; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
