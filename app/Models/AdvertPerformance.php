<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class AdvertPerformance extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $presenter = \App\Presenters\AdvertPerformancePresenter::class;

    protected $table = 'advert_performance';

    public function advert()
    {
        return $this->belongsTo(\App\Models\Advert::class);
    }
}
