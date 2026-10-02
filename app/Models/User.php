<?php
/**
 * User Database Model
 */

namespace Models;

use Database;
use PDO;

class User {
    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByUsername($username) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function findByEmail($email) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($username, $email, $password, $fullName, $role = 'subscriber') {
        $db = Database::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, full_name, role, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        return $stmt->execute([$username, $email, $hash, $fullName, $role]);
    }

    public static function updatePassword($id, $newPassword) {
        $db = Database::getInstance();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = datetime('now','localtime') WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public static function updateProfile($id, $fullName, $email, $username = null, $avatar = null, $bio = null) {
        $db = Database::getInstance();
        $fields = [
            'full_name = ?',
            'email = ?',
            "updated_at = datetime('now','localtime')"
        ];
        $params = [$fullName, $email];

        if ($username !== null && $username !== '') {
            $fields[] = 'username = ?';
            $params[] = $username;
        }

        if ($avatar !== null) {
            $fields[] = 'avatar = ?';
            $params[] = $avatar;
        }

        if ($bio !== null) {
            $fields[] = 'bio = ?';
            $params[] = $bio;
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY full_name ASC");
        return $stmt->fetchAll();
    }
}
