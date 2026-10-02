// Comprehensive Live Sidebar Preview Updater
function updateLiveSidebarPreview() {
    const previewList = document.getElementById('sidebarLivePreviewList');
    if (!previewList) return;
    const emptyNotice = document.getElementById('previewEmptyNotice');

    // 1. Trending Widget
    const trendingCheck = document.getElementById('post_sidebar_trending_enabled');
    const trendingTitleInput = document.getElementById('post_sidebar_trending_title');
    const trendingLimitSelect = document.getElementById('post_sidebar_trending_limit');
    const previewTrending = previewList.querySelector('[data-preview-key="trending"]');

    if (previewTrending) {
        const isEn = trendingCheck && trendingCheck.checked;
        previewTrending.style.display = isEn ? 'block' : 'none';
        const titleEl = document.getElementById('previewTrendingTitle');
        if (titleEl && trendingTitleInput) {
            titleEl.innerText = trendingTitleInput.value.trim() || 'Đọc nhiều nhất';
        }

        const listEl = document.getElementById('previewTrendingList');
        if (listEl && trendingLimitSelect) {
            const limit = parseInt(trendingLimitSelect.value, 10) || 4;
            const sampleTitles = [
                'Chiến lược xây dựng Topic Cluster đỉnh cao 2026',
                'Tối ưu Core Web Vitals chuẩn Google PageSpeed',
                'Ứng dụng AI Agent vào quy trình tạo nội dung',
                'Nghiên cứu Intent từ khóa và thực chiến Onpage',
                'Cấu trúc liên kết nội bộ Internal Link hiệu quả',
                'Cách lập chỉ mục nhanh bài viết mới trên Google',
                'Phân tích đối thủ cạnh tranh bằng mô hình Semantic',
                'Tối ưu tỷ lệ chuyển đổi cho Blog & Website tin tức'
            ];
            let itemsHtml = '';
            for (let i = 0; i < Math.min(limit, sampleTitles.length); i++) {
                itemsHtml += `
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-weight: 800; color: var(--muted-foreground); font-size: 0.75rem;">${String(i + 1).padStart(2, '0')}</span>
                        <span style="color: var(--foreground); font-weight: 600; line-height: 1.3;">${sampleTitles[i]}</span>
                    </div>
                `;
            }
            listEl.innerHTML = itemsHtml;
        }
    }

    // 2. Banner Widget
    const bannerCheck = document.getElementById('post_sidebar_banner_enabled');
    const bannerTitleInput = document.getElementById('post_sidebar_banner_title');
    const bannerBadgeInput = document.getElementById('post_sidebar_banner_badge');
    const bannerImageInput = document.getElementById('post_sidebar_banner_image');
    const previewBanner = previewList.querySelector('[data-preview-key="banner"]');

    if (previewBanner) {
        const isEn = bannerCheck && bannerCheck.checked;
        previewBanner.style.display = isEn ? 'block' : 'none';

        const bTitle = document.getElementById('previewBannerTitle');
        const bBadge = document.getElementById('previewBannerBadge');
        const bHeader = document.getElementById('previewBannerHeader');
        const bImg = document.getElementById('previewBannerImg');
        const bHolder = document.getElementById('previewBannerPlaceholder');

        const titleVal = bannerTitleInput ? bannerTitleInput.value.trim() : '';
        const badgeVal = bannerBadgeInput ? bannerBadgeInput.value.trim() : '';
        const imgUrl = bannerImageInput ? bannerImageInput.value.trim() : '';

        if (bTitle) bTitle.innerText = titleVal || 'Khám phá đối tác';
        if (bBadge) {
            bBadge.innerText = badgeVal || 'Tài trợ';
            bBadge.style.display = badgeVal ? 'inline-block' : 'none';
        }
        if (bHeader) {
            bHeader.style.display = (titleVal || badgeVal) ? 'flex' : 'none';
        }

        if (imgUrl) {
            if (bImg) {
                bImg.src = imgUrl;
                bImg.style.display = 'block';
            }
            if (bHolder) bHolder.style.display = 'none';
        } else {
            if (bImg) bImg.style.display = 'none';
            if (bHolder) bHolder.style.display = 'block';
        }
    }

    // 3. Newsletter Widget
    const newsCheck = document.getElementById('post_sidebar_newsletter_enabled');
    const newsTitleInput = document.getElementById('post_sidebar_newsletter_title');
    const newsDescInput = document.getElementById('post_sidebar_newsletter_desc');
    const newsBtnInput = document.getElementById('post_sidebar_newsletter_btn');
    const previewNews = previewList.querySelector('[data-preview-key="newsletter"]');

    if (previewNews) {
        const isEn = newsCheck && newsCheck.checked;
        previewNews.style.display = isEn ? 'block' : 'none';

        const nTitle = document.getElementById('previewNewsletterTitle');
        const nDesc = document.getElementById('previewNewsletterDesc');
        const nBtn = document.getElementById('previewNewsletterBtn');

        if (nTitle && newsTitleInput) nTitle.innerText = newsTitleInput.value.trim() || 'Bản tin SEO & AI';
        if (nDesc && newsDescInput) nDesc.innerText = newsDescInput.value.trim() || 'Cập nhật các thuật toán mới nhất...';
        if (nBtn && newsBtnInput) nBtn.innerText = newsBtnInput.value.trim() || 'Gửi';
    }

    // 4. Custom CTA Widget
    const ctaCheck = document.getElementById('post_sidebar_cta_enabled');
    const ctaTitleInput = document.getElementById('post_sidebar_cta_title');
    const ctaBadgeInput = document.getElementById('post_sidebar_cta_badge');
    const ctaDescInput = document.getElementById('post_sidebar_cta_desc');
    const ctaBtnInput = document.getElementById('post_sidebar_cta_btn_text');
    const previewCta = previewList.querySelector('[data-preview-key="cta"]');

    if (previewCta) {
        const isEn = ctaCheck && ctaCheck.checked;
        previewCta.style.display = isEn ? 'block' : 'none';

        const cTitle = document.getElementById('previewCtaTitle');
        const cBadge = document.getElementById('previewCtaBadge');
        const cDesc = document.getElementById('previewCtaDesc');
        const cBtn = document.getElementById('previewCtaBtn');

        if (cTitle && ctaTitleInput) cTitle.innerText = ctaTitleInput.value.trim() || 'Tư vấn chiến lược SEO & AI';
        if (cBadge && ctaBadgeInput) {
            cBadge.innerText = ctaBadgeInput.value.trim() || 'Tư vấn';
            cBadge.style.display = ctaBadgeInput.value.trim() ? 'inline-block' : 'none';
        }
        if (cDesc && ctaDescInput) cDesc.innerText = ctaDescInput.value.trim() || 'Đồng hành cùng doanh nghiệp...';
        if (cBtn && ctaBtnInput) cBtn.innerText = ctaBtnInput.value.trim() || 'Liên hệ tư vấn ngay';
    }

    // 5. Categories Widget
    const catCheck = document.getElementById('post_sidebar_categories_enabled');
    const catTitleInput = document.getElementById('post_sidebar_categories_title');
    const previewCat = previewList.querySelector('[data-preview-key="categories"]');

    if (previewCat) {
        const isEn = catCheck && catCheck.checked;
        previewCat.style.display = isEn ? 'block' : 'none';

        const cTitle = document.getElementById('previewCategoriesTitle');
        if (cTitle && catTitleInput) cTitle.innerText = catTitleInput.value.trim() || 'Chuyên mục đề xuất';
    }

    // 6. Multiple Custom HTML Widgets
    document.querySelectorAll('.custom-html-widget-block').forEach(card => {
        const uId = card.getAttribute('data-widget-id');
        const enCheck = card.querySelector('.hw-input-enabled');
        const titleIn = card.querySelector('.hw-input-title');
        const contentIn = card.querySelector('.hw-input-content');
        let pBox = previewList.querySelector(`[data-preview-key="${uId}"]`);

        if (!pBox) {
            pBox = document.createElement('div');
            pBox.className = 'preview-widget-box';
            pBox.setAttribute('data-preview-key', uId);
            pBox.style.cssText = 'background: var(--card); border: 1px solid var(--border); border-radius: calc(var(--radius) * 0.75); padding: 1.25rem;';
            pBox.innerHTML = `
                <strong style="font-size: 0.875rem; color: var(--foreground); display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.35rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    <span class="preview-html-title">Widget HTML mới</span>
                </strong>
                <div class="preview-html-content" style="font-size: 0.75rem; color: var(--muted-foreground); padding: 0.5rem; background: var(--secondary); border-radius: 4px; border: 1px dashed var(--border); word-break: break-word;">
                    [Mã HTML / Widget tự do]
                </div>
            `;
            previewList.appendChild(pBox);
        }

        const isEn = enCheck && enCheck.checked;
        pBox.style.display = isEn ? 'block' : 'none';

        const titleSpan = pBox.querySelector('.preview-html-title');
        const contentDiv = pBox.querySelector('.preview-html-content');

        if (titleSpan && titleIn) titleSpan.innerText = titleIn.value.trim() || 'Tiện ích tùy chỉnh';
        if (contentDiv && contentIn) {
            const val = contentIn.value.trim();
            contentDiv.innerHTML = val || '[Mã HTML / Widget tự do]';
        }
    });

    // Check if all widgets are hidden
    const allBoxes = Array.from(previewList.querySelectorAll('.preview-widget-box'));
    const hasVisible = allBoxes.some(box => box.style.display !== 'none');
    if (emptyNotice) {
        emptyNotice.style.display = hasVisible ? 'none' : 'block';
    }
}

function previewBannerFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fnLabel = document.getElementById('bannerFileName');
        if (fnLabel) fnLabel.innerText = file.name;
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.getElementById('bannerPreviewContainer');
            const img = document.getElementById('bannerPreviewImg');
            if (img) img.src = e.target.result;
            if (box) box.style.display = 'block';

            const pImg = document.getElementById('previewBannerImg');
            const pHolder = document.getElementById('previewBannerPlaceholder');
            if (pImg) {
                pImg.src = e.target.result;
                pImg.style.display = 'block';
            }
            if (pHolder) pHolder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function previewBannerUrl(url) {
    url = url.trim();
    const box = document.getElementById('bannerPreviewContainer');
    const img = document.getElementById('bannerPreviewImg');
    const pImg = document.getElementById('previewBannerImg');
    const pHolder = document.getElementById('previewBannerPlaceholder');

    if (url) {
        if (img) img.src = url;
        if (box) box.style.display = 'block';
        if (pImg) {
            pImg.src = url;
            pImg.style.display = 'block';
        }
        if (pHolder) pHolder.style.display = 'none';
    } else {
        if (box) box.style.display = 'none';
        if (pImg) pImg.style.display = 'none';
        if (pHolder) pHolder.style.display = 'block';
    }
}

function undoRemoveBanner() {
    const rm = document.getElementById('remove_banner_image');
    if (rm) rm.value = '0';
    const box = document.getElementById('bannerCardBox');
    if (box) box.style.opacity = '1';
    const notice = document.getElementById('bannerRemovedNotice');
    if (notice) notice.style.display = 'none';
}

function confirmRemoveBanner() {
    const rm = document.getElementById('remove_banner_image');
    if (rm) rm.value = '1';
    const box = document.getElementById('bannerCardBox');
    if (box) box.style.opacity = '0.4';
    const notice = document.getElementById('bannerRemovedNotice');
    if (notice) notice.style.display = 'inline-flex';
}
