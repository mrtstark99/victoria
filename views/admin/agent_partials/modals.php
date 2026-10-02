<?php
/**
 * Agent Management Modals Partial
 */
?>
<!-- Modal: New Token Generated -->
<?php if (!empty($new_token)): ?>
    <div class="modal-overlay" id="newTokenCreatedModal" style="display: flex;" onclick="closeModalOnBackdrop(event, 'newTokenCreatedModal')">
        <div class="modal-card" style="max-width: 520px; border: 2px solid #16a34a; box-shadow: 0 20px 40px -10px rgba(22, 163, 74, 0.3);">
            <div class="modal-header" style="background: rgba(22, 163, 74, 0.08); border-bottom: 1px solid rgba(22, 163, 74, 0.2);">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--foreground);">Cấp Token AI Agent Thành Công!</h3>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('newTokenCreatedModal')">&times;</button>
            </div>
            <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="font-size: 0.875rem; color: var(--foreground);">
                    Tên Agent: <strong style="color: var(--primary); font-size: 0.95rem;"><?php echo htmlspecialchars($new_token['name']); ?></strong>
                </div>
                <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 6px; padding: 0.75rem; font-size: 0.8rem; color: #b45309; line-height: 1.45;">
                    ⚠️ <strong>Lưu ý:</strong> Mã Token chỉ hiển thị <u>duy nhất 1 lần</u>. Hãy sao chép ngay để cấu hình cho IDE AI (Cursor, Claude, Antigravity...).
                </div>
                <div style="background: var(--secondary); border: 1px solid var(--border); padding: 0.75rem 0.9rem; border-radius: 8px;">
                    <label style="font-size: 0.7rem; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.35rem; display: block;">Mã Bearer Token:</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="text" readonly id="modalTokenInput" value="<?php echo htmlspecialchars($new_token['raw']); ?>" class="form-control" style="font-family: ui-monospace, SFMono-Regular, monospace; font-weight: 700; font-size: 0.85rem; background: var(--card); color: var(--foreground); cursor: pointer;" onclick="this.select();">
                        <button type="button" onclick="copyModalToken()" class="btn btn-sm" id="btnCopyModalToken" style="flex-shrink: 0; padding: 0.45rem 0.85rem; font-weight: 700;">
                            <span>Sao chép</span>
                        </button>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.25rem;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('newTokenCreatedModal')">Đã Lưu &amp; Đóng</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal 1: Create Token Modal -->
