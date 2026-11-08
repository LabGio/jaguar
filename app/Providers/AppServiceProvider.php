<?php

namespace App\Providers;

use App\Models\Page;
use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        View::composer('layouts.public', function ($view): void {
            $view->with([
                'siteSettings' => app(SiteSettings::class)->all(),
                'navigationPages' => Page::published()
                    ->whereNull('parent_id')
                    ->with('publishedChildren')
                    ->orderBy('menu_order')
                    ->orderBy('title')
                    ->get(['id', 'title', 'slug', 'parent_id']),
            ]);
        });
    }
}
