<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email AND is_active = 1');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT id, name, email, role, language, dark_mode, is_active, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role, language)
             VALUES (:name, :email, :password_hash, :role, :language)'
        );
        $stmt->execute([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
            'role'          => $data['role'] ?? 'student',
            'language'      => $data['language'] ?? 'pt',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function setResetToken(string $email, string $token, string $expiry): bool {
        $stmt = $this->db->prepare(
            'UPDATE users SET reset_token = :reset_token, reset_expiry = :reset_expiry
             WHERE email = :email AND is_active = 1'
        );
        return $stmt->execute([
            'email' => $email,
            'reset_token' => $token,
            'reset_expiry' => $expiry,
        ]);
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->db->prepare(
            'SELECT * FROM users
             WHERE reset_token = :reset_token
             AND reset_expiry >= NOW()
             AND is_active = 1'
        );
        $stmt->execute(['reset_token' => $token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updatePassword(int $id, string $passwordHash): bool {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET password_hash = :password_hash, reset_token = NULL, reset_expiry = NULL
             WHERE id = :id'
        );
        return $stmt->execute(['id' => $id, 'password_hash' => $passwordHash]);
    }

    public function findByRole(string $role): array {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, role, language, dark_mode, is_active, created_at
             FROM users
             WHERE role = :role
             ORDER BY created_at DESC'
        );
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET name = :name, email = :email, role = :role, language = :language, is_active = :is_active
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'language' => $data['language'] ?? 'pt',
            'is_active' => (int)($data['is_active'] ?? 1),
        ]);
    }

    public function deactivate(int $id): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = 0 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
