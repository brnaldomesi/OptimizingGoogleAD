<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use UuidModelTrait;

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($campaign) {

            //cascade delete ad grous
            $campaign->adgroups->each(function ($child) {
                $child->delete();
            });

            //cascade delete performance records
            $campaign->performance->each(function ($child) {
                $child->delete();
            });

            //cascade delete winning elements
            $campaign->winningElements->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function adgroups()
    {
        return $this->hasMany(\App\Models\Adgroup::class)->orderBy('name');
    }

    public function adgroupsWithWinners()
    {
        return $this->adgroups()->withWinners();
    }

    public function adgroupsWithTooManyAdverts()
    {
        return $this->adgroups()->withTooManyAdverts();
    }

    public function adgroupsWithTooFewAdverts()
    {
        return $this->adgroups()->withTooFewAdverts();
    }

    public function adverts()
    {
        return $this->hasManyThrough(\App\Models\Advert::class, \App\Models\Adgroup::class);
    }

    public function advertsLoser()
    {
        return $this->adverts()->loser();
    }

    public function performance()
    {
        return $this->hasMany(\App\Models\CampaignPerformance::class);
    }

    public function winningElements()
    {
        return $this->hasMany(\App\Models\CampaignWinningElement::class);
    }

    public function budgetGroup()
    {
        return $this->hasOne(\App\Models\BudgetCommander::class, 'id', 'budget_group_id');
    }
}
