<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageImageRequest;
use App\Models\Page;
use App\Models\PageImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageImageController extends Controller
{
    public function store(StorePageImageRequest $request, Page $page, ImageService $images): RedirectResponse
    {
        $nextOrder = ((int) $page->images()->max('sort_order')) + 1;

        foreach ($request->file('images') as $file) {
            $page->images()->create([...$images->storePageImage($file), 'sort_order' => $nextOrder++]);
        }

        return back()->with('status', 'Fotografie caricate.');
    }

    public function update(Request $request, Page $page, PageImage $image): RedirectResponse
    {
        abort_unless($image->page_id === $page->id, 404);
        $data = $request->validate(['alt_text' => ['nullable', 'string', 'max:255']]);
        $image->update($data);

        return back()->with('status', 'Descrizione aggiornata.');
    }

    public function destroy(Page $page, PageImage $image, ImageService $images): RedirectResponse
    {
        abort_unless($image->page_id === $page->id, 404);
        $images->delete($image->path, $image->thumbnail_path);
        $image->delete();

        return back()->with('status', 'Fotografia eliminata.');
    }

    public function reorder(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate(['image_ids' => ['required', 'array'], 'image_ids.*' => ['integer']]);
        $images = $page->images()->whereIn('id', $data['image_ids'])->get()->keyBy('id');

        abort_unless($images->count() === count($data['image_ids']), 422);

        foreach ($data['image_ids'] as $order => $id) {
            $images[$id]->update(['sort_order' => $order]);
        }

        return back()->with('status', 'Ordine della galleria aggiornato.');
    }
}
