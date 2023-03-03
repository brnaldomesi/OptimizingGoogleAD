<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AccountWinningElement extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $dates = [

        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    
}
