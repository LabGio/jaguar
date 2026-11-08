<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CollectionGallerySeeder extends Seeder
{
    private const COLLECTION_COUNT = 10;

    private const IMAGES_PER_COLLECTION = 10;

    public function run(): void
    {
        for ($collectionNumber = 1; $collectionNumber <= self::COLLECTION_COUNT; $collectionNumber++) {
            $slug = sprintf('collezioni/collezione-%02d', $collectionNumber);
            $page = Page::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => sprintf('Collezione %02d', $collectionNumber),
                    'intro' => 'Collezione di esempio predisposta per la presentazione dei prodotti.',
                    'content' => 'Aggiorna testi, fotografie e metadati dall’area amministrativa.',
                    'meta_title' => sprintf('Collezione %02d', $collectionNumber),
                    'meta_description' => 'Collezione di esempio con galleria fotografica.',
                    'is_published' => true,
                    'menu_order' => $collectionNumber,
                ],
            );

            $this->deleteExistingGallery($page);

            for ($imageNumber = 1; $imageNumber <= self::IMAGES_PER_COLLECTION; $imageNumber++) {
                $basePath = sprintf(
                    'pages/galleries/seed/collection-%02d-%02d',
                    $collectionNumber,
                    $imageNumber,
                );
                $path = $basePath.'.webp';
                $thumbnailPath = $basePath.'-thumbnail.webp';

                Storage::disk('public')->put(
                    $path,
                    $this->makeImage($collectionNumber, $imageNumber, 1600, 1200),
                );
                Storage::disk('public')->put(
                    $thumbnailPath,
                    $this->makeImage($collectionNumber, $imageNumber, 720, 540),
                );

                $page->images()->create([
                    'path' => $path,
                    'thumbnail_path' => $thumbnailPath,
                    'alt_text' => sprintf('Collezione %02d, immagine %02d', $collectionNumber, $imageNumber),
                    'sort_order' => $imageNumber,
                ]);

                if ($imageNumber === 1) {
                    $page->update(['cover_image_path' => $path]);
                }
            }
        }
    }

    private function deleteExistingGallery(Page $page): void
    {
        $page->load('images');

        Storage::disk('public')->delete(
            $page->images->flatMap(fn ($image): array => [$image->path, $image->thumbnail_path])->all(),
        );

        $page->images()->delete();
    }

    private function makeImage(int $collectionNumber, int $imageNumber, int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        $hue = (($collectionNumber * 31) + ($imageNumber * 17)) % 360;
        [$red, $green, $blue] = $this->hslToRgb($hue, 46, 34);
        $background = imagecolorallocate($image, $red, $green, $blue);
        $accent = imagecolorallocate($image, min(255, $red + 75), min(255, $green + 75), min(255, $blue + 75));
        $dark = imagecolorallocate($image, max(0, $red - 50), max(0, $green - 50), max(0, $blue - 50));

        imagefill($image, 0, 0, $background);
        imagefilledellipse($image, (int) ($width * 0.3), (int) ($height * 0.35), (int) ($width * 0.75), (int) ($height * 0.75), $accent);
        imagefilledellipse($image, (int) ($width * 0.75), (int) ($height * 0.72), (int) ($width * 0.8), (int) ($height * 0.8), $dark);
        imagefilledrectangle($image, (int) ($width * 0.08), (int) ($height * 0.08), (int) ($width * 0.92), (int) ($height * 0.13), $dark);

        ob_start();
        imagewebp($image, null, 82);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents === false ? throw new \RuntimeException('Impossibile generare l’immagine di esempio.') : $contents;
    }

    /**
     * @return array{int, int, int}
     */
    private function hslToRgb(int $hue, int $saturation, int $lightness): array
    {
        $saturation /= 100;
        $lightness /= 100;
        $chroma = (1 - abs((2 * $lightness) - 1)) * $saturation;
        $segment = $hue / 60;
        $secondary = $chroma * (1 - abs(fmod($segment, 2) - 1));
        [$red, $green, $blue] = match (true) {
            $segment < 1 => [$chroma, $secondary, 0],
            $segment < 2 => [$secondary, $chroma, 0],
            $segment < 3 => [0, $chroma, $secondary],
            $segment < 4 => [0, $secondary, $chroma],
            $segment < 5 => [$secondary, 0, $chroma],
            default => [$chroma, 0, $secondary],
        };
        $match = $lightness - ($chroma / 2);

        return [
            (int) round(($red + $match) * 255),
            (int) round(($green + $match) * 255),
            (int) round(($blue + $match) * 255),
        ];
    }
}
