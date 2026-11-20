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
    private const MAX_COLLECTION_IMAGE_SIDE = 1920;

    private const MAX_THUMBNAIL_SIDE = 720;

    public function run(): void
    {
        $allOriginals = $this->collectOriginalImageFiles();
        $siteImages = array_values(array_filter($allOriginals, fn (array $item): bool => $item['year'] === '2013'));
        $collections = [
            [
                'slug' => 'collezione-estate-15',
                'title' => 'Collezione Estate ’15',
                'images' => array_values(array_filter(
                    $allOriginals,
                    fn (array $image): bool => Str::startsWith(Str::lower($image['basename']), 'ant_2015'),
                )),
            ],
            [
                'slug' => 'collezione-inverno-15-16',
                'title' => "Collezione Inverno '15-'16",
                'images' => array_values(array_filter(
                    $allOriginals,
                    fn (array $image): bool => Str::startsWith(Str::lower($image['basename']), 'inv15_'),
                )),
            ],
            [
                'slug' => 'nuova-collezione',
                'title' => 'Nuova Collezione',
                'images' => array_values(array_filter(
                    $allOriginals,
                    fn (array $image): bool => $image['year'] === '2018',
                )),
            ],
        ];

        foreach ($collections as $collection) {
            if ($collection['images'] === []) {
                throw new RuntimeException(sprintf('Nessuna immagine trovata per %s.', $collection['title']));
            }
        }

        $this->deletePlaceholderCollections();

        foreach ($collections as $index => $collection) {
            $collectionNumber = $index + 1;
            $page = Page::query()->updateOrCreate(
                ['slug' => $collection['slug']],
                [
                    'title' => $collection['title'],
                    'intro' => null,
                    'content' => null,
                    'meta_title' => $collection['title'],
                    'meta_description' => null,
                    'is_published' => true,
                    'menu_order' => 20 + $index * 10,
                ],
            );

            $this->deleteExistingGallery($page);

            $this->importCollectionImages($page, $collection['images'], $collectionNumber);
        }

        $companyPage = Page::query()->where('slug', 'azienda')->first();
        if ($companyPage !== null) {
            $this->deleteExistingGallery($companyPage);
            $this->importCompanyGallery($companyPage, $allOriginals);
        }

        $this->importSiteAssets($siteImages);
    }

    private function deletePlaceholderCollections(): void
    {
        for ($collectionNumber = 1; $collectionNumber <= 99; $collectionNumber++) {
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
            throw new RuntimeException(sprintf('Archivio immagini non trovato: %s', $root));
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

        if ($paths === []) {
            throw new RuntimeException(sprintf('Nessuna immagine valida trovata in %s.', $root));
        }

        return $paths;
    }

    /**
     * @param  list<array{path:string, year:string, month:string, basename:string, width:int, height:int}>  $images
     */
    private function importCollectionImages(Page $page, array $images, int $collectionNumber): void
    {
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
        $companyImageNames = [
            'santamariamonte',
            'santamariamonte2',
            'il-montaggio',
            'il-montaggio2',
            'la-manovia',
            'la-modelleria',
            'lavora_pelle',
            'montaggio',
            'montaggio1',
            'frabrique',
        ];
        $selected = array_values(array_filter(
            $images,
            fn (array $image): bool => in_array(Str::lower($image['basename']), $companyImageNames, true),
        ));

        if ($selected === []) {
            return;
        }

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

        $logoSource = base_path('jaguar_immagini/jaguar.png');
        if (! is_file($logoSource)) {
            throw new RuntimeException(sprintf('Logo non trovato: %s', $logoSource));
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
        [$logoPath] = $this->storeOptimizedVariants($logoSource, 'settings/logo/imported-logo');
        $settings['logo'] = $logoPath;

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
}
