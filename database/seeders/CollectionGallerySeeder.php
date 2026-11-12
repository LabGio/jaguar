<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Services\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use SplFileInfo;

class CollectionGallerySeeder extends Seeder
{
    private const COLLECTION_COUNT = 5;

    private const MAX_COLLECTION_IMAGE_SIDE = 1920;

    private const MAX_THUMBNAIL_SIDE = 720;

    public function run(): void
    {
        $this->deleteObsoleteCollections();

        $allOriginals = $this->collectOriginalImageFiles();
        $siteImages = array_values(array_filter($allOriginals, fn (array $item): bool => $item['year'] === '2013'));
        $collectionImages = array_values(array_filter($allOriginals, fn (array $item): bool => $item['year'] !== '2013'));

        if ($collectionImages === []) {
            $collectionImages = $allOriginals;
        }

        $chunks = $this->chunkEvenly($collectionImages, self::COLLECTION_COUNT);

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

            $this->importCollectionImages($page, $chunks[$collectionNumber - 1] ?? [], $collectionNumber);
        }

        $companyPage = Page::query()->where('slug', 'azienda')->first();
        if ($companyPage !== null) {
            $this->deleteExistingGallery($companyPage);
            $this->importCompanyGallery($companyPage, $siteImages);
        }

        $this->importSiteAssets($siteImages);
    }

    private function deleteObsoleteCollections(): void
    {
        for ($collectionNumber = self::COLLECTION_COUNT + 1; $collectionNumber <= 99; $collectionNumber++) {
            $slug = sprintf('collezioni/collezione-%02d', $collectionNumber);
            $page = Page::query()->where('slug', $slug)->first();

            if ($page === null) {
                continue;
            }

            $this->deleteExistingGallery($page);
            $page->delete();
        }
    }

    /**
     * @return list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>
     */
    private function collectOriginalImageFiles(): array
    {
        $root = base_path('jaguar_immagini');
        if (! is_dir($root)) {
            return [];
        }

        $paths = [];
        /** @var SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $filename = $file->getFilename();
            if (! preg_match('/\.(jpe?g|png|webp)$/i', $filename)) {
                continue;
            }

            if (preg_match('/-[0-9]+x[0-9]+\.(jpe?g|png|webp)$/i', $filename)) {
                continue;
            }

            $relativePath = Str::replaceFirst($root.DIRECTORY_SEPARATOR, '', $file->getPathname());
            $segments = preg_split('#[/\\\\]+#', $relativePath) ?: [];
            $year = $segments[0] ?? 'unknown';
            $month = $segments[1] ?? '00';

            $size = @getimagesize($file->getPathname());
            if (! is_array($size) || ! isset($size[0], $size[1])) {
                continue;
            }

            $paths[] = [
                'path' => $file->getPathname(),
                'year' => $year,
                'month' => $month,
                'basename' => pathinfo($filename, PATHINFO_FILENAME),
                'width' => (int) $size[0],
                'height' => (int) $size[1],
            ];
        }

        usort($paths, function (array $a, array $b): int {
            return [$a['year'], $a['month'], $a['basename'], $a['path']] <=> [$b['year'], $b['month'], $b['basename'], $b['path']];
        });

        return $paths;
    }

    /**
     * @param  list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>  $images
     * @return list<list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>>
     */
    private function chunkEvenly(array $images, int $chunks): array
    {
        if ($chunks <= 0) {
            return [];
        }

        $result = array_fill(0, $chunks, []);
        foreach ($images as $index => $image) {
            $bucket = $index % $chunks;
            $result[$bucket][] = $image;
        }

        return $result;
    }

    /**
     * @param  list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>  $images
     */
    private function importCollectionImages(Page $page, array $images, int $collectionNumber): void
    {
        if ($images === []) {
            for ($imageNumber = 1; $imageNumber <= 10; $imageNumber++) {
                $basePath = sprintf('pages/galleries/seed/collection-%02d-%02d', $collectionNumber, $imageNumber);
                $path = $basePath.'.webp';
                $thumbnailPath = $basePath.'-thumbnail.webp';

                Storage::disk('public')->put($path, $this->makeImage($collectionNumber, $imageNumber, 1600, 1200));
                Storage::disk('public')->put($thumbnailPath, $this->makeImage($collectionNumber, $imageNumber, 720, 540));

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

            return;
        }

        foreach (array_values($images) as $index => $image) {
            $imageNumber = $index + 1;
            $basePath = sprintf('pages/galleries/imported/collection-%02d-%03d', $collectionNumber, $imageNumber);
            [$path, $thumbnailPath] = $this->storeOptimizedVariants($image['path'], $basePath);

            $page->images()->create([
                'path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'alt_text' => $this->humanizeFileName($image['basename']),
                'sort_order' => $imageNumber,
            ]);

            if ($imageNumber === 1) {
                $page->update(['cover_image_path' => $path]);
            }
        }
    }

    /**
     * @param  list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>  $images
     */
    private function importCompanyGallery(Page $page, array $images): void
    {
        $selected = array_values(array_filter($images, function (array $image): bool {
            $lowerName = Str::lower($image['basename']);

            return ! Str::contains($lowerName, ['logo', 'calzaturificio', 'sabrina_martone', 'frabrique']);
        }));

        if ($selected === []) {
            return;
        }

        $selected = array_slice($selected, 0, 24);

        foreach ($selected as $index => $image) {
            $imageNumber = $index + 1;
            $basePath = sprintf('pages/galleries/imported/company-%03d', $imageNumber);
            [$path, $thumbnailPath] = $this->storeOptimizedVariants($image['path'], $basePath);

            $page->images()->create([
                'path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'alt_text' => $this->humanizeFileName($image['basename']),
                'sort_order' => $imageNumber,
            ]);

            if ($imageNumber === 1) {
                $page->update(['cover_image_path' => $path]);
            }
        }
    }

    /**
     * @param  list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>  $siteImages
     */
    private function importSiteAssets(array $siteImages): void
    {
        if ($siteImages === []) {
            return;
        }

        $logoSource = null;
        foreach ($siteImages as $image) {
            $name = Str::lower($image['basename']);
            if (Str::contains($name, ['calzaturificiojaguar', 'logo2014', 'logo'])) {
                $logoSource = $image['path'];
                break;
            }
        }

        $heroSource = null;
        foreach ($siteImages as $image) {
            $name = Str::lower($image['basename']);
            if (Str::contains($name, ['sm_cover', 'santamariamonte', 'fotor']) && $image['width'] >= 900) {
                $heroSource = $image['path'];
                break;
            }
        }

        $settings = [];
        if ($logoSource !== null) {
            [$logoPath] = $this->storeOptimizedVariants($logoSource, 'settings/logo/imported-logo');
            $settings['logo'] = $logoPath;
        }

        if ($heroSource !== null) {
            [$heroPath] = $this->storeOptimizedVariants($heroSource, 'settings/hero_fallback/imported-hero');
            $settings['hero_fallback'] = $heroPath;
        }

        if ($settings !== []) {
            app(SiteSettings::class)->save($settings);
        }
    }

    /**
     * @return array{string,string}
     */
    private function storeOptimizedVariants(string $sourcePath, string $basePath): array
    {
        $sourceContent = file_get_contents($sourcePath);
        if ($sourceContent === false) {
            throw new RuntimeException(sprintf('Impossibile leggere il file immagine: %s', $sourcePath));
        }

        $image = imagecreatefromstring($sourceContent);
        if ($image === false) {
            throw new RuntimeException(sprintf('Impossibile decodificare il file immagine: %s', $sourcePath));
        }

        $path = $basePath.'.webp';
        $thumbnailPath = $basePath.'-thumbnail.webp';

        try {
            Storage::disk('public')->put(
                $path,
                $this->encode($this->resize($image, self::MAX_COLLECTION_IMAGE_SIDE, self::MAX_COLLECTION_IMAGE_SIDE), 82),
            );
            Storage::disk('public')->put(
                $thumbnailPath,
                $this->encode($this->resize($image, self::MAX_THUMBNAIL_SIDE, self::MAX_THUMBNAIL_SIDE), 75),
            );
        } finally {
            imagedestroy($image);
        }

        return [$path, $thumbnailPath];
    }

    private function deleteExistingGallery(Page $page): void
    {
        $page->load('images');

        if ($page->cover_image_path !== null) {
            Storage::disk('public')->delete($page->cover_image_path);
        }

        Storage::disk('public')->delete(
            $page->images->flatMap(fn ($image): array => [$image->path, $image->thumbnail_path])->all(),
        );

        $page->images()->delete();

        $page->update(['cover_image_path' => null]);
    }

    private function humanizeFileName(string $basename): string
    {
        $cleaned = preg_replace('/[._-]+/', ' ', $basename);
        $normalized = is_string($cleaned) ? trim($cleaned) : $basename;

        return Str::title($normalized);
    }

    private function resize(\GdImage $source, int $maxWidth, int $maxHeight): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min($maxWidth / max(1, $width), $maxHeight / max(1, $height), 1);
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

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
            throw new RuntimeException('Impossibile ottimizzare l’immagine sorgente.');
        }

        return $contents;
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

        return $contents === false ? throw new RuntimeException('Impossibile generare l’immagine di esempio.') : $contents;
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
