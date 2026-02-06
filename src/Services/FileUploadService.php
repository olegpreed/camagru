<?php

namespace Services;

use finfo;

/**
 * File Upload Service
 * Validates and stores uploaded image files
 */
class FileUploadService
{
    private int $maxSize;
    private array $allowedMimeTypes;
    private string $baseUploadDir;

    public function __construct()
    {
        $this->maxSize = 5 * 1024 * 1024; // 5 MB
        $this->allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];
        $this->baseUploadDir = rtrim(__DIR__ . '/../../public/uploads', '/');
    }

    /**
     * Handle an image upload
     *
     * @param array $file $_FILES['image'] array
     * @return array Upload result data
     */
    public function uploadImage(array $file): array
    {
        if (!isset($file['error'], $file['tmp_name'], $file['size'], $file['name'])) {
            return $this->fail('Invalid upload payload.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->fail($this->errorMessage($file['error']));
        }

        if ($file['size'] > $this->maxSize) {
            return $this->fail('File size exceeds the limit of 5 MB.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return $this->fail('Upload validation failed.');
        }

        $mimeType = $this->detectMimeType($file['tmp_name']);
        if (!isset($this->allowedMimeTypes[$mimeType])) {
            return $this->fail('Unsupported image type.');
        }

        if (@getimagesize($file['tmp_name']) === false) {
            return $this->fail('Uploaded file is not a valid image.');
        }

        $extension = $this->allowedMimeTypes[$mimeType];
        $relativeDir = $this->buildRelativeDirectory();
        $targetDir = $this->baseUploadDir . '/' . $relativeDir;

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
            return $this->fail('Unable to create upload directory.');
        }

        $filename = $this->generateFilename($extension);
        $targetPath = $targetDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $this->fail('Failed to save uploaded file.');
        }

        return [
            'success' => true,
            'relativePath' => $relativeDir . '/' . $filename,
            'originalName' => $this->sanitizeOriginalName($file['name']),
            'size' => (int) $file['size'],
            'mimeType' => $mimeType,
        ];
    }

    private function detectMimeType(string $tmpName): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);
        return is_string($mime) ? $mime : '';
    }

    private function buildRelativeDirectory(): string
    {
        return 'images/' . date('Y/m');
    }

    private function generateFilename(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }

    private function sanitizeOriginalName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
        return substr($name, 0, 255);
    }

    private function errorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File size exceeds the allowed limit.',
            UPLOAD_ERR_PARTIAL => 'File upload was incomplete.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
            default => 'Unknown upload error.',
        };
    }

    private function fail(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
        ];
    }
}
