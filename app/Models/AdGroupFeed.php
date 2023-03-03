<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdGroupFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'adgroup_feed';

    public function adgroup()
    {
        return $this->hasOne(\App\Models\Adgroup::class, 'id', 'adgroup_id');
    }

    public function performance()
    {
        return $this->hasOne(\App\Models\AdgroupPerformance::class, 'adgroup_id', 'adgroup_id');
    }
}
