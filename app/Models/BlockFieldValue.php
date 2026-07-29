<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockFieldValue extends Model
{
    protected $fillable = [
        'page_block_id',
        'block_field_id',
        'row',
        'index',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
        'row'   => 'integer',
        'index' => 'integer',
    ];

    // -----------------------------------------------
    // Relations
    // -----------------------------------------------

    public function pageBlock(): BelongsTo
    {
        return $this->belongsTo(PageBlock::class, 'page_block_id');
    }

    public function blockField(): BelongsTo
    {
        return $this->belongsTo(BlockField::class, 'block_field_id');
    }

    // -----------------------------------------------
    // Translation Helper
    // -----------------------------------------------

    public function translate(?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();

        if (! is_array($this->value)) {
            return $this->value;
        }

        return $this->value[$locale]
            ?? $this->value[config('app.fallback_locale')]
            ?? reset($this->value)
            ?? null;
    }
}
