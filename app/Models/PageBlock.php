<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Pivot
{
    protected $table = 'page_block';
    public $incrementing = true;

    protected $fillable = [
        'page_id',
        'block_id',
        'order',
        'is_visible',
        'anchor',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // -----------------------------------------------
    // Relationships
    // -----------------------------------------------

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(BlockFieldValue::class, 'page_block_id');
    }

    // -----------------------------------------------
    // Value Helpers — مع الـ row الجديد
    // -----------------------------------------------

    // field عادي (row=0, index=0)
    public function getValueFor(BlockField $field, int $row = 0, int $index = 0): mixed
    {
        return $this->fieldValues
            ->where('block_field_id', $field->id)
            ->where('row', $row)
            ->where('index', $index)
            ->first()
            ?->value;
    }

    public function getValueForKey(string $key, int $row = 0, int $index = 0): mixed
    {
        $field = $this->block?->getFieldByKey($key);
        if (! $field) return null;

        return $this->getValueFor($field, $row, $index);
    }

    // كل الـ values مجمعة by row
    public function getGroupedValues(): array
    {
        return $this->fieldValues
            ->groupBy('row')
            ->map(function ($rowValues) {
                return $rowValues
                    ->groupBy('index')
                    ->map(function ($indexValues) {
                        return $indexValues->mapWithKeys(function ($fv) {
                            return [$fv->blockField->key => $fv->value];
                        });
                    });
            })
            ->toArray();
    }

    // -----------------------------------------------
    // Translation Helper
    // -----------------------------------------------

    public function getTranslation(string $key, ?string $locale = null, int $row = 0): mixed
    {
        $locale ??= app()->getLocale();
        $value = $this->getValueForKey($key, $row);

        if (is_array($value)) {
            return $value[$locale]
                ?? $value[config('app.fallback_locale')]
                ?? null;
        }

        return $value;
    }

    // -----------------------------------------------
    // Utilities
    // -----------------------------------------------

    public function isVisible(): bool
    {
        return $this->is_visible && $this->block?->is_active;
    }

    public function getRowCount(): int
    {
        return $this->fieldValues->max('row') ?? 0;
    }
}
