<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Laracodes\Presenter\Traits\Presentable;

class AccountPerformanceChange extends Model
{
    use UuidModelTrait;
    use Presentable;

    protected $casts = [
        'ctr_graph_data'				=>	'array',
        'conversion_rate_graph_data'	=>	'array',
        'cpa_graph_data'				=>	'array',
        'roas_graph_data'				=>	'array',
    ];

    protected $presenter = \App\Presenters\AccountPerformanceChangePresenter::class;

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
