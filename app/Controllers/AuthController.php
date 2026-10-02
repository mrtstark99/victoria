<?php
/**
 * Auth controller logic: handling user logins/logouts
 */

namespace Controllers;

use Models\User;

class AuthController {
    public function showLogin() {
        if (isLoggedIn()) {
            if (isEditor()) {
                redirect('/admin/dashboard');
            } else {
                redirect('/', 'Bạn đã đăng nhập.', 'info');
            }
        }
        view('auth/login', [
            'page_title' => 'Đăng nhập hệ thống',
            'page_css' => 'auth'
        ]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCSRFToken($token)) {
            redirect('/login', 'Phiên làm việc hết hạn, vui lòng đăng nhập lại.', 'error');
        }

        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $clientIp = getClientIP();

        // Check Brute Force lockout
        $db = \Database::getInstance();
        $lockoutSeconds = defined('LOGIN_LOCKOUT_TIME') ? LOGIN_LOCKOUT_TIME : 900;
        $maxAttempts = defined('MAX_LOGIN_ATTEMPTS') ? MAX_LOGIN_ATTEMPTS : 5;

        // Cleanup old attempts (> lockout period)
        $db->prepare("DELETE FROM login_attempts WHERE attempted_at < datetime('now', '-' || ? || ' seconds')")
           ->execute([$lockoutSeconds]);

        // Count recent failed attempts for this IP or username
        $stmtAttempts = $db->prepare("
            SELECT COUNT(*) FROM login_attempts 
            WHERE (ip_address = ? OR (username = ? AND username != ''))
              AND attempted_at >= datetime('now', '-' || ? || ' seconds')
        ");
        $stmtAttempts->execute([$clientIp, $username, $lockoutSeconds]);
        $failCount = (int)$stmtAttempts->fetchColumn();

        if ($failCount >= $maxAttempts) {
            $lockoutMinutes = ceil($lockoutSeconds / 60);
            redirect('/login', "Bạn đã thử đăng nhập sai quá nhiều lần ({$failCount}/{$maxAttempts}). Vui lòng thử lại sau {$lockoutMinutes} phút.", 'error');
        }

        $user = User::findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            // Record failed attempt
            $stmtRecord = $db->prepare("INSERT INTO login_attempts (ip_address, username) VALUES (?, ?)");
            $stmtRecord->execute([$clientIp, $username]);
            
            $remaining = max(0, $maxAttempts - ($failCount + 1));
            $msg = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
            if ($remaining > 0) {
                $msg .= " Còn lại {$remaining} lần thử.";
            }
            redirect('/login', $msg, 'error');
        }

        if ($user['status'] !== 'active') {
            redirect('/login', 'Tài khoản của bạn đã bị khóa.', 'error');
        }

        // Clear failed attempts on successful login
        $db->prepare("DELETE FROM login_attempts WHERE ip_address = ? OR username = ?")
           ->execute([$clientIp, $username]);

        // Session fixation protection
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        // Write audit log
        \Models\AgentToken::logAudit($user['id'], 'login', 'users', $user['id'], null, ['status' => 'logged_in']);

        if (in_array($user['role'], ['admin', 'editor'])) {
            redirect('/admin/dashboard', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        } else {
            redirect('/', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        }
    }

    public function logout() {
        if (isLoggedIn()) {
            $userId = $_SESSION['user_id'];
            \Models\AgentToken::logAudit($userId, 'logout', 'users', $userId, null, ['status' => 'logged_out']);
        }
        
        session_destroy();
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
        
        // Start a new session to send flash message
        session_start();
        redirect('/login', 'Bạn đã đăng xuất thành công.');
    }
}
