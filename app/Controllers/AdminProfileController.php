<?php
/**
 * Admin Profile Controller
 * Handles user profile update and password change.
 */

namespace Controllers;

use Models\User;

class AdminProfileController {

    public function profile() {
        requireEditor();
        $db = \Database::getInstance();
        $userId = $_SESSION['user_id'] ?? 0;
        $user = User::findById($userId);

        if (!$user) {
            redirect('/login', 'Không tìm thấy thông tin tài khoản.', 'error');
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ?");
        $stmt->execute([$userId]);
        $authoredCount = (int)$stmt->fetchColumn();

        view('admin/profile', [
            'user'           => $user,
            'authored_count' => $authoredCount,
            'page_title'     => 'Cài đặt tài khoản Profile'
        ]);
    }

    public function updateProfile() {
        requireEditor();
        $db = \Database::getInstance();
        $userId = $_SESSION['user_id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!verifyCSRFToken($token)) {
                redirect('/admin/profile', 'Phiên làm việc hết hạn, vui lòng thử lại.', 'error');
            }

            $fullName = sanitizeInput($_POST['full_name'] ?? '');
            $email    = sanitizeInput($_POST['email'] ?? '');
            $username = sanitizeInput($_POST['username'] ?? '');
            $bio = trim((string)($_POST['bio'] ?? ''));

            if (empty($fullName)) redirect('/admin/profile', 'Họ và tên không được để trống.', 'error');
            if (empty($email) || !validateEmail($email)) redirect('/admin/profile', 'Địa chỉ email không hợp lệ.', 'error');
            if (empty($username)) redirect('/admin/profile', 'Tên đăng nhập không được để trống.', 'error');
            if (mb_strlen($bio, 'UTF-8') > 1000) redirect('/admin/profile', 'Giới thiệu tác giả không được vượt quá 1.000 ký tự.', 'error');

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) redirect('/admin/profile', 'Địa chỉ email này đã được sử dụng bởi tài khoản khác.', 'error');

            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $userId]);
            if ($stmt->fetch()) redirect('/admin/profile', 'Tên đăng nhập này đã tồn tại.', 'error');

            // Avatar upload / URL
            $avatar = null;
            if (!empty($_POST['remove_avatar'])) {
                $avatar = '';
            } elseif (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar_file'];
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ALLOWED_IMAGE_TYPES)) {
                    redirect('/admin/profile', 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận: ' . implode(', ', ALLOWED_IMAGE_TYPES), 'error');
                }
                if ($file['size'] > MAX_FILE_SIZE) {
                    redirect('/admin/profile', 'Kích thước ảnh vượt quá 5MB.', 'error');
                }
                $avatarDir = UPLOAD_PATH . 'avatars/';
                if (!is_dir($avatarDir)) mkdir($avatarDir, 0777, true);
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $avatarDir . $filename)) {
                    $avatar = '/uploads/avatars/' . $filename;
                }
            } elseif (!empty($_POST['avatar_url'])) {
                $avatar = trim($_POST['avatar_url']);
            }

            User::updateProfile($userId, $fullName, $email, $username, $avatar, $bio);
            $_SESSION['user_name'] = $fullName;
            if ($avatar !== null) $_SESSION['user_avatar'] = $avatar;

            $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, ip_address, user_agent) VALUES (?, 'UPDATE_PROFILE', 'users', ?, ?, ?)");
            $stmtLog->execute([$userId, $userId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);

            redirect('/admin/profile', 'Cập nhật thông tin tài khoản và ảnh đại diện thành công.');
        }
        redirect('/admin/profile');
    }

    public function updatePassword() {
        requireEditor();
        $db = \Database::getInstance();
        $userId = $_SESSION['user_id'] ?? 0;
        $user   = User::findById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!verifyCSRFToken($token)) {
                redirect('/admin/profile', 'Phiên làm việc hết hạn, vui lòng thử lại.', 'error');
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                redirect('/admin/profile', 'Vui lòng nhập đầy đủ mật khẩu hiện tại và mật khẩu mới.', 'error');
            }
            if (!password_verify($currentPassword, $user['password'])) {
                redirect('/admin/profile', 'Mật khẩu hiện tại không chính xác.', 'error');
            }
            if (strlen($newPassword) < 6) {
                redirect('/admin/profile', 'Mật khẩu mới phải có độ dài ít nhất 6 ký tự.', 'error');
            }
            if ($newPassword !== $confirmPassword) {
                redirect('/admin/profile', 'Xác nhận mật khẩu mới không khớp.', 'error');
            }

            User::updatePassword($userId, $newPassword);

            $stmtLog = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, ip_address, user_agent) VALUES (?, 'CHANGE_PASSWORD', 'users', ?, ?, ?)");
            $stmtLog->execute([$userId, $userId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);

            redirect('/admin/profile', 'Đổi mật khẩu thành công. Mật khẩu mới đã được áp dụng.');
        }
        redirect('/admin/profile');
    }
}
