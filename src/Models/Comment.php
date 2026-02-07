<?php

namespace Models;

use Core\Model;
use PDOException;

/**
 * Comment Model
 * Handles image comments
 */
class Comment extends Model
{
    protected string $table = 'comments';

    public function countByImageId(int $imageId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE image_id = :image_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['image_id' => $imageId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Failed to count comments: " . $e->getMessage());
            return 0;
        }
    }

    public function create(int $imageId, int $userId, string $content): int|false
    {
        $sql = "INSERT INTO {$this->table} (user_id, image_id, content, created_at)
                VALUES (:user_id, :image_id, :content, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'image_id' => $imageId,
                'content' => $content
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Failed to create comment: " . $e->getMessage());
            return false;
        }
    }

    public function getByImageId(int $imageId): array
    {
        $sql = "SELECT c.id, c.content, c.created_at, u.id as user_id, u.username
                FROM {$this->table} c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.image_id = :image_id
                ORDER BY c.created_at ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['image_id' => $imageId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch comments: " . $e->getMessage());
            return [];
        }
    }
}
