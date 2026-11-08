<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Amministrazione') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[17rem_1fr]">
        <aside class="bg-stone-950 p-6 text-stone-200">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold tracking-widest text-white">{{ config('app.name') }}</a>
            <nav class="mt-10 flex flex-col gap-2 text-sm">
                <a class="rounded px-3 py-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="rounded px-3 py-2 hover:bg-white/10" href="{{ route('admin.pages.index') }}">Pagine</a>
                <a class="rounded px-3 py-2 hover:bg-white/10" href="{{ route('admin.settings.edit') }}">Impostazioni sito</a>
                <a class="rounded px-3 py-2 hover:bg-white/10" href="{{ route('home') }}" target="_blank">Vedi il sito</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="mt-5">@csrf<button class="px-3 py-2 text-left text-stone-400 hover:text-white">Esci</button></form>
            </nav>
        </aside>
        <main class="p-6 lg:p-10">
            @if (session('status'))<div class="mb-6 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</body>
</html>
