<?php
/**
 * Admin Dashboard - Overview Metrics Stats Grid
 * Unified with the design system of Analytics (local_overview.php)
 */
$organicSessions = (int)round($analytics['ga']['summary']['sessions'] ?? 0);
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
                <span>28 ngày qua (Google GA4)</span>
            </div>
        </div>
    </div>
</div>
