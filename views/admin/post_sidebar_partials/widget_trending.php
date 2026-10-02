<?php
/**
 * Widget 1: Trending Reads Accordion Partial
 */
?>
<!-- Widget 1: Trending Reads -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Widget: Bài viết đọc nhiều nhất (Trending)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
            <label class="toggle-switch">
                <input type="checkbox" id="post_sidebar_trending_enabled" name="post_sidebar_trending_enabled" value="1" <?php echo ($settings['post_sidebar_trending_enabled'] ?? '1') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                <span class="toggle-slider"></span>
            </label>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div class="grid-layout columns-2" style="margin-bottom: 0;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_trending_title">Tiêu đề Widget</label>
                <input 
                    type="text" 
                    id="post_sidebar_trending_title" 
                    name="post_sidebar_trending_title" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($settings['post_sidebar_trending_title'] ?? 'Đọc nhiều nhất'); ?>" 
                    placeholder="Đọc nhiều nhất"
                    oninput="updateLiveSidebarPreview()"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="post_sidebar_trending_limit">Số lượng bài hiển thị</label>
                <select id="post_sidebar_trending_limit" name="post_sidebar_trending_limit" class="form-control" onchange="updateLiveSidebarPreview()">
                    <option value="3" <?php echo ($settings['post_sidebar_trending_limit'] ?? '4') == '3' ? 'selected' : ''; ?>>Top 3 bài</option>
                    <option value="4" <?php echo ($settings['post_sidebar_trending_limit'] ?? '4') == '4' ? 'selected' : ''; ?>>Top 4 bài</option>
                    <option value="5" <?php echo ($settings['post_sidebar_trending_limit'] ?? '4') == '5' ? 'selected' : ''; ?>>Top 5 bài</option>
                    <option value="8" <?php echo ($settings['post_sidebar_trending_limit'] ?? '4') == '8' ? 'selected' : ''; ?>>Top 8 bài</option>
                </select>
            </div>
        </div>
    </div>
</div>
