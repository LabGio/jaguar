@php
    $currentName = str_replace('localized.', '', request()->route()->getName());
    $currentPage = request()->route('page');
    $languageParameters = $currentPage ? ['page' => $currentPage->slug] : [];
    $collections = $collections ?? collect();
    $availableLanguages = collect(\App\Services\Localization::LANGUAGES)->keys()->filter(function ($locale) use ($currentName, $currentPage, $collections) {
        if ($currentPage) {
            return $currentPage->isTranslated($locale);
        }
        if ($currentName === 'home') {
            return app(\App\Services\SiteSettings::class)->isTranslated($locale)
                && $collections->every(fn ($collection) => $collection->isTranslated($locale));
        }
        return true;
    });
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription ?? ($siteSettings['footer_text'] ?? '') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if (! $availableLanguages->contains(app()->getLocale()))
        <meta name="robots" content="noindex,follow">
    @endif
    @foreach ($availableLanguages as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ \App\Services\Localization::route($currentName, $languageParameters, $locale) }}">
    @endforeach
    <title>{{ $metaTitle ?? ($siteSettings['company_name'] ?? config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site">
    <a class="skip-link" href="#main-content">{{ __('site.skip') }}</a>
    <header class="site-header" data-site-header>
        <div class="header-inner">
            <a href="{{ \App\Services\Localization::route('home') }}" class="brand" aria-label="{{ $siteSettings['company_name'] ?? config('app.name') }} - Home">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ Storage::disk('public')->url($siteSettings['logo']) }}" alt="{{ $siteSettings['company_name'] ?? __('site.logo_alt') }}">
                @else
                    {{ $siteSettings['company_name'] ?? config('app.name') }}
                @endif
            </a>
            <nav class="desktop-nav" aria-label="{{ __('site.navigation') }}">
                <a href="{{ \App\Services\Localization::route('home') }}">Home</a>
                @foreach ($navigationPages as $navigationPage)
                    @if ($navigationPage->publishedChildren->isEmpty())
                        <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}" lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</a>
                    @else
                        <details class="nav-dropdown">
                            <summary><span lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</span> <span aria-hidden="true">⌄</span></summary>
                            <div class="nav-dropdown-panel">
                                <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}" lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</a>
                                @foreach ($navigationPage->publishedChildren as $childPage)
                                    <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $childPage->slug]) }}" lang="{{ $childPage->translatedLanguage('title') }}">{{ $childPage->translated('title') }}</a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
                <a href="{{ \App\Services\Localization::route('contact.create') }}">{{ __('site.contacts') }}</a>
            </nav>
            <nav class="language-switcher" aria-label="{{ __('site.language') }}">
                @foreach (\App\Services\Localization::LANGUAGES as $locale => $language)
                    <a href="{{ \App\Services\Localization::route($currentName, $languageParameters, $locale) }}" lang="{{ $locale }}" aria-label="{{ $language }}" @if(app()->getLocale() === $locale) aria-current="true" @endif>{{ strtoupper($locale) }}</a>
                @endforeach
            </nav>
            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu">
                <span>Menu</span><span class="menu-toggle-icon" aria-hidden="true"></span>
            </button>
        </div>
        <nav id="mobile-menu" class="mobile-nav hidden" data-mobile-menu aria-label="{{ __('site.mobile_navigation') }}">
            <a href="{{ \App\Services\Localization::route('home') }}">Home</a>
            @foreach ($navigationPages as $navigationPage)
                @if ($navigationPage->publishedChildren->isEmpty())
                    <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}" lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</a>
                @else
                    <details>
                        <summary><span lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</span></summary>
                        <div class="mobile-subnav">
                            <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}"><span lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</span>: {{ __('site.overview') }}</a>
                            @foreach ($navigationPage->publishedChildren as $childPage)
                                <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $childPage->slug]) }}" lang="{{ $childPage->translatedLanguage('title') }}">{{ $childPage->translated('title') }}</a>
                            @endforeach
                        </div>
                    </details>
                @endif
            @endforeach
            <a href="{{ \App\Services\Localization::route('contact.create') }}">{{ __('site.contacts') }}</a>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')

        @if (request()->routeIs('pages.show', 'localized.pages.show') && $navigationPages->isNotEmpty())
            <nav class="page-directory wrap" aria-label="{{ __('site.explore_pages') }}">
                <p class="eyebrow">{{ __('site.continue_exploring') }}</p>
                <div class="directory-links">
                    @foreach ($navigationPages as $navigationPage)
                        <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}"><span lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</span> <span aria-hidden="true">↗</span></a>
                        @foreach ($navigationPage->publishedChildren as $childPage)
                            <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $childPage->slug]) }}"><span lang="{{ $childPage->translatedLanguage('title') }}">{{ $childPage->translated('title') }}</span> <span aria-hidden="true">↗</span></a>
                        @endforeach
                    @endforeach
                </div>
            </nav>
        @endif
    </main>

    <footer class="site-footer">
        <div class="wrap footer-grid">
            <div class="footer-brand">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ Storage::disk('public')->url($siteSettings['logo']) }}" alt="{{ $siteSettings['company_name'] ?? __('site.logo_alt') }}" loading="lazy">
                @else
                    <p class="footer-name">{{ $siteSettings['company_name'] ?? config('app.name') }}</p>
                @endif
                <p lang="{{ app(\App\Services\SiteSettings::class)->languageOf('footer_text') }}">{{ $siteSettings['footer_text'] ?? __('site.footer_default') }}</p>
            </div>
            <div class="footer-column">
                <p class="eyebrow">{{ __('site.explore') }}</p>
                <a href="{{ \App\Services\Localization::route('home') }}">Home</a>
                @foreach ($navigationPages as $navigationPage)
                    <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $navigationPage->slug]) }}" lang="{{ $navigationPage->translatedLanguage('title') }}">{{ $navigationPage->translated('title') }}</a>
                    @foreach ($navigationPage->publishedChildren as $childPage)
                        <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $childPage->slug]) }}" lang="{{ $childPage->translatedLanguage('title') }}">{{ $childPage->translated('title') }}</a>
                    @endforeach
                @endforeach
                <a href="{{ \App\Services\Localization::route('contact.create') }}">{{ __('site.contacts') }}</a>
            </div>
            <div class="footer-column">
                <p class="eyebrow">{{ __('site.contacts') }}</p>
                @if (! empty($siteSettings['address']))<p class="preserve-lines">{{ $siteSettings['address'] }}</p>@endif
                @if (! empty($siteSettings['phone']))<a href="tel:{{ $siteSettings['phone'] }}">{{ $siteSettings['phone'] }}</a>@endif
                @if (! empty($siteSettings['email']))<a href="mailto:{{ $siteSettings['email'] }}">{{ $siteSettings['email'] }}</a>@endif
            </div>
            <div class="footer-column">
                <p class="eyebrow">{{ __('site.follow') }}</p>
                @foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'linkedin_url' => 'LinkedIn'] as $key => $label)
                    @if (! empty($siteSettings[$key]))<a href="{{ $siteSettings[$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif
                @endforeach
            </div>
        </div>
        <div class="wrap footer-bottom">
            <span>© {{ date('Y') }} {{ $siteSettings['company_name'] ?? config('app.name') }}</span>
            @if (! empty($siteSettings['vat_number']))<span>P. IVA {{ $siteSettings['vat_number'] }}</span>@endif
            <a href="{{ \App\Services\Localization::route('cookie-policy') }}">Cookie policy</a>
        </div>
    </footer>
    <div class="cookie-banner hidden" data-cookie-banner>
        <p>{{ __('site.cookie_notice') }} <a href="{{ \App\Services\Localization::route('cookie-policy') }}">Cookie policy</a></p>
        <button type="button" data-cookie-accept>{{ __('site.accept') }}</button>
    </div>
</body>
</html>
