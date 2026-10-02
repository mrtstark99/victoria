// Accordion Toggle function
function toggleAccordion(headerEl) {
    const card = headerEl.closest('.accordion-card');
    if (!card) return;
    const body = card.querySelector('.accordion-body');
    const chevron = card.querySelector('.chevron-icon');

    if (body.style.display === 'none' || !body.style.display) {
        body.style.display = 'block';
        if (chevron) chevron.style.transform = 'rotate(180deg)';
    } else {
        body.style.display = 'none';
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
}

// Toggle All Accordions
let allExpanded = false;
function toggleAllAccordions() {
    allExpanded = !allExpanded;
    const bodies = document.querySelectorAll('.accordion-body');
    const chevrons = document.querySelectorAll('.chevron-icon');
    const btnText = document.getElementById('toggleAllBtnText');

    bodies.forEach(b => b.style.display = allExpanded ? 'block' : 'none');
    chevrons.forEach(c => c.style.transform = allExpanded ? 'rotate(180deg)' : 'rotate(0deg)');
    if (btnText) btnText.innerText = allExpanded ? 'Thu gọn tất cả' : 'Mở rộng tất cả';
}

// Move Widget Order Up or Down
function moveWidgetOrder(widgetKey, direction) {
    const listContainer = document.getElementById('widgetOrderList');
    if (!listContainer) return;
    const items = Array.from(listContainer.querySelectorAll('.widget-order-item'));
    const index = items.findIndex(el => el.getAttribute('data-widget-key') === widgetKey);

    if (index === -1) return;
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= items.length) return;

    if (direction === -1) {
        listContainer.insertBefore(items[index], items[targetIndex]);
    } else {
        listContainer.insertBefore(items[targetIndex], items[index]);
    }

    syncWidgetOrder();
}

function syncWidgetOrder() {
    const listContainer = document.getElementById('widgetOrderList');
    if (!listContainer) return;
    const items = Array.from(listContainer.querySelectorAll('.widget-order-item'));
    const newOrder = items.map((el, i) => {
        const badge = el.querySelector('.order-number-badge');
        if (badge) badge.innerText = String(i + 1).padStart(2, '0');
        return el.getAttribute('data-widget-key');
    });

    const orderInput = document.getElementById('post_sidebar_widget_order');
    if (orderInput) orderInput.value = newOrder.join(',');

    // Reorder live preview simulator widgets
    const previewContainer = document.getElementById('sidebarLivePreviewList');
    if (previewContainer) {
        newOrder.forEach(key => {
            const pEl = previewContainer.querySelector(`[data-preview-key="${key}"]`);
            if (pEl) previewContainer.appendChild(pEl);
        });
    }

    if (typeof updateLiveSidebarPreview === 'function') {
        updateLiveSidebarPreview();
    }
}

