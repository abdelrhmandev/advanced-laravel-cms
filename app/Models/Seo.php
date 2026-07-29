<?php
namespace App\Models;
use App\Models\SeoTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seo extends Model
{
    public $timestamps = true;

    protected $fillable = ['canonical_url', 'og_image', 'twitter_image', 'no_index', 'no_follow'];

    protected $casts = [
        'no_index' => 'boolean',
        'no_follow' => 'boolean',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SeoTranslation::class);
    }

    public function translate(): HasOne
    {
        return $this->hasOne(SeoTranslation::class)->where('locale', app()->getLocale());
    }
}
