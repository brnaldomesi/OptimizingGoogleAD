<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Adgroup extends Model
{
    use UuidModelTrait;

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($adgroup) {

            //cascade delete adverts
            $adgroup->adverts->each(function ($child) {
                $child->delete();
            });

            //cascade delete performance records
            $adgroup->performance->each(function ($child) {
                $child->delete();
            });

            //cascade delete winning elements
            $adgroup->winningElements->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function campaign()
    {
        return $this->belongsTo(\App\Models\Campaign::class);
    }

    public function adverts()
    {
        return $this->hasMany(\App\Models\Advert::class)->orderBy('headline_1');
    }

    public function keywords()
    {
        return $this->hasMany(\App\Models\Keyword::class);
    }

    public function advertsLoser()
    {
        return $this->adverts()->loser();
    }

    public function performanceByDateRange($dateRange)
    {
        return $this->hasMany(\App\Models\AdgroupPerformance::class)->where('date_range', $dateRange);
    }

    public function scopeWithWinners($query)
    {
        return $query->where('has_winners', 1);
    }

    public function scopeWithTooManyAdverts($query)
    {
        return $query->where('too_many_adverts', 1);
    }

    public function scopeWithTooFewAdverts($query)
    {
        return $query->where('too_few_adverts', 1);
    }

    public function performance()
    {
        return $this->hasMany(\App\Models\AdgroupPerformance::class);
    }

    public function winningElements()
    {
        return $this->hasMany(\App\Models\AdgroupWinningElement::class);
    }
}
