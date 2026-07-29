<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Page extends Model
{

    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'image',
        'template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['image', 'template', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('page')
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->causer()->associate(auth('admin')->user());
    }


    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(Block::class, 'page_block')
            ->using(PageBlock::class)
            ->withPivot(['id', 'order', 'is_visible', 'anchor'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function activeBlocks(): BelongsToMany
    {
        return $this->blocks()
            ->wherePivot('is_visible', true)
            ->where('blocks.is_active', true);
    }

    public function pageBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('order');
    }

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translate()
    {
        return $this->hasOne(PageTranslation::class)->where('locale', app()->getLocale());
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'page_id');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
    }

    public function lastActivity()
    {
        return $this->activities()->latest()->first();
    }
    public function seo(): MorphOne
{
    return $this->morphOne(Seo::class, 'seoable');
}
}
