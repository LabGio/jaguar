<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\PageImage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'pageCount' => Page::count(),
            'publishedPageCount' => Page::published()->count(),
            'imageCount' => PageImage::count(),
            'recentPages' => Page::query()->latest('updated_at')->limit(6)->get(),
            'unreadContactCount' => ContactMessage::query()->whereNull('read_at')->count(),
        ]);
    }
}
