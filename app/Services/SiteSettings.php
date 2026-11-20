<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    private const CACHE_KEY = 'site-settings';

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => Setting::query()
            ->pluck('value', 'key')
            ->all());
    }

    /**
     * @param  array<string, string|null>  $settings
     */
    public function save(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, string|null>
     */
    public function localized(): array
    {
        $settings = $this->all();
        $locale = app()->getLocale();

        if ($locale !== 'it') {
            foreach (['footer_text', 'hero_title', 'hero_subtitle', 'homepage_intro_title', 'homepage_intro_text'] as $field) {
                $translation = $settings["{$field}_{$locale}"] ?? null;
                $settings[$field] = filled($translation) ? $translation : ($settings[$field] ?? null);
            }
        }

        return $settings;
    }

    public function languageOf(string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $settings = $this->all();

        return $locale !== 'it' && filled($settings[$field] ?? null) && blank($settings["{$field}_{$locale}"] ?? null)
            ? 'it'
            : $locale;
    }

    public function isTranslated(string $locale): bool
    {
        if ($locale === 'it') {
            return true;
        }

        $settings = $this->all();
        foreach (['hero_title', 'hero_subtitle', 'homepage_intro_title', 'homepage_intro_text'] as $field) {
            if (filled($settings[$field] ?? null) && blank($settings["{$field}_{$locale}"] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
