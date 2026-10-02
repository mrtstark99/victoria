<?php
/**
 * Widget Ordering Manager Accordion Partial
 */
?>
<!-- Section 1: Widget Ordering Manager -->
<div class="card accordion-card" style="margin-bottom: 0;">
    <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/><polyline points="19 5 12 12 5 5"/>
            </svg>
            <h3 class="card-title" style="margin: 0; font-size: 0.95rem;">Sắp xếp thứ tự hiển thị Widget</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.75rem; color: var(--muted-foreground);">Dùng nút mũi tên ↑ ↓ để đổi vị trí</span>
            <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <div class="accordion-body" style="display: none; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
        <div id="widgetOrderList" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php 
            $orderIndex = 1;
            foreach ($currentOrder as $wKey): 
                $title = $allWidgetTitles[$wKey] ?? $wKey;
            ?>
                <div class="widget-order-item" data-widget-key="<?php echo $wKey; ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); font-size: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <span class="order-number-badge" style="font-weight: 800; color: var(--muted-foreground); font-size: 0.75rem; width: 20px; text-align: center;"><?php echo sprintf('%02d', $orderIndex++); ?></span>
                        <strong class="order-widget-label" style="color: var(--foreground); font-weight: 600;"><?php echo htmlspecialchars($title); ?></strong>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; height: auto;" onclick="moveWidgetOrder('<?php echo $wKey; ?>', -1)" title="Di chuyển lên">↑</button>
                        <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; height: auto;" onclick="moveWidgetOrder('<?php echo $wKey; ?>', 1)" title="Di chuyển xuống">↓</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
