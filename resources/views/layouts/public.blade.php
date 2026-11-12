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
<body class="bg-stone-50 font-sans text-stone-900 antialiased">
    <header class="absolute inset-x-0 top-0 z-30 text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between border-b border-white/20 bg-stone-950/45 px-6 py-4 backdrop-blur-md lg:px-8">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-[0.18em]">
                @if (! empty($siteSettings['logo']))
                    <img src="{{ Storage::disk('public')->url($siteSettings['logo']) }}" alt="{{ $siteSettings['company_name'] ?? 'Logo aziendale' }}" class="h-10 w-auto">
                @else
                    {{ $siteSettings['company_name'] ?? config('app.name') }}
                @endif
            </a>
            <button type="button" class="rounded border border-white/40 px-3 py-2 text-xs uppercase tracking-widest md:hidden" data-menu-toggle aria-expanded="false">Menu</button>
            <nav class="hidden items-center gap-2 text-sm font-medium md:flex" data-menu>
                <a href="{{ route('home') }}" class="rounded px-3 py-2 transition hover:bg-white/10 hover:text-white">Home</a>
                @foreach ($navigationPages as $navigationPage)
                    @if ($navigationPage->publishedChildren->isEmpty())
                        <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}" class="rounded px-3 py-2 transition hover:bg-white/10 hover:text-white">{{ $navigationPage->title }}</a>
                    @else
                        <details class="group relative">
                            <summary class="flex cursor-pointer list-none items-center gap-1 rounded px-3 py-2 transition hover:bg-white/10 hover:text-white">{{ $navigationPage->title }}<svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="m5.23 7.21 4.77 4.77 4.77-4.77 1.06 1.06L10 13.04 4.17 8.27l1.06-1.06Z" clip-rule="evenodd"/></svg></summary>
                            <div class="absolute left-0 top-full mt-2 min-w-56 rounded-lg border border-white/15 bg-stone-950 p-2 text-stone-100 shadow-xl">
                                <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}" class="block rounded px-3 py-2 font-semibold hover:bg-white/10">{{ $navigationPage->title }}</a>
                                @foreach ($navigationPage->publishedChildren as $childPage)
                                    <a href="{{ route('pages.show', ['page' => $childPage->slug]) }}" class="block rounded px-3 py-2 hover:bg-white/10">{{ $childPage->title }}</a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
                <a href="{{ route('contact.create') }}" class="rounded border border-white/40 px-3 py-2 transition hover:bg-white hover:text-stone-950">Contatti</a>
            </nav>
        </div>
        <nav class="mx-6 hidden flex-col gap-3 border-x border-b border-white/20 bg-stone-950/95 px-5 py-5 text-sm shadow-xl md:hidden" data-mobile-menu>
            <a href="{{ route('home') }}">Home</a>
            @foreach ($navigationPages as $navigationPage)
                @if ($navigationPage->publishedChildren->isEmpty())
                    <a href="{{ route('pages.show', ['page' => $navigationPage->slug]) }}">{{ $navigationPage->title }}</a>
                @else
                    <details>
                        <summary class="cursor-pointer font-semibold">{{ $navigationPage->title }}</summary>
                        <div class="mt-2 flex flex-col gap-2 border-l border-white/20 pl-4 text-stone-300">
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

    <main>
        @yield('content')
    </main>

    <footer class="bg-stone-950 px-6 py-14 text-stone-300 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-10 md:grid-cols-[2fr_1fr_1fr]">
            <div class="space-y-4">
                <p class="text-lg font-semibold text-white">{{ $siteSettings['company_name'] ?? config('app.name') }}</p>
                <p class="max-w-md text-sm leading-6">{{ $siteSettings['footer_text'] ?? 'Dati aziendali e informazioni legali configurabili dall’area amministrativa.' }}</p>
            </div>
            <div class="space-y-2 text-sm">
                @if (! empty($siteSettings['address']))<p>{{ $siteSettings['address'] }}</p>@endif
                @if (! empty($siteSettings['phone']))<a class="block hover:text-white" href="tel:{{ $siteSettings['phone'] }}">{{ $siteSettings['phone'] }}</a>@endif
                @if (! empty($siteSettings['email']))<a class="block hover:text-white" href="mailto:{{ $siteSettings['email'] }}">{{ $siteSettings['email'] }}</a>@endif
            </div>
            <div class="flex gap-4 text-sm">
                @foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'linkedin_url' => 'LinkedIn'] as $key => $label)
                    @if (! empty($siteSettings[$key]))<a href="{{ $siteSettings[$key] }}" target="_blank" rel="noopener noreferrer" class="hover:text-white">{{ $label }}</a>@endif
                @endforeach
            </div>
        </div>
        <div class="mx-auto mt-10 flex max-w-7xl flex-wrap gap-4 border-t border-white/10 pt-6 text-xs text-stone-500">
            @if (! empty($siteSettings['vat_number']))<span>P. IVA {{ $siteSettings['vat_number'] }}</span>@endif
            <a href="{{ route('cookie-policy') }}" class="hover:text-white">Cookie policy</a>
        </div>
    </footer>
    <div class="fixed inset-x-4 bottom-4 z-40 hidden max-w-xl rounded bg-stone-950 p-4 text-sm text-white shadow-xl md:left-auto" data-cookie-banner>
        <p>Utilizziamo solo cookie tecnici necessari al funzionamento del sito.</p>
        <button type="button" class="mt-3 rounded bg-white px-3 py-2 text-xs font-semibold text-stone-900" data-cookie-accept>Accetta</button>
    </div>
</body>
</html>
