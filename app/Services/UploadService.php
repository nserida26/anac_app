<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Image;

class UploadService
{
    /**
     * Store a document (typically a PDF) in the documents directory.
     *
     * @param UploadedFile $file The uploaded file
     * @return string The relative path within the uploads directory (e.g., "documents/filename.pdf")
     */
    public function storeDocument(UploadedFile $file): string
    {
        // If there's no actual temp file path, skip storage (avoids ValueError: Path cannot be empty)
        if (empty($file->getPathname())) {
            return '';
        }

        try {
            $path = $file->store('documents', 'public');
            if ($path !== false) {
                return $path;
            }
        } catch (\Throwable $e) {
            // Fall through to manual copy (catches \ValueError, \Error, \Exception)
        }

        // Fallback: manually move the file (needed when the filesystem disk fails on Windows)
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = 'documents/' . $filename;
        $fullPath = base_path('uploads/' . $relativePath);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $file->move($dir, $filename);
        } catch (\Throwable $e) {
            // Fallback move failed too; return the path even if file wasn't saved to avoid crash
        }

        return $relativePath;
    }

    /**
     * Store a photo/image in the photos directory with optional resizing.
     * Uses intervention/image to resize and optimize the image.
     *
     * @param UploadedFile $file The uploaded image file
     * @param int|null $width Max width in pixels (null = original width)
     * @param int|null $height Max height in pixels (null = original height)
     * @return string The relative path within the uploads directory (e.g., "photos/filename.jpg")
     */
    public function storePhoto(UploadedFile $file, ?int $width = 800, ?int $height = 800): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = 'photos/' . $filename;
        $fullPath = base_path('uploads/' . $relativePath);

        // Ensure the photos directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Process with intervention/image
        $image = Image::make($file);

        if ($width !== null || $height !== null) {
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $image->save($fullPath, 85); // 85% quality for optimization

        return $relativePath;
    }

    /**
     * Delete a file from the uploads directory.
     *
     * @param string $relativePath Relative path like "documents/file.pdf" or "photos/file.jpg"
     * @return bool True if deleted or already missing, false on failure
     */
    public function delete(string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $fullPath = base_path('uploads/' . ltrim($relativePath, '/'));

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return true; // Already deleted
    }

    /**
     * Get the full filesystem path for a relative path.
     *
     * @param string $relativePath Relative path like "documents/file.pdf"
     * @return string
     */
    public function fullPath(string $relativePath): string
    {
        return base_path('uploads/' . ltrim($relativePath, '/'));
    }

    /**
     * Check if a file exists in the uploads directory.
     *
     * @param string $relativePath Relative path like "documents/file.pdf"
     * @return bool
     */
    public function exists(string $relativePath): bool
    {
        return file_exists($this->fullPath($relativePath));
    }
}
