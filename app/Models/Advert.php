<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use UuidModelTrait;

    protected $casts = [
        'final_urls' =>  'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($advert) {
            $advert->performance->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function adgroup()
    {
        return $this->belongsTo(\App\Models\Adgroup::class);
    }

    public function performance()
    {
        return $this->hasMany(\App\Models\AdvertPerformance::class)->orderBy('clicks');
    }

    public function performanceByDateRange($dateRange)
    {
        return $this->hasMany(\App\Models\AdvertPerformance::class)->where('date_range', $dateRange);
    }

}
