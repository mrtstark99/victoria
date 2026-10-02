<?php 
include APP_ROOT . '/views/layouts/admin_header.php'; 
$userInitial = strtoupper(mb_substr($user['full_name'] ?? 'A', 0, 1, 'UTF-8'));
$hasAvatar = !empty($user['avatar']);
?>

<div class="grid-layout columns-1-2">
    <!-- Left Column: User Summary Card -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card" style="margin-bottom: 0; text-align: center; display: flex; flex-direction: column; align-items: center; padding: 2rem 1.5rem;">
            <!-- Big Avatar -->
            <div id="profileAvatarPreviewWrap" style="width: 88px; height: 88px; border-radius: 50%; background: var(--foreground); color: var(--background); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; margin-bottom: 1rem; box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.15); overflow: hidden; border: 3px solid var(--border);">
                <?php if ($hasAvatar): ?>
                    <img id="profileAvatarImg" src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.innerHTML='<div id=\'profileAvatarInitials\'><?php echo htmlspecialchars($userInitial); ?></div>';">
                <?php else: ?>
                    <div id="profileAvatarInitials"><?php echo htmlspecialchars($userInitial); ?></div>
                <?php endif; ?>
            </div>

            <h2 style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.25rem; color: var(--foreground);">
                <?php echo htmlspecialchars($user['full_name']); ?>
            </h2>
            <span style="font-size: 0.85rem; color: var(--muted-foreground); margin-bottom: 0.75rem;">
                @<?php echo htmlspecialchars($user['username']); ?>
            </span>

            <span class="badge badge-published" style="margin-bottom: 1.5rem; text-transform: uppercase;">
                ● <?php echo htmlspecialchars($user['role']); ?>
            </span>

            <div style="width: 100%; border-top: 1px solid var(--border); padding-top: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; text-align: left; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-foreground);">Email:</span>
                    <strong style="color: var(--foreground);"><?php echo htmlspecialchars($user['email']); ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-foreground);">Bài viết đã đăng:</span>
                    <span class="badge-pill-tag"><strong><?php echo number_format($authored_count); ?></strong> bài</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-foreground);">Ngày khởi tạo:</span>
                    <span style="color: var(--foreground);"><?php echo formatDate($user['created_at'], 'd/m/Y'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-foreground);">Trạng thái:</span>
                    <span style="color: oklch(0.5 0.18 145); font-weight: 700;">Đang hoạt động</span>
                </div>
            </div>
        </div>

        <!-- Security Tips Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="margin-bottom: 0.75rem; padding-bottom: 0.5rem;">
                <h4 class="card-title" style="font-size: 0.9rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Lưu ý bảo mật</span>
                </h4>
            </div>
            <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.8rem; color: var(--muted-foreground); line-height: 1.6;">
                <li>Sử dụng mật khẩu mạnh có từ 8 ký tự trở lên.</li>
                <li>Không chia sẻ tài khoản đăng nhập hoặc mã Token AI Agent cho người khác.</li>
                <li>Đăng xuất sau khi hoàn thành phiên làm việc trên thiết bị lạ.</li>
            </ul>
        </div>
    </div>

    <!-- Right Column: Edit Forms -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Card 1: Update Profile Details & Avatar -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Thông tin cá nhân &amp; Ảnh đại diện</span>
                </h3>
            </div>

            <form method="POST" action="/admin/profile" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                
                <!-- Avatar Upload Section -->
                <div class="form-group" style="padding-bottom: 1.25rem; border-bottom: 1px solid var(--border); margin-bottom: 1.25rem;">
                    <label style="font-weight: 700; margin-bottom: 0.5rem; display: block;">Ảnh đại diện (Avatar)</label>
                    
                    <div style="display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
                        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--secondary); border: 2px dashed var(--border); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                            <?php if ($hasAvatar): ?>
                                <img id="formAvatarPreview" src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <img id="formAvatarPreview" src="" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                <span id="formAvatarPlaceholder" style="font-size: 1.4rem; font-weight: 700; color: var(--muted-foreground);"><?php echo htmlspecialchars($userInitial); ?></span>
                            <?php endif; ?>
                        </div>

                        <div style="flex-grow: 1; min-width: 240px; display: flex; flex-direction: column; gap: 0.5rem;">
                            <!-- File Upload Input -->
                            <div>
                                <label for="avatar_file" class="btn btn-secondary btn-sm" style="cursor: pointer; display: inline-flex;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span>Tải ảnh lên từ máy tính...</span>
                                </label>
                                <input 
                                    type="file" 
                                    id="avatar_file" 
                                    name="avatar_file" 
                                    accept="image/jpeg,image/png,image/webp,image/gif" 
                                    style="display: none;" 
                                    onchange="previewAvatarFile(this)"
                                >
                                <span id="avatarFileName" style="font-size: 0.75rem; color: var(--muted-foreground); margin-left: 0.5rem;"></span>
                            </div>

                            <!-- Or URL input -->
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input 
                                    type="text" 
                                    id="avatar_url" 
                                    name="avatar_url" 
                                    class="form-control" 
                                    value="<?php echo htmlspecialchars($user['avatar'] ?? ''); ?>" 
                                    placeholder="Hoặc dán URL ảnh đại diện (https://...)..."
                                    style="font-size: 0.8rem; padding: 0.4rem 0.75rem;"
                                    oninput="previewAvatarUrl(this.value)"
                                >
                            </div>

                            <?php if ($hasAvatar): ?>
                                <label style="font-size: 0.8rem; color: var(--destructive); cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                    <input type="checkbox" name="remove_avatar" value="1">
                                    <span>Xóa ảnh đại diện hiện tại</span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="full_name">Họ và tên hiển thị <span style="color:var(--destructive)">*</span></label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        class="form-control" 
                        required 
                        value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                        placeholder="Nhập họ và tên..."
                    >
                </div>

                <div class="form-group">
                    <label for="bio">Giới thiệu tác giả</label>
                    <textarea id="bio" name="bio" class="form-control" rows="4" maxlength="1000" placeholder="Giới thiệu ngắn về tác giả; nội dung này hiển thị trong author bio của bài viết."><?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <small style="color: var(--muted-foreground);">Tối đa 1.000 ký tự. Bio sẽ xuất hiện trong thẻ tác giả ở cuối bài viết.</small>
                </div>

                <div class="grid-layout columns-2" style="margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="email">Địa chỉ Email <span style="color:var(--destructive)">*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            required 
                            value="<?php echo htmlspecialchars($user['email']); ?>" 
                            placeholder="admin@domain.com"
                        >
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="username">Tên đăng nhập <span style="color:var(--destructive)">*</span></label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            required 
                            value="<?php echo htmlspecialchars($user['username']); ?>" 
                            placeholder="admin"
                        >
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <button type="submit" class="btn">Lưu thông tin</button>
                </div>
            </form>
        </div>

        <!-- Card 2: Change Password -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Đổi mật khẩu bảo mật</span>
                </h3>
            </div>

            <form method="POST" action="/admin/profile/password" id="passwordForm">
                <?php echo csrfField(); ?>

                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại <span style="color:var(--destructive)">*</span></label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="form-control" 
                        required 
                        placeholder="Nhập mật khẩu đang sử dụng..."
                        autocomplete="current-password"
                    >
                </div>

                <div class="grid-layout columns-2" style="margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="new_password">Mật khẩu mới <span style="color:var(--destructive)">*</span></label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            class="form-control" 
                            required 
                            placeholder="Tối thiểu 6 ký tự..."
                            autocomplete="new-password"
                        >
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="confirm_password">Xác nhận mật khẩu mới <span style="color:var(--destructive)">*</span></label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-control" 
                            required 
                            placeholder="Nhập lại mật khẩu mới..."
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <button type="submit" class="btn">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewAvatarFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('avatarFileName').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('formAvatarPreview');
                const placeholder = document.getElementById('formAvatarPlaceholder');
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';

                // Also update left summary preview
                const leftWrap = document.getElementById('profileAvatarPreviewWrap');
                leftWrap.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">';
            };
            reader.readAsDataURL(file);
        }
    }

    function previewAvatarUrl(url) {
        url = url.trim();
        const previewImg = document.getElementById('formAvatarPreview');
        const placeholder = document.getElementById('formAvatarPlaceholder');
        const leftWrap = document.getElementById('profileAvatarPreviewWrap');

        if (url) {
            previewImg.src = url;
            previewImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            leftWrap.innerHTML = '<img src="' + url + '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">';
        }
    }
</script>

<?php include APP_ROOT . '/views/layouts/admin_footer.php'; ?>
