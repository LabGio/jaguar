<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImageService
{
    /**
     * @return array{path: string, thumbnail_path: string}
     */
    public function storePageImage(UploadedFile $file): array
    {
        $image = imagecreatefromstring($file->get());

        if ($image === false) {
            throw new RuntimeException('Impossibile elaborare l’immagine caricata.');
        }

        $baseName = bin2hex(random_bytes(16));
        $path = "pages/galleries/{$baseName}.webp";
        $thumbnailPath = "pages/galleries/thumbnails/{$baseName}.webp";

        try {
            Storage::disk('public')->put($path, $this->encode($this->resize($image, 1920, 1440), 82));
            Storage::disk('public')->put($thumbnailPath, $this->encode($this->resize($image, 720, 540), 75));
        } finally {
            imagedestroy($image);
        }

        return ['path' => $path, 'thumbnail_path' => $thumbnailPath];
    }

    public function delete(string $path, string $thumbnailPath): void
    {
        Storage::disk('public')->delete([$path, $thumbnailPath]);
    }

    public function storeImage(UploadedFile $file, string $directory, int $maxWidth = 1920, int $maxHeight = 1440): string
    {
        $image = imagecreatefromstring($file->get());

        if ($image === false) {
            throw new RuntimeException('Impossibile elaborare l’immagine caricata.');
        }

        $path = trim($directory, '/').'/'.bin2hex(random_bytes(16)).'.webp';

        try {
            Storage::disk('public')->put($path, $this->encode($this->resize($image, $maxWidth, $maxHeight), 82));
        } finally {
            imagedestroy($image);
        }

        return $path;
    }

    private function resize(\GdImage $source, int $maxWidth, int $maxHeight): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $target;
    }

    private function encode(\GdImage $image, int $quality): string
    {
        ob_start();
        imagewebp($image, null, $quality);
        $contents = ob_get_clean();
        imagedestroy($image);

        if ($contents === false) {
            throw new RuntimeException('Impossibile ottimizzare l’immagine caricata.');
        }

        return $contents;
    }
}
