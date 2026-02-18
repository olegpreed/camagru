-- Migration: 001_initial_schema.sql
-- Description: Initial database schema creation
-- Created: 2024-01-XX

USE camagru_db;

-- USERS
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    verification_token VARCHAR(255) DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expires_at DATETIME DEFAULT NULL,
    pending_email VARCHAR(255) DEFAULT NULL,
    pending_email_token VARCHAR(255) DEFAULT NULL,
    pending_email_token_expires_at DATETIME DEFAULT NULL,
    comment_notifications TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SUPERPOSABLE IMAGES
CREATE TABLE IF NOT EXISTS superposable_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAGES
CREATE TABLE IF NOT EXISTS images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    superposable_image_id BIGINT UNSIGNED DEFAULT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_images_superposable
        FOREIGN KEY (superposable_image_id) REFERENCES superposable_images(id)
        ON DELETE SET NULL,
    INDEX idx_images_user_id (user_id),
    INDEX idx_images_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- LIKES
CREATE TABLE IF NOT EXISTS likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    image_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_likes_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_likes_image
        FOREIGN KEY (image_id) REFERENCES images(id)
        ON DELETE CASCADE,
    UNIQUE KEY uniq_like_user_image (user_id, image_id),
    INDEX idx_likes_image_id (image_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- COMMENTS
CREATE TABLE IF NOT EXISTS comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    image_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_comments_image
        FOREIGN KEY (image_id) REFERENCES images(id)
        ON DELETE CASCADE,
    INDEX idx_comments_image_id (image_id),
    INDEX idx_comments_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample superposable overlay images
-- Add corresponding PNG files to public/assets/overlays/
INSERT INTO superposable_images (name, filename) VALUES
('Sunglasses', 'sunglasses.png'),
('Mustache', 'mustache.png'),
('Crown', 'crown.png'),
('Heart Frame', 'heart-frame.png'),
('Star Effect', 'star-effect.png'),
('Wig', 'wig.png'),
('South Park Hat', 'south-park-hat.png'),
('Kanye Glasses', 'kanye.png');