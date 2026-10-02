<?php include APP_ROOT . '/views/layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-blob auth-blob-1"></div>

    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h1 class="auth-title">Đăng nhập</h1>
            <p class="auth-subtitle">Truy cập bảng điều khiển quản trị hệ thống</p>
        </div>

        <form method="POST" action="/login" id="loginForm">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    required 
                    placeholder="Nhập tên đăng nhập..." 
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="password">Mật khẩu</label>
                </div>
                <div class="input-password-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        placeholder="Nhập mật khẩu..." 
                        autocomplete="current-password"
                    >
                    <button type="button" id="togglePasswordBtn" class="toggle-password-btn" title="Hiện/ẩn mật khẩu" aria-label="Hiện mật khẩu">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn auth-submit-btn">
                <span>Đăng nhập</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </button>
        </form>

        <div class="demo-account-box">
            <div class="demo-account-header">
                <span>Tài khoản Demo Admin</span>
                <button type="button" class="quick-fill-btn" onclick="fillCredentials('admin', 'Admin@123')">
                    Tự điền
                </button>
            </div>
            <div class="demo-account-details">
                <span>User: <code>admin</code></span>
                <span>Pass: <code>Admin@123</code></span>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCredentials(user, pass) {
        const u = document.getElementById('username');
        const p = document.getElementById('password');
        if (u && p) {
            u.value = user;
            p.value = pass;
            p.focus();
        }
    }

    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.style.display = isPassword ? 'none' : 'block';
            eyeOffIcon.style.display = isPassword ? 'block' : 'none';
        });
    }
</script>

<?php include APP_ROOT . '/views/layouts/footer.php'; ?>
