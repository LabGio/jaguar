<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription ?? ($siteSettings['footer_text'] ?? '') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $metaTitle ?? ($siteSettings['company_name'] ?? config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site">
    <a class="skip-link" href="#main-content">Vai al contenuto</a>
    <header class="site-header" data-site-header>
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ $siteSettings['company_name'] ?? config('app.name') }} - Home">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ Storage::disk('public')->url($siteSettings['logo']) }}" alt="{{ $siteSettings['company_name'] ?? 'Logo aziendale' }}">
                @else
                    {{ $siteSettings['company_name'] ?? config('app.name') }}
                @endif
            </a>
            <nav class="desktop-nav" aria-label="Navigazione principale">
                <a href="{{ route('home') }}">Home</a>
                @foreach ($navigationPages as $navigationPage)
                    @if ($navigationPage->publishedChildren->isEmpty())
                        <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}</a>
                    @else
                        <details class="nav-dropdown">
                            <summary>{{ $navigationPage->title }} <span aria-hidden="true">⌄</span></summary>
                            <div class="nav-dropdown-panel">
                                <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}</a>
                                @foreach ($navigationPage->publishedChildren as $childPage)
                                    <a href="{{ route('pages.show', ['page' => $childPage->slug]) }}">{{ $childPage->title }}</a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
                <a href="{{ route('contact.create') }}">Contatti</a>
            </nav>
            <button type="button" class="menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu">
                <span>Menu</span><span class="menu-toggle-icon" aria-hidden="true"></span>
            </button>
        </div>
        <nav id="mobile-menu" class="mobile-nav hidden" data-mobile-menu aria-label="Navigazione mobile">
            <a href="{{ route('home') }}">Home</a>
            @foreach ($navigationPages as $navigationPage)
                @if ($navigationPage->publishedChildren->isEmpty())
                    <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}</a>
                @else
                    <details>
                        <summary>{{ $navigationPage->title }}</summary>
                        <div class="mobile-subnav">
                            <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}: panoramica</a>
                            @foreach ($navigationPage->publishedChildren as $childPage)
                                <a href="{{ route('pages.show', ['page' => $childPage->slug]) }}">{{ $childPage->title }}</a>
                            @endforeach
                        </div>
                    </details>
                @endif
            @endforeach
            <a href="{{ route('contact.create') }}">Contatti</a>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')

        @if (request()->routeIs('pages.show') && $navigationPages->isNotEmpty())
            <nav class="page-directory wrap" aria-label="Esplora le pagine">
                <p class="eyebrow">Continua a esplorare</p>
                <div class="directory-links">
                    @foreach ($navigationPages as $navigationPage)
                        <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }} <span aria-hidden="true">↗</span></a>
                        @foreach ($navigationPage->publishedChildren as $childPage)
                            <a href="{{ route('pages.show', ['page' => $childPage->slug]) }}">{{ $childPage->title }} <span aria-hidden="true">↗</span></a>
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
                    <img src="{{ Storage::disk('public')->url($siteSettings['logo']) }}" alt="{{ $siteSettings['company_name'] ?? 'Logo aziendale' }}" loading="lazy">
                @else
                    <p class="footer-name">{{ $siteSettings['company_name'] ?? config('app.name') }}</p>
                @endif
                <p>{{ $siteSettings['footer_text'] ?? 'Dati aziendali e informazioni legali configurabili dall’area amministrativa.' }}</p>
            </div>
            <div class="footer-column">
                <p class="eyebrow">Esplora</p>
                <a href="{{ route('home') }}">Home</a>
                @foreach ($navigationPages as $navigationPage)
                    <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}</a>
                    @foreach ($navigationPage->publishedChildren as $childPage)
                        <a href="{{ route('pages.show', ['page' => $childPage->slug]) }}">{{ $childPage->title }}</a>
                    @endforeach
                @endforeach
                <a href="{{ route('contact.create') }}">Contatti</a>
            </div>
            <div class="footer-column">
                <p class="eyebrow">Contatti</p>
                @if (! empty($siteSettings['address']))<p class="preserve-lines">{{ $siteSettings['address'] }}</p>@endif
                @if (! empty($siteSettings['phone']))<a href="tel:{{ $siteSettings['phone'] }}">{{ $siteSettings['phone'] }}</a>@endif
                @if (! empty($siteSettings['email']))<a href="mailto:{{ $siteSettings['email'] }}">{{ $siteSettings['email'] }}</a>@endif
            </div>
            <div class="footer-column">
                <p class="eyebrow">Seguici</p>
                @foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'linkedin_url' => 'LinkedIn'] as $key => $label)
                    @if (! empty($siteSettings[$key]))<a href="{{ $siteSettings[$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif
                @endforeach
            </div>
        </div>
        <div class="wrap footer-bottom">
            <span>© {{ date('Y') }} {{ $siteSettings['company_name'] ?? config('app.name') }}</span>
            @if (! empty($siteSettings['vat_number']))<span>P. IVA {{ $siteSettings['vat_number'] }}</span>@endif
            <a href="{{ route('cookie-policy') }}">Cookie policy</a>
        </div>
    </footer>
    <div class="cookie-banner hidden" data-cookie-banner>
        <p>Utilizziamo solo cookie tecnici necessari al funzionamento del sito. <a href="{{ route('cookie-policy') }}">Cookie policy</a></p>
        <button type="button" data-cookie-accept>Accetta</button>
    </div>
</body>
</html>
