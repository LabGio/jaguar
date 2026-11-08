<x-mail::message>
@component('mail::message')
# Nuova richiesta dal sito

**Nome:** {{ $contactMessage->name }}  
**Email:** {{ $contactMessage->email }}  
**Oggetto:** {{ $contactMessage->subject }}

{{ $contactMessage->message }}
@endcomponent

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
