@extends('layouts.admin')
@section('title', $page->exists ? 'Modifica pagina' : 'Nuova pagina')
@section('content')
<div class="flex items-end justify-between"><div><p class="text-sm text-stone-500">Pagine</p><h1 class="text-3xl font-semibold">{{ $page->exists ? 'Modifica pagina' : 'Nuova pagina' }}</h1></div><a href="{{ route('admin.pages.index') }}" class="text-sm underline">Torna all’elenco</a></div>
<form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" enctype="multipart/form-data" class="mt-8 grid max-w-5xl gap-6 rounded-lg bg-white p-6 shadow-sm">
    @csrf @if ($page->exists) @method('PUT') @endif
    <div class="grid gap-6 md:grid-cols-2"><label class="text-sm font-medium">Titolo<input class="mt-1 w-full rounded border-stone-300" name="title" value="{{ old('title', $page->title) }}" required></label><label class="text-sm font-medium">Slug URL<input class="mt-1 w-full rounded border-stone-300" name="slug" value="{{ old('slug', $page->slug) }}" required placeholder="collezioni/primavera-estate"><span class="mt-1 block font-normal text-stone-500">Usa minuscole, numeri, trattini e /.</span></label></div>
    <label class="text-sm font-medium">Posizione nel menu<select class="mt-1 w-full rounded border-stone-300" name="parent_id"><option value="">Voce principale</option>@foreach ($parentPages as $parentPage)<option value="{{ $parentPage->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $parentPage->id)>Sottomenu di: {{ $parentPage->title }}</option>@endforeach</select><span class="mt-1 block font-normal text-stone-500">Le pagine in un sottomenu non possono avere altre voci figlie.</span></label>
    <label class="text-sm font-medium">Descrizione introduttiva<textarea class="mt-1 w-full rounded border-stone-300" name="intro" rows="3">{{ old('intro', $page->intro) }}</textarea></label>
    <label class="text-sm font-medium">Contenuto<textarea class="mt-1 w-full rounded border-stone-300" name="content" rows="10">{{ old('content', $page->content) }}</textarea></label>
    <div class="grid gap-6 md:grid-cols-2"><label class="text-sm font-medium">Immagine di copertina<input class="mt-1 block w-full text-sm" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp">@if ($page->cover_image_path)<img class="mt-3 h-28 w-44 rounded object-cover" src="{{ Storage::url($page->cover_image_path) }}" alt="Copertina corrente">@endif</label><label class="text-sm font-medium">Ordine nel menu<input class="mt-1 w-full rounded border-stone-300" type="number" min="0" name="menu_order" value="{{ old('menu_order', $page->menu_order) }}" required></label></div>
    <div class="grid gap-6 md:grid-cols-2"><label class="text-sm font-medium">Meta title<input class="mt-1 w-full rounded border-stone-300" name="meta_title" maxlength="70" value="{{ old('meta_title', $page->meta_title) }}"></label><label class="text-sm font-medium">Meta description<input class="mt-1 w-full rounded border-stone-300" name="meta_description" maxlength="160" value="{{ old('meta_description', $page->meta_description) }}"></label></div>
    <label class="flex items-center gap-2 text-sm font-medium"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))> Pubblica la pagina</label>
    <div><button class="rounded bg-stone-950 px-5 py-3 text-sm font-semibold text-white">Salva pagina</button></div>
</form>
@if ($page->exists)
<section class="mt-8 max-w-5xl rounded-lg bg-white p-6 shadow-sm"><h2 class="text-xl font-semibold">Fotogallery</h2><p class="mt-1 text-sm text-stone-500">Carica fino a 20 fotografie per volta: verranno convertite e ottimizzate automaticamente.</p>
    <form class="mt-5 flex flex-wrap items-end gap-4" method="POST" action="{{ route('admin.pages.images.store', $page) }}" enctype="multipart/form-data">@csrf<label class="text-sm font-medium">Fotografie<input class="mt-1 block text-sm" type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required></label><button class="rounded bg-stone-800 px-4 py-2 text-sm font-semibold text-white">Carica</button></form>
    @if ($page->images->isNotEmpty())
    <form id="reorder-images" method="POST" action="{{ route('admin.pages.images.reorder', $page) }}" class="hidden">@csrf @method('PATCH')</form>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-sortable>
        @foreach ($page->images as $image)<article class="rounded border border-stone-200 p-3" draggable="true" data-sortable-item><input form="reorder-images" type="hidden" name="image_ids[]" value="{{ $image->id }}"><img class="aspect-square w-full rounded object-cover" src="{{ Storage::url($image->thumbnail_path) }}" alt="{{ $image->alt_text }}"><p class="mt-2 cursor-grab text-xs text-stone-500">Trascina per riordinare</p>
        <div class="mt-3 flex gap-2"><input form="image-{{ $image->id }}" class="min-w-0 flex-1 rounded border-stone-300 text-sm" name="alt_text" value="{{ $image->alt_text }}" placeholder="Descrizione"><button form="image-{{ $image->id }}" class="text-sm underline">Salva</button></div>
        <form id="image-{{ $image->id }}" method="POST" action="{{ route('admin.pages.images.update', [$page, $image]) }}" class="hidden">@csrf @method('PATCH')</form>
        <form method="POST" action="{{ route('admin.pages.images.destroy', [$page, $image]) }}" class="mt-2">@csrf @method('DELETE')<button class="text-xs text-red-700 underline" onclick="return confirm('Eliminare questa fotografia?')">Elimina</button></form></article>@endforeach
    </div><button form="reorder-images" class="mt-5 rounded border border-stone-300 px-4 py-2 text-sm font-medium">Salva ordine</button>
    @endif
</section>
<form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="mt-8">@csrf @method('DELETE')<button class="text-sm text-red-700 underline" onclick="return confirm('Eliminare definitivamente questa pagina e le sue fotografie?')">Elimina pagina</button></form>
@endif
@endsection
