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
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
