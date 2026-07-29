<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Throwable;

trait Upload
{
    protected string $defaultUploadDir = 'uploads';
    protected int $webpQuality = 80;

    public function upload(?UploadedFile $file, ?string $dir = null, ?string $oldFile = null, string $disk = 'public'): ?string
    {
        if (!$file) {
            return $oldFile;
        }

        $dir = $this->resolveDir($dir);

        $this->deleteFile($oldFile, $disk);

        if ($this->isImage($file)) {
            return $this->storeWebp($file, $dir, $disk);
        }

        return $file->store($dir, $disk);
    }

    public function uploadAs(?UploadedFile $file, ?string $dir = null, ?string $oldFile = null, string $disk = 'public'): ?string
    {
        if (!$file) {
            return $oldFile;
        }

        $dir = $dir ?? $this->defaultUploadDir;

        $this->deleteFile($oldFile, $disk);

        if ($this->isImage($file)) {
            return $this->storeWebp($file, $dir, $disk);
        }

        $filename = $this->generateFileName($file);

        return $file->storeAs($dir, $filename, $disk);
    }

    protected function resolveDir(?string $dir): string
    {
        return $dir ? $this->defaultUploadDir . '/' . $dir : $this->defaultUploadDir;
    }

    /**
     * Store image as webp (Intervention Image v4 API)
     */
    protected function storeWebp(UploadedFile $file, string $dir, string $disk): string
    {
          $filename = pathinfo($this->generateFileName($file), PATHINFO_FILENAME) . '.webp';
        $path = $dir . '/' . $filename;

        try {
            $manager = ImageManager::usingDriver(Driver::class);

            $encoded = $manager->decode($file)
                ->encodeUsingFormat(Format::WEBP, quality: $this->webpQuality);

            Storage::disk($disk)->put($path, (string) $encoded);

            return $path;
        } catch (Throwable $e) {
            report($e);
            return $file->store($dir, $disk);
        }
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with((string) $file->getMimeType(), 'image/');
    }

    protected function generateFileName(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }

    public function deleteFile(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function fileUrl(?string $path, string $disk = 'public'): ?string
    {
        return $path ? Storage::disk($disk)->url($path) : null;
    }
}
