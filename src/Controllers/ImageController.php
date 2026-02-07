<?php

namespace Controllers;

use Core\Controller;
use Core\CSRF;
use Middleware\AuthMiddleware;
use Models\Image;
use Services\FileUploadService;
use Services\ImageCompositionService;

/**
 * Image Controller
 * Handles image upload actions
 */
class ImageController extends Controller
{
    protected function before(): bool
    {
        // Gallery is public, but other actions require authentication
        $protectedActions = ['upload', 'edit', 'compose', 'getUserImages', 'delete', 'getCsrfToken'];
        $action = $this->routeParams['action'] ?? '';

        if (in_array($action, $protectedActions, true)) {
            AuthMiddleware::requireAuth();
        }

        return true;
    }

    public function uploadAction(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            \Core\View::render('image/upload', [
                'title' => 'Upload Image - Camagru',
                'errors' => []
            ]);
            return;
        }

        // POST request - handle upload
        $errors = [];

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!CSRF::verify($csrfToken)) {
            $errors['general'] = 'Invalid security token. Please try again.';
            \Core\View::render('image/upload', [
                'title' => 'Upload Image - Camagru',
                'errors' => $errors
            ]);
            return;
        }

        if (!isset($_FILES['image'])) {
            $errors['general'] = 'No file uploaded.';
            \Core\View::render('image/upload', [
                'title' => 'Upload Image - Camagru',
                'errors' => $errors
            ]);
            return;
        }

        $uploader = new FileUploadService();
        $result = $uploader->uploadImage($_FILES['image']);

        if (!$result['success']) {
            $errors['general'] = $result['error'];
            \Core\View::render('image/upload', [
                'title' => 'Upload Image - Camagru',
                'errors' => $errors
            ]);
            return;
        }

        $user = AuthMiddleware::user();
        $imageModel = new Image();
        $imageId = $imageModel->create([
            'user_id' => (int) $user['id'],
            'superposable_image_id' => null,
            'filename' => $result['relativePath'],
            'original_filename' => $result['originalName'],
        ]);

        if (!$imageId) {
            $errors['general'] = 'Failed to save image metadata. Please try again.';
            \Core\View::render('image/upload', [
                'title' => 'Upload Image - Camagru',
                'errors' => $errors
            ]);
            return;
        }

        header('Location: /gallery?upload=1');
        exit;
    }

    /**
     * Show gallery of all images
     */
    public function galleryAction(): void
    {
        $imageModel = new Image();
        $images = $imageModel->findAllWithUsers();

        $successMessage = '';
        if (isset($_GET['upload'])) {
            $successMessage = 'Image uploaded successfully!';
        }

        \Core\View::render('image/gallery', [
            'title' => 'Gallery - Camagru',
            'images' => $images,
            'successMessage' => $successMessage
        ]);
    }

    /**
     * Show image editing page
     */
    public function editAction(): void
    {
        $imageModel = new Image();
        $superposableImages = $imageModel->getSuperposableImages();

        \Core\View::render('image/edit', [
            'title' => 'Create Image - Camagru',
            'superposableImages' => $superposableImages,
            'errors' => []
        ]);
    }

    /**
     * Get CSRF token (AJAX endpoint)
     */
    public function getCsrfTokenAction(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode(['csrf_token' => \Core\CSRF::token()]);
    }

    /**
     * Compose base image with overlay (AJAX endpoint)
     */
    public function composeAction(): void
    {
        // Increase memory limit for image processing
        ini_set('memory_limit', '256M');
        
        // Clear any output buffering
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');

        try {
            // Verify CSRF token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CSRF::verify($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid security token']);
                return;
            }

            // Validate inputs
            if (!isset($_FILES['base_image']) || !isset($_POST['overlay_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                return;
            }

            $overlayId = (int)$_POST['overlay_id'];
            $overlayX = (int)($_POST['overlay_x'] ?? 0);
            $overlayY = (int)($_POST['overlay_y'] ?? 0);
            $overlayWidth = (int)($_POST['overlay_width'] ?? 0);
            $overlayHeight = (int)($_POST['overlay_height'] ?? 0);
            $baseImageFile = $_FILES['base_image'];

            // Validate overlay exists
            $imageModel = new Image();
            $overlays = $imageModel->getSuperposableImages();
            $selectedOverlay = null;
            
            foreach ($overlays as $overlay) {
                if ((int)$overlay['id'] === $overlayId) {
                    $selectedOverlay = $overlay;
                    break;
                }
            }

            if (!$selectedOverlay) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid overlay selection']);
                return;
            }

            // Validate base image upload
            if ($baseImageFile['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Image upload failed']);
                return;
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $baseImageFile['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and GIF allowed']);
                return;
            }

            // Validate image dimensions
            $imageInfo = getimagesize($baseImageFile['tmp_name']);
            if (!$imageInfo) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid image file']);
                return;
            }
            
            $imgWidth = $imageInfo[0];
            $imgHeight = $imageInfo[1];
            $imgPixels = $imgWidth * $imgHeight;
            
            // Check dimensions against limits (should match ImageCompositionService limits)
            if ($imgWidth > 3000 || $imgHeight > 3000 || $imgPixels > 5000000) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Image dimensions too large. Maximum 3000x3000 pixels or 5 megapixels. Your image is ' . $imgWidth . 'x' . $imgHeight]);
                return;
            }
            $compositionService = new ImageCompositionService();
            $result = $compositionService->composeImages(
                $baseImageFile['tmp_name'],
                $selectedOverlay['filename'],
                $overlayX,
                $overlayY,
                $overlayWidth,
                $overlayHeight
            );

            if (!$result['success']) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            // Save to database
            $user = AuthMiddleware::user();
            $imageId = $imageModel->create([
                'user_id' => (int)$user['id'],
                'superposable_image_id' => $overlayId,
                'filename' => $result['relativePath'],
                'original_filename' => $baseImageFile['name']
            ]);

            if (!$imageId) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to save image metadata']);
                return;
            }

            echo json_encode([
                'success' => true,
                'imageUrl' => '/uploads/images/' . $result['relativePath'],
                'imageId' => $imageId
            ]);
        } catch (\Exception $e) {
            error_log("Image composition error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Image composition failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Get current user's images (AJAX endpoint)
     */
    public function getUserImagesAction(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $user = AuthMiddleware::user();
        $imageModel = new Image();
        $images = $imageModel->findByUserId((int)$user['id']);

        // Format images for response
        $formattedImages = array_map(function($image) {
            return [
                'id' => $image['id'],
                'url' => '/uploads/images/' . $image['filename'],
                'created_at' => $image['created_at']
            ];
        }, $images);

        echo json_encode(['success' => true, 'images' => $formattedImages]);
    }

    /**
     * Delete user's image
     */
    public function deleteAction(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // Verify CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!CSRF::verify($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid security token']);
            return;
        }

        $imageId = (int)($_POST['image_id'] ?? 0);
        if ($imageId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid image ID']);
            return;
        }

        $user = AuthMiddleware::user();
        $imageModel = new Image();
        $compositionService = new ImageCompositionService();

        // Validate ownership
        if (!$compositionService->validateImageOwnership($imageId, (int)$user['id'], $imageModel)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'You can only delete your own images']);
            return;
        }

        // Get image data before deletion
        $image = $imageModel->findById($imageId);
        
        // Delete from database
        if (!$imageModel->delete($imageId)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete image']);
            return;
        }

        // Delete file from filesystem
        if ($image && !empty($image['filename'])) {
            $compositionService->deleteImageFile($image['filename']);
        }

        echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
    }
}
