let uploadedLogoPreviewSrc = null;
let uploadedFaviconPreviewSrc = null;

// Confirm & Mark Logo Removal (Deferred until Save)
function confirmRemoveLogo() {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh Logo này? Thao tác sẽ được áp dụng sau khi bạn nhấn "Lưu cấu hình Brand".')) {
        const logoUrlInput = document.getElementById('site_logo_url');
        if (logoUrlInput) logoUrlInput.value = '';
        const fileInput = document.getElementById('site_logo_file');
        if (fileInput) fileInput.value = '';
        const fnLabel = document.getElementById('logoFileName');
        if (fnLabel) fnLabel.innerText = '';
        const removeInput = document.getElementById('remove_site_logo');
        if (removeInput) removeInput.value = '1';
        uploadedLogoPreviewSrc = null;

        const card = document.getElementById('logoPreviewCard');
        if (card) card.style.display = 'none';
        const notice = document.getElementById('logoRemovedNotice');
        if (notice) notice.style.display = 'inline-flex';

        updateBrandLivePreview();
    }
}

function undoRemoveLogo() {
    const removeInput = document.getElementById('remove_site_logo');
    if (removeInput) removeInput.value = '0';
    const logoUrlInput = document.getElementById('site_logo_url');
    if (logoUrlInput) logoUrlInput.value = window.initialLogoUrl || '';
    const card = document.getElementById('logoPreviewCard');
    if (card) card.style.display = window.initialLogoUrl ? 'flex' : 'none';
    const notice = document.getElementById('logoRemovedNotice');
    if (notice) notice.style.display = 'none';

    updateBrandLivePreview();
}

// Confirm & Mark Favicon Removal (Deferred until Save)
function confirmRemoveFavicon() {
    if (confirm('Bạn có chắc chắn muốn xóa Favicon tùy chỉnh này? Thao tác sẽ được áp dụng sau khi bạn nhấn "Lưu cấu hình Brand".')) {
        const favUrlInput = document.getElementById('site_favicon_url');
        if (favUrlInput) favUrlInput.value = '';
        const fileInput = document.getElementById('site_favicon_file');
        if (fileInput) fileInput.value = '';
        const fnLabel = document.getElementById('faviconFileName');
        if (fnLabel) fnLabel.innerText = '';
        const removeInput = document.getElementById('remove_site_favicon');
        if (removeInput) removeInput.value = '1';
        uploadedFaviconPreviewSrc = null;

        const card = document.getElementById('faviconPreviewCard');
        if (card) card.style.display = 'none';
        const notice = document.getElementById('faviconRemovedNotice');
        if (notice) notice.style.display = 'inline-flex';

        updateBrandLivePreview();
    }
}

function undoRemoveFavicon() {
    const removeInput = document.getElementById('remove_site_favicon');
    if (removeInput) removeInput.value = '0';
    const favUrlInput = document.getElementById('site_favicon_url');
    if (favUrlInput) favUrlInput.value = window.initialFaviconUrl || '';
    const card = document.getElementById('faviconPreviewCard');
    if (card) card.style.display = window.initialFaviconUrl ? 'flex' : 'none';
    const notice = document.getElementById('faviconRemovedNotice');
    if (notice) notice.style.display = 'none';

    updateBrandLivePreview();
}

// Confirm & Mark OG Image Removal (Deferred until Save)
function confirmRemoveOgImage() {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh Open Graph này? Thao tác sẽ được áp dụng sau khi bạn nhấn "Lưu cấu hình Brand".')) {
        const ogUrlInput = document.getElementById('default_og_image');
        if (ogUrlInput) ogUrlInput.value = '';
        const fileInput = document.getElementById('default_og_image_file');
        if (fileInput) fileInput.value = '';
        const fnLabel = document.getElementById('ogFileName');
        if (fnLabel) fnLabel.innerText = '';
        const removeInput = document.getElementById('remove_default_og_image');
        if (removeInput) removeInput.value = '1';

        const card = document.getElementById('ogPreviewCard');
        if (card) card.style.display = 'none';
        const notice = document.getElementById('ogRemovedNotice');
        if (notice) notice.style.display = 'inline-flex';

        updateBrandLivePreview();
    }
}

function undoRemoveOgImage() {
    const removeInput = document.getElementById('remove_default_og_image');
    if (removeInput) removeInput.value = '0';
    const ogUrlInput = document.getElementById('default_og_image');
    if (ogUrlInput) ogUrlInput.value = window.initialOgImageUrl || '';
    const card = document.getElementById('ogPreviewCard');
    if (card) card.style.display = window.initialOgImageUrl ? 'flex' : 'none';
    const notice = document.getElementById('ogRemovedNotice');
    if (notice) notice.style.display = 'none';

    updateBrandLivePreview();
}

