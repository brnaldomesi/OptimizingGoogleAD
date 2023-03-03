<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CampaignWinningElement extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $dates = [

        'created_at',
        'updated_at',
    ];

    public function campaign()
    {
        return $this->belongsTo(\App\Models\Campaign::class);
    }

}
