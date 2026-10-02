// Reading Progress Bar handler
window.addEventListener('scroll', function() {
    const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    const bar = document.getElementById('readingProgressBar');
    if (bar) {
        bar.style.width = scrolled + '%';
    }
});

// In-Article TOC Toggle
function toggleTOC() {
    const list = document.getElementById('tocList');
    const text = document.getElementById('tocToggleText');
    if (!list || !text) return;
    if (list.style.display === 'none') {
        list.style.display = 'flex';
        text.innerText = 'Thu gọn';
    } else {
        list.style.display = 'none';
        text.innerText = 'Mở rộng';
    }
}

// Copy Link function with toast
function copyPostLink() {
    if (navigator.clipboard && window.isSecureContext && typeof navigator.clipboard.writeText === 'function') {
        navigator.clipboard.writeText(window.location.href).then(showToast).catch(function() {
            fallbackCopyUrl();
        });
    } else {
        fallbackCopyUrl();
    }
}

function fallbackCopyUrl() {
    try {
        const tempInput = document.createElement('input');
        tempInput.value = window.location.href;
        tempInput.style.position = 'fixed';
        tempInput.style.left = '-9999px';
        tempInput.style.top = '0';
        tempInput.style.opacity = '0.01';
        document.body.appendChild(tempInput);
        tempInput.focus();
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        showToast();
    } catch (e) {
        prompt('Vui lòng bấm Ctrl+C để sao chép liên kết:', window.location.href);
    }
}

function showToast() {
    const toast = document.getElementById('copyToast');
    if (toast) {
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }, 2500);
    }
}
