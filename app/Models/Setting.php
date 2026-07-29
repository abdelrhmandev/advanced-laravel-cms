<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    use LogsActivity;

    protected $table = 'settings';

    protected $guarded = [];
    protected $fillable = ['key','label','type','value'];

    public $timestamps = false;

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('settings')
            ->logOnly(['value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Setting [{$this->key}] {$eventName}";
    }

    public function tapActivity($activity, string $eventName)
    {
        $activity->properties = [
            'key' => $this->key,
            'old' => $this->getOriginal('value'),
            'new' => $this->value,
        ];
    }
}
