<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SearchQueryFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'search_query_feed';

    public function search_query()
    {
        return $this->hasOne(\App\Models\SearchQuery::class, 'id', 'search_query_id');
    }

    public function performance()
    {
        return $this->hasOne(\App\Models\SearchQueryPerformance::class, 'search_query_id', 'search_query_id');
    }

    public function keyword()
    {
        return $this->hasOne(\App\Models\Keyword::class, 'id', 'keyword_id');
    }
}
