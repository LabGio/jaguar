<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('public.page', ['page' => $page->load('images')]);
    }
}
