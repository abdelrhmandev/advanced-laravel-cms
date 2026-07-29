<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Block extends Model
{
    protected $fillable = [
        'title',
        'show_title',
        'is_repeatable',
        'is_active',
        'settings',      // ← بدل meta
    ];

    protected $casts = [
        'show_title'    => 'boolean',
        'is_active'     => 'boolean',
        'is_repeatable' => 'boolean',
        'settings'      => 'array',
        'title'         => 'array',
    ];

    public function getTranslatedTitle(?string $locale = null): string
{
    $locale = $locale ?? app()->getLocale();
    $title  = is_array($this->title) ? $this->title : [];

    return $title[$locale]
        ?? $title['en']
        ?? $title['ar']
        ?? '';
}

    // -----------------------------------------------
    // Relations
    // -----------------------------------------------

    public function fields(): HasMany
    {
        return $this->hasMany(BlockField::class)
            ->whereNull('parent_id')   // ← root fields بس
            ->orderBy('order');
    }

    public function allFields(): HasMany
    {
        return $this->hasMany(BlockField::class)->orderBy('order');
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_block')
            ->using(PageBlock::class)
            ->withPivot(['id', 'order', 'is_visible', 'anchor'])  // ← ضيف id و anchor
            ->withTimestamps()
            ->orderByPivot('order');
    }

    // -----------------------------------------------
    // Scopes
    // -----------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    public function getFieldByKey(string $key): ?BlockField
    {
        return $this->fields->firstWhere('key', $key);
    }
}
