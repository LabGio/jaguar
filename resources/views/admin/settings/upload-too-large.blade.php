<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caricamento troppo grande · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 p-6 text-stone-900">
    <main class="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold">Caricamento troppo grande</h1>
        <p class="mt-4">La richiesta supera il limite consentito dal server. Le impostazioni non sono state salvate.</p>
        <p class="mt-4">Prova a caricare un video alla volta o a ridurne le dimensioni. Se il problema persiste, chiedi al gestore del server di verificare i limiti di caricamento PHP e del server web.</p>
        <a class="mt-6 inline-block underline" href="{{ route('admin.settings.edit') }}">Torna alle impostazioni</a>
    </main>
</body>
</html>
