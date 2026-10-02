<?php
/**
 * Multiple Custom HTML Widgets Manager Partial
 */
?>
<!-- Section 3: Multiple Custom HTML Widgets Manager -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; margin-bottom: -0.25rem;">
    <h4 style="font-size: 1rem; font-weight: 800; margin: 0; color: var(--foreground); display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
        </svg>
        <span>Các Widget HTML Tự Do (Custom HTML)</span>
    </h4>
    <button type="button" class="btn btn-secondary btn-sm" onclick="addCustomHtmlWidget()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Thêm Widget HTML mới</span>
    </button>
</div>

<div id="customHtmlWidgetsContainer" style="display: flex; flex-direction: column; gap: 1rem;">
    <?php if (empty($customHtmlWidgets)): ?>
        <div id="noCustomHtmlPlaceholder" style="padding: 1.5rem; background: var(--card); border: 1px dashed var(--border); border-radius: var(--radius); text-align: center; color: var(--muted-foreground); font-size: 0.85rem;">
            Chưa có Widget HTML tự do nào. Nhấn <strong>"Thêm Widget HTML mới"</strong> để chèn mã HTML, iframe, video, bản đồ hoặc banner tùy biến.
        </div>
    <?php else: ?>
        <?php foreach ($customHtmlWidgets as $idx => $hw): 
            $hwId = htmlspecialchars($hw['id'] ?? ('html_' . ($idx + 1)));
            $hwTitle = htmlspecialchars($hw['title'] ?? 'Tiện ích tùy chỉnh');
            $hwContent = htmlspecialchars($hw['content'] ?? '');
            $hwEn = ($hw['enabled'] ?? '1') === '1';
        ?>
            <div class="card accordion-card custom-html-widget-block" data-widget-id="<?php echo $hwId; ?>" style="margin-bottom: 0;">
                <input type="hidden" class="hw-input-id" name="custom_html_widgets[<?php echo $idx; ?>][id]" value="<?php echo $hwId; ?>">
                
                <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        <h3 class="card-title widget-header-title-display" style="margin: 0; font-size: 0.95rem;">Widget HTML: <?php echo $hwTitle ?: 'Tùy chỉnh'; ?></h3>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
                        <label class="toggle-switch">
                            <input type="checkbox" class="hw-input-enabled" name="custom_html_widgets[<?php echo $idx; ?>][enabled]" value="1" <?php echo $hwEn ? 'checked' : ''; ?> onchange="updateLiveSidebarPreview()">
                            <span class="toggle-slider"></span>
                        </label>
                        <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.2rem 0.5rem; height: auto;" onclick="deleteCustomHtmlWidget(this, '<?php echo $hwId; ?>')" title="Xóa Widget này">Xóa</button>
                        <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Tiêu đề Widget (Để trống nếu không muốn hiển thị header)</label>
                        <input 
                            type="text" 
                            name="custom_html_widgets[<?php echo $idx; ?>][title]" 
                            class="form-control hw-input-title" 
                            value="<?php echo $hwTitle; ?>" 
                            placeholder="Tiện ích tùy chỉnh"
                            oninput="updateHtmlWidgetTitle(this, '<?php echo $hwId; ?>')"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Nội dung HTML / Embed / Script / CSS tự do</label>
                        <textarea 
                            name="custom_html_widgets[<?php echo $idx; ?>][content]" 
                            class="form-control hw-input-content" 
                            rows="5" 
                            placeholder="&lt;div class=&quot;my-box&quot;&gt;&lt;p&gt;Nội dung HTML...&lt;/p&gt;&lt;/div&gt;"
                            style="font-family: inherit; font-size: 0.85rem; line-height: 1.5;"
                            oninput="updateLiveSidebarPreview()"
                        ><?php echo $hwContent; ?></textarea>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
