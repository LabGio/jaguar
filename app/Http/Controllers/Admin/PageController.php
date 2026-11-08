<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->with('parent')->withCount('images')->orderBy('parent_id')->orderBy('menu_order')->orderBy('title')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.form', [
            'page' => new Page,
            'parentPages' => $this->parentPages(),
        ]);
    }

    public function store(StorePageRequest $request, ImageService $images): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $images->storeImage($request->file('cover_image'), 'pages/covers');
        }

        $page = Page::create($data);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Pagina creata.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', [
            'page' => $page->load('images'),
            'parentPages' => $this->parentPages($page),
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page, ImageService $images): RedirectResponse
    {
        $data = $request->safe()->except('cover_image');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $newPath = $images->storeImage($request->file('cover_image'), 'pages/covers');
            if ($page->cover_image_path !== null) {
                Storage::disk('public')->delete($page->cover_image_path);
            }

            $data['cover_image_path'] = $newPath;
        }

        $page->update($data);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Pagina aggiornata.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->load('images');
        if ($page->cover_image_path !== null) {
            Storage::disk('public')->delete($page->cover_image_path);
        }

        Storage::disk('public')->delete($page->images->flatMap(fn ($image) => [$image->path, $image->thumbnail_path])->all());
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Pagina eliminata.');
    }

    /**
     * @return Collection<int, Page>
     */
    private function parentPages(?Page $currentPage = null): Collection
    {
        return Page::query()
            ->whereNull('parent_id')
            ->when($currentPage, fn ($query) => $query->whereKeyNot($currentPage))
            ->orderBy('menu_order')
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}
