@extends('layouts.public')
@php($metaTitle = (app()->getLocale() === 'it' ? $page->meta_title : data_get($page->translations, app()->getLocale().'.meta_title')) ?: $page->translated('title'))
@php($metaDescription = (app()->getLocale() === 'it' ? $page->meta_description : data_get($page->translations, app()->getLocale().'.meta_description')) ?: $page->translated('intro'))
@section('content')
<section class="page-hero {{ $page->cover_image_path ? 'has-image' : '' }}">
    @if ($page->cover_image_path)<img class="hero-media" src="{{ Storage::disk('public')->url($page->cover_image_path) }}" alt="" fetchpriority="high">@endif
    <div class="hero-shade"></div>
    <div class="wrap page-hero-content">
        <p class="eyebrow light">Jaguar · Made in Italy</p>
        <h1 lang="{{ $page->translatedLanguage('title') }}">{{ $page->translated('title') }}</h1>
        @if ($page->translated('intro'))<p lang="{{ $page->translatedLanguage('intro') }}">{{ $page->translated('intro') }}</p>@endif
    </div>
</section>
@if ($page->translated('content'))
    <article class="page-story wrap section-space" data-reveal>
        <p class="eyebrow">{{ __('site.detail') }}</p>
        <div class="story-copy preserve-lines" lang="{{ $page->translatedLanguage('content') }}">{{ $page->translated('content') }}</div>
    </article>
@endif
@if ($page->images->isNotEmpty())
    <section class="gallery-section section-space">
        <div class="wrap">
            <div class="section-heading" data-reveal><div><p class="eyebrow">{{ __('site.close_look') }}</p><h2>{{ __('site.gallery') }}</h2></div><span class="section-number" lang="{{ $page->translatedLanguage('title') }}">{{ $page->translated('title') }}</span></div>
            <div class="gallery-grid">
                @foreach ($page->images as $image)
                    <button type="button" class="gallery-item" data-reveal data-lightbox-image="{{ Storage::disk('public')->url($image->path) }}" data-lightbox-alt="{{ $image->translatedAlt() }}" aria-label="{{ __('site.open_image', ['name' => $image->translatedAlt() ?: $page->translated('title')]) }}">
                        <img src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->translatedAlt() }}" lang="{{ $image->translatedAltLanguage() }}" loading="lazy">
                        <span aria-hidden="true">↗</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>
    <dialog class="lightbox" data-lightbox data-counter-separator="{{ __('site.of') }}" aria-label="{{ __('site.image_view') }}">
        <button type="button" class="lightbox-close" data-lightbox-close aria-label="{{ __('site.close_gallery') }}">{{ __('site.close') }} <span aria-hidden="true">×</span></button>
        <button type="button" class="lightbox-navigation lightbox-previous" data-lightbox-previous aria-label="{{ __('site.previous_image') }}"><span aria-hidden="true">←</span></button>
        <img src="" alt="" data-lightbox-content>
        <button type="button" class="lightbox-navigation lightbox-next" data-lightbox-next aria-label="{{ __('site.next_image') }}"><span aria-hidden="true">→</span></button>
        <p class="lightbox-counter" data-lightbox-counter aria-live="polite"></p>
    </dialog>
@endif
@endsection
