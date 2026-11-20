<?php

namespace App\Services;

class Localization
{
    public const LANGUAGES = ['it' => 'Italiano', 'en' => 'English', 'de' => 'Deutsch'];

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'it') {
            return route($name, $parameters);
        }

        return route('localized.'.$name, ['locale' => $locale, ...$parameters]);
    }
}
