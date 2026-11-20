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
    <fieldset class="grid gap-5"><legend class="text-lg font-semibold">Footer, social e mappa</legend><label class="text-sm font-medium">Testo footer<textarea class="mt-1 w-full rounded border-stone-300" name="footer_text" rows="3">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea></label><div class="grid gap-5 md:grid-cols-2">@foreach (['map_embed_url' => 'URL embed Google Maps', 'instagram_url' => 'Instagram URL', 'facebook_url' => 'Facebook URL', 'linkedin_url' => 'LinkedIn URL'] as $name => $label)<label class="text-sm font-medium">{{ $label }}<input class="mt-1 w-full rounded border-stone-300" type="url" name="{{ $name }}" value="{{ old($name, $settings[$name] ?? '') }}"></label>@endforeach</div></fieldset>
    <div><button data-settings-save class="rounded bg-stone-950 px-5 py-3 text-sm font-semibold text-white disabled:opacity-50">Salva impostazioni</button></div>
</form>
@endsection
