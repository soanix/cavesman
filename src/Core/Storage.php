<?php

namespace Cavesman;

use Cavesman\Enum\Directory;

/**
 * Local Storage Service
 *
 * Simulates AWS S3 behaviour using the local file system under Directory::STORAGE.
 * The $bucket parameter maps to a subdirectory inside the storage root.
 */
class Storage
{
    /**
     * Resolve the storage base path, optionally scoped to a bucket (subdirectory).
     */
    private static function basePath(?string $bucket = null): string
    {
        $root = FileSystem::getPath(Directory::STORAGE);
        return $bucket ? rtrim($root, '/') . '/' . ltrim($bucket, '/') : $root;
    }

    /**
     * Upload (copy) a file into local storage.
     *
     * @param string      $source  Absolute path of the source file.
     * @param string      $target  Relative destination path within storage (or bucket).
     * @param string|null $bucket  Optional subdirectory acting as a bucket.
     * @return string The stored relative key ($target).
     */
    public static function upload(string $source, string $target, ?string $bucket = null): string
    {
        $destination = self::basePath($bucket) . '/' . ltrim($target, '/');
        $dir = dirname($destination);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($source, $destination);

        return $target;
    }

    /**
     * Download (read) file contents from local storage.
     *
     * @param string      $file   Relative path of the file within storage (or bucket).
     * @param string|null $bucket Optional subdirectory acting as a bucket.
     * @return string File contents, or empty string if the file does not exist.
     */
    public static function download(string $file, ?string $bucket = null): string
    {
        $path = self::basePath($bucket) . '/' . ltrim($file, '/');

        if (!file_exists($path)) {
            return '';
        }

        return file_get_contents($path) ?: '';
    }

    /**
     * Delete a file from local storage.
     *
     * @param string      $file   Relative path of the file within storage (or bucket).
     * @param string|null $bucket Optional subdirectory acting as a bucket.
     * @return bool True on success, false if the file did not exist.
     */
    public static function delete(string $file, ?string $bucket = null): bool
    {
        $path = self::basePath($bucket) . '/' . ltrim($file, '/');

        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * Get the absolute local path for a stored file (equivalent to a public URL).
     *
     * @param string      $file   Relative path of the file within storage.
     * @param string|null $bucket Optional subdirectory acting as a bucket.
     * @return string Absolute file-system path to the stored file.
     */
    public static function getUrl(string $file, ?string $bucket = null): string
    {
        return self::basePath($bucket) . '/' . ltrim($file, '/');
    }
}
