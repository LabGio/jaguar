<!DOCTYPE html>
<html lang="it">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Accedi · {{ config('app.name') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="grid min-h-screen place-items-center bg-stone-950 p-6">
    <form method="POST" action="{{ route('login.store') }}" class="w-full max-w-md space-y-6 rounded-xl bg-white p-8 shadow-2xl">
        @csrf
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Area riservata</p><h1 class="mt-2 text-2xl font-semibold">Accedi all’amministrazione</h1></div>
        @if ($errors->any())<p class="text-sm text-red-700">{{ $errors->first() }}</p>@endif
        <label class="block text-sm font-medium">Email<input class="mt-1 w-full rounded border-stone-300" type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label class="block text-sm font-medium">Password<input class="mt-1 w-full rounded border-stone-300" type="password" name="password" required></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Ricordami</label>
        <button class="w-full rounded bg-stone-950 px-4 py-3 text-sm font-semibold text-white hover:bg-stone-800">Accedi</button>
    </form>
</body>
</html>
