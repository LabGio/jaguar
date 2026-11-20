@extends('layouts.public')
@section('content')
<section class="hero">
    @if (! empty($settings['hero_video']) || ! empty($settings['hero_video_mobile']))
        @php
            $desktopVideo = ! empty($settings['hero_video']) ? $settings['hero_video'] : $settings['hero_video_mobile'];
            $mobileVideo = ! empty($settings['hero_video_mobile']) ? $settings['hero_video_mobile'] : $desktopVideo;
            $poster = ! empty($settings['hero_fallback']) ? Storage::disk('public')->url($settings['hero_fallback']) : '';
        @endphp
        <video class="hero-media" autoplay muted loop playsinline preload="metadata" poster="{{ $poster }}" data-hero-video data-desktop-src="{{ Storage::disk('public')->url($desktopVideo) }}" data-mobile-src="{{ Storage::disk('public')->url($mobileVideo) }}"></video>
        <noscript><video class="hero-media" autoplay muted loop playsinline preload="metadata" poster="{{ $poster }}" src="{{ Storage::disk('public')->url($desktopVideo) }}"></video></noscript>
    @elseif (! empty($settings['hero_fallback']))
        <img class="hero-media" src="{{ Storage::disk('public')->url($settings['hero_fallback']) }}" alt="" fetchpriority="high">
    @endif
    <div class="hero-shade"></div>
    <div class="wrap hero-content">
        <p class="eyebrow light">{{ __('site.shoes') }}</p>
        <h1 lang="{{ app(\App\Services\SiteSettings::class)->languageOf('hero_title') }}">{{ $settings['hero_title'] ?? __('site.hero_default') }}</h1>
        <p class="hero-subtitle" lang="{{ app(\App\Services\SiteSettings::class)->languageOf('hero_subtitle') }}">{{ $settings['hero_subtitle'] ?? __('site.subtitle_default') }}</p>
        @if ($collections->isNotEmpty())<a class="text-link light-link" href="#collezioni">{{ __('site.explore_collections') }} <span aria-hidden="true">↘</span></a>@endif
    </div>
    <span class="hero-index" aria-hidden="true">01 / Jaguar</span>
</section>

<section class="intro-section wrap section-space" data-reveal>
    <div><p class="eyebrow">{{ __('site.identity') }}</p><span class="section-number">01 — {{ __('site.company') }}</span></div>
    <div class="intro-copy">
        <h2 lang="{{ app(\App\Services\SiteSettings::class)->languageOf('homepage_intro_title') }}">{{ $settings['homepage_intro_title'] ?? __('site.intro_title_default') }}</h2>
        <p class="body-copy preserve-lines" lang="{{ app(\App\Services\SiteSettings::class)->languageOf('homepage_intro_text') }}">{{ $settings['homepage_intro_text'] ?? __('site.intro_default') }}</p>
    </div>
</section>

<section id="collezioni" class="collections-section section-space">
    <div class="wrap">
        <div class="section-heading" data-reveal>
            <div><p class="eyebrow">{{ __('site.story') }}</p><h2>{{ __('site.collections') }}</h2></div>
            <span class="section-number">02 — {{ __('site.collections') }}</span>
        </div>
        <div class="collection-grid">
            @forelse ($collections as $collection)
                <a href="{{ \App\Services\Localization::route('pages.show', ['page' => $collection->slug]) }}" class="collection-card" data-reveal>
                    <div class="collection-image"><img src="{{ Storage::disk('public')->url($collection->cover_image_path) }}" alt="" loading="lazy"></div>
                    <div class="collection-caption"><div><span class="eyebrow">{{ __('site.collection') }} / {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3 lang="{{ $collection->translatedLanguage('title') }}">{{ $collection->translated('title') }}</h3>@if ($collection->translated('intro'))<p lang="{{ $collection->translatedLanguage('intro') }}">{{ $collection->translated('intro') }}</p>@endif</div><span class="card-arrow" aria-hidden="true">↗</span></div>
                </a>
            @empty
                <p class="body-copy">{{ __('site.no_collections') }}</p>
            @endforelse
        </div>
    </div>
</section>

<section class="craft-section section-space">
    <div class="wrap">
        <div class="craft-heading" data-reveal><p class="eyebrow light">{{ __('site.craft_value') }}</p><h2>{{ __('site.craft_heading') }}</h2><span class="section-number">03 — {{ __('site.craft') }}</span></div>
        <div class="craft-grid">
            @foreach ([['01', __('site.craft'), __('site.craft_text')], ['02', __('site.materials'), __('site.materials_text')], ['03', __('site.research'), __('site.research_text')]] as [$number, $title, $text])
                <article data-reveal><span>{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $text }}</p></article>
            @endforeach
        </div>
    </div>
</section>
@endsection
