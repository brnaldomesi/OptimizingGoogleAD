<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Recent extends Model
{
    use UuidModelTrait;
    use Notifiable;

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
