<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class BestPerformer extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $presenter = \App\Presenters\BestPerformerPresenter::class;

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
