<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class SearchQueryPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $casts = [

        'show_on_graph'	=>	'boolean',
    ];

    protected $presenter = \App\Presenters\SearchQueryPerformancePresenter::class;

    protected $table = 'search_query_performance';

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function search_query()
    {
        return $this->hasOne(\App\Models\SearchQuery::class, 'id', 'search_query_id');
    }
}
