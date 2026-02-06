<?php

namespace Controllers;

use Core\Controller;
use Core\CSRF;
use Middleware\AuthMiddleware;
use Models\Image;
use Services\FileUploadService;

/**
 * Image Controller
 * Handles image upload actions
 */
class ImageController extends Controller
{
    protected function before(): bool
    {
        // Gallery is public, but upload requires authentication
        $protectedActions = ['upload'];
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
}
