<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Services;
use ErrorException;
use RuntimeException;
use Throwable;

/**
 * Validates optional uploads and publishes only re-encoded, square PNG images.
 * The controller saves the returned filename before removing the previous image.
 */
class AvatarStorage
{
    private const FILENAME_PATTERN = '/\Aavatar_[a-f0-9]{32}\.png\z/D';
    private const MAX_DIMENSION = 4096;

    private string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = rtrim($directory ?? FCPATH . 'uploads/avatars', '/\\');
    }

    public function validate(?UploadedFile $file): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return 'Choose a JPG or PNG image no larger than 2 MB (2048 KB).';
        }

        if (! $file->isValid() || $file->hasMoved()) {
            return 'The image upload did not complete. Please select the image again.';
        }

        try {
            // These installed CI rules inspect the request's avatar upload.
            $validation = Services::validation(null, false);
            $validation->setRules([
                'avatar' => [
                    'label' => 'Profile picture',
                    'rules' => 'uploaded[avatar]|max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => 'The image upload did not complete. Please select the image again.',
                        'max_size' => 'Choose a JPG or PNG image no larger than 2 MB (2048 KB).',
                        'is_image' => 'Choose a valid JPG/JPEG or PNG image.',
                        'mime_in' => 'Choose a valid JPG/JPEG or PNG image with a matching file extension.',
                    ],
                ],
            ]);

            if (! $validation->run([])) {
                return $validation->getError('avatar');
            }

            return $this->inspectImage($file);
        } catch (Throwable $exception) {
            log_message('error', 'Avatar validation failed: {message}', ['message' => $exception->getMessage()]);

            return 'The image could not be read. Please select a valid JPG or PNG image again.';
        }
    }

    /**
     * Call only after all text and file validation has passed.
     * Never move the original, client-controlled upload into the public directory.
     */
    public function prepare(UploadedFile $file): string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('The image is not a valid upload.');
        }

        $directory = $this->safeDirectory(true);
        $filename = 'avatar_' . bin2hex(random_bytes(16)) . '.png';
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;

        // Exclusive creation prevents any existing file from being overwritten.
        $handle = @fopen($destination, 'x');
        if ($handle === false) {
            throw new RuntimeException('The avatar directory is not writable.');
        }
        fclose($handle);

        set_error_handler(static function (int $severity, string $message, string $path, int $line): never {
            throw new ErrorException($message, 0, $severity, $path, $line);
        });

        try {
            $saved = Services::image('gd', null, false)
                ->withFile($file->getTempName())
                ->fit(256, 256, 'center')
                ->convert(IMAGETYPE_PNG)
                ->save($destination);

            $prepared = getimagesize($destination);
            if (! $saved || $prepared === false || $prepared[0] !== 256 || $prepared[1] !== 256 || $prepared[2] !== IMAGETYPE_PNG) {
                throw new RuntimeException('The avatar could not be prepared.');
            }
        } catch (Throwable $exception) {
            // Restore first: cleanup must not turn a warning into another exception.
            restore_error_handler();
            $this->deleteManaged($filename);

            throw new RuntimeException('The profile picture could not be prepared.', 0, $exception);
        }

        restore_error_handler();

        return $filename;
    }

    /** Delete only one of this application's generated files, never a placeholder. */
    public function deleteManaged(?string $filename): void
    {
        try {
            $path = $this->managedPath($filename);
            if ($path !== null && ! @unlink($path)) {
                log_message('error', 'An obsolete managed avatar could not be removed.');
            }
        } catch (Throwable $exception) {
            // Cleanup cannot undo a successful database commit or hide its result.
            log_message('error', 'Managed avatar cleanup failed: {message}', ['message' => $exception->getMessage()]);
        }
    }

    public function url(?string $filename): string
    {
        helper('url');

        return $this->managedPath($filename) === null
            ? base_url('assets/avatar-placeholder.svg')
            : base_url('uploads/avatars/' . $filename);
    }

    private function inspectImage(UploadedFile $file): ?string
    {
        if (! extension_loaded('gd')) {
            return 'Profile pictures are temporarily unavailable. Please try again later.';
        }

        $decoded = null;
        set_error_handler(static function (int $severity, string $message, string $path, int $line): never {
            throw new ErrorException($message, 0, $severity, $path, $line);
        });

        try {
            $info = getimagesize($file->getTempName());
            if ($info === false || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                return 'Choose a valid JPG/JPEG or PNG image.';
            }

            if ($info[0] < 1 || $info[1] < 1 || $info[0] > self::MAX_DIMENSION || $info[1] > self::MAX_DIMENSION) {
                return 'Choose an image no wider or taller than 4096 pixels.';
            }

            // Compressed images may need much more memory when decoded/cropped.
            $memoryLimit = ini_parse_quantity((string) ini_get('memory_limit'));
            $estimatedMemory = $info[0] * $info[1] * 12 + 16 * 1024 * 1024;
            if ($memoryLimit > 0 && $estimatedMemory > $memoryLimit - memory_get_usage(true)) {
                return 'This image needs too much memory to prepare. Please choose a smaller image.';
            }

            $decoded = $info[2] === IMAGETYPE_JPEG
                ? imagecreatefromjpeg($file->getTempName())
                : imagecreatefrompng($file->getTempName());

            if ($decoded === false) {
                return 'The image is damaged or incomplete. Please choose another JPG or PNG image.';
            }
        } catch (Throwable) {
            return 'The image is damaged or incomplete. Please choose another JPG or PNG image.';
        } finally {
            if ($decoded instanceof \GdImage) {
                imagedestroy($decoded);
            }
            restore_error_handler();
        }

        return null;
    }

    private function managedPath(?string $filename): ?string
    {
        if ($filename === null || preg_match(self::FILENAME_PATTERN, $filename) !== 1) {
            return null;
        }

        try {
            $directory = $this->safeDirectory();
        } catch (RuntimeException) {
            return null;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $filename;
        if (is_link($path) || ! is_file($path)) {
            return null;
        }

        $resolved = realpath($path);

        return $resolved !== false && dirname($resolved) === $directory ? $resolved : null;
    }

    private function safeDirectory(bool $create = false): string
    {
        // Reject symlinks at every level, including a linked uploads directory.
        for ($path = $this->directory; $path !== dirname($path); $path = dirname($path)) {
            if (is_link($path)) {
                throw new RuntimeException('The avatar directory must not be a symbolic link.');
            }
        }

        if (! is_dir($this->directory) && (! $create || ! @mkdir($this->directory, 0775, true))) {
            throw new RuntimeException('The avatar directory is unavailable.');
        }

        $resolved = realpath($this->directory);
        if ($resolved === false || ! is_dir($resolved)) {
            throw new RuntimeException('The avatar directory is unavailable.');
        }

        return $resolved;
    }
}