<div class="modal-overlay" id="createTokenModal" style="display: none;" onclick="closeModalOnBackdrop(event, 'createTokenModal')">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">Cấp Token Mới Cho AI Agent</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('createTokenModal')">&times;</button>
        </div>
        <form method="POST" action="/admin/agent" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.95rem;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="create_token">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="new_token_name" style="font-weight: 600; font-size: 0.825rem;">Tên Agent / Ứng dụng <span style="color:var(--destructive)">*</span></label>
                <input type="text" class="form-control" name="token_name" id="new_token_name" required placeholder="Ví dụ: Cursor-SEO-Bot, Claude-Assistant...">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="new_default_author_id" style="font-weight: 600; font-size: 0.825rem;">Tác giả mặc định</label>
                <select class="form-control" name="default_author_id" id="new_default_author_id">
                    <?php foreach ($authors as $auth): ?>
                        <option value="<?php echo $auth['id']; ?>"><?php echo htmlspecialchars($auth['full_name'] . ' (@' . $auth['username'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <label style="font-weight: 700; font-size: 0.825rem; margin: 0; color: var(--foreground);">Phân Quyền (Scopes)</label>
                    <div style="display: flex; gap: 0.35rem;">
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('create', 'all')">⚡ Full</button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('create', 'writer')">✍️ Writer</button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('create', 'none')">Bỏ chọn</button>
                    </div>
                </div>

                <div id="createTokenScopesContainer" class="scopes-checkbox-grid">
                    <label class="scope-label full-width"><input type="checkbox" name="scopes[]" value="admin"> <span>👑 <code>admin</code> (Toàn quyền)</span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:read" checked> <span><code>posts:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:draft" checked> <span><code>posts:draft</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:publish"> <span><code>posts:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:read" checked> <span><code>pages:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:draft" checked> <span><code>pages:draft</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:publish"> <span><code>pages:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:read"> <span><code>services:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:write"> <span><code>services:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:publish"> <span><code>services:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:delete"> <span><code>services:delete</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="cost:read"> <span><code>cost:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="cost:write"> <span><code>cost:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:read"> <span><code>contacts:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:write"> <span><code>contacts:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:delete"> <span><code>contacts:delete</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="calculator:read"> <span><code>calculator:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="calculator:write"> <span><code>calculator:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="homepage:read"> <span><code>homepage:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="homepage:write"> <span><code>homepage:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="profile:read"> <span><code>profile:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="profile:write"> <span><code>profile:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="sidebar:read"> <span><code>sidebar:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="sidebar:write"> <span><code>sidebar:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:settings:read"> <span><code>analytics:settings:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:settings:write"> <span><code>analytics:settings:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="fixed_pages:read"> <span><code>fixed_pages:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="fixed_pages:write"> <span><code>fixed_pages:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="navigation:read" checked> <span><code>navigation:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="navigation:write"> <span><code>navigation:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="media:upload" checked> <span><code>media:upload</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="seo:read" checked> <span><code>seo:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="seo:write" checked> <span><code>seo:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="category:read" checked> <span><code>category:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="brand:read" checked> <span><code>brand:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:read" checked> <span><code>analytics:read</code></span></label>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="new_allowed_ips" style="font-weight: 600; font-size: 0.825rem;">IP Allowlist (Tùy chọn)</label>
                <input type="text" class="form-control" name="allowed_ips" id="new_allowed_ips" placeholder="Để trống nếu cho phép mọi IP">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border); padding-top: 0.85rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('createTokenModal')">Hủy</button>
                <button type="submit" class="btn btn-sm btn-primary-action">Cấp Token</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Token Modal -->
<div class="modal-overlay" id="editTokenModal" style="display: none;" onclick="closeModalOnBackdrop(event, 'editTokenModal')">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 700;">Chỉnh Sửa Token</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('editTokenModal')">&times;</button>
        </div>
        <form method="POST" action="/admin/agent" id="editTokenForm" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 0.95rem;">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="update_token">
            <input type="hidden" name="token_id" id="edit_token_id">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="edit_token_name" style="font-weight: 600; font-size: 0.825rem;">Tên Agent <span style="color:var(--destructive)">*</span></label>
                <input type="text" class="form-control" name="token_name" id="edit_token_name" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="edit_default_author_id" style="font-weight: 600; font-size: 0.825rem;">Tác giả mặc định</label>
                <select class="form-control" name="default_author_id" id="edit_default_author_id">
                    <?php foreach ($authors as $auth): ?>
                        <option value="<?php echo $auth['id']; ?>"><?php echo htmlspecialchars($auth['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <label style="font-weight: 700; font-size: 0.825rem; margin: 0; color: var(--foreground);">Phân Quyền (Scopes)</label>
                    <div style="display: flex; gap: 0.35rem;">
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('edit', 'all')">⚡ Full</button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('edit', 'writer')">✍️ Writer</button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="setTokenScopesPreset('edit', 'none')">Bỏ chọn</button>
                    </div>
                </div>

                <div id="editTokenScopesContainer" class="scopes-checkbox-grid">
                    <label class="scope-label full-width"><input type="checkbox" name="scopes[]" value="admin"> <span>👑 <code>admin</code> (Toàn quyền)</span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:read"> <span><code>posts:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:draft"> <span><code>posts:draft</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="posts:publish"> <span><code>posts:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:read"> <span><code>pages:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:draft"> <span><code>pages:draft</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="pages:publish"> <span><code>pages:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:read"> <span><code>services:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:write"> <span><code>services:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:publish"> <span><code>services:publish</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="services:delete"> <span><code>services:delete</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="cost:read"> <span><code>cost:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="cost:write"> <span><code>cost:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:read"> <span><code>contacts:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:write"> <span><code>contacts:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="contacts:delete"> <span><code>contacts:delete</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="calculator:read"> <span><code>calculator:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="calculator:write"> <span><code>calculator:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="homepage:read"> <span><code>homepage:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="homepage:write"> <span><code>homepage:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="profile:read"> <span><code>profile:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="profile:write"> <span><code>profile:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="sidebar:read"> <span><code>sidebar:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="sidebar:write"> <span><code>sidebar:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:settings:read"> <span><code>analytics:settings:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:settings:write"> <span><code>analytics:settings:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="fixed_pages:read"> <span><code>fixed_pages:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="fixed_pages:write"> <span><code>fixed_pages:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="navigation:read"> <span><code>navigation:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="navigation:write"> <span><code>navigation:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="media:upload"> <span><code>media:upload</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="seo:read"> <span><code>seo:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="seo:write"> <span><code>seo:write</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="category:read"> <span><code>category:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="brand:read"> <span><code>brand:read</code></span></label>
                    <label class="scope-label"><input type="checkbox" name="scopes[]" value="analytics:read"> <span><code>analytics:read</code></span></label>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="edit_allowed_ips" style="font-weight: 600; font-size: 0.825rem;">IP Allowlist</label>
                <input type="text" class="form-control" name="allowed_ips" id="edit_allowed_ips" placeholder="Để trống nếu cho phép mọi IP">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border); padding-top: 0.85rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editTokenModal')">Hủy</button>
                <button type="submit" class="btn btn-sm btn-primary-action">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
</div>
