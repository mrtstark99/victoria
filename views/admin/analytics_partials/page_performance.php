<?php
/**
 * Detailed Page Search Performance (GSC / GA4) Table Partial
 */
?>
<!-- Page Performance GSC/GA4 Table -->
<div class="card" style="margin-bottom: 2rem; padding: 0; overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
        <h3 class="card-title" style="margin: 0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Hiệu suất tìm kiếm chi tiết từng bài viết (GSC / GA4)</span>
        </h3>
    </div>
    <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="min-width: 260px;">Bài viết (URL)</th>
                    <th>Clicks Search</th>
                    <th>Hiển thị Search</th>
                    <th>CTR Search</th>
                    <th>Vị trí GSC</th>
                    <th>Sessions GA4</th>
                    <th>Tỷ lệ tương tác</th>
                    <th>Leads</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($page_perf)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--muted-foreground); padding: 3rem 1.5rem;">
                            Chưa có dữ liệu hiệu suất trang.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($page_perf as $perf): ?>
                        <tr>
                            <td>
                                <strong style="display: block; color: var(--foreground); font-size: 0.875rem; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($perf['title']); ?>">
                                    <?php echo htmlspecialchars($perf['title']); ?>
                                </strong>
                                <a href="<?php echo htmlspecialchars($perf['url']); ?>" target="_blank" style="color: var(--muted-foreground); font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($perf['url']); ?> ↗
                                </a>
                            </td>
                            <td><strong style="font-variant-numeric: tabular-nums;"><?php echo number_format($perf['gsc']['clicks'] ?? 0); ?></strong></td>
                            <td style="color: var(--muted-foreground);"><?php echo number_format($perf['gsc']['impressions'] ?? 0); ?></td>
                            <td><?php echo number_format(($perf['gsc']['ctr'] ?? 0) * 100, 1); ?>%</td>
                            <td><code style="font-weight: 700;">#<?php echo number_format($perf['gsc']['position'] ?? 0, 1); ?></code></td>
                            <td><strong style="font-variant-numeric: tabular-nums;"><?php echo number_format($perf['ga4']['organic_sessions'] ?? 0); ?></strong></td>
                            <td style="color: var(--muted-foreground);"><?php echo number_format(($perf['ga4']['engagement_rate'] ?? 0) * 100, 1); ?>%</td>
                            <td>
                                <strong style="color: var(--foreground);"><?php echo number_format($perf['ga4']['leads'] ?? 0); ?></strong>
                                <?php if (($perf['ga4']['leads'] ?? 0) > 0): ?>
                                    <span style="font-size: 0.7rem; color: var(--muted-foreground); display: block;">
                                        (<?php echo number_format(($perf['ga4']['conversion_rate'] ?? 0) * 100, 1); ?>% CR)
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
$perfPage = max(1, (int)($_GET['perf_page'] ?? 1));
$perfPerPage = 20;
$perfTotal = (int)($page_perf_total ?? count($page_perf));
$perfPages = max(1, (int)ceil($perfTotal / $perfPerPage));
?>
<?php if ($perfTotal > $perfPerPage): ?>
  <nav aria-label="Phân trang hiệu suất tìm kiếm" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:1rem 1.5rem;border-top:1px solid var(--border);">
    <span style="font-size:.8rem;color:var(--muted-foreground);">Trang <?= $perfPage ?> / <?= $perfPages ?> · <?= number_format($perfTotal) ?> bài viết</span>
    <div style="display:flex;gap:.5rem;">
      <?php if ($perfPage > 1): ?><a class="btn btn-secondary" href="?days=<?= (int)($_GET['days'] ?? 28) ?>&amp;perf_page=<?= $perfPage - 1 ?>">Trước</a><?php endif; ?>
      <?php if ($perfPage < $perfPages): ?><a class="btn btn-secondary" href="?days=<?= (int)($_GET['days'] ?? 28) ?>&amp;perf_page=<?= $perfPage + 1 ?>">Tiếp</a><?php endif; ?>
    </div>
  </nav>
<?php endif; ?>
</div>
