<?php
/**
 * Widget 5: Categories Quick Links Accordion Partial
 */
?>
<!-- Widget 5: Categories Quick Links -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Widget: Chuyên mục đề xuất (Categories)</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
            <label class="toggle-switch">
                <input type="checkbox" id="post_sidebar_categories_enabled" name="post_sidebar_categories_enabled" value="1" <?php echo ($settings['post_sidebar_categories_enabled'] ?? '0') === '1' ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                <span class="toggle-slider"></span>
            </label>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="post_sidebar_categories_title">Tiêu đề Widget</label>
            <input 
                type="text" 
                id="post_sidebar_categories_title" 
                name="post_sidebar_categories_title" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['post_sidebar_categories_title'] ?? 'Chuyên mục đề xuất'); ?>" 
                placeholder="Chuyên mục đề xuất"
                oninput="updateLiveSidebarPreview()"
            >
        </div>
    </div>
</div>
