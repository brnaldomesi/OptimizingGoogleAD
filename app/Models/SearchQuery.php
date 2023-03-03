<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use UuidModelTrait;

    public function performance()
    {
        return $this->hasOne(\App\Models\SearchQueryPerformance::class);
    }

    public function searchqueryfeeditem()
    {
        return $this->hasOne(\App\Models\SearchQueryFeed::class);
    }
}
