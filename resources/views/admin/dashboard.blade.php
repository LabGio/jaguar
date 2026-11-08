@extends('layouts.admin')
@section('content')
<div class="flex items-end justify-between"><div><p class="text-sm text-stone-500">Panoramica</p><h1 class="text-3xl font-semibold">Dashboard</h1></div><a href="{{ route('admin.pages.create') }}" class="rounded bg-stone-950 px-4 py-2 text-sm font-semibold text-white">Nuova pagina</a></div>
<div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([['Pagine create', $pageCount], ['Pagine pubblicate', $publishedPageCount], ['Immagini caricate', $imageCount], ['Messaggi da leggere', $unreadContactCount]] as [$label, $value])
        <div class="rounded-lg bg-white p-6 shadow-sm"><p class="text-sm text-stone-500">{{ $label }}</p><p class="mt-2 text-3xl font-semibold">{{ $value }}</p></div>
    @endforeach
</div>
<section class="mt-10 rounded-lg bg-white p-6 shadow-sm"><h2 class="text-lg font-semibold">Ultime modifiche</h2><div class="mt-4 divide-y">@forelse ($recentPages as $page)<a href="{{ route('admin.pages.edit', $page) }}" class="flex justify-between py-3 text-sm hover:text-stone-600"><span>{{ $page->title }}</span><time datetime="{{ $page->updated_at->toAtomString() }}">{{ $page->updated_at->diffForHumans() }}</time></a>@empty<p class="py-3 text-sm text-stone-500">Nessuna pagina ancora creata.</p>@endforelse</div></section>
@endsection
