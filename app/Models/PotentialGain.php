<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laracodes\Presenter\Traits\Presentable;

class PotentialGain extends Model
{
    use UuidModelTrait;
    use Notifiable;
    use Presentable;

    protected $presenter = \App\Presenters\PotentialGainPresenter::class;

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
