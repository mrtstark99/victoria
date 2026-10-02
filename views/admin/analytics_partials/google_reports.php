<?php
/**
 * Google Analytics 4, Search Console & CRO Events Partial
 */
?>
<!-- Section: Google Performance Metrics -->
<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="stat-icon-wrap indigo" style="width: 32px; height: 32px; border-radius: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><circle cx="12" cy="12" r="4"/>
                </svg>
            </div>
            <h2 style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.02em; margin: 0;">Hiệu suất Kênh tìm kiếm (Google GA4 &amp; GSC)</h2>
        </div>
        <span class="badge-pill-tag">Google Data</span>
    </div>

    <!-- Google 8 Metric Cards Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
        <?php foreach ($metricCards as $card): ?>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-label"><?php echo htmlspecialchars($card[0]); ?></span>
                    <div class="stat-icon-wrap <?php echo htmlspecialchars($card[4]); ?>">
                        <?php echo $card[3]; ?>
                    </div>
                </div>
                <div>
                    <div class="stat-value"><?php echo htmlspecialchars($card[1]); ?></div>
                    <div class="stat-subtext" style="margin-top: 0.35rem; font-size: 0.725rem;"><?php echo htmlspecialchars($card[2]); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Referrers Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
            <span>Nguồn giới thiệu truy cập (Top Referrers)</span>
        </h3>
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
        <?php if (empty($referers)): ?>
            <p style="color: var(--muted-foreground); font-size: 0.875rem; margin: 0; padding: 1rem 0;">Chưa ghi nhận nguồn giới thiệu nào.</p>
        <?php else: ?>
            <?php foreach ($referers as $ref): ?>
                <?php 
                $refName = $ref['referer'] ?: 'Truy cập Trực tiếp (Direct / Search / Bookmark)'; 
                $refPercent = $real_views > 0 ? ($ref['count'] / $real_views) * 100 : 0;
                ?>
                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;">
                        <span style="color: var(--foreground); max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($refName); ?></span>
                        <span style="color: var(--muted-foreground);"><?php echo number_format($ref['count']); ?> lượt (<?php echo number_format($refPercent, 1); ?>%)</span>
                    </div>
                    <div style="width: 100%; height: 6px; background-color: var(--secondary); border-radius: 9999px; overflow: hidden;">
                        <div style="width: <?php echo $refPercent; ?>%; height: 100%; background-color: var(--foreground); border-radius: 9999px; transition: width 0.6s ease;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Layout grid with 2 columns: GSC Keywords & CRO Custom Events -->
<div class="grid-layout columns-2" style="margin-bottom: 2rem;">
    <!-- Top GSC Keywords -->
    <div class="card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
            <h3 class="card-title" style="margin: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Top từ khóa Organic (Google Search Console)</span>
            </h3>
        </div>
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Từ khóa</th>
                        <th>Clicks</th>
                        <th>Impressions</th>
                        <th>CTR</th>
                        <th>Vị trí TB</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keywords)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--muted-foreground); padding: 2.5rem 1rem;">Chưa có dữ liệu từ khóa.</td></tr>
                    <?php else: foreach ($keywords as $row): ?>
                        <tr>
                            <td><strong style="color: var(--foreground); font-size: 0.875rem;"><?php echo htmlspecialchars($row['keyword']); ?></strong></td>
                            <td><strong style="font-variant-numeric: tabular-nums;"><?php echo number_format($row['clicks']); ?></strong></td>
                            <td style="color: var(--muted-foreground);"><?php echo number_format($row['impressions']); ?></td>
                            <td><?php echo number_format($row['ctr'] * 100, 1); ?>%</td>
                            <td><code style="padding: 0.2rem 0.45rem; background: var(--secondary); border-radius: 4px; font-weight: 700;">#<?php echo number_format($row['position'], 1); ?></code></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom CRO Events (GA4) -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>Tối ưu chuyển đổi (Custom Events CRO)</span>
            </h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
            <?php foreach ($eventLabels as $eventName => $eventMeta): ?>
                <?php 
                $count = (int)($events[$eventName] ?? 0);
                $percent = $organicSessions > 0 ? ($count / $organicSessions) * 100 : 0;
                ?>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <span style="color: var(--muted-foreground);"><?php echo $eventMeta[1]; ?></span>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--foreground);"><?php echo htmlspecialchars($eventMeta[0]); ?></span>
                            <code style="font-size: 0.7rem; color: var(--muted-foreground);"><?php echo htmlspecialchars($eventName); ?></code>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.95rem; font-weight: 800; color: var(--foreground);"><?php echo number_format($count); ?></span>
                        <span style="font-size: 0.75rem; color: var(--muted-foreground); display: block;"><?php echo number_format($percent, 1); ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
