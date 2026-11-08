@extends('layouts.public')
@section('content')
<section class="bg-stone-950 px-6 pb-16 pt-40 text-white lg:px-8"><div class="mx-auto max-w-7xl"><p class="text-sm uppercase tracking-[0.2em] text-stone-400">Contatti</p><h1 class="mt-4 text-5xl font-semibold sm:text-7xl">Parliamone.</h1></div></section>
<section class="mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-2 lg:px-8">
    <div class="space-y-5 text-stone-600">
        <h2 class="text-2xl font-semibold text-stone-900">{{ $settings['company_name'] ?? config('app.name') }}</h2>
        @if (! empty($settings['address']))
            <p class="whitespace-pre-line">{{ $settings['address'] }}</p>
        @endif
        @if (! empty($settings['phone']))
            <p><a class="underline" href="tel:{{ $settings['phone'] }}">{{ $settings['phone'] }}</a></p>
        @endif
        @if (! empty($settings['email']))
            <p><a class="underline" href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></p>
        @endif
        @if (! empty($settings['map_embed_url']))
            <iframe class="mt-8 h-80 w-full border-0" src="{{ $settings['map_embed_url'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mappa sede aziendale"></iframe>
        @endif
    </div>
    <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 rounded-lg bg-stone-100 p-6">
        @csrf
        @if (session('status'))
            <p class="rounded bg-emerald-100 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
        @endif
        <label class="block text-sm font-medium">Nome<input class="mt-1 w-full rounded border-stone-300" name="name" value="{{ old('name') }}" required></label>
        <label class="block text-sm font-medium">Email<input class="mt-1 w-full rounded border-stone-300" type="email" name="email" value="{{ old('email') }}" required></label>
        <label class="block text-sm font-medium">Oggetto<input class="mt-1 w-full rounded border-stone-300" name="subject" value="{{ old('subject') }}" required></label>
        <label class="block text-sm font-medium">Messaggio<textarea class="mt-1 w-full rounded border-stone-300" name="message" rows="6" required>{{ old('message') }}</textarea></label>
        <div class="hidden" aria-hidden="true"><label>Website<input tabindex="-1" autocomplete="off" name="website"></label></div>
        <button class="rounded bg-stone-950 px-5 py-3 text-sm font-semibold text-white">Invia richiesta</button>
    </form>
</section>
@endsection
