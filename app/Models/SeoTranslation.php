<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoTranslation extends Model
{
    protected $table = 'seo_translations';

    public $timestamps = true;

    protected $fillable = [
        'seo_id',
        'locale',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description'
    ];

    public function seo(): BelongsTo
    {
        return $this->belongsTo(Seo::class);
    }
}
