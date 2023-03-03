<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdvertFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'advert_feed';

    public function advert()
    {
        return $this->hasOne(\App\Models\Advert::class, 'id', 'advert_id');
    }

    public function performance()
    {
        return $this->hasOne(\App\Models\AdvertPerformance::class, 'advert_id', 'advert_id');
    }
}
