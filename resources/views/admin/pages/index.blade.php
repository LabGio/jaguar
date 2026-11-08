@extends('layouts.admin')
@section('title', 'Pagine')
@section('content')
<div class="flex items-end justify-between"><div><p class="text-sm text-stone-500">Contenuti</p><h1 class="text-3xl font-semibold">Pagine</h1></div><a href="{{ route('admin.pages.create') }}" class="rounded bg-stone-950 px-4 py-2 text-sm font-semibold text-white">Nuova pagina</a></div>
<div class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm">
    <table class="w-full text-left text-sm"><thead class="bg-stone-50 text-xs uppercase text-stone-500"><tr><th class="px-5 py-4">Titolo</th><th class="px-5 py-4">Posizione menu</th><th class="px-5 py-4">Slug</th><th class="px-5 py-4">Stato</th><th class="px-5 py-4">Galleria</th><th class="px-5 py-4"></th></tr></thead>
    <tbody class="divide-y">@forelse ($pages as $page)<tr><td class="px-5 py-4 font-medium">@if ($page->parent_id) <span class="mr-2 text-stone-400">↳</span> @endif{{ $page->title }}</td><td class="px-5 py-4 text-stone-500">{{ $page->parent?->title ?? 'Voce principale' }}</td><td class="px-5 py-4 text-stone-500">/{{ $page->slug }}</td><td class="px-5 py-4">@if ($page->is_published)<span class="rounded bg-emerald-100 px-2 py-1 text-xs text-emerald-800">Pubblicata</span>@else<span class="rounded bg-stone-100 px-2 py-1 text-xs text-stone-600">Bozza</span>@endif</td><td class="px-5 py-4">{{ $page->images_count }}</td><td class="px-5 py-4 text-right"><a class="font-medium underline" href="{{ route('admin.pages.edit', $page) }}">Modifica</a></td></tr>@empty<tr><td colspan="6" class="px-5 py-10 text-center text-stone-500">Nessuna pagina disponibile.</td></tr>@endforelse</tbody></table>
</div>
<div class="mt-6">{{ $pages->links() }}</div>
@endsection
