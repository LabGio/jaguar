@extends('layouts.public')
@php($metaTitle = $page->meta_title ?: $page->title)
@php($metaDescription = $page->meta_description ?: $page->intro)
@section('content')
<section class="page-hero {{ $page->cover_image_path ? 'has-image' : '' }}">
    @if ($page->cover_image_path)<img class="hero-media" src="{{ Storage::disk('public')->url($page->cover_image_path) }}" alt="" fetchpriority="high">@endif
    <div class="hero-shade"></div>
    <div class="wrap page-hero-content">
        <p class="eyebrow light">Jaguar · Made in Italy</p>
        <h1>{{ $page->title }}</h1>
        @if ($page->intro)<p>{{ $page->intro }}</p>@endif
    </div>
</section>
@if ($page->content)
    <article class="page-story wrap section-space" data-reveal>
        <p class="eyebrow">In dettaglio</p>
        <div class="story-copy preserve-lines">{{ $page->content }}</div>
    </article>
@endif
@if ($page->images->isNotEmpty())
    <section class="gallery-section section-space">
        <div class="wrap">
            <div class="section-heading" data-reveal><div><p class="eyebrow">Uno sguardo da vicino</p><h2>La galleria</h2></div><span class="section-number">{{ $page->title }}</span></div>
            <div class="gallery-grid">
                @foreach ($page->images as $image)
                    <button type="button" class="gallery-item" data-reveal data-lightbox-image="{{ Storage::disk('public')->url($image->path) }}" data-lightbox-alt="{{ $image->alt_text }}" aria-label="Apri immagine: {{ $image->alt_text ?: $page->title }}">
                        <img src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text }}" loading="lazy">
                        <span aria-hidden="true">↗</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>
    <dialog class="lightbox" data-lightbox aria-label="Visualizzazione immagine">
        <button type="button" class="lightbox-close" data-lightbox-close aria-label="Chiudi galleria">Chiudi <span aria-hidden="true">×</span></button>
        <img src="" alt="" data-lightbox-content>
    </dialog>
@endif
@endsection
