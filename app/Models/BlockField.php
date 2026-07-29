<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class BlockField extends Model
{
    protected $fillable = [
        'block_id',
        'parent_id',     // ← للـ repeater sub-fields
        'key',
        'label',
        'type',
        'order',
        'translatable',
        'required',
        'is_active',
        'settings',
        'validation',
        'hint',
    ];

    protected $casts = [
        'translatable'  => 'boolean',
        'required'      => 'boolean',
        'is_active'     => 'boolean',
        'settings'      => 'array',
        'validation'    => 'array',
    ];

    // -----------------------------------------------
    // Field Types
    // -----------------------------------------------

    public const TYPE_TEXT      = 'text';
    public const TYPE_TEXTAREA  = 'textarea';
    public const TYPE_RICHTEXT  = 'richtext';
    public const TYPE_NUMBER    = 'number';
    public const TYPE_SELECT    = 'select';
    public const TYPE_IMAGE     = 'image';
    public const TYPE_ICON      = 'icon';
    public const TYPE_FILE      = 'file';
    public const TYPE_COLOR     = 'color';
    public const TYPE_REPEATER  = 'repeater';
    public const TYPE_RELATION  = 'relation';

    public const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_TEXTAREA,
        self::TYPE_RICHTEXT,
        self::TYPE_NUMBER,
        self::TYPE_SELECT,
        self::TYPE_IMAGE,
        self::TYPE_ICON,
        self::TYPE_FILE,
        self::TYPE_COLOR,
        self::TYPE_REPEATER,
        self::TYPE_RELATION,
    ];

    // -----------------------------------------------
    // Relationships
    // -----------------------------------------------

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlockField::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(BlockField::class, 'parent_id')->orderBy('order');
    }

    public function values(): HasMany
    {
        return $this->hasMany(BlockFieldValue::class);
    }

    // -----------------------------------------------
    // Type Helpers
    // -----------------------------------------------

    public function isRepeater(): bool
    {
        return $this->type === self::TYPE_REPEATER;
    }

    public function isTranslatable(): bool
    {
        return $this->translatable;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->isRepeater() && $this->children->isNotEmpty();
    }

    // -----------------------------------------------
    // Settings Helpers
    // -----------------------------------------------

    public function getOptions(): array
    {
        return $this->settings['options'] ?? [];
    }

    public function getDisk(): string
    {
        return $this->settings['disk'] ?? 'public';
    }

    public function getMaxSize(): int
    {
        return $this->settings['max_size'] ?? 2048;
    }

    public function getMaxLength(): int
    {
        return $this->settings['max_length'] ?? 255;
    }

    public function getFormat(): string
    {
        return $this->settings['format'] ?? 'hex';
    }


    // -----------------------------------------------
    // Default Value Resolver
    // -----------------------------------------------

            public function resolveDefault(?string $locale = null): mixed
            {
                $locale ??= app()->getLocale();

                if ($this->isTranslatable() && is_array($this->default_value)) {
                    return $this->default_value[$locale]
                        ?? $this->default_value[config('app.fallback_locale')]
                        ?? Arr::first($this->default_value)
                        ?? null;
                }

                return $this->default_value;
            }

    // -----------------------------------------------
    // Validation Builder
    // -----------------------------------------------

    public function getValidationRules(string $prefix = ''): array
    {
        $key   = $prefix . $this->key;
        $rules = $this->required ? ['required'] : ['nullable'];

        switch ($this->type) {
            case self::TYPE_TEXT:
            case self::TYPE_TEXTAREA:
            case self::TYPE_RICHTEXT:
                $rules[] = 'string';
                $rules[] = 'max:' . $this->getMaxLength();
                break;

            case self::TYPE_NUMBER:
                $rules[] = 'numeric';
                break;

            case self::TYPE_IMAGE:
            case self::TYPE_ICON:
            case self::TYPE_FILE:
                $rules[] = 'string';
                break;

            case self::TYPE_SELECT:
                $options = array_column($this->getOptions(), 'value');
                if (! empty($options)) {
                    $rules[] = 'in:' . implode(',', $options);
                }
                break;

            case self::TYPE_COLOR:
                $rules[] = 'string';
                break;

            case self::TYPE_RELATION:
                $rules[] = 'integer';
                $rules[] = 'exists:pages,id';
                break;

            case self::TYPE_REPEATER:
                $rules[] = 'array';
                break;
        }

        return [$key => implode('|', $rules)];
    }
}
