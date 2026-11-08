<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\SiteSettings;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SiteSettings $settings): View
    {
        return view('public.home', [
            'settings' => $settings->all(),
            'collections' => Page::published()
                ->whereNotNull('cover_image_path')
                ->orderBy('menu_order')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
