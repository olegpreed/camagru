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
}
