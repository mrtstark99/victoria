let renderedSkillIds = new Set();
let areAllSkillsOpen = false;

function switchMainAgentTab(tabId) {
    document.querySelectorAll('.agent-nav-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.agent-tab-panel').forEach(c => c.classList.remove('active'));

    const btn = document.querySelector(`.agent-nav-btn[data-tab="${tabId}"]`);
    const content = document.getElementById(tabId);

    if (btn) btn.classList.add('active');
    if (content) content.classList.add('active');

    if (tabId === 'tab-main-manual') {
        renderOpenSkillDocs();
    }

    if (history.replaceState) {
        const hash = tabId.replace('tab-main-', '');
        history.replaceState(null, null, '#' + hash);
    }
}

// Toggle Single Skill FAQ Item
function toggleSkillAccordion(uniqueId) {
    const card = document.getElementById('skill_card_' + uniqueId);
    if (!card) return;
    const body = card.querySelector('.skill-faq-body');
    if (!body) return;

    const isCurrentlyOpen = (body.style.display !== 'none');
    if (isCurrentlyOpen) {
        body.style.display = 'none';
        card.classList.remove('active');
    } else {
        body.style.display = 'block';
        card.classList.add('active');
        renderSingleSkillDoc(uniqueId);
    }
}

// Toggle All FAQ Items
function toggleAllSkillAccordions() {
    areAllSkillsOpen = !areAllSkillsOpen;
    document.querySelectorAll('.skill-faq-card').forEach(card => {
        const body = card.querySelector('.skill-faq-body');
        if (body) {
            body.style.display = areAllSkillsOpen ? 'block' : 'none';
        }
        if (areAllSkillsOpen) {
            card.classList.add('active');
            const uniqueId = card.id.replace('skill_card_', '');
            renderSingleSkillDoc(uniqueId);
        } else {
            card.classList.remove('active');
        }
    });

    const btnText = document.getElementById('btnToggleAllSkillsText');
    if (btnText) {
        btnText.textContent = areAllSkillsOpen ? 'Thu gọn tất cả' : 'Mở rộng tất cả';
    }
}

// Switch Rendered vs Raw view per skill doc
function switchSkillDocViewMode(uniqueId, mode) {
    const renderedBox = document.getElementById('rendered_skill_' + uniqueId);
    const rawBox = document.getElementById('rawbox_skill_' + uniqueId);
    const btnRendered = document.getElementById('btnModeRendered_' + uniqueId);
    const btnRaw = document.getElementById('btnModeRaw_' + uniqueId);

    if (mode === 'rendered') {
        if (renderedBox) renderedBox.style.display = 'block';
        if (rawBox) rawBox.style.display = 'none';
        if (btnRendered) btnRendered.classList.add('active');
        if (btnRaw) btnRaw.classList.remove('active');
        renderSingleSkillDoc(uniqueId);
    } else {
        if (renderedBox) renderedBox.style.display = 'none';
        if (rawBox) rawBox.style.display = 'block';
        if (btnRendered) btnRendered.classList.remove('active');
        if (btnRaw) btnRaw.classList.add('active');
    }
}