// Add new Custom HTML Widget dynamically
let widgetIndexCounter = window.initialCustomHtmlCount ? window.initialCustomHtmlCount + 10 : 50;
function addCustomHtmlWidget() {
    const placeholder = document.getElementById('noCustomHtmlPlaceholder');
    if (placeholder) placeholder.style.display = 'none';

    const uniqueId = 'html_' + Date.now();
    const idx = widgetIndexCounter++;

    const newBlock = document.createElement('div');
    newBlock.className = 'card accordion-card custom-html-widget-block';
    newBlock.setAttribute('data-widget-id', uniqueId);
    newBlock.style.marginBottom = '0';
    newBlock.innerHTML = `
        <input type="hidden" class="hw-input-id" name="custom_html_widgets[${idx}][id]" value="${uniqueId}">
        <div class="accordion-header" onclick="toggleAccordion(this)" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 0.25rem 0;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                <h3 class="card-title widget-header-title-display" style="margin: 0; font-size: 0.95rem;">Widget HTML mới</h3>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;" onclick="event.stopPropagation()">
                <label class="toggle-switch">
                    <input type="checkbox" class="hw-input-enabled" name="custom_html_widgets[${idx}][enabled]" value="1" checked onchange="updateLiveSidebarPreview()">
                    <span class="toggle-slider"></span>
                </label>
                <button type="button" class="btn btn-destructive btn-sm" style="padding: 0.2rem 0.5rem; height: auto;" onclick="deleteCustomHtmlWidget(this, '${uniqueId}')">Xóa</button>
                <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s; transform: rotate(180deg);"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="accordion-body" style="display: block; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 0.75rem;">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Tiêu đề Widget (Để trống nếu không muốn hiển thị header)</label>
                <input 
                    type="text" 
                    name="custom_html_widgets[${idx}][title]" 
                    class="form-control hw-input-title" 
                    value="Widget HTML mới" 
                    placeholder="Tiện ích tùy chỉnh"
                    oninput="updateHtmlWidgetTitle(this, '${uniqueId}')"
                >
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Nội dung HTML / Embed / Script / CSS tự do</label>
                <textarea 
                    name="custom_html_widgets[${idx}][content]" 
                    class="form-control hw-input-content" 
                    rows="5" 
                    placeholder="&lt;div class=&quot;my-box&quot;&gt;&lt;p&gt;Nội dung HTML...&lt;/p&gt;&lt;/div&gt;"
                    style="font-family: inherit; font-size: 0.85rem; line-height: 1.5;"
                    oninput="updateLiveSidebarPreview()"
                ></textarea>
            </div>
        </div>
    `;

    document.getElementById('customHtmlWidgetsContainer').appendChild(newBlock);

    // Add to Widget Order list
    const orderList = document.getElementById('widgetOrderList');
    const newOrderItem = document.createElement('div');
    newOrderItem.className = 'widget-order-item';
    newOrderItem.setAttribute('data-widget-key', uniqueId);
    newOrderItem.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--secondary); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.5); font-size: 0.85rem;';
    newOrderItem.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span class="order-number-badge" style="font-weight: 800; color: var(--muted-foreground); font-size: 0.75rem; width: 20px; text-align: center;">00</span>
            <strong class="order-widget-label" style="color: var(--foreground); font-weight: 600;">Widget HTML mới</strong>
        </div>
        <div style="display: flex; align-items: center; gap: 0.35rem;">
            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; height: auto;" onclick="moveWidgetOrder('${uniqueId}', -1)">↑</button>
            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; height: auto;" onclick="moveWidgetOrder('${uniqueId}', 1)">↓</button>
        </div>
    `;
    orderList.appendChild(newOrderItem);

    syncWidgetOrder();
}

// Delete a Custom HTML widget
function deleteCustomHtmlWidget(btn, uniqueId) {
    if (!confirm('Bạn chắc chắn muốn xóa Widget HTML này?')) return;
    const block = btn.closest('.custom-html-widget-block');
    if (block) block.remove();

    const orderItem = document.querySelector(`.widget-order-item[data-widget-key="${uniqueId}"]`);
    if (orderItem) orderItem.remove();

    const previewItem = document.querySelector(`.preview-widget-box[data-preview-key="${uniqueId}"]`);
    if (previewItem) previewItem.remove();

    syncWidgetOrder();

    const remaining = document.querySelectorAll('.custom-html-widget-block').length;
    if (remaining === 0) {
        const placeholder = document.getElementById('noCustomHtmlPlaceholder');
        if (placeholder) placeholder.style.display = 'block';
    }
}

// Update Title in Header display and Order List and Live Preview
function updateHtmlWidgetTitle(input, uniqueId) {
    const titleVal = input.value.trim() || 'Tùy chỉnh';
    const card = input.closest('.custom-html-widget-block');
    if (card) {
        const titleDisplay = card.querySelector('.widget-header-title-display');
        if (titleDisplay) titleDisplay.innerText = 'Widget HTML: ' + titleVal;
    }

    const orderItem = document.querySelector(`.widget-order-item[data-widget-key="${uniqueId}"]`);
    if (orderItem) {
        const label = orderItem.querySelector('.order-widget-label');
        if (label) label.innerText = 'Widget HTML: ' + titleVal;
    }

    if (typeof updateLiveSidebarPreview === 'function') {
        updateLiveSidebarPreview();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    syncWidgetOrder();
});
