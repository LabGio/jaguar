@extends('layouts.public')
@section('content')
<section class="hero">
    @if (! empty($settings['hero_video']))
        <video class="hero-media" autoplay muted loop playsinline preload="metadata" poster="{{ ! empty($settings['hero_fallback']) ? Storage::disk('public')->url($settings['hero_fallback']) : '' }}" data-hero-video><source src="{{ Storage::disk('public')->url($settings['hero_video']) }}"></video>
    @elseif (! empty($settings['hero_fallback']))
        <img class="hero-media" src="{{ Storage::disk('public')->url($settings['hero_fallback']) }}" alt="" fetchpriority="high">
    @endif
    <div class="hero-shade"></div>
    <div class="wrap hero-content">
        <p class="eyebrow light">Calzature · Made in Italy</p>
        <h1>{{ $settings['hero_title'] ?? 'L’eccellenza prende forma.' }}</h1>
        <p class="hero-subtitle">{{ $settings['hero_subtitle'] ?? 'Una presenza digitale progettata per raccontare la qualità, i materiali e le collezioni della vostra azienda.' }}</p>
        @if ($collections->isNotEmpty())<a class="text-link light-link" href="#collezioni">Esplora le collezioni <span aria-hidden="true">↘</span></a>@endif
    </div>
    <span class="hero-index" aria-hidden="true">01 / Jaguar</span>
</section>

<section class="intro-section wrap section-space" data-reveal>
    <div><p class="eyebrow">La nostra identità</p><span class="section-number">01 — Azienda</span></div>
    <div class="intro-copy">
        <h2>{{ $settings['homepage_intro_title'] ?? 'Esperienza, ricerca e manifattura.' }}</h2>
        <p class="body-copy preserve-lines">{{ $settings['homepage_intro_text'] ?? 'Aggiornate questa sezione dall’area amministrativa per presentare l’identità della vostra azienda.' }}</p>
    </div>
</section>

<section id="collezioni" class="collections-section section-space">
    <div class="wrap">
        <div class="section-heading" data-reveal>
            <div><p class="eyebrow">Una storia da indossare</p><h2>Le collezioni</h2></div>
            <span class="section-number">02 — Collezioni</span>
        </div>
        <div class="collection-grid">
            @forelse ($collections as $collection)
                <a href="{{ route('pages.show', ['page' => $collection->slug]) }}" class="collection-card" data-reveal>
                    <div class="collection-image"><img src="{{ Storage::disk('public')->url($collection->cover_image_path) }}" alt="" loading="lazy"></div>
                    <div class="collection-caption"><div><span class="eyebrow">Collezione / {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $collection->title }}</h3>@if ($collection->intro)<p>{{ $collection->intro }}</p>@endif</div><span class="card-arrow" aria-hidden="true">↗</span></div>
                </a>
            @empty
                <p class="body-copy">Le collezioni pubblicate con una copertina compariranno qui.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="craft-section section-space">
    <div class="wrap">
        <div class="craft-heading" data-reveal><p class="eyebrow light">Il valore del saper fare</p><h2>La cura è nei dettagli.</h2><span class="section-number">03 — Manifattura</span></div>
        <div class="craft-grid">
            @foreach ([['01', 'Manifattura', 'Cura rigorosa in ogni passaggio produttivo.'], ['02', 'Materiali', 'Selezione attenta di pellami, componenti e finiture.'], ['03', 'Ricerca', 'Collezioni contemporanee con identità riconoscibile.']] as [$number, $title, $text])
                <article data-reveal><span>{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $text }}</p></article>
            @endforeach
        </div>
    </div>
</section>
@endsection
