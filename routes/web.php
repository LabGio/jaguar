<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageImageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicPageController;
use App\Http\Middleware\SetPublicLocale;
use App\Models\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

$publicRoutes = function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/contatti', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contatti', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
    Route::view('/cookie-policy', 'public.cookie-policy')->name('cookie-policy');
};

Route::middleware(SetPublicLocale::class)->group($publicRoutes);
Route::prefix('{locale}')->where(['locale' => 'en|de'])->name('localized.')
    ->middleware(SetPublicLocale::class)->group($publicRoutes);

Route::get('/storage/home/video/{filename}', function (string $filename) {
    $disk = Storage::disk('public');
    $path = "home/video/{$filename}";

    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path), ['X-Content-Type-Options' => 'nosniff']);
})->where('filename', '[A-Za-z0-9]{40}\.(?:mp4|webm)');

Route::get('/sitemap.xml', function () {
    return response()->view('public.sitemap', [
        'pages' => Page::published()->orderBy('updated_at')->get(),
        'collections' => Page::published()->where('slug', '!=', 'azienda')->whereNotNull('cover_image_path')
            ->orderBy('menu_order')->orderByDesc('updated_at')->limit(3)->get(),
    ], 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('pages', PageController::class)->except('show');
    Route::post('pages/{page}/images', [PageImageController::class, 'store'])->name('pages.images.store');
    Route::patch('pages/{page}/images/reorder', [PageImageController::class, 'reorder'])->name('pages.images.reorder');
    Route::patch('pages/{page}/images/{image}', [PageImageController::class, 'update'])->name('pages.images.update');
    Route::delete('pages/{page}/images/{image}', [PageImageController::class, 'destroy'])->name('pages.images.destroy');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/{locale}/{page:slug}', [PublicPageController::class, 'show'])
    ->where(['locale' => 'en|de', 'page' => '[A-Za-z0-9][A-Za-z0-9\/-]*'])
    ->middleware(SetPublicLocale::class)
    ->name('localized.pages.show');

Route::get('/{page:slug}', [PublicPageController::class, 'show'])
    ->where('page', '[A-Za-z0-9][A-Za-z0-9\/-]*')
    ->middleware(SetPublicLocale::class)
    ->name('pages.show');
