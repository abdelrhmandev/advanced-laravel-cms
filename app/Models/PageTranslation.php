<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PageTranslation extends Model
{
    use LogsActivity;

    protected $table = 'page_translations';

    public $timestamps = true;

    protected $fillable = [
        'page_id',
        'title',
        'description',
        'slug',
        'locale',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title','description', 'slug', 'locale'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('page-translation')
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->causer()->associate(auth('admin')->user());
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