// Accordion Toggle function
function toggleBrandAccordion(headerEl) {
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
let allBrandExpanded = false;
function toggleAllBrandAccordions() {
    allBrandExpanded = !allBrandExpanded;
    const bodies = document.querySelectorAll('.accordion-body');
    const chevrons = document.querySelectorAll('.chevron-icon');
    const btnText = document.getElementById('toggleAllBrandBtnText');

    bodies.forEach(b => b.style.display = allBrandExpanded ? 'block' : 'none');
    chevrons.forEach(c => c.style.transform = allBrandExpanded ? 'rotate(180deg)' : 'rotate(0deg)');
    if (btnText) btnText.innerText = allBrandExpanded ? 'Thu gọn tất cả' : 'Mở rộng tất cả';
}

// Real-time Brand Live Preview Updater
function updateBrandLivePreview() {
    const nameVal = (document.getElementById('site_name')?.value || '').trim() || 'MinimaList';
    const sloganVal = (document.getElementById('site_slogan')?.value || '').trim() || 'Chia sẻ kiến thức SEO & AI';
    const displayMode = document.getElementById('site_logo_display_mode')?.value || 'logo_and_text';
    const badgeVal = (document.getElementById('site_logo_badge')?.value || '').trim() || 'M';
    const logoUrlInput = (document.getElementById('site_logo_url')?.value || '').trim();
    const faviconUrlInput = (document.getElementById('site_favicon_url')?.value || '').trim();
    const removeLogoChecked = document.getElementById('remove_site_logo')?.value === '1';
    const removeFaviconChecked = document.getElementById('remove_site_favicon')?.value === '1';
    const aboutVal = (document.getElementById('footer_about_text')?.value || '').trim();
    const copyrightVal = (document.getElementById('footer_copyright')?.value || '').trim();
    const metaDescVal = (document.getElementById('default_meta_description')?.value || '').trim();

    // Active Logo Source: File Upload takes precedence, then URL input, unless removal marked
    const activeLogoSrc = removeLogoChecked ? '' : (uploadedLogoPreviewSrc || logoUrlInput);
    const activeFaviconSrc = removeFaviconChecked ? '' : (uploadedFaviconPreviewSrc || faviconUrlInput);

    // 1. Browser Tab Simulation
    const tabFavicon = document.getElementById('previewTabFavicon');
    const tabTitle = document.getElementById('previewTabTitle');
    if (tabFavicon) {
        if (activeFaviconSrc) {
            tabFavicon.src = activeFaviconSrc;
            tabFavicon.style.display = 'block';
        } else {
            tabFavicon.src = 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%236366f1%22/><text y=%22.9em%22 font-size=%2280%22 x=%2250%%22 text-anchor=%22middle%22 fill=%22white%22 font-weight=%22bold%22>' + encodeURIComponent(badgeVal.charAt(0) || 'M') + '</text></svg>';
            tabFavicon.style.display = 'block';
        }
    }
    if (tabTitle) {
        tabTitle.innerText = `${nameVal} - ${sloganVal}`;
    }

    // 2. Header Logo & Brand Text
    const imageLogoImg = document.getElementById('previewImageLogoImg');
    const badgeEl = document.getElementById('previewBadge');
    const brandTextEl = document.getElementById('previewBrandText');
    const modeTag = document.getElementById('previewDisplayModeTag');

    if (displayMode === 'text_only') {
        if (imageLogoImg) imageLogoImg.style.display = 'none';
        if (badgeEl) badgeEl.style.display = 'none';
        if (brandTextEl) brandTextEl.style.display = 'inline-flex';
        if (modeTag) modeTag.innerText = 'Chỉ Tên Brand';
    } else if (displayMode === 'logo_only') {
        if (brandTextEl) brandTextEl.style.display = 'none';
        if (activeLogoSrc) {
            if (imageLogoImg) {
                imageLogoImg.src = activeLogoSrc;
                imageLogoImg.style.display = 'block';
            }
            if (badgeEl) badgeEl.style.display = 'none';
        } else {
            if (imageLogoImg) imageLogoImg.style.display = 'none';
            if (badgeEl) {
                badgeEl.innerText = badgeVal;
                badgeEl.style.display = 'flex';
            }
        }
        if (modeTag) modeTag.innerText = 'Chỉ Logo';
    } else {
        // logo_and_text
        if (brandTextEl) brandTextEl.style.display = 'inline-flex';
        if (activeLogoSrc) {
            if (imageLogoImg) {
                imageLogoImg.src = activeLogoSrc;
                imageLogoImg.style.display = 'block';
            }
            if (badgeEl) badgeEl.style.display = 'none';
        } else {
            if (imageLogoImg) imageLogoImg.style.display = 'none';
            if (badgeEl) {
                badgeEl.innerText = badgeVal;
                badgeEl.style.display = 'flex';
            }
        }
        if (modeTag) modeTag.innerText = 'Logo + Tên Brand';
    }

    if (brandTextEl) brandTextEl.innerText = nameVal;

    // 3. Footer Title & Description
    if (document.getElementById('previewFooterTitle')) document.getElementById('previewFooterTitle').innerText = nameVal;
    if (document.getElementById('previewFooterBrand')) document.getElementById('previewFooterBrand').innerText = nameVal;
    if (document.getElementById('previewFooterDesc')) document.getElementById('previewFooterDesc').innerText = aboutVal || sloganVal;

    // 4. Contact Info
    const emailVal = (document.getElementById('site_email')?.value || '').trim();
    const phoneVal = (document.getElementById('site_phone')?.value || '').trim();
    const addressVal = (document.getElementById('site_address')?.value || '').trim();

    if (document.getElementById('previewEmailText')) document.getElementById('previewEmailText').innerText = emailVal || 'admin@example.com';
    if (document.getElementById('previewPhoneText')) document.getElementById('previewPhoneText').innerText = phoneVal || '+84 123 456 789';
    if (document.getElementById('previewAddressText')) document.getElementById('previewAddressText').innerText = addressVal || 'Hà Nội, Việt Nam';

    // 5. Social Links Opacity
    const fbVal = (document.getElementById('social_facebook')?.value || '').trim();
    const twVal = (document.getElementById('social_twitter')?.value || '').trim();
    const ghVal = (document.getElementById('social_github')?.value || '').trim();
    const liVal = (document.getElementById('social_linkedin')?.value || '').trim();
    const ytVal = (document.getElementById('social_youtube')?.value || '').trim();

    const pFb = document.getElementById('pSocialFb');
    const pTw = document.getElementById('pSocialTw');
    const pGh = document.getElementById('pSocialGh');
    const pLi = document.getElementById('pSocialLi');
    const pYt = document.getElementById('pSocialYt');

    if (pFb) { pFb.style.opacity = fbVal ? '1' : '0.35'; pFb.style.fontWeight = fbVal ? '700' : 'normal'; }
    if (pTw) { pTw.style.opacity = twVal ? '1' : '0.35'; pTw.style.fontWeight = twVal ? '700' : 'normal'; }
    if (pGh) { pGh.style.opacity = ghVal ? '1' : '0.35'; pGh.style.fontWeight = ghVal ? '700' : 'normal'; }
    if (pLi) { pLi.style.opacity = liVal ? '1' : '0.35'; pLi.style.fontWeight = liVal ? '700' : 'normal'; }
    if (pYt) { pYt.style.opacity = ytVal ? '1' : '0.35'; pYt.style.fontWeight = ytVal ? '700' : 'normal'; }

    // 6. Copyright String
    const currentYear = new Date().getFullYear();
    let finalCopyright = copyrightVal ? copyrightVal.replace('{year}', currentYear) : `© ${currentYear} ${nameVal}. All rights reserved.`;
    if (document.getElementById('previewFooterCopyright')) document.getElementById('previewFooterCopyright').innerHTML = finalCopyright;

    // 7. Google SERP Snippet
    const gTitle = document.getElementById('previewGoogleTitle');
    const gDesc = document.getElementById('previewGoogleDesc');
    if (gTitle) gTitle.innerText = `${nameVal} - ${sloganVal}`;
    if (gDesc) gDesc.innerText = metaDescVal || sloganVal || 'Không gian chia sẻ kiến thức chọn lọc về SEO On-page, cấu trúc Topic Cluster...';
}

function previewLogoUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fnLabel = document.getElementById('logoFileName');
        if (fnLabel) fnLabel.innerText = file.name;
        const removeInput = document.getElementById('remove_site_logo');
        if (removeInput) removeInput.value = '0';
        const notice = document.getElementById('logoRemovedNotice');
        if (notice) notice.style.display = 'none';

        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedLogoPreviewSrc = e.target.result;
            updateBrandLivePreview();
        };
        reader.readAsDataURL(file);
    }
}

function previewFaviconUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fnLabel = document.getElementById('faviconFileName');
        if (fnLabel) fnLabel.innerText = file.name;
        const removeInput = document.getElementById('remove_site_favicon');
        if (removeInput) removeInput.value = '0';
        const notice = document.getElementById('faviconRemovedNotice');
        if (notice) notice.style.display = 'none';

        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedFaviconPreviewSrc = e.target.result;
            updateBrandLivePreview();
        };
        reader.readAsDataURL(file);
    }
}

function previewOgUpload(input) {
    if (input.files && input.files[0]) {
        const fnLabel = document.getElementById('ogFileName');
        if (fnLabel) fnLabel.innerText = input.files[0].name;
        const removeInput = document.getElementById('remove_default_og_image');
        if (removeInput) removeInput.value = '0';
        const notice = document.getElementById('ogRemovedNotice');
        if (notice) notice.style.display = 'none';
    }
}

// Attach global form listeners
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('brandSettingsForm');
    if (form) {
        form.addEventListener('input', updateBrandLivePreview);
        form.addEventListener('change', updateBrandLivePreview);
    }
    updateBrandLivePreview();
});
