<?php

namespace Models;

use Core\Model;
use PDO;
use PDOException;

/**
 * User Model
 * Handles user-related database operations
 */
class User extends Model
{
    protected string $table = 'users';

    /**
     * Create a new user
     * 
     * @param array $data User data (username, email, password_hash, verification_token)
     * @return int|false User ID on success, false on failure
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO {$this->table} 
                (username, email, password_hash, verification_token, created_at) 
                VALUES (:username, :email, :password_hash, :verification_token, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
                'verification_token' => $data['verification_token']
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Find user by username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Find user by verification token
     * 
     * @param string $token
     * @return array|null
     */
    public function findByVerificationToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE verification_token = :token");
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Verify user account
     * 
     * @param int $userId
     * @return bool
     */
    public function verify(int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET is_verified = 1, verification_token = NULL 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $userId]);
        } catch (PDOException $e) {
            error_log("User verification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate email format
     * 
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate username
     * 
     * @param string $username
     * @return bool
     */
    public static function validateUsername(string $username): bool
    {
        // Username: 3-50 characters, alphanumeric and underscores only
        return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username) === 1;
    }

    /**
     * Validate password complexity
     * 
     * @param string $password
     * @return array ['valid' => bool, 'errors' => []]
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}