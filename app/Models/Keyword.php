<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use UuidModelTrait;

    public function adgroup()
    {
        return $this->belongsTo(\App\Models\Adgroup::class, 'adgroup_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(\App\Models\Campaign::class);
    }

    public function performance()
    {
        return $this->hasMany(\App\Models\KeywordPerformance::class);
    }

    public function keywordfeeditem()
    {
        return $this->hasOne(\App\Models\KeywordFeed::class);
    }
}
