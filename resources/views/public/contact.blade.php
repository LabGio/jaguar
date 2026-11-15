@extends('layouts.public')
@section('content')
<section class="simple-hero"><div class="wrap"><p class="eyebrow light">Contatti</p><h1>Parliamone.</h1><p>Un dialogo inizia da qui.</p></div></section>
<section class="contact-section wrap section-space">
    <div class="contact-details" data-reveal>
        <p class="eyebrow">Dove trovarci</p>
        <h2>{{ $settings['company_name'] ?? config('app.name') }}</h2>
        @if (! empty($settings['address']))<p class="preserve-lines">{{ $settings['address'] }}</p>@endif
        @if (! empty($settings['phone']))<p><a href="tel:{{ $settings['phone'] }}">{{ $settings['phone'] }}</a></p>@endif
        @if (! empty($settings['email']))<p><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></p>@endif
        @if (! empty($settings['map_embed_url']))<iframe class="contact-map" src="{{ $settings['map_embed_url'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mappa sede aziendale"></iframe>@endif
    </div>
    <form method="POST" action="{{ route('contact.store') }}" class="contact-form" data-reveal>
        @csrf
        <p class="eyebrow">Scrivici</p>
        <h2>Inviaci un messaggio.</h2>
        @if (session('status'))<p class="form-notice" role="status">{{ session('status') }}</p>@endif
        @if ($errors->any())<div class="form-errors" role="alert"><p>Controlla i dati inseriti:</p><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <label>Nome<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Oggetto<input name="subject" value="{{ old('subject') }}" required></label>
        <label>Messaggio<textarea name="message" rows="5" required>{{ old('message') }}</textarea></label>
        <div class="hidden" aria-hidden="true"><label>Website<input tabindex="-1" autocomplete="off" name="website"></label></div>
        <button type="submit" class="button-dark">Invia richiesta <span aria-hidden="true">↗</span></button>
    </form>
</section>
@endsection
