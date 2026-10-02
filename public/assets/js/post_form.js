// Generate slug from title
function generateSlugFromTitle() {
    const titleVal = document.getElementById('title').value;
    if (!titleVal) return;
    
    let slug = titleVal.toLowerCase().trim();
    // Remove Vietnamese accents
    slug = slug.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    slug = slug.replace(/[đĐ]/g, 'd');
    slug = slug.replace(/[^a-z0-9\s-]/g, '');
    slug = slug.replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
    
    const slugInput = document.getElementById('slug');
    slugInput.value = slug;
    syncSerpPreview();
}

// Real-time image preview
function updateImagePreview(url) {
    const box = document.getElementById('imagePreviewBox');
    const img = document.getElementById('imagePreviewTag');
    if (url && url.trim() !== '') {
        img.src = url.trim();
        box.style.display = 'flex';
    } else {
        box.style.display = 'none';
    }
}

// Real-time Google SERP Simulation sync
function syncSerpPreview() {
    const titleInput = document.getElementById('title')?.value || '';
    const slugInput = document.getElementById('slug')?.value || '';
    const metaTitleInput = document.getElementById('meta_title')?.value || '';
    const metaDescInput = document.getElementById('meta_description')?.value || '';
    
    // Update Title
    const serpTitle = document.getElementById('serpTitlePreview');
    if (serpTitle) {
        serpTitle.innerText = metaTitleInput.trim() || titleInput.trim() || 'Tiêu đề bài viết';
    }

    // Update Slug
    const serpSlug = document.getElementById('serpSlugPreview');
    if (serpSlug) {
        serpSlug.innerText = slugInput.trim() || 'duong-dan';
    }

    // Update Desc
    const serpDesc = document.getElementById('serpDescPreview');
    if (serpDesc) {
        serpDesc.innerText = metaDescInput.trim() || 'Mô tả tóm tắt nội dung bài viết sẽ hiển thị ở đây trên kết quả tìm kiếm Google...';
    }

    // Update Char Count
    const countSpan = document.getElementById('descCharCount');
    if (countSpan) {
        const len = metaDescInput.length;
        countSpan.innerText = len + '/160';
        if (len > 160) {
            countSpan.style.color = 'var(--destructive)';
            countSpan.style.fontWeight = '700';
        } else {
            countSpan.style.color = 'var(--muted-foreground)';
            countSpan.style.fontWeight = 'normal';
        }
    }
}

// Handle file upload preview for featured image
function previewFeaturedImageFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fnLabel = document.getElementById('featuredImageFileName');
        if (fnLabel) fnLabel.innerText = file.name;
        const reader = new FileReader();
        reader.onload = function(e) {
            const box = document.getElementById('imagePreviewBox');
            const img = document.getElementById('imagePreviewTag');
            if (img) img.src = e.target.result;
            if (box) box.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
}

// Init count on page load
document.addEventListener("DOMContentLoaded", function() {
    syncSerpPreview();
});
