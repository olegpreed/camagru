<?php

namespace Services;

/**
 * ImageCompositionService
 * Handles server-side image composition and manipulation
 */
class ImageCompositionService
{
    private string $uploadBasePath;
    private string $overlayBasePath;

    public function __construct()
    {
        $this->uploadBasePath = __DIR__ . '/../../public/uploads/images/';
        $this->overlayBasePath = __DIR__ . '/../../public/assets/overlays/';
    }

    /**
     * Compose a base image with a superposable overlay
     *
     * @param string $baseImageTmpPath Temporary path of uploaded base image
     * @param string $overlayFilename Filename of the overlay image
     * @param int $overlayX X position of overlay
     * @param int $overlayY Y position of overlay
     * @return array Result with success status and data
     */
    public function composeImages(string $baseImageTmpPath, string $overlayFilename, int $overlayX = 0, int $overlayY = 0): array
    {
        // Validate inputs
        if (!file_exists($baseImageTmpPath)) {
            return ['success' => false, 'error' => 'Base image not found'];
        }

        $overlayPath = $this->overlayBasePath . $overlayFilename;
        if (!file_exists($overlayPath)) {
            return ['success' => false, 'error' => 'Overlay image not found'];
        }

        try {
            // Load base image
            $baseImage = $this->loadImage($baseImageTmpPath);
            if (!$baseImage) {
                return ['success' => false, 'error' => 'Failed to load base image'];
            }

            // Load overlay image
            $overlayImage = $this->loadImage($overlayPath);
            if (!$overlayImage) {
                imagedestroy($baseImage);
                return ['success' => false, 'error' => 'Failed to load overlay image'];
            }

            // Resize images to a standard size (e.g., 800x600)
            $standardWidth = 800;
            $standardHeight = 600;
            $resizedBase = $this->resizeImage($baseImage, $standardWidth, $standardHeight);
            $resizedOverlay = with positioning
            imagecopy(
                $resizedBase,
                $resizedOverlay,
                $overlayX,
                $overlayY,
                0,
                0,
                imagesx($resizedOverlay),
                imagesy($resizedOverlay)
            
            // Compose images
            imagecopy($resizedBase, $resizedOverlay, 0, 0, 0, 0, $standardWidth, $standardHeight);

            // Generate unique filename
            $filename = $this->generateFilename();
            $relativePath = $this->getRelativeUploadPath() . $filename;
            $absolutePath = $this->uploadBasePath . $relativePath;

            // Ensure directory exists
            $dir = dirname($absolutePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Save composed image
            $saved = imagepng($resizedBase, $absolutePath, 9);

            // Clean up
            imagedestroy($baseImage);
            imagedestroy($overlayImage);
            imagedestroy($resizedBase);
            imagedestroy($resizedOverlay);

            if (!$saved) {
                return ['success' => false, 'error' => 'Failed to save composed image'];
            }

            return [
                'success' => true,
                'relativePath' => $relativePath,
                'absolutePath' => $absolutePath
            ];

        } catch (\Exception $e) {
            error_log("Image composition error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Image composition failed'];
        }
    }

    /**
     * Load an image from file based on its type
     *
     * @param string $path
     * @return \GdImage|false
     */
    private function loadImage(string $path)
    {
        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];

        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            default => false,
        };
    }

    /**
     * Resize image to specified dimensions
     *
     * @param \GdImage $image
     * @param int $width
     * @param int $height
     * @return \GdImage
     */
    private function resizeImage($image, int $width, int $height)
    {
        $resized = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
        imagealphablending($resized, true);

        $srcWidth = imagesx($image);
        $srcHeight = imagesy($image);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

        return $resized;
    }

    /**
     * Generate unique filename for composed image
     *
     * @return string
     */
    private function generateFilename(): string
    {
        return uniqid('composed_', true) . '.png';
    }

    /**
     * Get relative upload path based on current date
     *
     * @return string
     */
    private function getRelativeUploadPath(): string
    {
        return date('Y') . '/' . date('m') . '/';
    }

    /**
     * Delete an image file from the filesystem
     *
     * @param string $relativePath
     * @return bool
     */
    public function deleteImageFile(string $relativePath): bool
    {
        $absolutePath = $this->uploadBasePath . $relativePath;
        
        if (file_exists($absolutePath)) {
            return unlink($absolutePath);
        }
        
        return false;
    }

    /**
     * Validate that a user owns an image
     *
     * @param int $imageId
     * @param int $userId
     * @param \Models\Image $imageModel
     * @return bool
     */
    public function validateImageOwnership(int $imageId, int $userId, \Models\Image $imageModel): bool
    {
        $image = $imageModel->findById($imageId);
        
        if (!$image) {
            return false;
        }
        
        return (int)$image['user_id'] === $userId;
    }
}
