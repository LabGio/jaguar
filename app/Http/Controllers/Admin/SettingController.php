<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\ImageService;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(SiteSettings $settings): View
    {
        return view('admin.settings.edit', ['settings' => $settings->all()]);
    }

    public function update(UpdateSettingRequest $request, SiteSettings $settings, ImageService $images): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'hero_fallback', 'hero_video']);
        $current = $settings->all();

        foreach (['logo', 'hero_fallback'] as $field) {
            if ($request->hasFile($field)) {
                if (isset($current[$field])) {
                    Storage::disk('public')->delete($current[$field]);
                }

                $data[$field] = $images->storeImage($request->file($field), "settings/{$field}");
            }
        }

        if ($request->hasFile('hero_video')) {
            if (isset($current['hero_video'])) {
                Storage::disk('public')->delete($current['hero_video']);
            }

            $data['hero_video'] = $request->file('hero_video')->store('home/video', 'public');
        }

        $settings->save($data);

        return back()->with('status', 'Impostazioni salvate.');
    }
}
