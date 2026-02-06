<?php

namespace Models;

use Core\Model;
use PDOException;

/**
 * Image Model
 * Handles image-related database operations
 */
class Image extends Model
{
    protected string $table = 'images';

    /**
     * Create a new image record
     *
     * @param array $data Image data
     * @return int|false Image ID on success, false on failure
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO {$this->table}
                (user_id, superposable_image_id, filename, original_filename, created_at)
                VALUES (:user_id, :superposable_image_id, :filename, :original_filename, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => (int) $data['user_id'],
                'superposable_image_id' => $data['superposable_image_id'] ?? null,
                'filename' => $data['filename'],
                'original_filename' => $data['original_filename'] ?? null,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Image creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find all images with user information, ordered by newest first
     *
     * @return array
     */
    public function findAllWithUsers(): array
    {
        $sql = "SELECT 
                    i.id,
                    i.filename,
                    i.original_filename,
                    i.created_at,
                    u.id as user_id,
                    u.username
                FROM {$this->table} i
                INNER JOIN users u ON i.user_id = u.id
                ORDER BY i.created_at DESC";

        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Failed to fetch images: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all images for a specific user
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Failed to fetch user images: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all available superposable overlay images
     *
     * @return array
     */
    public function getSuperposableImages(): array
    {
        $sql = "SELECT * FROM superposable_images ORDER BY id ASC";

        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Failed to fetch superposable images: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find image by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Failed to fetch image: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("Failed to delete image: " . $e->getMessage());
            return false;
        }
    }
}
