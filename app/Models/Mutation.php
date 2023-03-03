<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Mutation extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'mutations';

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

}
