<?php

namespace Models;

use Core\Model;
use PDOException;

/**
 * Like Model
 * Handles image likes
 */
class Like extends Model
{
    protected string $table = 'likes';

    public function countByImageId(int $imageId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE image_id = :image_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['image_id' => $imageId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Failed to count likes: " . $e->getMessage());
            return 0;
        }
    }

    public function isLikedByUser(int $imageId, int $userId): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE image_id = :image_id AND user_id = :user_id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'image_id' => $imageId,
                'user_id' => $userId
            ]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Failed to check like: " . $e->getMessage());
            return false;
        }
    }

    public function toggleLike(int $imageId, int $userId): array
    {
        $liked = $this->isLikedByUser($imageId, $userId);

        try {
            if ($liked) {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE image_id = :image_id AND user_id = :user_id");
                $stmt->execute([
                    'image_id' => $imageId,
                    'user_id' => $userId
                ]);
                $liked = false;
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, image_id, created_at) VALUES (:user_id, :image_id, NOW())");
                $stmt->execute([
                    'user_id' => $userId,
                    'image_id' => $imageId
                ]);
                $liked = true;
            }
        } catch (PDOException $e) {
            error_log("Failed to toggle like: " . $e->getMessage());
        }

        return [
            'liked' => $liked,
            'count' => $this->countByImageId($imageId)
        ];
    }
}
