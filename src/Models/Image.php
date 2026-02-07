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
     * Get paginated images with user information
     *
     * @param int $limit Number of images per page
     * @param int $offset Starting position
     * @return array
     */
    public function findAllWithUsersPaginated(int $limit = 15, int $offset = 0): array
    {
        $sql = "SELECT 
                    i.id,
                    i.filename,
                    i.original_filename,
                    i.created_at,
                    u.id as user_id,
                    u.username,
                    (SELECT COUNT(*) FROM likes l WHERE l.image_id = i.id) as like_count,
                    (SELECT COUNT(*) FROM comments c WHERE c.image_id = i.id) as comment_count
                FROM {$this->table} i
                INNER JOIN users u ON i.user_id = u.id
                ORDER BY i.created_at DESC
                LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Failed to fetch paginated images: " . $e->getMessage());
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
     * Find image by ID with author info
     *
     * @param int $id
     * @return array|null
     */
    public function findByIdWithUser(int $id): ?array
    {
        $sql = "SELECT 
                    i.id,
                    i.filename,
                    i.original_filename,
                    i.created_at,
                    i.user_id,
                    u.username,
                    u.email,
                    u.comment_notifications
                FROM {$this->table} i
                INNER JOIN users u ON i.user_id = u.id
                WHERE i.id = :id
                LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Failed to fetch image with user: " . $e->getMessage());
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