// Render Markdown for a specific Skill item
function renderSingleSkillDoc(uniqueId) {
    if (renderedSkillIds.has(uniqueId)) return;
    const rawEl = document.getElementById('raw_skill_' + uniqueId);
    const targetEl = document.getElementById('rendered_skill_' + uniqueId);
    if (!rawEl || !targetEl) return;

    let md = rawEl.value || '';
    if (!md.trim()) {
        targetEl.innerHTML = '<div style="padding: 1rem; color: var(--muted-foreground);">Tệp trống.</div>';
        renderedSkillIds.add(uniqueId);
        return;
    }

    try {
        if (typeof marked !== 'undefined' && marked.parse) {
            let html = marked.parse(md);
            html = html.replace(/<blockquote>\s*<p>\s*\[!(WARNING|CAUTION)\]\s*(<br>|\n)?/gi, '<blockquote class="alert-box alert-warning"><p><strong>Lưu ý:</strong><br>');
            html = html.replace(/<blockquote>\s*<p>\s*\[!(CRITICAL)\]\s*(<br>|\n)?/gi, '<blockquote class="alert-box alert-critical"><p><strong>Bắt buộc:</strong><br>');
            html = html.replace(/<blockquote>\s*<p>\s*\[!(TIP)\]\s*(<br>|\n)?/gi, '<blockquote class="alert-box alert-tip"><p><strong>Mẹo:</strong><br>');
            html = html.replace(/<blockquote>\s*<p>\s*\[!(NOTE|INFO)\]\s*(<br>|\n)?/gi, '<blockquote class="alert-box alert-info"><p><strong>Ghi chú:</strong><br>');
            targetEl.innerHTML = html;
        } else {
            targetEl.innerHTML = fallbackParseMarkdown(md);
        }
        renderedSkillIds.add(uniqueId);
    } catch (e) {
        console.error('Markdown rendering error:', e);
        targetEl.innerHTML = fallbackParseMarkdown(md);
        renderedSkillIds.add(uniqueId);
    }
}

function renderOpenSkillDocs() {
    document.querySelectorAll('.skill-faq-card.active').forEach(card => {
        const uniqueId = card.id.replace('skill_card_', '');
        renderSingleSkillDoc(uniqueId);
    });
}

function fallbackParseMarkdown(md) {
    let text = md.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    text = text.replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/gi, '<pre><code>$2</code></pre>');
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
    text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');
    text = text.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
    text = text.replace(/\*(.*?)\*/gim, '<em>$1</em>');
    text = text.replace(/^---$/gim, '<hr>');
    text = text.replace(/\n\n+/g, '</p><p>');
    return '<p>' + text + '</p>';
}

function fallbackCopyText(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.top = '-9999px';
    textArea.style.left = '-9999px';
    textArea.style.opacity = '0';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    let success = false;
    try {
        success = document.execCommand('copy');
    } catch (err) {
        success = false;
    }
    document.body.removeChild(textArea);
    return success;
}

function copyTextHelper(text) {
    return new Promise((resolve, reject) => {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(resolve).catch(() => {
                if (fallbackCopyText(text)) resolve();
                else reject(new Error('Không thể sao chép qua Clipboard API'));
            });
        } else {
            if (fallbackCopyText(text)) resolve();
            else {
                window.prompt('Vui lòng sao chép văn bản:', text);
                resolve();
            }
        }
    });
}

function copySkillFile(textareaId, btn) {
    const rawEl = document.getElementById(textareaId);
    const text = rawEl ? rawEl.value : '';
    copyTextHelper(text).then(() => {
        if (btn) {
            const orig = btn.innerHTML;
            btn.innerHTML = '<span style="color:#16a34a; font-weight:700;">✓ Đã sao chép!</span>';
            setTimeout(() => { btn.innerHTML = orig; }, 2000);
        }
    }).catch(err => alert('Lỗi sao chép: ' + err));
}

function copySnippetText(text, btn) {
    copyTextHelper(text).then(() => {
        if (btn) {
            btn.style.color = '#16a34a';
            setTimeout(() => { btn.style.color = ''; }, 2000);
        }
    });
}

function copySnippet(elemId, btn) {
    const el = document.getElementById(elemId);
    if (!el) return;
    const text = el.value || el.textContent;
    copyTextHelper(text).then(() => {
        if (btn) {
            const orig = btn.textContent;
            btn.innerHTML = '<span style="color:#16a34a; font-weight:700;">✓ Đã chép!</span>';
            setTimeout(() => { btn.textContent = orig; }, 2000);
        }
    }).catch(err => alert('Lỗi sao chép: ' + err));
}

function resetDefaultPrompt() {
    if (confirm('Khôi phục Master System Prompt về bản mặc định chuẩn Google E-E-A-T?')) {
        const el = document.getElementById('ai_custom_system_prompt');
        if (el && window.DEFAULT_MASTER_SYSTEM_PROMPT) {
            el.value = window.DEFAULT_MASTER_SYSTEM_PROMPT;
        }
    }
}

