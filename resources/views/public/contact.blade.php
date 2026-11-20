@extends('layouts.public')
@section('content')
<section class="simple-hero"><div class="wrap"><p class="eyebrow light">{{ __('site.contacts') }}</p><h1>{{ __('site.lets_talk') }}</h1><p>{{ __('site.dialogue') }}</p></div></section>
<section class="contact-section wrap section-space">
    <div class="contact-details" data-reveal>
        <p class="eyebrow">{{ __('site.find_us') }}</p>
        <h2>{{ $settings['company_name'] ?? config('app.name') }}</h2>
        @if (! empty($settings['address']))<p class="preserve-lines">{{ $settings['address'] }}</p>@endif
        @if (! empty($settings['phone']))<p><a href="tel:{{ $settings['phone'] }}">{{ $settings['phone'] }}</a></p>@endif
        @if (! empty($settings['email']))<p><a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a></p>@endif
        @if (! empty($settings['map_embed_url']))<iframe class="contact-map" src="{{ $settings['map_embed_url'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('site.map') }}"></iframe>@endif
    </div>
    <form method="POST" action="{{ \App\Services\Localization::route('contact.store') }}" class="contact-form" data-reveal>
        @csrf
        <p class="eyebrow">{{ __('site.write_us') }}</p>
        <h2>{{ __('site.send_message') }}</h2>
        @if (session('status'))<p class="form-notice" role="status">{{ session('status') }}</p>@endif
        @if ($errors->any())<div class="form-errors" role="alert"><p>{{ __('site.check_data') }}</p><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <label>{{ __('site.name') }}<input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>{{ __('site.subject') }}<input name="subject" value="{{ old('subject') }}" required></label>
        <label>{{ __('site.message') }}<textarea name="message" rows="5" required>{{ old('message') }}</textarea></label>
        <div class="hidden" aria-hidden="true"><label>Website<input tabindex="-1" autocomplete="off" name="website"></label></div>
        <button type="submit" class="button-dark">{{ __('site.send_request') }} <span aria-hidden="true">↗</span></button>
    </form>
</section>
@endsection
