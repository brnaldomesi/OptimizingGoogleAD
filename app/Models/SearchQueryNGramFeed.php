<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SearchQueryNGramFeed extends Model
{
    use UuidModelTrait;
    use Notifiable;

    protected $table = 'search_query_n_gram_feed';

    public function performance()
    {
        return $this->hasOne(\App\Models\SearchQueryNGramPerformance::class, 'id', 'search_query_n_gram_id');
    }
}
