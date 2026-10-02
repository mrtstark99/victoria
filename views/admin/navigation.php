<?php include APP_ROOT . '/views/layouts/admin_header.php'; ?>

<?php
$navItems = $settings['nav_items'] ?? [];
$footerCol2 = $settings['footer_col2'] ?? [];
$footerCol3 = $settings['footer_col3'] ?? [];
$bottomLinks = $settings['bottom_links'] ?? [];
$publishedPages = $pages ?? [];
$categoriesList = $categories ?? [];
?>

<div class="admin-page-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <button type="submit" form="navForm" class="btn btn-primary-action" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.6rem 1.25rem; font-weight: 700;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span>Lưu toàn bộ thay đổi</span>
            </button>
        </div>
    </div>

    <!-- Quick Add Links Drawer / Helper Bar -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.5rem; background: var(--secondary); border: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span style="font-size: 0.85rem; font-weight: 600;">Thêm nhanh liên kết từ nội dung hiện có:</span>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <!-- Pages quick insert dropdown -->
                <select id="quickPageSelect" class="form-control" style="font-size: 0.825rem; padding: 0.35rem 0.65rem; width: auto;" onchange="quickAddPageToNav(this)">
                    <option value="">+ Chèn từ Trang tĩnh...</option>
                    <?php foreach ($publishedPages as $pg): ?>
                        <option value="<?php echo htmlspecialchars($pg['slug']); ?>" data-title="<?php echo htmlspecialchars($pg['title']); ?>">
                            📄 <?php echo htmlspecialchars($pg['title']); ?> (/page/<?php echo htmlspecialchars($pg['slug']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Categories quick insert dropdown -->
                <select id="quickCatSelect" class="form-control" style="font-size: 0.825rem; padding: 0.35rem 0.65rem; width: auto;" onchange="quickAddCatToNav(this)">
                    <option value="">+ Chèn từ Chuyên mục...</option>
                    <?php foreach ($categoriesList as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['slug']); ?>" data-title="<?php echo htmlspecialchars($cat['name']); ?>">
                            📁 <?php echo htmlspecialchars($cat['name']); ?> (/category/<?php echo htmlspecialchars($cat['slug']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <form method="POST" action="/admin/navigation" id="navForm">
        <?php echo csrfField(); ?>

        <!-- SECTION 1: HEADER NAVIGATION MENU -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">1. Menu Thanh Điều Hướng (Header Navigation)</h3>
                    <span style="font-size: 0.75rem; background: var(--secondary); border: 1px solid var(--border); padding: 0.15rem 0.5rem; border-radius: 9999px; font-weight: 600; color: var(--muted-foreground);">Tối đa 12 mục</span>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addNavItem()">
                    + Thêm mục menu
                </button>
            </div>

            <div id="navItemsContainer" style="display: flex; flex-direction: column; gap: 0.65rem;">
                <?php 
                $navItems = array_slice($navItems, 0, 12);
                foreach ($navItems as $idx => $item): 
                ?>
                    <div class="nav-item-row" style="display: grid; grid-template-columns: 1fr 1fr 110px 90px 40px; gap: 0.65rem; align-items: center; background: var(--secondary); border: 1px solid var(--border); padding: 0.65rem 0.85rem; border-radius: 8px;">
                        <input type="hidden" name="nav_key[]" value="<?php echo (int)$idx; ?>">
                        <div>
                            <input type="text" name="nav_label[]" value="<?php echo htmlspecialchars($item['label'] ?? ''); ?>" placeholder="Tên hiển thị (VD: Hướng dẫn)" class="form-control" style="font-size: 0.85rem; font-weight: 600;" required>
                        </div>
                        <div>
                            <input type="text" name="nav_url[]" value="<?php echo htmlspecialchars($item['url'] ?? ''); ?>" placeholder="Đường dẫn (VD: /page/gioi-thieu)" class="form-control" style="font-size: 0.85rem;" required>
                        </div>
                        <div>
                            <select name="nav_target[]" class="form-control" style="font-size: 0.8rem;">
                                <option value="_self" <?php echo ($item['target'] ?? '_self') === '_self' ? 'selected' : ''; ?>>Cùng tab</option>
                                <option value="_blank" <?php echo ($item['target'] ?? '') === '_blank' ? 'selected' : ''; ?>>Tab mới</option>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem;">
                            <label style="display: flex; align-items: center; gap: 0.3rem; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="nav_active[<?php echo $idx; ?>]" value="1" <?php echo !empty($item['is_active']) ? 'checked' : ''; ?>>
                                <span>Hiển thị</span>
                            </label>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive); padding: 0.35rem 0.5rem;" onclick="removeRow(this)" title="Xóa">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- SECTION 2: FOOTER COLUMNS CONFIGURATION -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- Column 2 (Chuyên mục) -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">2. Footer Cột 2 (Chuyên mục)</h3>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="addCol2Item()">+ Thêm link</button>
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="font-weight: 600; font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">Tiêu đề cột:</label>
                    <input type="text" name="footer_col2_title" value="<?php echo htmlspecialchars($footerCol2['title'] ?? 'Chuyên Mục'); ?>" class="form-control" style="font-weight: 700;">
                </div>

                <div id="col2ItemsContainer" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php foreach ($footerCol2['links'] ?? [] as $l): ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center;">
                            <input type="text" name="col2_label[]" value="<?php echo htmlspecialchars($l['label'] ?? ''); ?>" placeholder="Nhãn link" class="form-control" style="font-size: 0.8rem;" required>
                            <input type="text" name="col2_url[]" value="<?php echo htmlspecialchars($l['url'] ?? ''); ?>" placeholder="URL (/category/...)" class="form-control" style="font-size: 0.8rem;" required>
                            <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 3 (Trang tĩnh / Chính sách) -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">3. Footer Cột 3 (Thông Tin &amp; Chính Sách)</h3>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="addCol3Item()">+ Thêm link</button>
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="font-weight: 600; font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">Tiêu đề cột:</label>
                    <input type="text" name="footer_col3_title" value="<?php echo htmlspecialchars($footerCol3['title'] ?? 'Thông Tin & Chính Sách'); ?>" class="form-control" style="font-weight: 700;">
                </div>

                <div id="col3ItemsContainer" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php foreach ($footerCol3['links'] ?? [] as $l): ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center;">
                            <input type="text" name="col3_label[]" value="<?php echo htmlspecialchars($l['label'] ?? ''); ?>" placeholder="Nhãn link" class="form-control" style="font-size: 0.8rem;" required>
                            <input type="text" name="col3_url[]" value="<?php echo htmlspecialchars($l['url'] ?? ''); ?>" placeholder="URL (/page/...)" class="form-control" style="font-size: 0.8rem;" required>
                            <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SECTION 3: FOOTER BOTTOM LINKS -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">4. Liên kết Chân Trang Dưới Cùng (Bottom Bar Links)</h3>
                    <p style="margin: 0.2rem 0 0 0; font-size: 0.8rem; color: var(--muted-foreground);">Hiển thị dạng hàng ngang cạnh thông tin bản quyền Copyright.</p>
                </div>
                <button type="button" class="btn btn-secondary btn-xs" onclick="addBotItem()">+ Thêm link</button>
            </div>

            <div id="botItemsContainer" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <?php foreach ($bottomLinks as $l): ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center; background: var(--secondary); padding: 0.5rem 0.65rem; border-radius: 6px; border: 1px solid var(--border);">
                        <input type="text" name="bot_label[]" value="<?php echo htmlspecialchars($l['label'] ?? ''); ?>" placeholder="Nhãn link" class="form-control" style="font-size: 0.8rem;" required>
                        <input type="text" name="bot_url[]" value="<?php echo htmlspecialchars($l['url'] ?? ''); ?>" placeholder="URL (/page/...)" class="form-control" style="font-size: 0.8rem;" required>
                        <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
            <a href="/admin/dashboard" class="btn btn-secondary">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem;">
                💾 Lưu cấu hình Điều hướng &amp; Footer
            </button>
        </div>
    </form>
</div>

<script>
function removeRow(btn) {
    const row = btn.closest('.nav-item-row') || btn.parentElement;
    if (row) row.remove();
}

function addNavItem(label = '', url = '', target = '_self', isActive = true) {
    const container = document.getElementById('navItemsContainer');
    const currentRows = container.querySelectorAll('.nav-item-row');
    if (currentRows.length >= 12) {
        alert('Thanh điều hướng chỉ cho phép tối đa 12 mục.');
        return;
    }
    const idx = Date.now().toString();
    const div = document.createElement('div');
    div.className = 'nav-item-row';
    div.style = 'display: grid; grid-template-columns: 1fr 1fr 110px 90px 40px; gap: 0.65rem; align-items: center; background: var(--secondary); border: 1px solid var(--border); padding: 0.65rem 0.85rem; border-radius: 8px;';
    div.innerHTML = `
        <input type="hidden" name="nav_key[]" value="${idx}">
        <div>
            <input type="text" name="nav_label[]" value="${escapeHtml(label)}" placeholder="Tên hiển thị" class="form-control" style="font-size: 0.85rem; font-weight: 600;" required>
        </div>
        <div>
            <input type="text" name="nav_url[]" value="${escapeHtml(url)}" placeholder="Đường dẫn URL" class="form-control" style="font-size: 0.85rem;" required>
        </div>
        <div>
            <select name="nav_target[]" class="form-control" style="font-size: 0.8rem;">
                <option value="_self" ${target === '_self' ? 'selected' : ''}>Cùng tab</option>
                <option value="_blank" ${target === '_blank' ? 'selected' : ''}>Tab mới</option>
            </select>
        </div>
        <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem;">
            <label style="display: flex; align-items: center; gap: 0.3rem; margin: 0; cursor: pointer;">
                <input type="checkbox" name="nav_active[${idx}]" value="1" ${isActive ? 'checked' : ''}>
                <span>Hiển thị</span>
            </label>
        </div>
        <div style="text-align: right;">
            <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive); padding: 0.35rem 0.5rem;" onclick="removeRow(this)" title="Xóa">✕</button>
        </div>
    `;
    container.appendChild(div);
}

function addCol2Item(label = '', url = '') {
    const container = document.getElementById('col2ItemsContainer');
    const div = document.createElement('div');
    div.style = 'display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center;';
    div.innerHTML = `
        <input type="text" name="col2_label[]" value="${escapeHtml(label)}" placeholder="Nhãn link" class="form-control" style="font-size: 0.8rem;" required>
        <input type="text" name="col2_url[]" value="${escapeHtml(url)}" placeholder="URL (/category/...)" class="form-control" style="font-size: 0.8rem;" required>
        <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
    `;
    container.appendChild(div);
}

function addCol3Item(label = '', url = '') {
    const container = document.getElementById('col3ItemsContainer');
    const div = document.createElement('div');
    div.style = 'display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center;';
    div.innerHTML = `
        <input type="text" name="col3_label[]" value="${escapeHtml(label)}" placeholder="Nhãn link" class="form-control" style="font-size: 0.8rem;" required>
        <input type="text" name="col3_url[]" value="${escapeHtml(url)}" placeholder="URL (/page/...)" class="form-control" style="font-size: 0.8rem;" required>
        <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
    `;
    container.appendChild(div);
}

function addBotItem(label = '', url = '') {
    const container = document.getElementById('botItemsContainer');
    const div = document.createElement('div');
    div.style = 'display: grid; grid-template-columns: 1fr 1fr 34px; gap: 0.5rem; align-items: center;';
    div.innerHTML = `
        <input type="text" name="bot_label[]" value="${escapeHtml(label)}" placeholder="Nhãn liên kết" class="form-control" style="font-size: 0.8rem;" required>
        <input type="text" name="bot_url[]" value="${escapeHtml(url)}" placeholder="Đường dẫn (URL)" class="form-control" style="font-size: 0.8rem;" required>
        <button type="button" class="btn btn-secondary btn-xs" style="color: var(--destructive);" onclick="removeRow(this)">✕</button>
    `;
    container.appendChild(div);
}

function quickAddPageToNav(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const slug = opt.value;
        const title = opt.dataset.title || opt.textContent;
        addNavItem(title, '/page/' + slug, '_self', true);
        select.selectedIndex = 0;
    }
}

function quickAddCatToNav(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const slug = opt.value;
        const title = opt.dataset.title || opt.textContent;
        addNavItem(title, '/category/' + slug, '_self', true);
        select.selectedIndex = 0;
    }
}

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
