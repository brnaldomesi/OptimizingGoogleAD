<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class AdgroupPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $presenter = \App\Presenters\AdgroupPerformancePresenter::class;

    protected $table = 'adgroup_performance';

    public function adgroup()
    {
        return $this->belongsTo(\App\Models\Adgroup::class);
    }
}
