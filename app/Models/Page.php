<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'parent_id',
        'slug',
        'intro',
        'content',
        'cover_image_path',
        'meta_title',
        'meta_description',
        'is_published',
        'menu_order',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'menu_order' => 'integer',
            'parent_id' => 'integer',
            'translations' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('menu_order')->orderBy('title');
    }

    public function publishedChildren(): HasMany
    {
        return $this->children()->published();
    }

    public function images(): HasMany
    {
        return $this->hasMany(PageImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function translated(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $translation = $locale !== 'it' ? data_get($this->translations, "{$locale}.{$field}") : null;

        return filled($translation) ? $translation : $this->getAttribute($field);
    }

    public function translatedLanguage(string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale !== 'it' && filled(data_get($this->translations, "{$locale}.{$field}")) ? $locale : 'it';
    }

    public function isTranslated(string $locale): bool
    {
        if ($locale === 'it') {
            return true;
        }

        foreach (['title', 'intro', 'content'] as $field) {
            if (filled($this->getAttribute($field)) && blank(data_get($this->translations, "{$locale}.{$field}"))) {
                return false;
            }
        }

        return true;
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
