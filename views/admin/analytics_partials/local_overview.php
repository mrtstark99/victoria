<?php
/**
 * Overview Metrics Stats Grid & Daily Views Trend Chart Partial
 */
?>
<!-- Overview Metrics Stats Grid -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <!-- Stat 1: Posts -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Tổng bài viết</span>
            <div class="stat-icon-wrap sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($total_posts ?? 0); ?></div>
            <div class="stat-subtext" style="margin-top: 0.35rem;">
                Công khai: <strong><?php echo number_format($published_posts ?? 0); ?></strong> / Bản nháp: <strong><?php echo number_format(($total_posts ?? 0) - ($published_posts ?? 0)); ?></strong>
            </div>
        </div>
    </div>

    <!-- Stat 2: Categories -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Chuyên mục</span>
            <div class="stat-icon-wrap amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($total_cats ?? 0); ?></div>
            <div class="stat-subtext" style="margin-top: 0.35rem;">
                <span>Phân loại nội dung hoạt động</span>
            </div>
        </div>
    </div>

    <!-- Stat 3: Views -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Tổng lượt xem</span>
            <div class="stat-icon-wrap emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($total_views ?? 0); ?></div>
            <div class="stat-subtext" style="margin-top: 0.35rem;">
                <span>Lượt đọc tích lũy toàn bộ blog</span>
            </div>
        </div>
    </div>

    <!-- Stat 4: Organic Traffic -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Organic Sessions</span>
            <div class="stat-icon-wrap indigo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($organicSessions); ?></div>
            <div class="stat-subtext" style="margin-top: 0.35rem;">
                <span><?php echo $days; ?> ngày qua (Google GA4)</span>
            </div>
        </div>
    </div>
</div>

<!-- Daily Views Trend Chart Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-header-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            <h3 class="card-title">Biểu đồ xu hướng lượt xem thực tế (<?php echo $days; ?> ngày qua)</h3>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <select class="form-control" style="width: auto; padding: 0.35rem 2.25rem 0.35rem 0.75rem; font-size: 0.85rem;" onchange="location.href='/admin/analytics?days='+this.value">
                <?php foreach ([7, 28, 90] as $period): ?>
                    <option value="<?php echo $period; ?>" <?php echo $days === $period ? 'selected' : ''; ?>><?php echo $period; ?> ngày qua</option>
                <?php endforeach; ?>
            </select>
            <a href="/admin/analytics?days=<?php echo $days; ?>&refresh=1" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                </svg>
                <span>Làm mới</span>
            </a>
        </div>
    </div>
    <div style="position: relative; height: 350px; width: 100%;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<!-- Section: Local Analytics -->
<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="stat-icon-wrap emerald" style="width: 32px; height: 32px; border-radius: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <h2 style="font-weight: 800; font-size: 1.15rem; letter-spacing: -0.02em; margin: 0;">Lượt xem Thực tế (Local Analytics)</h2>
        </div>
        <span class="badge-pill-tag">Thời gian thực</span>
    </div>

    <!-- Local Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-label">Tổng lượt xem</span>
                <div class="stat-icon-wrap sky">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
            </div>
            <div>
                <div class="stat-value"><?php echo number_format($real_views); ?></div>
                <div class="stat-subtext" style="margin-top: 0.35rem;">Toàn bộ lượt xem các trang</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-label">Lượt xem hôm nay</span>
                <div class="stat-icon-wrap emerald">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                </div>
            </div>
            <div>
                <div class="stat-value"><?php echo number_format($views_today); ?></div>
                <div class="stat-subtext" style="margin-top: 0.35rem;">Cập nhật tự động trực tiếp</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-label">Thiết bị di động</span>
                <div class="stat-icon-wrap amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
            </div>
            <div>
                <div class="stat-value"><?php echo $real_views > 0 ? number_format(($mobile_views / $real_views) * 100, 1) : 0; ?>%</div>
                <div class="stat-subtext" style="margin-top: 0.35rem;">Tổng lượt: <strong><?php echo number_format($mobile_views); ?></strong></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-label">Thiết bị máy tính</span>
                <div class="stat-icon-wrap indigo">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
            </div>
            <div>
                <div class="stat-value"><?php echo $real_views > 0 ? number_format(($desktop_views / $real_views) * 100, 1) : 0; ?>%</div>
                <div class="stat-subtext" style="margin-top: 0.35rem;">Tổng lượt: <strong><?php echo number_format($desktop_views); ?></strong></div>
            </div>
        </div>
    </div>
</div>
