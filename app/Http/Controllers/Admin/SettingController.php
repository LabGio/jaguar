<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\ImageService;
use App\Services\SiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class SettingController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.edit', ['settings' => $settings->all()]);
    }

    public function update(UpdateSettingRequest $request, SiteSettings $settings, ImageService $images): RedirectResponse|JsonResponse
    {
        $data = $request->safe()->except(['logo', 'hero_fallback', 'hero_video', 'hero_video_mobile']);
        $current = $settings->all();

        foreach (['logo', 'hero_fallback'] as $field) {
            if ($request->hasFile($field)) {
                if (isset($current[$field])) {
                    Storage::disk('public')->delete($current[$field]);
                }

                $data[$field] = $images->storeImage($request->file($field), "settings/{$field}");
            }
        }

        $replacedVideos = [];
        foreach (['hero_video', 'hero_video_mobile'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('home/video', 'public');
                if ($path === false) {
                    throw new RuntimeException("Impossibile salvare il video {$field}.");
                }

                $data[$field] = $path;
                if (! empty($current[$field])) {
                    $replacedVideos[] = $current[$field];
                }
            }
        }

        $settings->save($data);

        foreach ($replacedVideos as $path) {
            if (! Storage::disk('public')->delete($path)) {
                throw new RuntimeException("Impossibile eliminare il video sostituito: {$path}");
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['saved' => true]);
        }

        return back()->with('status', 'Impostazioni salvate.');
    }
}
