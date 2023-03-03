<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class Account extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $casts = [

        'budget_target_graph_data'	=>	'array',
        'budget_actual_graph_data'  =>  'array',
        'kpi_target_graph_data'     =>  'array',
        'kpi_actual_graph_data'     =>  'array',
    ];

    protected $dates = [

        'created_at',
        'updated_at',
        'ad_performance_report_processed_at',
    ];

    protected $fillable = [
        'user_id',
        'google_id',
        'budget',
        'kpi_name',
        'kpi_value',
        'elapsed_time',
        'budget_actual_vs_target',
        'budget_forecast_vs_target',
        'kpi_actual_vs_target',
        'kpi_forecast_vs_target',

    ];

    protected $presenter = \App\Presenters\AccountPresenter::class;

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($account) {

            //cascade delete campaigns
            $account->campaigns->each(function ($child) {
                $child->delete();
            });

            //cascade delete performance records
            $account->performance->each(function ($child) {
                $child->delete();
            });

            //cascade delete winning elements
            $account->winningElements->each(function ($child) {
                $child->delete();
            });

            //cascade delete best performers
            $account->bestPerformers->each(function ($child) {
                $child->delete();
            });

            $account->worstPerformers->each(function ($child) {
                $child->delete();
            });

            $account->potentialGains->each(function ($child) {
                $child->delete();
            });

            $account->performanceChanges->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(\App\Models\Campaign::class)->orderBy('name');
    }

    public function adgroups()
    {
        return $this->hasMany(\App\Models\Adgroup::class);
    }

    public function performance()
    {
        return $this->hasMany(\App\Models\AccountPerformance::class);
    }

    public function account_performance_report()
    {
        return $this->hasMany(\App\Models\AccountPerformanceReport::class);
    }

    public function budgetCommander()
    {
        return $this->hasMany(\App\Models\BudgetCommander::class);
    }

    public function nGramPerformance()
    {
        return $this->hasMany(\App\Models\AdNGramPerformance::class);
    }

    public function winningElements()
    {
        return $this->hasMany(\App\Models\AccountWinningElement::class)->orderBy('order');
    }

    public function bestPerformers()
    {
        return $this->hasMany(\App\Models\BestPerformer::class);
    }

    public function worstPerformers()
    {
        return $this->hasMany(\App\Models\WorstPerformer::class);
    }

    public function potentialGains()
    {
        return $this->hasMany(\App\Models\PotentialGain::class);
    }

    public function performanceChanges()
    {
        return $this->hasMany(\App\Models\AccountPerformanceChange::class);
    }
}
