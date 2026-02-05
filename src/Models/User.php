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
     * Find user by username OR email (login helper)
     */
    public function findByLogin(string $login): ?array
    {
        // Note: with PDO::ATTR_EMULATE_PREPARES = false, reusing the same named placeholder
        // multiple times can trigger "SQLSTATE[HY093]: Invalid parameter number" in MySQL.
        // Use distinct placeholders instead.
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :login_username OR email = :login_email LIMIT 1");
        $stmt->execute([
            'login_username' => $login,
            'login_email' => $login,
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Mark verification token null when verified is already done.
     * (No change needed here unless you want extra helpers later.)
     */

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

    /**
     * Find user by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Find user by reset token
     * 
     * @param string $token
     * @return array|null
     */
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE reset_token = :token 
             AND reset_token_expires_at > NOW()"
        );
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Set password reset token
     * 
     * @param int $userId
     * @param string $token
     * @param string $expiresAt (format: 'Y-m-d H:i:s')
     * @return bool
     */
    public function setResetToken(int $userId, string $token, string $expiresAt): bool
    {
        $sql = "UPDATE {$this->table} 
                SET reset_token = :token, reset_token_expires_at = :expires_at 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'token' => $token,
                'expires_at' => $expiresAt,
                'id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to set reset token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear password reset token
     * 
     * @param int $userId
     * @return bool
     */
    public function clearResetToken(int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET reset_token = NULL, reset_token_expires_at = NULL 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $userId]);
        } catch (PDOException $e) {
            error_log("Failed to clear reset token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user password
     * 
     * @param int $userId
     * @param string $passwordHash
     * @return bool
     */
    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $sql = "UPDATE {$this->table} 
                SET password_hash = :password_hash, updated_at = NOW() 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'password_hash' => $passwordHash,
                'id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to update password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile (username and email)
     * 
     * @param int $userId
     * @param string $username
     * @param string $email
     * @return bool
     */
    public function updateProfile(int $userId, string $username, string $email): bool
    {
        $sql = "UPDATE {$this->table} 
                SET username = :username, email = :email, updated_at = NOW() 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to update profile: " . $e->getMessage());
            return false;
        }
    }
}