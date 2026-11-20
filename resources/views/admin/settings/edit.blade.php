@extends('layouts.admin')
@section('title', 'Impostazioni')
@section('content')
<div><p class="text-sm text-stone-500">Configurazione</p><h1 class="text-3xl font-semibold">Impostazioni sito</h1></div>
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" data-settings-form class="mt-8 grid max-w-5xl gap-8 rounded-lg bg-white p-6 shadow-sm">@csrf @method('PUT')
    <fieldset class="grid gap-5"><legend class="text-lg font-semibold">Dati aziendali</legend><div class="grid gap-5 md:grid-cols-2">@foreach (['company_name' => 'Ragione sociale', 'address' => 'Indirizzo', 'phone' => 'Telefono', 'email' => 'Email', 'vat_number' => 'Partita IVA'] as $name => $label)<label class="text-sm font-medium">{{ $label }}<input class="mt-1 w-full rounded border-stone-300" name="{{ $name }}" value="{{ old($name, $settings[$name] ?? '') }}"></label>@endforeach</div></fieldset>
    <fieldset class="grid gap-5">
        <legend class="text-lg font-semibold">Homepage</legend>
        <div class="grid gap-5 md:grid-cols-2"><label class="text-sm font-medium">Titolo hero<input class="mt-1 w-full rounded border-stone-300" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}"></label><label class="text-sm font-medium">Sottotitolo hero<input class="mt-1 w-full rounded border-stone-300" name="hero_subtitle" value="{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}"></label></div>
        <label class="text-sm font-medium">Titolo sezione introduttiva<input class="mt-1 w-full rounded border-stone-300" name="homepage_intro_title" value="{{ old('homepage_intro_title', $settings['homepage_intro_title'] ?? '') }}"></label>
        <label class="text-sm font-medium">Testo introduttivo<textarea class="mt-1 w-full rounded border-stone-300" name="homepage_intro_text" rows="4">{{ old('homepage_intro_text', $settings['homepage_intro_text'] ?? '') }}</textarea></label>
        <div class="grid gap-5 md:grid-cols-2">
            <label class="text-sm font-medium">Logo<input class="mt-1 block text-sm" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"></label>
            <label class="text-sm font-medium">Fallback hero<input class="mt-1 block text-sm" type="file" name="hero_fallback" accept=".jpg,.jpeg,.png,.webp"></label>
            @foreach (['hero_video' => 'Video hero desktop e tablet (1920 × 1080)', 'hero_video_mobile' => 'Video hero smartphone (720 × 1280)'] as $name => $label)
                <div data-video-upload>
                    <label class="text-sm font-medium">{{ $label }}<input class="mt-1 block text-sm" type="file" name="{{ $name }}" accept=".mp4,.webm" data-video-file></label>
                    <button type="button" hidden data-video-button class="mt-2 rounded bg-stone-950 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Carica file</button>
                    <progress hidden data-video-progress class="mt-2 w-full" max="100" value="0" aria-label="Avanzamento caricamento {{ $label }}"></progress>
                    <p data-video-status role="status" aria-live="polite" class="mt-2 text-sm"></p>
                </div>
            @endforeach
        </div>
        <p class="text-sm text-stone-500">Video MP4 o WebM, massimo 50 MB ciascuno. Se è presente un solo video, viene usato su tutti i dispositivi.</p>
    </fieldset>
    <fieldset class="grid gap-5"><legend class="text-lg font-semibold">Footer, social e mappa</legend><label class="text-sm font-medium">Testo footer<textarea class="mt-1 w-full rounded border-stone-300" name="footer_text" rows="3">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea></label><div class="grid gap-5 md:grid-cols-2">@foreach (['map_embed_url' => 'URL embed Google Maps', 'instagram_url' => 'Instagram URL', 'facebook_url' => 'Facebook URL', 'linkedin_url' => 'LinkedIn URL'] as $name => $label)<label class="text-sm font-medium">{{ $label }}<input class="mt-1 w-full rounded border-stone-300" type="url" name="{{ $name }}" value="{{ old($name, $settings[$name] ?? '') }}"></label>@endforeach</div><div class="mt-5 rounded border border-stone-200 p-4"><p class="mb-4 text-sm font-semibold">Mappa Google (alternativa)</p><div class="grid gap-5 md:grid-cols-3"><label class="text-sm font-medium">Latitudine<input class="mt-1 w-full rounded border-stone-300" type="number" step="any" name="map_latitude" value="{{ old('map_latitude', $settings['map_latitude'] ?? '') }}" placeholder="es. 45.5017"></label><label class="text-sm font-medium">Longitudine<input class="mt-1 w-full rounded border-stone-300" type="number" step="any" name="map_longitude" value="{{ old('map_longitude', $settings['map_longitude'] ?? '') }}" placeholder="es. -73.0952"></label><label class="text-sm font-medium">Zoom<input class="mt-1 w-full rounded border-stone-300" type="number" min="1" max="21" name="map_zoom" value="{{ old('map_zoom', $settings['map_zoom'] ?? 15) }}"></label></div><p class="mt-3 text-xs text-stone-500">Configura latitudine, longitudine e zoom per mostrare una mappa Google interattiva. Se vuoto, usa l'URL embed sopra.</p></div></fieldset>
    @foreach (['en' => 'Inglese', 'de' => 'Tedesco'] as $locale => $language)
        <fieldset class="grid gap-4 rounded border border-stone-200 p-4">
            <legend class="px-2 text-lg font-semibold">{{ $language }}</legend>
            <p class="text-sm text-stone-500">I campi vuoti mostrano il contenuto italiano.</p>
            @foreach (['hero_title' => 'Titolo hero', 'hero_subtitle' => 'Sottotitolo hero', 'homepage_intro_title' => 'Titolo sezione introduttiva', 'homepage_intro_text' => 'Testo introduttivo', 'footer_text' => 'Testo footer'] as $field => $label)
                @php($name = "{$field}_{$locale}")
                <label class="text-sm font-medium">{{ $label }}<textarea class="mt-1 w-full rounded border-stone-300" name="{{ $name }}" rows="3">{{ old($name, $settings[$name] ?? '') }}</textarea></label>
            @endforeach
        </fieldset>
    @endforeach
    <div><button data-settings-save class="rounded bg-stone-950 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50">Salva impostazioni</button></div>
</form>
@endsection