function copyMasterPrompt() {
    const el = document.getElementById('ai_custom_system_prompt');
    const bodyText = el ? el.value.trim() : '';

    copyTextHelper(bodyText).then(() => {
        alert('Đã sao chép Master System Prompt vào Clipboard!');
    }).catch(err => {
        alert('Không thể sao chép: ' + err);
    });
}

function copyModalToken() {
    const input = document.getElementById('modalTokenInput');
    if (!input) return;
    const raw = input.value.trim();
    const btn = document.getElementById('btnCopyModalToken');
    
    copyTextHelper(raw).then(() => {
        if (btn) {
            btn.innerHTML = '<span style="color:#16a34a; font-weight:700;">✓ Đã sao chép!</span>';
            setTimeout(() => { btn.innerHTML = '<span>Sao chép</span>'; }, 2500);
        }
    }).catch(err => {
        window.prompt('Sao chép mã Token tại đây:', raw);
    });
}

// Modal Handlers
function openModal(modalId) {
    const m = document.getElementById(modalId);
    if (m) m.style.display = 'flex';
}
function closeModal(modalId) {
    const m = document.getElementById(modalId);
    if (m) m.style.display = 'none';
}
function closeModalOnBackdrop(e, modalId) {
    if (e.target === document.getElementById(modalId)) {
        closeModal(modalId);
    }
}
function openCreateTokenModal() { openModal('createTokenModal'); }

function setTokenScopesPreset(modalType, preset) {
    const containerId = modalType === 'create' ? 'createTokenScopesContainer' : 'editTokenScopesContainer';
    const container = document.getElementById(containerId);
    if (!container) return;

    const checkboxes = container.querySelectorAll('input[name="scopes[]"]');
    
    if (preset === 'all') {
        checkboxes.forEach(cb => cb.checked = true);
    } else if (preset === 'none') {
        checkboxes.forEach(cb => cb.checked = false);
    } else if (preset === 'writer') {
        const writerScopes = ['posts:read', 'posts:draft', 'posts:publish', 'pages:read', 'pages:draft', 'pages:publish', 'navigation:read', 'navigation:write', 'media:upload', 'seo:read', 'seo:write', 'category:read', 'brand:read', 'analytics:read'];
        checkboxes.forEach(cb => {
            cb.checked = writerScopes.includes(cb.value);
        });
    }
}

function openEditTokenModal(tk) {
    document.getElementById('edit_token_id').value = tk.id;
    document.getElementById('edit_token_name').value = tk.token_name;
    document.getElementById('edit_default_author_id').value = tk.default_author_id || 1;
    if (document.getElementById('edit_allowed_ips')) {
        document.getElementById('edit_allowed_ips').value = tk.allowed_ips || '';
    }

    const scopes = (tk.permissions || '').split(',').map(s => s.trim().toLowerCase());
    document.querySelectorAll('#editTokenForm input[name="scopes[]"]').forEach(cb => {
        cb.checked = scopes.includes(cb.value.toLowerCase());
    });

    openModal('editTokenModal');
}

function handleGuidelinesFormSubmit(e) {
    return true;
}

window.initAgentApp = function(hasNewToken) {
    renderOpenSkillDocs();

    if (hasNewToken) {
        switchMainAgentTab('tab-main-tokens');
    } else {
        const hash = window.location.hash.replace('#', '');
        if (hash === 'tokens') {
            switchMainAgentTab('tab-main-tokens');
        } else if (hash === 'guidelines' || hash === 'prompt') {
            switchMainAgentTab('tab-main-guidelines');
        } else if (hash === 'activities') {
            switchMainAgentTab('tab-main-activities');
        } else if (hash === 'manual' || hash === 'docs' || hash === 'skill' || hash === 'skills') {
            switchMainAgentTab('tab-main-manual');
        } else if (hash === 'ui' || hash === 'ui-elements') {
            switchMainAgentTab('tab-main-ui-elements');
        } else if (hash && document.getElementById('tab-main-' + hash)) {
            switchMainAgentTab('tab-main-' + hash);
        }
    }
};
