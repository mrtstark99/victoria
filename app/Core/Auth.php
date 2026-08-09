<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Auth
{
    public function __construct(private readonly PDO $database)
    {
    }

    public function user(): ?array
    {
        $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            return null;
        }
        $statement = $this->database->prepare(
            'SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function register(string $name, string $email, string $password): array
    {
        $errors = $this->validate($name, $email, $password);
        if ($errors !== []) {
            return $errors;
        }
        $exists = $this->database->prepare('SELECT 1 FROM users WHERE email = :email');
        $exists->execute(['email' => $email]);
        if ($exists->fetchColumn()) {
            return ['email' => 'Email này đã được sử dụng.'];
        }
        $statement = $this->database->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)'
        );
        $statement->execute([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $this->database->lastInsertId();
        return [];
    }

    public function login(string $email, string $password): bool
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $update = $this->database->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $update->execute(['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        unset($_SESSION['login_attempts']);
        return true;
    }

    public function tooManyAttempts(string $email, string $ip): bool
    {
        $key = $this->attemptKey($email, $ip);
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM login_attempts WHERE attempt_key = :key AND attempted_at >= :since'
        );
        $statement->execute(['key' => $key, 'since' => time() - 900]);
        return (int) $statement->fetchColumn() >= 5;
    }

    public function recordFailedAttempt(string $email, string $ip): void
    {
        $this->database->prepare('DELETE FROM login_attempts WHERE attempted_at < :expiry')
            ->execute(['expiry' => time() - 86400]);
        $this->database->prepare(
            'INSERT INTO login_attempts (attempt_key, attempted_at) VALUES (:key, :time)'
        )->execute(['key' => $this->attemptKey($email, $ip), 'time' => time()]);
    }

    public function clearAttempts(string $email, string $ip): void
    {
        $this->database->prepare('DELETE FROM login_attempts WHERE attempt_key = :key')
            ->execute(['key' => $this->attemptKey($email, $ip)]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], '', (bool) $params['secure'], true);
        }
        session_destroy();
    }

    private function validate(string $name, string $email, string $password): array
    {
        $errors = [];
        if (strlen(trim($name)) < 2 || strlen(trim($name)) > 160) {
            $errors['name'] = 'Họ tên phải có từ 2 đến 80 ký tự.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $errors['email'] = 'Email không hợp lệ.';
        }
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $errors['password'] = 'Mật khẩu cần ít nhất 8 ký tự, gồm chữ và số.';
        }
        return $errors;
    }

    private function attemptKey(string $email, string $ip): string
    {
        return hash('sha256', strtolower(trim($email)) . '|' . $ip);
    }
}
