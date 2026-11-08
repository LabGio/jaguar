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
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'menu_order' => 'integer',
            'parent_id' => 'integer',
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

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
