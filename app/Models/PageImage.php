<?php

namespace App\Models;

use Database\Factories\PageImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageImage extends Model
{
    /** @use HasFactory<PageImageFactory> */
    use HasFactory;

    protected $fillable = [
        'path',
        'thumbnail_path',
        'alt_text',
        'sort_order',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'translations' => 'array',
        ];
    }

    public function translatedAlt(): ?string
    {
        $translation = data_get($this->translations, app()->getLocale().'.alt_text');

        return filled($translation) ? $translation : $this->alt_text;
    }

    public function translatedAltLanguage(): string
    {
        return app()->getLocale() !== 'it' && filled($this->alt_text) && blank(data_get($this->translations, app()->getLocale().'.alt_text'))
            ? 'it'
            : app()->getLocale();
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
