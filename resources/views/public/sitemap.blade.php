<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc></url>
    <url><loc>{{ route('contact.create') }}</loc></url>
    <url><loc>{{ route('cookie-policy') }}</loc></url>
    @foreach (['en', 'de'] as $locale)
        @if (app(\App\Services\SiteSettings::class)->isTranslated($locale) && $collections->every(fn ($collection) => $collection->isTranslated($locale)))
            <url><loc>{{ \App\Services\Localization::route('home', [], $locale) }}</loc></url>
        @endif
        <url><loc>{{ \App\Services\Localization::route('contact.create', [], $locale) }}</loc></url>
        <url><loc>{{ \App\Services\Localization::route('cookie-policy', [], $locale) }}</loc></url>
    @endforeach
    @foreach ($pages as $page)
    <url><loc>{{ route('pages.show', ['page' => $page->slug]) }}</loc><lastmod>{{ $page->updated_at->toAtomString() }}</lastmod></url>
        @foreach (['en', 'de'] as $locale)
            @if ($page->isTranslated($locale))
                <url><loc>{{ \App\Services\Localization::route('pages.show', ['page' => $page->slug], $locale) }}</loc><lastmod>{{ $page->updated_at->toAtomString() }}</lastmod></url>
            @endif
        @endforeach
    @endforeach
</urlset>
